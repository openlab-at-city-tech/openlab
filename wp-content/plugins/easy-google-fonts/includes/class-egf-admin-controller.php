<?php
/**
 * Class: EGF_Admin_Controller
 *
 * This controller class is used to build the admin page
 * output. It includes the necessary views contained in 
 * the views/admin-page directory.
 *
 * @package   Easy_Google_Fonts_Admin
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
if ( ! class_exists( 'EGF_Admin_Controller' ) ) :
	class EGF_Admin_Controller {
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
		 * Url variables used on the admin page
		 * 
		 * @var      string
		 * @since    1.2
		 *
		 */
		protected $admin_url;
		protected $create_url;
		protected $manage_url;
		protected $advanced_url;

		/**
		 * Variables to keep track of current screen state
		 *
		 * @var boolean
		 * @since  1.2
		 */
		protected $is_edit_screen;
		protected $is_create_screen;
		protected $is_manage_screen;
		protected $is_advanced_screen;

		/**
		 * Variables to keep track of the font control 
		 * to load/edit.
		 *
		 * @var mixed
		 * @since  1.2
		 */
		protected $font_controls;
		protected $custom_controls;
		protected $first_control;
		protected $control_instance;
		protected $no_controls;
		protected $current_control_id;
		protected $control_selected_id;

		/**
		 * Constructor Function
		 * 
		 * Initialize the plugin by loading admin scripts & styles and adding a
		 * settings page and menu.
		 *
		 * @uses Easy_Google_Fonts::get_instance() defined in \includes\class-easy-google-fonts.php
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		function __construct() {
			/**
			 * Call $plugin_slug from public plugin class.
			 *
			 */
			$plugin = Easy_Google_Fonts::get_instance();
			$this->plugin_slug = $plugin->get_plugin_slug();
			
			// Setup class variables
			$this->set_urls();
			$this->set_font_controls();
			$this->set_screen_state();

			// Register actions and filters
			$this->register_actions();		
			$this->register_filters();
		}

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.2
		 * @version 1.4.4
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
		 * Set URLs for Admin Page
		 *
		 * Sets the URL variables that are used throughout the
		 * admin settings page.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		private function set_urls() {
			$this->admin_url    = esc_url( add_query_arg( array( 'page' => $this->plugin_slug ), admin_url( 'options-general.php' ) ) );
			$this->manage_url   = esc_url( add_query_arg( array( 'screen' => 'manage_controls' ), $this->admin_url ) );
			$this->advanced_url = esc_url( add_query_arg( array( 'screen' => 'advanced' ), $this->admin_url ) );
			$this->create_url   = esc_url( add_query_arg( array( 'screen' => 'edit_controls', 'action' => 'create' ), $this->admin_url ) );
		}
		
		/**
		 * Set Font Control Variables
		 *
		 * Sets the font control variables that are used throughout the
		 * admin settings page.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function set_font_controls() {
			$this->font_controls   = EGF_Posttype::get_all_font_controls();
			$this->custom_controls = array();
			
			// Set no control flag
			$this->no_controls = $this->font_controls ? false : true;

			if ( ! $this->no_controls ) {
				$count = 0;

				while ( $this->font_controls->have_posts() ) {
					$this->font_controls->the_post();

					// Add this control to the $custom_controls array
					$id                           = get_post_meta( get_the_ID(), 'control_id', true );
					$this->custom_controls[ $id ] = get_the_title();

					// Set curent control id to the first control
					if( 0 == $count ) {
						$this->current_control_id = $id;
						$this->first_control      = EGF_Posttype::get_font_control( $id );
					}

					$count++;
				}

				// Restore original Post Data
				wp_reset_postdata();

			}

			// Determine Screen via $_GET['action']
			$action = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : false;

			// Update current control id if it is passed in the URL
			if ( isset( $_GET['control'] ) ) {
				$this->current_control_id = esc_attr( $_GET['control'] );
			}

			// The control id of the current control being edited - Note this is a string representation of '0', not an integer
			$this->control_selected_id = isset( $_GET['control'] ) ? esc_attr( $_GET['control'] ) : '0';

			// Attempt to get a control instance if it exists 
			$this->control_instance = EGF_Posttype::get_font_control( $this->control_selected_id );

			// Edit and and no control but has first control
			if ( 'edit' == $action ) {
				if ( ! isset( $_GET['control'] ) && $this->first_control ) {
					$this->control_instance = $this->first_control;
				}
			}
			
			/**
			 * Initialise screen action if no action has been set
			 * in the parameter.
			 */
			if ( ! $action ) {
				if ( $this->first_control ) {
					$this->control_instance    = $this->first_control;
					$this->control_selected_id = get_post_meta( $this->control_instance->ID, 'control_id', true );
				}
			} elseif ( 'create' == $action ) {
				$this->control_selected_id = '0';
			}

		}

		/**
		 * Set Screen State
		 *
		 * Performs a set of checks/tests to determine what
		 * screen the user is currently on.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function set_screen_state() {

			$this->is_edit_screen     = false;
			$this->is_create_screen   = false;
			$this->is_manage_screen   = false;
			$this->is_advanced_screen = false;

			// Determine Screen
			if ( isset( $_GET['screen'] ) ) {
				switch ( esc_attr( $_GET['screen'] ) ) {

					case 'edit':
						$this->is_edit_screen = true;
						break;
					
					case 'manage_controls':
						$this->is_manage_screen = true;
						break;

					case 'advanced':
						$this->is_advanced_screen = true;
						break;
				}
			}

			if ( ! $this->is_manage_screen && ! $this->is_advanced_screen ) {
				// Determine Screen via $_GET['action']
				$action = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : false;

				if ( 'edit' == $action ) {
					$this->is_edit_screen   = true;
					$this->is_create_screen = false;
				}

				if ( ! $action ) {
					if ( $this->first_control ) {
						$this->is_edit_screen   = true;
						$this->is_create_screen = false;				
					} else {
						$this->is_edit_screen   = false;
						$this->is_create_screen = true;
					}
				} else {

					/**
					 * PHP Switch to determine what action to take
					 * upon screen initialisation.
					 */
					switch ( $action ) {
						case 'edit':
							// Change action if we are creating a new font control
							if ( '0' == $this->control_selected_id ) {
								$this->is_edit_screen   = false;
								$this->is_create_screen = true;
							} else {

								// Change action if the control instance doesn't exist
								if ( ! $this->control_instance ) {
									$this->is_edit_screen   = false;
									$this->is_create_screen = true;
								}
							}
							break;

						case 'create':
							$this->is_edit_screen   = false;
							$this->is_create_screen = true;
							break;
					}
				}				
			}
		}


		public function get_font_controls() {
			return $this->font_controls;
		}


		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_actions() {
		}

		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_filters() {
		}

		/**
		 * Edit Font Controls Screen Check
		 *
		 * Boolean function to check if we are currently
		 * on the Edit Font Controls Screen.
		 * 
		 * @return boolean true
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function is_edit_screen() {
			return $this->is_edit_screen;
		}

		/**
		 * Create Font Controls Screen Check
		 *
		 * Boolean function to check if we are currently
		 * on the Edit Font Controls Screen and the action
		 * is set to create control.
		 * 
		 * @return boolean true
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function is_create_screen() {
			return $this->is_create_screen;
		}

		/**
		 * Manage Font Controls Screen Check
		 *
		 * Boolean function to check if we are currently
		 * on the Manage Font Controls Screen.
		 * 
		 * @return boolean true
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function is_manage_screen() {
			return $this->is_manage_screen;
		}

		/**
		 * Advanced Screen Check
		 *
		 * Boolean function to check if we are currently
		 * on the Advanced Screen.
		 * 
		 * @return boolean true
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function is_advanced_screen() {
			return $this->is_advanced_screen;
		}

		
		/**
		 * Get Page Container Opening Markup
		 * 
		 * Gets the page container openining tag markup.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function get_page_start() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/page-start.php' );
		}

		/**
		 * Get Page Container Closing Markup
		 * 
		 * Gets the page container closing tag markup.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function get_page_end() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/page-end.php' );
		}

		/**
		 * Get Page Tabs
		 * 
		 * Gets the navigation tabs on the top of the
		 * settings page.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function get_page_tabs() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/tabs.php' );
		}

		public function get_deleted_dialog() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/dialog-deleted.php' );
		}

		public function get_updated_dialog() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/dialog-updated.php' );
		}

		public function get_manage_control_form() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/form-manage-control.php' );
		}

		public function get_create_screen() {
			$control_name        = '';
			$control_description = '';
			
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/create-screen.php' );
		}

		public function get_edit_screen() {
			$control_name        = $this->control_instance->post_title;
			$control_selectors   = get_post_meta( $this->control_instance->ID, 'control_selectors', true );
			$control_description = get_post_meta( $this->control_instance->ID, 'control_description', true );	
			
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/edit-screen.php' );
		}

		public function get_manage_screen() {
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/manage-screen.php' );
		}

		public function get_advanced_screen() {
			$api_key  = EGF_Font_Utilities::get_google_api_key();
			$validity = EGF_Font_Utilities::is_valid_google_api_key( $api_key ) ? 'valid-key' : 'invalid-key';
			include_once( plugin_dir_path( dirname(__FILE__) ) . 'views/admin-page/advanced-screen.php' );
		}


		/**
		 * Render Admin Page Output
		 * @return [type] [description]
		 */
		public function render() {
			
			// Opening markup
			$this->get_page_start();
			$this->get_page_tabs();
			

			// Load Appropriate Screen
			if ( $this->is_create_screen() ) {
				$this->get_deleted_dialog();
				$this->get_updated_dialog();
				$this->get_manage_control_form();
				$this->get_create_screen();
			}

			// Load Appropriate Screen
			if ( $this->is_edit_screen() ) {
				$this->get_deleted_dialog();
				$this->get_updated_dialog();
				$this->get_manage_control_form();
				$this->get_edit_screen();
			}

			// Load Appropriate Screen
			if ( $this->is_manage_screen() ) {
				$this->get_manage_screen();
			}

			if ( $this->is_advanced_screen() ) {
				$this->get_advanced_screen();
			}

			// Closing container markup
			$this->get_page_end();
		}
	}
endif;
