<?php
/* 
 * adjust nav display based on delicious nav coding
 */
function delicious_bp_nav() {
	global $gconnect_theme;
	if( 'nav' == $gconnect_theme->get_option( 'subnav' ) ) {
		remove_action('genesis_after_header','gconnect_site_nav', 10);
		remove_action('genesis_before_header','genesis_do_nav', 10);
		add_action('genesis_before_header','gconnect_site_nav', 10);
	}
}
add_action( 'wp_head', 'delicious_bp_nav', 11 );
