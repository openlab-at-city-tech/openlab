<?php
/**
 * Moina Wp functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Moina Wp
 */

if ( ! defined( 'KOYEL_BLOG_VERSION' ) ) {
	$koyel_blog_theme = wp_get_theme();
	define( 'KOYEL_BLOG_VERSION', $koyel_blog_theme->get( 'Version' ) );
}


/**
 * Register custom fonts.
 */
function koyel_blog_fonts_url() {
    $fonts_url = '';

    $font_families = array();
    $font_families[] = 'Sora:wght@200;300;400;500;600;700;800';
    $font_families[] = 'Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
    $query_args = array(
        'family' => urlencode( implode( '|', $font_families ) ),
        'subset' => urlencode( 'latin,latin-ext' ),
    );

    $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    return esc_url_raw( $fonts_url );
}


/**
 * Enqueue scripts and styles.
 */
function koyel_blog_scripts() {
    wp_enqueue_style( 'koyel-blog-google-fonts', koyel_blog_fonts_url(), array(), null );
    wp_enqueue_style( 'koyel-blog-parent-style', get_template_directory_uri() . '/style.css',array('bootstrap','slicknav','koyel-default-block','koyel-style'), '', 'all');
    wp_enqueue_style( 'koyel-blog-main-style',get_stylesheet_directory_uri() . '/assets/css/main-style.css',array(), KOYEL_BLOG_VERSION, 'all');
}
add_action( 'wp_enqueue_scripts', 'koyel_blog_scripts' );

/*
 * This theme styles the visual editor to resemble the theme style,
 * specifically font, colors, and column width.
*/
add_editor_style( array(koyel_blog_fonts_url() ) ); 

/**
 * Custom excerpt length.
 */
function koyel_blog_excerpt_length( $length ) {
    if ( is_admin() ) return $length;
    return 19;
}
add_filter( 'excerpt_length', 'koyel_blog_excerpt_length', 999 );

/**
 * Custom excerpt More.
 */
function koyel_blog_excerpt_more( $more ) {
    if ( is_admin() ) return $more;
    return '.';
}
add_filter( 'excerpt_more', 'koyel_blog_excerpt_more' );

/**
 * Load Razia Photography Tags files.
 */
require get_stylesheet_directory() . '/inc/customizer.php';

/**
 * Load Razia Photography Tags files.
 */
require get_stylesheet_directory() . '/inc/template-tags.php';