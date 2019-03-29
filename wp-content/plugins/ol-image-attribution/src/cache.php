<?php
/**
 * Caching helpers.
 */

namespace OpenLab\ImageAttribution\Cache;

use function OpenLab\ImageAttribution\Helpers\get_supported_post_types;

/**
 * Delete attached media cache on post update.
 *
 * @param int $post_id
 * @param WP_Post $post
 * @return void
 */
function clear_attached_media_cache( $post_id, $post ) {
	if ( ! in_array( $post->post_type, get_supported_post_types(), true ) ) {
		return false;
	}

	delete_post_meta( $post_id, '_wp_attached_media_cache' );
}
add_action( 'edit_post', __NAMESPACE__ . '\\clear_attached_media_cache', 10, 2 );