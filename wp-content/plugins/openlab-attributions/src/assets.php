<?php

namespace OpenLab\Attributions\Assets;

use const OpenLab\Attributions\ROOT_DIR;
use const OpenLab\Attributions\ROOT_FILE;
use function OpenLab\Attributions\Helpers\get_licenses;
use function OpenLab\Attributions\Helpers\get_supported_post_types;

/**
 * Register our assets.
 *
 * @return void
 */
function register_assets() {
	$block_filepath   = ROOT_DIR . '/build/js/block-editor.asset.php';
	$block_asset_file = file_exists( $block_filepath ) ? include $block_filepath : [
		'dependencies' => [],
		'version'      => false,
	];

	$classic_filepath   = ROOT_DIR . '/build/js/block-editor.asset.php';
	$classic_asset_file = file_exists( $classic_filepath ) ? include $classic_filepath : [
		'dependencies' => [],
		'version'      => false,
	];

	wp_register_script(
		'attribution-block-script',
		plugins_url( '/build/js/block-editor.js', ROOT_FILE ),
		$block_asset_file['dependencies'],
		$block_asset_file['version']
	);

	wp_register_script(
		'attribution-classic-script',
		plugins_url( '/build/js/classic-editor.js', ROOT_FILE ),
		array_merge( $classic_asset_file['dependencies'], [ 'wp-tinymce' ] ),
		$classic_asset_file['version']
	);

	wp_register_style(
		'attribution-frontend-styles',
		plugins_url( '/build/css/style.css', ROOT_FILE ),
		[],
		'20191107'
	);

	wp_register_style(
		'attribution-editor-styles',
		plugins_url( '/build/css/editor.css', ROOT_FILE ),
		[ 'wp-components' ],
		'20191107'
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_assets' );

/**
 * Enqueue Attributions marker styles.
 *
 * @return void
 */
function frontend_assets() {
	wp_enqueue_style( 'attribution-frontend-styles' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\frontend_assets' );

/**
 * Equeue "Block Editor" assets.
 *
 * @return void
 */
function block_editor_assets() {
	$post = get_post();

	if ( ! in_array( $post->post_type, get_supported_post_types(), true ) ) {
		return;
	}

	wp_enqueue_script( 'attribution-block-script' );
	wp_enqueue_style( 'attribution-editor-styles' );
	wp_enqueue_style( 'attribution-frontend-styles' );

	$meta         = get_post_meta( $post->ID, 'attributions', true );
	$attributions = ! empty( $meta ) ? $meta : [];

	wp_localize_script( 'attribution-block-script', 'attrMeta', $attributions );
	wp_localize_script( 'attribution-block-script', 'attrLicenses', get_licenses() );
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
	add_filter( 'editor_stylesheets', __NAMESPACE__ . '\\editor_styles' );
	add_action( 'wp_enqueue_editor', __NAMESPACE__ . '\\classic_editor_assets' );
}
add_action( 'admin_head', __NAMESPACE__ . '\\classic_editor_init' );

/**
 * Enqueue "Classic Editor" assets.
 *
 * @return void
 */
function classic_editor_assets() {
	if ( ! is_admin() ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! in_array( $screen->post_type, get_supported_post_types(), true ) ) {
		return;
	}

	wp_enqueue_script( 'attribution-classic-script' );
	wp_enqueue_style( 'attribution-editor-styles' );

	$meta         = get_post_meta( get_post()->ID, 'attributions', true );
	$attributions = ! empty( $meta ) ? $meta : [];

	wp_localize_script( 'attribution-classic-script', 'attrMeta', $attributions );
	wp_localize_script( 'attribution-classic-script', 'attrLicenses', get_licenses() );
}

/**
 * Adds TinyMCE editor stylesheets.
 *
 * @param array $stylesheets
 * @return array
 */
function editor_styles( $stylesheets ) {
	$stylesheets[] = plugins_url( '/build/css/style.css', ROOT_FILE );

	return $stylesheets;
}

/**
 * Register TinyMCE button.
 *
 * @param array $buttons
 * @return void
 */
function add_tinymce_button( $buttons ) {
	array_push( $buttons, 'attribution-button' );

	return $buttons;
}

/**
 * Register a fake TinyMCE plugin.
 * The actual plugin code is enqueued later,
 * but we need this to add the button.
 *
 * @param array $plugins
 * @return array
 */
function add_tinymce_plugins( array $plugins = [] ) {
	$plugins['attribution-button'] = plugins_url( '/build/js/plugin.js', ROOT_FILE );

	return $plugins;
}
