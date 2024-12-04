<?php
/**
 * Kenta Theme Setup
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;
use LottaFramework\Typography\Fonts;


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kenta_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Kenta, use a find and replace
	 * to change 'kenta' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'kenta', get_template_directory() . '/languages' );

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

	// Support align wide
	add_theme_support( 'align-wide' );

	// Gutenberg custom stylesheet
	add_theme_support( 'editor-styles' );
	add_editor_style( 'dist/css/editor-style' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min' ) . '.css' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Support responsive embeds
	add_theme_support( "responsive-embeds" );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Starter Content
	add_theme_support( 'starter-content', apply_filters( 'kenta_filter_starter_content', array(
		'widgets'   => array(
			'primary-sidebar'           => array(
				'search',
				'text_about',
				'text_business_info',
			),
			'kenta_footer_el_widgets_1' => array(
				'text_business_info',
			),
			'kenta_footer_el_widgets_2' => array(
				'text_about',
			),
			'kenta_footer_el_widgets_3' => array(
				'recent-posts',
				'categories',
			),
			'kenta_footer_el_widgets_4' => array(
				'search',
				'recent-comments',
			),
		),
		'posts'     => array(
			'home' => array(
				'post_type'    => 'page',
				'post_title'   => __( 'Home', 'kenta' ),
				'post_content' => '',
			),
			'about',
			'contact',
			'blog',
		),
		'nav_menus' => array(
			'kenta_header_el_menu_1'           => array(
				'name'  => __( 'Header Menu #1', 'kenta' ),
				'items' => array(
					'link_home',
					'page_about',
					'page_contact',
					'page_blog',
					'post_news',
				),
			),
			'kenta_header_el_menu_2'           => array(
				'name'  => __( 'Header Menu #2', 'kenta' ),
				'items' => array(
					'link_home',
					'page_about',
					'page_contact',
					'page_blog',
					'post_news',
				),
			),
			'kenta_header_el_collapsable_menu' => array(
				'name'  => __( 'Collapsable Menu', 'kenta' ),
				'items' => array(
					'link_home',
					'page_about',
					'page_contact',
					'page_blog',
					'post_news',
				),
			),
			'kenta_footer_el_menu'             => array(
				'name'  => __( 'Footer Menu', 'kenta' ),
				'items' => array(
					'page_about',
					'page_contact',
					'page_blog',
				),
			),
		),
	) ) );

	// theme.json support
	remove_theme_support( 'block-templates' );
}

add_action( 'after_setup_theme', 'kenta_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function kenta_widgets_init() {
	$sidebar_class = 'kenta-widget clearfix %2$s';
	if ( CZ::checked( 'kenta_global_sidebar_scroll-reveal' ) ) {
		$sidebar_class = 'kenta-scroll-reveal-widget ' . $sidebar_class;
	}

	$title_class = 'widget-title mb-half-gutter heading-content';
	$tag         = CZ::get( 'kenta_global_sidebar_title-tag' ) ?? 'h2';

	register_sidebar(
		array(
			'name'          => esc_html__( 'Primary Sidebar', 'kenta' ),
			'id'            => 'primary-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'kenta' ),
			'before_widget' => '<section id="%1$s" class="' . esc_attr( $sidebar_class ) . '">',
			'after_widget'  => '</section>',
			'before_title'  => '<' . $tag . ' class="' . esc_attr( $title_class ) . '">',
			'after_title'   => '</' . $tag . '>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Store Sidebar', 'kenta' ),
			'id'            => 'store-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'kenta' ),
			'before_widget' => '<section id="%1$s" class="' . esc_attr( $sidebar_class ) . '">',
			'after_widget'  => '</section>',
			'before_title'  => '<' . $tag . ' class="' . esc_attr( $title_class ) . '">',
			'after_title'   => '</' . $tag . '>',
		)
	);
}

add_action( 'widgets_init', 'kenta_widgets_init' );

/**
 * Register post meta
 *
 * @return void
 */
function kenta_register_meta_settings() {
	$object_subtype = apply_filters( 'kenta_filter_meta_object_subtype', '' );

	register_post_meta(
		$object_subtype,
		'site-container-style',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'site-container-layout',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'site-sidebar-layout',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'site-transparent-header',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'disable-article-header',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'disable-site-header',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'disable-site-footer',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);

	register_post_meta(
		$object_subtype,
		'disable-content-area-spacing',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'default'       => 'default',
			'type'          => 'string',
			'auth_callback' => '__return_true',
		)
	);
}

add_action( 'init', 'kenta_register_meta_settings' );

/**
 * Enqueue scripts and styles.
 */
function kenta_enqueue_scripts() {
	$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
	$ver    = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : KENTA_VERSION;

	// Vendors
	wp_enqueue_style( 'lotta-fontawesome' );

	wp_enqueue_style(
		'kenta-style',
		get_template_directory_uri() . '/dist/css/style' . $suffix . '.css',
		array(),
		$ver
	);

	$asset_file = get_template_directory() . '/dist/js/app.asset.php';
	$asset      = array();
	if ( file_exists( $asset_file ) ) {
		$asset = require $asset_file;
	}

	wp_enqueue_script(
		'kenta-script',
		get_template_directory_uri() . '/dist/js/app' . $suffix . '.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? $ver,
		true
	);

	kenta_enqueue_global_vars();
	kenta_enqueue_dynamic_css();
	kenta_enqueue_transparent_header_css();
	Fonts::enqueue_scripts( 'kenta_fonts', $ver );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'kenta_enqueue_scripts', 20 );

/**
 * Enqueue admin scripts & styles
 *
 * @return void
 */
function kenta_enqueue_admin_scripts() {
	if ( is_customize_preview() ) {
		return;
	}

	$suffix     = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
	$asset_file = get_template_directory() . '/dist/js/admin.asset.php';
	$asset      = array();
	if ( file_exists( $asset_file ) ) {
		$asset = require $asset_file;
	}

	wp_register_script(
		'kenta-admin-script',
		get_template_directory_uri() . '/dist/js/admin' . $suffix . '.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? KENTA_VERSION
	);

	// Theme admin scripts
	wp_register_style(
		'kenta-admin-style',
		get_template_directory_uri() . '/dist/css/admin' . $suffix . '.css',
		[],
		KENTA_VERSION
	);

	wp_enqueue_script( 'kenta-admin-script' );
	wp_enqueue_style( 'kenta-admin-style' );

	// Admin script
	wp_localize_script( 'kenta-admin-script', 'KentaAdmin', apply_filters( 'kenta_admin_localize_script', [
		'install_cmp_url' => esc_url_raw( add_query_arg( array(
			'action'   => 'kenta_install_companion',
			'_wpnonce' => wp_create_nonce( 'kenta_install_companion' )
		), admin_url( 'admin.php' ) ) ),
	] ) );
}

add_action( 'admin_enqueue_scripts', 'kenta_enqueue_admin_scripts', 9999 );

/**
 * Add kenta theme settings panel & theme switch button in block editor
 *
 * @return void
 */
function kenta_enqueue_block_editor_assets() {
	global $pagenow;

	if ( 'widgets.php' === $pagenow || is_customize_preview() ) {
		return;
	}

	kenta_enqueue_global_vars( [
		'defaultScheme' => kenta_get_html_attributes( 'data-kenta-theme' ),
	] );

	$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

	$asset_file = get_template_directory() . '/dist/js/block-editor.asset.php';
	$asset      = array();
	if ( file_exists( $asset_file ) ) {
		$asset = require $asset_file;
	}

	wp_register_script(
		'kenta-block-editor-scripts',
		get_template_directory_uri() . '/dist/js/block-editor' . $suffix . '.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? KENTA_VERSION,
	);

	wp_enqueue_script( 'kenta-block-editor-scripts' );
}

add_action( 'enqueue_block_editor_assets', 'kenta_enqueue_block_editor_assets' );

/**
 * Enqueue user-generated content (blocks) assets for all blocks, editor only.
 *
 * @return void
 * @since 1.4.0
 */
function kenta_block_editor_assets() {
	Fonts::enqueue_scripts( 'kenta_fonts', KENTA_VERSION );
}

add_filter( 'enqueue_block_assets', 'kenta_block_editor_assets' );

/**
 * Enqueue user-generated content (blocks) styles for all blocks, editor only.
 *
 * @param $settings
 *
 * @return array
 * @since 1.4.0
 */
function kenta_enqueue_block_editor_dynamic_css( $settings ) {

	$css = kenta_global_css_vars( ':root', '', kenta_get_html_attributes( 'data-kenta-theme' ) );
	$css .= kenta_block_editor_dynamic_css();

	$settings['styles'][] = array(
		'css'            => $css,
		'__unstableType' => 'theme',
		'source'         => 'kenta'
	);

	return $settings;
}

if ( class_exists( 'WP_Block_Editor_Context' ) ) {
	// WP 5.8+
	add_filter( 'block_editor_settings_all', 'kenta_enqueue_block_editor_dynamic_css' );
} else {
	add_filter( 'block_editor_settings', 'kenta_enqueue_block_editor_dynamic_css' );
}

/**
 * Enqueue scripts and styles for customizer.
 */
function kenta_enqueue_customizer_scripts() {
	$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

	wp_enqueue_style(
		'kenta-customizer-style',
		get_template_directory_uri() . '/dist/css/customizer' . $suffix . '.css',
		array(),
		KENTA_VERSION
	);

	wp_enqueue_script(
		'kenta-customizer-script',
		get_template_directory_uri() . '/dist/js/customizer' . $suffix . '.js',
		array( 'lotta-customizer-script', 'customize-controls', 'jquery' ),
		KENTA_VERSION
	);

	// Customer script
	wp_localize_script( 'kenta-customizer-script', 'KentaCustomizer', apply_filters( 'kenta_customizer_localize_script', [
		'theme'           => kenta_get_html_attributes( 'data-kenta-theme' ),
		'call_to_actions' => apply_filters( 'kenta_customizer_call_to_actions', [
			'#kenta_install_companion .button',
			'#kenta_update_dynamic_css_cache .button',
		] ),
	] ) );
}

add_action( 'customize_controls_enqueue_scripts', 'kenta_enqueue_customizer_scripts', 10 );

function kenta_enqueue_customize_preview_scripts() {
	$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

	wp_enqueue_script(
		'kenta-customizer-preview-script',
		get_template_directory_uri() . '/dist/js/customizer-preview' . $suffix . '.js',
		array( 'customize-preview', 'customize-selective-refresh' ),
		KENTA_VERSION
	);
}

add_action( 'customize_preview_init', 'kenta_enqueue_customize_preview_scripts', 20 );
