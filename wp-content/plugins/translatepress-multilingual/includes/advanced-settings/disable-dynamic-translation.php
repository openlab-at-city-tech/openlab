<?php

add_filter( 'trp_register_advanced_settings', 'trp_register_disable_dynamic_translation', 30 );
function trp_register_disable_dynamic_translation( $settings_array ){
	$settings_array[] = array(
		'name'          => 'disable_dynamic_translation',
		'type'          => 'checkbox',
		'label'         => esc_html__( 'Disable dynamic translation', 'translatepress-multilingual' ),
		'description'   => wp_kses( __( 'It disables detection of strings displayed dynamically using JavaScript. <br/>Strings loaded via a server side AJAX call will still be translated.', 'translatepress-multilingual' ), array( 'br' => array() ) ),
        'id'            =>'troubleshooting',
	);
	return $settings_array;
}

add_filter( 'trp_enable_dynamic_translation', 'trp_adst_disable_dynamic' );
function trp_adst_disable_dynamic( $enable ){
	$option = get_option( 'trp_advanced_settings', true );
	if ( isset( $option['disable_dynamic_translation'] ) && $option['disable_dynamic_translation'] === 'yes' ){
		return false;
	}
	return $enable;
}

add_filter( 'trp_editor_missing_scripts_and_styles', 'trp_adst_disable_dynamic2' );
function trp_adst_disable_dynamic2( $scripts ){
	$option = get_option( 'trp_advanced_settings', true );
	if ( isset( $option['disable_dynamic_translation'] ) && $option['disable_dynamic_translation'] === 'yes' ){
		unset($scripts['trp-translate-dom-changes.js']);
	}
	return $scripts;
}