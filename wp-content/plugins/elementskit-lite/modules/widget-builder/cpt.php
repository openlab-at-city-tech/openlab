<?php 
namespace ElementsKit_Lite\Modules\Widget_Builder;

defined( 'ABSPATH' ) || exit;

class Cpt {

	public function __construct() {
		$this->post_type(); 

		add_action( 'admin_menu', array( $this, 'cpt_menu' ) );
	}

	public function post_type() {
		
		$labels = array(
			'name'               => esc_html__( 'Widgets', 'elementskit-lite' ),
			'singular_name'      => esc_html__( 'Widget', 'elementskit-lite' ),
			'menu_name'          => esc_html__( 'Widget Builder', 'elementskit-lite' ),
			'name_admin_bar'     => esc_html__( 'Widgets', 'elementskit-lite' ),
			'add_new'            => esc_html__( 'Add New', 'elementskit-lite' ),
			'add_new_item'       => esc_html__( 'Add New Widget', 'elementskit-lite' ),
			'new_item'           => esc_html__( 'New Widget', 'elementskit-lite' ),
			'edit_item'          => esc_html__( 'Edit Widget', 'elementskit-lite' ),
			'view_item'          => esc_html__( 'View Widget', 'elementskit-lite' ),
			'all_items'          => esc_html__( 'All Widgets', 'elementskit-lite' ),
			'search_items'       => esc_html__( 'Search Widgets', 'elementskit-lite' ),
			'parent_item_colon'  => esc_html__( 'Parent Widgets:', 'elementskit-lite' ),
			'not_found'          => esc_html__( 'No Widgets found.', 'elementskit-lite' ),
			'not_found_in_trash' => esc_html__( 'No Widgets found in Trash.', 'elementskit-lite' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'elementor' ),
		);

		register_post_type( 'elementskit_widget', $args );
	}

	public function cpt_menu() {
		$link_our_new_cpt = 'edit.php?post_type=elementskit_widget';
		add_submenu_page( 'elementskit', esc_html__( 'Widget Builder', 'elementskit-lite' ), esc_html__( 'Widget Builder', 'elementskit-lite' ), 'manage_options', $link_our_new_cpt );
	}
}
