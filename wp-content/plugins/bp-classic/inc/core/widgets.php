<?php
/**
 * BP Classic Core Widget Functions.
 *
 * @package bp-classic\inc\core
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Login widget.
 *
 * @since 1.0.0
 */
function bp_classic_register_login_widget() {
	register_widget( 'BP_Classic_Core_Login_Widget' );
}

/**
 * Register bp-core widgets.
 *
 * @since 1.0.0
 */
function bp_classic_register_widgets() {
	add_action( 'widgets_init', 'bp_classic_register_login_widget' );
}
add_action( 'bp_register_widgets', 'bp_classic_register_widgets' );

/**
 * Injects specific BuddyPress CSS classes into a widget sidebar.
 *
 * Helps to standardize styling of BuddyPress widgets within a theme that
 * does not use dynamic CSS classes in their widget sidebar's 'before_widget'
 * call.
 *
 * @since 1.0.0
 *
 * @global array $wp_registered_widgets Current registered widgets.
 *
 * @param array $params Current sidebar params.
 * @return array
 */
function bp_classic_inject_widget_css_class( $params ) {
	global $wp_registered_widgets;

	$widget_id = $params[0]['widget_id'];

	// If callback isn't an array, bail.
	if ( false === is_array( $wp_registered_widgets[ $widget_id ]['callback'] ) ) {
		return $params;
	}

	// If the current widget isn't a BuddyPress one, stop!
	// We determine if a widget is a BuddyPress widget, if the widget class
	// begins with 'bp_'.
	if ( 0 !== strpos( $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base, 'bp_' ) ) {
		return $params;
	}

	// Dynamically add our widget CSS classes for BP widgets if not already there.
	$classes = array();

	// Try to find 'widget' CSS class.
	if ( false === strpos( $params[0]['before_widget'], 'widget ' ) ) {
		$classes[] = 'widget';
	}

	// Try to find 'buddypress' CSS class.
	if ( false === strpos( $params[0]['before_widget'], ' buddypress' ) ) {
		$classes[] = 'buddypress';
	}

	// Stop if widget already has our CSS classes.
	if ( empty( $classes ) ) {
		return $params;
	}

	// CSS injection time!
	$params[0]['before_widget'] = str_replace( 'class="', 'class="' . implode( ' ', $classes ) . ' ', $params[0]['before_widget'] );

	return $params;
}
add_filter( 'dynamic_sidebar_params', 'bp_classic_inject_widget_css_class' );

/**
 * Returns the upper limit on the "max" item count, for widgets that support it.
 *
 * @since 1.0.0
 *
 * @param string $widget_class Optional. Class name of the calling widget.
 * @return int
 */
function bp_get_widget_max_count_limit( $widget_class = '' ) {
	/**
	 * Filters the upper limit on the "max" item count, for widgets that support it.
	 *
	 * @since 5.0.0
	 *
	 * @param int    $count        Defaults to 50.
	 * @param string $widget_class Class name of the calling widget.
	 */
	return apply_filters( 'bp_get_widget_max_count_limit', 50, $widget_class );
}

/**
 * Registers Classic widgets styles.
 *
 * @since 1.0.0
 */
function bp_classic_register_template_pack_widget_styles() {
	if ( current_theme_supports( 'buddypress' ) ) {
		return;
	}

	$bpc = bp_classic();
	$tp  = bp_get_theme_package_id();

	$template_pack_file = sprintf( trailingslashit( $bpc->inc_dir ) . 'templates/css/widgets-%s.css', $tp );
	if ( file_exists( $template_pack_file ) ) {
		wp_register_style(
			'bp-classic-widget-styles',
			sprintf( trailingslashit( $bpc->inc_url ) . 'templates/css/widgets-%s.css', $tp ),
			array(),
			$bpc->version
		);
	}
}
add_action( 'bp_enqueue_scripts', 'bp_classic_register_template_pack_widget_styles', 1 );
