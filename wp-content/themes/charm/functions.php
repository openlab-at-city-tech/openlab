<?php

/**
 * -----------------------------------------------------------------------------
 * Set up theme defaults and registers support for various WordPress features
 * -----------------------------------------------------------------------------
 */

function rain_theme_setup() {
	load_theme_textdomain( 'themerain', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	register_nav_menu( 'menu-header', 'Header menu' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'project', 920 );
	add_editor_style( array( 'assets/css/editor-style.css', 'includes/font-awesome/css/font-awesome.min.css', tr_fonts_url() ) );
}
add_action( 'after_setup_theme', 'rain_theme_setup' );

/**
 * -----------------------------------------------------------------------------
 * Set up the content width value
 * -----------------------------------------------------------------------------
 */

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/**
 * -----------------------------------------------------------------------------
 * Register sidebars
 * -----------------------------------------------------------------------------
 */

function rain_sidebars() {
	register_sidebar( array(
		'name' 			=> 'Default sidebar',
		'id' 			=> 'sidebar-1',
		'before_widget'	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</aside>',
		'before_title'	=> '<h6 class="widget-title"><span>',
		'after_title'	=> '</span></h6>',
	) );

	register_sidebar( array(
		'name' 			=> 'Contact page sidebar',
		'id' 			=> 'sidebar-2',
		'before_widget'	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</aside>',
		'before_title'	=> '<h6 class="widget-title"><span>',
		'after_title'	=> '</span></h6>',
	) );
};
add_action( 'widgets_init', 'rain_sidebars' );

/**
 * Register Google fonts.
 */
function tr_fonts_url() {
	$fonts_url = '';
	$font_families = array();
	$font_families[] = 'Lato:400,400i,700,700i';
	$query_args = array(
		'family' => urlencode( implode( '|', $font_families ) ),
		'subset' => urlencode( 'latin,latin-ext' ),
	);
	$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	return esc_url_raw( $fonts_url );
}

/**
 * Enqueues scripts and styles.
 */
function tr_scripts() {
	wp_enqueue_style( 'tr-fonts', tr_fonts_url(), array(), null );
	wp_enqueue_style( 'tr-font-awesome', get_template_directory_uri() . '/includes/font-awesome/css/font-awesome.min.css', array(), null );
	wp_enqueue_style( 'tr-fancybox', get_template_directory_uri() . '/assets/css/fancybox.min.css', array(), null );
	wp_enqueue_style( 'tr-style', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'tr-isotope', get_template_directory_uri() . '/assets/js/isotope.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'tr-imagesloaded', get_template_directory_uri() . '/assets/js/imagesloaded.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'tr-infinitescroll', get_template_directory_uri() . '/assets/js/infinitescroll.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'tr-fitvids', get_template_directory_uri() . '/assets/js/fitvids.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'tr-fancybox', get_template_directory_uri() . '/assets/js/fancybox.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'tr-functions', get_template_directory_uri() . '/assets/js/functions.js', array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'tr_scripts' );

/**
 * -----------------------------------------------------------------------------
 * Includes
 * -----------------------------------------------------------------------------
 */

require get_template_directory() . '/includes/template-functions.php';
require get_template_directory() . '/includes/template-tags.php';
require get_template_directory() . '/includes/customizer.php';
require get_template_directory() . '/includes/meta-boxes.php';
require get_template_directory() . '/includes/plugins/plugin-registration.php';
require get_template_directory() . '/includes/widgets/widget-recent-projects.php';
require get_template_directory() . '/includes/widgets/widget-social.php';