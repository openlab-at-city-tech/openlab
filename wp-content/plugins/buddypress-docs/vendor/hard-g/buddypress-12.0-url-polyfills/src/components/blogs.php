<?php

/**
 * BuddyPress 12.0 URL polyfills - Blogs component.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bp_blogs_directory_url' ) ) :
/**
 * Output Blogs directory's URL.
 *
 * @return void
 */
function bp_blogs_directory_url() {
	echo esc_url( bp_get_blogs_directory_url() );
}
endif;

if ( ! function_exists( 'bp_get_blogs_directory_url' ) ) :
/**
 * Returns the Blogs directory's URL.
 *
 * @since 12.0.0
 *
 * @param array $path_chunks {
 *     An array of arguments. Optional.
 *
 *     @type int $create_single_item `1` to get the Blogs create link.
 * }
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_get_blogs_directory_url( $path_chunks = array() ) {
	$directory_url = bp_get_blogs_directory_permalink();

	if ( ! empty( $path_chunks['create_single_item'] ) ) {
		$directory_url = trailingslashit( $directory_url . 'create' );
	}

	return $directory_url;
}
endif;
