<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function afl_clear_site_settings() {
    $settings = get_option( 'ari_fancy_lightbox_settings' );
    $clean_uninstall = isset( $settings['advanced'] ) && isset( $settings['advanced']['clean_uninstall'] ) ? (bool) $settings['advanced']['clean_uninstall'] : false;

    if ( ! $clean_uninstall )
        return ;

    delete_option( 'ari_fancy_lightbox' );
    delete_option( 'ari_fancy_lightbox_settings' );
}

if ( ! is_multisite() ) {
    afl_clear_site_settings();
} else {
    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id )   {
        switch_to_blog( $blog_id );

        afl_clear_site_settings();
    }

    switch_to_blog( $original_blog_id );
}
