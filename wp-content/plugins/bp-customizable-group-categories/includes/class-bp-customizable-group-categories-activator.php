<?php

/**
 * Fired during plugin activation
 *
 * @link       http://early-adopter.com/
 * @since      1.0.0
 *
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bp_Customizable_Group_Categories
 * @subpackage Bp_Customizable_Group_Categories/includes
 * @author     Joe Unander <joe@early-adopter.com>
 */
class Bp_Customizable_Group_Categories_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;

        //first we make sure the db_version option matches what's stored in version.php
        //on Dev Org, these values where mismatched
        $check_db_version = get_option('db_version');
        
        require ABSPATH . WPINC . '/version.php';
        
        if($check_db_version !== $wp_db_version){
            update_option('db_version', $wp_db_version);
        }
        
        //next we clear the options values in object cache to make sure we have the latest values
        $cache_delete = wp_cache_delete('alloptions', 'options');
        $alloptions = wp_load_alloptions();
        wp_cache_set('alloptions', $alloptions, 'options');

        $current_db_version = get_option('db_version');

// Upgrade versions prior to 4.4.
        if ($current_db_version < 34978) {
// If compatible termmeta table is found, use it, but enforce a proper index and update collation.
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->termmeta}'") && $wpdb->get_results("SHOW INDEX FROM {$wpdb->termmeta} WHERE Column_name = 'meta_key'")) {

                /** Load WordPress Administration Upgrade API */
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

                $wpdb->query("ALTER TABLE $wpdb->termmeta DROP INDEX meta_key, ADD INDEX meta_key(meta_key(191))");
                maybe_convert_table_to_utf8mb4($wpdb->termmeta);
            } else {

                $max_index_length = 191;

                $charset_collate = '';

                if (!empty($wpdb->charset))
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate))
                    $charset_collate .= " COLLATE $wpdb->collate";

                $sql = "CREATE TABLE {$wpdb->prefix}termmeta (
  meta_id bigint(20) unsigned NOT NULL auto_increment,
  term_id bigint(20) unsigned NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value longtext,
  PRIMARY KEY  (meta_id),
  KEY term_id (term_id),
  KEY meta_key (meta_key($max_index_length))
) $charset_collate;";

                $result = $wpdb->query($sql);
            }
        }
    }

}
