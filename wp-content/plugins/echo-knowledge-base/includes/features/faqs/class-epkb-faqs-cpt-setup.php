<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_FAQs_CPT_Setup {

	const FAQS_POST_TYPE = 'echo_kb_faq';
	const FAQ_CATEGORY = 'echo_kb_faq_category';

	public function __construct() {
		add_action( 'init', array( $this, 'register_faqs_post_type'), 10 );
	}

	public function register_faqs_post_type() {

		/** setup Groups taxonomy */
		$labels = [
			'name'              => esc_html__( 'KB FAQ Groups', 'echo-knowledge-base' ),
			'singular_name'     => esc_html__( 'KB FAQ Group', 'echo-knowledge-base' ),
			'search_items'      => esc_html__( 'Search FAQ Groups', 'echo-knowledge-base' ),
			'all_items'         => esc_html__( 'FAQ Groups', 'echo-knowledge-base' ),
			'parent_item'       => esc_html__( 'Parent FAQ Group', 'echo-knowledge-base' ),
			'parent_item_colon' => esc_html__( 'Parent FAQ Group:', 'echo-knowledge-base' ),
			'edit_item'         => esc_html__( 'Edit FAQ Group', 'echo-knowledge-base' ),
			'update_item'       => esc_html__( 'Update FAQ Group', 'echo-knowledge-base' ),
			'add_new_item'      => esc_html__( 'Add New FAQ Group', 'echo-knowledge-base' ),
			'new_item_name'     => esc_html__( 'New FAQ Group Name', 'echo-knowledge-base' ),
			'menu_name'         => esc_html__( 'FAQs', 'echo-knowledge-base' )
		];
		$args = [
			'hierarchical'      => true,
			'public'            => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'has_archive'       => false,
			'rewrite'           => false
		];
		register_taxonomy( self::FAQ_CATEGORY, self::FAQS_POST_TYPE, $args );

		// FAQs Group status - draft or publish
		register_term_meta( self::FAQ_CATEGORY, 'faq_group_status', [
			'show_in_rest'  => true,
			'type'          => 'string',
			'single'        => true,
			'default'       => 'draft'
		] );
		// Order of FAQs Groups within one FAQs - 1, 2, 3 ..
		register_term_meta( self::FAQ_CATEGORY, 'group_order', [
			'show_in_rest'  => true,
			'type'          => 'integer',
			'single'        => true,
			'default'       => '1'
		] );
		// Sequence of FAQs within one FAQs Group - [1, 2, 3, ..]
		register_term_meta( self::FAQ_CATEGORY, 'faqs_order_sequence', [
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'number',
					),
				),
			),
			'type'          => 'array',
			'single'        => true,
			'default'       => []
		] );

		/** setup Custom Post Type */
		$labels = [
			'name'               => esc_html__( 'Knowledge Base FAQs', 'echo-knowledge-base' ),
			'singular_name'      => esc_html__( 'Knowledge Base FAQ', 'echo-knowledge-base' ),
			'menu_name'          => esc_html__( 'FAQs', 'echo-knowledge-base' ),
			'name_admin_bar'     => esc_html__( 'FAQs', 'echo-knowledge-base' ),
			'add_new'            => esc_html__( 'Add New', 'echo-knowledge-base' ),
			'add_new_item'       => esc_html__( 'Add New FAQ', 'echo-knowledge-base' ),
			'new_item'           => esc_html__( 'New FAQ', 'echo-knowledge-base' ),
			'edit_item'          => esc_html__( 'Edit FAQ', 'echo-knowledge-base' ),
			'all_items'          => esc_html__( 'All FAQs', 'echo-knowledge-base' ),
			'view_item'          => esc_html__( 'View FAQ', 'echo-knowledge-base' ),
			'search_items'       => esc_html__( 'Search FAQs', 'echo-knowledge-base' ),
			'not_found'          => esc_html__( 'No FAQs found', 'echo-knowledge-base' ),
			'not_found_in_trash' => esc_html__( 'No FAQs found in trash', 'echo-knowledge-base' ),
			'parent_item_colon'  => ''
		];
		$args = [
			'labels'              => $labels,
			'description'         => esc_html__( 'Add new FAQs', 'echo-knowledge-base' ),
			'public'              => false,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => ['faq', 'faqs'],
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-feedback',
			'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author', 'comments', 'custom-fields']
		];
		register_post_type( self::FAQS_POST_TYPE, $args );

		// flush rules on plugin activation after CPTs were registered
		$is_flush_rewrite_rules = get_transient( '_epkb_faqs_flush_rewrite_rules' );
		if ( ! empty( $is_flush_rewrite_rules ) && ! is_wp_error( $is_flush_rewrite_rules ) ) {
			flush_rewrite_rules( false );
		}
	}
}