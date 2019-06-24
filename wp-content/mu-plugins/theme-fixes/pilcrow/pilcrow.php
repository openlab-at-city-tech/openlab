<?php

/**
 * Override Pilcrow's fallback page menu overrides.
 */
function openlab_pilcrow_page_menu_args( $args ) {
	remove_filter( 'wp_page_menu_args', 'pilcrow_page_menu_args' );
	$args['depth'] = 0;
	return $args;
}
add_filter( 'wp_page_menu_args', 'openlab_pilcrow_page_menu_args', 5 );
