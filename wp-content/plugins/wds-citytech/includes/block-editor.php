<?php

/**
 * Registers blocks that must be registered on the server side.
 *
 * This includes any blocks with server-side rendering.
 */
function openlab_register_blocks() {
	$block_types = [
		'openlab-help',
		'openlab-support',
	];

	$blocks_dir        = WDS_CITYTECH_DIR . '/build/';
	$blocks_asset_file = include $blocks_dir . 'index.asset.php';

	foreach ( $block_types as $block_type ) {
		$block_dir = $blocks_dir . 'blocks/' . $block_type . '/';

		$block_php  = $block_dir . $block_type . '.php';
		$block_json = $block_dir . 'block.json';

		register_block_type_from_metadata(
			$block_json,
			require $block_php
		);
	}
}
add_action( 'init', 'openlab_register_blocks' );

/**
 * Enqueues block assets for the Dashboard.
 */
function openlab_enqueue_block_assets() {
	$blocks_dir        = WDS_CITYTECH_DIR . '/build/';
	$blocks_asset_file = include $blocks_dir . 'index.asset.php';

	// Replace "wp-blockEditor" with "wp-block-editor".
	$blocks_asset_file['dependencies'] = array_replace(
		$blocks_asset_file['dependencies'],
		array_fill_keys(
			array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor', true ),
			'wp-block-editor'
		)
	);

	wp_enqueue_script(
		'openlab-blocks',
		WDS_CITYTECH_URL . 'build/index.js',
		$blocks_asset_file['dependencies'],
		$blocks_asset_file['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'openlab_enqueue_block_assets' );

/**
 * Renders a block template.
 */
function openlab_render_block( $block_name, $raw_atts, $content = '', $block = null ) {
	$atts = is_array( $raw_atts ) ? $raw_atts : [];

	$template_args = array_merge(
		$atts,
		[
			'content' => $content,
			'block'   => $block,
		]
	);

	ob_start();
	load_template( WDS_CITYTECH_DIR . '/templates/blocks/' . $block_name . '.php', false, $template_args );
	$contents = ob_get_contents();
	ob_end_clean();

	return $contents;
}
