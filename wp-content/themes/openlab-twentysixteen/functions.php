<?php

/**
 * Enqueue parent stylesheet.
 */
add_action( 'after_setup_theme', function() {
	if ( ! is_admin() ) {
		wp_enqueue_style( 'twentysixteen', get_template_directory_uri() . '/style.css' );
	}
} );

/**
 * Theme requires Breadcrumb Navxt.
 */
function openlab_activate_breadcrumb_navxt_on_openlab_twentysixteen() {
	if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	if ( ! is_plugin_active( 'breadcrumb-navxt/breadcrumb-navxt.php' ) ) {
		activate_plugin( 'breadcrumb-navxt/breadcrumb-navxt.php' );
	}

	// Set default plugin options.
	$bcn_options = get_option( 'bcn_options', array() );
	if ( ! $bcn_options ) {
		$bcn_options['bpaged_display'] = true;
		$bcn_options['Hhome_template'] = '<span property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" title="Go to title." href="%link%" class="%type%"><span property="name">Home</span></a><meta property="position" content="%position%"></span>';
		$bcn_options['Hhome_template_no_anchor'] = '<span property="itemListElement" typeof="ListItem"><span property="name">Home</span><meta property="position" content="%position%"></span>';
		$bcn_options['bblog_display'] = false;
		$bcn_options['bmainsite_display'] = false;
		update_option( 'bcn_options', $bcn_options );
	}
}
add_action( 'after_setup_theme', 'openlab_activate_breadcrumb_navxt_on_openlab_twentysixteen', 50 );

/**
 * Ensure that breadcrumbs settings are filled.
 */
add_filter(
	'default_option_bcn_options',
	function( $option ) {
		$option = array();
		$option['bpaged_display'] = true;
		$option['Hhome_template'] = '<span property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" title="Go to title." href="%link%" class="%type%"><span property="name">Home</span></a><meta property="position" content="%position%"></span>';
		$option['Hhome_template_no_anchor'] = '<span property="itemListElement" typeof="ListItem"><span property="name">Home</span><meta property="position" content="%position%"></span>';
		$option['bblog_display'] = false;
		$option['bmainsite_display'] = false;

		return $option;
	}
);
