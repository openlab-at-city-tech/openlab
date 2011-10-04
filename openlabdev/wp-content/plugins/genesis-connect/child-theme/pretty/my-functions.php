<?php
/* 
 * adjust nav display based on pretty nav coding
 */
function pretty_bp_nav() {
	global $gconnect_theme;

	if( $gconnect_theme->get_option( 'subnav' ) == 'nav' ) {
		remove_action( 'genesis_before_header','genesis_do_nav' );
		remove_action( 'genesis_after_header','gconnect_site_nav', 10 );
		add_action( 'genesis_before_header','gconnect_site_nav' );
	}
}
add_action( 'wp_head', 'pretty_bp_nav', 11 );

function pretty_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'pretty_bp_nav_filter' );
