<?php
/**
 * Class: EGF_Posttype
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
 * @version   1.4.4
 * 
 */
if ( ! class_exists( 'EGF_Posttype' ) ) :
	class EGF_Posttype {
		
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
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_actions() {
			add_action( 'init', array( $this, 'register_font_control_posttype' ) );
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
		 * Register Font Control Posttype
		 * 
		 * Register the font control posttype in the same fashion that
		 * WordPress registers nav-menus internally. This will be used
		 * to store any font control instances. Created when the 'init' 
		 * action is fired.
		 *
		 * @link 	http://codex.wordpress.org/Function_Reference/register_post_type 	register_post_type()
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public function register_font_control_posttype() {
			register_post_type( 'tt_font_control', array(
				'labels' => array(
					'name'          => __( 'Google Font Controls', 'theme-translate' ),
					'singular_name' => __( 'Google Font Control',  'theme-translate' )
				),
				'public'           => false,
				'hierarchical'     => false,
				'rewrite'          => false,
				'delete_with_user' => false,
				'query_var'        => false 
			) );
		}

		/**
		 * Add Custom Font Control
		 * 
		 * Create a post for the 'tt_font_control' posttype which 
		 * will use the custom post meta WordPress functionality to store 
		 * all of the necessary attributes for each custom font control. 
		 * Note: The control_id is different to the actual post id for each 
		 * font control instance.
		 * 
		 * @param  string $control_name     The name for this custom font control item.
		 * @param  array  $selectors 		An array of css selectors that will be managed by this font control
		 * @param  string $description    	The description text for this font control.
		 * 
		 * @return $post  The ID of the post if the post is successfully added to the database or 0 on failure.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function add_font_control( $control_name, $selectors = array(), $description = '', $force_styles = false ) {

			// Remove stale data
			delete_transient( 'tt_font_theme_options' );

			// Generate ID and make sure its unique
			$control_count  = rand( 1, 100 );
			$control_id     = 'tt-font-' . $control_count;

			// Generate an array of existing font control ids and names
			$existing_control_ids   = array();
			$existing_control_names = array();
			$control_id_exists      = true;
			$control_name_exists    = true;

			$params = array(
				'post_type'      => 'tt_font_control',
				'posts_per_page' => -1
			);
			$query = new WP_Query( $params );

			while( $query->have_posts() ) {
				$query->the_post();
				$existing_control_ids[]   = get_post_meta( get_the_ID(), 'control_id', true );
				$existing_control_names[] = get_the_title();
			}

			// Make sure the ID doesn't already exist
			while ( $control_id_exists ) {
				if ( in_array( $control_id, $existing_control_ids ) ) {
					$control_count++;
					$control_id = "tt-font-{$control_count}";
				} else {
					$control_id_exists = false;
				}
			}

			// Strip any unallowed characters from the post title
			$control_name = str_replace( array( '#', "'", '"', '&', "{", "}" ), '', $control_name );

			// Give the post a title if it is an empty string
			if ( '' == $control_name ) {
				$control_name = __( 'Font Control', 'theme-translate' );
			}

			// Make sure the name doesn't already exist
			$name_count    = 1;
			$original_name = $control_name;

			while ( $control_name_exists ) {
				if ( in_array( $control_name, $existing_control_names ) ) {
					$name_count++;
					$control_name = "{$original_name} {$name_count}";
				} else {
					$control_name_exists = false;
				}		
			} 	

			$postarr = array(
				'post_type'   => 'tt_font_control',
				'post_title'  => $control_name,
				'post_status' => 'publish' 
			); 
			$post = wp_insert_post( $postarr );
			
			/*
			 * Sanitize Selectors 
			 */
			for ( $i=0; $i < count( $selectors ); $i++ ) {
				while ( substr( $selectors[ $i ], -1 ) == ',' ) {
					$selectors[ $i ] = rtrim( $selectors[ $i ], ',' );
				}
			}

			// Update the post meta to hold the custom font control properties
			update_post_meta( $post, 'control_id', 	$control_id );
			update_post_meta( $post, 'control_selectors', $selectors );
			update_post_meta( $post, 'control_description', sanitize_text_field( $description ) );
			update_post_meta( $post, 'force_styles', $force_styles );

			return $post;
		}

		/**
		 * Update Font Control Instance
		 *
		 * Updates an existing font control instance with the values 
		 * passed into the parameter. If a font control instance is
		 * not found a new font control instance would be created.
		 * 
		 * @param  string $control_id     The ID for the control we wish to update. Note: This is NOT the post id but the control_id meta value.
		 * @param  string $control_name   The name for this custom font control item.
		 * @param  array  $selectors 	  An array of css selectors that will be managed by this font control
		 * @param  string $description    The description text for this custom font control.
		 * 
		 * @return string $post_id The post ID of the updated/created post.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function update_font_control( $control_id, $control_name, $selectors = array(), $description = '', $force_styles = false ) {
			// Remove stale data
			delete_transient( 'tt_font_theme_options' );

			$params = array(
				'post_type'  => 'tt_font_control',
				'meta_key'   => 'control_id',
				'meta_value' => $control_id
			);

			$query = new WP_Query( $params );

			// Strip any unallowed characters from the post title
			$control_name = str_replace( array( '#', "'", '"', '&', "}", "{" ), '', $control_name );

			// Give the post a title if it is an empty string
			if ( '' == $control_name ) {
				$control_name = __( 'Font Control', 'theme-translate' );
			}

			if( $query->found_posts > 0 ) {
				$query->the_post();
				$post_id = get_the_ID();

				// Make sure no other font control has the same name
				if ( self::font_control_exists( $control_name, $control_id ) ) {
					
					$control_name_exists = true;
					$name_count          = 1;
					$original_name       = $control_name;

					while ( $control_name_exists ) {
						
						$control_name = "{$original_name} {$name_count}";
						
						if ( self::font_control_exists( $control_name, $control_id ) ) {
							$name_count++;
						} else {
							$control_name_exists = false;
						}
					}
				}

				// Update the post object
				$post_arr = array(
					'ID'         => $post_id,
					'post_title' => $control_name
				);
				wp_update_post( $post_arr );

			} else {		
				$new_post = self::add_font_control( $control_name, $selectors, $description );
				$post_id  = $new_post;
			}
			
			// Reset the query globals
			wp_reset_postdata();

			/*
			 * Sanitize Selectors 
			 */
			for ( $i=0; $i < count( $selectors ); $i++ ) {
				while ( substr( $selectors[ $i ], -1 ) == ',' ) {
					$selectors[ $i ] = rtrim( $selectors[ $i ], ',' );
				}
			}

			/*
			 * Update other post meta properties to hold
			 * the custom font control properties.
			 */	
			update_post_meta( $post_id, 'control_selectors', $selectors );
			update_post_meta( $post_id, 'control_description', sanitize_text_field( $description ) );
			update_post_meta( $post_id, 'force_styles', $force_styles );

			return $post_id;
		}
		
		/**
		 * Font Control Name Exists
		 *
		 * Takes the font control name to check and the control id to 
		 * exclude and returns true if there are any other font control
		 * instances that have this name. (Boolean Function)
		 * 
		 * @param  string $control_name           The font control name we wish to check
		 * @param  string $control_exclusion_id   The font control id to exclude in the search
		 * @return boolean - true if there is another font control instance that has a matching $control_name
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function font_control_exists( $name, $exclude_control_id ) {
			$control_name_exists = false;

			$params = array(
				'post_type'      => 'tt_font_control',
				'posts_per_page' => -1
			);
			$query = new WP_Query( $params );
			
			// Check if the font control name exists
			while ( $query->have_posts() ) {

				$query->the_post();
				$control_id = get_post_meta( get_the_ID(), 'control_id', true );

				if ( $control_id ) {
					if ( $control_id != $exclude_control_id ) {
						if ( $name == get_the_title() ) {
							$control_name_exists = true;
						}
					}
				}
			}

			wp_reset_postdata();

			return $control_name_exists;
		}

		/**
		 * Get Font Control
		 *
		 * Takes the control id as a parameter and returns the
		 * post object if it's 'control_id' meta value matches 
		 * the control id passed in the parameter. Returns false
		 * if no matches have been found.
		 * 
		 * @param  string $control_id The ID of the font control we wish to check
		 * @return post object if found otherwise false
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function get_font_control( $control_id ) {
			$params = array(
				'post_type'  => 'tt_font_control',
				'meta_key'   => 'control_id',
				'meta_value' => $control_id
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
		 * Get All Font Control Posts
		 *
		 * Returns all of the 'tt_font_control' posttypes objects
		 * in alphabetical order by default. This function will return 
		 * false if there are no 'tt_font_control' posts in the 
		 * database.
		 * 
		 * @return array $query if post exists and 
		 *         boolean if there are no posts.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function get_all_font_controls( $orderby = 'title', $order = 'ASC' ) {

			$params = array(
				'post_type'      => 'tt_font_control',
				'posts_per_page' => -1,
				'orderby'        => $orderby,
				'order'          => $order
			);
			
			$query = new WP_Query( $params );

			if( $query->have_posts() ) {
				return $query;
			} else {
				return false;
			}			
		}

		/**
		 * Delete Custom Font Control Instance
		 *
		 * Looks for a custom control instance with the id that is 
		 * passed as a string in the parameter and deletes it.
		 * Returns false if no matches have been found. 
		 * 
		 * @param  string  $sidebar_id    The id of the control we want to delete. Note: This is NOT the post id but the control_id meta value.
		 * 
		 * @return boolean $deleted       True if the control has been located and deleted, false otherwise.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function delete_font_control( $control_id ) {
			// Remove stale data
			delete_transient( 'tt_font_theme_options' );

			$params = array(
					'post_type'      => 'tt_font_control',
					'posts_per_page' => -1,
					'meta_key'       => 'control_id',
					'meta_value'     => $control_id
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
		 * Delete All Font Controls
		 * 
		 * A function used to delete all posts in the 'tt_font_control'
		 * custom posttype, which will remove all custom font controls
		 * generated by the user.
		 *
		 * @since 1.2
		 * @version 1.4.4
		 * 
		 */
		public static function delete_all_font_controls() {
			
			// Remove stale data
			delete_transient( 'tt_font_theme_options' );

			$params = array(
					'post_type'      => 'tt_font_control',
					'posts_per_page' => -1
				);

			$query  = new WP_Query($params);

			while ( $query->have_posts() ) {
				$query->the_post();
				wp_delete_post( get_the_ID(), true );
			}

			// Reset postdata as we have used the_post()
			wp_reset_postdata();	
		}

	}
endif;
