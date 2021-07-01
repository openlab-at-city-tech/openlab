<?php

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

class nggLoader
{
	var $version     = NGG_PLUGIN_VERSION;
	var $dbversion   = '1.8.1';
	var $minimum_WP  = '3.6.1';
	var $options     = '';
    var $add_PHP5_notice = false;
    var $plugin_name = '';

	/** @var nggManageGallery|nggManageAlbum $manage_page */
	var $manage_page;

	function __construct()
	{
		// Stop the plugin if we missed the requirements
		if ( ( !$this->required_version() ) || ( !$this->check_memory_limit() ) )
			return;

		// Determine plugin basename based on whether NGG is being used in
		// it's legacy form, or as a Photocrati-theme Gallery
		if (defined('NGG_PLUGIN_BASENAME')) $this->plugin_name = NGG_PLUGIN_BASENAME;
		else $this->plugin_name = basename(dirname(__FILE__)).'/'.basename(__FILE__);

		// Get some constants first
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();

		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'start_plugin') );

		// Register_taxonomy must be used during the init
		add_action( 'init', array(&$this, 'register_taxonomy'), 9);
		add_action( 'wpmu_new_blog', array(&$this, 'multisite_new_blog'), 10, 6);

		// Add a message for PHP4 Users, can disable the update message later on
		if (version_compare(PHP_VERSION, '5.0.0', '<'))
			add_filter('transient_update_plugins', array(&$this, 'disable_upgrade'));

		//Add some links on the plugin page
		add_filter('plugin_row_meta', array(&$this, 'add_plugin_links'), 10, 2);

		// Check for the header / footer
		add_action( 'init', array(&$this, 'test_head_footer_init' ) );
	}

	function start_plugin() {

		global $nggRewrite;

		// All credits to the tranlator
		$this->translator  = '<p class="hint">'. __('<strong>Translation by : </strong><a target="_blank" href="https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/">See here</a>', 'nggallery') . '</p>';

		// Content Filters
		add_filter('ngg_gallery_name', 'sanitize_title');

		// Check if we are in the admin area
		if ( is_admin() ) {

			// Pass the init check or show a message
			if (get_option( 'ngg_init_check' ) != false )
				add_action( 'admin_notices', array($this, 'output_init_check_error'));

		} else {

			// Add MRSS to wp_head
			if ( isset( $this->options['useMediaRSS'] ) && $this->options['useMediaRSS'] )
				add_action('wp_head', array('nggMediaRss', 'add_mrss_alternate_link'));

		}
	}

	function output_init_check_error()
	{
		echo sprintf("<div id='message' class='error'><p><strong>%s</strong></p></div>", esc_html(get_option('ngg_init_check')));
	}

	function required_version() {

		global $wp_version;

		// Check for WP version installation
		$wp_ok  =  version_compare($wp_version, $this->minimum_WP, '>=');

		if ( ($wp_ok == FALSE) ) {
			add_action(
				'admin_notices',
				array($this, 'output_minimum_wp_version_error')
			);
			return false;
		}

		return true;

	}

	function output_minimum_wp_version_error()
	{
		echo sprintf(
			"<div id='message' class='error'><p><strong>%s</strong></p></div>",
			sprintf(__("Sorry, NextGEN Gallery works only under WordPress %s or higher.", 'nggallery'), $this->minimum_WP)
		);
	}

	function check_memory_limit() {

		// get the real memory limit before some increase it
		$this->memory_limit = ini_get('memory_limit');

		// PHP docs : Note that to have no memory limit, set this directive to -1.
		if ($this->memory_limit == -1 ) return true;

		// Yes, we reached Gigabyte limits, so check if it's a megabyte limit
		if (strtolower( substr($this->memory_limit, -1) ) == 'm') {

			$this->memory_limit = (int) substr( $this->memory_limit, 0, -1);

			//This works only with enough memory, 16MB is silly, wordpress requires already 16MB :-)
			if ( ($this->memory_limit != 0) && ($this->memory_limit < 32 ) ) {
				add_action(
					'admin_notices',
					array($this, 'output_memory_limit_error')
				);
				return false;
			}
		}

		return true;

	}

	function output_memory_limit_error()
	{
		echo sprintf(
			"<div id='message' class='error'><p><strong>%s</strong></p>",
			__("Sorry, NextGEN Gallery works only with a memory limit of 32MB or higher")
		);
	}

	function define_tables() {
		global $wpdb;

		// add database pointer
		$wpdb->nggpictures					= $wpdb->prefix . 'ngg_pictures';
		$wpdb->nggallery					= $wpdb->prefix . 'ngg_gallery';
		$wpdb->nggalbum						= $wpdb->prefix . 'ngg_album';

	}

	function register_taxonomy() {
		global $wp_rewrite;

		// Register the NextGEN taxonomy
		$args = array(
				'label' => __('Picture tag', 'nggallery'),
				'template' => __('Picture tag: %2$l.', 'nggallery'),
				'helps' => __('Separate picture tags with commas.', 'nggallery'),
				'sort' => true,
				'args' => array('orderby' => 'term_order')
				);

		register_taxonomy( 'ngg_tag', 'nggallery', $args );
	}

	function define_constant() {

		global $wp_version;

		//TODO:SHOULD BE REMOVED LATER
		define('NGGVERSION', $this->version);
		// Minimum required database version

		define('NGG_DBVERSION', $this->dbversion);

		// define URL
		define('NGGFOLDER', dirname( $this->plugin_name ) );

        // Legacy expects this to have a trailing slash
		define(
			'NGGALLERY_ABSPATH',
			defined('NGG_LEGACY_MOD_DIR') ?
				rtrim(NGG_LEGACY_MOD_DIR, "/\\").DIRECTORY_SEPARATOR :
				rtrim(dirname(__FILE__), "/\\").DIRECTORY_SEPARATOR
		);

        // Legacy expects this to have a trailing slash
        define('NGGALLERY_URLPATH', plugin_dir_url(__FILE__));

		// look for imagerotator
		define('NGGALLERY_IREXIST', !empty( $this->options['irURL'] ));
	}

	function load_dependencies() {

		// Load global libraries												// average memory usage (in bytes)
		require_once (dirname (__FILE__) . '/lib/core.php');					//  94.840
		require_once (dirname (__FILE__) . '/lib/ngg-db.php');					// 132.400
		require_once (dirname (__FILE__) . '/lib/image.php');					//  59.424
		require_once (dirname (__FILE__) . '/lib/tags.php');				    // 117.136
		require_once (dirname (__FILE__) . '/lib/post-thumbnail.php');			//  n.a.
		require_once (dirname (__FILE__) . '/lib/multisite.php');
		require_once (dirname (__FILE__) . '/lib/sitemap.php');

		// Load frontend libraries
		require_once (dirname (__FILE__) . '/lib/shortcodes.php'); 		        // 92.664

		// We didn't need all stuff during a AJAX operation
		if ( defined('DOING_AJAX') )
			require_once (dirname (__FILE__) . '/admin/ajax.php');
		else {
			require_once (dirname (__FILE__) . '/lib/meta.php');				// 131.856
			require_once (dirname (__FILE__) . '/lib/media-rss.php');			//  82.768
			require_once (dirname (__FILE__) . '/lib/rewrite.php');				//  71.936

			// Load backend libraries
			if ( is_admin() && !$this->is_rest_url()) {
				require_once (dirname (__FILE__) . '/admin/admin.php');
				require_once (dirname (__FILE__) . '/admin/media-upload.php');
				$this->nggAdminPanel = new nggAdminPanel();
			}
		}
	}

	function is_rest_url()
	{
		return strpos($_SERVER['REQUEST_URI'], 'wp-json') !== FALSE;
	}

	function load_thickbox_images() {
		// WP core reference relative to the images. Bad idea
		echo "\n" . '<script type="text/javascript">tb_pathToImage = "' . site_url() . '/wp-includes/js/thickbox/loadingAnimation.gif";tb_closeImage = "' . site_url() . '/wp-includes/js/thickbox/tb-close.png";</script>'. "\n";
	}

	function load_options() {
		// Load the options
		$this->options = get_option('ngg_options');
	}

	// THX to Shiba for the code
	// See: http://shibashake.com/wordpress-theme/write-a-plugin-for-wordpress-multi-site
	function multisite_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;

		include_once (dirname (__FILE__) . '/admin/install.php');

		if (is_plugin_active_for_network( $this->plugin_name )) {
			$current_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
			$installer = new C_NggLegacy_Installer;
			nggallery_install($installer);
			switch_to_blog($current_blog);
		}
	}

	function disable_upgrade($option){

		// PHP5.2 is required for NGG V1.4.0
		if ( version_compare($option->response[ $this->plugin_name ]->new_version, '1.4.0', '>=') )
			return $option;

		if( isset($option->response[ $this->plugin_name ]) ){
			//Clear it''s download link
			$option->response[ $this->plugin_name ]->package = '';
		}
		return $option;
	}

	// Add links to Plugins page
	function add_plugin_links($links, $file) {

		if ( $file == $this->plugin_name ) {
			$links[] = '<a href="http://wordpress.org/support/plugin/nextgen-gallery">' . __('Get help', 'nggallery') . '</a>';
			$links[] = '<a href="https://bitbucket.org/photocrati/nextgen-gallery">' . __('Contribute', 'nggallery') . '</a>';
		}
		return $links;
	}

	// Check for the header / footer, parts taken from Matt Martz (http://sivel.net/)
	function test_head_footer_init() {

		// If test-head query var exists hook into wp_head
		if ( isset( $_GET['test-head'] ) )
			add_action( 'wp_head', array($this, 'output_wp_head_comment'), 99999 );

		// If test-footer query var exists hook into wp_footer
		if ( isset( $_GET['test-footer'] ) )
			add_action( 'wp_footer', array($this, 'output_wp_footer_comment'), 99999 );
	}

	function output_wp_head_comment()
	{
		echo "<!--wp_user-->";
	}

	function output_wp_footer_comment()
	{
		echo "<!--wp_footer-->";
	}
}

// Let's start the holy plugin
global $ngg;
$ngg = new nggLoader();
