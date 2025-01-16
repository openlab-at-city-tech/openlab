<?php
/**
 * Kenta functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Kenta
 */

if ( ! defined( 'KENTA_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'KENTA_VERSION', '1.4.4' );
}

if ( ! defined( 'MIN_KENTA_CMP_VERSION' ) ) {
	// Minimal Kenta Companion plugin compatible
	define( 'MIN_KENTA_CMP_VERSION', '1.2.5' );
}

if ( ! defined( 'KENTA_WOOCOMMERCE_ACTIVE' ) ) {
	// Used to check whether WooCommerce plugin is activated
	define( 'KENTA_WOOCOMMERCE_ACTIVE', class_exists( 'WooCommerce' ) );
}

// Companion plugin check
if ( ! defined( 'KENTA_CMP_ACTIVE' ) ) {
	define( 'KENTA_CMP_ACTIVE', defined( 'KCMP_VERSION' ) );

	if ( ! defined( 'KENTA_CMP_PRO_ACTIVE' ) ) {
		define( 'KENTA_CMP_PRO_ACTIVE', defined( 'KCMP_PREMIUM' ) );
	}
}

/**
 * Load lotta-framework
 */
require get_template_directory() . '/lotta-framework/vendor/autoload.php';

/**
 * Helper functions
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Dynamic Css
 */
require get_template_directory() . '/inc/dynamic-css.php';

/**
 * Theme Setup
 */
require get_template_directory() . '/inc/theme-setup.php';

if ( KENTA_WOOCOMMERCE_ACTIVE ) {
	/**
	 * WooCommerce Setup
	 */
	require get_template_directory() . '/inc/woo-setup.php';
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Php traits
 */
require get_template_directory() . '/inc/traits.php';

/**
 * Theme extensions
 */
require get_template_directory() . '/inc/extensions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Admin page settings.
 */
require get_template_directory() . '/inc/admin-page.php';

/**
 * Boostrap lotta-framework
 */
\LottaFramework\Bootstrap::run(
	'kenta',
	trailingslashit( get_template_directory_uri() ) . 'lotta-framework/'
);

// save theme settings in options
kenta_app( 'CZ' )->storeAs( 'option' );

// add global dynamic css partial
kenta_app( 'CZ' )->addPartial( 'kenta-global-selective-css', '#kenta-global-selective-css', function () {
	echo kenta_dynamic_css() . kenta_no_cache_dynamic_css();
} );

// add preloader customize partial
kenta_app( 'CZ' )->addPartial( 'kenta-preloader-selective-css', '#kenta-preloader-selective-css', function () {
	echo kenta_preloader_css();
} );

// add WooCommerce css partial
kenta_app( 'CZ' )->addPartial( 'kenta-woo-selective-css', '#kenta-woo-selective-css', function () {
	if ( function_exists( 'kenta_woo_dynamic_css' ) ) {
		echo \LottaFramework\Facades\Css::parse( kenta_woo_dynamic_css() );
	}
} );

// add transparent header dynamic css partial
kenta_app( 'CZ' )->addPartial( 'kenta-transparent-selective-css', '#kenta-transparent-selective-css', function () {
	echo kenta_transparent_header_css();
} );

// add header customize partial
kenta_app( 'CZ' )->addPartial( 'kenta-header-selective-css', '#kenta-header-selective-css', function () {
	Kenta_Header_Builder::instance()->builder()->do( 'enqueue_frontend_scripts' );
	echo \LottaFramework\Facades\Css::parse( apply_filters( 'kenta_filter_dynamic_css', [] ) );
	echo \LottaFramework\Facades\Css::parse( apply_filters( 'kenta_filter_no_cache_dynamic_css', [] ) );
} );

// add footer customize partial
kenta_app( 'CZ' )->addPartial( 'kenta-footer-selective-css', '#kenta-footer-selective-css', function () {
	Kenta_Footer_Builder::instance()->builder()->do( 'enqueue_frontend_scripts' );
	echo \LottaFramework\Facades\Css::parse( apply_filters( 'kenta_filter_dynamic_css', [] ) );
	echo \LottaFramework\Facades\Css::parse( apply_filters( 'kenta_filter_no_cache_dynamic_css', [] ) );
} );

/**
 * After lotta-framework boostrap
 */
do_action( 'kenta_after_lotta_framework_bootstrap' );

// support locally hosted google-fonts and we should do this after all options are loaded
if ( kenta_app( 'CZ' )->checked( 'kenta_use_local_fonts' ) ) {
	kenta_app()->support( 'local_webfonts' );
}
