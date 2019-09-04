<?php

namespace OpenLab\Attributions\Meta;

/**
 * Register our meta.
 *
 * @return void
 */
function register() {
	register_meta( 'post', 'citations', [
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	] );
}
add_action( 'init', __NAMESPACE__ . '\\register' );

/**
 * Currently there's no need to stub `get_post_metadata`, since we'll store
 * post meta values as JSON encoded strings.
 *
 * There won't be a need to use JSON encoded string after WP 5.3.
 * @link https://core.trac.wordpress.org/ticket/43392
 */

/**
 * Stub update metadata and return meta for images.
 *
 * @param null|bool $check
 * @param int       $object_id
 * @param string    $meta_key
 * @param mixed     $meta_value
 * @return null|bool
 */
function stub_update_metadata( $check, $object_id, $meta_key, $meta_value ) {
	if ( $meta_key !== 'citations' ) {
		return $check;
	}

	// Decode JSON string and sync updated citations to attachments.

	// Continue and save JSON string.
	return null;
}
add_filter( 'update_post_metadata', __NAMESPACE__ . '\\stub_update_metadata', 10, 4 );
