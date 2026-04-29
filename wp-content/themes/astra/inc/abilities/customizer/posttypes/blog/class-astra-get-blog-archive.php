<?php
/**
 * Get Blog Archive Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Blog_Archive
 */
class Astra_Get_Blog_Archive extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/get-blog-archive';
		$this->label       = __( 'Get Astra Blog Archive Settings', 'astra' );
		$this->description = __( 'Retrieves the current Astra theme blog archive settings including blog layout, posts per page, sidebar layout, sidebar style, and sidebar width.', 'astra' );
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
				'blog_layout'               => array(
					'type'        => 'string',
					'description' => __( 'Current blog layout key.', 'astra' ),
				),
				'blog_layout_label'         => array(
					'type'        => 'string',
					'description' => __( 'Human-readable blog layout name.', 'astra' ),
				),
				'posts_per_page'            => array(
					'type'        => 'integer',
					'description' => __( 'Number of posts per page.', 'astra' ),
				),
				'sidebar_layout'            => array(
					'type'        => 'string',
					'description' => __( 'Current sidebar layout key.', 'astra' ),
				),
				'sidebar_layout_label'      => array(
					'type'        => 'string',
					'description' => __( 'Human-readable sidebar layout name.', 'astra' ),
				),
				'sidebar_style'             => array(
					'type'        => 'string',
					'description' => __( 'Current sidebar style key.', 'astra' ),
				),
				'sidebar_style_label'       => array(
					'type'        => 'string',
					'description' => __( 'Human-readable sidebar style name.', 'astra' ),
				),
				'sidebar_width'             => array(
					'type'        => 'integer',
					'description' => __( 'Sidebar width percentage.', 'astra' ),
				),
				'available_blog_layouts'    => array(
					'type'        => 'object',
					'description' => __( 'Available blog layout options.', 'astra' ),
				),
				'available_sidebar_layouts' => array(
					'type'        => 'object',
					'description' => __( 'Available sidebar layout options.', 'astra' ),
				),
				'available_sidebar_styles'  => array(
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
			'get blog archive settings',
			'show blog layout configuration',
			'view sidebar settings',
			'display posts per page',
			'get current blog layout',
			'show sidebar layout',
			'view sidebar style settings',
			'display blog archive layout',
			'get sidebar width',
			'show blog grid settings',
			'view blog list layout',
			'display archive page settings',
			'get blog cover layout',
			'show current sidebar position',
			'view blog post layout',
			'display archive configuration',
			'get blog and sidebar settings',
			'show archive layout options',
			'view blog page settings',
			'display sidebar configuration',
			'get posts display settings',
			'show blog archive layout',
			'view archive sidebar',
			'display blog layout style',
			'get archive page configuration',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$blog_layout    = astra_get_option( 'blog-layout', 'blog-layout-5' );
		$posts_per_page = astra_get_option( 'blog-post-per-page', get_option( 'posts_per_page', 10 ) );
		$sidebar_layout = astra_get_option( 'site-sidebar-layout', 'right-sidebar' );
		$sidebar_style  = astra_get_option( 'site-sidebar-style', 'unboxed' );
		$sidebar_width  = astra_get_option( 'site-sidebar-width', 30 );

		$layout_labels = array(
			'blog-layout-classic' => 'Classic Layout',
			'blog-layout-1'       => 'Layout 1',
			'blog-layout-2'       => 'Layout 2',
			'blog-layout-3'       => 'Layout 3',
			'blog-layout-4'       => 'Grid',
			'blog-layout-5'       => 'List',
			'blog-layout-6'       => 'Cover',
		);

		$sidebar_layout_labels = array(
			'no-sidebar'    => 'No Sidebar',
			'left-sidebar'  => 'Left Sidebar',
			'right-sidebar' => 'Right Sidebar',
		);

		$sidebar_style_labels = array(
			'unboxed' => 'Unboxed',
			'boxed'   => 'Boxed',
		);

		$blog_layout_name    = isset( $layout_labels[ $blog_layout ] ) ? $layout_labels[ $blog_layout ] : $blog_layout;
		$sidebar_layout_name = isset( $sidebar_layout_labels[ $sidebar_layout ] ) ? $sidebar_layout_labels[ $sidebar_layout ] : $sidebar_layout;
		$sidebar_style_name  = isset( $sidebar_style_labels[ $sidebar_style ] ) ? $sidebar_style_labels[ $sidebar_style ] : $sidebar_style;

		return Astra_Abilities_Response::success(
			/* translators: 1: blog layout name, 2: posts per page, 3: sidebar layout name, 4: sidebar style name, 5: sidebar width */
			sprintf(
				__( 'Your blog archive is using the "%1$s" layout with %2$d posts per page. Sidebar is set to "%3$s" (%4$s style, %5$d%% width).', 'astra' ),
				$blog_layout_name,
				(int) $posts_per_page,
				$sidebar_layout_name,
				$sidebar_style_name,
				(int) $sidebar_width
			),
			array(
				'blog_layout'               => $blog_layout,
				'blog_layout_label'         => $blog_layout_name,
				'posts_per_page'            => (int) $posts_per_page,
				'sidebar_layout'            => $sidebar_layout,
				'sidebar_layout_label'      => $sidebar_layout_name,
				'sidebar_style'             => $sidebar_style,
				'sidebar_style_label'       => $sidebar_style_name,
				'sidebar_width'             => (int) $sidebar_width,
				'available_blog_layouts'    => $layout_labels,
				'available_sidebar_layouts' => $sidebar_layout_labels,
				'available_sidebar_styles'  => $sidebar_style_labels,
			)
		);
	}
}

Astra_Get_Blog_Archive::register();
