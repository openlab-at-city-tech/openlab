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
        global $wpdb, $wp_current_db_version;

        if (!$wp_current_db_version) {
            $current_db_version = get_option('db_version');
        } else {
            $current_db_version = $wp_current_db_version;
        }

// Upgrade versions prior to 4.4.
        if ($current_db_version < 34978) {
// If compatible termmeta table is found, use it, but enforce a proper index and update collation.
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->termmeta}'") && $wpdb->get_results("SHOW INDEX FROM {$wpdb->termmeta} WHERE Column_name = 'meta_key'")) {

                $wpdb->query("ALTER TABLE $wpdb->termmeta DROP INDEX meta_key, ADD INDEX meta_key(meta_key(191))");
                maybe_convert_table_to_utf8mb4($wpdb->termmeta);
            } else {

                $max_index_length = 191;

                $charset_collate = '';

                if (!empty($wpdb->charset))
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate))
                    $charset_collate .= " COLLATE $wpdb->collate";

                $sql = "CREATE TABLE $wpdb->termmeta (
  meta_id bigint(20) unsigned NOT NULL auto_increment,
  term_id bigint(20) unsigned NOT NULL default '0',
  meta_key varchar(255) default NULL,
  meta_value longtext,
  PRIMARY KEY  (meta_id),
  KEY term_id (term_id),
  KEY meta_key (meta_key($max_index_length))
) $charset_collate;";

                $wpdb->query($sql);
            }
        }
    }

}
