<?php
define( 'AEONIUM_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'AEONIUM_THEME_NAME', wp_get_theme()->get( 'Name' ) );
define( 'AEONIUM_TEMPLATE_DIR', get_template_directory() );
define( 'AEONIUM_TEMPLATE_DIR_URI', get_template_directory_uri() );

if ( ! function_exists( 'aeonium_support' ) ) {
	function aeonium_support()  {
		// Make theme available for translation.
		load_theme_textdomain( 'aeonium', AEONIUM_TEMPLATE_DIR . '/languages' );

		// Adding support for core block visual styles.
		add_theme_support( 'wp-block-styles' );

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );
	}
}
add_action( 'after_setup_theme', 'aeonium_support' );


/**
 * Enqueue scripts and styles.
 */
function aeonium_scripts_styles() {
	wp_enqueue_style( 'aeonium-style', AEONIUM_TEMPLATE_DIR_URI . '/style.css', array(), AEONIUM_VERSION );
	wp_enqueue_style( 'aeonium-fonts-style', aeonium_google_fonts(), array(), AEONIUM_VERSION );
}
add_action( 'wp_enqueue_scripts', 'aeonium_scripts_styles' );


/**
 * Enqueue editor styles.
 */
function aeonium_editor_styles() {
	add_editor_style( array( 'style.css', aeonium_google_fonts() ) );
}
add_action( 'admin_init', 'aeonium_editor_styles' );


/**
 * Block pattern categories.
 */
function aeonium_register_pattern_cats() {
	register_block_pattern_category( 'aeonium', array( 'label' => __( 'Aeonium', 'aeonium' ) ) );
}
add_action( 'init', 'aeonium_register_pattern_cats', 9 );


/**
 * Get Google fonts and save locally with WPTT Webfont Loader.
 */
function aeonium_google_fonts() {
	$font_families = array(
		'Inter:wght@100;200;300;400;500;600;700;800;900',
		'Poppins:wght@100;200;300;400;500;600;700;800;900',
		'Montserrat:wght@100;200;300;400;500;600;700;800;900',
		'Source+Serif+Pro:wght@200;300;400;600;700;900',
		'Space+Mono:wght@400;700'
	);

	$fonts_url = add_query_arg( array(
		'family' => implode( '&family=', $font_families ),
		'display' => 'swap',
	), 'https://fonts.googleapis.com/css2' );

	require_once get_theme_file_path( 'inc/wptt-webfont-loader.php' );

	return wptt_get_webfont_url( esc_url_raw( $fonts_url ) );
}


/**
 * Block styles.
 */
require AEONIUM_TEMPLATE_DIR . '/inc/styles.php';


/**
 * Display archives placeholder image if no post featured image.
 * Can be changed in child theme.json: settings.custom.featured-image.placeholder
 * Use either absolute URI or image in /theme-slug/assets/images/ directory.
 */
function aeonium_featured_image_placeholder( $html ) {
	$global_styles = WP_Theme_JSON_Resolver::get_merged_data()->get_settings();

	$placeholder = ! empty( $global_styles['custom']['featured-image']['placeholder'] ) ? $global_styles['custom']['featured-image']['placeholder'] : false;

	if ( $placeholder && $html === '' && !is_single() ) {

		if ( substr( $placeholder, 0, 7 ) === "http://" || substr( $placeholder, 0, 8 ) === "https://" ) {
			$placeholder_url = esc_url( $placeholder );
		} else {
			$placeholder_url = esc_url( AEONIUM_TEMPLATE_DIR_URI . '/assets/images/' . $placeholder );
		}

		$html = '<img class="attachment-post-thumbnail wp-post-image placeholder" src="' . $placeholder_url . '">';
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'aeonium_featured_image_placeholder' );


/**
 * Filter allowed CSS to allow min() property.
 * Will be removed when fixed in core.
 * See core ticket: https://core.trac.wordpress.org/ticket/55966
 */
function aeonium_safe_css( $allow_css ) {
	$allow_css = 1;
	return $allow_css;
}
add_filter( 'safecss_filter_attr_allow_css', 'aeonium_safe_css' );
