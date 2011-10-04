<?php
/*
 * center the adminbar
 */
function gconnect_metric_adminbar() { ?>
<div id="bp-admin-bar">
	<?php bp_core_admin_bar(); ?>
</div>
<?php }

function gconnect_metric_bp_head() {
	remove_action( 'genesis_after_footer', 'bp_core_admin_bar', 88 );
	if( gconnect_have_adminbar() && ( !is_home() || gconnect_get_option( 'home_adminbar' ) ) ) {
		add_action( 'genesis_after_footer', 'gconnect_metric_adminbar', 88 );
	}
}
add_action('wp_head', 'gconnect_metric_bp_head', 91);