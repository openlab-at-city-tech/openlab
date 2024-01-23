<?php

add_filter( 'trp_register_advanced_settings', 'trp_show_opposite_flag_language_switcher_shortcode', 1250 );
function trp_show_opposite_flag_language_switcher_shortcode( $settings_array ){
    $settings_array[] = array(
        'name'          => 'show_opposite_flag_language_switcher_shortcode',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Show opposite language in the language switcher', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Transforms the language switcher into a button showing the other available language, not the current one.<br> Only works when there are exactly two languages, the default one and a translation one.<br>This will affect the shortcode language switcher and floating language switcher as well.<br> To achieve this in menu language switcher go to Appearance->Menus->Language Switcher and select Opposite Language.', 'translatepress-multilingual' ), array( 'br' => array()) ),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}

function trp_opposite_ls_current_language( $current_language, $published_languages, $TRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $current_language['code'] = $code;
                $current_language['name'] = $name;
                break;
            }
        }
    }
    return $current_language;
}

function trp_opposite_ls_other_language( $other_language, $published_languages, $TRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        $other_language = array();
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $other_language[$code] = $name;
                break;
            }
        }
    }
    return $other_language;
}

function trp_opposite_ls_hide_disabled_language($return, $current_language, $current_language_preference, $settings){
    if ( count( $settings['publish-languages'] ) == 2 ){
        return false;
    }
    return $return;
}

function trp_enqueue_language_switcher_shortcode_scripts(){
    $trp                 = TRP_Translate_Press::get_trp_instance();
    $trp_languages       = $trp->get_component( 'languages' );
    $trp_settings        = $trp->get_component( 'settings' );   
    $published_languages = $trp_languages->get_language_names( $trp_settings->get_settings()['publish-languages'] );
    if(count ( $published_languages ) == 2 ) {
        wp_add_inline_style( 'trp-language-switcher-style', '.trp-language-switcher > div {
    padding: 3px 5px 3px 5px;
    background-image: none;
    text-align: center;}' );
    }
}

function trp_opposite_ls_floating_current_language($current_language, $published_languages, $TRP_LANGUAGE, $settings){
    if ( count ( $published_languages ) == 2 ) {
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $current_language['code'] = $code;
                $current_language['name'] = $name;
                break;
            }
        }
    }
    return $current_language;
}

function trp_opposite_ls_floating_other_language( $other_language, $published_languages, $TRP_LANGUAGE, $settings ){
    if ( count ( $published_languages ) == 2 ) {
        $other_language = array();
        foreach ($published_languages as $code => $name) {
            if ($code != $TRP_LANGUAGE) {
                $other_language[$code] = $name;
                break;
            }
        }
    }
    return $other_language;
}

function trp_opposite_ls_floating_hide_disabled_language($return, $current_language, $settings){
    if ( count( $settings['publish-languages'] ) == 2 ){
        return false;
    }
    return $return;
}

function trp_show_opposite_flag_settings(){
    $option = get_option( 'trp_advanced_settings', true );

     if(isset($option['show_opposite_flag_language_switcher_shortcode']) && $option['show_opposite_flag_language_switcher_shortcode'] !== 'no'){
         add_filter( 'trp_ls_shortcode_current_language', 'trp_opposite_ls_current_language', 10, 4 );
         add_filter( 'trp_ls_shortcode_other_languages', 'trp_opposite_ls_other_language', 10, 4 );
         add_filter( 'trp_ls_shortcode_show_disabled_language', 'trp_opposite_ls_hide_disabled_language', 10, 4 );
         add_action( 'wp_enqueue_scripts', 'trp_enqueue_language_switcher_shortcode_scripts', 20 );
         add_action('trp_ls_floating_current_language', 'trp_opposite_ls_floating_current_language', 10, 4);
         add_action('trp_ls_floating_other_languages', 'trp_opposite_ls_floating_other_language', 10, 4);
         add_action('trp_ls_floater_show_disabled_language', 'trp_opposite_ls_floating_hide_disabled_language', 10, 3 );
     }
 }

trp_show_opposite_flag_settings();