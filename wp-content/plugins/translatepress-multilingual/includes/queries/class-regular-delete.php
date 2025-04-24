<?php
/**
 * Class TRP_Gettext_Normalization
 *
 * Queries for inserting and updating strings in gettext tables
 *
 * To access this component use:
 *      $trp_regular_delete = new TRP_Regular_Delete( );
 *
 */
class TRP_Regular_Delete extends TRP_Query {

    public    $db;
    protected $settings;
    protected $error_manager;

    /**
     * TRP_Query constructor.
     *
     * @param $settings
     */
    public function __construct() {
        global $wpdb;
        $this->db       = $wpdb;
        $trp            = TRP_Translate_Press::get_trp_instance();
        $trp_settings   = $trp->get_component( 'settings' );
        $settings       = $trp_settings->get_settings();
        $this->settings = $settings;
    }

    public function delete_strings( $original_ids ){
        global $wpdb;

        // Ensure IDs are properly formatted as integers
        $original_ids = array_map('intval', $original_ids);
        $ids_placeholder = implode(',', array_fill(0, count($original_ids), '%d'));

        if ( empty( $original_ids ) ) {
            return false;
        }

        foreach ($this->settings['translation-languages'] as $language_code ){
            if ( $this->settings['default-language'] == $language_code ){
                continue;
            }
            $dictionary_table = $this->get_table_name( $language_code );

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM `" . $dictionary_table . "` WHERE original_id IN ($ids_placeholder)",
                    ...$original_ids
                )
            );
        }

        // Delete from wp_trp_original_strings
        $items_deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->get_table_name_for_original_strings() . " WHERE id IN ($ids_placeholder)",
                ...$original_ids
            )
        );

        // Delete from wp_trp_original_meta
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->get_table_name_for_original_meta() . " WHERE original_id IN ($ids_placeholder)",
                ...$original_ids
            )
        );

        return (int)$items_deleted;
    }
}