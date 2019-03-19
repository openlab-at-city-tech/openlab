<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'education', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'education' ) );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Education Pro Theme', 'education' ) );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/education/' );
define( 'CHILD_THEME_VERSION', '3.0.2' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'education_load_scripts' );
function education_load_scripts() {

	wp_enqueue_script( 'education-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
	
	wp_enqueue_style( 'dashicons' );

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,700', array(), CHILD_THEME_VERSION );
	
}

//* Add new image sizes
add_image_size( 'slider', 1600, 800, TRUE );
add_image_size( 'sidebar', 280, 150, TRUE );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 300,
	'height'          => 100,
	'header-selector' => '.site-title a',
	'header-text'     => false,
) );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for additional color style options
add_theme_support( 'genesis-style-selector', array(
	'education-pro-blue'   => __( 'Education Pro Blue', 'education' ),
	'education-pro-green'  => __( 'Education Pro Green', 'education' ),
	'education-pro-red'    => __( 'Education Pro Red', 'education' ),
	'education-pro-purple' => __( 'Education Pro Purple', 'education' ),
) );

//* Add support for 5-column footer widgets
add_theme_support( 'genesis-footer-widgets', 5 );

//* Add support for after entry widget
add_theme_support( 'genesis-after-entry-widget-area' );

//* Rename Primary and Secondary Menu
add_theme_support( 'genesis-menus' , array( 'primary' => __( 'Before Header Menu', 'education' ), 'secondary' => __( 'Footer Menu', 'education' ) ) );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'education_secondary_menu_args' );
function education_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

//* Reposition the entry meta in the entry header
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );

//* Customize the entry meta in the entry header
add_filter( 'genesis_post_info', 'education_post_info_filter' );
function education_post_info_filter($post_info) {
	$post_info = '[post_date]';
	return $post_info;
}

//* Customize the entry meta in the entry footer
add_filter( 'genesis_post_meta', 'education_post_meta_filter' );
function education_post_meta_filter($post_meta) {
	$post_meta = 'Article by [post_author_posts_link] [post_categories before=" &#47; "] [post_tags before=" &#47; "] [post_comments] [post_edit]';
	return $post_meta;
}

//* Relocate after post widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

//* Modify the size of the Gravatar in the author box
add_filter( 'genesis_author_box_gravatar_size', 'parallax_author_box_gravatar' );
function parallax_author_box_gravatar( $size ) {

	return 96;

}

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-featured',
	'name'        => __( 'Home - Featured', 'education' ),
	'description' => __( 'This is the featured section of the Home page.', 'education' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home - Top', 'education' ),
	'description' => __( 'This is the top section of the Home page.', 'education' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle',
	'name'        => __( 'Home - Middle', 'education' ),
	'description' => __( 'This is the middle section of the Home page.', 'education' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-bottom',
	'name'        => __( 'Home - Bottom', 'education' ),
	'description' => __( 'This is the bottom section of the Home page.', 'education' ),
) );
