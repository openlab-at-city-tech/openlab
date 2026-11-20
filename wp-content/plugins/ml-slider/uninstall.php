<?php
/**
 * Removes the campaign answers and cache when the plugin is deleted, on every site of a network.
 *
 * @since 3.114.1
 */

defined('ABSPATH') || exit;

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die('No direct access.');
}

if (is_multisite()) {
    foreach (get_sites(array('fields' => 'ids', 'number' => 0)) as $metaslider_site_id) {
        switch_to_blog($metaslider_site_id);
        delete_option('metaslider_campaign_state');
        delete_transient('metaslider_campaign_has_pro');
        restore_current_blog();
    }
} else {
    delete_option('metaslider_campaign_state');
    delete_transient('metaslider_campaign_has_pro');
}
