<?php
/**
 * PostType Class.
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soliloquy PostType
 *
 * @since 2.5.0
 */
class Soliloquy_Posttype_Lite {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base = Soliloquy_Lite::get_instance();

		// Build the labels for the post type.
		$labels = apply_filters(
			'soliloquy_post_type_labels',
			[
				'name'               => __( 'Soliloquy Sliders', 'soliloquy' ),
				'singular_name'      => __( 'Soliloquy', 'soliloquy' ),
				'add_new'            => __( 'Add New', 'soliloquy' ),
				'add_new_item'       => __( 'Add New Soliloquy Slider', 'soliloquy' ),
				'edit_item'          => __( 'Edit Soliloquy Slider', 'soliloquy' ),
				'new_item'           => __( 'New Soliloquy Slider', 'soliloquy' ),
				'view_item'          => __( 'View Soliloquy Slider', 'soliloquy' ),
				'search_items'       => __( 'Search Soliloquy Sliders', 'soliloquy' ),
				'not_found'          => __( 'No Soliloquy sliders found.', 'soliloquy' ),
				'not_found_in_trash' => __( 'No Soliloquy sliders found in trash.', 'soliloquy' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Soliloquy', 'soliloquy' ),
			]
		);

		// Build out the post type arguments.
		$args = apply_filters(
			'soliloquy_post_type_args',
			[
				'labels'              => $labels,
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_admin_bar'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'show_in_rest'        => true,
				'rest_base'           => 'soliloquy',
				'capability_type'     => 'page',
				'capabilities'        => [
					'publish_posts' => 'upload_files',
					'delete_posts'  => 'upload_files',
					'edit_post'     => 'upload_files',
					'delete_post'   => 'upload_files',
					'read_post'     => 'upload_files',
					'create_posts'  => 'upload_files',
				],
				'menu_position'       => apply_filters( 'soliloquy_post_type_menu_position', 248 ),
				'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', $this->base->file ),
				'supports'            => [ 'title' ],
			]
		);

		// Register the post type with WordPress.
		register_post_type( 'soliloquy', $args );
	}
	/**
	 * Helper Method to add Soliloquy Meta data to the Rest API
	 *
	 * @param [type] $data Rest Data.
	 * @param [type] $post Post Object.
	 * @param [type] $context Context.
	 * @return array
	 */
	public function prepare_meta( $data, $post, $context ) {

		$slider_data = get_post_meta( $post->ID, '_sol_slider_data', true );

		if ( $slider_data ) {
			$data->data['slider_data'] = $slider_data;
		}

		return $data;
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Soliloquy_Posttype_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Posttype_Lite ) ) {
			self::$instance = new Soliloquy_Posttype_Lite();
		}

		return self::$instance;
	}
}

// Load the posttype class.
$soliloquy_posttype_lite = Soliloquy_Posttype_Lite::get_instance();
