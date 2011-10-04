<?php
/* 
 * remove below nav option
 */
function focus_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'focus_bp_nav_filter' );
