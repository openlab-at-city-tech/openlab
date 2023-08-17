<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Admin
{
    public static function clear_unwanted_notices()
    {
        if (isset($_REQUEST['page'])) {

            if (
                $_REQUEST['page'] == 'emcs-customizer' || $_REQUEST['page'] == 'emcs-event-types' || $_REQUEST['page'] == 'emcs-settings'
                || $_REQUEST['page'] == 'emcp-analytics' || $_REQUEST['page'] == 'emcp-events'
            ) {
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
            }
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
}
