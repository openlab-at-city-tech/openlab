<?php
/**
 * Site, content, sidebar & footer layout functions
 *
 * @package Miniva
 */

/**
 * Get posts layouts
 *
 * @return array
 */
function miniva_get_posts_layouts() {
	return apply_filters(
		'miniva_posts_layouts',
		array(
			'large' => esc_html__( 'Large Image', 'miniva' ),
			'small' => esc_html__( 'Small Image', 'miniva' ),
			'grid'  => esc_html__( 'Grid', 'miniva' ),
		)
	);
}

/**
 * Get posts layout from option
 *
 * @return string
 */
function miniva_get_posts_layout() {
	$layout = get_theme_mod( 'posts_layout', 'large' );
	return $layout;
}

/**
 * Check if layout is grid
 *
 * @return string
 */
function miniva_is_grid() {
	if ( is_home() || is_archive() || is_search() ) {
		$layout  = miniva_get_posts_layout();
		$is_grid = strpos( $layout, 'grid' ) === 0;
		return apply_filters( 'miniva_is_grid', $is_grid );
	}
	return false;
}

/**
 * Get sidebar layouts
 *
 * @return array
 */
function miniva_get_sidebar_layouts() {
	return apply_filters(
		'miniva_sidebar_layouts',
		array(
			'right' => esc_html__( 'Right Sidebar', 'miniva' ),
			'left'  => esc_html__( 'Left Sidebar', 'miniva' ),
		)
	);
}

/**
 * Add custom classes to the array of body classes.
 *
 * @param  array $classes Classes for the body element.
 * @return array
 */
function miniva_content_body_classes( $classes ) {
	$site_layout = get_theme_mod( 'site_layout', 'boxed' );
	$classes[]   = esc_attr( $site_layout );
	if ( is_page_template( 'template-fullwidth.php' ) ) {
		$classes[] = 'full-width';
	} elseif ( is_page_template( 'template-centered.php' ) ) {
		$classes[] = 'no-sidebar';
	} else {
		$sidebar_layout = get_theme_mod( 'sidebar_layout', 'right' );
		if ( 'right' === $sidebar_layout ) {
			$classes[] = 'sidebar-right';
		} elseif ( 'left' === $sidebar_layout ) {
			$classes[] = 'sidebar-left';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'miniva_content_body_classes' );

/**
 * Insert extra class name for content section.
 */
function miniva_content_class() {
	$class = ' container';
	$class = apply_filters( 'miniva_content_class', $class );
	echo esc_attr( $class );
}

/**
 * Insert container in footer.
 */
add_action( 'miniva_footer_start', 'miniva_container_open', 8 );
add_action( 'miniva_footer_end', 'miniva_container_close' );

/**
 * Insert footer widgets
 */
function miniva_footer_widgets() {
	$footer_col = miniva_get_footer_col();
	miniva_container_open( 'footer-widgets footer-widgets-' . $footer_col );
	for ( $i = 1; $i <= $footer_col; $i++ ) {
		miniva_container_open( 'footer-widget-' . $i );
		if ( is_active_sidebar( 'footer-' . $i ) ) {
			dynamic_sidebar( 'footer-' . $i );
		}
		miniva_container_close();
	}
	miniva_container_close();
}
add_action( 'miniva_footer_start', 'miniva_footer_widgets', 9 );

/**
 * Get footer widgets column number
 *
 * @return int
 */
function miniva_get_footer_col() {
	$footer_col = apply_filters( 'miniva_footer_col', 3 );
	$footer_col = absint( $footer_col );
	$footer_col = min( max( $footer_col, 1 ), 6 );
	return $footer_col;
}
