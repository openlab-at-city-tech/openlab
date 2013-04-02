<?php

/**
 * When Jill Belli's digest items are saved, save a stack trace
 *
 * See #818
 */
function openlab_jb_digest_debug( $meta_id, $object_id, $meta_key, $meta_value ) {
	if ( 2614 === $object_id && 'ass_digest_items' === $meta_key ) {
		$val = array();
		$val[] = func_get_args();
		$val[] = debug_backtrace();
		$val[] = $_SERVER;
		update_site_option( 'openlab_jb_debug_' . time(), json_encode( $val ) );
	}
}
add_action( 'updated_user_meta', 'openlab_jb_digest_debug', 10, 4);
