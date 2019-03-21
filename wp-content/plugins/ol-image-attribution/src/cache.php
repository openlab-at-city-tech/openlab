<?php
/**
 * Caching helpers.
 */

/**
 * Delete attached media cache on post update.
 *
 * @param int $post_id
 * @return void
 */
function clear_attached_media_cache( $post_id ) {
	delete_post_meta( $post_id, '_wp_attached_media_cache' );
}
add_action( 'edit_post', __NAMESPACE__ . '\\clear_attached_media_cache' );