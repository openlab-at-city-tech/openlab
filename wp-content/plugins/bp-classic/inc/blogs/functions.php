<?php
/**
 * BP Classic Blogs Functions.
 *
 * @package bp-classic\inc\blogs
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output blog directory permalink.
 *
 * @since 1.0.0
 */
function bp_blogs_directory_permalink() {
	bp_blogs_directory_url();
}

/**
 * Return blog directory permalink.
 *
 * @since 1.0.0
 *
 * @return string The URL of the Blogs directory.
 */
function bp_get_blogs_directory_permalink() {
	$url = bp_get_blogs_directory_url();

	/**
	 * Filters the blog directory permalink.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Permalink URL for the blog directory.
	 */
	return apply_filters( 'bp_get_blogs_directory_permalink', $url );
}
