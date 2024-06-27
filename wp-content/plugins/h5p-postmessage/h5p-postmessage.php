<?php

/**
 * Plugin Name: H5P PostMessage
 * Description: Adds postMessage support to H5P.
 * Version: 1.0
 * Author: City Tech OpenLab
 * Author URI: https://openlab.citytech.cuny.edu
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function() {
	}
);

add_action(
	'h5p_additional_embed_head_tags',
	function( &$tags ) {
		$blocks_dir        = __DIR__ . '/build/';
		$blocks_asset_file = include $blocks_dir . 'frontend.asset.php';

		// Add the script to the tags.
		$tags[] = sprintf(
			'<script src="%s"></script>',
			plugin_dir_url( __FILE__ ) . 'build/frontend.js'
		);

		return $tags;
	}
);
