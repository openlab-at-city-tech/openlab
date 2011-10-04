<?php
/* 
 * adjust nav display based on lexicon nav coding
 */
function lexicon_bp_nav() {
	global $gconnect_theme;
	$priority = 0;
	switch( $gconnect_theme->get_option( 'subnav' ) ) {
		case 'nav':
			remove_action( 'genesis_after_header','genesis_do_subnav', 11 );
			break;
		case 'subnav':
			remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_subnav' );
			$priority = 11;
			break;
		case 'bpnav':
			$priority = 20;
			break;
	}
	if( $priority ) {
		remove_action( 'genesis_after_header', 'gconnect_site_nav', $priority );
		add_action( 'genesis_before_content_sidebar_wrap', 'gconnect_site_nav', $priority );
	}
}
add_action( 'wp_head', 'lexicon_bp_nav', 11 );
