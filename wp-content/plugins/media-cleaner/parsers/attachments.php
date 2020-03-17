<?php

// Attachment (https://wordpress.org/plugins/attachments/)
// Added by Mike Meinz
// Discussion: https://wordpress.org/support/topic/attachments-plugin/

add_action( 'wpmc_scan_postmeta', 'wpmc_scan_postmeta_attachments' );

function wpmc_scan_postmeta_attachments($id) {
	global $wpmc;
	$postmeta_images_ids = array();
	$attachments_json = get_post_meta( $id, 'attachments', true );  // meta_key=='attachments'
	$attachments_decoded = is_string( $attachments_json ) ? json_decode( $attachments_json ) : false;
	if ( !empty( $attachments_decoded )) {
		foreach ( $attachments_decoded as $AttachmentData => $TheAttachment ) {
			foreach( $TheAttachment as $AttachmentData => $attachment ) {
				array_push( $postmeta_images_ids, $attachment->id );
			}
		}
	}
	if ( !empty( $postmeta_images_ids ) ) {
		$wpmc->add_reference_id( $postmeta_images_ids, 'ATTACHMENT (ID)' );  // mm change
	}
}

?>