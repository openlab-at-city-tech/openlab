<?php
/**
 * Plugin Name: OpenLab TOC
 * Plugin URI:  https://openlab.citytech.cuny.edu/
 * Description: Modifications for Easy TOC plugin.
 * Author:      OpenLab
 * Author URI:  https://openlab.citytech.cuny.edu/
 * Version:     1.0.0
 */

namespace OpenLab\TOC;

const VERSION = '1.0.0';

/**
 * Conditnionally load our modifications.
 *
 * @return void
 */
function bootstrap() {
	if ( ! function_exists( 'ezTOC' ) ) {
		return;
	}

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
	add_filter( 'genesis_attr_entry-title', __NAMESPACE__ . '\\add_anchor' );

	// This breaks Highlighter Pro for some reason.
	// add_filter( 'ez_toc_extract_headings_content', __NAMESPACE__ . '\\prepend_title' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Reprend entry title to the content.
 * Allows to be parsed as ToC.
 *
 * @param string $content
 * @return string
 */
function prepend_title( $content ) {
	$title = sprintf( '<h1 class="entry-title">%s</h1>', get_the_title() );

	$title .= $content;

	return $title;
};

/**
 * Adds the 'id' attribute that is used by ToC.
 *
 * @param  array $attributes
 * @return array attributes
 */
function add_anchor( $attributes ) {
	$attributes['id'] = sanitize_title_with_dashes( get_the_title() );

	return $attributes;
}

/**
 * Enqueue our scripts and styles.
 *
 * @return void
 */
function enqueue_assets() {
	wp_enqueue_script(
		'openalab-toc-script',
		plugins_url( 'assets/js/openlab-toc.js', __FILE__ ),
		[ 'jquery' ],
		VERSION,
		true
	);

	wp_enqueue_style(
		'openalab-toc-style',
		plugins_url( 'assets/css/openlab-toc.css', __FILE__ ),
		[],
		VERSION
	);
}
