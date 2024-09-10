<?php
/**
 * Miniva functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Miniva
 */

/**
 * Current theme version
 */
define( 'MINIVA_VERSION', '1.7.1' );

if ( ! function_exists( 'miniva_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function miniva_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Miniva, use a find and replace
		 * to change 'miniva' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'miniva', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'miniva' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		$args = apply_filters(
			'miniva_custom_background_args',
			array(
				'default-color' => 'eeeeee',
				'default-image' => '',
			)
		);
		add_theme_support( 'custom-background', $args );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_theme_support(
			'amp',
			array(
				'nav_menu_toggle'   => array(
					'nav_container_id'           => 'site-navigation',
					'nav_container_toggle_class' => 'toggled',
					'menu_button_xpath'          => '//nav[@id = "site-navigation"]//button[ contains( @class, "menu-toggle" ) ]',
				),
				'nav_menu_dropdown' => array(
					'sub_menu_button_class'        => 'submenu-toggle',
					'sub_menu_button_toggle_class' => 'toggled',
					'expand_text '                 => __( 'expand sub menu', 'miniva' ),
					'collapse_text'                => __( 'collapse sub menu', 'miniva' ),
					'icon'                         => '<svg aria-hidden="true" width="12" height="12" class="icon"><use xlink:href="#expand" /></svg>',
				),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'miniva_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function miniva_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.
	$GLOBALS['content_width'] = apply_filters( 'miniva_content_width', 640 );
}
add_action( 'after_setup_theme', 'miniva_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function miniva_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'miniva' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'miniva' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'miniva_widgets_init' );

/**
 * Determine whether this is an AMP response.
 *
 * Note that this must only be called after the parse_query action.
 *
 * @link https://github.com/Automattic/amp-wp
 * @return bool Is AMP endpoint (and AMP plugin is active).
 */
function miniva_is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}

/**
 * Enqueue styles.
 */
function miniva_styles() {
	wp_enqueue_style( 'miniva-style', get_stylesheet_uri(), array(), MINIVA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'miniva_styles' );

/**
 * Enqueue scripts.
 *
 * This short-circuits in AMP because custom scripts are not allowed. There is are AMP equivalents provided elsewhere.
 */
function miniva_scripts() {
	if ( miniva_is_amp() ) {
		return;
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'miniva_scripts' );

/**
 * Add SVG definitions to body.
 */
function miniva_include_svg_icons() {
	// Define SVG sprite file.
	$svg_icons = get_theme_file_path( '/images/genericons/genericons-neue.svg' );

	// If it exists, include it.
	if ( file_exists( $svg_icons ) ) {
		require_once $svg_icons;
	}
}
add_action( 'miniva_body_start', 'miniva_include_svg_icons' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	require get_template_directory() . '/inc/customizer/class-miniva-custom-control.php';
	require get_template_directory() . '/inc/customizer/class-miniva-radio-image-control.php';
}
require get_template_directory() . '/inc/customizer.php';

/**
 * Theme info.
 */
require get_template_directory() . '/inc/theme-info.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Extra functions
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Content, sidebar & footer layouts.
 */
require get_template_directory() . '/inc/layouts.php';

/**
 * Posts functions.
 */
require get_template_directory() . '/inc/posts.php';

/**
 * Header layouts.
 */
require get_template_directory() . '/inc/header-layouts.php';

/**
 * Custom styles.
 */
require get_template_directory() . '/inc/custom-styles.php';
