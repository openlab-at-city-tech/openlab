<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_html_lang_attribute', 1001 );
function trp_register_html_lang_attribute( $settings_array ){
    $settings_array[] = array(
        'name'          => 'html_lang_remove_locale',
        'type'          => 'radio',
        'options'       => array( 'default', 'regional' ),
        'default'       => 'default',
        'labels'        => array( esc_html__( 'Default (example: en-US, fr-CA, etc.)', 'translatepress-multilingual' ), esc_html__( 'Regional (example: en, fr, es, etc.)', 'translatepress-multilingual' ) ),
        'label'         => esc_html__( 'HTML Lang Attribute Format', 'translatepress-multilingual' ),
        'description'   => wp_kses(  __( 'Change lang attribute of the html tag to a format that includes country regional or not. <br>In HTML, the lang attribute (<html lang="en-US">)  should be used to  specify the language of text content so that the  browser can correctly display or process  your content (eg. for  hyphenation, styling, spell checking, etc).', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options'
    );
    return $settings_array;
}

add_filter( 'trp_add_default_lang_tags', 'trp_display_default_lang_tag' );
function trp_display_default_lang_tag( $display ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['html_lang_remove_locale'] ) && $option['html_lang_remove_locale'] === 'default' ) {
        return true;
    }
    return false;
}

add_filter( 'trp_add_regional_lang_tags', 'trp_display_regional_lang_tag' );
function trp_display_regional_lang_tag( $display ){

    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['html_lang_remove_locale'] ) && $option['html_lang_remove_locale'] === 'regional' ) {
        return true;
    }
    return false;
}