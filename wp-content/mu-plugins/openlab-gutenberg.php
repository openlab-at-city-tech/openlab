<?php
/**
 * Gutenberg modifications for OpenLab network.
 *
 * @package OpenLab\Gutenberg
 */

namespace OpenLab\Gutenberg;

/**
 * Disable Block-Based Widgets screen.
 *
 * Introduced in Gutenberg 8.9.
 */
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );

/**
 * Enqueue Block Editor assets.
 *
 * @return void
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_script(
		'openlab-gutenberg',
		plugins_url( 'js/openlab-gutenberg.js', __FILE__ ),
		[ 'wp-data', 'wp-dom-ready' ],
		'1.0.0',
		true
	);
} );
