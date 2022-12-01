<?php
/**
 * Custom style functions
 *
 * @package Miniva
 */

/**
 * Prints custom css
 */
function miniva_custom_styles() {
	$h = get_theme_mod( 'accent_color', 349 );
	$s = apply_filters( 'miniva_color_saturation', 59 );
	$l = apply_filters( 'miniva_color_lightness', 53 );

	$l2 = max( 0, $l - 13 );

	if ( 349 !== $h ) {
		$style  = '.page-numbers.current, a.page-numbers:hover, .search-form .search-submit, .tagcloud a:hover, .calendar_wrap #today, .bypostauthor > article .fn, #submit, input[type="submit"], button[type="submit"], #infinite-handle span, .fp-post .fp-readmore-link:hover { background-color: hsl(' . absint( $h ) . ', ' . absint( $s ) . '%, ' . absint( $l ) . '%); }';
		$style .= '@media (min-width: 768px) { ul.primary-menu > li:hover > a { background-color: hsl(' . absint( $h ) . ', ' . absint( $s ) . '%, ' . absint( $l ) . '%); } }';
		$style .= 'a, .post-navigation a:hover, .jetpack-social-navigation a:hover, .widget a:hover, .calendar_wrap a, .jetpack_widget_social_icons a:hover, .entry-title a:hover { color: hsl(' . absint( $h ) . ', ' . absint( $s ) . '%, ' . absint( $l ) . '%); }';
		$style .= '#submit:hover, input[type="submit"]:hover, button[type="submit"]:hover, #infinite-handle:hover span { background-color: hsl(' . absint( $h ) . ', ' . absint( $s ) . '%, ' . absint( $l2 ) . '%); }';
		wp_add_inline_style( 'miniva-style', $style );
	}
}
add_action( 'wp_enqueue_scripts', 'miniva_custom_styles' );
