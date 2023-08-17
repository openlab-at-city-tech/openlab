<?php
/**
 * The about page model class for ThemeIsle SDK
 *
 * Here's how to hook it in your plugin:
 *
 * add_filter( <product_slug>_about_us_metadata', 'add_about_meta' );
 *
 * function add_about_meta($data) {
 *  return [
 *     'location'           => <top level page - e.g. themes.php>,
 *     'logo'               => <logo url>,
 *     'page_menu'          => [['text' => '', 'url' => '']], // optional
 *     'has_upgrade_menu'   => <condition>,
 *     'upgrade_link'       => <url>,
 *     'upgrade_text'       => 'Get Pro Version',
 *  ]
 * }
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2023, Andrei Baicus
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.2.42
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promotions module for ThemeIsle SDK.
 */
class About_Us extends Abstract_Module {
	/**
	 * About data.
	 *
	 * @var array $about_data About page data, received from the filter.
	 *
	 * Shape of the $about_data property array:
	 * [
	 *     'location' => 'top level page',
	 *     'logo' => 'logo path',
	 *     'page_menu' => [['text' => '', 'url' => '']], // Optional
	 *     'has_upgrade_menu' => !defined('NEVE_PRO_VERSION'),
	 *     'upgrade_link' => 'upgrade url',
	 *     'upgrade_text' => 'Get Pro Version',
	 * ]
	 */
	private $about_data = array();

	/**
	 * Should we load this module.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		$this->about_data = apply_filters( $product->get_key() . '_about_us_metadata', array() );

		return ! empty( $this->about_data );
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 */
	public function load( $product ) {
		$this->product = $product;

		add_action( 'admin_menu', [ $this, 'add_submenu_pages' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_about_page_script' ] );
	}

	/**
	 * Adds submenu pages.
	 *
	 * @return void
	 */
	public function add_submenu_pages() {
		if ( ! isset( $this->about_data['location'] ) ) {
			return;
		}

		add_submenu_page(
			$this->about_data['location'],
			__( 'About Us', 'neve' ),
			__( 'About Us', 'neve' ),
			'manage_options',
			$this->get_about_page_slug(),
			array( $this, 'render_about_us_page' ),
			100
		);

		if ( ! isset( $this->about_data['has_upgrade_menu'] ) ) {
			return;
		}

		if ( $this->about_data['has_upgrade_menu'] !== true ) {
			return;
		}

		if ( ! isset( $this->about_data['upgrade_link'] ) ) {
			return;
		}

		if ( ! isset( $this->about_data['upgrade_text'] ) ) {
			return;
		}

		add_submenu_page(
			$this->about_data['location'],
			$this->about_data['upgrade_text'],
			$this->about_data['upgrade_text'],
			'manage_options',
			$this->about_data['upgrade_link'],
			'',
			101
		);
	}

	/**
	 * Render page content.
	 *
	 * @return void
	 */
	public function render_about_us_page() {
		echo '<div id="ti-sdk-about"></div>';
	}

	/**
	 * Enqueue scripts & styles.
	 *
	 * @return void
	 */
	public function enqueue_about_page_script() {
		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->id ) ) {
			return;
		}

		if ( strpos( $current_screen->id, $this->get_about_page_slug() ) === false ) {
			return;
		}
		global $themeisle_sdk_max_path;
		$handle     = 'ti-sdk-about-' . $this->product->get_key();
		$asset_file = require $themeisle_sdk_max_path . '/assets/js/build/about/about.asset.php';
		$deps       = array_merge( $asset_file['dependencies'], [ 'updates' ] );

		wp_register_script( $handle, $this->get_sdk_uri() . 'assets/js/build/about/about.js', $deps, $asset_file['version'], true );
		wp_localize_script( $handle, 'tiSDKAboutData', $this->get_about_localization_data() );

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle, $this->get_sdk_uri() . 'assets/js/build/about/about.css', [ 'wp-components' ], $asset_file['version'] );
	}

	/**
	 * Get localized data.
	 *
	 * @return array
	 */
	private function get_about_localization_data() {
		$links = isset( $this->about_data['page_menu'] ) ? $this->about_data['page_menu'] : [];

		return [
			'links'          => $links,
			'logoUrl'        => $this->about_data['logo'],
			'products'       => $this->get_other_products_data(),
			'homeUrl'        => esc_url( home_url() ),
			'pageSlug'       => $this->get_about_page_slug(),
			'currentProduct' => [
				'slug' => $this->product->get_key(),
				'name' => $this->product->get_name(),
			],
			'teamImage'      => $this->get_sdk_uri() . 'assets/images/team.jpg',
			'strings'        => [
				'aboutUs'          => __( 'About us', 'neve' ),
				'heroHeader'       => __( 'Our Story', 'neve' ),
				'heroTextFirst'    => __( 'Themeisle was founded in 2012 by a group of passionate developers who wanted to create beautiful and functional WordPress themes and plugins. Since then, we have grown into a team of over 20 dedicated professionals who are committed to delivering the best possible products to our customers.', 'neve' ),
				'heroTextSecond'   => __( 'At Themeisle, we offer a wide range of WordPress themes and plugins that are designed to meet the needs of both beginners and advanced users. Our products are feature-rich, easy to use, and are designed to help you create beautiful and functional websites.', 'neve' ),
				'teamImageCaption' => __( 'Our team in WCEU2022 in Portugal', 'neve' ),
				'newsHeading'      => __( 'Stay connected for news & updates!', 'neve' ),
				'emailPlaceholder' => __( 'Your email address', 'neve' ),
				'signMeUp'         => __( 'Sign me up', 'neve' ),
				'installNow'       => __( 'Install Now', 'neve' ),
				'activate'         => __( 'Activate', 'neve' ),
				'learnMore'        => __( 'Learn More', 'neve' ),
				'installed'        => __( 'Installed', 'neve' ),
				'notInstalled'     => __( 'Not Installed', 'neve' ),
				'active'           => __( 'Active', 'neve' ),
			],
		];
	}

	/**
	 * Get products data.
	 *
	 * @return array
	 */
	private function get_other_products_data() {
		$products = [
			'optimole-wp'                         => [
				'name'        => 'Optimole',
				'description' => 'Optimole is an image optimization service that automatically optimizes your images and serves them to your visitors via a global CDN, making your website lighter, faster and helping you reduce your bandwidth usage.',
			],
			'neve'                                => [
				'skip_api'    => true,
				'name'        => 'Neve',
				'description' => __( 'A fast, lightweight, customizable WordPress theme offering responsive design, speed, and flexibility for various website types.', 'neve' ),
				'icon'        => $this->get_sdk_uri() . 'assets/images/neve.png',
			],
			'otter-blocks'                        => [
				'name' => 'Otter',
			],
			'tweet-old-post'                      => [
				'name' => 'Revive Old Post',
			],
			'feedzy-rss-feeds'                    => [
				'name' => 'Feedzy',
			],
			'woocommerce-product-addon'           => [
				'name'      => 'PPOM',
				'condition' => class_exists( 'WooCommerce', false ),
			],
			'visualizer'                          => [
				'name' => 'Visualizer',
			],
			'wp-landing-kit'                      => [
				'skip_api'    => true,
				'premiumUrl'  => tsdk_utmify( 'https://themeisle.com/plugins/wp-landing-kit', $this->get_about_page_slug() ),
				'name'        => 'WP Landing Kit',
				'description' => __( 'Turn WordPress into a landing page powerhouse with Landing Kit, map domains to pages or any other published resource.', 'neve' ),
				'icon'        => $this->get_sdk_uri() . 'assets/images/wplk.png',
			],
			'multiple-pages-generator-by-porthas' => [
				'name' => 'MPG',
			],
			'sparks-for-woocommerce'              => [
				'skip_api'    => true,
				'premiumUrl'  => tsdk_utmify( 'https://themeisle.com/plugins/sparks-for-woocommerce', $this->get_about_page_slug() ),
				'name'        => 'Sparks',
				'description' => __( 'Extend your store functionality with 8 ultra-performant features like product comparisons, variation swatches, wishlist, and more.', 'neve' ),
				'icon'        => $this->get_sdk_uri() . 'assets/images/sparks.png',
				'condition'   => class_exists( 'WooCommerce', false ),
			],
			'templates-patterns-collection'       => [
				'name'        => 'Template Cloud',
				'description' => __( 'Ultimate Free Templates Cloud for WordPress, for blocks, patters of full pages.', 'neve' ),
			],
		];

		foreach ( $products as $slug => $product ) {
			if ( isset( $product['condition'] ) && ! $product['condition'] ) {
				unset( $products[ $slug ] );
				continue;
			}

			if ( $slug === 'neve' ) {
				$theme  = get_template();
				$themes = wp_get_themes();

				$products[ $slug ]['status'] = isset( $themes['neve'] ) ? 'installed' : 'not-installed';
				$products[ $slug ]['status'] = $theme === 'neve' ? 'active' : $products[ $slug ]['status'];

				$products[ $slug ]['activationLink'] = add_query_arg(
					[
						'stylesheet' => 'neve',
						'action'     => 'activate',
						'_wpnonce'   => wp_create_nonce( 'switch-theme_neve' ),
					],
					admin_url( 'themes.php' )
				);

				continue;
			}

			$products[ $slug ]['status']         = $this->is_plugin_installed( $slug ) ? 'installed' : 'not-installed';
			$products[ $slug ]['status']         = $this->is_plugin_active( $slug ) ? 'active' : $products[ $slug ]['status'];
			$products[ $slug ]['activationLink'] = $this->get_plugin_activation_link( $slug );


			if ( isset( $product['skip_api'] ) ) {
				continue;
			}

			$api_data = $this->call_plugin_api( $slug );

			if ( ! isset( $product['icon'] ) ) {
				$products[ $slug ]['icon'] = isset( $api_data->icons['2x'] ) ? $api_data->icons['2x'] : $api_data->icons['1x'];
			}
			if ( ! isset( $product['description'] ) ) {
				$products[ $slug ]['description'] = $api_data->short_description;
			}
			if ( ! isset( $product['name'] ) ) {
				$products[ $slug ]['name'] = $api_data->name;
			}
		}

		return $products;
	}

	/**
	 * Get the page slug.
	 *
	 * @return string
	 */
	private function get_about_page_slug() {
		return 'ti-about-' . $this->product->get_key();
	}
}
