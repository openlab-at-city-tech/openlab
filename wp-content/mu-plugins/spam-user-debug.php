<?php

function openlab_spam_signup_logger( $meta ) {
	if ( is_super_admin() ) {
		return $meta;
	}

	if ( isset( $meta['profile_field_ids'] ) ) {
		return $meta;
	}

	$ms = [
		date( 'Y-m-d H:i:s' ),
		'User registration on hook ' . current_filter(),
		print_r( $meta, true ),
		print_r( $_SERVER, true ),
		print_r( debug_backtrace(), true ),
	];

	wp_mail( 'boone@gorg.es', 'Potential spam registration on OpenLab', implode( "\n\n", $ms ) );

	return $meta;
}
add_filter( 'signup_user_meta', 'openlab_spam_signup_logger' );
add_filter( 'signup_site_meta', 'openlab_spam_signup_logger' );

