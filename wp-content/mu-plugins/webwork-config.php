<?php

add_filter( 'webwork_client_site_base', function() {
	$base = get_blog_option( 1, 'home' );
	return trailingslashit( $base ) . 'ol-webwork';
} );

add_filter( 'webwork_server_site_base', function() {
	$base = get_blog_option( 1, 'home' );
	return trailingslashit( $base ) . 'ol-webwork';
} );
