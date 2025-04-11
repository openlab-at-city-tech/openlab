<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_exclude_selectors', 110 );
function trp_register_exclude_selectors( $settings_array ){
    $settings_array[] = array(
        'name'          => 'exclude_translate_selectors',
        'type'          => 'list_input',
        'columns'       => array(
            'selector' => __('Selector', 'translatepress-multilingual' ),
        ),
        'label'         => esc_html__( 'Exclude selectors from translation', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Do not translate strings that are found in html nodes matching these selectors.<br>Excludes all the children of HTML nodes matching these selectors from being translated.<br>These strings cannot be translated manually nor automatically.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_selectors'
    );
    return $settings_array;
}


add_filter( 'trp_no_translate_selectors', 'trp_skip_translation_for_selectors' );
function trp_skip_translation_for_selectors( $skip_selectors ){
    $option = get_option( 'trp_advanced_settings', true );
    $add_skip_selectors = array( );
    if ( isset( $option['exclude_translate_selectors'] ) && is_array( $option['exclude_translate_selectors']['selector'] ) ) {
        $add_skip_selectors = $option['exclude_translate_selectors']['selector'];
    }

    return array_merge( $skip_selectors, $add_skip_selectors );
}

