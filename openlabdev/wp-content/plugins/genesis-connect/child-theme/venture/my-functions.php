<?php
/* 
 * adjust nav display based on venture nav coding
 */
function venture_bp_nav() {
	global $gconnect_theme;

	if( $gconnect_theme->get_option( 'subnav' ) == 'nav' ) {
		remove_action( 'genesis_header','genesis_do_nav' );
		remove_action( 'genesis_after_header','gconnect_site_nav', 10 );
		remove_action('genesis_header', 'child_header_tabs', 10 );
		add_action( 'genesis_header','gconnect_site_nav' );
		add_action('genesis_header', 'child_header_tabs', 10 );
	}
}
add_action( 'wp_head', 'venture_bp_nav', 11 );

function venture_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'venture_bp_nav_filter' );

