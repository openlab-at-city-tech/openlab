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

		// Default to the current domain only.
		$url_parts       = wp_parse_url( get_site_url() );
		$allowed_domains = [ $url_parts['scheme'] . '://' . $url_parts['host'] ];

		$data = [
			'allowedDomains' => apply_filters( 'h5p_postmessage_allowed_domains', $allowed_domains ),
		];

		$tags[] = sprintf(
			'<script>var h5pPostMessageData = %s;</script>',
			wp_json_encode( $data )
		);

		return $tags;
	}
);
