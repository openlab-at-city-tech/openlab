<?php
// Check that code was called from WordPress with
// uninstallation constant declared
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit;

function db_prefix() {
    global $wpdb;
    if (method_exists($wpdb, "get_blog_prefix"))
        return $wpdb->get_blog_prefix();
    else
        return $wpdb->prefix;
}

// Check if options exist and delete them if present
if ( get_option( 'LinkLibraryGeneral' ) != false ) {
    
    $genoptions = get_option( 'LinkLibraryGeneral' );

    for ($i = 1; $i <= $genoptions['numberstylesets']; $i++) {
        $settingsname = 'LinkLibraryPP' . $i;
        
        delete_option( $settingsname );
    }
    
    delete_option( 'LinkLibraryGeneral' );
}

global $wpdb;

$wpdb->links_extrainfo = db_prefix().'links_extrainfo';

$deletionquery = 'DROP TABLE IF EXISTS ' . $wpdb->links_extrainfo;

$wpdb->get_results( $deletionquery );