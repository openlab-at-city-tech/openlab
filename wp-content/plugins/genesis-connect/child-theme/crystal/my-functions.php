<?php
/* 
 * remove below nav option
 */
function crystal_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'crystal_bp_nav_filter' );
