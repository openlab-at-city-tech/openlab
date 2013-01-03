<?php
/*
 * center the adminbar
 */
function gc_enterprise_adminbar() {
	echo "<div id='bp-admin-bar'>\n";
	bp_core_admin_bar();
	echo "</div>\n";
}

function gc_enterprise_bp_head() {
	remove_action( 'genesis_after_footer', 'bp_core_admin_bar', 88 );
	if( gconnect_have_adminbar() && ( !is_home() || gconnect_get_option( 'adminbar' ) ) )
		add_action( 'genesis_after_footer', 'gc_enterprise_adminbar', 88 );
}
add_action('wp_head', 'gc_enterprise_bp_head', 91);