<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

include_once(EMCS_INCLUDES . 'api.php');

class EMCS_Event_Types
{
    public static function get_event_types()
    {
        if (!self::get_event_types_from_db()) {
            $event_types = self::fetch_event_types_from_calendly();

            if (!empty($event_types)) {
                self::cache_calendly_event_types($event_types);
            } else {
                return [];
            }
        }

        return self::get_event_types_from_db();
    }

    private static function get_event_types_from_db()
    {
        global $wpdb;
        $table_name = self::get_emcs_table();

        if (!self::emcs_event_types_table_exists()) {
            return false;
        }

        $query = "SELECT * FROM $table_name";
        $event_types = $wpdb->get_results($query);

        if (!empty($event_types)) {
            return $event_types;
        }

        return false;
    }

    private static function emcs_event_types_table_exists()
    {
        global $wpdb;
        $table_name = self::get_emcs_table();

        return ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name);
    }

    private static function cache_calendly_event_types($event_types)
    {
        global $wpdb;

        if (!empty($event_types)) {

            self::create_emcs_event_types_table();

            foreach ($event_types as $event_type) {
                $data = self::prepare_event_type($event_type);
                $wpdb->insert(self::get_emcs_table(), $data);
            }

            return true;
        }

        return false;
    }

    private static function prepare_event_type($event_type)
    {

        if (!empty($event_type)) {

            return array(
                'name'      => sanitize_text_field($event_type->get_event_type_name()),
                'url'       => sanitize_text_field($event_type->get_event_type_url()),
                'slug'      => sanitize_text_field($event_type->get_event_type_slug()),
                'status'    => sanitize_text_field($event_type->get_event_type_status())
            );
        }

        return false;
    }

    private static function fetch_event_types_from_calendly()
    {
        $options = get_option('emcs_settings');

        if (!empty($options['emcs_v2api_key'])) {

            if(function_exists('emcs_decrypt_key')) {
                
                $api_key = emcs_decrypt_key($options['emcs_v2api_key']);
                $calendly = new EMCS_API('v2', $api_key);

                // retry v1 key if v2 key returns empty results
                if($calendly->emcs_get_events() === FALSE) {
                    
                    $api_key = emcs_decrypt_key($options['emcs_v1api_key']);
                    $calendly = new EMCS_API('v1', $api_key);

                    return $calendly->emcs_get_events();
                    
                }

                return $calendly->emcs_get_events();
            }

        } elseif (!empty($options['emcs_v1api_key'])) {

            if(function_exists('emcs_decrypt_key')) {
                
                $api_key = emcs_decrypt_key($options['emcs_v1api_key']);
                $calendly = new EMCS_API('v1', $api_key);
                
                return $calendly->emcs_get_events();
            }
        }

        return false;
    }

    public static function extract_event_type_owner($event_type_url)
    {

        if (!empty($event_type_url)) {

            $owner = str_ireplace('https://calendly.com/', '', $event_type_url);
            $owner = strstr($owner, '/', true);

            return $owner;
        }

        return false;
    }

    public static function create_emcs_event_types_table()
    {
        $table_name = self::get_emcs_table();
        $charset_collate = self::get_emcs_table_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                url varchar(255) NOT NULL,
                slug tinytext NOT NULL,
                status tinytext NOT NULL,
                    PRIMARY KEY  (id)
                ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table($table_name, $sql);
    }

    private static function get_emcs_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'emcs_event_types';
    }

    private static function get_emcs_table_charset_collate()
    {
        global $wpdb;
        return $wpdb->get_charset_collate();
    }

    public static function sync_event_types_button_listener()
    {
        if (isset($_REQUEST['emcs_sync_event_types'])) {
            self::sync_event_types();
        }
    }

    private static function sync_event_types()
    {
        self::flush_event_types();
        $event_types = self::fetch_event_types_from_calendly();
        self::cache_calendly_event_types($event_types);
    }

    private static function flush_event_types()
    {
        global $wpdb;
        $table_name = self::get_emcs_table();
        $query = "TRUNCATE table $table_name";

        return $wpdb->query($query);
    }
}
