<?php
/**
 * Class: ECS_Widget_Areas
 *
 * This file initialises the admin functionality for this plugin.
 * It initalises a posttype that acts as a data structure for
 * the custom sidebar instances. It also has useful static helper 
 * functions each custom sidebar. 
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
if ( ! class_exists( 'ECS_Widget_Areas' ) ) :
	class ECS_Widget_Areas {
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
			add_action( 'init', array( $this, 'register_widget_areas' ), 40 );
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
		 * Register All Widget Areas
		 *
		 * Gets all sidebar instances and registers them with 
		 * WordPress using the built in register_sidebar() function.
		 * This function has been updated to compensate for themes
		 * that are not coded correctly.
		 *
		 * @uses global $wp_registered_sidebars
		 * 
		 * @since  1.0
		 * @version 1.0.9
		 * 
		 */
		public function register_widget_areas() {
			global $wp_registered_sidebars;
			global $post;

			$main_post = $post;

			$params = array(
					'post_type'      => 'sidebar_instance',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC'
				);

			$query   = new WP_Query( $params );

			while ( $query->have_posts() ) {
				
				$query->the_post();
				$id                  = get_post_meta( get_the_ID(), 'sidebar_id', true );
				$original_sidebar_id = get_post_meta( get_the_ID(), 'sidebar_replacement_id', true );
				$description         = get_post_meta( get_the_ID(), 'sidebar_description', true );

				if ( isset( $wp_registered_sidebars[ $original_sidebar_id ] ) ) {

					$original_sidebar = $wp_registered_sidebars[ $original_sidebar_id ];

					$sidebar  = array(
						'ecs_custom_sidebar' => 'true',
						'name'               => get_the_title(),
						'id'                 => $id,
						'description'        => $description,
						'class'              => $original_sidebar['class'],
						'before_widget'      => $original_sidebar['before_widget'],
						'after_widget'       => $original_sidebar['after_widget'],
						'before_title'       => $original_sidebar['before_title'],
						'after_title'        => $original_sidebar['after_title'],
					);

					register_sidebar( $sidebar );
				}		
			}
			
			// Reset postdata as we have used the_post()
			wp_reset_postdata();
			
			$post = $main_post;		
		}

		/**
		 * Return All Registered Widget Areas
		 *
		 * Gets all sidebars that are currently registered
		 * with WordPress.
		 *
		 * @uses global $wp_registered_sidebars
		 * @return array $wp_registered_sidebars
		 *
		 * @since 1.0.1
		 * @version  1.0
		 * 
		 */
		public function get_all_registered_widget_areas() {
			global $wp_registered_sidebars;
			return $wp_registered_sidebars;
		}

		/**
		 * Return All Default Widget Areas
		 * 
		 * Gets all registered widget areas and only returns the 
		 * default widget areas that have been registered with the
		 * theme.
		 *
		 * @uses   get_all_registered_widget_areas() - Defined in this class
		 * @return array $default_widgets_areas - An array of default sidebar objects
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_default_widget_areas() {
			$default_widgets_areas = array();

			foreach( $this->get_all_registered_widget_areas() as $widget_area ) {
				if( ! array_key_exists( 'ecs_custom_sidebar', $widget_area ) ) {
					$default_widgets_areas[] = $widget_area;
				}
			}

			return $default_widgets_areas;			
		}

		/**
		 * Return All Custom Widget Areas
		 * 
		 * Gets all registered widget areas and only returns the 
		 * custom ones that have been created by this plugin.
		 *
		 * @uses   get_all_registered_widget_areas() - Defined in this class
		 * @return array $custom_widget_areas - An array of custom widget areas
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function get_custom_widget_areas() {
			$custom_widget_areas = array();
			
			foreach( $this->get_all_registered_widget_areas() as $widget_area ) {
				if( array_key_exists('ecs_custom_sidebar', $widget_area ) ) {
					$custom_widget_areas[] = $widget_area;
				}				
			}

			return $custom_widget_areas;
		}

		/**
		 * Get Ordered Widget Areas
		 *
		 * Utility function that allows you to pass in a sort parameter and
		 * and array to sort. This function will return an array of sorted 
		 * sidebar options. By default (if no parameters are given) this function
		 * will return an ordered array of custom widget areas that are ordered by
		 * name.
		 *
		 * @uses get_custom_widget_areas() - Defined in this class
		 * @param  string $sort_by - Name by default
		 * 
		 * @return array $array An array of sidebar objects to sort
		 *
		 * @since 1.0.1
		 * @version  1.0
		 * 
		 */
		public function get_ordered_custom_widget_areas( $sort_by = 'name', $array = null ) {
			$registered_widget_areas = $array == null ? $this->get_custom_widget_areas() : $array;	
			$ordered_widget_areas = array();

			if ( ! empty( $registered_widget_areas ) ) {
				foreach ( $registered_widget_areas as $widget_area ) {
					$ordered_widget_areas[] = $widget_area[ $sort_by ];
				}
				array_multisort( $ordered_widget_areas, SORT_ASC, $registered_widget_areas );
				return $registered_widget_areas;
			}

			return false;
		}

		/**
		 * Get A Single Widget ARea
		 *
		 * Gets all registered widget areas and only returns the 
		 * custom widget areas that have been generated by this
		 * plugin.
		 * 
		 * @uses   get_custom_widget_areas() - Defined in this class
		 * @return array $widget_area - The widget area if it exists, false otherwise.
		 *
		 * @since 1.0.1
		 * @version  1.0
		 * 
		 */
		public function get_custom_widget_area( $id ) {
			$custom_widget_areas = $this->get_custom_widget_areas();
			
			if( ! empty( $custom_widget_areas ) ) {
				foreach ( $custom_widget_areas as $widget_area ) {
					if ( $id == $widget_area['id'] ) {
						return $widget_area;
					}
				}
			}

			return false;
		}

		/**
		 * Unregister A Custom Widget Area
		 *
		 * Finds a custom widget area if it exists and uses 
		 * the native WordPress function to unregister the 
		 * widget area.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/unregister_sidebar 	unregister_sidebar()
		 * 
		 * @uses   get_custom_widget_area() - Defined in this class.
		 * @param  $id - ID of the widget area to unregister
		 * @return boolean	true if successfully deleted, false otherwise
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		public function unregister_custom_widget_area( $id ) {
			$custom_widget_area = $this->get_custom_widget_area( $id );

			if ( $custom_widget_area ) {
				unregister_sidebar( $id );
				return true;
			}

			return false;
		}
	}
endif;
