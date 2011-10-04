<?php
/* 
 * adjust nav display based on expose nav coding
 */
function expose_bp_nav() {
	global $gconnect_theme;
	if( 'nav' == $gconnect_theme->get_option( 'subnav' ) ) {
		remove_action('genesis_after_header','gconnect_site_nav', 10);
		remove_action('genesis_before_header','genesis_do_nav', 10);
		add_action('genesis_before_header','gconnect_site_nav', 10);
	}
}
add_action( 'wp_head', 'expose_bp_nav', 11 );

function expose_bp_nav_filter( $nav ) {
	unset( $nav['bpnav'] );
	return $nav;
}
add_filter( 'gconnect_subnav_options', 'expose_bp_nav_filter' );

function expose_after_bp_content() { ?>
	<div class="clear"></div>
<?php }
add_action( 'gconnect_after_content', 'expose_after_bp_content' );
