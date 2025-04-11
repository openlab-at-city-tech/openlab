<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_exclude_words_from_auto_translate', 100 );
function trp_register_exclude_words_from_auto_translate( $settings_array ){
    $settings_array[] = array(
        'name'          => 'exclude_words_from_auto_translate',
        'type'          => 'list_input',
        'columns'       => array(
            'words' => __('String', 'translatepress-multilingual' ),
        ),
        'label'         => esc_html__( 'Exclude strings from automatic translation', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Do not automatically translate these strings (ex. names, technical words...)<br>Paragraphs containing these strings will still be translated except for the specified part.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            => 'exclude_strings',
        'container'     => 'exclude_at_strings'
    );
    return $settings_array;
}


add_filter( 'trp_exclude_words_from_automatic_translation', 'trp_exclude_words_from_auto_translate' );
function trp_exclude_words_from_auto_translate( $exclude_words ){
    $option = get_option( 'trp_advanced_settings', true );
    $add_skip_selectors = array( );
    if ( isset( $option['exclude_words_from_auto_translate'] ) && is_array( $option['exclude_words_from_auto_translate']['words'] ) ) {
        $exclude_words = array_merge( $exclude_words, $option['exclude_words_from_auto_translate']['words'] );
    }

    return $exclude_words;
}

