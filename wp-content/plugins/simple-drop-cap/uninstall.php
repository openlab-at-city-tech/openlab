<?php 
// if delete/uninstall is not called from WP, then exit
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit('Failed to uninstall.');
}

// delete plugin options
delete_option( 'wpsdc_options' );
?>