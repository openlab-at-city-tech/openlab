<?php
/*
 * center the adminbar
 */
function gconnect_streamline_adminbar() { ?>
<div id="bp-admin-bar">
	<?php bp_core_admin_bar(); ?>
</div>
<?php }

function gconnect_streamline_head() {
	remove_action( 'genesis_after_footer', 'bp_core_admin_bar', 88 );
	if( gconnect_have_adminbar() && ( !is_home() || gconnect_get_option( 'adminbar' ) ) )
		add_action( 'genesis_after_footer', 'gconnect_streamline_adminbar', 88 );
}
//add_action( 'wp_head', 'gconnect_streamline_head', 91 );
