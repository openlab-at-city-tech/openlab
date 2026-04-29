<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Admin
{
    public static function clear_unwanted_notices()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only reading admin page slug.
        if (!isset($_GET['page'])) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only reading admin page slug.
        $page = sanitize_text_field(wp_unslash($_GET['page']));

        $allowed_pages = [
            'emcs-customizer',
            'emcs-event-types',
            'emcs-settings',
            'emcp-analytics',
            'emcp-events',
            'emcs-licenses'
        ];

        if (in_array($page, $allowed_pages, true)) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }

    public static function on_activation()
    {
        add_option('emcs_activation_time', strtotime('now'));
        add_option('emcs_display_greeting', 1);
        add_option('emcs_encryption_key', bin2hex(openssl_random_pseudo_bytes(10)));

        require_once(EMCS_EVENT_TYPES . 'event-types.php');
        EMCS_Event_Types::create_emcs_event_types_table();
    }

    public static function sync_event_types_button_listener()
    {
        if (
            !isset($_POST['_wpnonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['_wpnonce'])),
                'emcs_sync_event_types_action'
            )
        ) {
            wp_die('Invalid nonce');
        }

        include_once(EMCS_EVENT_TYPES . 'event-types.php');
        EMCS_Event_Types::sync_event_types();

        $redirect = isset($_POST['_wp_http_referer'])
            ? sanitize_text_field(wp_unslash($_POST['_wp_http_referer']))
            : admin_url('admin.php?page=emcs-event-types');

        wp_safe_redirect($redirect);
        exit;
    }
}
