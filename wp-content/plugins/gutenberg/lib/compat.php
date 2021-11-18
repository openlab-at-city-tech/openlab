<?php
/**
 * Temporary compatibility shims for features present in Gutenberg, pending
 * upstream commit to the WordPress core source repository. Functions here
 * exist only as long as necessary for corresponding WordPress support, and
 * each should be associated with a Trac ticket.
 *
 * @package gutenberg
 */

/**
 * Backporting wp_should_load_separate_core_block_assets from WP-Core.
 *
 * @todo Remove this function when the minimum supported version is WordPress 5.8.
 */
if ( ! function_exists( 'wp_should_load_separate_core_block_assets' ) ) {
	/**
	 * Checks whether separate assets should be loaded for core blocks on-render.
	 *
	 * @since 5.8.0
	 *
	 * @return bool Whether separate assets will be loaded.
	 */
	function wp_should_load_separate_core_block_assets() {
		if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		/**
		 * Filters the flag that decides whether separate scripts and styles
		 * will be loaded for core blocks on-render.
		 *
		 * @since 5.8.0
		 *
		 * @param bool $load_separate_assets Whether separate assets will be loaded.
		 *                                   Default false.
		 */
		return apply_filters( 'should_load_separate_core_block_assets', false );
	}
}

/**
 * Opt-in to separate styles loading for block themes in WordPress 5.8.
 *
 * @todo Remove this function when the minimum supported version is WordPress 5.8.
 */
add_filter(
	'separate_core_block_assets',
	function( $load_separate_styles ) {
		if ( function_exists( 'gutenberg_is_fse_theme' ) && gutenberg_is_fse_theme() ) {
			return true;
		}
		return $load_separate_styles;
	}
);

/**
 * Remove the `wp_enqueue_registered_block_scripts_and_styles` hook if needed.
 *
 * @return void
 */
function gutenberg_remove_hook_wp_enqueue_registered_block_scripts_and_styles() {
	if ( wp_should_load_separate_core_block_assets() ) {
		/**
		 * Avoid enqueueing block assets of all registered blocks for all posts, instead
		 * deferring to block render mechanics to enqueue scripts, thereby ensuring only
		 * blocks of the content have their assets enqueued.
		 *
		 * This can be removed once minimum support for the plugin is outside the range
		 * of the version associated with closure of the following ticket.
		 *
		 * @see https://core.trac.wordpress.org/ticket/50328
		 *
		 * @see WP_Block::render
		 */
		remove_action( 'enqueue_block_assets', 'wp_enqueue_registered_block_scripts_and_styles' );
	}
}

add_action( 'init', 'gutenberg_remove_hook_wp_enqueue_registered_block_scripts_and_styles' );

/**
 * Callback hooked to the register_block_type_args filter.
 *
 * This hooks into block registration to inject the default context into the block object.
 * It can be removed once the default context is added into Core.
 *
 * @param array $args Block attributes.
 * @return array Block attributes.
 */
function gutenberg_inject_default_block_context( $args ) {
	if ( is_callable( $args['render_callback'] ) ) {
		$block_render_callback   = $args['render_callback'];
		$args['render_callback'] = function( $attributes, $content, $block = null ) use ( $block_render_callback ) {
			global $post;

			// Check for null for back compatibility with WP_Block_Type->render
			// which is unused since the introduction of WP_Block class.
			//
			// See:
			// - https://core.trac.wordpress.org/ticket/49927
			// - commit 910de8f6890c87f93359c6f2edc6c27b9a3f3292 at wordpress-develop.

			if ( null === $block ) {
				return $block_render_callback( $attributes, $content );
			}

			$registry   = WP_Block_Type_Registry::get_instance();
			$block_type = $registry->get_registered( $block->name );

			// For WordPress versions that don't support the context API.
			if ( ! $block->context ) {
				$block->context = array();
			}

			// Inject the post context if not done by Core.
			$needs_post_id = ! empty( $block_type->uses_context ) && in_array( 'postId', $block_type->uses_context, true );
			if ( $post instanceof WP_Post && $needs_post_id && ! isset( $block->context['postId'] ) && 'wp_template' !== $post->post_type && 'wp_template_part' !== $post->post_type ) {
				$block->context['postId'] = $post->ID;
			}
			$needs_post_type = ! empty( $block_type->uses_context ) && in_array( 'postType', $block_type->uses_context, true );
			if ( $post instanceof WP_Post && $needs_post_type && ! isset( $block->context['postType'] ) && 'wp_template' !== $post->post_type && 'wp_template_part' !== $post->post_type ) {
				/*
				* The `postType` context is largely unnecessary server-side, since the
				* ID is usually sufficient on its own. That being said, since a block's
				* manifest is expected to be shared between the server and the client,
				* it should be included to consistently fulfill the expectation.
				*/
				$block->context['postType'] = $post->post_type;
			}

			return $block_render_callback( $attributes, $content, $block );
		};
	}
	return $args;
}

add_filter( 'register_block_type_args', 'gutenberg_inject_default_block_context' );

/**
 * Override post type labels for Reusable Block custom post type.
 * The labels are different from the ones in Core.
 *
 * Remove this when Core receives the new labels (minimum supported version WordPress 5.8)
 *
 * @return array Array of new labels for Reusable Block post type.
 */
function gutenberg_override_reusable_block_post_type_labels() {
	return array(
		'name'                     => _x( 'Reusable blocks', 'post type general name', 'gutenberg' ),
		'singular_name'            => _x( 'Reusable block', 'post type singular name', 'gutenberg' ),
		'menu_name'                => _x( 'Reusable blocks', 'admin menu', 'gutenberg' ),
		'name_admin_bar'           => _x( 'Reusable block', 'add new on admin bar', 'gutenberg' ),
		'add_new'                  => _x( 'Add New', 'Reusable block', 'gutenberg' ),
		'add_new_item'             => __( 'Add new Reusable block', 'gutenberg' ),
		'new_item'                 => __( 'New Reusable block', 'gutenberg' ),
		'edit_item'                => __( 'Edit Reusable block', 'gutenberg' ),
		'view_item'                => __( 'View Reusable block', 'gutenberg' ),
		'all_items'                => __( 'All Reusable blocks', 'gutenberg' ),
		'search_items'             => __( 'Search Reusable blocks', 'gutenberg' ),
		'not_found'                => __( 'No reusable blocks found.', 'gutenberg' ),
		'not_found_in_trash'       => __( 'No reusable blocks found in Trash.', 'gutenberg' ),
		'filter_items_list'        => __( 'Filter reusable blocks list', 'gutenberg' ),
		'items_list_navigation'    => __( 'Reusable blocks list navigation', 'gutenberg' ),
		'items_list'               => __( 'Reusable blocks list', 'gutenberg' ),
		'item_published'           => __( 'Reusable block published.', 'gutenberg' ),
		'item_published_privately' => __( 'Reusable block published privately.', 'gutenberg' ),
		'item_reverted_to_draft'   => __( 'Reusable block reverted to draft.', 'gutenberg' ),
		'item_scheduled'           => __( 'Reusable block scheduled.', 'gutenberg' ),
		'item_updated'             => __( 'Reusable block updated.', 'gutenberg' ),
	);
}
add_filter( 'post_type_labels_wp_block', 'gutenberg_override_reusable_block_post_type_labels', 10, 0 );

/**
 * Update allowed inline style attributes list.
 *
 * Note: This should be removed when the minimum required WP version is >= 5.8.
 *
 * @param string[] $attrs Array of allowed CSS attributes.
 * @return string[] CSS attributes.
 */
function gutenberg_safe_style_attrs( $attrs ) {
	$attrs[] = 'object-position';
	$attrs[] = 'border-top-left-radius';
	$attrs[] = 'border-top-right-radius';
	$attrs[] = 'border-bottom-right-radius';
	$attrs[] = 'border-bottom-left-radius';

	return $attrs;
}
add_filter( 'safe_style_css', 'gutenberg_safe_style_attrs' );
