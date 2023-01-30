<?php
/**
 * Enqueue plugin assets
 *
 * @since   0.0.1
 * @package BU Learning Blocks
 */

namespace BU\Plugins\LearningBlocks;

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-element`: WordPress wrapper for React libraries.
 *
 * @since 0.0.1
 */
function bulb_block_assets() {
	// Frontend Scripts.
	if ( ! is_admin() ) { // If not on an admin page, enqueue JavaScript for the front-end view.
		wp_enqueue_script(
			'bulb-frontend-js',
			BULB_PLUGIN_URL . 'build/frontend/frontend.build.js', // Minified JS file, built with Webpack.
			array( 'wp-element', 'wp-i18n' ), // Dependency 'wp-element' loads react and react-dom in the frontend view.
			filemtime( plugin_dir_path( __DIR__ ) . 'build/frontend/frontend.build.js' ), // Gets file modification time for cache busting.
			true // Enqueue the script in the footer.
		);
	}

	global $wp_version;
	if ( version_compare( $wp_version, '5.0.0', '>=' ) ) {
		wp_set_script_translations( 'bulb-frontend-js', 'bu-learning-blocks' );
	}

	// Shared Frontend/Editor Styles.
	wp_enqueue_style(
		'bulb-block-style-css',
		BULB_PLUGIN_URL . 'build/frontend/frontend.css', // Block style CSS.
		array(),
		filemtime( plugin_dir_path( __DIR__ ) . 'build/frontend/frontend.css' ) // Gets file modification time for cache busting.
	);
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\bulb_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function bulb_block_editor_assets() {
	wp_enqueue_script(
		'bulb-block-js',
		BULB_PLUGIN_URL . 'build/blocks/blocks.build.js', // Minified JS file, built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
		filemtime( plugin_dir_path( __DIR__ ) . 'build/blocks/blocks.build.js' ), // Gets file modification time for cach busting.
		true // Enqueue the script in the footer.
	);

	global $wp_version;
	if ( version_compare( $wp_version, '5.0.0', '>=' ) ) {
		wp_set_script_translations( 'bulb-block-js', 'bu-learning-blocks' );
	}

	// Styles.
	wp_enqueue_style(
		'bulb-block-editor-css',
		BULB_PLUGIN_URL . 'build/blocks/blocks.css', // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		filemtime( plugin_dir_path( __DIR__ ) . 'build/blocks/blocks.css' ) // Gets file modification time for cache busting.
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\bulb_block_editor_assets' );
