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

	if ( empty( $searchlabel ) ) {
		$searchlabel = __( 'Search', 'link-library' );
	}

	$output = '<form method="get" id="llsearch"';

	if ( !empty( $searchresultsaddress ) ) {
		$output .= ' action="' . $searchresultsaddress . '"';
	}

	$output .= ">\n";
	$output .= "<div>\n";
	$output .= "<input type='text' onfocus=\"this.value=''\" value='" . $searchlabel . "...' name='searchll' id='searchll' />";
	$output .= "<input type='hidden' value='" . get_the_ID() . "' name='page_id' id='page_id' />";
	$output .= "<input type='submit' value='" . $searchlabel . "' />";
	$output .= "</div>\n";
	$output .= "</form>\n\n";

	return $output;
}