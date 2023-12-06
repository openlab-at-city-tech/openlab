<?php
function trp_register_fix_broken_html( $settings_array ){
    $settings_array[] = array(
        'name'          => 'fix_broken_html',
        'type'          => 'checkbox',
        'label'         => esc_html__( 'Fix broken HTML', 'translatepress-multilingual' ),
        'description'   => wp_kses( __( 'General attempt to fix broken or missing HTML on translated pages.<br/>', 'translatepress-multilingual' ), array( 'br' => array(), 'strong' => array() ) ),
        'id'            =>'troubleshooting',
    );
    return $settings_array;
}

add_filter('trp_try_fixing_invalid_html', 'trp_fix_broken_html');
function trp_fix_broken_html($allow) {

    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['fix_broken_html'] ) && $option['fix_broken_html'] === 'yes' ) {
        return true;
    }
    return $allow;
}