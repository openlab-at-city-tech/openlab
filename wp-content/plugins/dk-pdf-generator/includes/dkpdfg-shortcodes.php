<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
* [dkpdfg-button]
*/
function dkpdfg_button_shortcode( $atts, $content = null ) {
/*
	global $dkpdfg_button_atts;

	$dkpdfg_button_atts = shortcode_atts( array(
		'columns' => '2',
	), $atts );
*/

	$template = new DKPDFG_Template_Loader;

	ob_start();

	$template->get_template_part( 'dkpdfg-button' );

	return ob_get_clean();

}
add_shortcode( 'dkpdfg-button', 'dkpdfg_button_shortcode' );
