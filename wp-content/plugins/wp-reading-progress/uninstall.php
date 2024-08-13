<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

// remove settings
delete_option( 'ruigehond006' );
delete_option( 'ruigehond006_upgraded_1.2.4' );
// remove the post_meta entries
delete_post_meta_by_key( '_ruigehond006_show' );
