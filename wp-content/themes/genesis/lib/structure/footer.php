<?php
/**
 * Adds footer structure.
 *
 * @package Genesis
 */


add_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
/**
 * Echos the markup necessary to facilitate the footer
 * widget areas. The child theme must style them.
 *
 * @since 1.6
 */
function genesis_footer_widget_areas() {

	$footer_widgets = get_theme_support( 'genesis-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) )
		return;

	$footer_widgets = (int) $footer_widgets[0];

	/**
	 * Check to see if first widget area has widgets. If not,
	 * do nothing. No need to check all footer widget areas.
	 */
	if ( ! is_active_sidebar( 'footer-1' ) )
		return;

	$output = '';
	$counter = 1;

	while ( $counter <= $footer_widgets ) {

		/** Darn you, WordPress! Gotta output buffer. */
		ob_start();
		dynamic_sidebar( 'footer-' . $counter );
		$widgets = ob_get_clean();

		$output .= sprintf( '<div class="footer-widgets-%d widget-area">%s</div>', $counter, $widgets );

		$counter++;

	}

	echo apply_filters( 'genesis_footer_widget_areas', sprintf( '<div id="footer-widgets" class="footer-widgets">%2$s%1$s%3$s</div>', $output, genesis_structural_wrap( 'footer-widgets', 'open', 0 ), genesis_structural_wrap( 'footer-widgets', 'close', 0 ) ) );

}

add_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
/**
 * Echos the opening div tag for the footer
 *
 * @since 1.2
 */
function genesis_footer_markup_open() {

	echo '<div id="footer" class="footer">';
	genesis_structural_wrap( 'footer', 'open' );

}

add_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
/**
 * Echos the closing div tag for the footer
 *
 * @since 1.2
 */
function genesis_footer_markup_close() {

	genesis_structural_wrap( 'footer', 'close' );
	echo '</div><!-- end #footer -->' . "\n";

}

add_filter( 'genesis_footer_output', 'do_shortcode', 20 );
add_action( 'genesis_footer', 'genesis_do_footer' );
/**
 * Echo the contents of the footer. Execute any shortcodes that might be present.
 *
 * @since 1.0.1
 */
function genesis_do_footer() {

	// Build the filterable text strings. Includes shortcodes.
	$backtotop_text = apply_filters( 'genesis_footer_backtotop_text', '[footer_backtotop]' );
	$creds_text = apply_filters( 'genesis_footer_creds_text', sprintf( '[footer_copyright before="%1$s "] [footer_childtheme_link after=" %2$s"] [footer_genesis_link after=" %3$s"] [footer_wordpress_link after=" %3$s"] [footer_loginout]', __( 'Copyright', 'genesis' ), __( 'on', 'genesis' ), g_ent( '&middot;' ) ) );

	// For backward compatibility (pre-1.1 filter)
	if ( apply_filters( 'genesis_footer_credits', FALSE ) ) {
		$filtered = apply_filters( 'genesis_footer_credits', sprintf( '[footer_childtheme_link after=" %s"] [footer_genesis_link after=" %s"] [footer_wordpress_link]', __( 'on', 'genesis' ), g_ent( '&middot;' ) ) );
		$creds_text = sprintf( '[footer_copyright before="%s "] %s [footer_loginout before="%s "]', __( 'Copyright', 'genesis' ), $filtered, g_ent( '&middot;' ) );
	}

	$backtotop = $backtotop_text ? sprintf( '<div class="gototop"><p>%s</p></div>', $backtotop_text ) : '';
	$creds = $creds_text ? sprintf( '<div class="creds"><p>%s</p></div>', g_ent( $creds_text ) ) : '';

	$output = $backtotop . $creds;

	echo apply_filters( 'genesis_footer_output', $output, $backtotop_text, $creds_text );

}

add_filter( 'genesis_footer_scripts', 'do_shortcode' );
add_action( 'wp_footer', 'genesis_footer_scripts' );
/**
 * Echo the footer scripts, defined in Theme Settings.
 *
 * @since 1.1
 */
function genesis_footer_scripts() {

	echo apply_filters('genesis_footer_scripts', genesis_option('footer_scripts'));

}