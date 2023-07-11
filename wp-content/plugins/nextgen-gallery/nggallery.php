<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * Plugin Name: NextGEN Gallery
 * Description: The most popular gallery plugin for WordPress and one of the most popular plugins of all time with over 30 million downloads.
 * Version: 3.37
 * Author: Imagely
 * Plugin URI: https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/
 * Author URI: https://www.imagely.com
 * License: GPLv3
 * Text Domain: nggallery
 * Domain Path: /products/photocrati_nextgen/modules/i18n/lang
 * Requires PHP: 5.6
 */

if (!class_exists('E_Clean_Exit')) { class E_Clean_Exit extends RuntimeException {} }
if (!class_exists('E_NggErrorException')) { class E_NggErrorException extends RuntimeException {} }

// This is a temporary function to replace the use of WP's esc_url which strips spaces away from URLs
// TODO: Move this to a better place
if (!function_exists('nextgen_esc_url')) {
	function nextgen_esc_url( $url, $protocols = null, $_context = 'display' ) {
		$original_url = $url;

		if ( '' == $url )
			return $url;
		$url = preg_replace('|[^a-z0-9 \\-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = _deep_replace($strip, $url);
		$url = str_replace(';//', '://', $url);
		/* If the URL doesn't appear to contain a scheme, we
		 * presume it needs http:// appended (unless a relative
		 * link starting with /, # or ? or a php file).
		 */

		if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
		     ! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
			$url = 'http://' . $url;

		// Replace ampersands and single quotes only when displaying.
		if ( 'display' == $_context ) {
			$url = wp_kses_normalize_entities( $url );
			$url = str_replace( '&amp;', '&#038;', $url );
			$url = str_replace( "'", '&#039;', $url );
			$url = str_replace( ' ', '%20', $url );
		}

		if ( '/' === $url[0] ) {
			$good_protocol_url = $url;
		} else {
			if ( ! is_array( $protocols ) )
				$protocols = wp_allowed_protocols();
			$good_protocol_url = wp_kses_bad_protocol( $url, $protocols );
			if ( strtolower( $good_protocol_url ) != strtolower( $url ) )
				return '';
		}

		return apply_filters('clean_url', $good_protocol_url, $original_url, $_context);
	}
}

/**
 * NextGEN Gallery is built on top of the Pope Framework:
 * https://bitbucket.org/photocrati/pope-framework
 *
 * Pope constructs applications by assembling modules.
 *
 * The Bootstrapper. This class performs the following:
 * 1) Loads the Pope Framework
 * 2) Adds a path to the C_Component_Registry instance to search for products
 * 3) Loads all found Products. A Product is a collection of modules with some
 * additional meta data. A Product is responsible for loading any modules it
 * requires.
 * 4) Once all Products (and their associated modules) have been loaded (or in
 * otherwords, "included"), the modules are initialized.
 */
class C_NextGEN_Bootstrap
{
	var $_registry = NULL;
	var $_settings_option_name = 'ngg_options';
	var $_pope_loaded = FALSE;
	static $debug = FALSE;
	var $minimum_ngg_pro_version = '2.0.5';
    var $minimum_ngg_plus_version = '1.0.1';

	static function shutdown($exception=NULL)
	{
		if (is_null($exception))
		{
            $name = php_sapi_name();
            if (FALSE === strpos($name, 'cgi')
            &&  version_compare(PHP_VERSION, '5.3.3') >= 0)
            {
                $status = session_status();
                if (in_array($status, array(PHP_SESSION_DISABLED, PHP_SESSION_NONE), TRUE))
                    session_write_close();
                fastcgi_finish_request();
            }
            else {
                exit();
            }
        }
		elseif (!($exception instanceof E_Clean_Exit)) {
			if (ob_get_level() > 0) ob_end_clean();
			self::print_exception($exception);
		}
	}

	static function print_exception($exception)
	{
		$klass = get_class($exception);
		echo "<h1>{$klass} thrown</h1>";
		echo "<p>{$exception->getMessage()}</p>";
		if (self::$debug OR (defined('NGG_DEBUG') AND NGG_DEBUG == TRUE)) {
			echo "<h3>Where:</h3>";
			echo "<p>On line <strong>{$exception->getLine()}</strong> of <strong>{$exception->getFile()}</strong></p>";
			echo "<h3>Trace:</h3>";
			echo "<pre>{$exception->getTraceAsString()}</pre>";
			if (method_exists($exception, 'getPrevious')) {
				if (($previous = $exception->getPrevious())) {
					self::print_exception($previous);
				}
			}
		}
	}

	static function get_backtrace($objects=FALSE, $remove_dynamic_calls=TRUE)
	{
		$trace = debug_backtrace($objects);
		if ($remove_dynamic_calls) {
			$skip_methods = array(
				'_exec_cached_method',
				'__call',
				'get_method_property',
				'set_method_property',
				'call_method'
			);
			foreach ($trace as $key => &$value) {
				if (isset($value['class']) && isset($value['function'])) {
					if ($value['class'] == 'ReflectionMethod' && $value['function'] == 'invokeArgs')
						unset($trace[$key]);

					else if ($value['class'] == 'ExtensibleObject' && in_array($value['function'], $skip_methods))
						unset($trace[$key]);
				}
			}
		}

		return $trace;
	}

	public function php_version_incompatible()
	{ ?>
		<div class="notice notice-error is-dismissible">
			<p><?php print __('We’ve detected you are running PHP versions 7.0.26 or 7.1.12. These versions of PHP have a bug that breaks NextGEN Gallery and causes server crashes in certain conditions. To protect your site, NextGEN Gallery will not load. We recommend asking your host to roll back to an earlier version of PHP. For details on the PHP bug, see: <a target="_blank" href="https://bugs.php.net/bug.php?id=75573">bugs.php.net/bug.php?id=75573</a>', 'nggallery'); ?></p>
		</div>
		<?php
	}

	function __construct()
	{
	    if (!defined('NGG_DISABLE_SHUTDOWN_EXCEPTION_HANDLER') || !NGG_DISABLE_SHUTDOWN_EXCEPTION_HANDLER)
		    set_exception_handler(__CLASS__.'::shutdown');

		// We only load the plugin if we're outside of the activation request, loaded in an iframe
		// by WordPress. Reason being, if WP_DEBUG is enabled, and another Pope-based plugin (such as
		// the photocrati theme or NextGEN Pro/Plus), then PHP will output strict warnings
		if ($this->is_not_activating() && !$this->is_topscorer_request()) {
			$this->_define_constants();
			$this->_load_non_pope();
			$this->_register_hooks();
			$this->_load_pope();
		}
	}

	function is_topscorer_request()
	{
		return strpos($_SERVER['REQUEST_URI'], 'topscorer/v1') !== FALSE;
	}

	function is_not_activating()
	{
		return !$this->is_activating();
	}

	function is_activating()
	{
        $retval =  strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== FALSE && isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('activate-selected'));

		if (!$retval && strpos($_SERVER['REQUEST_URI'], 'update.php') !== FALSE && isset($_REQUEST['action']) && $_REQUEST['action'] == 'install-plugin' && isset($_REQUEST['plugin']) && strpos($_REQUEST['plugin'], 'nextgen-gallery') === 0) {
			$retval = TRUE;
		}

		if (!$retval && strpos($_SERVER['REQUEST_URI'], 'update.php') !== FALSE && isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate-plugin' && isset($_REQUEST['plugin']) && strpos($_REQUEST['plugin'], 'nextgen-gallery') === 0) {
			$retval = TRUE;
		}

		return $retval;
	}

	function _load_non_pope()
	{
		// Load caching component
		include_once('non_pope/class.photocrati_transient_manager.php');
		include_once('non_pope/class.nextgen_serializable.php');

		// Load Settings Manager
		include_once('non_pope/class.photocrati_settings_manager.php');
		include_once('non_pope/class.nextgen_settings.php');
		C_Photocrati_Global_Settings_Manager::$option_name = $this->_settings_option_name;
		C_Photocrati_Settings_Manager::$option_name = $this->_settings_option_name;

		// Load the installer
		include_once('non_pope/class.photocrati_installer.php');

		// Load the (mostly deprecated) resource manager
		include_once('non_pope/class.photocrati_resource_manager.php');
		C_Photocrati_Resource_Manager::init();

		// Load the shortcode manager
		include_once('non_pope/class.nextgen_shortcode_manager.php');
		C_NextGen_Shortcode_Manager::get_instance();
	}

	function fix_loading_order()
	{
        // If a plugin wasn't activated/deactivated siliently, we can listen for these things
	    if (did_action('activate_plugin') || did_action('deactivate_plugin')) return;
	    else if (strpos($_SERVER['REQUEST_URI'], 'plugins') !== FALSE) return;
	    else if (!$this->is_page_request()) return;

		$plugins = get_option('active_plugins');

		// Remove NGG from the list
        $ngg = basename(dirname(__FILE__)).'/'.basename(__FILE__);
        $order = array();
        foreach ($plugins as $plugin) {
            if ($plugin != $ngg) $order[] = $plugin;
        }


        // Get the position of either NGG Pro or NGG Plus
        $insert_at = FALSE;
        for($i=0; $i<count($order); $i++) {
            $plugin = $order[$i];
            if (strpos($plugin, 'nggallery-pro') !== FALSE) $insert_at = $i+1;
            else if (strpos($plugin, 'ngg-plus') !== FALSE) $insert_at = $i+1;
        }

        // Re-insert NGG after Pro or Plus
        if ($insert_at === FALSE || $insert_at === count($order)) $order[] = $ngg;
        elseif ($insert_at === 0) array_unshift($order, $ngg);
        else array_splice($order, $insert_at, 0, array($ngg));
		
		if ($order != $plugins) {
		    $order = array_filter($order);
			update_option('active_plugins', $order);
		}
	}

	/**
	 * Loads the Pope Framework
	 */
	function _load_pope()
	{
		// No need to initialize pope again
		if ($this->_pope_loaded) return;

		// Pope requires a a higher limit
		$tmp = ini_get('xdebug.max_nesting_level');
		if ($tmp && (int)$tmp <= 300) @ini_set('xdebug.max_nesting_level', 300);

		// Include pope framework
		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php');

		// Enable/disable pope caching. For now, the pope cache will not be used in multisite environments
		if (class_exists('C_Pope_Cache')) {
			if ((C_Pope_Cache::$enabled = NGG_POPE_CACHE)) {
				$blogid = (is_multisite() ? get_current_blog_id() : NULL);
				if (isset($_SERVER['SERVER_ADDR']))
					$cache_key_prefix = abs(crc32((implode('|', array($blogid, site_url(), AUTH_KEY, $_SERVER['SERVER_ADDR'])))));
				else
					$cache_key_prefix = abs(crc32(implode('|', array($blogid, site_url(), AUTH_KEY))));

				C_Pope_Cache::set_driver('C_Pope_Cache_SingleFile');
				C_Pope_Cache::add_key_prefix($cache_key_prefix);
			}
		}

		// Enforce interfaces
		if (property_exists('ExtensibleObject', 'enforce_interfaces')) ExtensibleObject::$enforce_interfaces = EXTENSIBLE_OBJECT_ENFORCE_INTERFACES;

		// Get the component registry
		$this->_registry = C_Component_Registry::get_instance();

		// Add the default Pope factory utility, C_Component_Factory
		$this->_registry->add_utility('I_Component_Factory', 'C_Component_Factory');

		// Blacklist any modules which are known NOT to work with this version of NextGEN Gallery
		// We need to check if we have this ability as it's only available with Pope 0.9
		if (method_exists($this->_registry, 'blacklist_module_file')) {
			$this->_registry->blacklist_module_file('module.nextgen_pro_lightbox_legacy.php');
			$this->_registry->blacklist_module_file('module.protect_image.php');
			// TODO: Add module id for protect image
		}

		// If Pro is incompatible, then we need to blacklist all of Pro's modules
		// TODO: Pope needs a better way of introspecting into a product's list of provided modules
		if ($this->is_pro_incompatible()) {
			$pro_modules = array(
				'photocrati-comments',
				'photocrati-galleria',
				'photocrati-nextgen_pro_slideshow',
				'photocrati-nextgen_pro_horizontal_filmstrip',
				'photocrati-nextgen_pro_thumbnail_grid',
				'photocrati-nextgen_pro_blog_gallery',
				'photocrati-nextgen_pro_film',
				'photocrati-nextgen_pro_masonry',
				'photocrati-nextgen_pro_albums',
				'photocrati-nextgen_pro_lightbox',
				'photocrati-nextgen_pro_lightbox_legacy',
				'photocrati-nextgen_pro_ecommerce',
				'photocrati-paypal_express_checkout',
				'photocrati-paypal_standard',
				'photocrati-stripe'
			);
			foreach ($pro_modules as $mod) $this->_registry->blacklist_module_file($mod);
		}

		// Load embedded products. Each product is expected to load any
		// modules required
		$this->_registry->add_module_path(NGG_PRODUCT_DIR, 2, false);
		$this->_registry->load_all_products();

		// Give third-party plugins that opportunity to include their own products
		// and modules
		do_action('load_nextgen_gallery_modules', $this->_registry);

		// Initializes all loaded modules
		$this->_registry->initialize_all_modules();

		$this->_pope_loaded = TRUE;
	}

	function is_pro_compatible()
	{
		$retval = TRUE;

        if (defined('NEXTGEN_GALLERY_PRO_VERSION')) $retval = FALSE;
        if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME') && !defined('NGG_PRO_PLUGIN_VERSION')) $retval = FALSE; // 1.0 - 1.0.6
		if (defined('NGG_PRO_PLUGIN_VERSION')  && version_compare(NGG_PRO_PLUGIN_VERSION,  $this->minimum_ngg_pro_version)  < 0) $retval = FALSE;
        if (defined('NGG_PLUS_PLUGIN_VERSION') && version_compare(NGG_PLUS_PLUGIN_VERSION, $this->minimum_ngg_plus_version) < 0) $retval = FALSE;

		return $retval;
	}

	function is_pro_incompatible()
	{
		return !$this->is_pro_compatible();
	}

	function render_incompatibility_warning()
	{
		echo '<div class="updated error"><p>';
		echo esc_html(
			sprintf(
				__("NextGEN Gallery %s is incompatible with this version of NextGEN Pro. Please update NextGEN Pro to version %s or higher to restore NextGEN Pro functionality.",
					'nggallery'
				),
				NGG_PLUGIN_VERSION, $this->minimum_ngg_pro_version
			));
		echo '</p></div>';
	}

	public function render_jquery_wp_55_warning()
    {
		$render  = false;
		global $wp_version;

        if (defined('NGG_PRO_PLUGIN_VERSION')  && version_compare(NGG_PRO_PLUGIN_VERSION,  '3.1')  < 0)
        {
			$render = TRUE;
			$message = __("Your version of NextGEN Pro is known to have some issues with NextGEN Gallery 3.4 and later.", 'nggallery');
			$account_msg = preg_match("#photocrati#i", wp_get_theme()->get('Name'))
				? sprintf(__("Please download the latest version of NextGEN Pro from your <a href='%s' target='_blank'>account area</a>", 'nggallery'), 'https://members.photocrati.com/account/')
				: sprintf(__("Please download the latest version of NextGEN Pro from your <a href='%s' target='_blank'>account area</a>", 'nggallery'), 'https://www.imagely.com/account/');
        }

        if (defined('NGG_PLUS_PLUGIN_VERSION') && version_compare(NGG_PLUS_PLUGIN_VERSION, '1.7') < 0)
        {
            $render = TRUE;
			$message = __("Your version of NextGEN Plus is known to have some issues with NextGEN Gallery 3.4 and later.", 'nggallery');
			$account_msg = preg_match("#photocrati#i", wp_get_theme()->get('Name'))
				? sprintf(__("Please download the latest version of NextGEN Plus from your <a href='%s' target='_blank'>account area</a>", 'nggallery'), 'https://members.photocrati.com/account/')
				: sprintf(__("Please download the latest version of NextGEN Plus from your <a href='%s' target='_blank'>account area</a>", 'nggallery'), 'https://www.imagely.com/account/');
        }

        if (!$render)
            return;

        print '<div class="updated error"><p>';
		print $message;
		print ' ';
		print $account_msg;
		
		if ( version_compare( $wp_version, '5.5', '>=' )  && version_compare( $wp_version, '5.5.9', '<=') ) {
			$note = __("NOTE: The autoupdater doesn't work on the version of WordPress you have installed.", 'ngallery');
			print "<div style='font-weight: bold;'>";
			print $note;
			print "</div>";
		}
        print '</p></div>';
    }


	/**
	 * Registers hooks for the WordPress framework necessary for instantiating
	 * the plugin
	 */
	function _register_hooks()
	{
		// Register the (de)activation routines
		add_action('deactivate_' . NGG_PLUGIN_BASENAME, array(get_class(), 'deactivate'));
		add_action('activate_'   . NGG_PLUGIN_BASENAME, array(get_class(), 'activate'), -10);
		
		// Handle activation redirect to overview page
		add_action('init', array($this, 'handle_activation_redirect'));

		// Ensure that settings manager is saved as an array
		add_filter('pre_update_option_' . $this->_settings_option_name, array($this, 'persist_settings'));
		add_filter('pre_update_site_option_' . $this->_settings_option_name, array($this, 'persist_settings'));

		// Delete displayed gallery transients periodically
		if (NGG_CRON_ENABLED) {
			add_filter('cron_schedules', array(&$this, 'add_ngg_schedule'));
			add_action('ngg_delete_expired_transients', array($this, 'delete_expired_transients'));
			add_action('wp', array(&$this, 'schedule_cron_jobs'));
		}

		// Update modules
		add_action('init', array(&$this, 'update'), PHP_INT_MAX-2);

		// Start the plugin!
		add_action('init', array(&$this, 'route'), 11);

		// Flush pope cache
		add_action('init', array(&$this, 'flush_pope_cache'));

		// NGG extension plugins should be loaded in a specific order
        add_action('shutdown', array(&$this, 'fix_loading_order'));

		// Display a warning if an compatible version of NextGEN Pro is installed alongside this version of NextGEN Gallery
		if ($this->is_pro_incompatible()) {
			add_filter('http_request_args', array($this, 'fix_autoupdate_api_requests'), 10, 2);
			add_action('all_admin_notices', array($this, 'render_incompatibility_warning'));
		}

		add_action('all_admin_notices', [$this, 'render_jquery_wp_55_warning']);
	}

	function handle_activation_redirect()
	{
		if (get_transient('ngg-activated')) {
			delete_transient('ngg-activated');
			wp_redirect(admin_url("?page=nextgen-gallery"));
		}
	}

	function fix_autoupdate_api_requests($args, $url)
	{
		// Is this an HTTP request to the licensing server?
		if (preg_match("/api_act=/", $url)) {
			$args['autoupdate'] = TRUE;

			// If we're supposed to pass all Pro modules, then include them here
			if (preg_match("/api_act=(ckups|cklic)/", $url) && isset($args['body']) && is_array($args['body']) && isset($args['body']['module-list'])) {
				$pro_modules = array(
					'photocrati-comments',
					'photocrati-galleria',
					'photocrati-nextgen_pro_slideshow',
					'photocrati-nextgen_pro_horizontal_filmstrip',
					'photocrati-nextgen_pro_thumbnail_grid',
					'photocrati-nextgen_pro_blog_gallery',
					'photocrati-nextgen_pro_film',
					'photocrati-nextgen_pro_masonry',
					'photocrati-nextgen_pro_albums',
					'photocrati-auto_update',
					'photocrati-auto_update-admin',
					'photocrati-nextgen_pro_lightbox',
					'photocrati-nextgen_pro_lightbox_legacy',
					'photocrati-nextgen_pro_ecommerce',
					'photocrati-paypal_express_checkout',
					'photocrati-paypal_standard',
					'photocrati-stripe'
				);
				foreach ($pro_modules as $mod) {
					if (!isset($args['body']['module-list'][$mod])) $args['body']['module-list'][$mod] = '0.1';
				}
			}
		}
		return $args;
	}

	function flush_pope_cache()
	{
		if (is_user_logged_in() && current_user_can('manage_options') && isset($_REQUEST['ngg_flush_pope_cache'])) {
			C_Pope_Cache::get_instance()->flush();
			print "Flushed pope cache";
			exit;
		}
	}

	function schedule_cron_jobs()
	{
		if (!wp_next_scheduled('ngg_delete_expired_transients')) {
			wp_schedule_event(time(), 'ngg_custom', 'ngg_delete_expired_transients');
		}
	}

	/**
	 * Defines a new cron schedule
	 * @param $schedules
	 * @return mixed
	 */
	function add_ngg_schedule($schedules)
	{
		$schedules['ngg_custom'] = array(
			'interval'	=>	NGG_CRON_SCHEDULE,
			'display'	=>	sprintf(__('Every %d seconds', 'nggallery'), NGG_CRON_SCHEDULE)
		);

		return $schedules;
	}


	/**
	 * Flush all expires transients created by the plugin
	 */
	function delete_expired_transients()
	{
        C_Photocrati_Transient_Manager::get_instance()->flush_expired();
	}

	/**
	 * Ensure that C_Photocrati_Settings_Manager gets persisted as an array
	 * @param C_Photocrati_Settings_Manager_Base|array $settings
	 * @return array
	 */
	function persist_settings($settings = array())
	{
		if (is_object($settings) && $settings instanceof C_Photocrati_Settings_Manager_Base) {
			$settings = $settings->to_array();
		}
		return $settings;
	}

	/**
	 * Updates all modules
	 */
	function update()
	{
		if ((!(defined('DOING_AJAX') && DOING_AJAX)) && !isset($_REQUEST['doing_wp_cron'])) {

			$this->_load_pope();

			// Try updating all modules
			C_Photocrati_Installer::update();
		}
	}

	/**
	 * Routes access points using the Pope Router
	 * @return boolean
	 */
	function route()
	{
		$this->_load_pope();
		$router = C_Router::get_instance();

		// Set context to path if subdirectory install
		$parts     = parse_url($router->get_base_url(FALSE));
		$siteparts = parse_url(get_option('home'));

        if (isset($parts['path']) && isset($siteparts['path']))
        {
            if (strpos($parts['path'], '/index.php') === FALSE)
            {
                $router->context = $siteparts['path'];
            }
            else {
                $new_parts = explode('/index.php', $parts['path']);
                if (!empty($new_parts[0]) && $new_parts[0] == $siteparts['path']) {
                    $router->context = array_shift($new_parts);
                }
            }
        }

        // Provide a means for modules/third-parties to configure routes
		do_action_ref_array('ngg_routes', array(&$router));

		// Serve the routes
		if (!$router->serve_request() && $router->has_parameter_segments()) {
			return $router->passthru();
		}
	}

    function is_page_request()
    {
        return !(defined('DOING_AJAX') && DOING_AJAX) && !(defined('DOING_CRON') && DOING_CRON) && !(defined('NGG_AJAX_SLUG') && strpos($_SERVER['REQUEST_URI'], NGG_AJAX_SLUG) !== FALSE);
    }

	/**
	 * Run the uninstaller
	 */
	public static function deactivate()
	{
        include_once('products/photocrati_nextgen/class.nextgen_product_installer.php');
        C_Photocrati_Installer::add_handler(NGG_PLUGIN_BASENAME, 'C_NextGen_Product_Installer');
		C_Photocrati_Installer::uninstall(NGG_PLUGIN_BASENAME);
	}

	public static function set_role_caps()
	{
		// Set the capabilities for the administrator
        $role = get_role('administrator');

        if (!$role)
        {
            if (!class_exists('WP_Roles'))
                include_once(ABSPATH.'/wp-includes/class-wp-roles.php');
            $roles = new WP_Roles();
            $roles->init_roles();
        }

        // We need this role, no other chance
        $role = get_role('administrator');
        if (!$role)
        {
            update_option("ngg_init_check", __('Sorry, NextGEN Gallery works only with a role called administrator',"nggallery"));
            return;
        }

        delete_option("ngg_init_check");

        $capabilities = array(
            'NextGEN Attach Interface',
            'NextGEN Change options',
            'NextGEN Change style',
            'NextGEN Edit album',
            'NextGEN Gallery overview',
            'NextGEN Manage gallery',
            'NextGEN Manage others gallery',
            'NextGEN Manage tags',
            'NextGEN Upload images',
            'NextGEN Use TinyMCE'
        );

        foreach ($capabilities as $capability) {
            $role->add_cap($capability);
		}
	}

	public static function activate()
    {
        self::set_role_caps();
		
		set_transient('ngg-activated', time(), 30);
    }

	/**
	 * Defines necessary plugins for the plugin to load correctly
	 */
	function _define_constants()
	{
		define('NGG_PLUGIN', basename($this->directory_path()));
		define('NGG_PLUGIN_BASENAME', plugin_basename(__FILE__));
		define('NGG_PLUGIN_DIR', plugin_dir_path(__FILE__));
		define('NGG_PLUGIN_URL', $this->path_uri());
		define('NGG_TESTS_DIR',   implode(DIRECTORY_SEPARATOR, array(rtrim(NGG_PLUGIN_DIR, "/\\"), 'tests')));
		define('NGG_PRODUCT_DIR', implode(DIRECTORY_SEPARATOR, array(rtrim(NGG_PLUGIN_DIR, "/\\"), 'products')));
		define('NGG_MODULE_DIR', implode(DIRECTORY_SEPARATOR, array(rtrim(NGG_PRODUCT_DIR, "/\\"), 'photocrati_nextgen', 'modules')));
		define('NGG_PRODUCT_URL', path_join(str_replace("\\" , '/', NGG_PLUGIN_URL), 'products'));
		define('NGG_MODULE_URL', path_join(str_replace("\\", '/', NGG_PRODUCT_URL), 'photocrati_nextgen/modules'));
		define('NGG_PLUGIN_STARTED_AT', microtime());
		define('NGG_PLUGIN_VERSION', '3.37');

		define(
			'NGG_SCRIPT_VERSION',
			defined('SCRIPT_DEBUG') && SCRIPT_DEBUG
				? (string)mt_rand(0, mt_getrandmax())
				: NGG_PLUGIN_VERSION
		);

		// Should we display NGG debugging information?
		if (!defined('NGG_DEBUG')) {
			define('NGG_DEBUG', FALSE);
		}
		self::$debug = NGG_DEBUG;

		// User definable constants
		if (!defined('NGG_IMPORT_ROOT')) {
			$path = WP_CONTENT_DIR;
			if (defined('NEXTGEN_GALLERY_IMPORT_ROOT')) {
				$path = NEXTGEN_GALLERY_IMPORT_ROOT;
			}
			define('NGG_IMPORT_ROOT', $path);
		}

		// Should the Photocrati cache be enabled
		if (!defined('PHOTOCRATI_CACHE')) {
			define('PHOTOCRATI_CACHE', TRUE);
		}
		if (!defined('PHOTOCRATI_CACHE_TTL')) {
			define('PHOTOCRATI_CACHE_TTL', 1800);
		}

		// Cron job
		if (!defined('NGG_CRON_SCHEDULE')) {
			define('NGG_CRON_SCHEDULE', 900);
		}

		if (!defined('NGG_CRON_ENABLED')) {
			define('NGG_CRON_ENABLED', TRUE);
		}

		// Don't enforce interfaces
		if (!defined('EXTENSIBLE_OBJECT_ENFORCE_INTERFACES')) {
			define('EXTENSIBLE_OBJECT_ENFORCE_INTERFACES', FALSE);
		}

		// Use Pope's new caching mechanism?
		if (!defined('NGG_POPE_CACHE')) {
			define('NGG_POPE_CACHE', FALSE);
		}

		// Where are galleries restricted to?
		if (!defined('NGG_GALLERY_ROOT_TYPE')) {
			define('NGG_GALLERY_ROOT_TYPE', 'site'); // "content" is the other possible value
		}

		// Define what file extensions and mime are accepted, with optional WebP
        $default_extensions_list = 'jpeg,jpg,png,gif';
		$default_mime_list = 'image/gif,image/jpg,image/jpeg,image/pjpeg,image/png';
		if (function_exists('imagewebp'))
        {
            $default_extensions_list .= ',webp';
            $default_mime_list .= ',image/webp';
        }

		if (!defined('NGG_DEFAULT_ALLOWED_FILE_TYPES'))
            define('NGG_DEFAULT_ALLOWED_FILE_TYPES', $default_extensions_list);

		if (!defined('NGG_DEFAULT_ALLOWED_MIME_TYPES'))
            define('NGG_DEFAULT_ALLOWED_MIME_TYPES', $default_mime_list);

		add_filter('ngg_allowed_file_types', function($string) {
		    return explode(',', $string);
        }, -10);

        add_filter('ngg_allowed_mime_types', function($string) {
            return explode(',', $string);
        }, -10);
	}

	/**
	 * Returns the path to a file within the plugin root folder
     *
	 * @param string $file_name
	 * @return string
	 */
	function file_path($file_name = NULL)
	{
		$path = dirname(__FILE__);
		if ($file_name != null)
			$path .= '/' . $file_name;

		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Gets the directory path used by the plugin
     *
     * @param string|null $dir (optional)
	 * @return string
	 */
	function directory_path($dir = NULL)
	{
		return $this->file_path($dir);
	}

	/**
	 * Determine the path to the NGG directory.
     *
     * @TODO Remove this, it's silly.
	 * @return string
	 */
	function path_uri()
	{
        // Note: paths could not match but STILL being contained in the theme (i.e. WordPress returns the wrong path for the theme directory, either with wrong formatting or wrong encoding)
        $base = basename(dirname(__FILE__));

        // This is needed when using symlinks, if the user renames the plugin folder everything will break though
        if ($base != 'nextgen-gallery')
            $base = 'nextgen-gallery';

        $uri = plugins_url($base);

		return $uri;
	}
}

new C_NextGEN_Bootstrap();
