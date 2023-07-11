<?php

add_filter( 'trp_register_advanced_settings', 'trp_open_language_switcher_shortcode_on_click', 1350 );
function trp_open_language_switcher_shortcode_on_click( $settings_array ){
    $settings_array[] = array(
        'name'          => 'open_language_switcher_shortcode_on_click',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Open language switcher only on click', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'Open the language switcher shortcode by clicking on it instead of hovering.<br> Close it by clicking on it, anywhere else on the screen or by pressing the escape key. This will affect only the shortcode language switcher.', 'translatepress-multilingual' ), array( 'br' => array()) ),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}

function trp_lsclick_enqueue_scriptandstyle() {
    wp_enqueue_script('trp-clickable-ls-js', TRP_PLUGIN_URL . 'assets/js/trp-clickable-ls.js', array('jquery'), TRP_PLUGIN_VERSION, true );

    wp_add_inline_style('trp-language-switcher-style', '.trp_language_switcher_shortcode .trp-language-switcher .trp-ls-shortcode-current-language.trp-ls-clicked{
    visibility: hidden;
}

.trp_language_switcher_shortcode .trp-language-switcher:hover div.trp-ls-shortcode-current-language{
    visibility: visible;
}

.trp_language_switcher_shortcode .trp-language-switcher:hover div.trp-ls-shortcode-language{
    visibility: hidden;
    height: 1px;
}
.trp_language_switcher_shortcode .trp-language-switcher .trp-ls-shortcode-language.trp-ls-clicked,
.trp_language_switcher_shortcode .trp-language-switcher:hover .trp-ls-shortcode-language.trp-ls-clicked{
    visibility:visible;
    height:auto;
    position: absolute;
    left: 0;
    top: 0;
    display: inline-block !important;
}');
}

function trp_open_language_switcher_on_click(){
    $option = get_option( 'trp_advanced_settings', true );

    if(isset($option['open_language_switcher_shortcode_on_click']) && $option['open_language_switcher_shortcode_on_click'] !== 'no'){
        add_action( 'wp_enqueue_scripts', 'trp_lsclick_enqueue_scriptandstyle', 99 );
    }
}

trp_open_language_switcher_on_click();