<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_hreflang_remove_locale', 1000 );
function trp_register_hreflang_remove_locale( $settings_array ){
    $settings_array[] = array(
        'name'          => 'hreflang_remove_locale',
        'type'          => 'radio',
        'options'       => array( 'show_both', 'remove_country_locale', 'remove_region_independent_locale' ),
        'default'       => 'show_both',
        'labels'        => array( esc_html__( 'Show Both (recommended)', 'translatepress-multilingual' ), esc_html__( 'Remove Country Locale', 'translatepress-multilingual' ), esc_html__( 'Remove Region Independent Locale', 'translatepress-multilingual' ) ),
        'label'         => esc_html__( 'Remove duplicate hreflang', 'translatepress-multilingual' ),
        'description'   => wp_kses(  __( 'Choose which hreflang tags will appear on your website.<br/>We recommend showing both types of hreflang tags as indicated by <a href="https://developers.google.com/search/docs/advanced/crawling/localized-versions" title="Google Crawling" target="_blank">Google documentation</a>.<br/>Removing Country Locale when having multiple Country Locales of the same language (ex. English UK and English US) will result in showing one hreflang tag with link to just one of the region locales for that language.', 'translatepress-multilingual' ), array( 'br' => array(), 'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ) ) ),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options'
    );
    return $settings_array;
}

add_filter( 'trp_add_country_hreflang_tags', 'trp_display_country_hreflang_tag' );
function trp_display_country_hreflang_tag( $display ){
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['hreflang_remove_locale'] ) && $option['hreflang_remove_locale'] === 'remove_country_locale' ) {
        return false;
    }
    return $display;
}

add_filter( 'trp_add_region_independent_hreflang_tags', 'trp_display_region_independent_hreflang_tag' );
function trp_display_region_independent_hreflang_tag( $display ){

    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['hreflang_remove_locale'] ) && $option['hreflang_remove_locale'] === 'remove_region_independent_locale' ) {
        return false;
    }
    return $display;
}
