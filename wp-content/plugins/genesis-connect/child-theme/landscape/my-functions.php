<?php
/*
 * center the adminbar
 */
function gconnect_landscape_head() {
	remove_action( 'genesis_after_footer', 'bp_core_admin_bar', 88 );
	if( gconnect_have_adminbar() && ( !is_home() || gconnect_get_option( 'adminbar' ) ) )
		add_action( 'genesis_after', 'bp_core_admin_bar', 88 );
}
add_action( 'wp_head', 'gconnect_landscape_head', 91 );

function landscape_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'landscape_bp_nav_filter' );
