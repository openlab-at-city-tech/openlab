<?php 
namespace ElementsKit_Lite\Modules\Dynamic_Content;

defined( 'ABSPATH' ) || exit;

class Cpt {

	public function __construct() {
		$this->post_type();
		register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
		register_activation_hook( __FILE__, array( $this, 'flush_rewrites' ) );   
	}

	public function post_type() {
		
		$labels  = array(
			'name'                  => _x( 'ElementsKit items', 'Post Type General Name', 'elementskit-lite' ),
			'singular_name'         => _x( 'ElementsKit item', 'Post Type Singular Name', 'elementskit-lite' ),
			'menu_name'             => esc_html__( 'ElementsKit item', 'elementskit-lite' ),
			'name_admin_bar'        => esc_html__( 'ElementsKit item', 'elementskit-lite' ),
			'archives'              => esc_html__( 'Item Archives', 'elementskit-lite' ),
			'attributes'            => esc_html__( 'Item Attributes', 'elementskit-lite' ),
			'parent_item_colon'     => esc_html__( 'Parent Item:', 'elementskit-lite' ),
			'all_items'             => esc_html__( 'All Items', 'elementskit-lite' ),
			'add_new_item'          => esc_html__( 'Add New Item', 'elementskit-lite' ),
			'add_new'               => esc_html__( 'Add New', 'elementskit-lite' ),
			'new_item'              => esc_html__( 'New Item', 'elementskit-lite' ),
			'edit_item'             => esc_html__( 'Edit Item', 'elementskit-lite' ),
			'update_item'           => esc_html__( 'Update Item', 'elementskit-lite' ),
			'view_item'             => esc_html__( 'View Item', 'elementskit-lite' ),
			'view_items'            => esc_html__( 'View Items', 'elementskit-lite' ),
			'search_items'          => esc_html__( 'Search Item', 'elementskit-lite' ),
			'not_found'             => esc_html__( 'Not found', 'elementskit-lite' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'elementskit-lite' ),
			'featured_image'        => esc_html__( 'Featured Image', 'elementskit-lite' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'elementskit-lite' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'elementskit-lite' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'elementskit-lite' ),
			'insert_into_item'      => esc_html__( 'Insert into item', 'elementskit-lite' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'elementskit-lite' ),
			'items_list'            => esc_html__( 'Items list', 'elementskit-lite' ),
			'items_list_navigation' => esc_html__( 'Items list navigation', 'elementskit-lite' ),
			'filter_items_list'     => esc_html__( 'Filter items list', 'elementskit-lite' ),
		);
		$rewrite = array(
			'slug'       => 'elementskit-content',
			'with_front' => true,
			'pages'      => false,
			'feeds'      => false,
		);
		$args    = array(
			'label'               => esc_html__( 'ElementsKit item', 'elementskit-lite' ),
			'description'         => esc_html__( 'elementskit_content', 'elementskit-lite' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'elementor', 'permalink' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'query_var'           => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
			'rest_base'           => 'elementskit-content',
		);
		register_post_type( 'elementskit_content', $args );
	}

	public function flush_rewrites() {
		$this->post_type();
		flush_rewrite_rules();
	}
}

new Cpt();
