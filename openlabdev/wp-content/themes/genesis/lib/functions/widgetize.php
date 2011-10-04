<?php
/**
 * Register default sidebars.
 *
 * @package Genesis
 */

/**
 * This function expedites the widget area registration process by taking
 * common things, before/after_widget, before/after_title, and doing them automatically.
 *
 * @uses wp_parse_args, register_sidebar
 * @since 1.0.1
 * @author Charles Clarkson
 */
function genesis_register_sidebar($args) {
	$defaults = (array) apply_filters( 'genesis_register_sidebar_defaults', array(
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => "</div></div>\n",
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => "</h4>\n"
	) );

	$args = wp_parse_args($args, $defaults);

	return register_sidebar($args);
}


add_action( 'genesis_setup', 'genesis_register_default_widget_areas' );
/**
 * This function registers all the default Genesis widget areas.
 *
 * @since 1.6
 */
function genesis_register_default_widget_areas() {

	if ( genesis_get_option('header_right') ) {
		genesis_register_sidebar(array(
			'name' => __('Header Right', 'genesis'),
			'description' => __('This is the right side of the header', 'genesis'),
			'id' => 'header-right'
		));
	}

	genesis_register_sidebar(array(
		'name' => __('Primary Sidebar', 'genesis'),
		'description' => __('This is the primary sidebar if you are using a 2 or 3 column site layout option', 'genesis'),
		'id' => 'sidebar'
	));

	genesis_register_sidebar(array(
		'name' => __('Secondary Sidebar', 'genesis'),
		'description' => __('This is the secondary sidebar if you are using a 3 column site layout option', 'genesis'),
		'id' => 'sidebar-alt'
	));

}

add_action( 'after_setup_theme', 'genesis_register_footer_widget_areas' );
/**
 * This function registers footer widget areas based on the number of
 * widget areas the user wishes to create with add_theme_support()
 *
 * @since 1.6
 */
function genesis_register_footer_widget_areas() {

	$footer_widgets = get_theme_support( 'genesis-footer-widgets' );

	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) )
		return;

	$footer_widgets = (int) $footer_widgets[0];

	$counter = 1;

	while ( $counter <= $footer_widgets ) {

		genesis_register_sidebar( array(
			'name' => sprintf( __( 'Footer %d', 'genesis' ), $counter ),
			'description' => sprintf( __( 'Footer %s widget area', '' ), $counter ),
			'id' => sprintf( 'footer-%d', $counter )
		) );

		$counter++;

	}

}