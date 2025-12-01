<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_manual_translation_only', 1090);
function trp_register_manual_translation_only( $settings_array ){
    $settings_array[] = array(
        'name'          => 'manual_translation_only',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Manual Translation Only', 'translatepress-multilingual' ),
        'description'   => nl2br(esc_html__( "TranslatePress pro-actively scans and saves strings in the database when users access translated pages.
        
                                             This setting disables this functionality and only allows translation and string saving when inside the Translation Editor. 
                                             
                                             Also disables machine translation outside the Translation Editor, giving you better control over character spending, by translating only the pages you visit in the Translation Editor.", 'translatepress-multilingual' )),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options'
    );
    return $settings_array;
}

// Filter to restrict string saving to translation editor only
add_filter('trp_allow_string_saving', 'trp_restrict_string_saving_to_editor', 10, 3);
function trp_restrict_string_saving_to_editor($allow, $new_strings, $update_strings) {
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['manual_translation_only'] ) && $option['manual_translation_only'] === 'yes' ) {
        // Only allow string saving if we're in the translation editor
        if ( !isset($_GET['trp-edit-translation']) || $_GET['trp-edit-translation'] !== 'preview' ) {
            return false;
        }
    }
    return $allow;
}

// Hook to disable machine translation outside translation editor
add_filter('trp_machine_translator_is_available', 'trp_disable_machine_translation_outside_editor', 10);
function trp_disable_machine_translation_outside_editor($is_available) {
    $advanced_option = get_option( 'trp_advanced_settings', true );
    if ( isset( $advanced_option['manual_translation_only'] ) && $advanced_option['manual_translation_only'] === 'yes' ) {
        // If not in translation editor, disable machine translation
        if ( !isset($_GET['trp-edit-translation']) || $_GET['trp-edit-translation'] !== 'preview' ) {
            // Modify the machine translation setting to 'no'
            $is_available = false;
        }
    }
    return $is_available;
}