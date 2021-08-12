<?php
/**
 * Block Editor API.
 *
 * @package Gutenberg
 * @subpackage Editor
 * @since 10.5.0
 */

/**
 * Returns the list of default categories for block types.
 *
 * This is a temporary solution until the Gutenberg plugin sets
 * the required WordPress version to 5.8.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.5.0.
 *
 * @return array[] Array of categories for block types.
 */
function gutenberg_get_default_block_categories() {
	return array(
		array(
			'slug'  => 'text',
			'title' => _x( 'Text', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'media',
			'title' => _x( 'Media', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'design',
			'title' => _x( 'Design', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'widgets',
			'title' => _x( 'Widgets', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'theme',
			'title' => _x( 'Theme', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'embed',
			'title' => _x( 'Embeds', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
		array(
			'slug'  => 'reusable',
			'title' => _x( 'Reusable Blocks', 'block category', 'gutenberg' ),
			'icon'  => null,
		),
	);
}

/**
 * Returns all the categories for block types that will be shown in the block editor.
 *
 * This is a temporary solution until the Gutenberg plugin sets
 * the required WordPress version to 5.8.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.5.0
 *
 * @param WP_Post|WP_Block_Editor_Context $post_or_block_editor_context The current post object or
 *                                                                      the block editor context.
 * @return array[] Array of categories for block types.
 */
function gutenberg_get_block_categories( $post_or_block_editor_context ) {
	$block_categories     = gutenberg_get_default_block_categories();
	$block_editor_context = $post_or_block_editor_context instanceof WP_Post ?
		new WP_Block_Editor_Context(
			array(
				'post' => $post_or_block_editor_context,
			)
		) : $post_or_block_editor_context;

	/**
	 * Filters the default array of categories for block types.
	 *
	 * @since 5.8.0
	 *
	 * @param array[]                 $block_categories     Array of categories for block types.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	$block_categories = apply_filters( 'block_categories_all', $block_categories, $block_editor_context );
	if ( ! empty( $block_editor_context->post ) ) {
		$post = $block_editor_context->post;

		/**
		 * Filters the default array of categories for block types.
		 *
		 * @since 5.0.0
		 * @deprecated 5.8.0 The hook transitioned to support also screens that don't contain the $post instance.
		 *
		 * @param array[] $block_categories Array of categories for block types.
		 * @param WP_Post $post             Post being loaded.
		 */
		$block_categories = apply_filters_deprecated( 'block_categories', array( $block_categories, $post ), '5.8.0', 'block_categories_all' );
	}

	return $block_categories;
}

/**
 * Gets the list of allowed block types to use in the block editor.
 *
 * This is a temporary solution until the Gutenberg plugin sets
 * the required WordPress version to 5.8.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.5.0
 *
 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
 *
 * @return bool|array Array of block type slugs, or boolean to enable/disable all.
 */
function gutenberg_get_allowed_block_types( $block_editor_context ) {
	$allowed_block_types = true;

	/**
	 * Filters the allowed block types for all editor types, defaulting to `true`
	 * (all registered block types supported).
	 *
	 * @since 5.8.0
	 *
	 * @param bool|array              $allowed_block_types  Array of block type slugs, or
	 *                                                      boolean to enable/disable all.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	$allowed_block_types = apply_filters( 'allowed_block_types_all', $allowed_block_types, $block_editor_context );
	if ( ! empty( $block_editor_context->post ) ) {
		$post = $block_editor_context->post;

		/**
		 * Filters the allowed block types for the editor, defaulting to true (all
		 * block types supported).
		 *
		 * @since 5.0.0
		 * @deprecated 5.8.0 The hook transitioned to support also screens that don't contain $post instance.
		 *
		 * @param bool|array $allowed_block_types Array of block type slugs, or
		 *                                        boolean to enable/disable all.
		 * @param WP_Post    $post                The post resource data.
		 */
		$allowed_block_types = apply_filters_deprecated( 'allowed_block_types', array( $allowed_block_types, $post ), '5.8.0', 'allowed_block_types_all' );
	}

	return $allowed_block_types;
}

/**
 * Returns the default block editor settings.
 *
 * This is a temporary solution until the Gutenberg plugin sets
 * the required WordPress version to 5.8.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.5.0
 *
 * @return array The default block editor settings.
 */
function gutenberg_get_default_block_editor_settings() {
	// Media settings.
	$max_upload_size = wp_max_upload_size();
	if ( ! $max_upload_size ) {
		$max_upload_size = 0;
	}

	/** This filter is documented in wp-admin/includes/media.php */
	$image_size_names = apply_filters(
		'image_size_names_choose',
		array(
			'thumbnail' => __( 'Thumbnail', 'gutenberg' ),
			'medium'    => __( 'Medium', 'gutenberg' ),
			'large'     => __( 'Large', 'gutenberg' ),
			'full'      => __( 'Full Size', 'gutenberg' ),
		)
	);

	$available_image_sizes = array();
	foreach ( $image_size_names as $image_size_slug => $image_size_name ) {
		$available_image_sizes[] = array(
			'slug' => $image_size_slug,
			'name' => $image_size_name,
		);
	}

	$default_size       = get_option( 'image_default_size', 'large' );
	$image_default_size = in_array( $default_size, array_keys( $image_size_names ), true ) ? $default_size : 'large';

	$image_dimensions = array();
	$all_sizes        = wp_get_registered_image_subsizes();
	foreach ( $available_image_sizes as $size ) {
		$key = $size['slug'];
		if ( isset( $all_sizes[ $key ] ) ) {
			$image_dimensions[ $key ] = $all_sizes[ $key ];
		}
	}

	$editor_settings = array(
		'__unstableEnableFullSiteEditingBlocks' => gutenberg_supports_block_templates(),
		'alignWide'                             => get_theme_support( 'align-wide' ),
		'allowedBlockTypes'                     => true,
		'allowedMimeTypes'                      => get_allowed_mime_types(),
		'blockCategories'                       => gutenberg_get_default_block_categories(),
		'disableCustomColors'                   => get_theme_support( 'disable-custom-colors' ),
		'disableCustomFontSizes'                => get_theme_support( 'disable-custom-font-sizes' ),
		'disableCustomGradients'                => get_theme_support( 'disable-custom-gradients' ),
		'enableCustomLineHeight'                => get_theme_support( 'custom-line-height' ),
		'enableCustomSpacing'                   => get_theme_support( 'custom-spacing' ),
		'enableCustomUnits'                     => get_theme_support( 'custom-units' ),
		'isRTL'                                 => is_rtl(),
		'imageDefaultSize'                      => $image_default_size,
		'imageDimensions'                       => $image_dimensions,
		'imageEditing'                          => true,
		'imageSizes'                            => $available_image_sizes,
		'maxUploadFileSize'                     => $max_upload_size,
	);

	// Theme settings.
	$color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );
	if ( false !== $color_palette ) {
		$editor_settings['colors'] = $color_palette;
	}

	$font_sizes = current( (array) get_theme_support( 'editor-font-sizes' ) );
	if ( false !== $font_sizes ) {
		$editor_settings['fontSizes'] = $font_sizes;
	}

	$gradient_presets = current( (array) get_theme_support( 'editor-gradient-presets' ) );
	if ( false !== $gradient_presets ) {
		$editor_settings['gradients'] = $gradient_presets;
	}

	return $editor_settings;
}

/**
 * Returns the contextualized block editor settings settings for a selected editor type.
 *
 * This is a temporary solution until the Gutenberg plugin sets
 * the required WordPress version to 5.8.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.5.0
 *
 * @param array                   $custom_settings      Custom settings to use with the given editor type.
 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
 *
 * @return array The contextualized block editor settings.
 */
function gutenberg_get_block_editor_settings( $custom_settings, $block_editor_context ) {
	$editor_settings = array_merge(
		gutenberg_get_default_block_editor_settings(),
		array(
			'allowedBlockTypes' => gutenberg_get_allowed_block_types( $block_editor_context ),
			'blockCategories'   => gutenberg_get_block_categories( $block_editor_context ),
		),
		$custom_settings
	);

	/**
	 * Filters the settings to pass to the block editor for all editor type.
	 *
	 * @since 5.8.0
	 *
	 * @param array                   $editor_settings      Default editor settings.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 */
	$editor_settings = apply_filters( 'block_editor_settings_all', $editor_settings, $block_editor_context );
	if ( ! empty( $block_editor_context->post ) ) {
		$post = $block_editor_context->post;

		/**
		 * Filters the settings to pass to the block editor.
		 *
		 * @since 5.0.0
		 * @deprecated 5.8.0 The hook transitioned to support also screens that don't contain $post instance.
		 *
		 * @param array   $editor_settings Default editor settings.
		 * @param WP_Post $post            Post being edited.
		 */
		$editor_settings = apply_filters_deprecated( 'block_editor_settings', array( $editor_settings, $post ), '5.8.0', 'block_editor_settings_all' );
	}

	return $editor_settings;
}

/**
 * Preloads common data used with the block editor by specifying an array of
 * REST API paths that will be preloaded for a given block editor context.
 *
 * @see https://core.trac.wordpress.org/ticket/52920
 *
 * @since 10.7.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param array                   $preload_paths        List of paths to preload.
 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
 *
 * @return void
 */
function gutenberg_block_editor_rest_api_preload( array $preload_paths, $block_editor_context ) {
	global $post;

	/**
	 * Filters the array of REST API paths that will be used to preloaded common data
	 * to use with the block editor.
	 *
	 * @since 5.8.0
	 *
	 * @param string[] $preload_paths Array of paths to preload.
	 */
	$preload_paths = apply_filters( 'block_editor_rest_api_preload_paths', $preload_paths, $block_editor_context );
	if ( ! empty( $block_editor_context->post ) ) {
		$selected_post = $block_editor_context->post;

		/**
		 * Preload common data by specifying an array of REST API paths that will be preloaded.
		 *
		 * Filters the array of paths that will be preloaded.
		 *
		 * @since 5.0.0
		 * @deprecated 5.8.0 The hook transitioned to support also screens that don't contain $post instance.
		 *
		 * @param string[] $preload_paths Array of paths to preload.
		 * @param WP_Post  $selected_post Post being edited.
		 */
		$preload_paths = apply_filters_deprecated( 'block_editor_preload_paths', array( $preload_paths, $selected_post ), '5.8.0', 'block_editor_rest_api_preload_paths' );
	}

	if ( empty( $preload_paths ) ) {
		return;
	}

	/*
	 * Ensure the global $post remains the same after API data is preloaded.
	 * Because API preloading can call the_content and other filters, plugins
	 * can unexpectedly modify $post.
	 */
	$backup_global_post = ! empty( $post ) ? clone $post : $post;

	$preload_data = array_reduce(
		$preload_paths,
		'rest_preload_api_request',
		array()
	);

	// Restore the global $post as it was before API preloading.
	$post = $backup_global_post;

	wp_add_inline_script(
		'wp-api-fetch',
		sprintf(
			'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );',
			wp_json_encode( $preload_data )
		),
		'after'
	);
}

/**
 * Filters the arguments for registering a block type.
 *
 * @todo Remove from the Gutenberg plugin when WordPress 5.8 is the minimum required version.
 *
 * @param array $args Array of arguments for registering a block type.
 *
 * @return array Returns the $metadata with any missing `style` and `editorStyle` added.
 */
function gutenberg_add_missing_styles_to_core_block_json( $args ) {
	if ( ! empty( $args['name'] ) && 0 === strpos( $args['name'], 'core/' ) ) {
		$block_name = str_replace( 'core/', '', $args['name'] );

		if ( ! isset( $args['style'] ) ) {
			$args['style'] = "wp-block-$block_name";
		}
		if ( ! isset( $args['editor_style'] ) ) {
			$args['editor_style'] = "wp-block-$block_name-editor";
		}
	}
	return $args;
}
add_filter( 'register_block_type_args', 'gutenberg_add_missing_styles_to_core_block_json' );
