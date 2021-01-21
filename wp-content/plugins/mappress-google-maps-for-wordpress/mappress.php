<?php
/*
Plugin Name: MapPress Maps for WordPress
Plugin URI: https://www.mappresspro.com/mappress
Author URI: https://www.mappresspro.com/chris-contact
Description: MapPress makes it easy to add Google and Leaflet Maps to WordPress
Version: 2.57.3
Author: Chris Richardson
Text Domain: mappress-google-maps-for-wordpress
Thanks to all the translators and to Matthias Stasiak for his wonderful icons (http://code.google.com/p/google-maps-icons/)
*/

/*
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the license.txt file for details.
*/

require_once dirname( __FILE__ ) . '/mappress_obj.php';
require_once dirname( __FILE__ ) . '/mappress_controls.php';
require_once dirname( __FILE__ ) . '/mappress_poi.php';
require_once dirname( __FILE__ ) . '/mappress_map.php';
require_once dirname( __FILE__ ) . '/mappress_settings.php';
include_once dirname( __FILE__ ) . '/mappress_template.php';
include_once dirname( __FILE__ ) . '/mappress_wpml.php';

if (is_dir(dirname( __FILE__ ) . '/pro')) {
	include_once dirname( __FILE__ ) . '/pro/mappress_filter.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_geocoder.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_icons.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_meta.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_pro_settings.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_query.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_updater.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget_map.php';
}

class Mappress {
	const VERSION = '2.57.3';

	static
		$baseurl,
		$basename,
		$basedir,
		$debug,
		$loaded,
		$options,
		$notices,
		$pages,
		$pointers,
		$pro,
		$updater,
		$version
		;

	function __construct()  {
		self::$basedir = dirname(__FILE__);
		self::$basename = plugin_basename(__FILE__);
		self::$baseurl = plugins_url('', __FILE__);
		self::$options = Mappress_Options::get();
		self::$pro = is_dir(dirname( __FILE__ ) . '/pro');
		self::$version = (self::$pro) ? self::VERSION . "PRO" : self::VERSION;
		self::$version = (defined('MAPPRESS_DEV') && MAPPRESS_DEV) ? self::$version . '-' . rand(0,99999) : self::$version;

		self::debugging();

		// Pro updater
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

		// Review
		add_action('current_screen', array(__CLASS__, 'current_screen'));

		// Plugin action links
		add_filter("plugin_action_links_" . self::$basename, array(__CLASS__, 'plugin_action_links'), 10, 2);

		if (self::$pro)
			add_shortcode('mashup', array(__CLASS__, 'shortcode_mashup'));

		// Remove API loaded by other plugins
		if (self::$options->engine != 'leaflet' && (self::$options->deregister || isset($_REQUEST['mp_compat']))) {
			add_action('wp_print_footer_scripts', array(__CLASS__, 'deregister'), -1);
			add_action('admin_print_footer_scripts', array(__CLASS__, 'deregister'), -1);
			add_action('wp_print_scripts', array(__CLASS__, 'deregister'), -1);
		}

		// Slow heartbeat
		if (self::$debug)
			add_filter( 'heartbeat_settings', array(__CLASS__, 'heartbeat_settings'));

		// Dismissible notices
		add_action('wp_ajax_mapp_dismiss', array(__CLASS__, 'ajax_dismiss' ));

		// Add block category
		add_filter( 'block_categories', array(__CLASS__, 'block_categories'), 10, 2 );

		add_filter('autoptimize_filter_js_exclude', array(__CLASS__, 'autoptimize_filter_js_exclude'), 10, 2);
		add_filter('autoptimize_filter_css_exclude', array(__CLASS__, 'autoptimize_filter_css_exclude'), 10, 2);
	}

	// Scripts & styles for admin
	// CSS is always loaded from the plugin directory
	static function admin_enqueue_scripts($hook) {
		global $postid;

		// Some plugins call this without setting $hook
		if (empty($hook))
			return;

		// Network admin has no pages
		if (empty(self::$pages))
			return;

		$is_editing = in_array($hook, array('post.php', 'post-new.php'));
		$is_settings = in_array($hook, self::$pages);
		$is_customizer = $hook == 'customize.php';

		if ($is_editing || $is_settings || $is_customizer) {
			self::scripts_enqueue('backend');

			// Settings - Gutenberg CSS for library + code editor
			if ($is_settings) {
				wp_enqueue_style( 'wp-edit-blocks' );
				if (function_exists('wp_enqueue_code_editor'))
					wp_enqueue_code_editor(array( 'type' => 'php' ));
			}

			// Leaflet CSS
			if (self::$options->engine == 'leaflet')
				wp_enqueue_style('mappress-leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', null, '1.7.1');
			if (self::$options->clustering) {
				wp_enqueue_style('mappress-leaflet-markercluster-default', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.Default.css", null, '1.4.1');
				wp_enqueue_style('mappress-leaflet-markercluster', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.css", null, '1.4.1');
			}

			// Mappress CSS
			wp_enqueue_style('mappress', self::$baseurl . '/css/mappress.css', null, self::$version);
			wp_enqueue_style('mappress-admin', self::$baseurl . '/css/mappress_admin.css', null, self::$version);
		}

		// Temporary, for mce pointer
		if ($is_editing) {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
		}
	}

	static function admin_menu() {
		// Settings
		$settings = (self::$pro) ? new Mappress_Pro_Settings() : new Mappress_Settings();
		self::$pages[] = add_menu_page('MapPress', 'MapPress', 'manage_options', 'mappress', array(&$settings, 'options_page'), 'dashicons-location');
		self::$pages[] = add_submenu_page('mappress', __('Settings', 'mappress-google-maps-for-wordpress'), __('Settings', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress', array(&$settings, 'options_page'));
		self::$pages[] = add_submenu_page('mappress', __('Map Library', 'mappress-google-maps-for-wordpress'), __('Map Library', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_maps', array(__CLASS__, 'map_library'));
	}

	static function admin_notices() {
		global $wpdb;
		$error =  "<div class='notice notice-error'><p>%s</p></div>";
		$map_table = $wpdb->prefix . "mappress_maps";
		$exists = $wpdb->get_var("show tables like '$map_table'");

		// Non-dismissible notices
		if (!$exists) {
			printf($error, __("MapPress database tables are missing.  Please deactivate the plugin and activate it again to fix this.", 'mappress-google-maps-for-wordpress'));
			return;
		}

		if (self::$options->engine != 'leaflet' && !self::get_api_keys()->browser)
			printf($error, sprintf("%s. %s <a href='%s'>%s</a>.", __("A Google Maps API key is required", 'mappress-google-maps-for-wordpress'), __("Please update your", 'mappress-google-maps-for-wordpress'), admin_url('admin.php?page=mappress'), __('MapPress Settings', 'mappress-google-maps-for-wordpress')));

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

	static function ajax_dismiss() {
		$key = isset($_POST['key']) ? $_POST['key'] : null;

		if (!$key || sanitize_key( $key) != $key)
			wp_die( 0 );

		$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
		$dismissed[] = $key;
		update_user_meta( get_current_user_id(), 'mappress_dismissed', implode( ',', $dismissed ));
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

	static function autoptimize_filter_css_exclude($exlude_css, $content) {
		if ($exclude_css)
			$exclude_css .= ',';
		$exclude_css .= "mappress.css,mappress_admin.css,leaflet.css,MarkerCluster.Default.css,Leaflet.markercluster/MarkerCluster.css";
		return $exclude_css;
	}

	static function autoptimize_filter_js_exclude($exclude_js, $content) {
		if ($exclude_js)
			$exclude_js .= ',';
		$exclude_js .= "underscore.js,underscore.min,js,index_mappress.js,index_mappress_admin.js";
		return $exclude_js;
	}

	// GT assets are still loaded when using classic editor, but this script doesn't run
	static function block_categories( $categories, $post ) {
		if (!in_array($post->post_type, self::$options->postTypes))
			return $categories;

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

	static function current_screen($screen) {
		if ($screen && self::$pages && $screen->id == self::$pages[0] && !self::$pro) {
			$first_time = get_option('mappress_review');
			if (!$first_time) {
				update_option('mappress_review', time());
				return;
			}

			if (time() <= $first_time + (60 * 60 * 24 * 10))
				return;

			$ids = Mappress_Map::get_list(null, 'ids');
			if (count($ids) < 3)
				return;

			$review_link = sprintf("<a class='button button-primary mapp-dismiss' href='https://wordpress.org/support/view/plugin-reviews/mappress-google-maps-for-wordpress?filter=5' target='_blank'>%s</a>", __('OK, you deserve it!', 'mappress-google-maps-for-wordpress'));
			$no_link = sprintf("<a class='button mapp-dismiss' href='#'>%s</a>", __('Nope, maybe later', 'mappress-google-maps-for-wordpress'));
			$help_link = sprintf("<a class='mapp-dismiss' href='https://mappresspro.com/chris-contact' target='_blank'>%s</a>", __('I neeed some help', 'mappress-google-maps-for-wordpress'));
			$body = "<div class='mapp-review'>";
			$body .= "<h3>" . __("Please Help Spread the Word", 'mappress-google-maps-for-wordpress') . "</h3>";
			$body .= "<p>" . __("If you're enjoying MapPress, would you mind taking a moment to write a brief review?  It would mean the world to me!", 'mappress-google-maps-for-wordpress') . "</p>";
			$body .= "<p>" . __("If you have any issues or suggestions, please let me know.  I'll be happy to help!", 'mappress-google-maps-for-wordpress') . "</p>";
			$body .= "<p>" . "~ Chris Richardson" . "</p>";
			$body .= "<div class='mapp-review-options'>" . $review_link . $no_link . $help_link . "</div>";
			$body .= "</div>";
			self::$notices['review'] = array('review', $body);
		}
	}

	static function debugging() {
		global $wpdb;

		if (isset($_GET['mp_info'])) {
			echo "<b>Plugin</b> " . self::$version;
			$posts_table = $wpdb->prefix . 'mappress_posts';
			$results = $wpdb->get_results("SELECT postid, mapid FROM $posts_table");
			echo "<br/>postid => mapid<br/>";
			foreach($results as $i => $result) {
				if ($i > 50)
					break;
				echo "<br/>$result->postid => $result->mapid";
			}
			$options = Mappress_Options::get();
			unset($options->mapbox, $options->license, $options->apiKey, $options->apiKeyServer);
			echo str_replace(array("\r", "\n"), array('<br/>', '<br/>'), print_r($options, true));
			die();
		}

		if (isset($_REQUEST['mp_debug']))
			self::$debug = max(1, (int) $_REQUEST['mp_debug']);
		else if (defined('MAPPRESS_DEBUG'))
			self::$debug = MAPPRESS_DEBUG;

		if (self::$debug) {
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors','On');
			$wpdb->show_errors();
		}
	}

	static function deregister() {
		if (self::$loaded) {
			$wps = wp_scripts();
			foreach($wps->registered as $registered) {
				if (stripos($registered->src, 'maps.googleapis.com') !== false && stripos($registered->handle, 'mappress') === false) {
					$registered->src = null;
				}
			}
		}
	}

	static function get_api_keys() {
		$results = (object) array('browser' => self::$options->apiKey, 'server' => self::$options->apiKeyServer, 'mapbox' => self::$options->mapbox);
		if (empty($results->browser) && defined('MAPPRESS_APIKEY'))
			$results->browser = MAPPRESS_APIKEY;
		if (empty($results->server) && defined('MAPPRESS_APIKEY_SERVER'))
			$results->server = MAPPRESS_APIKEY_SERVER;
		if (empty($results->mapbox) && defined('MAPPRESS_APIKEY_MAPBOX'))
			$result->mapbox = MAPPRESS_APIKEY_MAPBOX;
		return $results;
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
		$mashup->query = Mappress_Query::parse_query($atts);

		// If parameter test="true", output the query result (or global query) without using a map
		if (isset($_GET['mp_test']) || (isset($atts['test']) && $atts['test'])) {
			$wpq = ($mashup->query) ? new WP_Query($mashup->query) : $wp_query;
			return "<pre>" . print_r($wpq, true) . "</pre>";
		}
		// If using query 'current' then create a static map for current posts
		if (empty($mashup->query))
			$mashup->pois = Mappress_Query::get_pois($wp_query);

		// If 'hideEmpty' is set, try to suppress the map if there are no POIs
		if ($mashup->hideEmpty) {
			// 'current' query - check found pois
			if (empty($mashup->query) && empty($mashup->pois))
				return "";

			// Other queries - check for at least 1 result
			if (Mappress_Query::is_empty($mashup->query))
				return "";
		}
		return $mashup->display();
	}

	static function get_support_links() {
		$html = "<div class='mapp-support'>";
		$html .= __('Version', 'mappress-google-maps-for-wordpress') . ' ';
		$html .= self::$version;
		$html .= " | <a target='_blank' href='https://mappresspro.com/mappress/mappress-documentation'>" . __('Help', 'mappress-google-maps-for-wordpress') . "</a>";
		if (!self::$pro)
			$html .= "<a class='button button-primary' href='https://mappresspro.com/mappress' target='_blank'>" . __('Upgrade to MapPress Pro', 'mappress-google-maps-for-wordpress') . "</a>";
		$html .= "</div>";
		echo $html;
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
		Mappress_Map::register();
		Mappress_Template::register();
		Mappress_WPML::register();

		if (self::$pro) {
			Mappress_Icons::register();
			Mappress_Meta::register();
			Mappress_Pro_Settings::register();
			Mappress_Query::register();
			Mappress_Widget::register();
			Mappress_Widget_Map::register();
		}

		self::scripts_register();

		// Register Gutenberg block types and load GT scripts
		if (function_exists('register_block_type')) {
			register_block_type('mappress/map', array(
				'render_callback' => array(__CLASS__, 'shortcode_map'),
				'editor_script' => array('mappress_admin')
			));
			if (self::$pro) {
				register_block_type('mappress/mashup', array(
					'render_callback' => array(__CLASS__, 'shortcode_mashup'),
					'editor_script' => array('mappress_admin'),
				));
			}
		}

		// Check if upgrade is needed
		$current_version = get_option('mappress_version');

		// Convert meta key settings
		if ($current_version < '2.45') {
			$old = (object) get_option('mappress_options');
			foreach(array('address1', 'address2', 'address3', 'address4', 'address5', 'address6', 'lat', 'lng', 'iconid', 'title', 'body', 'zoom') as $i => $key) {
				if ($i < 6) {
					$value = (isset($old->metaKeyAddress[$i])) ? $old->metaKeyAddress[$i] : null;
				} else {
					$old_key = 'metaKey' . ucfirst($key);
					$value = (isset($old->$old_key)) ? $old->$old_key : null;
				}
				if ($value)
					Mappress::$options->metaKeys[$key] = $value;
			}
			Mappress::$options->save();
		}

		// Missing license
		if (self::$pro && !trim(self::$options->license) && (!is_multisite() || (is_super_admin() && is_main_site())))
			self::$notices['nolicense'] = array('warning', __('Please enter your MapPress license key to enable plugin updates', 'mappress-google-maps-for-wordpress'));

		// WP minimum version
		if (self::VERSION >= '2.55' && version_compare(get_bloginfo('version'),'5.3', '<') )
			self::$notices['255_min_version'] = array('error', __('MapPress Gutenberg blocks require WordPress 5.3 or the latest Gutenberg Plugin. Please update if using the block editor.', 'mappress-google-maps-for-wordpress'));

		// New features
		if ($current_version && $current_version < '2.55' and self::VERSION >= '2.55')
			self::$notices['255_whats_new'] = array('info', sprintf(__('MapPress has many new features!  Please take a moment to learn %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("what's new", 'mappress-google-maps-for-wordpress') . '</a>'));

		// New MCE editor
		if ($current_version && $current_version < '2.57') {
			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
			if (!in_array('257_new_editor', $dismissed)) {
				$header = sprintf("<h3>%s</h3>", __("The MapPress map editor has moved!", 'mappress-google-maps-for-wordpress'));
				$content = sprintf("<p>%s</p>", __('Use this button to open the new popup editor where you can easily create, edit and insert maps.', 'mappress-google-maps-for-wordpress'));
				self::$pointers['257_new_editor'] = $header . $content;
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
		$infinite = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' );
		$rest = defined('REST_REQUEST') && REST_REQUEST;
		return (is_admin() || $rest || self::$options->footer && !$infinite);
	}

	static function is_gt($post) {
		return function_exists('use_block_editor_for_post') && use_block_editor_for_post($post) && in_array($post->post_type, self::$options->postTypes);
	}

	static function is_localhost() {
		return !filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	static function is_ssl() {
		return (is_ssl() || !filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
	}

	static function l10n() {
		global $post;

		$l10n = array(
			'delete_prompt' => __('Are you sure you want to delete?', 'mappress-google-maps-for-wordpress'),
			'delete_map_prompt' => __('Permanently delete this map from the map library?', 'mappress-google-maps-for-wordpress'),
			'kml_error' => __('Error reading KML file', 'mappress-google-maps-for-wordpress'),
			'layer' => __('URL for KML file', 'mappress-google-maps-for-wordpress'),
			'loading' => "<span class='mapp-spinner'></span>" . __('Loading', 'mappress-google-maps-for-wordpress'),
			'need_classic' => __('Please select a classic editor block to insert into.', 'mappress-google-maps-for-wordpress'),
			'no_geolocate' => __('Unable to get your location', 'mappress-google-maps-for-wordpress'),
			'no_results' => __('No results', 'mappress-google-maps-for-wordpress'),
			'save' => __('Save changes?', 'mappress-google-maps-for-wordpress'),
			'shape' => __('Shape', 'mappress-google-maps-for-wordpress')
		);

		// Globals
		$l10n['options'] = array(
			'admin' => current_user_can('administrator'),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'baseurl' => self::$baseurl,
			'debug' => self::$debug,
			'editurl' => admin_url('post.php'),
			'iconsUrl' => (self::$pro) ? Mappress_Icons::$icons_url : null,
			'language' => self::get_language(),
			'mapbox' => self::get_api_keys()->mapbox,
			'nonce' => wp_create_nonce('mappress'),
			'pointers' => self::$pointers,
			'postid' => ($post) ? $post->ID : null,	// Note: GT => numeric, classic => string
			'pro' => self::$pro,
			'ssl' => self::is_ssl(),                // SSL is needed for 'your location' in directions
			'standardIcons' => (self::$pro) ? Mappress_Icons::$standard_icons : null,
			'standardIconsUrl' => (self::$pro) ? Mappress_Icons::$standard_icons_url : null,
			'userIcons' => (self::$pro) ? Mappress_Icons::get_user_icons() : null
		);

		// Leaflet layers for mapbox / OSM
		if (self::$options->engine == 'leaflet') {
			// Providers
			$providers = array(
				'mapbox' => array(
					'accessToken' => self::get_api_keys()->mapbox,
					'attribution' => "<a href='https://www.mapbox.com/about/maps/' target='_blank'>&copy; Mapbox &copy; OpenStreetMap</a> <a class='mapbox-improve-map' href='https://www.mapbox.com/map-feedback/' target='_blank'>" . __('Improve this map', 'mappress-google-maps-for-wordpress') . "</a>",
					'fresh' => true,	// Fresh = true in order to provide updated studio styles
					'url' => "https://api.mapbox.com/styles/v1/{user}/{id}/tiles/256/{z}/{x}/{y}{r}?access_token={accessToken}&fresh={fresh}",
					'zoomOffset' => 0
				),
				'osm' => array(
					'attribution' => 'Map data (c)<a href="https://openstreetmap.org">OpenStreetMap</a>',
					'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
				)
			);
			$l10n['options']['providers'] = $providers;

			// Baselayers
			$baselayers = array();
			if (self::get_api_keys()->mapbox) {
				$baselayers = array(
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'streets-v10', 'name' => 'streets', 'label' => __('Streets', 'mappress-google-maps-for-wordpress')),
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'outdoors-v10', 'name' => 'outdoors', 'label' => __('Outdoors', 'mappress-google-maps-for-wordpress')),
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'light-v9', 'name' => 'light', 'label' => __('Light', 'mappress-google-maps-for-wordpress')),
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'dark-v9', 'name' => 'dark', 'label' => __('Dark', 'mappress-google-maps-for-wordpress')),
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'satellite-v9', 'name' => 'satellite', 'label' => __('Satellite', 'mappress-google-maps-for-wordpress')),
					array('provider' => 'mapbox', 'user' => 'mapbox', 'id' => 'satellite-streets-v10', 'name' => 'satellite-streets', 'label' => __('Satellite Streets', 'mappress-google-maps-for-wordpress'))
				);

				// Mapbox studio styles - extract user/id from url
				foreach(self::$options->mapboxStyles as $name => $url) {
					$url = str_ireplace('.html', '', $url);
					$url = str_ireplace('https://api.mapbox.com/styles/v1/', '', $url);		// Old studio format
					$url = str_ireplace('mapbox://styles/', '', $url);						// New studio format
					$parts = explode('/', $url);
					if (count($parts) == 2)
						$baselayers[] = array('provider' => 'mapbox', 'user' => $parts[0], 'id' => $parts[1], 'name' => $name, 'label' => $name);
				}
			} else {
				$baselayers = array(
					array('provider' => 'osm', 'id' => 'osm', 'name' => 'osm', 'label' => __('Streets', 'mappress-google-maps-for-wordpress'))
				);
			}
			$l10n['options']['baseLayers'] = $baselayers;
		} else {
			// Google wizard styles
			$l10n['options']['styles'] = array();
			foreach(self::$options->styles as $id => &$style)
				$l10n['options']['styles'][$id] = json_decode($style);
		}

		// Global settings
		$options = array('alignment', 'clustering', 'country', 'defaultIcon', 'directions', 'directionsServer', 'engine', 'filter', 'geocoder', 'iconScale', 'initialOpenInfo', 'iwType', 'layout', 'mashupClick', 'mini', 'poiList', 'poiZoom', 'radius', 'search', 'size', 'sizes', 'style');
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

	static function scripts_register() {
		$dev = self::is_dev();
		$footer = self::is_footer();

		// Directories
		$lib = self::$baseurl . '/lib';
		$js = ($dev) ? "http://localhost/$dev/wp-content/plugins/mappress-google-maps-for-wordpress/build" : self::$baseurl . '/build';

		// Leaflet
		wp_register_script("mappress-leaflet", 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', null, '1.7.1', $footer);
		wp_register_script("mappress-omnivore", $lib . "/leaflet-omnivore.min.js", null, '0.3.1', $footer);

		// Google
		$language = self::get_language();
		$language = ($language) ? "&language=$language" : '';
		$apiversion = ($dev) ? 'v=beta' : 'v=3';
		$apikey = "&key=" . self::get_api_keys()->browser;
		$libs = '&libraries=places,drawing';
		// TBD $mapids = '&map_ids=';
		wp_register_script("mappress-google", "https://maps.googleapis.com/maps/api/js?{$apiversion}{$language}{$libs}{$apikey}", null, null, $footer);

		// Clustering ( https://github.com/googlemaps/js-markerclustererplus | https://github.com/Leaflet/Leaflet.markercluster )
		wp_register_script('mappress-markerclustererplus', "https://unpkg.com/@googlemaps/markerclustererplus@1.0.3/dist/index.min.js", null, '1.0.3', $footer);
		wp_register_script('mappress-leaflet-markercluster', $lib . "/Leaflet.markercluster/leaflet.markercluster.js", null, '1.4.1', $footer);

		// Dependencies
		$deps = array('jquery', 'jquery-ui-autocomplete', 'underscore');
		if (self::$options->engine == 'leaflet')
			$deps = array_merge(array('mappress-leaflet', 'mappress-omnivore'), $deps);
		if (self::$options->engine != 'leaflet' || self::$options->geocoder == 'google')
			$deps[] = 'mappress-google';
		if (self::$options->clustering)
			$deps[] = (self::$options->engine == 'leaflet') ? 'mappress-leaflet-markercluster' : 'mappress-markerclustererplus';

		wp_register_script('mappress', $js . "/index_mappress.js", $deps, self::$version, $footer);
		wp_register_script('mappress_admin', $js . "/index_mappress_admin.js", array('mappress', 'jquery-ui-position', 'jquery-ui-sortable', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-editor', 'wp-element', 'wp-i18n'), self::$version, $footer);

		// I18N
		if (function_exists('wp_set_script_translations')) {
			wp_set_script_translations('mappress', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
			wp_set_script_translations('mappress_admin', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
		}
	}

	static function scripts_enqueue($type = 'frontend') {
		if (self::$loaded)
			return;
		else
			self::$loaded = true;

		wp_enqueue_script('mappress');
		wp_localize_script('mappress', 'mappl10n', self::l10n());

		if ($type == 'backend')
			wp_enqueue_script('mappress_admin');

		// Templates
		$templates = array('map', 'map-directions', 'map-filters', 'map-item', 'map-loop', 'map-popup', 'mashup-popup', 'mashup-loop', 'mashup-item');
		if ($type == 'backend')
			$templates = array_merge($templates, array('mce', 'editor', 'mce', 'widgets'));

		foreach($templates as $template_name)
			Mappress_Template::enqueue_template($template_name, self::is_footer());
	}

	static function plugin_action_links($links, $file) {
		$settings_link = "<a href='" . admin_url("admin.php?page=mappress") . "'>" . __('Settings', 'mappress-google-maps-for-wordpress') . "</a>";
		array_unshift( $links, $settings_link );
		return $links;
	}

	static function plugins_loaded() {
		load_plugin_textdomain('mappress-google-maps-for-wordpress', false, dirname(self::$basename) . '/languages');
	}

	static function script($script, $ready = false) {
		$html = "\r\n<script type='text/javascript'>\r\n";
		$html .= ($ready) ? "jQuery(function() { $script });" : $script;
		$html .= "\r\n</script>";
		return $html;
	}

	static function script_template($template, $id = null) {
		$id = ($id) ? "id='mapp-tmpl-{$id}'" : '';
		$html = "\r\n<script type='text/template' $id>\r\n{$template}\r\n</script>\r\n";
		return $html;
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

		$atts = self::string_to_boolean($atts);

		// Shortcode attributes are lowercase so convert everything to lowercase
		$atts = array_change_key_case($atts);

		// Map options - includes both leaflet and Google
		foreach(array('disableDefaultUI', 'disableDoubleClickZoom', 'draggable', 'dragging', 'fullscreenControl', 'keyboard', 'keyboardShortcuts', 'mapTypeControl', 'maxZoom', 'minZoom', 'panControl', 'rotateControl', 'scaleControl', 'scrollwheel', 'scrollWheelZoom', 'streetViewControl', 'zoomControl') as $opt) {
			$lcopt = strtolower($opt);
			if (isset($atts[$lcopt])) {
				$atts['mapopts'][$opt] = $atts[$lcopt];
				unset($atts[$lcopt]);
			}
		}

		// Explode layers
		if (isset($atts['layers'])) {
			$atts['layers'] = explode(',', $atts['layers']);
			foreach($atts['layers'] as &$layer)
				$layer = trim($layer);
		}

		// Search = 'post', replace with post's location
		if (isset($atts['center']) && $atts['center'] == 'post') {
			global $post;
			$maps = Mappress_Map::get_list($post->ID, 'ids');
			$map = ($maps) ? Mappress_Map::get($maps[0]) : null;
			$atts['center'] = ($map && $map->pois) ? $map->pois[0]->point['lat'] . ',' . $map->pois[0]->point['lng'] : null;
		}

		// Conver GT 'align' to 'alignment'
		if (isset($atts['align']))
			$atts['alignment'] = $atts['align'];

		return $atts;
	}

	/**
	* Switch WPML language during AJAX calls
	*/
	static function set_language() {
		global $sitepress;
		if ($sitepress && method_exists($sitepress, 'switch_lang')) {
			$language = self::get_language();
			$sitepress->switch_lang($language);
		}
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
			$maps = Mappress_Map::get_list($post->ID);
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
		$mapids = Mappress_Map::get_list($post->ID, 'ids');
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

	/**
	* Scripts & styles for frontend
	* CSS is loaded from: child theme, theme, or plugin directory
	*/
	static function wp_enqueue_scripts() {
		// Leaflet CSS
		if (self::$options->engine == 'leaflet') {
			wp_enqueue_style('mappress-leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', null, '1.7.1');
			if (self::$options->clustering) {
				wp_enqueue_style('mappress-leaflet-markercluster-default', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.Default.css", null, '1.4.1');
				wp_enqueue_style('mappress-leaflet-markercluster', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.css", null, '1.4.1');
			}
		}

		// Mappress CSS from plugin directory
		wp_enqueue_style('mappress', self::$baseurl . '/css/mappress.css', null, self::$version);

		// Mappress CSS from theme directory
		if ( @file_exists( get_stylesheet_directory() . '/mappress.css' ) )
			$file = get_stylesheet_directory_uri() . '/mappress.css';
		elseif ( @file_exists( get_template_directory() . '/mappress.css' ) )
			$file = get_template_directory_uri() . '/mappress.css';

		if (isset($file))
			wp_enqueue_style('mappress-custom', $file, array('mappress'), self::$version);

		// Load scripts in header
		if (!self::is_footer())
			self::scripts_enqueue();
	}

	static function wp_head() {
		echo "\r\n<!-- MapPress Easy Google Maps " . __('Version', 'mappress-google-maps-for-wordpress') . ':' . self::$version . " (http://www.mappresspro.com/mappress) -->\r\n";
		echo "<script type='text/javascript'>mapp = window.mapp || {}; mapp.data = [];</script>\r\n";
	}
}

$mappress = new Mappress();
?>