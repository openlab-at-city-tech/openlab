<?php
/** Uninstall script for Folders Plugin **/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$option_name = 'folders_settings';

delete_option( $option_name );

delete_site_option( $option_name );
