<?php
/**
 * Class: ECS_Posttype
 *
 * This file initialises the admin functionality for this plugin.
 * It initalises a posttype that acts as a data structure for
 * the custom widget areas. It also has useful static helper 
 * functions each custom sidebar. 
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
if ( ! class_exists( 'ECS_Posttype' ) ) :
	class ECS_Posttype {
		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 * 
		 * @var      string
		 * @since 	 1.0
		 */
		const VERSION = '1.0';

		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.0
		 *
		 */
		protected static $instance = null;

		/**
		 * Translation handle
		 * 
		 * @var      string
		 * @since    1.0
		 *
		 */
		public $plugin_slug = 'easy-custom-sidebars';

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
		 * Return the plugin slug.
		 *
		 * @return    Plugin slug variable.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
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
			add_action( 'init', array( $this, 'register_custom_post_type' ) );
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
		 * Register Custom Sidebar Posttype
		 * 
		 * Register the sidebar posttype in the same fashion that
		 * WordPress registers nav-menus internally. This will be used
		 * to store any sidebar instances. Created when the 'init' action
		 * is fired.
		 *
		 * Custom Filters:
		 *     - ecs_posttype_name
		 *     - ecs_posttype_singular_name
		 *
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/register_post_type 	register_post_type()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function register_custom_post_type() {
			register_post_type( 'sidebar_instance', array(
				'labels' => array(
					'name'          => apply_filters( 'ecs_posttype_name', __( 'Custom Sidebar Instances', 'easy-custom-sidebars' ) ),
					'singular_name' => apply_filters( 'ecs_posttype_singular_name', __( 'Custom Sidebar Instance',  'easy-custom-sidebars' ) ),
				),
				'public'           => false,
				'hierarchical'     => false,
				'rewrite'          => false,
				'delete_with_user' => false,
				'query_var'        => false,
			) );
		}

		/**
		 * Get Sidebar Instance
		 *
		 * Takes the sidebar id as a parameter and returns the
		 * post object if it's 'sidebar_id' meta value matches 
		 * the sidebar id passed in the parameter. Returns false
		 * if no matches have been found.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/have_posts           have_posts()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_post             get_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID           get_the_ID()
		 * 
		 * @param  string $sidebar_id The ID of the sidebar we wish to check
		 * @return post object if found otherwise false
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_sidebar_instance( $sidebar_id ) {
			$params = array(
				'post_type'  => 'sidebar_instance',
				'meta_key'   => 'sidebar_id',
				'meta_value' => $sidebar_id,
			);
			$query = new WP_Query( $params );

			if( $query->have_posts() ) {
				$query->the_post();
				return get_post( get_the_ID() );
			} else {
				return false;
			}			
		}

		/**
		 * Get All Sidebar Instance Posts
		 *
		 * Returns all of the sidebar-instance posttypes objects
		 * in alphabetical order by default. This function will return 
		 * false if there are no 'sidebar_instance' posts in the 
		 * database.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/have_posts           have_posts()
		 * 
		 * @return array $query if post exists and 
		 *         boolean if there are no posts.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_all_sidebar_instances( $orderby = 'title', $order = 'ASC' ) {
			$params = array(
				'post_type'      => 'sidebar_instance',
				'posts_per_page' => -1,
				'orderby'        => $orderby,
				'order'          => $order,
			);
			
			$query = new WP_Query( $params );

			if( $query->have_posts() ) {
				return $query;
			} else {
				return false;
			}
		}

		/**
		 * Sidebar Instance Name Exists
		 *
		 * Takes the sidebar name to check and the sidebar_id to 
		 * exclude and returns true if there are any other sidebar
		 * instances that have this name. (Boolean Function)
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/have_posts           have_posts()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID           get_the_ID()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_title        get_the_title()
		 * 
		 * @param  string $sidebar_name           The sidebar name we wish to check
		 * @param  string $sidebar_exclusion_id   The sidebar id to exclude in the search
		 * @return boolean - true if there is another sidebar instance that has $sidebar_name
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function sidebar_name_exists( $sidebar_name, $sidebar_exclusion_id ) {
			$sidebar_name_exists = false;

			$params = array(
				'post_type'      => 'sidebar_instance',
				'posts_per_page' => -1,
			);
			$query = new WP_Query( $params );

			// Check if the sidebar name exists
			while ( $query->have_posts() ) {

				$query->the_post();
				$sidebar_id = get_post_meta( get_the_ID(), 'sidebar_id', true );

				if ( $sidebar_id ) {
					if ( $sidebar_id != $sidebar_exclusion_id ) {
						if ( $sidebar_name == get_the_title() ) {
							$sidebar_name_exists = true;
						}
					}
				}
			}

			// Reset postdata
			wp_reset_postdata();

			return $sidebar_name_exists;
		}

		/**
		 * Add Custom Sidebar Instance
		 * 
		 * Create a post for the 'sidebar_instance' posttype which 
		 * will use the custom post meta WordPress functionality to store 
		 * all of the necessary attributes for each custom sidebar. 
		 * Note: The sidebar_id is different to the actual post id for each 
		 * sidebar instance.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/wp_insert_post 		wp_insert_post()
		 * @link http://codex.wordpress.org/Function_Reference/update_post_meta 	update_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_meta        get_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID           get_the_ID()
		 *
		 * @uses global $wp_registered_sidebars
		 * 
		 * @param  string $post_title     The name for this custom sidebar item.
		 * @param  string $replacement_id The ID for the default theme sidebar we wish to replace.
		 * @param  string $description    The description text for this custom sidebar.
		 * 
		 * @return $post  The ID of the post if the post is successfully added to the database or 0 on failure.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 *
		 */
		public function add_sidebar_instance( $post_title, $replacement_id, $description = '', $sidebar_attachment_data = array() ) {
			global $wp_registered_sidebars;

			// Generate ID and make sure its unique
			$sidebar_count  = rand( 1, 100 );
			$sidebar_id     = 'custom-sidebar-' . $sidebar_count;

			// Generate an array of existing sidebar ids and names
			$existing_sidebar_ids   = array();
			$existing_sidebar_names = array();
			$sidebar_id_exists      = true;
			$sidebar_name_exists    = true;
			
			$params = array(
				'post_type'      => 'sidebar_instance',
				'posts_per_page' => -1
			);
			$query = new WP_Query( $params );

			while( $query->have_posts() ) {
				$query->the_post();
				$existing_sidebar_ids[]   = get_post_meta( get_the_ID(), 'sidebar_id', true );
				$existing_sidebar_names[] = get_the_title();
			}
			
			// Make sure the ID doesn't already exist
			while ( $sidebar_id_exists ) {
				if ( in_array( $sidebar_id, $existing_sidebar_ids ) ) {
					$sidebar_count++;
					$sidebar_id = "custom-sidebar-{$sidebar_count}";
				} else {
					$sidebar_id_exists = false;
				}
			}

			// Strip any unallowed characters from the post title
			$post_title = str_replace( array( '#', "'", '"', '&' ), '', $post_title	);

			// Give the post a title if it is an empty string
			if ( '' == $post_title ) {
				$post_title = __( 'Sidebar', 'easy-custom-sidebars' );
			}

			// Make sure the name doesn't already exist
			$name_count    = 1;
			$original_name = $post_title; 	

			while ( $sidebar_name_exists ) {
				if ( in_array( $post_title, $existing_sidebar_names ) ) {
					$name_count++;
					$post_title = "{$original_name} {$name_count}";
				} else {
					$sidebar_name_exists = false;
				}		
			}

			$postarr = array(
				'post_type'   => 'sidebar_instance',
				'post_title'  => $post_title,
				'post_status' => 'publish' 
			); 
			$post = wp_insert_post( $postarr );

			// Update the post meta to hold the custom sidebar properties
			update_post_meta( $post, 'sidebar_id', 	$sidebar_id );
			update_post_meta( $post, 'sidebar_replacement_id', $replacement_id );
			update_post_meta( $post, 'sidebar_description', sanitize_text_field( $description ) );
			update_post_meta( $post, 'sidebar_attachments', $sidebar_attachment_data );

			return $post;
		}

		/**
		 * Update Sidebar Instance
		 *
		 * Updates an existing sidebar instance with the values 
		 * passed into the parameter. If a sidebar instance is
		 * not found a new sidebar instance would be created.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/wp_insert_post 		wp_insert_post()
		 * @link http://codex.wordpress.org/Function_Reference/update_post_meta 	update_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_meta        get_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID           get_the_ID()
		 * 
		 * @param  string $sidebar_id     The ID for the sidebar we wish to update. Note: This is NOT the post id but the sidebar_id meta value.
		 * @param  string $replacement_id The ID for the default theme sidebar we wish to replace.
		 * @param  string $post_title     The name for this custom sidebar item.
		 * @param  string $description    The description text for this custom sidebar.
		 * 
		 * @return string $post_id The post ID of the updated/created post.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function update_sidebar_instance( $sidebar_id, $replacement_id, $post_title, $description = '', $sidebar_attachment_data = array() ) {
			$params = array(
				'post_type'  => 'sidebar_instance',
				'meta_key'   => 'sidebar_id',
				'meta_value' => $sidebar_id
			);

			$query = new WP_Query( $params );

			// Strip any unallowed characters from the post title
			$post_title = str_replace( array( '#', "'", '"', '&' ), '', $post_title	);

			// Give the post a title if it is an empty string
			if ( '' == $post_title ) {
				$post_title = __( 'Sidebar', 'easy-custom-sidebars' );
			}

			if( $query->found_posts > 0 ) {
				$query->the_post();
				$post_id = get_the_ID();

				// Make sure no other sidebar has the same name
				if ( $this->sidebar_name_exists( $post_title, $sidebar_id ) ) {
					
					$sidebar_name_exists = true;
					$name_count          = 1;
					$original_name       = $post_title;

					while ( $sidebar_name_exists ) {
						
						$post_title = "{$original_name} {$name_count}";
						
						if ( $this->sidebar_name_exists( $post_title, $sidebar_id ) ) {
							$name_count++;
						} else {
							$sidebar_name_exists = false;
						}
					}
				}

				// Update the post object
				$post_arr = array(
					'ID'         => $post_id,
					'post_title' => $post_title
				);
				wp_update_post( $post_arr );

			} else {
				$new_post = $this->add_sidebar_instance( $post_title, $replacement_id, sanitize_text_field( $description ) );
				$post_id = $new_post;
			}
			
			// Reset the query globals
			wp_reset_postdata();

			/*
			 * Update other post meta properties to hold
			 * the custom sidebar properties.
			 */	
			update_post_meta( $post_id, 'sidebar_replacement_id', $replacement_id );
			update_post_meta( $post_id, 'sidebar_description', sanitize_text_field( $description ) );
			update_post_meta( $post_id, 'sidebar_attachments', $sidebar_attachment_data );

			return $post_id;
		}

		/**
		 * Delete Custom Sidebar Instance
		 *
		 * Looks for a custom sidebar instance with the id that is 
		 * passed as a string in the parameter and deletes it.
		 * Returns false if no matches have been found. 
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query              WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/wp_reset_postdata     wp_reset_postdata()
		 * 
		 * @param  string  $sidebar_id    The id of the sidebar we want to delete. Note: This is NOT the post id but the sidebar_id meta value.
		 * 
		 * @return boolean $deleted       True if the sidebar has been located and deleted, false otherwise.
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function delete_sidebar_instance( $sidebar_id ) {
			$params = array(
					'post_type'      => 'sidebar_instance',
					'posts_per_page' => -1,
					'meta_key'       => 'sidebar_id',
					'meta_value'     => $sidebar_id
				);
			$query   = new WP_Query( $params );
			$deleted = false;

			// If no posts are found set deleted to true as it doesn't exist
			if ( 0 == $query->found_posts ) {
				$deleted = true;
			}

			// Delete the post if it exists
			while ( $query->have_posts() ) {
				$query->the_post();
				wp_delete_post( get_the_ID(), true );
				$deleted = true;
			}

			// Reset postdata as we have used the_post()
			wp_reset_postdata();

			return $deleted;
		}

		/**
		 * Delete All Custom Sidebar Instances
		 * 
		 * A function used to delete all posts in the 'sidebar_item'
		 * custom posttype, which will remove all custom sidebars
		 * generated by the user.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID           get_the_ID()
		 * @link http://codex.wordpress.org/Function_Reference/wp_delete_post 		wp_delete_post()
		 * @link http://codex.wordpress.org/Function_Reference/wp_reset_postdata    wp_reset_postdata()
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function delete_all_sidebar_instances() {
			$params = array(
					'post_type'      => 'sidebar_instance',
					'posts_per_page' => -1
				);

			$query  = new WP_Query( $params );

			while ( $query->have_posts() ) {
				$query->the_post();
				wp_delete_post( get_the_ID(), true );
			}

			// Reset postdata as we have used the_post()
			wp_reset_postdata();
		}
	}
endif;
