<?php

/**
 * Manage loading of concatenated assets (JS, CSS).
 */

/**
 * Concatenate buddypress.js dependencies.
 *
 * @param array $deps
 * @return array
 */
function openlab_bp_js_dependencies( $deps ) {
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		return $deps;
	}

	wp_register_script( 'openlab-buddypress', content_url( 'js/buddypress.js' ), array( 'jquery' ) );

	$concat = array(
		'bp-confirm',
		'bp-widget-members',
		'bp-jquery-query',
		'bp-jquery-cookie',
		'bp-jquery-scroll-to',
	);

	$deps   = array_diff( $deps, $concat );
	$deps[] = 'openlab-buddypress';

	return $deps;
}
add_filter( 'bp_core_get_js_dependencies', 'openlab_bp_js_dependencies' );
