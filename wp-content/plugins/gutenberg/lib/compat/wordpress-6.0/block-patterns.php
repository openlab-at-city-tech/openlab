<?php
/**
 * Block patterns registration from `theme.json` and Pattern Directory.
 *
 * @package gutenberg
 */

if ( ! function_exists( '_register_remote_theme_patterns' ) ) {
	/**
	 * Registers patterns from Pattern Directory provided by a theme's
	 * `theme.json` file.
	 */
	function _register_remote_theme_patterns() {
		if ( ! get_theme_support( 'core-block-patterns' ) ) {
			return;
		}

		if ( ! apply_filters( 'should_load_remote_block_patterns', true ) ) {
			return;
		}

		if ( ! WP_Theme_JSON_Resolver_Gutenberg::theme_has_support() ) {
			return;
		}

		$pattern_settings = WP_Theme_JSON_Resolver_Gutenberg::get_theme_data()->get_patterns();
		if ( empty( $pattern_settings ) ) {
			return;
		}

		$request         = new WP_REST_Request( 'GET', '/wp/v2/pattern-directory/patterns' );
		$request['slug'] = $pattern_settings;
		$response        = rest_do_request( $request );
		if ( $response->is_error() ) {
			return;
		}
		$patterns          = $response->get_data();
		$patterns_registry = WP_Block_Patterns_Registry::get_instance();
		foreach ( $patterns as $pattern ) {
			$pattern_name = sanitize_title( $pattern['title'] );
			// Some patterns might be already registered as core patterns with the `core` prefix.
			$is_registered = $patterns_registry->is_registered( $pattern_name ) || $patterns_registry->is_registered( "core/$pattern_name" );
			if ( ! $is_registered ) {
				register_block_pattern( $pattern_name, (array) $pattern );
			}
		}
	}
}
