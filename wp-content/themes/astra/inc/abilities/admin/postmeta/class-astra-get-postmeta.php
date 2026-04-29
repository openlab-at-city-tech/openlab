<?php
/**
 * Get Post Meta Settings Ability
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Get_Postmeta
 */
class Astra_Get_Postmeta extends Astra_Abstract_Ability {
	/**
	 * Configure the ability.
	 */
	public function configure() {
		$this->id          = 'astra/get-post-meta';
		$this->label       = __( 'Get Post-Specific Meta Settings (Individual Posts/Pages)', 'astra' );
		$this->description = __( 'Retrieves Astra meta settings for ONE SPECIFIC post or page by post title or ID. Shows current overrides for that individual post only. IMPORTANT: Use this ONLY when the user asks about a specific post/page name (like "Sample Page", "Hello World", "About") or post ID. This is NOT for global settings.', 'astra' );
		$this->category    = 'astra';
	}

	/**
	 * Get tool type.
	 *
	 * @return string
	 */
	public function get_tool_type() {
		return 'read';
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
				'post_id'    => array(
					'type'        => 'integer',
					'description' => 'Post ID to get meta for. Either post_id or post_title is required.',
				),
				'post_title' => array(
					'type'        => 'string',
					'description' => 'Post title to find and get meta for. Either post_id or post_title is required. Example: "Sample Page", "Hello World"',
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
			'show settings for the Sample Page',
			'get meta for the Hello World post',
			'what are the settings for the About page',
			'check meta values for the Contact page',
			'show post settings for the Home page',
			'get configuration for Sample Page',
			'what layout is set for the Hello World post',
			'check settings for post ID 123',
			'show meta for page ID 456',
			'get settings for the Landing Page',
			'is title hidden on the Sample Page',
			'check if header is disabled on the Hello World post',
			'what container is set for the About page',
			'show sidebar settings for the Contact page',
			'is footer hidden on the Home page',
			'check transparent header on the Blog page',
			'what are the overrides for the Sample Page',
			'show layout settings for the Hello World post',
			'get meta configuration for the About page',
			'is banner hidden on the Contact page',
			'check breadcrumb settings for the Home page',
			'what header rows are disabled on the Sample Page',
			'show all meta values for the Hello World post',
			'what settings are overridden on the About page',
			'check container style for the Contact page',
			'is sidebar disabled on the Home page',
			'what meta keys are set for the Sample Page',
			'show appearance settings for the Hello World post',
			'check if title is visible on the About page',
			'what is the sidebar position on the Contact page',
			'is transparent header enabled on the Home page',
			'show header visibility for the Sample Page',
			'check footer display on the Hello World post',
			'what layout options are active on the About page',
			'get page specific settings for the Contact page',
			'show current overrides for the Home page',
			'what container layout is the Sample Page using',
			'check content style for the Hello World post',
			'is above header hidden on the About page',
			'what sidebar style is set for the Contact page',
			'show primary header settings for the Home page',
			'check below header visibility on the Sample Page',
			'is mobile header disabled on the Hello World post',
			'what are the page-level settings for the About page',
			'show post-specific customizations for the Contact page',
			'get individual post settings for the Home page',
			'check page layout overrides for the Sample Page',
			'what meta settings exist for the Hello World post',
			'show current page meta for the About page',
			'get post design settings for the Contact page',
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
				__( 'Please provide either post_id or post_title to identify the post.', 'astra' )
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

		// Get meta box instance.
		$meta_boxes = Astra_Meta_Boxes::get_instance();

		// Get all available options.
		$content_layout_options = $meta_boxes->get_content_layout_options();
		$content_style_options  = $meta_boxes->get_content_style_options();
		$sidebar_layout_options = $meta_boxes->get_sidebar_options();
		$sidebar_style_options  = $meta_boxes->get_sidebar_style_options();
		$header_enabled_options = $meta_boxes->get_header_enabled_options();

		// Get current meta values.
		$post_title_meta         = get_post_meta( $post_id, 'site-post-title', true );
		$content_layout_meta     = get_post_meta( $post_id, 'ast-site-content-layout', true );
		$content_style_meta      = get_post_meta( $post_id, 'site-content-style', true );
		$sidebar_layout_meta     = get_post_meta( $post_id, 'site-sidebar-layout', true );
		$sidebar_style_meta      = get_post_meta( $post_id, 'site-sidebar-style', true );
		$header_display_meta     = get_post_meta( $post_id, 'ast-global-header-display', true );
		$footer_layout_meta      = get_post_meta( $post_id, 'footer-sml-layout', true );
		$banner_title_meta       = get_post_meta( $post_id, 'ast-banner-title-visibility', true );
		$breadcrumbs_meta        = get_post_meta( $post_id, 'ast-breadcrumbs-content', true );
		$above_header_meta       = get_post_meta( $post_id, 'ast-hfb-above-header-display', true );
		$main_header_meta        = get_post_meta( $post_id, 'ast-main-header-display', true );
		$below_header_meta       = get_post_meta( $post_id, 'ast-hfb-below-header-display', true );
		$mobile_header_meta      = get_post_meta( $post_id, 'ast-hfb-mobile-header-display', true );
		$transparent_header_meta = get_post_meta( $post_id, 'theme-transparent-header-meta', true );

		$meta_data = array(
			'site-post-title'               => array(
				'value'       => $post_title_meta,
				'label'       => 'Disable Title',
				'value_label' => $this->get_value_label( 'site-post-title', $post_title_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-site-content-layout'       => array(
				'value'       => $content_layout_meta,
				'label'       => 'Container Layout',
				'value_label' => $this->get_value_label( 'ast-site-content-layout', $content_layout_meta ),
				'options'     => $content_layout_options,
			),
			'site-content-style'            => array(
				'value'       => $content_style_meta,
				'label'       => 'Container Style',
				'value_label' => $this->get_value_label( 'site-content-style', $content_style_meta ),
				'options'     => $content_style_options,
			),
			'site-sidebar-layout'           => array(
				'value'       => $sidebar_layout_meta,
				'label'       => 'Sidebar Layout',
				'value_label' => $this->get_value_label( 'site-sidebar-layout', $sidebar_layout_meta ),
				'options'     => $sidebar_layout_options,
			),
			'site-sidebar-style'            => array(
				'value'       => $sidebar_style_meta,
				'label'       => 'Sidebar Style',
				'value_label' => $this->get_value_label( 'site-sidebar-style', $sidebar_style_meta ),
				'options'     => $sidebar_style_options,
			),
			'ast-global-header-display'     => array(
				'value'       => $header_display_meta,
				'label'       => 'Disable Header',
				'value_label' => $this->get_value_label( 'ast-global-header-display', $header_display_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'footer-sml-layout'             => array(
				'value'       => $footer_layout_meta,
				'label'       => 'Disable Footer',
				'value_label' => $this->get_value_label( 'footer-sml-layout', $footer_layout_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-banner-title-visibility'   => array(
				'value'       => $banner_title_meta,
				'label'       => 'Disable Banner Area',
				'value_label' => $this->get_value_label( 'ast-banner-title-visibility', $banner_title_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-breadcrumbs-content'       => array(
				'value'       => $breadcrumbs_meta,
				'label'       => 'Disable Breadcrumb',
				'value_label' => $this->get_value_label( 'ast-breadcrumbs-content', $breadcrumbs_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-hfb-above-header-display'  => array(
				'value'       => $above_header_meta,
				'label'       => 'Disable Above Header',
				'value_label' => $this->get_value_label( 'ast-hfb-above-header-display', $above_header_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-main-header-display'       => array(
				'value'       => $main_header_meta,
				'label'       => 'Disable Primary Header',
				'value_label' => $this->get_value_label( 'ast-main-header-display', $main_header_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-hfb-below-header-display'  => array(
				'value'       => $below_header_meta,
				'label'       => 'Disable Below Header',
				'value_label' => $this->get_value_label( 'ast-hfb-below-header-display', $below_header_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'ast-hfb-mobile-header-display' => array(
				'value'       => $mobile_header_meta,
				'label'       => 'Disable Mobile Header',
				'value_label' => $this->get_value_label( 'ast-hfb-mobile-header-display', $mobile_header_meta ),
				'options'     => array(
					''         => 'Default',
					'disabled' => 'Disabled',
				),
			),
			'theme-transparent-header-meta' => array(
				'value'       => $transparent_header_meta,
				'label'       => 'Transparent Header',
				'value_label' => $this->get_value_label( 'theme-transparent-header-meta', $transparent_header_meta ),
				'options'     => $header_enabled_options,
			),
		);

		return Astra_Abilities_Response::success(
			/* translators: 1: post title, 2: post ID */
			sprintf( __( 'Retrieved post meta settings for "%1$s" (ID: %2$d)', 'astra' ), $post->post_title, $post_id ),
			array(
				'post_id'    => $post_id,
				'post_title' => $post->post_title,
				'post_type'  => $post->post_type,
				'meta'       => $meta_data,
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

Astra_Get_Postmeta::register();
