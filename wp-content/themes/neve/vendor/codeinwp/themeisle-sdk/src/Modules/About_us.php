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
 *     'review_link'        => false, // Leave it empty for default WPorg link or false to hide it.
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
use ThemeisleSDK\Loader;
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
			Loader::$labels['about_us']['title'],
			Loader::$labels['about_us']['title'],
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
			'<span class="tsdk-upg-menu-item">' . $this->about_data['upgrade_text'] . '</span>',
			'manage_options',
			$this->about_data['upgrade_link'],
			'',
			101
		);
		add_action(
			'admin_footer',
			function () {
				?>
			<style>
				.tsdk-upg-menu-item {
					color: #009528;
				}

				.tsdk-upg-menu-item:hover {
					color: #008a20;
				}
			</style>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$('.tsdk-upg-menu-item').parent().attr('target', '_blank');
				});
			</script>
				<?php
			} 
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

		do_action( 'themeisle_internal_page', $this->product->get_slug(), 'about_us' );

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
		$links         = isset( $this->about_data['page_menu'] ) ? $this->about_data['page_menu'] : [];
		$product_pages = isset( $this->about_data['product_pages'] ) ? $this->about_data['product_pages'] : [];

		return [
			'links'              => $links,
			'logoUrl'            => $this->about_data['logo'],
			'productPages'       => $this->get_product_pages_data( $product_pages ),
			'products'           => $this->get_other_products_data(),
			'homeUrl'            => esc_url( home_url() ),
			'pageSlug'           => $this->get_about_page_slug(),
			'currentProduct'     => [
				'slug' => $this->product->get_key(),
				'name' => $this->product->get_name(),
			],
			'teamImage'          => $this->get_sdk_uri() . 'assets/images/team.jpg',
			'strings'            => [
				'aboutUs'          => Loader::$labels['about_us']['title'],
				'heroHeader'       => Loader::$labels['about_us']['heroHeader'],
				'heroTextFirst'    => Loader::$labels['about_us']['heroTextFirst'],
				'heroTextSecond'   => Loader::$labels['about_us']['heroTextSecond'],
				'teamImageCaption' => Loader::$labels['about_us']['teamImageCaption'],
				'newsHeading'      => Loader::$labels['about_us']['newsHeading'],
				'emailPlaceholder' => Loader::$labels['about_us']['emailPlaceholder'],
				'signMeUp'         => Loader::$labels['about_us']['signMeUp'],
				'installNow'       => Loader::$labels['about_us']['installNow'],
				'activate'         => Loader::$labels['about_us']['activate'],
				'learnMore'        => Loader::$labels['about_us']['learnMore'],
				'installed'        => Loader::$labels['about_us']['installed'],
				'notInstalled'     => Loader::$labels['about_us']['notInstalled'],
				'active'           => Loader::$labels['about_us']['active'],
			],
			'canInstallPlugins'  => current_user_can( 'install_plugins' ),
			'canActivatePlugins' => current_user_can( 'activate_plugins' ),
			'showReviewLink'     => ! ( isset( $this->about_data['review_link'] ) && false === $this->about_data['review_link'] ),
		];
	}

	/**
	 * Get product pages data.
	 *
	 * @param array $product_pages Product pages.
	 *
	 * @return array
	 */
	private function get_product_pages_data( $product_pages ) {

		$otter_slug                     = 'otter-blocks';
		$otter_plugin                   = [
			'status' => 'not-installed',
		];
		$otter_plugin['status']         = $this->is_plugin_installed( $otter_slug ) ? 'installed' : 'not-installed';
		$otter_plugin['status']         = $this->is_plugin_active( $otter_slug ) ? 'active' : $otter_plugin['status'];
		$otter_plugin['activationLink'] = $this->get_plugin_activation_link( $otter_slug );

		$pages = [
			'otter-page' => [
				'name'    => 'Otter Blocks',
				'hash'    => '#otter-page',
				'product' => $otter_slug,
				'plugin'  => $otter_plugin,
				'strings' => [
					'heading'      => Loader::$labels['about_us']['otter-page']['heading'],
					'text'         => Loader::$labels['about_us']['otter-page']['text'],
					'buttons'      => [
						'install_otter_free' => Loader::$labels['about_us']['otter-page']['buttons']['install_otter_free'],
						'install_now'        => Loader::$labels['about_us']['otter-page']['buttons']['install_now'],
						'learn_more'         => Loader::$labels['about_us']['otter-page']['buttons']['learn_more'],
						'learn_more_link'    => tsdk_utmify( 'https://themeisle.com/plugins/otter-blocks/', 'otter-page', 'about-us' ),
					],
					'features'     => [
						'advancedTitle' => Loader::$labels['about_us']['otter-page']['features']['advancedTitle'],
						'advancedDesc'  => Loader::$labels['about_us']['otter-page']['features']['advancedDesc'],
						'fastTitle'     => Loader::$labels['about_us']['otter-page']['features']['fastTitle'],
						'fastDesc'      => Loader::$labels['about_us']['otter-page']['features']['fastDesc'],
						'mobileTitle'   => Loader::$labels['about_us']['otter-page']['features']['mobileTitle'],
						'mobileDesc'    => Loader::$labels['about_us']['otter-page']['features']['mobileDesc'],
					],
					'details'      => [
						's1Title' => Loader::$labels['about_us']['otter-page']['details']['s1Title'],
						's1Text'  => Loader::$labels['about_us']['otter-page']['details']['s1Text'],
						's2Title' => Loader::$labels['about_us']['otter-page']['details']['s2Title'],
						's2Text'  => Loader::$labels['about_us']['otter-page']['details']['s2Text'],
						's3Title' => Loader::$labels['about_us']['otter-page']['details']['s3Title'],
						's3Text'  => Loader::$labels['about_us']['otter-page']['details']['s3Text'],
						's1Image' => $this->get_sdk_uri() . 'assets/images/otter/otter-builder.png',
						's2Image' => $this->get_sdk_uri() . 'assets/images/otter/otter-patterns.png',
						's3Image' => $this->get_sdk_uri() . 'assets/images/otter/otter-library.png',
					],
					'testimonials' => [
						'heading' => Loader::$labels['about_us']['otter-page']['testimonials']['heading'],
						'users'   => [
							[
								'avatar' => 'https://mllj2j8xvfl0.i.optimole.com/cb:3970~373ad/w:80/h:80/q:mauto/https://themeisle.com/wp-content/uploads/2021/05/avatar-03.png',
								'name'   => 'Michael Burry',
								'text'   => Loader::$labels['about_us']['otter-page']['testimonials']['users']['user_1'],
							],
							[
								'avatar' => 'https://mllj2j8xvfl0.i.optimole.com/cb:3970~373ad/w:80/h:80/q:mauto/https://themeisle.com/wp-content/uploads/2022/04/avatar-04.png',
								'name'   => 'Maria Gonzales',
								'text'   => Loader::$labels['about_us']['otter-page']['testimonials']['users']['user_2'],
							],
							[
								'avatar' => 'https://mllj2j8xvfl0.i.optimole.com/cb:3970~373ad/w:80/h:80/q:mauto/https://themeisle.com/wp-content/uploads/2022/04/avatar-05.png',
								'name'   => 'Florian Henckel',
								'text'   => Loader::$labels['about_us']['otter-page']['testimonials']['users']['user_3'],
							],
						],
					],
				],
			],
		];

		return array_filter(
			$pages,
			function ( $page_data, $page_key ) use ( $product_pages ) {
				return in_array( $page_key, $product_pages, true ) &&
					   isset( $page_data['plugin']['status'] ) &&
					   $page_data['plugin']['status'] === 'not-installed';
			},
			ARRAY_FILTER_USE_BOTH
		);
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
				'description' => Loader::$labels['about_us']['others']['optimole_desc'],
			],
			'neve'                                => [
				'skip_api'    => true,
				'name'        => 'Neve',
				'description' => Loader::$labels['about_us']['others']['neve_desc'],
				'icon'        => $this->get_sdk_uri() . 'assets/images/neve.png',
			],
			'learning-management-system'          => [
				'name' => 'Masteriyo LMS',
			],
			'otter-blocks'                        => [
				'name' => 'Otter',
			],
			'tweet-old-post'                      => [
				'name' => 'Revive Social',
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
				'description' => Loader::$labels['about_us']['others']['landingkit_desc'],
				'icon'        => $this->get_sdk_uri() . 'assets/images/wplk.png',
			],
			'multiple-pages-generator-by-porthas' => [
				'name' => 'MPG',
			],
			'sparks-for-woocommerce'              => [
				'skip_api'    => true,
				'premiumUrl'  => tsdk_utmify( 'https://themeisle.com/plugins/sparks-for-woocommerce', $this->get_about_page_slug() ),
				'name'        => 'Sparks',
				'description' => Loader::$labels['about_us']['others']['sparks_desc'],
				'icon'        => $this->get_sdk_uri() . 'assets/images/sparks.png',
				'condition'   => class_exists( 'WooCommerce', false ),
			],
			'templates-patterns-collection'       => [
				'name'        => 'Templates Cloud',
				'description' => Loader::$labels['about_us']['others']['tpc_desc'],
			],
			'wp-cloudflare-page-cache'            => [
				'name' => 'Super Page Cache',
			],
			'hyve-lite'                           => [
				'name' => 'Hyve Lite',
			],
			'wp-full-stripe-free'                 => [
				'name' => 'WP Full Pay',
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
			if ( ! isset( $product['icon'] ) && ( isset( $api_data->icons['2x'] ) || $api_data->icons['1x'] ) ) {
				$products[ $slug ]['icon'] = isset( $api_data->icons['2x'] ) ? $api_data->icons['2x'] : $api_data->icons['1x'];
			}
			if ( ! isset( $product['description'] ) && isset( $api_data->short_description ) ) {
				$products[ $slug ]['description'] = $api_data->short_description;
			}
			if ( ! isset( $product['name'] ) && isset( $api_data->name ) ) {
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
