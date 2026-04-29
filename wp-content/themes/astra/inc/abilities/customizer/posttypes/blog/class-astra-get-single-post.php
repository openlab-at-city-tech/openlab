<?php
/**
 * Get Single Post Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Single_Post
 */
class Astra_Get_Single_Post extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-single-post';
		$this->label       = __( 'Get Astra Single Post Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme single post settings including container layout, container style, sidebar layout, sidebar style, content width, and related posts settings.', 'astra' );
		$this->category    = 'astra';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array();
	}

	/**
	 * Get output schema.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->build_output_schema(
			array(
				'container_layout'            => array(
					'type'        => 'string',
					'description' => __( 'Current container layout key.', 'astra' ),
				),
				'container_layout_label'      => array(
					'type'        => 'string',
					'description' => __( 'Human-readable container layout name.', 'astra' ),
				),
				'container_style'             => array(
					'type'        => 'string',
					'description' => __( 'Current container style key.', 'astra' ),
				),
				'container_style_label'       => array(
					'type'        => 'string',
					'description' => __( 'Human-readable container style name.', 'astra' ),
				),
				'sidebar_layout'              => array(
					'type'        => 'string',
					'description' => __( 'Current sidebar layout key.', 'astra' ),
				),
				'sidebar_layout_label'        => array(
					'type'        => 'string',
					'description' => __( 'Human-readable sidebar layout name.', 'astra' ),
				),
				'sidebar_style'               => array(
					'type'        => 'string',
					'description' => __( 'Current sidebar style key.', 'astra' ),
				),
				'sidebar_style_label'         => array(
					'type'        => 'string',
					'description' => __( 'Human-readable sidebar style name.', 'astra' ),
				),
				'content_width'               => array(
					'type'        => 'string',
					'description' => __( 'Content width setting key.', 'astra' ),
				),
				'content_width_label'         => array(
					'type'        => 'string',
					'description' => __( 'Human-readable content width name.', 'astra' ),
				),
				'content_max_width'           => array(
					'type'        => 'integer',
					'description' => __( 'Custom content max width in pixels.', 'astra' ),
				),
				'related_posts_enabled'       => array(
					'type'        => 'boolean',
					'description' => __( 'Whether related posts are enabled.', 'astra' ),
				),
				'related_posts_count'         => array(
					'type'        => 'integer',
					'description' => __( 'Number of related posts to display.', 'astra' ),
				),
				'available_container_layouts' => array(
					'type'        => 'object',
					'description' => __( 'Available container layout options.', 'astra' ),
				),
				'available_container_styles'  => array(
					'type'        => 'object',
					'description' => __( 'Available container style options.', 'astra' ),
				),
				'available_sidebar_layouts'   => array(
					'type'        => 'object',
					'description' => __( 'Available sidebar layout options.', 'astra' ),
				),
				'available_sidebar_styles'    => array(
					'type'        => 'object',
					'description' => __( 'Available sidebar style options.', 'astra' ),
				),
			)
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'get single post settings',
			'show single post layout',
			'view post container layout',
			'display single post sidebar',
			'get post content width',
			'show related posts settings',
			'view single post configuration',
			'display post layout options',
			'get post sidebar style',
			'show single post container',
			'view post page settings',
			'display related posts status',
			'get single blog post settings',
			'show post sidebar layout',
			'view single post style',
			'display post container style',
			'get post layout configuration',
			'show single post options',
			'view blog post settings',
			'display post page layout',
			'get single entry settings',
			'show post detail settings',
			'view individual post layout',
			'display single article settings',
			'get post display configuration',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$container_layout      = astra_get_option( 'single-post-ast-content-layout', 'default' );
		$container_style       = astra_get_option( 'site-content-style', 'unboxed' );
		$sidebar_layout        = astra_get_option( 'single-post-sidebar-layout', 'default' );
		$sidebar_style         = astra_get_option( 'single-post-sidebar-style', 'default' );
		$content_width         = astra_get_option( 'blog-single-width', 'default' );
		$content_max_width     = astra_get_option( 'blog-single-max-width', 1200 );
		$related_posts_enabled = astra_get_option( 'enable-related-posts', false );
		$related_posts_count   = astra_get_option( 'related-posts-total-count', 2 );

		$container_layout_labels = array(
			'default'                => 'Default',
			'normal-width-container' => 'Normal',
			'narrow-width-container' => 'Narrow',
			'full-width-container'   => 'Full Width',
		);

		$container_style_labels = array(
			'boxed'   => 'Boxed',
			'unboxed' => 'Unboxed',
		);

		$sidebar_layout_labels = array(
			'default'       => 'Default',
			'no-sidebar'    => 'No Sidebar',
			'left-sidebar'  => 'Left Sidebar',
			'right-sidebar' => 'Right Sidebar',
		);

		$sidebar_style_labels = array(
			'default' => 'Default',
			'unboxed' => 'Unboxed',
			'boxed'   => 'Boxed',
		);

		$content_width_labels = array(
			'default' => 'Default',
			'custom'  => 'Custom',
		);

		return Astra_Abilities_Response::success(
			__( 'Retrieved single post settings successfully.', 'astra' ),
			array(
				'container_layout'            => $container_layout,
				'container_layout_label'      => isset( $container_layout_labels[ $container_layout ] ) ? $container_layout_labels[ $container_layout ] : $container_layout,
				'container_style'             => $container_style,
				'container_style_label'       => isset( $container_style_labels[ $container_style ] ) ? $container_style_labels[ $container_style ] : $container_style,
				'sidebar_layout'              => $sidebar_layout,
				'sidebar_layout_label'        => isset( $sidebar_layout_labels[ $sidebar_layout ] ) ? $sidebar_layout_labels[ $sidebar_layout ] : $sidebar_layout,
				'sidebar_style'               => $sidebar_style,
				'sidebar_style_label'         => isset( $sidebar_style_labels[ $sidebar_style ] ) ? $sidebar_style_labels[ $sidebar_style ] : $sidebar_style,
				'content_width'               => $content_width,
				'content_width_label'         => isset( $content_width_labels[ $content_width ] ) ? $content_width_labels[ $content_width ] : $content_width,
				'content_max_width'           => (int) $content_max_width,
				'related_posts_enabled'       => (bool) $related_posts_enabled,
				'related_posts_count'         => (int) $related_posts_count,
				'available_container_layouts' => $container_layout_labels,
				'available_container_styles'  => $container_style_labels,
				'available_sidebar_layouts'   => $sidebar_layout_labels,
				'available_sidebar_styles'    => $sidebar_style_labels,
			)
		);
	}
}

Astra_Get_Single_Post::register();
