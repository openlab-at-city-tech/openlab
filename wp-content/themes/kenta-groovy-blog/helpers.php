<?php
/**
 * Template helpers
 *
 * @package Kenta Groovy Blog
 */

if ( ! function_exists( 'kenta_groovy_blog_asset_url' ) ) {
	/**
	 * Get template assets file url
	 *
	 * @param $asset
	 *
	 * @return string
	 */
	function kenta_groovy_blog_asset_url( $asset ) {
		return KENTA_GROOVY_BLOG_ASSETS_URL . $asset;
	}
}

if ( ! function_exists( 'kenta_groovy_blog_pattern_markup' ) ) {
	/**
	 * Get pattern markup
	 *
	 * @param $name
	 * @param array $args
	 *
	 * @return false|string
	 */
	function kenta_groovy_blog_pattern_markup( $name, $args = array() ) {
		extract( $args );

		ob_start();
		include KENTA_GROOVY_BLOG_PATH . 'template-parts/patterns/' . sanitize_title( $name ) . '.php';

		return ob_get_clean();
	}
}

if ( ! function_exists( 'kenta_groovy_blog_starter_template' ) ) {
	/**
	 * Get pattern markup
	 *
	 * @param $name
	 *
	 * @return false|string
	 */
	function kenta_groovy_blog_starter_template( $name ) {
		ob_start();
		include KENTA_GROOVY_BLOG_PATH . 'template-parts/starter-templates/' . sanitize_title( $name ) . '.php';

		return ob_get_clean();
	}
}
