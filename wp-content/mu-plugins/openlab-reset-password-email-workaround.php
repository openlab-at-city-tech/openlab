<?php

/**
 * See #957
 */
if ( isset( $_SERVER['SCRIPT_NAME'] ) && '/wp-login.php' == $_SERVER['SCRIPT_NAME'] && isset( $_GET['rpk'] ) && isset( $_GET['login'] ) ) {
	$rpk = urldecode( $_GET['rpk'] );
	$login = urldecode( $_GET['login'] );
	$url = network_site_url( 'wp-login.php?action=rp&key=' . rawurlencode( $rpk ) . '&login=' . rawurlencode( $login ), 'login' );
	status_header( 302 );
	header( "Location: $url", true, 302 );
        die();
}

function openlab_swap_retrievepassword_key( $message ) {
	return str_replace( '&key=', '&rpk=', $message );
}
add_filter( 'retrieve_password_message', 'openlab_swap_retrievepassword_key' );
