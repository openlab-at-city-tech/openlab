<?php
add_filter( 'trp_register_advanced_settings', 'trp_register_enable_hreflang_xdefault', 1100 );
function trp_register_enable_hreflang_xdefault( $settings_array ){
    $settings_array[] = array(
        'name'          => 'enable_hreflang_xdefault',
        'type'          => 'select',
        'default'       => 'disabled',
        'label'         => esc_html__( 'Enable the hreflang x-default tag for language:', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Enables the hreflang="x-default" for an entire language. See documentation for more details.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'options'       => trp_get_lang_for_xdefault(),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}

function trp_get_lang_for_xdefault(){
    $published_lang_labels = trp_get_languages();
    return array_merge(['disabled' => 'Disabled'], $published_lang_labels);
}
