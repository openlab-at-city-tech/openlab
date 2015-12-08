<?php
/*
Plugin Name: MapPress Easy Google Maps
Plugin URI: http://www.wphostreviews.com/mappress
Author URI: http://www.wphostreviews.com/mappress
Description: MapPress makes it easy to insert Google Maps in WordPress posts and pages.
Version: 2.43.4
Author: Chris Richardson
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
require_once dirname( __FILE__ ) . '/mappress_updater.php';

if (file_exists(dirname( __FILE__ ) . '/pro/mappress_pro.php')) {
	include_once dirname( __FILE__ ) . '/pro/mappress_pro.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_pro_settings.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_query.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_geocoders.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_icons.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget.php';
}
class Mappress {
	const VERSION = '2.43.4';

	static
		$baseurl,
		$basename,
		$basedir,
		$debug,
		$geocoders,
		$options,
		$pages,
		$updater;

	var
		$queue;

	function __construct()  {
		self::$options = Mappress_Options::get();
		self::$basename = plugin_basename(__FILE__);
		self::$baseurl = plugins_url('', __FILE__);
		self::$basedir = dirname(__FILE__);

		$this->debugging();

		// Initialize Pro classes
		if (class_exists('Mappress_Pro')) {
			self::$geocoders = new Mappress_Geocoders();
			self::$updater = new Mappress_Updater(self::$basename);
		}

		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('init', array($this, 'init'));

		add_shortcode('mappress', array($this, 'shortcode_map'));
		add_action('admin_notices', array($this, 'admin_notices'));

		// Post hooks
		add_action('deleted_post', array($this, 'deleted_post'));

		// Filter to automatically add maps to post/page content
		add_filter('the_content', array($this, 'the_content'), 2);

		// Scripts and stylesheets
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		// Frontend: output map in footer or header
		if (self::$options->footer)
			add_action('wp_print_footer_scripts', array($this, 'print_maps'));

		// Admin: output in footer only
		add_action('admin_print_footer_scripts', array($this, 'print_maps'));

		// Slow heartbeat
		if (self::$debug)
			add_filter( 'heartbeat_settings', array($this, 'heartbeat_settings'));
	}

	function heartbeat_settings( $settings ) {
		$settings['minimalInterval'] = 600;
		return $settings;
	}

	// mp_errors -> PHP errors
	// mp_remote -> use remote js
	// mp_debug -> add debug info
	function debugging() {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		self::$debug = (isset($_GET['mp_debug'])) ? true : ( defined('MAPPRESS_DEBUG') && MAPPRESS_DEBUG );

		if (isset($_GET['mp_errors'])) {
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors','On');
			$wpdb->show_errors();
		}

		if (isset($_GET['mp_info'])) {
			echo "<b>Plugin version</b> " . $this->get_version_string();
			die();
		}
	}

	static function get_version_string() {
		$version = __('Version', 'mappress') . ":" . self::VERSION;
		if (class_exists('Mappress_Pro'))
			$version .= " PRO";
		return $version;
	}

	static function get_support_links() {
		echo self::get_version_string();
		echo " | <a target='_blank' href='http://wphostreviews.com/mappress/mappress-documentation'>" . __('Documentation', 'mappress') . "</a>";
		echo " | <a target='_blank' href='http://wphostreviews.com/chris-contact'>" . __('Support', 'mappress') . "</a>";

		if (!class_exists('Mappress_Pro'))
			echo "<a class='button button-primary' style='margin-left: 20px' href='http://wphostreviews.com/mappress' target='_blank'>" . __('Upgrade to MapPress Pro', 'mappress') . "</a>";
	}

	static function ajax_response($status, $data=null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error
		header( "Content-Type: application/json" );
		$response = json_encode(array('status' => $status, 'data' => $data, 'output' => $output));
		die ($response);
	}

	/**
	* When a post is deleted, delete its map assignments
	*
	*/
	function deleted_post($postid) {
		Mappress_Map::delete_post_map($postid);
	}

	function admin_menu() {
		// Settings
		$settings = (class_exists('Mappress_Pro')) ? new Mappress_Pro_Settings() : new Mappress_Settings();
		self::$pages[] = add_menu_page('MapPress', 'MapPress', 'manage_options', 'mappress', array(&$settings, 'options_page'), self::$baseurl . '/images/mappress_pin_logo.png');
	}

	/**
	* Automatic map display.
	* If set, the [mappress] shortcode will be prepended/appended to the post body, once for each map
	* The shortcode is used so it can be filtered - for example WordPress will remove it in excerpts by default.
	*
	* @param mixed $content
	*/
	function the_content($content="") {
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
		if (is_feed() || is_admin())
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

		// Get maps associated with post
		$maps = Mappress_Map::get_post_map_list($post->ID);
		if (empty($maps))
			return $content;

		// Add the shortcode once for each map
		$shortcodes = "";
		foreach($maps as $map)
			$shortcodes .= '<p>[mappress mapid="' . $map->mapid . '"]</p>';

		if ($autodisplay == 'top')
			return $shortcodes . $content;
		else
			return $content . $shortcodes;
	}

	/**
	* Map a shortcode in a post.
	*
	* @param mixed $atts - shortcode attributes
	*/
	function shortcode_map($atts='') {
		global $post;

		// No feeds
		if (is_feed())
			return;

		// Try to protect against calls to do_shortcode() in the post editor...
		if (is_admin())
			return;

		$atts = $this->scrub_atts($atts);

		// Determine what to show
		$mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;

		if ($mapid) {
			// Show map by mapid
			$map = Mappress_Map::get($mapid);
		} else {
			// Get the first map attached to the post
			$maps = Mappress_Map::get_post_map_list($post->ID);
			$map = (isset ($maps[0]) ? $maps[0] : false);
		}

		if (!$map)
			return;

		return $map->display($atts);
	}

	/**
	* Scripts & styles for frontend
	* CSS is loaded from: child theme, theme, or plugin directory
	*/
	function wp_enqueue_scripts() {
		// Load CSS
		if (self::$options->css) {
			// Load the default CSS from the plugin directory
			wp_enqueue_style('mappress', self::$baseurl . '/css/mappress.css', null, self::VERSION);

			// If a 'mappress.css' exists in the theme directory, load that afterwards
			if ( @file_exists( get_stylesheet_directory() . '/mappress.css' ) )
				$file = get_stylesheet_directory_uri() . '/mappress.css';
			elseif ( @file_exists( get_template_directory() . '/mappress.css' ) )
				$file = get_template_directory_uri() . '/mappress.css';

			if (isset($file))
				wp_enqueue_style('mappress-custom', $file, array('mappress'), self::VERSION);
		}

		// Load scripts in header
		if (!self::$options->footer)
			$this->load();
	}

	// Scripts & styles for admin
	// CSS is always loaded from the plugin directory
	function admin_enqueue_scripts($hook) {
		// Some plugins call this without setting $hook
		if (empty($hook))
			return;

		// Network admin has no pages
		if (empty(self::$pages))
			return;

		// Settings scripts
		if ($hook == self::$pages[0])
			$this->load('settings');

		// CSS
		if (in_array($hook, self::$pages) || in_array($hook, array('edit.php', 'post.php', 'post-new.php'))) {
			wp_enqueue_style('mappress', self::$baseurl . '/css/mappress.css', null, self::VERSION);
			wp_enqueue_style('mappress-admin', self::$baseurl . '/css/mappress_admin.css', null, self::VERSION);
		}
	}

	/**
	* There are several WP bugs that prevent correct activation in multisitie:
	*   http://core.trac.wordpress.org/ticket/14170
	*   http://core.trac.wordpress.org/ticket/14718)
	* These bugs have been open for months.  A workaround is to just 'activate' the plugin whenever it runs
	* (the tables are only created if they don't exist already)
	*
	*/
	function init() {
		// Load text domain
		load_plugin_textdomain('mappress', false, dirname(self::$basename) . '/languages');

		// Register hooks and create database tables
		Mappress_Map::register();

		// Register static classes
		if (class_exists('Mappress_Pro')) {
			Mappress_Icons::register();
			Mappress_Query::register();
		}

		// Check if upgrade is needed
		$current_version = get_option('mappress_version');

		if ($current_version < '2.38.2') {
			self::$options->metaKeys = array(self::$options->metaKey);
			self::$options->save();
		}

		update_option('mappress_version', self::VERSION);
	}

	// Sanity checks via notices
	function admin_notices() {
		global $wpdb;
		$error =  "<div id='error' class='error'><p>%s</p></div>";

		$map_table = $wpdb->prefix . "mappress_maps";
		$exists = $wpdb->get_var("show tables like '$map_table'");

		if (!$exists) {
			echo sprintf($error, __("MapPress database tables are missing.  Please deactivate the plugin and activate it again to fix this.", 'mappress'));
			return;
		}

		if (get_bloginfo('version') < "3.2") {
			echo sprintf($error, __("WARNING: MapPress now requires WordPress 3.2 or higher.  Please upgrade before using MapPress.", 'mappress'));
			return;
		}

		if (class_exists('WPGeo')) {
			echo sprintf($error, __("WARNING: MapPress is not compfatible with the WP-Geo plugin.  Please deactivate or uninstall WP-Geo before using MapPress.", 'mappress'));
			return;
		}
	}

	/**
	* Scrub attributes
	* The WordPress shortcode API passes shortcode attributes in lowercase and with boolean values as strings (e.g. "true")
	* Converts atts to lowercase, replaces boolean strings with booleans, and creates arrays from comma-separated attributes
	*
	* Returns empty array if $atts is empty or not an array
	*/
	function scrub_atts($atts=null) {
		if (!$atts || !is_array($atts))
			return array();

		$atts = self::string_to_boolean($atts);

		// Shortcode attributes are lowercase so convert everything to lowercase
		$atts = array_change_key_case($atts);

		// Point
		if (isset($atts['point_lat']) && isset($atts['point_lng'])) {
			$atts['point'] = array('lat' => $atts['point_lat'], 'lng' => $atts['point_lng']);
			unset($atts['point_lat'], $atts['point_lng']);
		}

		// Center - back compat for center_lat/center_lng
		if (isset($atts['center_lat']) && isset($atts['center_lng'])) {
			$atts['center'] = array('lat' => $atts['center_lat'], 'lng' => $atts['center_lng']);
			unset($atts['center_lat'], $atts['center_lng']);
		} elseif (isset($atts['center'])) {
			$latlng = explode(',', $atts['center']);
			if (count($latlng) == 2)
				$atts['center'] = array('lat' => $latlng[0], 'lng' => $latlng[1]);
			else
				unset($atts['center']);
		}

		// Back-compat for initialOpenDirections: convert to 'to'
		if (isset($atts['initialopendirections']) && !is_bool($atts['initialopendirections']) && !isset($atts['to'])) {
			$atts['to'] = $atts['initialopendirections'];
			$atts['initialopendirections'] = true;
		}

		// MapTypeIds
		if (isset($atts['maptypeids']))
			$atts['maptypeids'] = explode(',', $atts['maptypeids']);

		// Poi Links
		if (isset($atts['poilinks']))
			$atts['poilinks'] = explode(',', $atts['poilinks']);

		// Map links
		if (isset($atts['maplinks']))
			$atts['maplinks'] = explode(',', $atts['maplinks']);

		return $atts;
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

	static function boolean_to_string($data) {
		if ($data === false)
			return "false";
		if ($data === true)
			return "true";

		if (is_array($data)) {
			foreach($data as &$datum)
				$datum = self::boolean_to_string($datum);
		}

		return $data;
	}

	/**
	* Output javascript
	*
	* @param mixed $script
	*/
	static function script($script) {
		// Workaround for Nextgen and Better WordPress Minify plugins, which reverse sequence of wp_enqueue_scripts and wp_print_footer_scripts output
		$script = "jQuery(document).ready(function () { \r\n$script\r\n });";
		return "\r\n<script type='text/javascript'>\r\n{$script}\r\n</script>\r\n";
	}

	function enqueue_map($map = null) {
		// Output map immediately if scripts loaded in header
		if ($map && !self::$options->footer && !is_admin())
			return $this->get_map($map);

		// Load scripts and enqueue map
		$this->load();
		$this->queue[$map->name] = $map;
	}

	function enqueue_editor() {
		$this->load('editor');
		$this->queue['editor'] = true;
	}

	function print_maps() {
		// If queue is empty there's nothing to do
		if (empty($this->queue))
			return;

		echo "\r\n<!-- MapPress Easy Google Maps " . self::get_version_string() . " (http://www.wphostreviews.com/mappress) -->\r\n";

		if (isset($this->queue['editor'])) {
			$script = "window.mappEditor = new mapp.Media();";
			echo Mappress::script($script);
			return;
		}

		foreach ($this->queue as $map)
			echo $this->get_map($map);

		$this->queue = array();
	}

	/**
	* Print a single map
	*
	* @param mixed $map
	*/
	function get_map($map) {
		// For static maps prepare the pois immediately
		if (empty($map->query))
			$map->prepare();

		$script = "var mapdata = " . json_encode($map) . ";\r\n"
			. "window.$map->name = new mapp.Map(mapdata); \r\n"
			. "$map->name.display(); ";

		$html = Mappress::script($script);

		if ($map->options->directions == 'inline') {
			$html .= "<div id='{$map->name}_directions_' style='display:none'>";
			$html .= $this->get_template($map->options->templateDirections, array('map' => $map));
			$html .= "</div>";
		}
		return $html;
	}

	function load($type = null) {
		static $loaded;

		if ($loaded)
			return;

		$loaded = true;

		$version = self::VERSION;
		$footer = self::$options->footer;
		$apikey = (!empty(self::$options->apiKey)) ? "&key=" . self::$options->apiKey : '';
		$libstring = ($type == 'editor') ? '&libraries=places,drawing' : '&libraries=places';

		// Directories
		$remote = (isset($_REQUEST['mp_remote'])) ? true : false;
		$min = (self::$debug || $remote) ? "" : ".min";
		$js = (self::$debug) ? self::$baseurl . '/src' : self::$baseurl . '/js';
		$js = ($remote) ? 'http://localhost/dev/wp-content/plugins/mappress-google-maps-for-wordpress/src' : $js;

		// Get language for WPML or qTranslate, or use options setting
		$language = (self::$options->language) ? self::$options->language : '';
		$language = (defined('ICL_LANGUAGE_CODE')) ? ICL_LANGUAGE_CODE : $language;
		$language = (function_exists('qtrans_getLanguage')) ? qtrans_getLanguage() : '';
		$language = ($language) ? "&language=$language" : '';

		wp_enqueue_script("mappress-gmaps", "https://maps.googleapis.com/maps/api/js?sensor=true{$language}{$libstring}{$apikey}", null, null, $footer);

		if ($type == 'editor')
			wp_enqueue_script('mappress_editor', $js . "/mappress_editor$min.js", array('jquery', 'jquery-ui-position', 'jquery-ui-slider'), $version);

		if ($type == 'settings')
			wp_enqueue_script('mappress_settings', $js . "/mappress_settings$min.js", array('postbox', 'jquery', 'jquery-ui-core', 'jquery-ui-position'));

		if (!$type && self::$options->dataTables) {
			wp_enqueue_script('mappress_datatables', self::$baseurl . "/pro/DataTables/media/js/jquery.dataTables$min.js", array('jquery'), $version, $footer);
			wp_enqueue_style('mappress-datatables', self::$baseurl . "/pro/DataTables/media/css/jquery.dataTables.css", null, '1.9.1');
		}

		if ($min) {
			wp_enqueue_script('mappress', $js . "/mappress.min.js", array('jquery'), $version, $footer);
		} else {
			wp_enqueue_script('mappress', $js . "/mappress.js", array('jquery'), $version, $footer);
			foreach(array('directions', 'geocoding', 'icons', 'infobox', 'lib', 'poi', 'widgets') as $script)
				wp_enqueue_script($script, $js . "/mappress_{$script}.js", null, $version, $footer);
		}

		wp_localize_script('mappress', 'mappl10n', $this->l10n());
	}

	function l10n() {
		global $post;

		$l10n = array(
			'bicycling' => __('Bicycling', 'mappress'),
			'bike' => __('Bike', 'mappress'),
			'dir_not_found' => __('One of the addresses could not be found.', 'mappress'),
			'dir_zero_results' => __('Google cannot return directions between those addresses.  There is no route between them or the routing information is not available.', 'mappress'),
			'dir_default' => __('Unknown error, unable to return directions.  Status code = ', 'mappress'),
			'directions' => __('Directions', 'mappress'),
			'kml_error' => __('Error reading KML file', 'mappress'),
			'loading' => __('Loading...', 'mappress'),
			'no_address' => __('No matching address', 'mappress'),
			'no_geolocate' => __('Unable to get your location', 'mappress'),
			'traffic' => __('Traffic', 'mappress'),
			'transit' => __('Transit', 'mappress'),
			'zoom' => __('Zoom', 'mappress')
		);

		if (is_admin()) {
			$l10n = array_merge($l10n, array(
				'add' => __('Add', 'mappress'),
				'click_and_drag' => __('Click & drag to move', 'mappress'),
				'click_to_change' => __('Click to change', 'mappress'),
				'del' => __('Delete', 'mappress'),
				'delete_prompt' => __('Delete this POI?', 'mappress'),
				'delete_map_prompt' => __('Delete this map?', 'mappress'),
				'save_first' => __('Please save the map first', 'mappress'),
				'shape' => __('Shape', 'mappress'),
				'untitled' => __('Untitled', 'mappress')
			));
		}

		// Globals
		$l10n['options'] = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'admin' => current_user_can('administrator'),
			'debug' => Mappress::$debug,
			'iconsUrl' => (class_exists('Mappress_Icons')) ? Mappress_Icons::$icons_url : null,
			'postid' => ($post) ? $post->ID : null,
			'siteUrl' => site_url(),
			'standardIconsUrl' => (class_exists('Mappress_Icons')) ? Mappress_Icons::$standard_icons_url : null
		);

		// Settings
		$options = array('country', 'defaultIcon', 'directionsServer', 'directionsUnits', 'iconScale', 'language', 'poiZoom', 'styles', 'tooltips');
		foreach($options as $option)
			$l10n['options'][$option] = self::$options->$option;

		// Styles
		foreach(self::$options->styles as $id => &$style)
			$l10n['options']['styles'][$id] = json_decode($style);

		return $l10n;
	}

	/**
	* Get language using settings/WPML/qTrans
	*
	*/
	static function get_language() {
		// For WPML (wpml.org): set the selected language if it wasn't specified in the options screen
		if (defined('ICL_LANGUAGE_CODE'))
			return ICL_LANGUAGE_CODE;

		// For qTranslate, pick up current language from that
		if (function_exists('qtrans_getLanguage'))
			return qtrans_getLanguage();

		return self::$options->language;
	}

	/**
	* Get a template to the buffer and return it
	*
	* @param mixed $template_name
	* @param mixed $args - see print_template()
	* @return mixed
	*/
	function get_template($template_name, $args = '') {
		ob_start();
		$this->print_template($template_name, $args);
		$html = ob_get_clean();
		$html = str_replace(array("\r\n", "\t"), array(), $html);  // Strip chars that won't display in html anyway
		return $html;
	}


	/**
	* Print a template.  $args:
	*   map         - map global to pass to the template
	*   poi         - poi global to pass to the template
	*
	* @param string $template_name
	* @param mixed $args
	* @return mixed
	*/
	function print_template( $template_name, $args = '' ) {
		$defaults = array(
			'map' => null,
			'poi' => null
		);
		extract(wp_parse_args($args, $defaults));
		$template_file = $this->find_template($template_name);
		require($template_file);
	}

	function find_template($template_name) {
		$template_name .= ".php";
		$template_file = locate_template($template_name, false);
		if (empty($template_file))
			$template_file = Mappress::$basedir . "/templates/$template_name";
		return $template_file;
	}
}  // End Mappress class

if (class_exists('Mappress_Pro'))
	$mappress = new Mappress_Pro();
else
	$mappress = new Mappress();
?>
