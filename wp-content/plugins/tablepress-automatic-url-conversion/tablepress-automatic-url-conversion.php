<?php
/*
Plugin Name: TablePress Extension: Automatic URL conversion
Plugin URI: https://tablepress.org/extensions/automatic-url-conversion/
Description: Custom Extension for TablePress to automatically make URLs (www, ftp, and email) in table cells clickable
Version: 1.3
Author: Tobias Bäthge
Author URI: https://tobias.baethge.com/
*/

/*
 * Usage and possible parameters:
 * [table id=1 automatic_url_conversion=true automatic_url_conversion_new_window=true automatic_url_conversion_rel_nofollow=true /]
 *
 * automatic_url_conversion: Whether URLs shall be made clickable.
 * automatic_url_conversion_new_window: Whether http(s) links shall open in a new window.
 * automatic_url_conversion_rel_nofollow: Whether http(s) links shall get the `nofollow` attribute.
 */

add_filter( 'tablepress_table_output', 'tablepress_auto_url_conversion', 10, 3 );
add_filter( 'tablepress_shortcode_table_default_shortcode_atts', 'tablepress_add_shortcode_parameter_auto_url_conversion' );

/**
 * Add Extension's parameters as a valid parameters to the [table /] Shortcode.
 */
function tablepress_add_shortcode_parameter_auto_url_conversion( $default_atts ) {
	$default_atts['automatic_url_conversion'] = false;
	$default_atts['automatic_url_conversion_new_window'] = false;
	$default_atts['automatic_url_conversion_rel_nofollow'] = false;
	return $default_atts;
}

/**
 * Convert URLs to links, if Shortcode parameter is set,
 * add the `target` attribute in http(s):// links,
 * or add the `rel` attribute, if the Shortcode parameter is set.
 */
function tablepress_auto_url_conversion ( $output, $table, $render_options ) {
	if ( $render_options['automatic_url_conversion'] ) {
		$output = make_clickable( $output );
	}

	if ( $render_options['automatic_url_conversion_new_window'] && $render_options['automatic_url_conversion_rel_nofollow'] ) {
		$output = str_replace( '<a href="http', '<a target="_blank" rel="nofollow" href="http', $output );
	} elseif ( $render_options['automatic_url_conversion_new_window'] ) {
		$output = str_replace( '<a href="http', '<a target="_blank" href="http', $output );
	} elseif ( $render_options['automatic_url_conversion_rel_nofollow'] ) {
		$output = str_replace( '<a href="http', '<a rel="nofollow" href="http', $output );
	}

	return $output;
}
