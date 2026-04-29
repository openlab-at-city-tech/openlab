<?php
/**
 * Update Blog Archive Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Blog_Archive
 */
class Astra_Update_Blog_Archive extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 *
	 * @return void
	 */
	public function configure() {
		$this->id          = 'astra/update-blog-archive';
		$this->category    = 'astra';
		$this->label       = __( 'Update Astra Blog Archive Settings', 'astra' );
		$this->description = __( 'Updates the Astra theme blog archive settings including blog layout, posts per page, sidebar layout, sidebar style, and sidebar width.', 'astra' );

		$this->meta = array(
			'tool_type' => 'write',
		);
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'write';
	}

	/**
	 * Get input schema.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'blog_layout'    => array(
					'type'        => 'string',
					'description' => __( 'Blog layout style. Options: "blog-layout-classic" (Classic), "blog-layout-4" (Grid), "blog-layout-5" (List), "blog-layout-6" (Cover).', 'astra' ),
					'enum'        => array( 'blog-layout-classic', 'blog-layout-4', 'blog-layout-5', 'blog-layout-6' ),
				),
				'posts_per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Number of posts to display per page (1-500).', 'astra' ),
				),
				'sidebar_layout' => array(
					'type'        => 'string',
					'description' => __( 'Sidebar layout position. Options: "no-sidebar" (No Sidebar), "left-sidebar" (Left Sidebar), "right-sidebar" (Right Sidebar).', 'astra' ),
					'enum'        => array( 'no-sidebar', 'left-sidebar', 'right-sidebar' ),
				),
				'sidebar_style'  => array(
					'type'        => 'string',
					'description' => __( 'Sidebar style. Options: "unboxed" (Unboxed), "boxed" (Boxed).', 'astra' ),
					'enum'        => array( 'unboxed', 'boxed' ),
				),
				'sidebar_width'  => array(
					'type'        => 'integer',
					'description' => __( 'Sidebar width in percentage (15-50).', 'astra' ),
				),
			),
		);
	}

	/**
	 * Get examples.
	 *
	 * @return array
	 */
	public function get_examples() {
		return array(
			'set blog layout to grid',
			'change posts per page to 12',
			'update sidebar to left position',
			'set blog layout to list view',
			'change sidebar style to boxed',
			'update posts per page',
			'set sidebar width to 25 percent',
			'change blog to cover layout',
			'update sidebar layout to right',
			'set blog archive to grid layout',
			'change sidebar to no sidebar',
			'update blog layout to classic',
			'set sidebar style to unboxed',
			'change posts display to 20 per page',
			'update blog to list layout',
			'set right sidebar layout',
			'change sidebar width to 30',
			'update archive to grid view',
			'set blog to cover style',
		);
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		$updated         = false;
		$update_messages = array();

		if ( isset( $args['blog_layout'] ) && ! empty( $args['blog_layout'] ) ) {
			$blog_layout   = sanitize_text_field( $args['blog_layout'] );
			$valid_layouts = array( 'blog-layout-classic', 'blog-layout-4', 'blog-layout-5', 'blog-layout-6' );
			if ( ! in_array( $blog_layout, $valid_layouts, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: layout value */
					sprintf( __( 'Invalid blog_layout: %s.', 'astra' ), $blog_layout ),
					__( 'Valid options: blog-layout-classic, blog-layout-4, blog-layout-5, blog-layout-6', 'astra' )
				);
			}

			$layout_labels = array(
				'blog-layout-classic' => 'Classic Layout',
				'blog-layout-4'       => 'Grid',
				'blog-layout-5'       => 'List',
				'blog-layout-6'       => 'Cover',
			);

			astra_update_option( 'blog-layout', $blog_layout );
			$updated           = true;
			$update_messages[] = sprintf( 'Blog layout set to %s', $layout_labels[ $blog_layout ] );
		}

		if ( isset( $args['posts_per_page'] ) ) {
			$posts_per_page = absint( $args['posts_per_page'] );
			if ( $posts_per_page < 1 || $posts_per_page > 500 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: posts per page value */
					sprintf( __( 'Invalid posts_per_page: %d.', 'astra' ), $posts_per_page ),
					__( 'Value must be between 1 and 500.', 'astra' )
				);
			}

			astra_update_option( 'blog-post-per-page', $posts_per_page );
			$updated           = true;
			$update_messages[] = sprintf( 'Posts per page set to %d', $posts_per_page );
		}

		if ( isset( $args['sidebar_layout'] ) && ! empty( $args['sidebar_layout'] ) ) {
			$sidebar_layout = sanitize_text_field( $args['sidebar_layout'] );
			$valid_sidebars = array( 'no-sidebar', 'left-sidebar', 'right-sidebar' );
			if ( ! in_array( $sidebar_layout, $valid_sidebars, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: sidebar layout value */
					sprintf( __( 'Invalid sidebar_layout: %s.', 'astra' ), $sidebar_layout ),
					__( 'Valid options: no-sidebar, left-sidebar, right-sidebar', 'astra' )
				);
			}

			$sidebar_labels = array(
				'no-sidebar'    => 'No Sidebar',
				'left-sidebar'  => 'Left Sidebar',
				'right-sidebar' => 'Right Sidebar',
			);

			astra_update_option( 'site-sidebar-layout', $sidebar_layout );
			$updated           = true;
			$update_messages[] = sprintf( 'Sidebar layout set to %s', $sidebar_labels[ $sidebar_layout ] );
		}

		if ( isset( $args['sidebar_style'] ) && ! empty( $args['sidebar_style'] ) ) {
			$sidebar_style = sanitize_text_field( $args['sidebar_style'] );
			$valid_styles  = array( 'unboxed', 'boxed' );
			if ( ! in_array( $sidebar_style, $valid_styles, true ) ) {
				return Astra_Abilities_Response::error(
					/* translators: %s: sidebar style value */
					sprintf( __( 'Invalid sidebar_style: %s.', 'astra' ), $sidebar_style ),
					__( 'Valid options: unboxed, boxed', 'astra' )
				);
			}

			$style_labels = array(
				'unboxed' => 'Unboxed',
				'boxed'   => 'Boxed',
			);

			astra_update_option( 'site-sidebar-style', $sidebar_style );
			$updated           = true;
			$update_messages[] = sprintf( 'Sidebar style set to %s', $style_labels[ $sidebar_style ] );
		}

		if ( isset( $args['sidebar_width'] ) ) {
			$sidebar_width = absint( $args['sidebar_width'] );
			if ( $sidebar_width < 15 || $sidebar_width > 50 ) {
				return Astra_Abilities_Response::error(
					/* translators: %d: sidebar width value */
					sprintf( __( 'Invalid sidebar_width: %d.', 'astra' ), $sidebar_width ),
					__( 'Value must be between 15 and 50 (percentage).', 'astra' )
				);
			}

			astra_update_option( 'site-sidebar-width', $sidebar_width );
			$updated           = true;
			$update_messages[] = sprintf( 'Sidebar width set to %d%%', $sidebar_width );
		}

		if ( ! $updated ) {
			return Astra_Abilities_Response::error(
				__( 'No changes specified.', 'astra' ),
				__( 'Please provide at least one setting to update (blog_layout, posts_per_page, sidebar_layout, sidebar_style, or sidebar_width).', 'astra' )
			);
		}

		$message = implode( ', ', $update_messages ) . '.';

		return Astra_Abilities_Response::success(
			$message,
			array(
				'updated' => true,
			)
		);
	}
}

Astra_Update_Blog_Archive::register();
