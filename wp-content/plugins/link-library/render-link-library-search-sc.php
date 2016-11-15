<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/**
 *
 * Render the output of the link-library-search shortcode
 *
 * @param $libraryoptions   Selected library settings array
 *
 * @return                  List of categories output for browser
 */

function RenderLinkLibrarySearchForm( $libraryoptions ) {

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	$output = '<form method="GET" id="llsearch"';

	if ( !empty( $searchresultsaddress ) ) {
		$output .= ' action="' . $searchresultsaddress . '"';
	}

	$output .= ">\n";
	$output .= "<div>\n";
	$output .= "<input type='text' onfocus=\"this.value=''\" value='" . $searchfieldtext . "' name='searchll' id='searchll' />";
	$output .= "<input type='submit' value='" . $searchlabel . "' />";
	$output .= "</div>\n";
	$output .= "</form>\n\n";

	$output .= "<script type='text/javascript'>\n";
	$output .= "jQuery(document).ready(function () {\n";
	$output .= "\tjQuery('#llsearch').submit(function () {\n";
	$output .= "\t\tif (jQuery('#searchll').val() == '" . $searchfieldtext . "') {\n";
	$output .= "\t\t\treturn false;\n";
	$output .= "\t\t}\n";
	$output .= "\t\telse {\n";
	$output .= "\t\t\treturn true;\n";
	$output .= "\t\t}\n";
	$output .= "\t});\n";
	$output .= "});\n";
	$output .= "</script>";

	return $output;
}