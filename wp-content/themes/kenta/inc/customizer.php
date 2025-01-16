<?php
/**
 * Kenta Theme Customizer
 *
 * @package Kenta
 */

use LottaFramework\Customizer\CallToActionSection;
use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;

// customizer elements
require get_template_directory() . '/inc/elements/class-button-element.php';
require get_template_directory() . '/inc/elements/class-logo-element.php';
require get_template_directory() . '/inc/elements/class-menu-element.php';
require get_template_directory() . '/inc/elements/class-collapsable-menu-element.php';
require get_template_directory() . '/inc/elements/class-trigger-element.php';
require get_template_directory() . '/inc/elements/class-copyright-element.php';
require get_template_directory() . '/inc/elements/class-search-element.php';
require get_template_directory() . '/inc/elements/class-cart-element.php';
require get_template_directory() . '/inc/elements/class-widgets-element.php';
require get_template_directory() . '/inc/elements/class-socials-element.php';
require get_template_directory() . '/inc/elements/class-breadcrumbs-element.php';
require get_template_directory() . '/inc/elements/class-theme-switch-element.php';

// customizer builder
require get_template_directory() . '/inc/builder/class-builder-column.php';
require get_template_directory() . '/inc/builder/class-modal-row.php';
require get_template_directory() . '/inc/builder/class-header-column.php';
require get_template_directory() . '/inc/builder/class-header-row.php';
require get_template_directory() . '/inc/builder/class-header-builder.php';
require get_template_directory() . '/inc/builder/class-footer-column.php';
require get_template_directory() . '/inc/builder/class-footer-row.php';
require get_template_directory() . '/inc/builder/class-footer-builder.php';

// customizer sections
require get_template_directory() . '/inc/customizer/class-homepage-section.php';
require get_template_directory() . '/inc/customizer/class-header-section.php';
require get_template_directory() . '/inc/customizer/class-footer-section.php';
require get_template_directory() . '/inc/customizer/class-colors-section.php';
require get_template_directory() . '/inc/customizer/class-background-section.php';
require get_template_directory() . '/inc/customizer/class-global-section.php';
require get_template_directory() . '/inc/customizer/class-archive-section.php';
require get_template_directory() . '/inc/customizer/class-content-section.php';
require get_template_directory() . '/inc/customizer/class-single-post-section.php';
require get_template_directory() . '/inc/customizer/class-pages-section.php';
require get_template_directory() . '/inc/customizer/class-store-catalog-section.php';
require get_template_directory() . '/inc/customizer/class-store-notice-section.php';
require get_template_directory() . '/inc/customizer/class-placeholders.php';

/**
 * @param $settings
 *
 * @return void
 * @since v1.2.8
 */
function kenta_update_customizer_default_settings( $settings = [] ) {
	kenta_update_option( 'customizer_queued_typography', \LottaFramework\Customizer\Controls\Typography::getQueued() );
	kenta_update_option( 'customizer_default_settings', $settings );
	kenta_update_option( 'customizer_default_settings_version', esc_html(
			kenta_apply_filters( 'customizer_default_settings_version', kenta_get_theme_version() ) )
	);
}

/**
 * Get all registered customizer settings.
 *
 * @return array
 *
 * @since 1.3.2
 */
function kenta_cz_settings() {
	$settings = [];
	foreach ( CZ::settings() as $id => $args ) {
		if ( ! \LottaFramework\Utils::str_starts_with( $id, 'lotta_rand' ) ) {
			if ( isset( $args['default'] ) ) {
				$settings[ $id ] = [
					'default' => $args['default']
				];
			}
		}
	}

	return $settings;
}

/**
 * Theme customizer register
 *
 * @param WP_Customize_Manager|null $wp_customize Theme Customizer object.
 */
function kenta_customize_register( $wp_customize ) {

	if ( ! $wp_customize instanceof WP_Customize_Manager ) {
		$wp_customize = null;
	}

	if ( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title a',
					'render_callback' => function () {
						echo esc_html( get_bloginfo( 'name' ) );
					},
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-tagline',
					'render_callback' => function () {
						echo esc_html( get_bloginfo( 'description' ) );
					},
				)
			);
		}

		if ( ! KENTA_CMP_ACTIVE ) {
			$wp_customize->add_section( new CallToActionSection( $wp_customize, 'kenta_install_companion', array(
				'priority' => 0,
				'title'    => __( 'Install Companion Plugin', 'kenta' ),
				'link'     => array(
					'url' => esc_url_raw( add_query_arg( array(
						'action'   => 'kenta_install_companion',
						'_wpnonce' => wp_create_nonce( 'kenta_install_companion' )
					), admin_url( 'admin.php' ) ) ),
				),
				'desc'     => kenta_why_companion_link()
			) ) );
		} else {
			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$wp_customize->add_section( new CallToActionSection( $wp_customize, 'kenta_upgrade', array(
					'priority' => 0,
					'title'    => __( 'Upgrade To Pro', 'kenta' ),
					'link'     => array(
						'url'    => kenta_upsell_url(),
						'target' => '_blank',
					)
				) ) );
			}

			$wp_customize->add_section( new CallToActionSection( $wp_customize, 'kenta_visit_starter_sites', array(
				'priority' => 0,
				'title'    => __( 'Visit Starter Sites', 'kenta' ),
				'link'     => array(
					'url'    => add_query_arg( [ 'page' => 'kenta-starter-sites' ], admin_url( 'admin.php' ) ),
					'target' => '_blank',
				)
			) ) );
		}

		$wp_customize->add_section( new CallToActionSection( $wp_customize, 'kenta_update_dynamic_css_cache', array(
			'priority' => 99999,
			'title'    => __( 'Update Customizer Cache', 'kenta' ),
			'desc'     => __( 'If the final style is not the same as the preview, please try to update the cache', 'kenta' ),
			'link'     => array(
				'url' => esc_url_raw( add_query_arg( array(
					'action'   => 'kenta_update_dynamic_css_cache',
					'_wpnonce' => wp_create_nonce( 'kenta_update_dynamic_css_cache' )
				), admin_url( 'admin.php' ) ) ),
			)
		) ) );
	}

	// Don't cache woocommerce controls
	if ( KENTA_WOOCOMMERCE_ACTIVE ) {
		if ( $wp_customize ) {
			CZ::changeObject( $wp_customize, 'panel', 'woocommerce', 'priority', 20 );
			// Remove default catalog columns
			$wp_customize->remove_control( 'woocommerce_catalog_columns' );
		}

		CZ::addSection( $wp_customize, new Kenta_Store_Notice_Section( 'woocommerce_store_notice', __( 'Store Notice', 'kenta' ), 0, 'woocommerce' ) );
		CZ::addSection( $wp_customize, new Kenta_Store_Catalog_Section( 'woocommerce_product_catalog', __( 'Product Catalog', 'kenta' ), 0, 'woocommerce' ) );
	}

	$settings_version        = kenta_get_option( 'customizer_default_settings_version' );
	$enable_customizer_cache = get_option( 'kenta_enable_customizer_cache', apply_filters( 'kenta_enable_customizer_cache_default_value', 'yes' ) );
	// load cached cz settings
	if ( ! kenta_apply_filters( 'should_reload_customizer_settings', ( $wp_customize || $enable_customizer_cache === 'no' || kenta_get_theme_version() !== $settings_version ) ) ) {
		$default_options = kenta_get_option( 'customizer_default_settings', [] );
		if ( ! empty( $default_options ) ) {
			CZ::restore( array_merge( $default_options, kenta_cz_settings() ) );
			\LottaFramework\Customizer\Controls\Typography::setQueued(
				kenta_get_option( 'customizer_queued_typography', [] )
			);

			// Manually trigger after register action for builders
			Kenta_Header_Builder::instance()->builder()->do( 'after_register' );
			Kenta_Footer_Builder::instance()->builder()->do( 'after_register' );

			return;
		}
	}

	Kenta_Placeholders::instance();

	CZ::addSection( $wp_customize, new Kenta_Header_Section( 'kenta_header', __( 'Header Builder', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Footer_Section( 'kenta_footer', __( 'Footer Builder', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Homepage_Section( 'static_front_page', __( 'Homepage Settings', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Colors_Section( 'kenta_colors', __( 'Colors', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Background_Section( 'kenta_background', __( 'Background', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Global_Section( 'kenta_global', __( 'Global', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Archive_Section( 'kenta_archive', __( 'Archive Settings', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Content_Section( 'kenta_content', __( 'Content Settings', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Single_Post_Section( 'kenta_single_post', __( 'Single Post Settings', 'kenta' ) ) );
	CZ::addSection( $wp_customize, new Kenta_Pages_Section( 'kenta_pages', __( 'Pages Settings', 'kenta' ) ) );

	/**
	 * Cache customizer settings
	 */
	$settings = kenta_cz_settings();
	kenta_update_customizer_default_settings( $settings );
	kenta_do_action( 'customizer_default_settings_saved', $settings );
}

add_action( 'customize_register', 'kenta_customize_register' );
add_action( 'kenta_after_lotta_framework_bootstrap', 'kenta_customize_register' );

if ( ! function_exists( 'kenta_customizer_scripts' ) ) {
	/**
	 * Enqueue customizer scripts
	 */
	function kenta_customizer_scripts() {
		kenta_enqueue_global_vars();
	}
}
add_action( 'customize_controls_enqueue_scripts', 'kenta_customizer_scripts' );

/**
 * Change customizer localize object
 *
 * @param $localize
 *
 * @return mixed
 */
function kenta_customizer_localize( $localize ) {
	$localize['customizer']['colorPicker']['swatches'] = [
		'var(--kenta-primary-color)',
		'var(--kenta-primary-active)',
		'var(--kenta-accent-color)',
		'var(--kenta-accent-active)',
		'var(--kenta-base-300)',
		'var(--kenta-base-200)',
		'var(--kenta-base-100)',
		'var(--kenta-base-color)',
		Css::INITIAL_VALUE,
	];

	$localize['customizer']['gradientPicker']['swatches'] = array_merge(
		$localize['customizer']['gradientPicker']['swatches'],
		[
			[ "gradient" => "linear-gradient(to right, rgb(142, 158, 171), rgb(238, 242, 243))" ],
			[ "gradient" => "linear-gradient(to right, rgb(172, 203, 238), rgb(231, 240, 253))" ],
			[ "gradient" => "linear-gradient(to right, rgb(211, 204, 227), rgb(233, 228, 240))" ],
			[ "gradient" => "linear-gradient(to right, rgb(217, 167, 199), rgb(255, 252, 220))" ],
			[ "gradient" => "linear-gradient(to right, rgb(251, 200, 212), rgb(151, 149, 240))" ],
			[ "gradient" => "linear-gradient(to right, rgb(255, 226, 89), rgb(255, 167, 81))" ],
			[ "gradient" => "linear-gradient(to right, rgb(247, 151, 30), rgb(255, 210, 0))" ],
			[ "gradient" => "linear-gradient(to right, rgb(248, 54, 0), rgb(249, 212, 35))" ],
			[ "gradient" => "linear-gradient(to right, rgb(238, 156, 167), rgb(255, 221, 225))" ],
			[ "gradient" => "linear-gradient(to right, rgb(252, 203, 144), rgb(213, 126, 235))" ],
			[ "gradient" => "linear-gradient(to right, rgb(255, 129, 119), rgb(177, 42, 91))" ],
			[ "gradient" => "linear-gradient(to right, rgb(242, 112, 156), rgb(255, 148, 114))" ],
			[ "gradient" => "linear-gradient(to right, rgb(237, 66, 100), rgb(255, 237, 188))" ],
			[ "gradient" => "linear-gradient(to right, rgb(255, 68, 197), rgb(255, 241, 163))" ],
			[ "gradient" => "linear-gradient(to right, rgb(236, 0, 140), rgb(252, 103, 103))" ],
			[ "gradient" => "linear-gradient(to right, rgb(121, 0, 255), rgb(255, 88, 202))" ],
			[ "gradient" => "linear-gradient(to right, rgb(183, 33, 255), rgb(33, 212, 253))" ],
			[ "gradient" => "linear-gradient(to right, rgb(106, 17, 203), rgb(119, 212, 255))" ],
			[ "gradient" => "linear-gradient(to right, rgb(43, 152, 248), rgb(0, 254, 163))" ],
			[ "gradient" => "linear-gradient(to right, rgb(84, 51, 255), rgb(32, 189, 255), rgb(165, 254, 203))" ],
			[ "gradient" => "linear-gradient(to right, rgb(202, 197, 49), rgb(243, 249, 167))" ],
			[ "gradient" => "linear-gradient(to right, rgb(161, 255, 206), rgb(250, 255, 209))" ],
			[ "gradient" => "linear-gradient(to right, rgb(29, 151, 108), rgb(147, 249, 185))" ],
			[ "gradient" => "linear-gradient(to right, rgb(255, 224, 0), rgb(121, 159, 12))" ],
			[ "gradient" => "linear-gradient(to right, rgb(86, 171, 47), rgb(168, 224, 99))" ],
			[ "gradient" => "linear-gradient(to right, rgb(66, 147, 33), rgb(180, 236, 81))" ],
			[ "gradient" => "linear-gradient(to right, rgb(22, 160, 133), rgb(244, 208, 63))" ],
			[ "gradient" => "linear-gradient(to right, rgb(78, 247, 255), rgb(255, 205, 27))" ],
			[ "gradient" => "linear-gradient(to right, rgb(94, 231, 223), rgb(180, 144, 202))" ],
			[ "gradient" => "linear-gradient(to right, rgb(186, 200, 224), rgb(106, 133, 182))" ],
			[ "gradient" => "linear-gradient(to right, rgb(161, 196, 253), rgb(194, 233, 251))" ],
			[ "gradient" => "linear-gradient(to right, rgb(102, 126, 234), rgb(118, 75, 162))" ],
			[ "gradient" => "linear-gradient(to right, rgb(173, 83, 137), rgb(60, 16, 83))" ],
			[ "gradient" => "linear-gradient(to right, rgb(102, 166, 255), rgb(137, 247, 254))" ],
			[ "gradient" => "linear-gradient(to right, rgb(97, 144, 232), rgb(167, 191, 232))" ],
			[ "gradient" => "linear-gradient(to right, rgb(71, 59, 123), rgb(48, 210, 190))" ],
			[ "gradient" => "linear-gradient(to right, rgb(20, 136, 204), rgb(43, 50, 178))" ],
			[ "gradient" => "linear-gradient(to right, rgb(9, 48, 40), rgb(35, 122, 87))" ],
			[ "gradient" => "linear-gradient(to right, rgb(100, 65, 165), rgb(42, 8, 69))" ],
			[ "gradient" => "linear-gradient(to right, rgb(35, 37, 38), rgb(65, 67, 69))" ],
			[ "gradient" => "linear-gradient(to right, rgb(15, 32, 39), rgb(32, 58, 67), rgb(44, 83, 100))" ],
			[ "gradient" => "linear-gradient(to right, rgb(20, 30, 48), rgb(36, 59, 8 ]5))" ]
		]
	);

	return $localize;
}

add_filter( 'lotta_filter_customizer_js_localize', 'kenta_customizer_localize' );
