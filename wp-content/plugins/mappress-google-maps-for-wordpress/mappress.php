<?php
/*
Plugin Name: MapPress Google Maps and Leaflet Maps
Plugin URI: https://www.mappresspro.com
Author URI: https://www.mappresspro.com
Pro Update URI: https://www.mappresspro.com
Description: MapPress makes it easy to add Google Maps and Leaflet Maps to WordPress
Version: 2.88.18
Author: Chris Richardson
Text Domain: mappress-google-maps-for-wordpress
Thanks to all the translators and to Scott DeJonge for his wonderful icons
*/

/*
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the license.txt file for details.
*/

require_once dirname( __FILE__ ) . '/mappress_api.php';
require_once dirname( __FILE__ ) . '/mappress_compliance.php';
require_once dirname( __FILE__ ) . '/mappress_db.php';
require_once dirname( __FILE__ ) . '/mappress_obj.php';
require_once dirname( __FILE__ ) . '/mappress_poi.php';
require_once dirname( __FILE__ ) . '/mappress_map.php';
require_once dirname( __FILE__ ) . '/mappress_settings.php';
include_once dirname( __FILE__ ) . '/mappress_template.php';
include_once dirname( __FILE__ ) . '/mappress_wpml.php';

if (is_dir(dirname( __FILE__ ) . '/pro')) {
	include_once dirname( __FILE__ ) . '/pro/mappress_filter.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_frontend.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_geocoder.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_icons.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_import.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_meta.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_query.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_updater.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget_map.php';
}

class Mappress {
	const VERSION = '2.88.18';

	static
		$api,
		$baseurl,
		$basename,
		$basedir,
		$block_category = 'text',
		$debug,
		$loaded,
		$options,
		$notices,
		$pages,
		$pro,
		$updater,
		$version
		;

	function __construct()  {
		global $wp_version;
		self::$basedir = dirname(__FILE__);
		self::$basename = plugin_basename(__FILE__);
		self::$baseurl = plugins_url('', __FILE__);
		self::$options = Mappress_Options::get();
		self::$pro = is_dir(dirname( __FILE__ ) . '/pro');
		self::$version = (self::$pro) ? self::VERSION . "PRO" : self::VERSION;
		self::$version = (defined('MAPPRESS_DEV') && MAPPRESS_DEV) ? self::$version . '-' . rand(0,99999) : self::$version;
		self::$api = new Mappress_Api();

		self::debugging();

		if (self::$pro)
			self::$updater = new Mappress_Updater(self::$basename, 'mappress', self::VERSION, self::$options->license, self::$options->betas);

		add_action('admin_menu', array(__CLASS__, 'admin_menu'));
		add_action('init', array(__CLASS__, 'init'), 0);	// Priority 0 required for widgets_init hook
		add_action('plugins_loaded', array(__CLASS__, 'plugins_loaded'));

		add_shortcode('mappress', array(__CLASS__, 'shortcode_map'));
		add_action('admin_notices', array(__CLASS__, 'admin_notices'));

		// Filter to automatically add maps to post/page content
		add_filter('the_content', array(__CLASS__, 'the_content'), 2);

		// Namespace
		add_action('wp_head', array(__CLASS__, 'wp_head'), 0);
		add_action('admin_head', array(__CLASS__, 'wp_head'), 0);

		// Scripts and stylesheets
		add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));

		// Plugin action links
		add_filter("plugin_action_links_" . self::$basename, array(__CLASS__, 'plugin_action_links'), 10, 2);

		if (self::$pro)
			add_shortcode('mashup', array(__CLASS__, 'shortcode_mashup'));

		// Adjust google script tag
		if (self::$options->engine == 'google')
			add_filter('script_loader_tag', array(__CLASS__, 'script_loader_tag'), PHP_INT_MAX, 3);

		// Slow heartbeat
		if (self::$debug)
			add_filter( 'heartbeat_settings', array(__CLASS__, 'heartbeat_settings'));

		// Dismissible notices
		add_action('wp_ajax_mapp_dismiss', array(__CLASS__, 'ajax_dismiss' ));

		// Add block category
		if ( version_compare( $wp_version, '5.8-RC4', '>=' ) )
			add_filter( 'block_categories_all', array(__CLASS__, 'block_categories'), 10, 2);
		else
			add_filter( 'block_categories', array(__CLASS__, 'block_categories'), 10, 2 );

		add_filter('mime_types', array(__CLASS__, 'mime_types'));
		add_action('deactivate_' . self::$basename, array(__CLASS__, 'deactivate'));

		// Welcome
		add_action('activate_' . self::$basename, array(__CLASS__, 'activate'), 10, 2);
		add_action('admin_init', array(__CLASS__, 'admin_init'), 10, 2);

		// Iframes
		if (isset($_GET['mappress']) && $_GET['mappress'] = 'embed')
			add_action('template_redirect', array(__CLASS__, 'template_redirect'));

		// Temporary fix for https://core.trac.wordpress.org/ticket/56969
		if (version_compare( $wp_version, '6.1.1', '<' ) )
			add_filter( 'wp_img_tag_add_decoding_attr', array(__CLASS__, 'wp_img_tag_add_decoding_attr'), 10, 3);
	}

	static function wp_img_tag_add_decoding_attr( $value, $filtered_image, $context) {
		return false;
	}

	static function activate($network_wide = false) {
		$current_version = get_option('mappress_version');
		if (!$current_version)
			set_transient('_mappress_activation_redirect', 'wizard', 30);
		else
			set_transient('_mappress_activation_redirect', true, 30);
	}

	static function admin_init() {
		$redirect = get_transient('_mappress_activation_redirect');
		if ($redirect) {
			delete_transient('_mappress_activation_redirect');
			if (is_network_admin() || isset( $_GET['activate-multi'])) {
				return;
			} else {
				$args = array('page' => 'mappress_support', 'wizard' => ($redirect == 'wizard') ? 1 : 0);
				wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
			}
		}
	}

	// Scripts & styles for admin
	// CSS is always loaded from the plugin directory
	static function admin_enqueue_scripts($hook) {
		// Some plugins call this without setting $hook
		if (empty($hook))
			return;

		$pages = (self::$pages) ? self::$pages : array();
		$admin_pages = array(
			'appearance_page_gutenberg-widgets',
			'appearance_page_gutenberg-edit-site',
			'customize.php',
			'edit.php',
			'plugins.php',
			'post.php',
			'post-new.php',
			'site-editor.php',
			'widgets.php'
		);

		if ($hook) {
			self::styles_enqueue('backend');
			if (isset($pages['main']) && $hook == $pages['main']) {
				self::scripts_enqueue('settings');
			} else if (in_array($hook, $pages) || in_array($hook, $admin_pages)) {
				self::scripts_enqueue('backend');
			}
		}
	}

	static function admin_menu() {
		$upgrade = Mappress_Db::upgrade_check();
		$parent = ($upgrade) ? null : 'mappress';

		self::$pages['main'] = add_menu_page('MapPress', 'MapPress', 'manage_options', 'mappress', array('Mappress_Settings', 'options_page'), 'dashicons-location');
		self::$pages['settings'] = add_submenu_page($parent, __('Settings', 'mappress-google-maps-for-wordpress'), __('Settings', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress', array('Mappress_Settings', 'options_page'));
		self::$pages['maps'] = add_submenu_page($parent, __('Maps', 'mappress-google-maps-for-wordpress'), __('Maps', 'mappress-google-maps-for-wordpress'), 'edit_posts', 'mappress_maps', array(__CLASS__, 'map_library'));
		if (self::$pro)
			self::$pages['import'] = add_submenu_page($parent, __('Import', 'mappress-google-maps-for-wordpress'), __('Import', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_import', array('Mappress_Import', 'import_page'));
		self::$pages['support'] = add_submenu_page($parent, __('Support', 'mappress-google-maps-for-wordpress'), __('Support', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_support', array('Mappress_Settings', 'support_page'));
		
		if ($upgrade)
			self::$pages['upgrade'] = add_submenu_page('mappress', __('Upgrade', 'mappress-google-maps-for-wordpress'), __('Upgrade', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_db', array('Mappress_Db', 'upgrade_page'));
	}

	static function admin_notices() {
		global $wpdb;
		$current_screen = get_current_screen();

		$error =  "<div class='notice notice-error'><p>%s</p></div>";
		$maps_table = $wpdb->prefix . "mapp_maps";
		$exists = $wpdb->get_var("show tables like '$maps_table'");

		// Non-dismissible notices
		if (!$exists) {
			printf($error, __("MapPress database tables are missing.  Please deactivate the plugin and activate it again to fix this.", 'mappress-google-maps-for-wordpress'));
			return;
		}

		if (self::$options->engine != 'leaflet' && !self::get_api_keys()->browser)
			printf($error, sprintf("%s. %s <a href='%s'>%s</a>.", __("A Google Maps API key is required", 'mappress-google-maps-for-wordpress'), __("Please update your", 'mappress-google-maps-for-wordpress'), admin_url('admin.php?page=mappress'), __('MapPress Settings', 'mappress-google-maps-for-wordpress')));

		// Notice to upgrade DB
		if (Mappress_Db::upgrade_check() && (!$current_screen || $current_screen->id != self::$pages['upgrade'])) {
			$url = admin_url('admin.php?page=mappress_db');
			$link = sprintf('<a href="%s">%s</a>', $url, __("Upgrade Now", 'mappress-google-maps-for-wordpress'));
			printf($error, sprintf('<strong>' . __('Your MapPress data must be upgraded!  Please %s.' . '</strong>', 'mappress-google-maps-for-wordpress'), $link));
		}

		// Dismissibles
		if (is_super_admin()) {
			$content =  "<div class='notice notice-%s is-dismissible' data-mapp-dismiss='%s'><p>%s</p></div>";
			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
			$notices = (self::$notices) ? array_diff_key(self::$notices, array_flip($dismissed)) : array();

			foreach($notices as $key => $notice)
				printf($content, $notice[0], $key, $notice[1]);

			if ($notices) {
				echo Mappress::script("jQuery('[data-mapp-dismiss]').on('click', '.notice-dismiss, .mapp-dismiss', function(e) {
					var key = jQuery(this).closest('.notice').attr('data-mapp-dismiss');
					jQuery(this).closest('[data-mapp-dismiss]').remove();
					jQuery.post(ajaxurl, { action : 'mapp_dismiss', key : key });
				});");
			}
		}
	}

	/**
	* Dismiss/undismiss admin notices
	*
	* @param mixed $key - notice to dismiss/undismiss
	* @param mixed $dismiss - true to dismiss, false to undismiss
	* @return mixed
	*/
	static function admin_notices_dismiss($key, $dismiss) {
		if (!$key)
			return;

		$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
		if ($dismiss)
			$dismissed[] = $key;
		else
			unset($dismissed[$key]);
		update_user_meta( get_current_user_id(), 'mappress_dismissed', implode( ',', $dismissed ));
	}

	static function ajax_dismiss() {
		// Still sent via jQuery
		$key = (isset($_POST['key'])) ? $_POST['key'] : null;
		if (!$key || sanitize_key( $key) != $key)
			wp_die( 0 );
		self::admin_notices_dismiss($key, true);
		self::ajax_response('OK');
	}

	static function ajax_response($status, $data=null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error
		header( "Content-Type: application/json" );

		// WP bug: when zlib active, warning messages are generated, which corrupt JSON output
		// Ticket has been open for 9 years.  Workaround is to disable flush when providing json response - may cause other conflicts!
		// https://core.trac.wordpress.org/ticket/22430, https://core.trac.wordpress.org/ticket/18525
		if (ini_get('zlib.output_compression'))
			remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

		$response = json_encode(array('status' => $status, 'output' => $output, 'data' => $data));
		die ($response);
	}

	// 5.8 version of block_categories hook
	// Older GT versions send ($categories, $post) instead of ($categories, $context)
	static function block_categories($categories, $context) {
		self::$block_category = 'mappress';
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'mappress',
					'title' => 'MapPress'
				),
			)
		);
	}

	static function deactivate() {
		$reason = (isset($_REQUEST['mapp_reason'])) ? $_REQUEST['mapp_reason'] : null;
		$reason_text = (isset($_REQUEST['mapp_reason_text'])) ? $_REQUEST['mapp_reason_text'] : null;

		if (!$reason || $reason == 'private' || $reason == 'temporary')
			return;

		// Don't bother if there's no reason text
		if (empty($reason_text))
			return;

		// Call API (static functions can't use api_call())
		$args = array(
			'api_action' => 'feedback',
			'network_url' => (is_multisite()) ? trim(network_home_url()) : trim(home_url()),
			'plugin' => 'mappress',
			'reason' => $reason,
			'reason_text' => $reason_text,
			'url' => trim(home_url()),
		);
		$response = wp_remote_post('https://mappresspro.com', array('timeout' => 15, 'sslverify' => false, 'body' => (array) $args));
	}

	static function debugging() {
		global $wpdb;

		if (isset($_GET['mp_info'])) {
			echo "<b>Plugin</b> " . self::$version;
			$maps_table = $wpdb->prefix . 'mapp_maps';
			$results = $wpdb->get_results("SELECT otype, oid, mapid FROM $maps_table");
			echo "<br/>otype/oid => mapid<br/>";
			foreach($results as $i => $result) {
				if ($i > 50)
					break;
				echo "<br/>$result->otype / $result->oid => $result->mapid";
			}
			$options = Mappress_Options::get();
			unset($options->mapbox, $options->license, $options->apiKey, $options->apiKeyServer);
			echo str_replace(array("\r", "\n"), array('<br/>', '<br/>'), print_r($options, true));
			die();
		}

		if (isset($_REQUEST['mp_debug']))
			self::$debug = max(1, (int) $_REQUEST['mp_debug']);
		else if (defined('MAPPRESS_DEBUG') && MAPPRESS_DEBUG)
			self::$debug = true;

		if (self::$debug) {
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors','On');
			$wpdb->show_errors();
		}
	}

	static function get_api_keys() {
		$results = (object) array(
			'browser' => self::$options->apiKey, 
			'server' => self::$options->apiKeyServer, 
			'liq' => self::$options->liq,
			'mapbox' => self::$options->mapbox
		);
		if (empty($results->browser) && defined('MAPPRESS_APIKEY'))
			$results->browser = MAPPRESS_APIKEY;
		if (empty($results->server) && defined('MAPPRESS_APIKEY_SERVER'))
			$results->server = MAPPRESS_APIKEY_SERVER;
		if (empty($results->mapbox) && defined('MAPPRESS_APIKEY_MAPBOX'))
			$results->mapbox = MAPPRESS_APIKEY_MAPBOX;
		return $results;
	}    

	static function get_iframe($map) {
		$styles = new WP_Styles();
		$scripts = new WP_Scripts();
		self::scripts_register($scripts);
		self::scripts_enqueue('frontend', $scripts);
		self::styles_register($styles);
		self::styles_enqueue('frontend', $styles);

		$content = $map->display(null, true);

		ob_start();
		?>
		<!doctype html>
		<html class='mapp-iframe-html' <?php language_attributes(); ?>>
		<head>
			<title>MapPress</title>
			<?php Mappress::wp_head(); ?>
			<?php $styles->do_items(array('mappress', 'mappress-custom')); ?>
		</head>
		<body class='mapp-iframe-body'>
			<?php echo $content; ?>
			<?php $scripts->do_items('mappress'); ?>
			<?php Mappress_Template::print_footer_templates(); ?>
			<script type='javascript'>mappload();</script>
		</body>
		</html>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	* Get language using settings/WPML/qTrans
	*
	*/
	static function get_language() {
		// WPML
		if (defined('ICL_LANGUAGE_CODE'))
			$lang = ICL_LANGUAGE_CODE;

		// qTranslate
		else if (function_exists('qtrans_getLanguage'))
			$lang = qtrans_getLanguage();

		else
			$lang = self::$options->language;

		return ($lang) ? $lang : null;
	}

	/**
	* Get a mashup - used by shortcode and widget
	*
	* @param mixed $atts
	*/
	static function get_mashup($atts) {
		global $wp_query;
													
		$mashup = new Mappress_Map($atts);
		$mashup->otype = (isset($atts['otype']) && $atts['otype'] == 'user') ? 'user' : 'post';
		$mashup->query = Mappress_Query::parse_query($atts);

		// If parameter test="true", output the query result (or global query) without using a map
		if (isset($_GET['mp_test']) || (isset($atts['test']) && $atts['test'])) {
			$wpq = ($mashup->query) ? new WP_Query($mashup->query) : $wp_query;
			return "<pre>" . print_r($wpq, true) . "</pre>";
		}

		// If 'hideEmpty' is set, try to suppress the map if there are no POIs
		if ($mashup->hideEmpty) {
			if (Mappress_Query::is_empty($mashup->query))
				return "";
		}
		return $mashup->display();
	}

	static function heartbeat_settings( $settings ) {
		$settings['minimalInterval'] = 600;
		return $settings;
	}

	/**
	* There are several WP bugs that prevent correct activation in multisitie:
	*   http://core.trac.wordpress.org/ticket/14170
	*   http://core.trac.wordpress.org/ticket/14718)
	*
	*/
	static function init() {
		Mappress_Compliance::register();
		Mappress_Db::register();
		Mappress_Map::register();
		Mappress_Settings::register();
		Mappress_Template::register();
		Mappress_WPML::register();

		if (self::$pro) {
			Mappress_Filter::register();
			Mappress_Frontend::register();
			Mappress_Icons::register();
			Mappress_Import::register();
			Mappress_Meta::register();
			Mappress_Query::register();
			Mappress_Widget::register();
			Mappress_Widget_Map::register();
		}

		self::styles_register();
		self::scripts_register();

		// Register Gutenberg block types and load GT scripts
		if (function_exists('register_block_type')) {
			register_block_type('mappress/map', array(
				'render_callback' => array(__CLASS__, 'shortcode_map'),
				'editor_script' => array('mappress_admin'),
				'style' => 'mappress',
				'editor_style' => 'mappress-admin'
			));
			if (self::$pro) {
				register_block_type('mappress/mashup', array(
					'render_callback' => array(__CLASS__, 'shortcode_mashup'),
					'editor_script' => array('mappress_admin'),
					'style' => 'mappress',
					'editor_style' => 'mappress-admin'
				));
			}
		}

		// Check if upgrade is needed
		$current_version = get_option('mappress_version');

		if (empty($current_version)) {
			$args = array(
				'api_action' => 'feedback',
				'network_url' => (is_multisite()) ? trim(network_home_url()) : trim(home_url()),
				'plugin' => 'mappress',
				'reason' => 'new',
				'reason_text' => '',
				'url' => trim(home_url()),
			);
			$response = wp_remote_post('https://mappresspro.com', array('timeout' => 15, 'sslverify' => false, 'body' => (array) $args));
		}

		// Algolia geocoder discontinued since 2.69.3
		if (empty(self::$options->geocoder || self::$options->geocoder == 'algolia')) {
			self::$options->geocoder = 'nominatim';
			self::$options->save();
		}

		// Check for license expired
		if (self::$pro && self::$options->license) {
			$last_check = get_option('mappress_license_check');
			if (!$last_check || time() > $last_check + (60 * 60 * 24 * 7)) {
				$status = Mappress::$updater->get_status();
				if ($status == 'inactive') {
					$renew_link = sprintf("<a target='_blank' href='https://mappresspro.com/account'>%s</a>", __('Renew your license', 'mappress-google-maps-for-wordpress'));
					self::admin_notices_dismiss('expiredlicense', false);
					self::$notices['expiredlicense'] = sprintf(__('Your MapPress license has expired.  %s to get the latest updates and prevent errors.', 'mappress-google-maps-for-wordpress'), $renew_link);
				}
				update_option('mappress_license_check', time());
				return;
			}
		}

		// Missing license
		if (self::$pro && empty(self::$options->license) && (!is_multisite() || (is_super_admin() && is_main_site())))
			self::$notices['nolicense'] = array('warning', __('Please enter your MapPress license key to enable plugin updates', 'mappress-google-maps-for-wordpress'));

		if (self::VERSION >= '2.55' && version_compare(get_bloginfo('version'),'5.3', '<') )
			self::$notices['255_min_version'] = array('error', __('MapPress Gutenberg blocks require WordPress 5.3 or the latest Gutenberg Plugin. Please update if using the block editor.', 'mappress-google-maps-for-wordpress'));

		if ($current_version && $current_version < '2.55' && self::VERSION >= '2.55')
			self::$notices['255_whats_new'] = array('info', sprintf(__('MapPress has many new features!  %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

		if ($current_version && $current_version < '2.60' && self::VERSION >= '2.60')
			self::$notices['260_whats_new'] = array('warning', sprintf(__('MapPress templates have changed!  Please update custom templates to the new format. %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

		// Upgrades
		if ($current_version) {
			if (version_compare($current_version, '2.63', '<')) {
				// New list templates
				self::$notices['263_whats_new'] = array('warning', sprintf(__('MapPress templates and filters have changed.  Please update custom templates and filters. %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

				// Convert filters to array
				if (self::$options->filter) {
					self::$options->filters = array(array('key' => self::$options->filter));
					self::$options->save();
				}

				// Convert styles to indexed arrays
				if (self::$options->styles && is_array(self::$options->styles)) {
					self::$options->stylesGoogle = array();
					self::$options->stylesMapbox = array();
					foreach(self::$options->styles as $name => $json)
						self::$options->stylesGoogle[] = array('id' => $name, 'name' => $name, 'url' => null, 'json' => $json, 'imageUrl' => self::$baseurl . '/images/roadmap.png');
					foreach(self::$options->mapboxStyles as $name => $url) {
						$parts = explode('?', strtolower($url));
						$short_url = str_ireplace(array('.html', 'https://api.mapbox.com/styles/v1/', 'mapbox://styles/'), '', $parts[0]);
						$parts = explode('/', $short_url);
						if (count($parts) == 2)
							self::$options->stylesMapbox[] = array('url' => $url, 'provider' => 'mapbox', 'user' => $parts[0], 'id' => $name, 'mapboxid' => $parts[1], 'name' => $name);
					}
					self::$options->save();
				}
			}

			// 2.73 Add a type to all filters
			if (version_compare($current_version, '2.73', '<')) {
				foreach(self::$options->filters as &$filter) {
					if (empty($filter['type']))
						$filter['type'] = 'tax';
				}
				self::$options->save();
			}

			// 2.76 New templates
			if (version_compare($current_version, '2.76', '<'))
				self::$notices['276_whats_new'] = array('warning', sprintf(__('MapPress templates have changed!  Please update custom templates to the new format. %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

			// 2.80 - DB upgrade, filters and meta
			if (version_compare($current_version, '2.80', '<')) {
				// Convert filters and meta to include users
				self::$options->filters = array('post' => self::$options->filters, 'user' => array());
				self::$options->metaKeys = array('post' => self::$options->metaKeys, 'user' => array());
				self::$options->save();

				// trigger DB ugprade by setting db_version lower than current version
				update_option('mappress_db_version', '2.79');
				Mappress_Db::upgrade();
			}

			// 2.84 - copy mashupbody setting => mashupthumbs (poi | post)
			if (version_compare($current_version, '2.84', '<'))
				self::$options->mashupThumbs = self::$options->mashupBody;

			// 2.85 - rename filters type checkbox=>checkboxes and radio=>radios
			if (version_compare($current_version, '2.85', '<')) {
				foreach(['post', 'user'] as $type) {
					$filters = self::$options->filters[$type] ?? [];
					foreach($filters as &$filter) {
						if ($filter['format'] == 'radio')
							$filter['format'] = 'radios';
						else if ($filter['format'] == 'checkbox')
							$filter['format'] = 'checkboxes';
					}
					self::$options->filters[$type] = $filters;
				}
				self::$options->save();
			}
		}

		update_option('mappress_version', self::VERSION);
	}

	// Prevent shortcodes on admin screens
	static function is_admin() {
		$ajax = defined('DOING_AJAX') && DOING_AJAX;
		$rest = defined('REST_REQUEST') && REST_REQUEST;
		return (is_admin() && !$ajax) || $rest;
	}

	static function is_dev() {
		if (defined('MAPPRESS_DEV') && MAPPRESS_DEV)
			return MAPPRESS_DEV;
		else if (isset($_REQUEST['mp_dev']))
			return ($_REQUEST['mp_dev']) ? $_REQUEST['mp_dev'] : 'dev';
		else
			return false;
	}

	static function is_footer() {
		if (defined('DOING_AJAX') && DOING_AJAX)
			return false;
		if (defined('REST_REQUEST') && REST_REQUEST)
			return true;
		if (is_admin())
			return true;
		return self::$options->footer;
	}

	static function is_localhost() {
		return !filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	static function is_plugin_active($plugin) {
		$plugins = array('complianz' => 'complianz-gdpr/complianz-gpdr.php', 'amp' => 'amp/amp.php');
		if (array_key_exists($plugin, $plugins))
			$plugin = $plugins[$plugin];

		// Can't use WP's is_plugin_active on frontend w/o including WP files
		if (in_array($plugin, (array) get_option('active_plugins', array()), true))
			return true;
		if (is_multisite() && in_array($plugin, (array) get_option('active_sitewide_plugins', array()), true))
			return true;
		return false;
	}

	static function is_ssl() {
		return (is_ssl() || !filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
	}

	static function l10n() {
		global $post, $is_IE;

		$l10n = array('delete_prompt' => __('Are you sure you want to delete?', 'mappress-google-maps-for-wordpress'));

		// Globals
		$l10n['options'] = array(
			'admin' => current_user_can('administrator'),
			'adminurl' => admin_url(),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'apikey' => self::get_api_keys()->browser,
			'baseurl' => self::$baseurl,
			'blockCategory' => self::$block_category,
			'debug' => self::$debug,
			'editurl' => admin_url('post.php'),
			'filterParams' => (class_exists('Mappress_Filter')) ? Mappress_Filter::get_url_params() : array(),
			'iconsUrl' => (self::$pro) ? Mappress_Icons::$icons_url : null,    
			'isIE' => $is_IE,
			'language' => self::get_language(),
			'liq' => self::get_api_keys()->liq,
			'mapbox' => self::get_api_keys()->mapbox,
			'nonce' => wp_create_nonce('mappress'),
			'oid' => ($post) ? $post->ID : null,	// Note: GT => numeric, classic => string
			'otype' => ($post) ? 'post' : null,		// Not for users yet
			'pro' => self::$pro,
			'ssl' => self::is_ssl(),                // SSL is needed for 'your location' in directions
			'standardIcons' => (self::$pro) ? Mappress_Icons::$standard_icons : null,
			'standardIconsUrl' => (self::$pro) ? Mappress_Icons::$standard_icons_url : null,
			'userStyles' => (self::$options->engine == 'leaflet') ? self::$options->stylesMapbox : self::$options->stylesGoogle,
			'userIcons' => (self::$pro) ? Mappress_Icons::get_user_icons() : null,
			'version' => self::$version
		);

		// Tile providers
		$l10n['options']['tileProviders'] = array(
			'mapbox' => array(
				'accessToken' => self::get_api_keys()->mapbox,
				'attribution' => ['<a href="https://www.mapbox.com/about/maps" target="_blank">&copy; Mapbox</a>', '<a href="https://www.openstreetmap.org/about/" target="_blank">&copy; OpenStreetMap</a>' ],
				'url' => 'https://api.mapbox.com/styles/v1/{user}/{mapboxid}/tiles/256/{z}/{x}/{y}{r}?access_token={accessToken}&fresh=true',
				'zoomOffset' => 0
			),
			'osm' => array(
				'attribution' => ['<a href="https://openstreetmap.org" target="_blank">&copy; OpenStreetMap</a>'],
				'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
			)
		);
		
		// Default styles
		if (Mappress::$options->engine == 'leaflet') {
			if (Mappress::get_api_keys()->mapbox) {
				$styles = array(
					array('id' => 'streets', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'streets-v11', 'name' => __('Streets', 'mappress-google-maps-for-wordpress')),
					array('id' => 'outdoors', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'outdoors-v11', 'name' => __('Outdoors', 'mappress-google-maps-for-wordpress')),
					array('id' => 'light', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'light-v10', 'name' => __('Light', 'mappress-google-maps-for-wordpress')),
					array('id' => 'dark', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'dark-v10', 'name' => __('Dark', 'mappress-google-maps-for-wordpress')),
					array('id' => 'satellite', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'satellite-v9', 'name' => __('Satellite', 'mappress-google-maps-for-wordpress')),
					array('id' => 'satellite-streets', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'satellite-streets-v11', 'name' => __('Satellite Streets', 'mappress-google-maps-for-wordpress'))
				);
			} else {
				$styles = array(
					array('id' => 'osm', 'type' => 'standard', 'provider' => 'osm', 'name' => __('Streets', 'mappress-google-maps-for-wordpress'))
				);
			}
		} else {
			// Google styles
			$styles = array(
				array( 'id' => 'roadmap', 'type' => 'standard', 'name' => __('Roadmap', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/roadmap.png'),
				array( 'id' => 'terrain', 'type' => 'standard', 'name' => __('Terrain', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/terrain.png'),
				array( 'id' => 'satellite', 'type' => 'standard', 'name' => __('Satellite', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/satellite.png'),
				array( 'id' => 'hybrid', 'type' => 'standard', 'name' => __('Hybrid', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/hybrid.png'),
			);
		}
		$l10n['options']['standardStyles'] = $styles;

		// Global settings
		$options = array('alignment', 'betaPoiFields', 'clustering', 'clusteringOptions', 'country', 'defaultIcon', 'directions', 'directionsList',
		'directionsPopup', 'directionsServer', 'engine', 'filters', 'filtersPos', 'geocoder', 'geolocate',
		'highlight', 'highlightIcon', 'iconScale', 'initialOpenInfo', 'layout', 'lines', 'lineOpts',
		'mashupClick', 'mini', 'poiFields', 'poiList', 'poiListOpen', 'poiListPageSize', 'poiListViewport', 'poiZoom', 'radius', 'scrollWheel', 'search',
		'searchBox', 'searchParam', 'searchPlaceholder', 'size', 'sizes', 'sort', 'style', 'thumbHeight', 'thumbWidth', 'thumbs', 'thumbsList', 'thumbsPopup', 
		'tooltips', 'units', 'userLocation', 'webComponent');

		foreach($options as $option) {
			if (isset(self::$options->$option)) {
				$l10n['options'][$option] = self::$options->$option;
			}
		}

		return apply_filters('mappress_options', $l10n);
	}

	static function map_library() {
		self::scripts_enqueue('backend');
		echo '<div id="mapp-library" class="mapp-library"></div>';
		wp_editor('', 'mapp-library-tinymce');
	}

	/**
	* Add KML/KMZ as valid mime types
	*
	* @param mixed $mimes
	*/
	static function mime_types($mimes) {
		// Additional entries must match WP, which use finfo_file(), e.g. KML => text/xml
		$mimes['kml'] = 'text/xml';			// Real type: 'application/vnd.google-earth.kml+xml';
		$mimes['kmz'] = 'application/zip';	// Real type: 'application/vnd.google-earth.kmz';
		return $mimes;
	}

	static function plugin_action_links($links, $file) {
		$settings_link = "<a href='" . admin_url("admin.php?page=mappress") . "'>" . __('Settings', 'mappress-google-maps-for-wordpress') . "</a>";
		$whatsnew_link = "<a href='https://mappresspro.com/whats-new/' target='_blank'>" . __("What's new", 'mappress-google-maps-for-wordpress') . "</a>";
		array_unshift( $links, $whatsnew_link );
		array_unshift( $links, $settings_link);
		return $links;
	}

	static function plugins_loaded() {
		load_plugin_textdomain('mappress-google-maps-for-wordpress', false, dirname(self::$basename) . '/languages');
	}

	static function script($script) {
		return "\r\n<script type='text/javascript'>\r\n$script\r\n</script>";
	}

	static function script_loader_tag($tag, $handle, $src) {
		// Deregister
		if (self::$options->engine == 'google' && self::$options->deregister && self::$loaded && ($handle != 'mappress-google' && (stripos($src, 'maps.googleapis.com') !== false || stripos($src, 'maps.google.com'))))
			return '';
		// Re-register
		else if ($handle == 'mappress-google' && empty($tag))
			return sprintf("<script src='%s' id='mappress-google-js-fixed'></script>\n", self::scripts_google_tag());
		else
			return $tag;
	}

	static function scripts_enqueue($type = 'frontend', $scripts = null) {
		if (self::$loaded)
			return;
		else
			self::$loaded = true;

		// Don't output frontend scripts if using iframes
		if (!$scripts && $type == 'frontend' && self::$options->iframes)
			return;

		if ($scripts) {
			// Some plugins add 'defer' using script_loader_tag, which interferes with script loading, so remove it
			$nodefer = function ($tag, $handle) { return str_ireplace('defer="defer"', '', $tag); };
			add_filter('script_loader_tag', $nodefer, 999);
			$scripts->enqueue('mappress');
			$scripts->localize('mappress', 'mappl10n', self::l10n());
			remove_filter('script_loader_tag', $nodefer, 999);
		} else {
			wp_enqueue_script('mappress');
			wp_localize_script('mappress', 'mappl10n', self::l10n());

			if ($type == 'backend' || $type == 'settings')
				wp_enqueue_script('mappress_admin');

			if ($type == 'settings') {
				if (function_exists('wp_enqueue_code_editor'))
					wp_enqueue_code_editor(array( 'type' => 'php' ));
			}
		}

		// Templates (iframes always queue in footer)
		$footer = ($scripts) ? true : self::is_footer();
		$templates = array('map-item', 'map-popup', 'mashup-popup', 'mashup-item', 'user-mashup-item', 'user-mashup-popup');
		foreach($templates as $template_name)
			Mappress_Template::enqueue_template($template_name, $footer);
	}

	static function scripts_register($scripts = null) {
		$dev = self::is_dev();
		$footer = ($scripts) ? false : self::is_footer();

		// Directories
		$lib = ($dev) ? "https://localhost/$dev/wp-content/plugins/mappress-google-maps-for-wordpress/lib" : self::$baseurl . '/lib';
		$js = ($dev) ? "https://localhost/$dev/wp-content/plugins/mappress-google-maps-for-wordpress/build" : self::$baseurl . '/build';

		// Dependencies
		$deps = array('react', 'react-dom', 'wp-i18n');
		if (self::$options->engine == 'leaflet')
			$deps = array_merge(array('mappress-leaflet', 'mappress-leaflet-omnivore'), $deps);
		if (self::$options->engine != 'leaflet' || self::$options->geocoder == 'google')
			$deps[] = 'mappress-google';
		if (self::$options->clustering)
			$deps[] = (self::$options->engine == 'leaflet') ? 'mappress-leaflet-markercluster' : 'mappress-markerclusterer';
		$admin_deps = array('mappress', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-core-data', 'wp-element', 'wp-media-utils', 'wp-i18n', 'wp-notices', 'wp-url');

		// Clustering ( https://github.com/googlemaps/js-markerclusterer | https://github.com/Leaflet/Leaflet.markercluster )
		$register = array(
			array("mappress-leaflet", $lib . '/leaflet/leaflet.js', null, null, $footer),
			array("mappress-leaflet-omnivore", $lib . '/leaflet/leaflet-omnivore.min.js', null, null, $footer),
			array("mappress-google", self::scripts_google_tag(), null, null, $footer),
			array('mappress-markerclusterer', self::unpkg('markerclusterer', 'index.min.js'), null, null, $footer),
			array('mappress-leaflet-markercluster', $lib . '/leaflet/leaflet.markercluster.js', null, null, $footer),
			array('mappress', $js . "/index_mappress.js", $deps, self::$version, $footer),
			array('mappress_admin', $js . "/index_mappress_admin.js", $admin_deps, self::$version, $footer)
		);

		foreach($register as $script) {
			if ($scripts)
				$scripts->add($script[0], $script[1], $script[2], $script[3], $script[4]);
			else
				wp_register_script($script[0], $script[1], $script[2], $script[3], $script[4]);
		}

		// I18N
		if (function_exists('wp_set_script_translations')) {
			if ($scripts) {
				$scripts->set_translations('mappress', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
				$scripts->set_translations('mappress_admin', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
			} else {
				wp_set_script_translations('mappress', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
				wp_set_script_translations('mappress_admin', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
			}
		}
	}

	static function scripts_google_tag() {
		$dev = self::is_dev();
		$language = self::get_language();
		$language = ($language) ? "&language=$language" : '';
		$apiversion = ($dev) ? '&v=beta' : '&v=3';
		$apikey = "&key=" . self::get_api_keys()->browser;
		$libs = '&libraries=places,drawing';
		return "https://maps.googleapis.com/maps/api/js?callback=Function.prototype{$apiversion}{$language}{$libs}{$apikey}";
	}

	/**
	* Scrub attributes
	* The WordPress shortcode API passes shortcode attributes in lowercase and with boolean values as strings (e.g. "true")
	* Converts atts to lowercase, replaces boolean strings with booleans, and creates arrays from comma-separated attributes
	*
	* Returns empty array if $atts is empty or not an array
	*/
	static function scrub_atts($atts=null) {
		if (!$atts || !is_array($atts))
			return array();
			
		// Sanitize, single quotes could be used for xss JS
		foreach($atts as $key => $value) {
			if (is_string($value))
				$atts[$key] = esc_attr($value);
		}

		$atts = self::string_to_boolean($atts);

		// Shortcode attributes are lowercase so convert everything to lowercase
		$atts = array_change_key_case($atts);

		// Map options - includes both leaflet and Google
		foreach(array('disableDefaultUI', 'disableDoubleClickZoom', 'draggable', 'dragging', 'fullscreenControl', 'geolocate', 'keyboard',
			'keyboardShortcuts', 'mapTypeControl', 'maxZoom', 'minZoom', 'panControl', 'rotateControl', 'scaleControl',
			'scrollwheel', 'scrollWheelZoom', 'streetViewControl', 'zoomControl') as $opt) {
			$lcopt = strtolower($opt);
			if (isset($atts[$lcopt])) {
				$atts['mapopts'][$opt] = $atts[$lcopt];
				unset($atts[$lcopt]);
			}
		}

		// For center = 'post', use location of first poi in first map
		if (isset($atts['center']) && $atts['center'] == 'post') {
			global $post;
			$maps = Mappress_Map::get_list('post', $post->ID, 'ids');
			$map = ($maps) ? Mappress_Map::get($maps[0]) : null;
			$atts['center'] = ($map && $map->pois) ? $map->pois[0]->point['lat'] . ',' . $map->pois[0]->point['lng'] : null;
		}

		// Conver GT 'align' to 'alignment'
		if (isset($atts['align']))
			$atts['alignment'] = $atts['align'];

		// Change legacy center='user' to geolocation='true'
		if (isset($atts['center']) && strtolower($atts['center']) == 'user') {
			$atts['center'] = null;
			$atts['geolocate'] = true;
		}
							 
		return $atts;
	}

	/**
	* Map shortcode
	*
	*/
	static function shortcode_map($atts='') {
		global $post;

		if (self::is_admin() || is_feed())
			return;

		$atts = self::scrub_atts($atts);

		// Determine what to show
		$mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;

		// On archive pages, $post isn't set
		if (!$mapid && !$post)
			return;

		if ($mapid) {
			// Show map by mapid
			$map = Mappress_Map::get($mapid);
		} else {
			// Get the first map attached to the post
			$maps = Mappress_Map::get_list('post', $post->ID);
			$map = (isset ($maps[0]) ? $maps[0] : false);
		}

		if (!$map)
			return;

		return $map->display($atts);
	}

	/**
	* Mashup shortcode
	*
	*/
	static function shortcode_mashup($atts='') {
		if (self::is_admin() || is_feed())
			return;

		$atts = self::scrub_atts($atts);
		return self::get_mashup($atts);
	}

	static function string_to_boolean($data) {
		if ($data === 'false')
			return false;

		if ($data === 'true')
			return true;

		if (is_array($data)) {
			foreach($data as &$datum)
				$datum = self::string_to_boolean($datum);
		}
		return $data;
	}

	static function styles_enqueue($type, $styles = null) {
		global $wp_styles;
		$styles = ($styles) ? $styles : $wp_styles;

		$styles->enqueue('mappress-leaflet');
		$styles->enqueue('mappress-leaflet-markercluster-default');
		$styles->enqueue('mappress-leaflet-markercluster');
		$styles->enqueue('mappress');

		if ($type == 'frontend')
			$styles->enqueue('mappress-custom');
		else if ($type == 'backend' || $type == 'settings')
			$styles->enqueue('mappress-admin');
	}

	static function styles_register($styles = null) {
		$styles = ($styles) ? $styles : wp_styles();

		$deps = array();

		// Leaflet CSS
		if (self::$options->engine == 'leaflet') {
			$styles->add('mappress-leaflet', self::$baseurl . '/lib/leaflet/leaflet.css', null, '1.7.1');
			$deps[] = 'mappress-leaflet';
			if (self::$options->clustering) {
				$styles->add('mappress-leaflet-markercluster-default', self::$baseurl . "/lib/leaflet/MarkerCluster.Default.css", null, '1.4.1');
				$deps[] = 'mappress-leaflet-markercluster-default';
				$styles->add('mappress-leaflet-markercluster', self::$baseurl . "/lib/leaflet/MarkerCluster.css", null, '1.4.1');
				$deps[] = 'mappress-leaflet-markercluster';
			}
		}

		// Frontend
		$styles->add('mappress', self::$baseurl . '/css/mappress.css', $deps, self::$version);

		// Admin CSS
		$styles->add('mappress-admin', self::$baseurl . '/css/mappress_admin.css', array('mappress', 'wp-edit-blocks'), self::$version);

		 // Mappress CSS from theme directory
		if ( @file_exists( get_stylesheet_directory() . '/mappress.css' ) )
			$file = get_stylesheet_directory_uri() . '/mappress.css';
		elseif ( @file_exists( get_template_directory() . '/mappress.css' ) )
			$file = get_template_directory_uri() . '/mappress.css';
		if (isset($file)) {
			$styles->add('mappress-custom', $file, array('mappress'), self::$version);
		}
	}

	static function template_redirect() {
		header("HTTP/1.1 200 OK");

		// Convert strings to booleans
		$args = array_map(function($arg) { if ($arg  == 'true') return true; if ($arg == 'false') return false; return $arg; }, $_GET);

		if (isset($args['mapid'])) {
			$map = Mappress_Map::get($args['mapid']);
			if (!$map)
				die("<html><body><!-- Bad mapid --></body></html>");
		} elseif (isset($args['transient'])) {
			$mapdata = get_transient($args['transient']);
			if (!$mapdata)
				die("<html><body><!-- Bad map transient --></body></html>");
			$map = new Mappress_Map($mapdata);
		} else {
			$map = new Mappress_Map();
		}

		$map->update($args);
		$map->layout = 'left';

		// Hydrate POIs for mashups
		if ($map->query) {
			$result = Mappress_Query::query(array('query' => $map->query));
			$map->pois = $result->pois;
		}

		echo self::get_iframe($map);
		die();
	}

	/**
	* Automatic map display.
	* If set, the [mappress] shortcode will be prepended/appended to the post body, once for each map
	* The shortcode is used so it can be filtered - for example WordPress will remove it in excerpts by default.
	*
	* @param mixed $content
	*/
	static function the_content($content="") {
		global $post;
		global $wp_current_filter;

		$autodisplay = self::$options->autodisplay;

		// No auto display
		if (!$autodisplay || $autodisplay == 'none')
			return $content;

		// Check if in the loop, to prevent conflicts with JetPack - see http://wordpress.org/support/topic/easy-adsense-lite-and-jetpack
		if (!in_the_loop())
			return $content;

		// Don't add the shortcode for feeds or admin screens
		if (is_feed() || self::is_admin())
			return $content;

		// No shortcode if post is password protected
		if (post_password_required())
			return $content;

		// If this is an excerpt don't attempt to add the map to it
		if (in_array('get_the_excerpt', $wp_current_filter))
			return $content;

		// Don't auto display if the post already contains a MapPress shortcode
		if (stristr($content, '[mappress') !== false || stristr($content, '[mashup') !== false)
			return $content;

		// Don't auto display if the post already contains GT block
		if (stristr($content, 'wp:mappress/map') !== false)
			return $content;

		// Get maps associated with post
		$mapids = Mappress_Map::get_list('post', $post->ID, 'ids');
		if (empty($mapids))
			return $content;

		// Add the shortcode once for each map
		$shortcodes = "";
		foreach($mapids as $mapid)
			$shortcodes .= '<p>[mappress mapid="' . $mapid . '"]</p>';

		if ($autodisplay == 'top')
			return $shortcodes . $content;
		else
			return $content . $shortcodes;
	}

	static function to_atts($vars) {
		$vars = is_object($vars) ? $vars : (object) $vars;
		$results = array();
		foreach($vars as $name => $value) {
			if ($value === null || $value === '')
				continue;

			$lcname = strtolower($name);        // Only lowercase is allowed

			if (is_object($value) || is_array($value))
				$results[] = sprintf("%s='%s'", $lcname, json_encode($value, JSON_HEX_APOS));
			else
				$results[] = "$lcname='" . str_replace('&quot;', '"', $value) . "'";
		}
		return join(' ', $results);
	}

	static function unpkg($package, $filename) {
		$urls = array(
			'markerclusterer' => 'https://unpkg.com/@googlemaps/markerclusterer@%s/dist',
		);
		$versions = array(
			'markerclusterer' => '2.0.11',
		);

		$url = $urls[$package];
		$version = $versions[$package];
		return apply_filters('mappress_unpkg', sprintf($url, $version) . "/$filename", $package, $filename);
	}

	/**
	* Scripts & styles for frontend
	* CSS is loaded from: child theme, theme, or plugin directory
	*/
	static function wp_enqueue_scripts() {
		self::styles_enqueue('frontend');

		// Load scripts in header if needed
		if (!self::is_footer())
			self::scripts_enqueue();
	}

	static function wp_head() {
		echo "\r\n<!-- MapPress Easy Google Maps " . __('Version', 'mappress-google-maps-for-wordpress') . ':' . self::$version . " (https://www.mappresspro.com) -->\r\n";
	}
}

$mappress = new Mappress();
?>