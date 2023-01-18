<?php

// Front-end scripts
function ct_period_load_scripts_styles() {

	$font_args = array(
		'family'  => urlencode( 'Roboto:300,300italic,400,700' ),
		'subset'  => urlencode( 'latin,latin-ext' ),
		'display' => 'swap'
	);
	$fonts_url = add_query_arg( $font_args, '//fonts.googleapis.com/css' );
	
	wp_enqueue_style( 'ct-period-google-fonts', $fonts_url );

	wp_enqueue_script( 'ct-period-js', get_template_directory_uri() . '/js/build/production.min.js', array( 'jquery' ), '', true );
	wp_localize_script( 'ct-period-js', 'ct_period_objectL10n', array(
		'openMenu'       => esc_html_x( 'open menu', 'verb: open the menu', 'period' ),
		'closeMenu'      => esc_html_x( 'close menu', 'verb: close the menu', 'period' ),
		'openChildMenu'  => esc_html_x( 'open dropdown menu', 'verb: open the dropdown menu', 'period' ),
		'closeChildMenu' => esc_html_x( 'close dropdown menu', 'verb: close the dropdown menu', 'period' )
	) );

	wp_enqueue_style( 'ct-period-font-awesome', get_template_directory_uri() . '/assets/font-awesome/css/all.min.css' );

	wp_enqueue_style( 'ct-period-style', get_stylesheet_uri() );

	// enqueue comment-reply script only on posts & pages with comments open ( included in WP core )
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ct_period_load_scripts_styles' );

// Back-end scripts
function ct_period_enqueue_admin_styles( $hook ) {

	if ( $hook == 'appearance_page_period-options' ) {
		wp_enqueue_style( 'ct-period-admin-styles', get_template_directory_uri() . '/styles/admin.min.css' );
	}
	if ( $hook == 'post.php' || $hook == 'post-new.php' ) {

		$font_args = array(
			'family' => urlencode( 'Roboto:300,300i,400,700' ),
			'subset' => urlencode( 'latin,latin-ext' )
		);
		$fonts_url = add_query_arg( $font_args, '//fonts.googleapis.com/css' );
	
		wp_enqueue_style( 'ct-period-google-fonts', $fonts_url );
	}
}
add_action( 'admin_enqueue_scripts', 'ct_period_enqueue_admin_styles' );

// Customizer scripts
function ct_period_enqueue_customizer_scripts() {
	wp_enqueue_script( 'ct-period-customizer-js', get_template_directory_uri() . '/js/build/customizer.min.js', array( 'jquery' ), '', true );
	wp_enqueue_style( 'ct-period-customizer-styles', get_template_directory_uri() . '/styles/customizer.min.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'ct_period_enqueue_customizer_scripts' );

/*
 * Script for live updating with customizer options. Has to be loaded separately on customize_preview_init hook
 * transport => postMessage
 */
function ct_period_enqueue_customizer_post_message_scripts() {
	wp_enqueue_script( 'ct-period-customizer-post-message-js', get_template_directory_uri() . '/js/build/postMessage.min.js', array( 'jquery' ), '', true );

}
add_action( 'customize_preview_init', 'ct_period_enqueue_customizer_post_message_scripts' );