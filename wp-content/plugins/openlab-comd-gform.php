<?php

/*
 * Plugin Name: COMD Gravity Forms
 * Description: Dropbox and Zapier integration customization for COMD's use of Gravity Forms.
 */

// We should be able to build this action name dynamically.
add_action( 'gform_dropbox_post_upload_1', function ( $feed, $entry, $form ) {
	if ( class_exists( 'GFZapier' ) ) {
		GFZapier::send_form_data_to_zapier( $entry, $form );
	}
}, 10, 3 );

add_filter( 'gform_is_delayed_pre_process_feed', function ( $is_delayed, $form, $entry, $slug ) {
	if ( 1 != $form['id'] ) {
		return $is_delayed;
	}

	// Instead of hardcoding, we can examine form, get IDs of all file upload forms, and then examine entry using those ids.
	$dropbox_field_id = 12;

	$has_dropbox_link = strpos( rgar( $entry, $dropbox_field_id ), 'dropbox.com' );

	if ( $slug === 'gravityformszapier' && false === $has_dropbox_link ) {
		GFCommon::log_debug( METHOD . '(): No Dropbox link, delaying Zapier.' );
		return true;
	}

	return $is_delayed;
}, 10, 4 );
