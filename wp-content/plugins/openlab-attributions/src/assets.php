<?php

namespace OpenLab\Attributions\Assets;

use const OpenLab\Attributions\ROOT_FILE;
use function OpenLab\Attributions\Helpers\get_licenses;

/**
 * Equeue "Block Editor" assets.
 *
 * @return void
 */
function block_editor_assets() {
	wp_enqueue_script(
		'ol-attribution-js',
		plugins_url( '/build/js/block-editor.js', ROOT_FILE ),
		[ 'wp-blocks', 'wp-editor', 'wp-element', 'wp-components', 'wp-rich-text' ],
		'20190904'
	);

	wp_enqueue_style(
		'ol-attribution-styles',
		plugins_url( '/build/css/editor.css', ROOT_FILE ),
		[],
		'20190904'
	);

	wp_localize_script( 'ol-attribution-js', 'attrLicenses', get_licenses() );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\block_editor_assets' );

/**
 * Initialize "Classic Editor" functionality.
 *
 * @return void
 */
function classic_editor_init() {
	if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( 'true' !== get_user_option( 'rich_editing' ) ) {
		return;
	}

	add_filter( 'mce_buttons', __NAMESPACE__ . '\\add_tinymce_button' );
	add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_tinymce_plugins' );
	add_action( 'wp_enqueue_editor', __NAMESPACE__ . '\\classic_editor_assets' );
}
add_action( 'admin_head', __NAMESPACE__ . '\\classic_editor_init' );

/**
 * Enqueue "Classic Editor" assets.
 *
 * @return void
 */
function classic_editor_assets() {
	wp_enqueue_style(
		'ol-attribution-styles',
		plugins_url( '/build/css/editor.css', ROOT_FILE ),
		[],
		'20190904'
	);

	wp_localize_script( 'editor', 'attrLicenses', get_licenses() );
}

/**
 * Register TinyMCE button.
 *
 * @param array $buttons
 * @return void
 */
function add_tinymce_button( $buttons ) {
	array_push( $buttons, 'olAttrButton' );

	return $buttons;
}

/**
 * Register the Tiny MCE plugin.
 *
 * @param array $plugins
 * @return array
 */
function add_tinymce_plugins( array $plugins = [] ) {
	$plugins['olAttrButton'] = plugins_url( '/build/js/classic-editor.js', ROOT_FILE );

	return $plugins;
}
