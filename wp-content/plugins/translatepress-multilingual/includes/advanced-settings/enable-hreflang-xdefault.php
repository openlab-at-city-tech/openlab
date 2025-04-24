<?php

if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_enable_hreflang_xdefault', 1100 );
function trp_register_enable_hreflang_xdefault( $settings_array ){
    $settings_array[] = array(
        'name'          => 'enable_hreflang_xdefault',
        'type'          => 'custom',
        'default'       => 'disabled',
        'label'         => esc_html__( 'Enable the hreflang x-default tag for language:', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Enables the hreflang="x-default" for an entire language. See documentation for more details.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'options'       => trp_get_lang_for_xdefault(),
        'id'            => 'miscellaneous_options',
        'container'     => 'miscellaneous_options',
    );

    return $settings_array;
}

function trp_get_lang_for_xdefault(){
    $published_lang_labels = trp_get_languages();
    return array_merge(['disabled' => 'Disabled'], $published_lang_labels);
}

add_filter( 'trp_advanced_setting_custom_enable_hreflang_xdefault', 'trp_output_enable_hreflang_xdefault' );
function trp_output_enable_hreflang_xdefault( $setting ){
    $trp_settings = ( new TRP_Settings() )->get_settings();
    $adv_option = $trp_settings['trp_advanced_settings'];

    $checked = ( isset( $adv_option[ $setting['name'] ] ) && $adv_option[ $setting['name'] ] !== 'disabled' ) || ( isset( $adv_option[ $setting['name'] . '-checkbox' ] ) && $adv_option[ $setting['name'] . '-checkbox' ] === 'yes' )
        ? 'checked' : '';

    $select = "<select class='trp-select' name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "]'>";

    foreach ( $setting['options'] as $option_key => $option_value ) {
        if ( $option_key === 'disabled' )
            continue;

        $selected = $adv_option[ $setting['name'] ] === $option_key ? ' selected' : '';

        $select .= "<option value='". esc_attr( $option_key ) ."' $selected>". esc_html( $option_value )."</option>";
    }

    $select .= "</select>";

    $html = "<div class='trp-settings-custom-checkbox__wrapper'>
                <div class='trp-settings-checkbox'>
                    <input type='checkbox' id='" . esc_attr( $setting['name'] ) . "-checkbox' 
                           name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "-checkbox]' 
                           value='yes' " . $checked . " />
    
                    <label for='" . esc_attr( $setting['name'] ) . "-checkbox' class='trp-checkbox-label'>
                        <div class='trp-checkbox-content'>
                            <span class='trp-primary-text-bold'>" . esc_html( $setting['label'] ) . "</span>
                            <span class='trp-description-text'>" . wp_kses_post( $setting['description'] ) . "</span>
                        </div>
                    </label>
                </div>
                $select
            </div>";

    return $html;
}