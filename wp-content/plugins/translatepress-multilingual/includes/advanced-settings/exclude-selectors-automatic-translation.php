<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Add automatic translate exclude selectors.
 */
add_filter( 'trp_register_advanced_settings', 'trp_register_exclude_selectors_automatic_translation', 120 );
function trp_register_exclude_selectors_automatic_translation( $settings_array ){
    $settings_array[] = array(
        'name'          => 'exclude_selectors_from_automatic_translation',
        'type'          => 'list_input',
        'columns'       => array(
            'selector' => __('Selector', 'translatepress-multilingual' ),
        ),
        'label'         => esc_html__( 'Exclude selectors only from automatic translation', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Do not automatically translate strings that are found in html nodes matching these selectors.<br>Excludes all the children of HTML nodes matching these selectors from being automatically translated.<br>Manual translation of these strings is still possible.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_selectors_at',
    );
    return $settings_array;
}


add_filter( 'trp_no_auto_translate_selectors', 'trp_skip_automatic_translation_for_selectors' );
function trp_skip_automatic_translation_for_selectors( $skip_selectors ){
    $option = get_option( 'trp_advanced_settings', true );
    $add_skip_selectors = array( );
    if ( isset( $option['exclude_selectors_from_automatic_translation'] ) && is_array( $option['exclude_selectors_from_automatic_translation']['selector'] ) ) {
        $add_skip_selectors = $option['exclude_selectors_from_automatic_translation']['selector'];
    }

    return array_merge( $skip_selectors, $add_skip_selectors );
}

