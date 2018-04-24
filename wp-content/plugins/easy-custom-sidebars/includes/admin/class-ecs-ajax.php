<?php
/**
 * Class: ECS_Ajax
 *
 * This class contains all of the admin ajax functionality
 * that is used by this plugin.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
if ( ! class_exists( 'ECS_Ajax' ) ) :
	class ECS_Ajax {
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
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function register_actions() {
			add_action( 'wp_ajax_ecs_add_sidebar_item', array( $this, 'add_sidebar_item' ) );
			add_action( 'wp_ajax_ecs_create_sidebar_instance', array( $this, 'create_sidebar_instance' ) );
			add_action( 'wp_ajax_ecs_update_sidebar_instance', array( $this, 'update_sidebar_instance' ) );
			add_action( 'wp_ajax_ecs_edit_sidebar_replacement', array( $this, 'edit_sidebar_replacement' ) );
			add_action( 'wp_ajax_ecs_delete_sidebar_instance', array( $this, 'delete_sidebar_instance' ) );
			add_action( 'wp_ajax_ecs_delete_all_sidebar_instances', array( $this, 'delete_all_sidebar_instances' ) );
			add_action( 'wp_ajax_ecs_sidebar_quick_search', array( $this, 'quick_search' ) );
			add_action( 'wp_ajax_ecs_sidebar_get_metabox', array( $this, 'get_metabox' ) );
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
		 * Add Item/Page Attachement to a Custom Sidebar - Ajax Function
		 *
		 * Checks WordPress nonce and upon successful validation
		 * creates the html markup for a new item to add to the
		 * current sidebar that is loaded.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/check_ajax_referer 		check_ajax_referer()
		 * @link 	http://codex.wordpress.org/Function_Reference/current_user_can 			current_user_can()
		 * @link 	http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link 	http://codex.wordpress.org/Function_Reference/get_post 					get_post()
		 * @link 	http://codex.wordpress.org/Function_Reference/apply_filters 			apply_filters()
		 * @link 	http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @uses  class CPS_Walker_Sidebar_Edit
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function add_sidebar_item() {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Check admin nonce for security
			check_ajax_referer( 'ecs_add_sidebar_item', 'ecs_sidebar_settings_column_nonce' );

			/**
			 * Include Nav Menu File
			 * 
			 * As this function uses some of the utility functions 
			 * contained in this file.
			 * 
			 */
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

			/**
			 * Variables to store menu output
			 * @var mixed
			 */
			$output          = '';
			$menu_items      = array();
			$menu_items_data = array();
			$item_ids        = array();
			/**
			 * Get Sidebar Menu Item Data:
			 * 
			 * Loops through the menu items that have been submitted
			 * in this request.
			 * 
			 */
			foreach ( (array) $_POST['menu-item'] as $menu_item_data ) {
				if ( ! empty( $menu_item_data['menu-item-type'] ) && ! empty( $menu_item_data['menu-item-object-id'] ) ) {
					
					// Get type of item
					$menu_item_type = $menu_item_data['menu-item-type'];
					
					// Initialise type, id and data
					switch ( $menu_item_type ) {
						
						case 'post_type':
							$item_ids[] = array( 
								'type' => 'post_type', 
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;

						case 'post_type_all':
							$item_ids[] = array( 
								'type' => 'post_type_all', 
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;

						case 'post_type_archive':
							$item_ids[] = array( 
								'type' => 'post_type_archive', 
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;
	
						case 'taxonomy':
							$item_ids[] = array( 
								'type' => 'taxonomy', 
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;

						case 'taxonomy_all':
							$item_ids[] = array( 
								'type' => 'taxonomy_all', 
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;
						
						case 'category_posts':
							$item_ids[] = array( 
								'type' => 'category_posts',
								'id'   => $menu_item_data['menu-item-object-id'],
								'data' => $menu_item_data,
							);
							break;

						case 'author_archive':
							$item_ids[] = array( 
								'type' => 'author_archive',
								'id'   => $menu_item_data['menu-item-object'],
								'data' => $menu_item_data,
							);
							break;

						case 'template_hierarchy':
							$item_ids[] = array( 
								'type' => 'template_hierarchy',
								'id'   => $menu_item_data['menu-item-object'],
								'data' => $menu_item_data,
							);
							break;
					}

					$menu_items_data[] = $menu_item_data;
				}
			}
			
			// Die if error
			if ( is_wp_error( $item_ids ) ) {
				wp_die();
			}			

			/**
			 * Generate Menu Items
			 *
			 * Determine the type of object we are working with
			 * and build an object that contains its properties.
			 * By building the object manually we avoid a trip
			 * to the database which results in a huge performance
			 * boost and a minimal database footprint.
			 * 
			 */
			foreach ( (array) $item_ids as $menu_item_id ) {

				switch ( $menu_item_id['type'] ) {
					
					case 'post_type':
						$current_post  = get_post( $menu_item_id['id'] );
						$post_type_obj = get_post_type_object( $current_post->post_type );

						
						// Manually build object (huge performance boost)
						if ( $post_type_obj ) {
							$menu_item                   = new stdClass();
							$menu_item                   = $current_post;
							$menu_item->ID               = $current_post->ID;
							$menu_item->db_id            = $current_post->ID;
							$menu_item->object           = $post_type_obj->name;
							$menu_item->type_label       = $post_type_obj->labels->singular_name;
							$menu_item->object_id        = $current_post->ID;
							$menu_item->menu_item_parent = 0;
							$menu_item->type             = 'post_type';
							$menu_item->post_title       = $current_post->post_title;
							$menu_item->label            = $current_post->post_title;
							$menu_item->url              = get_permalink( $current_post->ID );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;

					case 'post_type_all':
						$post_type_obj = get_post_type_object( $menu_item_id['data']['menu-item-object'] );
	
						// Manually build object (huge performance boost)
						if ( $post_type_obj ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
							$menu_item->object           = $post_type_obj->name;
							$menu_item->type_label       = $post_type_obj->labels->name;
							$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
							$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
							$menu_item->menu_order       = 0;
							$menu_item->type             = 'post_type_all';
							$menu_item->title            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
							$menu_item->label            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
							$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => $post_type_obj->name ), admin_url( 'edit.php' ) ) );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;

					case 'post_type_archive':
						$post_type_obj = get_post_type_object( $menu_item_id['data']['menu-item-object'] );

						// Manually build object (huge performance boost)
						if ( $post_type_obj ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
							$menu_item->object           = $post_type_obj->name;
							$menu_item->type_label       = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->name );
							$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
							$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
							$menu_item->menu_order       = 0;
							$menu_item->type             = 'post_type_archive';
							$menu_item->title            = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->singular_name );
							$menu_item->label            = sprintf( __( '%s Archive', 'easy-custom-sidebars' ), $post_type_obj->labels->singular_name );
							$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => $post_type_obj->name ), admin_url( 'edit.php' ) ) );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;	

					case 'taxonomy':
						$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );
						$tax_term = get_term_by( 'name', esc_html( $menu_item_id['data']['menu-item-title'] ), $tax_obj->name );

						if ( $tax_obj && $tax_term ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['id'];
							$menu_item->object           = $tax_obj->name;
							$menu_item->type_label       = $tax_obj->labels->singular_name;
							$menu_item->object_id        = $menu_item_id['id'];
							$menu_item->menu_order       = 0;
							$menu_item->menu_item_parent = 0;
							$menu_item->type             = 'taxonomy';
							$menu_item->title            = $tax_term->name;
							$menu_item->label            = $tax_term->name;
							$menu_item->url              = get_term_link( (int) $menu_item_id['id'], $tax_obj->name );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;

					case 'taxonomy_all':

						$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );

						if ( $tax_obj ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['id'];
							$menu_item->object           = $tax_obj->name;
							$menu_item->type_label       = $tax_obj->labels->name;
							$menu_item->object_id        = $menu_item_id['data']['menu-item-object-id'];
							$menu_item->menu_order       = 0;
							$menu_item->menu_item_parent = $menu_item_id['data']['menu-item-object'];
							$menu_item->type             = 'taxonomy_all';
							$menu_item->title            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $tax_obj->labels->name );
							$menu_item->label            = sprintf( __( 'All %s', 'easy-custom-sidebars' ), $tax_obj->labels->name );
							$menu_item->url              = esc_url( add_query_arg( array( 'taxonomy' => $tax_obj->name ), admin_url( 'edit-tags.php' ) ) );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;							
						}

						break;

					case 'category_posts':

						$tax_obj  = get_taxonomy( $menu_item_id['data']['menu-item-object'] );
						$tax_term = get_term_by( 'name', esc_attr( $menu_item_id['data']['menu-item-title'] ), $tax_obj->name );
						$category = get_category( $menu_item_id['data']['menu-item-object-id'] );

						if ( $tax_obj && $tax_term && $category ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['id'];
							$menu_item->object           = $tax_obj->name;
							$menu_item->type_label       = $tax_obj->labels->singular_name;
							$menu_item->object_id        = $menu_item_id['id'];
							$menu_item->menu_order       = 0;
							$menu_item->menu_item_parent = 0;
							$menu_item->type             = 'category_posts';
							$menu_item->title            = get_term_field( 'name', $menu_item_id['data']['menu-item-object-id'], $menu_item_id['data']['menu-item-object'], 'raw' );
							$menu_item->label            = get_term_field( 'name', $menu_item_id['data']['menu-item-object-id'], $menu_item_id['data']['menu-item-object'], 'raw' );
							$menu_item->url              = esc_url( add_query_arg( array( 'category_name' => $category->slug ), admin_url( 'edit.php' ) ) );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;							
						}
						break;

					case 'author_archive':
						$user = get_user_by( 'id', $menu_item_id['id'] );

						if ( $user ) {
							$menu_item                   = new stdClass();
							$menu_item->ID               = $menu_item_id['id'];
							$menu_item->db_id            = $menu_item_id['id'];
							$menu_item->object           = 'user';
							$menu_item->type_label       = __( 'Author Archive', 'easy-custom-sidebars' );
							$menu_item->object_id        = $menu_item_id['id'];
							$menu_item->menu_order       = 0;
							$menu_item->menu_item_parent = 0;
							$menu_item->type             = 'author_archive';
							$menu_item->title            = $user->display_name;
							$menu_item->label            = $user->display_name;
							$menu_item->url              = esc_url( get_author_posts_url( $menu_item_id['id'] ) );
							$menu_item->post_status      = 'draft';

							// Add to menu items
							$menu_items[]    = $menu_item;
						}
						break;

					case 'template_hierarchy':
						$menu_item                   = new stdClass();
						$menu_item->ID               = $menu_item_id['data']['menu-item-db-id'];
						$menu_item->db_id            = $menu_item_id['data']['menu-item-db-id'];
						$menu_item->object           = $menu_item_id['data']['menu-item-object'];
						$menu_item->type_label       = __( 'Template', 'easy-custom-sidebars' );
						$menu_item->object_id        = $menu_item_id['id'];
						$menu_item->menu_order       = 0;
						$menu_item->menu_item_parent = 0;
						$menu_item->type             = 'template_hierarchy';
						$menu_item->title            = $menu_item_id['data']['menu-item-title'];
						$menu_item->label            = $menu_item_id['data']['menu-item-title'];
						$menu_item->url              = esc_url( add_query_arg( array( 'post_type' => 'page' ), admin_url( 'edit.php' ) ) );
						$menu_item->post_status      = 'draft';

						// Add to menu items
						$menu_items[]    = $menu_item;
						break;


				}
			}

			// Define Walker
			$walker_class_name = apply_filters( 'ecs_edit_sidebar_walker', 'ECS_Walker_Edit', $menu_items );

			// Die if walker doesn't exist
			if ( ! class_exists( $walker_class_name ) ) {
				wp_die();
			}


			if ( ! empty( $menu_items ) ) {

				$args = array(
					'after'       => '',
					'before'      => '',
					'link_after'  => '',
					'link_before' => '',
					'pending'     => true,
					'walker'      => new $walker_class_name,
				);

				$output .= walk_nav_menu_tree( $menu_items, 0, (object) $args );
				echo $output;
			}

			// Return to client
			wp_die();
		}

		/**
		 * Create Sidebar Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * creates a new sidebar instance. This function then 
		 * constructs a new ajax response and sends it back to the
		 * client.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/check_ajax_referer 		check_ajax_referer()
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_meta 			get_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/WP_Ajax_Response 		WP_Ajax_Response
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function create_sidebar_instance() {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Check admin nonce for security
			check_ajax_referer( 'ecs_edit_sidebar_instance', 'ecs_edit_sidebar_instance_nonce' );

			// Refresh transients, to be implemented in future versions
			do_action( 'ecs-trigger-transient-refresh' );		

			// Get sidebar name
			if( isset( $_POST['sidebar_name'] ) ) {
				$sidebar_name =  esc_attr( $_POST['sidebar_name'] );
			} else {
				$sidebar_name = __( 'Custom Sidebar', 'easy-custom-sidebars' );
			}

			// Get sidebar data structure
			$data = ECS_Posttype::get_instance();

			// Create the new sidebar and get the associated ID
			$new_sidebar    = $data->update_sidebar_instance( '0', '0', $sidebar_name );
			$new_sidebar_id = get_post_meta( $new_sidebar, 'sidebar_id', true );

			// Create array to hold additional xml data
			$supplimental_data = array(
				'new_sidebar_id'     => $new_sidebar_id
			);

			$response = array(
				'what'         => 'new_sidebar',
				'id'           => 1,
				'data'         => '',
				'supplemental' => $supplimental_data
			);

			// Create a new WP_Ajax_Response obj and send the response
			$x = new WP_Ajax_Response( $response );
			$x->send();

			// Kill function and return to client
			wp_die();
		}

		/**
		 * Save/Update Sidebar Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * updates an existing sidebar instance. This function 
		 * then constructs a new ajax response and sends it back 
		 * to the client.
		 *
		 * @uses ECS_Posttype->update_sidebar_instance()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */		
		public function update_sidebar_instance() {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Check admin nonce for security
			check_ajax_referer( 'ecs_edit_sidebar_instance', 'ecs_edit_sidebar_instance_nonce' );

			// Refresh transients
			do_action( 'ecs-trigger-transient-refresh' );

			// Get sidebar attributes	
			$sidebar_id      = isset( $_POST[ 'sidebarId' ] )     ? (string) esc_attr( $_POST[ 'sidebarId' ] )     : (string) '0';
			$replacement_id  = isset( $_POST[ 'replacementId' ] ) ? (string) esc_attr( $_POST[ 'replacementId' ] ) : (string) '0';
			$sidebar_name    = isset( $_POST[ 'sidebarName' ] )   ? (string) esc_attr( $_POST[ 'sidebarName' ] )   : __( 'Custom Sidebar', 'easy-custom-sidebar' );
			$description     = isset( $_POST[ 'description' ] )   ? (string) esc_attr( $_POST[ 'description' ] )   : '';
			$attachment_data = array();

			// Check for sidebar attachements
			if ( isset( $_POST[ 'sidebar-items' ] ) ) {

				// Build the sidebar attachment data array
				foreach ( (array) $_POST[ 'sidebar-items' ] as $sidebar_item_data ) {

					// Array index position should have been set on the admin screen
					$attachment_data[] = $sidebar_item_data;
				}
			}

			$data = ECS_Posttype::get_instance();
			
			// Update sidebar or create a new one if it doesn't exist
			$sidebar = $data->update_sidebar_instance( 
				$sidebar_id, 
				$replacement_id, 
				$sidebar_name, 
				$description, 
				$attachment_data 
			);

			// Create array to hold additional xml data
			$supplimental_data = array(
				'sidebar_name'     => get_the_title( $sidebar )
			);

			$data = array(
				'what'         => 'sidebar',
				'id'           => 1,
				'data'         => '',
				'supplemental' => $supplimental_data
			);

			// Create a new WP_Ajax_Response obj and send the request
			$x = new WP_Ajax_Response( $data );
			$x->send();

			// Kill function and return to client
			wp_die();
		}
		
		/**
		 * Delete Sidebar Instance - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * it deletes the custom sidebar instance from the database.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/check_ajax_referer 		check_ajax_referer()
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */	
		public function delete_sidebar_instance() {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Check admin nonce for security
			check_ajax_referer( 'ecs_delete_sidebar_instance', 'ecs_delete_sidebar_instance_nonce' );	

			// Get posttype data structure
			$data = ECS_Posttype::get_instance();

			if ( isset( $_POST['sidebarId'] ) ) {
				// Refresh transients
				do_action( 'ecs-trigger-transient-refresh' );
				$data->delete_sidebar_instance( esc_attr( $_POST['sidebarId'] ) );
			}

			// Kill function and return to client
			wp_die();
		}

		/**
		 * Delete All Sidebar Instances - Ajax Function
		 * 
		 * Checks WordPress nonce and upon successful validation
		 * it deletes all custom sidebar instances from the database.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/check_ajax_referer 		check_ajax_referer()
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */	
		public function delete_all_sidebar_instances() {

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Check admin nonce for security
			check_ajax_referer( 'ecs_delete_sidebar_instance', 'ecs_delete_sidebar_instance_nonce' );

			// Get posttype data structure
			$data = ECS_Posttype::get_instance();

			// Refresh transients and delete all sidebars
			do_action( 'ecs-trigger-transient-refresh' );
			$data->delete_all_sidebar_instances();

			// Kill function and return to client
			wp_die();
		}

		/**
		 * Edit Sidebar Replacement - Ajax Function
		 * 
		 * Provides a quick way to only change the sidebar
		 * replacement of a custom sidebar on the Manage
		 * Sidebar Replacements Screen.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/check_ajax_referer 		check_ajax_referer()
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/update_post_meta 		update_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @uses master_get_sidebar_instance() defined in includes/theme-sidebar-functions.php
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function edit_sidebar_replacement() {
			
			// Check admin nonce for security
			check_ajax_referer( 'ecs_edit_sidebar_instance', 'ecs_edit_sidebar_instance_nonce' );

			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Update sidebar replacement id
			if( isset( $_POST['sidebarId'] ) && isset( $_POST['replacementId'] ) ) {
				
				// Get data structure
				$data = ECS_Posttype::get_instance();

				// Change sidebar replacement if the sidebar exists
				$sidebar_instance = $data->get_sidebar_instance( esc_attr( $_POST['sidebarId'] ) );
				$replacement_id   = esc_attr( $_POST['replacementId'] );

				if ( $sidebar_instance ) {
					update_post_meta( $sidebar_instance->ID, 'sidebar_replacement_id', $replacement_id );
				}
			}

			// Kill function and return to client
			wp_die();
		}

		/**
		 * Quick Search 
		 *
		 * AJAX function that performs a search query based on
		 * the user input that has been posted and returns a 
		 * search results response.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @uses ECS_Admin::quick_search()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function quick_search() {
		
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Get admin utility object
			$admin_obj = ECS_Admin::get_instance();

			// Generate Quick Search Output
			$admin_obj->quick_search( $_POST );

			// Die 
			wp_die();
		}

		/**
		 * AJAX Metabox Pagination
		 *
		 * Gets metabox information passed via AJAX and generates
		 * the approriate metabox markup to replace on the clients
		 * browser. Allows the user to paginate through each metabox
		 * without refreshing the page. This function echos back the
		 * html markup to the client admin page.
		 * 
		 * @link http://codex.wordpress.org/Function_Reference/current_user_can 		current_user_can()
		 * @link http://codex.wordpress.org/Function_Reference/wp_die 					wp_die()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_types 			get_post_types()
		 * @link http://codex.wordpress.org/Function_Reference/get_taxonomies 			get_taxonomies()
		 * @link http://codex.wordpress.org/Function_Reference/add_action 				add_action()
		 *
		 * @uses post_type_meta_box_output()
		 * @uses taxonomy_meta_box_output()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_metabox() {
			
			// Make sure user has the required access level
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			// Get admin utility object
			$admin_obj = ECS_Admin::get_instance();
			
			$type      = '';
			$callback  = '';

			// Determine the type of content requested
			if ( isset( $_POST['item-type'] ) ) {
				switch ( esc_attr( $_POST['item-type'] ) ) {

					// Handle Posttype Case
					case 'post_type':	
						$type     = 'posttype';
						$callback = array( $admin_obj, 'render_post_type_meta_box' );
						$items    = (array) get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
						break;

					// Handle Taxonomy Case
					case 'taxonomy':
						$type     = 'taxonomy';
						$method   = ( isset( $_POST['custom-item-type'] ) && 'category_posts' == $_POST['custom-item-type'] ) ? 'render_category_post_meta_box' : 'render_taxonomy_meta_box';
						$callback = array( $admin_obj, $method );
						$items    = (array) get_taxonomies( array( 'show_ui' => true ), 'object' );
						break;

					// Handle Author Case
					case 'author_archive':
						$type     = 'author_archive';
						$callback = array( $admin_obj, 'render_author_meta_box' );
						
						// Get markup from output buffer
						ob_start();
						call_user_func_array( $callback, array( null, array( 'callback' => $callback ) ) );
						$markup = ob_get_clean();

						// Return JSON data
						echo json_encode( array(
							'replace-id' => 'master-author-archive',
							'markup'     => $markup,
						) );
						
						break;
				}
			}

			// Determine replacement id and output JSON
			if ( ! empty( $_POST['item-object'] ) && isset( $items[ $_POST['item-object'] ] ) ) {

				$item = apply_filters( 'ecs_meta_box_object', $items[ $_POST['item-object'] ] );
				
				// Get markup from output buffer
				ob_start();
				call_user_func_array( $callback, array(
					null,
					array(
						'id'       => 'master-add-' . $item->name,
						'title'    => $item->labels->name,
						'callback' => $callback,
						'args'     => $item,
					)
				));
				$markup = ob_get_clean();

				// Generate Replacement ID
				$replace_id = $type . '-' . $item->name;

				// Add suffix if custom posts in category metabox
				if ( isset( $_POST['custom-item-type'] ) ) {
					if ( 'category_posts' == $_POST['custom-item-type'] ) {
						$replace_id .= '-custom-category';
					}
				}

				// Return JSON data
				echo json_encode( array(
					'replace-id' => $replace_id,
					'markup'     => $markup,
				) );
			}	

			// Die
			wp_die();
		}
	}
endif;
