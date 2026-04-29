<?php
/**
 * Update Post Meta Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Update_Postmeta
 */
class Astra_Update_Postmeta extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/update-post-meta';
		$this->category    = 'astra';
		$this->label       = __( 'Update Post-Specific Meta Settings (Individual Posts/Pages)', 'astra' );
		$this->description = __( 'Updates Astra meta settings for ONE SPECIFIC post or page by post title or ID. This overrides global settings for that individual post only. IMPORTANT: Use this ONLY when the user specifies a specific post/page name (like "Sample Page", "Hello World", "About") or post ID. This is NOT for global settings - use single page/post update tools for global settings instead.', 'astra' );

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
			'required'   => array( 'key', 'value' ),
			'properties' => array(
				'post_id'    => array(
					'type'        => 'integer',
					'description' => 'Post ID to update meta for. Either post_id or post_title is required.',
				),
				'post_title' => array(
					'type'        => 'string',
					'description' => 'Post title to find and update. Either post_id or post_title is required. Example: "Sample Page", "Hello World"',
				),
				'key'        => array(
					'type'        => 'string',
					'description' => 'Meta key to update. Valid keys: site-post-title, ast-site-content-layout, site-content-style, site-sidebar-layout, site-sidebar-style, ast-global-header-display, footer-sml-layout, ast-banner-title-visibility, ast-breadcrumbs-content, ast-hfb-above-header-display, ast-main-header-display, ast-hfb-below-header-display, ast-hfb-mobile-header-display, theme-transparent-header-meta',
					'enum'        => array(
						'site-post-title',
						'ast-site-content-layout',
						'site-content-style',
						'site-sidebar-layout',
						'site-sidebar-style',
						'ast-global-header-display',
						'footer-sml-layout',
						'ast-banner-title-visibility',
						'ast-breadcrumbs-content',
						'ast-hfb-above-header-display',
						'ast-main-header-display',
						'ast-hfb-below-header-display',
						'ast-hfb-mobile-header-display',
						'theme-transparent-header-meta',
					),
				),
				'value'      => array(
					'type'        => 'string',
					'description' => 'Value to set. For site-post-title: "" (default) or "disabled". For ast-site-content-layout: "default", "normal-width-container", "narrow-width-container", "full-width-container". For site-content-style and site-sidebar-style: "default", "unboxed", "boxed". For site-sidebar-layout: "default", "no-sidebar", "left-sidebar", "right-sidebar". For header/footer/banner/breadcrumb toggles: "" (default) or "disabled". For theme-transparent-header-meta: "default", "enabled", "disabled"',
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
			'change container layout to full width for Sample Page',
			'set container to full width for Sample Page',
			'make Sample Page full width',
			'update Sample Page container to full width',
			'set full width container for Sample Page',
			'disable transparent header for Hello World post',
			'hide title on the Sample Page',
			'remove sidebar from the Hello World post',
			'disable header for the Contact page',
			'make the About page full width',
			'hide footer on the Landing Page',
			'set the Home page container to narrow',
			'enable transparent header for the Blog post',
			'disable breadcrumb for Sample Page',
			'remove title from the Hello World post',
			'set sidebar to left for the Archive page',
			'make content boxed on the Sample Page',
			'hide banner on the Hello World post',
			'disable above header for the Contact page',
			'set right sidebar for the Blog page',
			'make sidebar boxed on the Archive page',
			'hide primary header on the Landing Page',
			'disable below header for the Home page',
			'remove mobile header from the Contact page',
			'set normal container for the About page',
			'disable title for the Sample Page',
			'enable transparent header on the Home page',
			'hide breadcrumbs on the Blog page',
			'remove header from the Hello World post',
			'disable footer on the Contact page',
			'make narrow container for the Archive page',
			'set no sidebar for the Sample Page',
			'hide banner area on the About page',
			'disable transparent header on the Blog page',
			'set unboxed content for the Home page',
			'make boxed sidebar on the Contact page',
			'hide above header row on the Landing page',
			'disable main header on the Archive page',
			'remove below header from the Sample Page',
			'set container layout normal for post ID 123',
			'disable title on page ID 456',
			'enable transparent header for post ID 789',
			'hide footer on page ID 234',
			'set full width container for post ID 567',
			'remove sidebar from page ID 890',
			'disable header on post ID 345',
			'make narrow container for page ID 678',
			'set left sidebar for post ID 901',
			'hide breadcrumb on page ID 123',
			'disable banner on post ID 456',
		);
	}

	/**
	 * Check permissions.
	 *
	 * @param WP_REST_Request $request REST Request.
	 * @return bool
	 */
	public function check_permission( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $args Input arguments.
	 * @return array Result array.
	 */
	public function execute( $args ) {
		if ( ! class_exists( 'Astra_Meta_Boxes' ) ) {
			return Astra_Abilities_Response::error(
				__( 'Astra theme is not active.', 'astra' ),
				__( 'Please activate the Astra theme to use this feature.', 'astra' )
			);
		}

		// Get post ID - either from post_id or by looking up post_title.
		$post_id = null;

		if ( isset( $args['post_id'] ) ) {
			$post_id = absint( $args['post_id'] );
		} elseif ( isset( $args['post_title'] ) ) {
			$post_title = sanitize_text_field( $args['post_title'] );
			$query      = new \WP_Query(
				array(
					'title'          => $post_title,
					'post_type'      => array( 'post', 'page' ),
					'posts_per_page' => 1,
					'post_status'    => 'any',
				)
			);

			$post = $query->have_posts() ? $query->posts[0] : null;

			if ( ! $post ) {
				// Try to find by searching all post types.
				$query = new \WP_Query(
					array(
						'title'          => $post_title,
						'post_type'      => 'any',
						'posts_per_page' => 1,
						'post_status'    => 'any',
					)
				);

				$post = $query->have_posts() ? $query->posts[0] : null;
			}

			if ( ! $post instanceof \WP_Post ) {
				return Astra_Abilities_Response::error(
					__( 'Post not found.', 'astra' ),
					/* translators: %s: post title */
					sprintf( __( 'Could not find a post or page with the title "%s".', 'astra' ), $post_title )
				);
			}

			$post_id = $post->ID;
		}

		if ( ! $post_id ) {
			return Astra_Abilities_Response::error(
				__( 'No post specified.', 'astra' ),
				__( 'Please provide either post_id or post_title to identify the post to update.', 'astra' )
			);
		}

		// Verify post exists.
		$post = get_post( $post_id );
		if ( ! $post ) {
			return Astra_Abilities_Response::error(
				__( 'Post not found.', 'astra' ),
				/* translators: %d: post ID */
				sprintf( __( 'Post with ID %d does not exist.', 'astra' ), $post_id )
			);
		}

		// Check user can edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return Astra_Abilities_Response::error(
				__( 'Permission denied.', 'astra' ),
				/* translators: %s: post title */
				sprintf( __( 'You do not have permission to edit "%s".', 'astra' ), $post->post_title )
			);
		}

		// Validate meta key.
		$valid_keys = array(
			'site-post-title',
			'ast-site-content-layout',
			'site-content-style',
			'site-sidebar-layout',
			'site-sidebar-style',
			'ast-global-header-display',
			'footer-sml-layout',
			'ast-banner-title-visibility',
			'ast-breadcrumbs-content',
			'ast-hfb-above-header-display',
			'ast-main-header-display',
			'ast-hfb-below-header-display',
			'ast-hfb-mobile-header-display',
			'theme-transparent-header-meta',
		);

		$key = isset( $args['key'] ) ? sanitize_text_field( $args['key'] ) : '';

		if ( ! in_array( $key, $valid_keys, true ) ) {
			return Astra_Abilities_Response::error(
				__( 'Invalid meta key.', 'astra' ),
				/* translators: 1: meta key, 2: list of valid keys */
				sprintf( __( 'The meta key "%1$s" is not valid. Valid keys are: %2$s', 'astra' ), $key, implode( ', ', $valid_keys ) )
			);
		}

		// Validate and sanitize value.
		$value = isset( $args['value'] ) ? sanitize_text_field( $args['value'] ) : '';

		// Get human-readable labels.
		$key_labels = array(
			'site-post-title'               => 'Disable Title',
			'ast-site-content-layout'       => 'Container Layout',
			'site-content-style'            => 'Container Style',
			'site-sidebar-layout'           => 'Sidebar Layout',
			'site-sidebar-style'            => 'Sidebar Style',
			'ast-global-header-display'     => 'Disable Header',
			'footer-sml-layout'             => 'Disable Footer',
			'ast-banner-title-visibility'   => 'Disable Banner Area',
			'ast-breadcrumbs-content'       => 'Disable Breadcrumb',
			'ast-hfb-above-header-display'  => 'Disable Above Header',
			'ast-main-header-display'       => 'Disable Primary Header',
			'ast-hfb-below-header-display'  => 'Disable Below Header',
			'ast-hfb-mobile-header-display' => 'Disable Mobile Header',
			'theme-transparent-header-meta' => 'Transparent Header',
		);

		$value_label = $this->get_value_label( $key, $value );
		$key_label   = isset( $key_labels[ $key ] ) ? $key_labels[ $key ] : $key;

		// Update the meta.
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}

		return Astra_Abilities_Response::success(
			sprintf(
				/* translators: 1: setting label, 2: value label, 3: post title, 4: post ID */
				__( 'Updated "%1$s" to "%2$s" for "%3$s" (ID: %4$d)', 'astra' ),
				$key_label,
				$value_label,
				$post->post_title,
				$post_id
			),
			array(
				'post_id'     => $post_id,
				'post_title'  => $post->post_title,
				'meta_key'    => $key,
				'meta_value'  => $value,
				'key_label'   => $key_label,
				'value_label' => $value_label,
			)
		);
	}

	/**
	 * Get human-readable value label.
	 *
	 * @param string $key   Meta key.
	 * @param string $value Meta value.
	 * @return string
	 */
	private function get_value_label( $key, $value ) {
		$value_labels = array(
			'site-post-title'               => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-site-content-layout'       => array(
				'default'                => 'Customizer Setting',
				'normal-width-container' => 'Normal',
				'narrow-width-container' => 'Narrow',
				'full-width-container'   => 'Full Width',
			),
			'site-content-style'            => array(
				'default' => 'Default',
				'unboxed' => 'Unboxed',
				'boxed'   => 'Boxed',
			),
			'site-sidebar-layout'           => array(
				'default'       => 'Customizer Setting',
				'no-sidebar'    => 'No Sidebar',
				'left-sidebar'  => 'Left Sidebar',
				'right-sidebar' => 'Right Sidebar',
			),
			'site-sidebar-style'            => array(
				'default' => 'Default',
				'unboxed' => 'Unboxed',
				'boxed'   => 'Boxed',
			),
			'ast-global-header-display'     => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'footer-sml-layout'             => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-banner-title-visibility'   => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-breadcrumbs-content'       => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-hfb-above-header-display'  => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-main-header-display'       => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-hfb-below-header-display'  => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'ast-hfb-mobile-header-display' => array(
				''         => 'Default',
				'disabled' => 'Disabled',
			),
			'theme-transparent-header-meta' => array(
				'default'  => 'Inherit',
				'enabled'  => 'Enabled',
				'disabled' => 'Disabled',
			),
		);

		if ( isset( $value_labels[ $key ][ $value ] ) ) {
			return $value_labels[ $key ][ $value ];
		}

		return $value ? $value : 'Default';
	}
}

Astra_Update_Postmeta::register();
