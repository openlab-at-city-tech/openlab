<?php
/* 
 * remove below nav option
 */
function socialeyes_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'socialeyes_bp_nav_filter' );
