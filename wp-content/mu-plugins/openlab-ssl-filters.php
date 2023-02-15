<?php

function openlab_ssl_fix( $text ) {
	// Replacing the root domain will catch the specific blog domain
	// because we aren't using subdomains
	if ( is_ssl() ) {
		$search  = set_url_scheme( bp_get_root_domain(), 'http' );
		$replace = set_url_scheme( bp_get_root_domain(), 'https' );
	} else {
		$search  = set_url_scheme( bp_get_root_domain(), 'https' );
		$replace = set_url_scheme( bp_get_root_domain(), 'http' );
	}

	return str_replace( $search, $replace, $text );
}

/**
 * wp_nav_menu() fixes
 *
 * This has to be done in filters because people can enter their own links
 * through the interface
 */
function openlab_ssl_filter_wp_nav_menu( $items ) {
	foreach ( $items as $item ) {
		$item->url = openlab_ssl_fix( $item->url );
	}

	return $items;
}

add_filter( 'wp_nav_menu_objects', 'openlab_ssl_filter_wp_nav_menu' );

/**
 * wp_upload_dir() - this fixes issues with attachment URLs throughout
 */
function openlab_ssl_upload_dir( $upload_dir ) {
	$upload_dir['url']     = openlab_ssl_fix( $upload_dir['url'] );
	$upload_dir['baseurl'] = openlab_ssl_fix( $upload_dir['baseurl'] );
	return $upload_dir;
}

add_filter( 'upload_dir', 'openlab_ssl_upload_dir' );

/**
 * Widget callbacks
 *
 * This is needed for cac-featured-content (and maybe others)
 */
function openlab_ssl_widget_display_callback( $instance, $widget ) {
	// A bit ham-handed, but whatevs
	if ( ! empty( $instance ) ) {
		$instance = map_deep( $instance, 'openlab_ssl_fix' );
	}

	return $instance;
}

add_filter( 'widget_display_callback', 'openlab_ssl_widget_display_callback', 10, 2 );

/**
 * Content
 *
 * This is very heavy-handed, but should be harmless, and is faster than
 * preg_replace() to catch things like inline images.
 */
add_filter( 'the_content', 'openlab_ssl_fix' );

/**
 * YouTube embeds
 */
function openlab_ssl_youtube_embeds( $content ) {
	return str_replace(
		array(
			'http://youtube.com',
			'http://www.youtube.com',
		),
		array(
			'https://youtube.com',
			'https://www.youtube.com',
		),
		$content
	);
}

add_filter( 'the_content', 'openlab_ssl_youtube_embeds' );
