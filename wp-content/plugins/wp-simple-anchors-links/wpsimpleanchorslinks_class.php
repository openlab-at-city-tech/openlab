<?php

/**
 * @package WP Simple Anchors Links
 * @link http://www.kilukrumedia.com
 * @copyright Copyright &copy; 2014, Kilukru Media
 * @version: 1.0.0
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
//ini_set('log_errors', 'On');
//ini_set('error_reporting', E_ALL);


/**
 * Init the Class
 */
class WP_Simple_Anchors_Links {

	// Set version of element
	public $wp_version;
	public $version_css;
	public $version_js;
	public $minimum_version_functions;
	public $minimum_PHP;
	public $minimum_WP;


	// Filename of log file.
	public $log_file;

	// Flag whether there should be logging.
	public $do_log;

	// Options of the plug-in
	public $options;

	// Set admin notices informations
	public $admin_notices_infos;

	//function WP_Mobilizer() {
	public function __construct() {
		global $wp_version;

		// Current WP version
		$this->wp_version 	= $wp_version;

		// Minimum requirements
		$this->minimum_PHP 	= '5.0';
		$this->minimum_WP 	= '3.6.0';

		// Version of assets files
		$this->version_css 	= '1.0.0';
		$this->version_js 	= '1.0.0';

		// Set admin notices into array mode
		$this->admin_notices_infos 	= array();

		// Stop the plugin if we missed the requirements
		if ( !$this->required_version() || !$this->required_version_php() ){
			return;
		}

		// Hook for init element
		add_action( 'init', 						array( &$this, 'init' 						), 5 );
		add_action( 'init', 						array( &$this, 'posts_button' 				) );

		//add_action( 'admin_init', 				array( &$this, 'admin_init'					) );
		add_action( 'wp_head', 						array( &$this, 'wp_head'					) );


		// Add the script and style files
		add_action('admin_enqueue_scripts', 		array( &$this, 'load_scripts'				) );
		add_action('admin_enqueue_scripts', 		array( &$this, 'load_styles'					) );
		add_action('wp_enqueue_scripts', 			array( &$this, 'load_styles_frontend'		) );

		// Add Shortcode
		add_shortcode( 'wpanchor', 					array( &$this, 'shortcode_anchor'			) );

		// Add Dashboard Widget
		add_action( 'wp_dashboard_setup', 			array( &$this, 'dashboard_setup'				) );

	}


	/**
	 * Runs after WordPress has finished loading but before any headers are sent
	 */
	public function init() {
		// Load Language files
		if ( !defined( 'WP_PLUGIN_DIR' ) ) {
			load_plugin_textdomain( 'wp_simple-anchors-links', str_replace( ABSPATH, '', dirname( __FILE__ ) ) );
		} else {
			load_plugin_textdomain('wp_simple-anchors-links', false, WPSIMPLEANCHORSLINKS_PLUGIN_DIRNAME . '/languages/' );
		}
	}


	/**
	 * Runs after WordPress has finished loading but before any headers are sent
	 */
	public function admin_init() {
	}


	/**
	 * Action hook is triggered within the <head></head> section of the user's template by the wp_head() function.
	 */
	public function wp_head() {
		if ( is_feed() ) {
			return;
		}

		echo "\n<!-- WP Simple Anchors Links " . WPSIMPLEANCHORSLINKS_VERSION . " by Kilukru Media (www.kilukrumedia.com)";
			if( isset($_GET['show_time']) ){
				echo "[" . time() . "] ";
			}
		echo "-->\n";
		echo "<!-- /WP Simple Anchors Links -->\n";

	}


	/**
	 * Runs after WordPress has finished loading but before any headers are sent
	 */
	function dashboard_widget_function() {
		if( defined('WPSIMPLEANCHORSLINKS_DISABLED_DASHBOARD_WIDGET') ){
			return;
		}

		//echo '<ul class="ul-disc">';
		//	echo '<li>Facts - [<a href="http://www.kilukrumedia.com" target="_blank">' . __('More infos', 'wp_simple-anchors-links') . '</a>] (' . __('Free', 'wp_simple-anchors-links') . ')</li>';
		//echo '</ul>';
		//

		echo '
		<div class="dashboard_widget_block">
			<p><i class="wpsal_icon-mug"></i> Beer == New Awesome Features on all our plugins.<br />Thanks so much for your support.</p>
			<a class="button button-primary" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CF9ZFWY59VXAJ">Buy me a Beer. <i class="wpsal_icon-heart"></i> You!</a>
			<!-- <a class="button button-primary" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CF9ZFWY59VXAJ">or 10$</a> -->
		</div>
		';
	}


	/**
	 * Runs after WordPress has finished loading but before any headers are sent
	 */
	function dashboard_setup() {
		if( defined('WPSIMPLEANCHORSLINKS_DISABLED_DASHBOARD_WIDGET') ){
			return;
		}

		wp_add_dashboard_widget('wp_dashboard_widget', '<i class="wpsal_icon-heart"></i> WP Simple Anchors Links?', array(&$this, 'dashboard_widget_function') );
	}


	/**
	 * Add setup string to parsing string
	 */
	function setup_id_name ( $string ){

		$string = iconv('UTF-8','ASCII//TRANSLIT', $string);
		$string = str_replace( array('!','=','*','','&','^','%','$','#','@','"',">","<","\n","\r","'",'`','?','|','\\','/','+',']','[','}','{'), '', $string);
		$string = str_replace( array(' '), '_', $string);
		$string = strtolower($string);

		return $string;
	}


	/**
	 * Add Shortcode for WP
	 */
	function shortcode_anchor( $atts, $content=null, $code="" ){
		extract(shortcode_atts(array(
			'id' 				=> false,
			'name' 				=> false,
			'type' 				=> 'a',
			'top' 				=> false,
			'bottom' 			=> false,
		), $atts));

		$styles = '';

		if( $content && !$id && !$name ){
			$id = $this->setup_id_name($content);
		}

		if( $id && !empty($id) && !$name ){
			$name = $id;
		}

		if( $name && !empty($name) && !$id ){
			$id = $name;
		}

		// Catch errors
		if( !$id || empty($id) ){
			return '<!-- No ID anchor detected /WP Simple Anchors Links -->';
		}

		// Set some style
		if( $top && !empty($top) ){
			$styles .= 'top:' . $top . ';';
		}
		if( $bottom && !empty($bottom) ){
			$styles .= 'bottom:' . $bottom . ';';
		}
		if( !empty($styles) ){
			$styles = ' style="' . $styles . '"';
		}

		// Satitize string
		$id = $this->setup_id_name($id);
		$name = $this->setup_id_name($name);

		$return = '';
		$return .= '<'. $type .' class="wpsal-anchor" name="' . $name . '" id="'.$id.'"' . $styles . '>';
			if( $content && !empty($content) ){
				$return .= $content;
			}
	    $return .= '</'. $type .'>';
	    return $return;
	}

	/**
	 * Register button for shortcode
	 */
	function register_button( $buttons ) {
	   array_push( $buttons, "|", "wpanchor" );
	   return $buttons;
	}
	function add_plugin( $plugin_array ) {
	   $plugin_array['wpanchor'] = WPSIMPLEANCHORSLINKS_PLUGIN_JS_URL . 'admin/wpsimpleanchorslinks.js';
	   return $plugin_array;
	}
	function posts_button() {
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
	      return;
	   }

	   if ( get_user_option('rich_editing') == 'true' ) {
	      add_filter( 'mce_external_plugins', array(&$this, 'add_plugin') );
	      add_filter( 'mce_buttons', array(&$this, 'register_button') );
	   }

	}


	/**
	 * Load JavaScripts
	 */
	function load_scripts() {
		// Check if they are in admin
		if( is_admin() ){

			// Set the common scripts
			wp_register_script('wpsimpleanchorslinks_admin_htmlhead', path_join(
				WPSIMPLEANCHORSLINKS_PLUGIN_JS_URL,
				'admin/htmlhead' . ( $this->get_filetime_forfile() ) . '.js'
			), array('jquery'), $this->get_version_number($this->version_js), true );
			wp_enqueue_script( 'wpsimpleanchorslinks_admin_htmlhead' );

		}

	}


	/**
	 * Load CSS styles
	 */
	function load_styles() {
		// Check if they are in admin
		if( is_admin() ){
			// Set the common style
			wp_register_style( 'wpsimpleanchorslinks_admin_styles', WPSIMPLEANCHORSLINKS_PLUGIN_CSS_URL .'admin/styles' . ( $this->get_filetime_forfile() ) . '.css', false, $this->get_version_number($this->version_css), 'screen' );
			wp_enqueue_style( 'wpsimpleanchorslinks_admin_styles' );
		}
	}


	/**
	 * Load CSS styles on Frontend
	 */
	function load_styles_frontend() {
		// Check if they not are in admin
		if( !defined('WPSIMPLEANCHORSLINKS_DISABLED_FRONTEND_CSS') && !is_admin() ){
			// Set the common style
			wp_register_style( 'wpsimpleanchorslinks_styles', WPSIMPLEANCHORSLINKS_PLUGIN_CSS_URL . 'styles' . ( $this->get_filetime_forfile() ) . '.css', false, $this->get_version_number($this->version_css), 'screen' );
			wp_enqueue_style( 'wpsimpleanchorslinks_styles' );
		}

	}


	/**
	 * Check if the version of WP is compatible with this plugins minimum requirment.
	 */
	function required_version() {
		global $wp_version;

		// Check for WP version installation
		$wp_ok  =  version_compare($wp_version, $this->minimum_WP, '>=');

		if ( ($wp_ok == FALSE) ) {
			$this->admin_notices( sprintf(__('Sorry, WP Simple Anchors Links works only under WordPress %s or higher', "wp_simple-anchors-links" ), $this->minimum_WP ), true );
			return false;
		}

		return true;

	}


	/**
	 * Check if the version of PHP of this server is compatible with this plugins minimum requirment.
	 */
	function required_version_php() {
		global $wp_version;

		// Check for PHP version installation
		$wp_ok  =  version_compare(PHP_VERSION, $this->minimum_PHP, '>=');

		if ( ($wp_ok == FALSE) ) {
			$this->admin_notices( sprintf(__('Sorry, WP Simple Anchors Links works only under PHP %s or higher', "wp_simple-anchors-links" ), $this->minimum_PHP ), true );

			return false;
		}

		return true;

	}


	/**
	 * Notice admin with some messages
	 */
	function admin_notices( $text, $errormsg = false ) {
		// Add text to admin notice info
		$this->admin_notices_infos[] = mblzr_show_essage($text, $errormsg);

		add_action(
			'admin_notices',
			create_function(
				'',
				'global $wpsimpleanchorslinks; if( is_array($wpsimpleanchorslinks->admin_notices_infos) && count($wpsimpleanchorslinks->admin_notices_infos) > 0 ){foreach($wpsimpleanchorslinks->admin_notices_infos as $notice){ echo $notice; } $wpsimpleanchorslinks->admin_notices_infos = array(); };'
			)
		);

	}


	/**
	* ADD Filetime into file if KM_FILEMTIME_REWRITE constant exist
	*
	* @param mixed $default
	* @return mixed
	*/
	public function get_filetime_forfile( $default = '' ){

		if( !defined('KM_FILEMTIME_REWRITE') || !defined('WPSIMPLEANCHORSLINKS_VERSION_FILETIME') ){
			return $default;
		}

		return '-' . WPSIMPLEANCHORSLINKS_VERSION_FILETIME;

	}


	/**
	* Return null value if KM_FILEMTIME_REWRITE constant exist
	*
	* @param mixed $default
	*/
	public function get_version_number( $default ){

		if( !defined('KM_FILEMTIME_REWRITE') ){
			return $default;
		}

		return null;

	}


	/**
	 * Set log file datas
	 */
	public function log( $message ) {
		if ( $this->do_log ) {
			error_log(date('Y-m-d H:i:s') . " " . $message . "\n", 3, $this->log_file);
		}
	}


	/**
	 * Check current user is an admin
	 *
	 */
	public function is_admin() {
		return current_user_can('level_8');
	}


	/**
	 * Dump var
	 */
	public function dump( $var ) {
		header('Content-Type:text/plain');
		var_dump( $var );
		exit;
	}


}
