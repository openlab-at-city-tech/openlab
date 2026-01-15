<?php
/**
 * @package Simple Pull Quote
 * @author Toby Cryns
 * @version 1.6.4
 */
/*
	* @wordpress-plugin
	* Plugin Name: Simple Pull Quote
	* Version: 1.6.4
	* Plugin URI: http://www.themightymo.com/simple-pull-quote
	* Description: Easily add pull quotes to blog posts using shortcode.
	* Author: Toby Cryns
	* Author URI: https://tobycryns.com
	* License: GPL v3
	* Requires PHP: 7.2.5
*/

/*  Copyright 2009-2024 Toby Cryns  (email : toby at themightymo dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// Load the TinyMCE Stuff
require_once (dirname(__FILE__) . '/simple-pull-quote_tinymce.php');

function my_css() {
	echo '<link type="text/css" rel="stylesheet" href="' . esc_url( plugins_url( 'css/simple-pull-quote.css', __FILE__ ) ) . '" />' . "\n";
}

// Add the CSS file to the header when the page loads
add_action('wp_head', 'my_css');

/* Call the javascript file that loads the html editor button */
//add_action('admin_print_scripts', 'simplePullQuotes');
function simplePullQuotes() {
	wp_enqueue_script(
		'simple-pull-quotes',
		esc_url( plugin_dir_url(__FILE__) . 'simple-pull-quote.js' ),
		array('quicktags')
	);
}

// Load the custom TinyMCE plugin
function simple_pull_quotes_plugin( $plugins ) {
	$plugins['simplepullquotes'] = esc_url( plugins_url('/simple-pull-quote/tinymce3/editor_plugin.js') );
	return $plugins;
}

function specific_enqueue($hook_suffix) {
   if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
     add_action('admin_print_scripts', 'simplePullQuotes');
  }
}
add_action( 'admin_enqueue_scripts', 'specific_enqueue' );

// v1.5 with help from https://developer.wordpress.org/plugins/shortcodes/shortcodes-with-parameters/
function getSimplePullQuote( $atts, $content = null ) {
    // Set default attributes
    $pull_quote_atts = shortcode_atts( array(
        'class' => 'right',
    ), $atts );

	// Normalize content - run shortcodes, autop paragraphs, then sanitize allowed HTML
	if ( is_null( $content ) ) {
		$content = '';
	} else {
		$content = trim( $content );
		// Allow nested shortcodes to run first
		$content = do_shortcode( $content );
		// Add paragraph tags where appropriate
		$content = wpautop( $content );
		// Strip disallowed HTML (removes scripts, event handlers, etc.)
		$content = wp_kses_post( $content );
	}

	// Create the output with the appropriate class and safely-escaped attributes/content
	$output = '<div class="simplePullQuote ' . esc_attr( sanitize_html_class( $pull_quote_atts['class'] ) ) . '">';
	$output .= $content;
	$output .= '</div>';

	return $output;
}
add_shortcode('pullquote', 'getSimplePullQuote');



// LEGACY CODE for Version < 0.2.4

function getQuote(){
	global $post;
	$my_custom_field = get_post_meta($post->ID, "quote", true);
	/* Add CSS classes to the pull quote (a.k.a. Style the thing!) */
	$my_custom_field = wp_kses_post( $my_custom_field );
	return '<div class="simplePullQuote">' . $my_custom_field . '</div>'; 
}

/* Allow us to add the pull quote using Wordpress shortcode, "[quote]" */
add_shortcode('quote', 'getQuote');
function getQuote1(){
	global $post;
	$my_custom_field = get_post_meta($post->ID, "quote1", true);
	/* Add CSS classes to the pull quote (a.k.a. Style the thing!) */
	$my_custom_field = wp_kses_post( $my_custom_field );
	return '<div class="simplePullQuote">' . $my_custom_field . '</div>'; 
}

/* Allow us to add the pull quote using Wordpress shortcode, "[quote]" */
add_shortcode('quote1', 'getQuote1');

function getQuote2(){
	global $post;
	$my_custom_field = get_post_meta($post->ID, "quote2", true);

	/* Add CSS classes to the pull quote (a.k.a. Style the thing!) */
	$my_custom_field = wp_kses_post( $my_custom_field );
	return '<div class="simplePullQuote">' . $my_custom_field . '</div>'; 
}

// Allow us to add the pull quote using Wordpress shortcode, "[quote]" */
add_shortcode('quote2', 'getQuote2');