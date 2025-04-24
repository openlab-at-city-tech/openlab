<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Check_Invalid_Text
 *
 * Used to exclude problematic strings triggering 'WordPress database error: Could not perform query because it contains invalid data.' from TP query functions.
 *
 * Divide et impera method used to minimise number of queries needed to detect needle in haystack. Applied for key functions:
 * get_existing_translations, insert_strings and update_strings.
 */
class TRP_Check_Invalid_Text{
    protected $table_charset;
    protected $check_current_query;
    protected $col_meta;

    public function get_existing_translations_without_invalid_text( $dictionary, $prepared_query, $strings_array, $language_code, $block_type ){
        if ( $this->is_invalid_data_error() ){
            $count = count($strings_array);
            if ( $count <= 1 ){
                // fake translated so it doesn't get auto translated or updated in DB later
                $entry = new stdClass();
                $entry->translated = $strings_array[0];
                $entry->original = $strings_array[0];
                $entry->status = "1";
                $entry->invalid_data = true;
                return array( $strings_array[0] => $entry);
            }else{
                $trp = TRP_Translate_Press::get_trp_instance();
                $trp_query = $trp->get_component( 'query' );

                $half = floor( $count / 2 );

                $array1 = $trp_query->get_existing_translations( array_slice( $strings_array, 0, $half ), $language_code, $block_type );
                $array2 = $trp_query->get_existing_translations( array_slice( $strings_array, $half ), $language_code, $block_type );
                return array_merge( $array1, $array2 );
            }
        }
        return $dictionary;
    }

    public function insert_translations_without_invalid_text( $new_strings, $language_code, $block_type ){
        if ( $this->is_invalid_data_error() ){
            $count = count($new_strings);
            if ( $count <= 1 ){
                return;
            }else{
                $trp = TRP_Translate_Press::get_trp_instance();
                $trp_query = $trp->get_component( 'query' );

                $half = floor( $count / 2 );

                $trp_query->insert_strings( array_slice( $new_strings, 0, $half ), $language_code, $block_type );
                $trp_query->insert_strings( array_slice( $new_strings, $half ), $language_code, $block_type );
                return;
            }
        }

    }

    public function update_translations_without_invalid_text( $update_strings, $language_code, $block_type ){
        if ( $this->is_invalid_data_error() ){
            $count = count($update_strings);
            if ( $count <= 1 ){
                return;
            }else{
                $trp = TRP_Translate_Press::get_trp_instance();
                $trp_query = $trp->get_component( 'query' );

                $half = floor( $count / 2 );

                $trp_query->update_strings( array_slice( $update_strings, 0, $half ), $language_code, $block_type );
                $trp_query->update_strings( array_slice( $update_strings, $half ), $language_code, $block_type );
                return;
            }
        }

    }

    public function is_invalid_data_error(){
        // Using $trp_disable_invalid_data_detection as a sort of apply_filters to turn off this feature.
        // Not using proper WP filter to reduce page load time. This function is executed many times.
        global $wpdb, $trp_disable_invalid_data_detection;
        if ( !empty($wpdb->last_error) && !isset( $trp_disable_invalid_data_detection) ) {
            $invalid_data_error = __( 'WordPress database error: Could not perform query because it contains invalid data.' );  /* phpcs:ignore */ /* $domain arg is purposely omitted because we want to identify the exact wpdb last_error message. Only used for comparison reasons, it's not actually displayed. */
            if ( $wpdb->last_error == $invalid_data_error ) {
                return true;
            }
        }
        return false;
    }

}
