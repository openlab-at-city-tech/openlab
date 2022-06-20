<?php 
namespace ElementsKit_Lite\Modules\HeaderFooterBuilder;

defined( 'ABSPATH' ) || exit;

class Cpt {

	public function __construct() {
		$this->post_type(); 

		add_action( 'admin_menu', array( $this, 'cpt_menu' ) );
		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );
	}

	public function post_type() {
		
		$labels = array(
			'name'               => esc_html__( 'Templates', 'elementskit-lite' ),
			'singular_name'      => esc_html__( 'Template', 'elementskit-lite' ),
			'menu_name'          => esc_html__( 'Header Footer', 'elementskit-lite' ),
			'name_admin_bar'     => esc_html__( 'Header Footer', 'elementskit-lite' ),
			'add_new'            => esc_html__( 'Add New', 'elementskit-lite' ),
			'add_new_item'       => esc_html__( 'Add New Template', 'elementskit-lite' ),
			'new_item'           => esc_html__( 'New Template', 'elementskit-lite' ),
			'edit_item'          => esc_html__( 'Edit Template', 'elementskit-lite' ),
			'view_item'          => esc_html__( 'View Template', 'elementskit-lite' ),
			'all_items'          => esc_html__( 'All Templates', 'elementskit-lite' ),
			'search_items'       => esc_html__( 'Search Templates', 'elementskit-lite' ),
			'parent_item_colon'  => esc_html__( 'Parent Templates:', 'elementskit-lite' ),
			'not_found'          => esc_html__( 'No Templates found.', 'elementskit-lite' ),
			'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', 'elementskit-lite' ),
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
			'supports'            => array( 'title', 'thumbnail', 'elementor' ),
		);

		register_post_type( 'elementskit_template', $args );
	}

	public function cpt_menu() {
		$link_our_new_cpt = 'edit.php?post_type=elementskit_template';
		add_submenu_page( 'elementskit', esc_html__( 'Header Footer', 'elementskit-lite' ), esc_html__( 'Header Footer', 'elementskit-lite' ), 'manage_options', $link_our_new_cpt );
	}

	function load_canvas_template( $single_template ) {

		global $post;

		if ( 'elementskit_template' == $post->post_type ) {

			$elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

			if ( file_exists( $elementor_2_0_canvas ) ) {
				return $elementor_2_0_canvas;
			} else {
				return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
			}
		}

		return $single_template;
	}
}

new Cpt();
