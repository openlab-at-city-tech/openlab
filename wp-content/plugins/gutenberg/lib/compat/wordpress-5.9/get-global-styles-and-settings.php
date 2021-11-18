<?php
/**
 * API to interact with global settings & styles.
 *
 * @package gutenberg
 */

/**
 * Function to get the settings resulting of merging core, theme, and user data.
 *
 * @param array  $path              Path to the specific setting to retrieve. Optional.
 *                                  If empty, will return all settings.
 * @param string $block_name        Which block to retrieve the settings from. Optional
 *                                  If empty, it'll return the settings for the global context.
 * @param string $origin            Which origin to take data from. Optional.
 *                                  It can be 'all' (core, theme, and user) or 'base' (core and theme).
 *                                  If empty or unknown, 'all' is used.
 *
 * @return array The settings to retrieve.
 */
function gutenberg_get_global_settings( $path = array(), $block_name = '', $origin = 'all' ) {
	if ( '' !== $block_name ) {
		$path = array_merge( array( 'blocks', $block_name ), $path );
	}

	if ( 'base' === $origin ) {
		$origin = 'theme';
	} else {
		$origin = 'user';
	}

	$settings = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data( $origin )->get_settings();

	return _wp_array_get( $settings, $path, $settings );
}

/**
 * Function to get the styles resulting of merging core, theme, and user data.
 *
 * @param array  $path              Path to the specific style to retrieve. Optional.
 *                                  If empty, will return all styles.
 * @param string $block_name        Which block to retrieve the styles from. Optional.
 *                                  If empty, it'll return the styles for the global context.
 * @param string $origin            Which origin to take data from. Optional.
 *                                  It can be 'all' (core, theme, and user) or 'base' (core and theme).
 *                                  If empty or unknown, 'all' is used.
 *
 * @return array The styles to retrieve.
 */
function gutenberg_get_global_styles( $path = array(), $block_name = '', $origin = 'all' ) {
	if ( '' !== $block_name ) {
		$path = array_merge( array( 'blocks', $block_name ), $path );
	}

	if ( 'base' === $origin ) {
		$origin = 'theme';
	} else {
		$origin = 'user';
	}

	$styles = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data( $origin )->get_raw_data()['styles'];

	return _wp_array_get( $styles, $path, $styles );
}

/**
 * Returns the stylesheet resulting of merging core, theme, and user data.
 *
 * @param array $types Types of styles to load. Optional.
 *                     It accepts 'variables', 'styles', 'presets' as values.
 *                     If empty, it'll load all for themes with theme.json support
 *                     and only [ 'variables', 'presets' ] for themes without theme.json support.
 *
 * @return string Stylesheet.
 */
function gutenberg_get_global_stylesheet( $types = array() ) {
	// Return cached value if it can be used and exists.
	// It's cached by theme to make sure that theme switching clears the cache.
	$can_use_cached = (
		( empty( $types ) ) &&
		( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) &&
		( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) &&
		( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) &&
		! is_admin()
	);
	$transient_name = 'gutenberg_global_styles_' . get_stylesheet();
	if ( $can_use_cached ) {
		$cached = get_transient( $transient_name );
		if ( $cached ) {
			return $cached;
		}
	}

	$supports_theme_json = WP_Theme_JSON_Resolver_Gutenberg::theme_has_support();
	$supports_link_color = get_theme_support( 'experimental-link-color' );
	if ( empty( $types ) && ! $supports_theme_json ) {
		$types = array( 'variables', 'presets' );
	} elseif ( empty( $types ) ) {
		$types = array( 'variables', 'styles', 'presets' );
	}

	$origins = array( 'core', 'theme', 'user' );
	if ( ! $supports_theme_json && ! $supports_link_color ) {
		// In this case we only enqueue the core presets (CSS Custom Properties + the classes).
		$origins = array( 'core' );
	} elseif ( ! $supports_theme_json && $supports_link_color ) {
		// For the legacy link color feature to work, the CSS Custom Properties
		// should be in scope (either the core or the theme ones).
		$origins = array( 'core', 'theme' );
	}

	$tree       = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data();
	$stylesheet = $tree->get_stylesheet( $types, $origins );

	if ( $can_use_cached ) {
		// Cache for a minute.
		// This cache doesn't need to be any longer, we only want to avoid spikes on high-traffic sites.
		set_transient( $transient_name, $stylesheet, MINUTE_IN_SECONDS );
	}

	return $stylesheet;
}
