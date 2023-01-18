<?php
/**
 * Custom functions for header layouts.
 *
 * @package Miniva
 */

/**
 * Get layouts data for header
 *
 * @return array
 */
function miniva_get_header_layouts() {
	return apply_filters(
		'miniva_header_layouts',
		array(
			'top'  => esc_html__( 'Top', 'miniva' ),
			'side' => esc_html__( 'Side', 'miniva' ),
		)
	);
}

/**
 * Insert container in header.
 */
add_action( 'miniva_header_start', 'miniva_container_open' );
add_action( 'miniva_header_end', 'miniva_container_close' );

/**
 * Add custom classes to the array of body classes.
 *
 * @param  array $classes Classes for the body element.
 * @return array
 */
function miniva_header_body_classes( $classes ) {
	if ( get_theme_mod( 'header_layout', 'top' ) === 'top' ) {
		$classes[] = 'logo-top';

		if ( get_theme_mod( 'menu_centered', true ) ) {
			$classes[] = 'menu-centered';
		} else {
			$classes[] = 'menu-default';
		}

		if ( get_theme_mod( 'logo_centered', true ) ) {
			$classes[] = 'logo-centered';
		} else {
			$classes[] = 'logo-default';
		}
	} else {
		$classes[] = 'logo-side';
	}

	if ( get_theme_mod( 'submenu_color', 'dark' ) === 'light' ) {
		$classes[] = 'submenu-light';
	}

	return $classes;
}
add_filter( 'body_class', 'miniva_header_body_classes' );


/**
 * Insert search form in header / main navigation.
 *
 * @param array $items Array of menu item.
 * @param obj   $args  Menu arguments.
 */
function miniva_header_search( $items, $args ) {
	if ( get_theme_mod( 'header_search', false ) && 'menu-1' === $args->theme_location ) {
		$search = '<li class="header-search">' . get_search_form( false ) . '</li>';
		return $items . $search;
	}

	return $items;
}
add_filter( 'wp_nav_menu_items', 'miniva_header_search', 10, 2 );
