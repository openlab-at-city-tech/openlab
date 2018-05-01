<?php
/**
 * Class: ECS_Admin_Controller
 *
 * This controller class is used to build the admin page
 * output. It includes the necessary views contained in 
 * the views/admin-page directory.
 *
 * Developer Note: Support has been added for an advanced
 * tab should the need arise in the future. To enable the
 * advanced tab:
 * 
 *     - Uncomment the tab in views\admin-page\tabs.php
 *     - Add your required content to the following file:
 *       views\admin-page\advanced-screen.php
 *       
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
if ( ! class_exists( 'ECS_Admin_Controller' ) ) :
	class ECS_Admin_Controller {
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.0
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.0
		 *
		 */
		protected $plugin_screen_hook_suffix = 'easy-custom-sidebars';

		/**
		 * Url Variables used on the admin page.
		 * 
		 * @var      string
		 * @since    1.0
		 *
		 */
		protected $admin_url;
		protected $create_url;
		protected $manage_url;
		protected $advanced_url;

		/**
		 * Variables to keep track of current screen state.
		 *
		 * @var boolean
		 * @since  1.0
		 * 
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
		 * @since  1.0
		 */
		protected $sidebars;
		protected $custom_sidebars;
		protected $first_sidebar;
		protected $sidebar_instance;
		protected $no_sidebars;
		protected $current_sidebar_id;
		protected $sidebar_selected_id;

		/**
		 * Constructor Function
		 * 
		 * Initialize the class and register all
		 * actions and filters.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		function __construct() {

			$this->plugin_slug = 'easy-custom-sidebars';

			// Bail if the current user doesn't 
			// have the admin capabilities.
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return;
			}
			
			// Setup class variables
			$this->set_urls();
			$this->set_sidebar_instances();
			$this->set_screen_state();
			$this->register_actions();		
			$this->register_filters();
		}

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
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
		 * Set Up URL Variables
		 *
		 * Declares and sets up all of the variables that are 
		 * necessary to display the correct options screen on 
		 * the admin page.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/esc_url 				esc_url()
		 * @link http://codex.wordpress.org/Function_Reference/add_query_arg 		add_query_arg()
		 *
		 * @since 1.0.1
		 * @version  1.0
		 * 
		 */
		private function set_urls() {
			$this->admin_url    = esc_url( add_query_arg( array( 'page' => $this->plugin_slug ), admin_url( 'themes.php' ) ) );
			$this->manage_url   = esc_url( add_query_arg( array( 'screen' => 'manage_sidebars' ), $this->admin_url ) );
			$this->advanced_url = esc_url( add_query_arg( array( 'screen' => 'advanced' ), $this->admin_url ) );
			$this->create_url   = esc_url( add_query_arg( array( 'action' => 'create' ), $this->admin_url ) );
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function register_actions() {
		}
		
		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
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
		 * @since 1.0.1
		 * @version 1.0.9
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
		 * @since 1.0.1
		 * @version 1.0.9
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
		 * @since 1.0.1
		 * @version 1.0.9
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
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function is_advanced_screen() {
			return $this->is_advanced_screen;
		}

		/**
		 * Render Admin Page Output
		 *
		 * Loads all of the admin page views and outputs the
		 * admin page.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function render() {
			$this->get_page_start();
			$this->get_page_tabs();

			if ( $this->is_create_screen() ) {
				$this->get_deleted_dialog();
				$this->get_updated_dialog();
				$this->get_manage_control_form();
				$this->get_create_screen();
			}


			if ( $this->is_edit_screen() ) {
				$this->get_deleted_dialog();
				$this->get_updated_dialog();
				$this->get_manage_control_form();
				$this->get_edit_screen();
			}

			if ( $this->is_manage_screen() ) {
				$this->get_manage_screen();
			}

			if ( $this->is_advanced_screen() ) {
				$this->get_advanced_screen();
			}

			$this->get_nonces();
			$this->get_page_end();

			// print_r(get_current_screen());
		}

		/**
		 * Set Screen State
		 *
		 * Performs a set of checks/tests to determine what
		 * screen the user is currently on.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
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
					
					case 'manage_sidebars':
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
					if ( $this->first_sidebar ) {
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
							if ( '0' == $this->sidebar_selected_id ) {
								$this->is_edit_screen   = false;
								$this->is_create_screen = true;
							} else {

								// Change action if the control instance doesn't exist
								if ( ! $this->sidebar_instance ) {
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

		/**
		 * Set Custom Sidebar Variables
		 *
		 * Sets the custom sidebar variables that are used throughout the
		 * admin settings page.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function set_sidebar_instances() {

			$data = ECS_Posttype::get_instance();

			$this->sidebars        = $data->get_all_sidebar_instances();
			$this->custom_sidebars = array();

			// Set no sidebars flag
			$this->no_sidebars = $this->sidebars ? false : true;

			if ( ! $this->no_sidebars ) {
				$count = 0;

				while ( $this->sidebars->have_posts() ) {
					$this->sidebars->the_post();

					// Add this sidebar to the $custom_sidebars array
					$id                           = get_post_meta( get_the_ID(), 'sidebar_id', true );
					$this->custom_sidebars[ $id ] = get_the_title();

					// Set current sidebar id to the first sidebar instance
					if ( 0 == $count ) {
						$this->current_sidebar_id = $id;
						$this->first_sidebar      = $data->get_sidebar_instance( $id );
					}

					$count++;
				}
				
				// Restore original postdata
				wp_reset_postdata();
			}

			// Determine screen via $_GET['action']
			$action = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : false;

			// Update the current sidebar id if it is passed in the url
			if ( isset( $_GET['sidebar'] ) ) {
				$this->current_sidebar_id = esc_attr( $_GET['sidebar'] );
			}

			/**
			 * The sidebar id of the current sidebar being edited
			 * Note: this is a string representation of '0', not an integer.
			 */ 
			$this->sidebar_selected_id = isset( $_GET['sidebar'] ) ? esc_attr( $_GET['sidebar'] ) : '0';

			// Attempts to get a sidebar if it exists
			$this->sidebar_instance = $data->get_sidebar_instance( $this->sidebar_selected_id );

			/**
			 * Roll back to first available sidebar if 
			 * edit action but no sidebar id passed in the URL.
			 */
			if ( 'edit' == $action ) {
				if ( ! isset( $_GET['sidebar'] ) && $this->first_sidebar ) {
					$this->sidebar_instance = $this->first_sidebar;
				}
			}

			/**
			 * Initialise screen action if no action has been set
			 * in the parameter.
			 */
			if ( ! $action ) {
				if ( $this->first_sidebar ) {
					$this->sidebar_instance    = $this->first_sidebar;
					$this->sidebar_selected_id = get_post_meta( $this->sidebar_instance->ID, 'sidebar_id', true );
				} elseif ( 'create' == $action ) {
					$this->sidebar_selected_id = '0';
				}
			}
		}

		/**
		 * Output Sidebar Accordion
		 * 
		 * @return [type] [description]
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function do_accordion_sections() {
			global $wp_meta_boxes;
			
			$screen  = get_current_screen();
			$page    = $screen->id;
			$context = 'side';
			$object  = null;
			$hidden  = get_hidden_meta_boxes( $screen );
			?>
			<div id="side-sortables" class="accordion-container">
				<ul class="outer-border">
					<?php
						$i = 0;
						do {
							if ( ! isset( $wp_meta_boxes ) || ! isset( $wp_meta_boxes[$page] ) || ! isset( $wp_meta_boxes[$page][$context] ) ) {
								break;
							}
							
							foreach ( array( 'high', 'sorted', 'core', 'default', 'low' ) as $priority ) {
								if ( isset( $wp_meta_boxes[$page][$context][$priority] ) ) {
									foreach ( $wp_meta_boxes[$page][$context][$priority] as $box ) {
										$i++;
										$hidden_class = in_array( $box['id'], $hidden ) ? 'hidden' : 'visible';
										?>
										<li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
											<h3 class="accordion-section-title hndle" tabindex="0" title="<?php echo esc_attr( $box['title'] ); ?>"><?php echo esc_html( $box['title'] ); ?></h3>
											<div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
												<div class="inside">
													<?php call_user_func( $box['callback'], $object, $box ); ?>
												</div><!-- .inside -->
											</div><!-- .accordion-section-content -->
										</li><!-- .accordion-section -->
										<?php
									}
								}
							}
						} while (0);
					?>
				</ul><!-- .outer-border -->
			</div><!-- .accordion-container -->
			<?php

			return $i;
		}

		/**
		 * Get Create Screen View
		 * 
		 * Gets the view containing the markup for the
		 * create screen.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_create_screen() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/create-screen.php' );
		}

		/**
		 * Get Manage Screen View
		 * 
		 * Gets the view containing the markup for the
		 * manage screen. The manage screen displays 
		 * all of the current custom sidebars.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_manage_screen() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/manage-screen.php' );
		}

		/**
		 * Get Nonces
		 * 
		 * Gets the view containing all of the security
		 * nonces used by the admin screens. This is to
		 * ensure that any requests made to the server
		 * originated from the user.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_nonces() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/nonces.php' );
		}

		/**
		 * Get Slider Options
		 *
		 * Retrieves both static and dynamic options 
		 * that are available for this custom sidebar.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_slider_options() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/page-slider-options.php' );
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/page-slider-template-options.php' );
		}

		/**
		 * Get Manage Control Form
		 * 
		 * Gets the view containing the manage control form.
		 * Provides an easy way to change the slider you wish
		 * to modify on the edit screen.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_manage_control_form() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/manage-control-form.php' );
		}

		/**
		 * Get Edit Screen View
		 * 
		 * Gets the view containing the markup for the
		 * edit screen.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_edit_screen() {
			// Init variables
			$controller           = ECS_Widget_Areas::get_instance();
			$default_widget_areas = $controller->get_ordered_custom_widget_areas( 'name', $controller->get_default_widget_areas() );
			$replacement_id       = get_post_meta( $this->sidebar_instance->ID, 'sidebar_replacement_id', true );
			$sidebar_description  = get_post_meta( $this->sidebar_instance->ID, 'sidebar_description', true );

			// Get edit screen
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/edit-screen.php' );
		}

		/**
		 * Get Page Container Opening Markup
		 * 
		 * Gets the page container openining tag markup.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_page_start() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/page-start.php' );
		}

		/**
		 * Get Page Container Closing Markup
		 * 
		 * Gets the page container closing tag markup.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_page_end() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/page-end.php' );
		}

		/**
		 * Get Page Tabs
		 * 
		 * Gets the navigation tabs on the top of the
		 * settings page.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_page_tabs() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/tabs.php' );
		}
		
		/**
		 * Get Deleted Dialog
		 *
		 * Gets the deleted dialog message and displays
		 * it to the user if a custom sidebar has been
		 * deleted.
		 * 
		 * @return [type] [description]
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_deleted_dialog() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/dialog-deleted.php' );
		}
		
		/**
		 * Get Updated Dialog
		 * 
		 * Gets the updated dialog message and displays
		 * it to the user if a custom sidebar has been
		 * updated.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_updated_dialog() {
			include_once( Easy_Custom_Sidebars::get_views_path() . '/admin-page/dialog-updated.php' );
		}
	}
endif;
