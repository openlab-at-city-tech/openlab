<?php
/**
 * Category Sticky Post
 *
 * @package   Category_Sticky_Post
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/category-sticky-post/
 * @copyright 2013 - 2015 Tom McFarlin
 */

/**
 * @package Category_Sticky_Post
 * @author  Tom McFarlin <tom@tommcfarlin.com>
 */

class Category_Sticky_Post {

	/*--------------------------------------------*
	 * Attributes
	 *--------------------------------------------*/

	/**
	 * A static reference to track the single instance of this class.
	 *
	 * @since   2.0.0
	 *
	 * @var     Category_Sticky_Post
	 */
	 private static $instance = null;

	/**
	 * A boolean used to track whether or not the sticky post has been marked as sticky.
	 *
	 * @since   1.0.0
	 *
	 * @var     boolean
	 */
	 private $is_sticky_post;

	/*--------------------------------------------*
	 * Singleton Implementation
	 *--------------------------------------------*/

	/**
	 * Method used to provide a single instance of this
	 *
	 * @since    2.0.0
	 */
	public static function get_instance() {

		if( null == self::$instance ) {
			self::$instance = new Category_Sticky_Post();
		}

		return self::$instance;

	}

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, admin styles, and content filters.
	 *
	 * @since    1.0.0
	 */
	private function __construct() {

		// Initialize the count of the sticky post
		$this->is_sticky_post = false;

		// Category Meta Box actions
		add_action( 'add_meta_boxes', array( $this, 'add_category_sticky_post_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_category_sticky_post_data' ) );
		add_action( 'wp_ajax_is_category_sticky_post', array( $this, 'is_category_sticky_post' ) );

		// Filters for displaying the sticky category posts
		add_filter( 'the_posts', array( $this, 'reorder_category_posts' ), 10, 2 );
		add_filter( 'post_class', array( $this, 'set_category_sticky_class' ) );

		// Stylesheets
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_styles_and_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );

	}

	/*---------------------------------------------*
	 * Action Functions
	 *---------------------------------------------*/

	/**
	 * Renders the meta box for allowing the user to select a category in which to stick a given post.
	 *
	 * @since    1.0.0
	 */
	public function add_category_sticky_post_meta_box() {

		// First, read all of the post types
		$post_types = get_post_types( '', 'names' );

		// Now, for each post type, add the meta box
		foreach( $post_types as $post_type ) {

			if ( 'page' === $post_type ) {
				continue;
			} // end if

			add_meta_box(
				'post_is_sticky',
				__( 'Category Sticky', 'category-sticky-post' ),
				array( $this, 'category_sticky_post_display' ),
				$post_type,
				'side',
				'low'
			);

		}

	}

	/**
	 * Renders the select box that allows users to choose the category into which to stick the
	 * specified post.
	 *
	 * @param	object	$post	The post to be marked as sticky for the specified category.
	 *
	 * @since    1.0.0
	 */
	public function category_sticky_post_display( $post ) {

		// Set the nonce for security
		wp_nonce_field( plugin_basename( __FILE__ ), 'category_sticky_post_nonce' );

		// Get the category dropdown and the checkbox for displaying the border
		$html =  $this->get_categories_list( $post );
		$html .= $this->get_border_checkbox( $post );

		echo $html;

	}

	/**
	 * Set the custom post meta for marking a post as sticky.
	 *
	 * @param	int    $post_id	    The ID of the post to which we're saving the post meta
	 *
	 * @since    1.0.0
	 */
	public function save_category_sticky_post_data( $post_id ) {

		if( isset( $_POST['category_sticky_post_nonce'] ) && isset( $_POST['post_type'] ) && $this->user_can_save( $post_id, 'category_sticky_post_nonce' ) ) {

			// Read the ID of the category to which we're going to stick this post
			$category_id = get_post_meta( $post_id, 'category_sticky_post', true );
			if( isset( $_POST['category_sticky_post'] ) ) {
				$category_id = esc_attr( $_POST['category_sticky_post'] );
			}

			// If the value exists, delete it first. I don't want to write extra rows into the table.
			if ( 0 == count( get_post_meta( $post_id, 'category_sticky_post' ) ) ) {
				delete_post_meta( $post_id, 'category_sticky_post' );
			}
			update_post_meta( $post_id, 'category_sticky_post', $category_id );

			// Read the ID of the category to which we're going to stick this post
			if( isset( $_POST['category_sticky_post_border'] ) ) {
				update_post_meta( $post_id, 'category_sticky_post_border', esc_attr( $_POST['category_sticky_post_border'] ) );
			} else {
				delete_post_meta( $post_id, 'category_sticky_post_border' );
			}

		}

	}

	/**
	 * Register and enqueue the stylesheets and JavaScript dependencies for styling the sticky post.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_styles_and_scripts() {

		wp_enqueue_style( 'category-sticky-post', plugins_url( '/category-sticky-post/css/admin.css' ) );
		wp_enqueue_script( 'category-sticky-post-editor', plugins_url( '/category-sticky-post/js/editor.min.js' ), array( 'jquery' ) );
		wp_enqueue_script( 'category-sticky-post', plugins_url( '/category-sticky-post/js/admin.min.js' ), array( 'jquery' ) );

	}

	/**
	 * Register and enqueue the stylesheets for styling the sticky post, but only do so on an archives page.
	 *
	 * @since    1.0.0
	 */
	public function add_styles() {

		global $post;

		if( is_archive() && '1' !== get_post_meta( $post->ID, 'category_sticky_post_border', true ) ) {
			wp_enqueue_style( 'category-sticky-post', plugins_url( '/category-sticky-post/css/plugin.css' ) );
		}

	}

	/**
	 * Ajax callback function used to decide if the specified post ID is marked as a category
	 * sticky post.
	 *
	 * TODO:	Eventually, I want to do this all server side.
	 *
	 * @since    1.0.0
	 */
	public function is_category_sticky_post() {

		if( isset( $_GET['post_id'] ) ) {

			$post_id = trim ( $_GET['post_id'] );

			if( 0 == get_post_meta( $post_id, 'category_sticky_post', true ) ) {
				die( '0' );
			} else {
				die( _e( ' - Category Sticky Post', 'category-sticky-post' ) );
			}

		}

	}

	/*---------------------------------------------*
	 * Filter Functions
	 *---------------------------------------------*/

	 /**
	  * Adds a CSS class to make it easy to style the sticky post.
	  *
	  * @param		array	$classes	The array of classes being applied to the given post
	  * @return		array				The updated array of classes for our posts
	  *
	  * @since      1.0.0
	  */
	  public function set_category_sticky_class( $classes ) {

	 	// If we've not set the category sticky post...
	 	if( is_category() && false == $this->is_sticky_post && $this->is_sticky_post() ) {

		 	// ...append the class to the first post (or the first time this event is raised)
			$classes[] = 'category-sticky';

			// ...and indicate that we've set the sticky post
			$this->is_sticky_post = true;

		}

		return $classes;

	 }

	 /**
	  * Places the sticky post at the top of the list of posts for the category that is being displayed.
	  *
	  * @param	    array	$posts	The lists of posts to be displayed for the given category
	  * @return	    array			The updated list of posts with the sticky post set as the first titem
	  *
	  * @since      1.0.0
	  */
	 public function reorder_category_posts( $posts, $query ) {

	 	// We only care to do this for the first page of the archives
	 	if( $query->is_main_query() && is_archive() && 0 == get_query_var( 'paged' ) && '' != get_query_var( 'cat' ) ) {

		 	// Read the current category to find the sticky post
		 	// and query for the ID of the post
		 	$category = get_category( get_query_var( 'cat' ) );
		 	$sticky_query = $this->get_sticky_query( $category );

		 	// If there's a post, then set the post ID
		 	$post_id = ( ! isset ( $sticky_query->posts[0] ) ) ? -1 : $sticky_query->posts[0];
		 	wp_reset_postdata();

		 	// If the query returns an actual post ID, then let's update the posts
		 	if( -1 < $post_id ) {

		 		// Store the sticky post in an array
			 	$new_posts = array( get_post( $post_id ) );

			 	// Look to see if the post exists in the current list of posts.
			 	foreach( $posts as $post_index => $post ) {

			 		// If so, then remove it so we don't duplicate its display
			 		if( $post_id == $posts[ $post_index ]->ID ) {
				 		unset( $posts[ $post_index ] );
			 		}

			 	}

			 	// Merge the existing array (with the sticky post first and the original posts second)
			 	$posts = array_merge( $new_posts, $posts );

		 	}

	 	}

	 	return $posts;

	 }

	/*---------------------------------------------*
	 * Helper Functions
	 *---------------------------------------------*/

	/**
	 * Determines whether or not the current post is a sticky post for the current category.
	 *
	 * @return	   boolean    Whether or not the current post is a sticky post for the current category.
	 *
	 * @since      1.0.0
	 */
	private function is_sticky_post() {

		global $post;
		return get_query_var( 'cat' ) == get_post_meta( $post->ID, 'category_sticky_post', true );

	}

	/**
	 * Determines whether or not the current user has the ability to save meta data associated with this post.
	 *
	 * @param		int		$post_id	The ID of the post being save
	 * @param		bool				Whether or not the user has the ability to save this post.
	 *
	 * @since      1.0.0
	 */
	private function user_can_save( $post_id, $nonce ) {

	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], plugin_basename( __FILE__ ) ) );

	    // Return true if the user is able to save; otherwise, false.
	    return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;

	}

	/**
	 * Creates the label and the checkbox used to give the user the option to hide or to display
	 * the sticky post border.
	 *
	 * @param    object    $category    The category for which we're looking to find the sticky post.
	 * @return   WP_Query               The query used to return the category with the sticky post.
	 *
	 * @since    2.0.0
	 */
	private function get_sticky_query( $category ) {

		return new WP_Query(
		 		array(
			 		'fields'			=>	'ids',
			 		'post_type'			=>	'post',
			 		'posts_per_page'	=>	'1',
			 		'tax_query'			=> array(
			 			'terms'				=> 	null,
			 			'include_children'	=>	false
			 		),
			 		'meta_query'		=>	array(
			 			array(
				 			'key'		=>	'category_sticky_post',
				 			'value'		=>	$category->cat_ID,
				 		)
			 		)
		 		)
		 	);

	}

	/**
	 * Creates the label and the select box used to give the user the option to select
	 * their category.
	 *
	 * @param    object    $post    The current post.
	 * @return   string    $html    The HTML used to render the markup
	 *
	 * @since    2.0.0
	 */
	private function get_categories_list( $post ) {

		// First, read all the categories
		$categories = get_categories();

		// Build the HTML that will display the select box
		$html = '<select id="category_sticky_post" name="category_sticky_post">';
			$html .= '<option value="0">' . __( 'Select a category...', 'category-sticky-post' ) . '</option>';
			foreach( $categories as $category ) {
				$html .= '<option value="' . $category->cat_ID . '" ' . selected( get_post_meta( $post->ID, 'category_sticky_post', true ), $category->cat_ID, false ) . '>';
					$html .= $category->cat_name;
				$html .= '</option>';
			}
		$html .= '</select>';

		return $html;

	}

	/**
	 * Creates the label and the checkbox used to give the user the option to hide or to display
	 * the sticky post border.
	 *
	 * @param    object    $post    The current post.
	 * @return   string    $html    The HTML used to render the markup
	 *
	 * @since    2.0.0
	 */
	private function get_border_checkbox( $post ) {

		// Display the option for showing a border on the sticky post
		$html = '<label for="category_sticky_post_border">';
			$html .= '<input type="checkbox" value="1" name="category_sticky_post_border" id="category_sticky_post_border" ' . checked( '1', get_post_meta( $post->ID, 'category_sticky_post_border', true ), false ) . ' />';
			$html .= __( 'Hide Sticky Post Border?', 'category-sticky-post' );
		$html .= '</label>';

		return $html;

	}

}