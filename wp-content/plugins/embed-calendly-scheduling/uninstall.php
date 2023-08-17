<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

function emcs_uninstall()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'emcs_event_types';
    $query = "DROP table $table_name";

    $wpdb->query($query);

    delete_option('emcs_settings');
    delete_option('emcs_activation_time');
    delete_option('emcs_stop_review_notice');
    delete_option('emcs_stop_newsletter_notice');
    delete_option('emcs_display_greeting');
    delete_option('emcs_encryption_key');
    delete_option('emcs_promotions');
    delete_option('emcs_stop_promotion_one');
    delete_option('emcs_stop_promotion_two');
    delete_option('emcs_promotion_two_delay');
    delete_option('emcs_promotion');
    delete_option('emcs_stop_promotions');
    delete_option('emcs_promotion_delay');
    delete_option('emcs_promotion_last_displayed');
}

emcs_uninstall();
