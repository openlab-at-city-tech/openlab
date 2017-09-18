<?php

// action for loading wpsdc's style
add_action( 'wp_head', 'wpsdc_load_css' );

// function for loading wpsdc's style
function wpsdc_load_css()
{
	$wpsdc_options = get_option( 'wpsdc_options' );
	$wpsdc_font_color = $wpsdc_options['option_font_color'];
	$wpsdc_font_color_css = ( isset( $wpsdc_font_color ) && ! empty( $wpsdc_font_color ) ) ? 'color : ' . $wpsdc_font_color . ';' : '' ;
	
	if ( $wpsdc_options['option_display_mode'] == 'normal' ) {
		echo 
		'<style type="text/css">
			.wpsdc-drop-cap {				
				padding : 0;
				font-size : 5em;
				line-height : 0.8em;'
				. $wpsdc_font_color_css
			. '}
		</style>';
	} elseif ( $wpsdc_options['option_display_mode'] == 'float' ) {
		echo 
		'<style type="text/css">
			.wpsdc-drop-cap {
				float : left;				
				padding : 0.25em 0.05em 0.25em 0;				
				font-size : 5em;
				line-height : 0.4em;'
				. $wpsdc_font_color_css
			. '}
		</style>';
	} elseif ( $wpsdc_options['option_display_mode'] == 'custom' ) {
		echo 
		'<style type="text/css">
			.wpsdc-drop-cap {'
				. $wpsdc_options['option_custom_css']
				. $wpsdc_font_color_css
			. '}
		</style>';
	}	
}

// action for registering iris color picker
add_action( 'admin_enqueue_scripts', 'wpsdc_load_color_picker' );

// funtion for loading iris color picker
function wpsdc_load_color_picker() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wpsdc-script', plugins_url( 'js/script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}