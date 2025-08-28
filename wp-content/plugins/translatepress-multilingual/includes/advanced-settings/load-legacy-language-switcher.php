<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_load_legacy_language_switcher', 90 );
function trp_register_load_legacy_language_switcher( $settings_array ){
    $settings_array[] = array(
        'name'          => 'load_legacy_language_switcher',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Load legacy Language Switcher', 'translatepress-multilingual' ),
        'description'   =>  esc_html__( 'Applies to all types of language switchers (floating, shortcode, and menu). When enabled, the site will revert to using the original Language Switcher configured in the General Settings tab, replacing the new customizable version. Your existing switcher settings will remain saved, but they will be ignored while this option is active.', 'translatepress-multilingual' ),
        'id'            => 'troubleshooting',
        'container'     => 'troubleshooting'
    );

    return $settings_array;
}
