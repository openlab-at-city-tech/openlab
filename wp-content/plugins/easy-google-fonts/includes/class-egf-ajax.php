<?php
/**
 * Class: EGF_Ajax
 *
 * This file initialises the admin functionality for this plugin.
 * It initalises a posttype that acts as a data structure for
 * the font controls. It also has useful static helper functions
 * to get font controls. 
 * 
 *
 * @package   Easy_Google_Fonts_Admin
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.2
 * 
 */
if ( ! class_exists( 'EGF_Ajax' ) ) :
	class EGF_Ajax {
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.2
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.2
		 *
		 */
		protected $plugin_screen_hook_suffix = null;
		
		/**
		 * Constructor Function
		 * 
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		function __construct() {
			/**
			 * Call $plugin_slug from public plugin class.
			 *
			 */
			$plugin = Easy_Google_Fonts::get_instance();
			$this->plugin_slug = $plugin->get_plugin_slug();
			$this->register_actions();		
			$this->register_filters();
		}	

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function register_actions() {
			add_action( 'wp_ajax_tt_font_set_google_api_key', array( $this, 'set_google_api_key' ) );
			add_action( 'wp_ajax_tt_font_control_force_styles', array( $this, 'force_font_control_styles' ) );
			add_action( 'wp_ajax_tt_font_create_control_instance', array( $this, 'create_control_instance' ) );
			add_action( 'wp_ajax_tt_font_update_control_instance', array( $this, 'update_control_instance' ) );
			add_action( 'wp_ajax_tt_font_delete_control_instance', array( $this, 'delete_control_instance' ) );
			add_action( 'wp_ajax_tt_font_delete_all_control_instances', array( $this, 'delete_all_control_instances' ) );
		}

		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function register_filters() {

		}
		
		/**
		 * Set Google Fonts API Key - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * updates the Google API key.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function set_google_api_key() {
			// Check admin nonce for security
			check_ajax_referer( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			if ( isset( $_POST['apiKey'] ) ) {
				$apiKey = esc_attr( $_POST['apiKey'] );
				EGF_Font_Utilities::set_google_api_key( $apiKey );
			}

			// Delete Font Transients
			EGF_Font_Utilities::delete_font_transients(); 

			wp_die();
		}

		/**
		 * Force Styles for Control - Ajax Function
		 *
		 * Updates the 'force_styles' meta option for a 
		 * particular font control instance. If this is
		 * set to true then the !important modifer will
		 * be added to the styles upon output. Note: the
		 * js plugin passes the boolean true as the string
		 * 'true', therefore we need to do a string 
		 * comparison to check the users intent.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function force_font_control_styles() {
			// Check admin nonce for security
			check_ajax_referer( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			if ( isset( $_POST['controlId'] ) ) {
				$control_id = esc_attr( $_POST['controlId'] );
				$switch     = ( isset( $_POST['force-styles'] ) && 'true' == $_POST['force-styles'] ) ? true : false;
				$control    = EGF_Posttype::get_font_control( $control_id );

				if ( $control ) {
					update_post_meta( $control->ID, 'force_styles', $switch );
				}		
			}	

			wp_die();
		}

		/**
		 * Create Font Control Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * creates a new font control instance. This function then 
		 * constructs a new ajax response and sends it back to the
		 * client.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function create_control_instance() {
			
			// Check admin nonce for security
			check_ajax_referer( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Get Control Name
			if( isset( $_POST['control_name'] ) ) {
				$control_name =  esc_attr( $_POST['control_name'] );
			} else {
				$control_name = __( 'Custom Font Control', $this->plugin_slug );
			}

			// Create the new font control and get the associated ID
			$new_control    = EGF_Posttype::update_font_control( '0', $control_name );
			$new_control_id = get_post_meta( $new_control, 'control_id', true );

			// Create array to hold additional xml data
			$supplimental_data = array(
				'new_control_id'     => $new_control_id
			);

			$data = array(
				'what'         => 'new_control',
				'id'           => 1,
				'data'         => '',
				'supplemental' => $supplimental_data
			);

			
			// Create a new WP_Ajax_Response obj and send the request
			$x = new WP_Ajax_Response( $data );
			$x->send();

			wp_die();
		}

		/**
		 * Update Font Control Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * updates a new font control instance. This function then 
		 * constructs a new ajax response and sends it back to the
		 * client.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		public function update_control_instance() {

			// Check admin nonce for security
			check_ajax_referer( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}
			
			// Get control attributes	
			$control_id   = isset( $_POST['controlId'] )   ? (string) esc_attr( $_POST['controlId'] ) : (string) '0';
			$control_name = isset( $_POST['controlName'] ) ? (string) esc_attr( $_POST['controlName'] ) : __( 'Custom Font Control', $this->plugin_slug );
			$force_styles = false;
			$description  = '';

			$selectors = array();

			if ( isset( $_POST['control-selectors'] ) ) {
				$selectors = (array) $_POST['control-selectors'];
			}

			if ( isset( $_POST['force-styles'] ) ) {
				$force_styles = ( 'true' == $_POST['force-styles'] ) ? true : false;
			}

			for ( $i=0; $i < count( $selectors ); $i++ ) {
				while ( substr( $selectors[ $i ], -1 ) == ',' ) {
					$selectors[ $i ] = rtrim( $selectors[ $i ], ',' );
				}
			}

			// Update control or create a new one if it doesn't exist
			$control = EGF_Posttype::update_font_control( $control_id, $control_name, $selectors, $description, $force_styles );

			// Create array to hold additional xml data
			$supplimental_data = array(
				'control_name'     => get_the_title( $control )
			);

			$data = array(
				'what'         => 'control',
				'id'           => 1,
				'data'         => '',
				'supplemental' => $supplimental_data
			);

			// Create a new WP_Ajax_Response obj and send the request
			$x = new WP_Ajax_Response( $data );
			$x->send();

			wp_die();
		}

		/**
		 * Delete Font Control Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * it deletes the font control instance from the database.
		 *
		 * @since 1.0
		 * @version 1.1.1
		 * 
		 */
		public function delete_control_instance() {

			// Check admin nonce for security
			check_ajax_referer( 'tt_font_delete_control_instance', 'tt_font_delete_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			if ( isset( $_POST['controlId'] ) ) {
				EGF_Posttype::delete_font_control( $_POST['controlId'] );
			}

			wp_die();
		}

		/**
		 * Delete All Font Control Instances - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * it deletes all control instances from the database.
		 *
		 * @since 1.2
		 * @version 1.4.2
		 * 
		 */
		function delete_all_control_instances() {
			
			// Check admin nonce for security
			check_ajax_referer( 'tt_font_delete_control_instance', 'tt_font_delete_control_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			EGF_Posttype::delete_all_font_controls();

			wp_die();
		}

	}
endif;
