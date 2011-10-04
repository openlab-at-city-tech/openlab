<?php
/* 
 * adjust nav display based on lexicon nav coding
 */
function tapestry_bp_nav() {
	global $gconnect_theme;
	$priority = 0;
	switch( $gconnect_theme->get_option( 'subnav' ) ) {
		case 'nav':
			remove_action('genesis_before_content', 'genesis_do_nav', 1);
			add_action('genesis_before_content', 'gconnect_site_nav', 1);
			remove_action('genesis_after_header','gconnect_site_nav', 10);
			remove_action('genesis_after_header','genesis_do_nav', 10);
			break;
		case 'subnav':
			remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_subnav' );
			break;
	}
}
add_action( 'wp_head', 'tapestry_bp_nav', 11 );

function tapestry_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'tapestry_bp_nav_filter' );
