<?php
/**
 * Astra theme fixes.
 */

/**
 * Show Featured Image in Post Title Area by default.
 *
 * In the Customizer, the "Structure" sortable control under Post Types > [type]
 * > Post Title Area > Structure contains a toggle for the featured image (the
 * closed/open eye icon). Out of the box this is OFF for posts and most CPTs.
 *
 * This filter adds the image element to the structure defaults whenever no
 * explicit value has been saved to the database, so new or unconfigured post
 * types show the featured image automatically.
 *
 * Option key pattern  : ast-dynamic-single-{post_type}-structure
 * Image element key   : ast-dynamic-single-{post_type}-image
 */
add_filter( 'astra_get_option_array', 'openlab_astra_featured_image_in_title_default', 10, 3 );

function openlab_astra_featured_image_in_title_default( $options, $option, $default ) {
	// Only target the structure settings for single post-type title areas.
	if ( ! preg_match( '/^ast-dynamic-single-(.+)-structure$/', $option, $matches ) ) {
		return $options;
	}

	// If the user has explicitly saved this setting via the Customizer, respect it.
	$db_options = get_option( ASTRA_THEME_SETTINGS, array() );
	if ( isset( $db_options[ $option ] ) ) {
		return $options;
	}

	// Build the image element key for this post type.
	$post_type = $matches[1];
	$image_key = 'ast-dynamic-single-' . $post_type . '-image';

	// Resolve the current default value (may come from theme defaults or the caller's $default).
	$structure = isset( $options[ $option ] ) && is_array( $options[ $option ] )
		? $options[ $option ]
		: ( is_array( $default ) ? $default : array() );

	// Prepend the image key if not already present, matching the convention used for pages.
	if ( ! in_array( $image_key, $structure, true ) ) {
		array_unshift( $structure, $image_key );
		$options[ $option ] = $structure;
	}

	return $options;
}
