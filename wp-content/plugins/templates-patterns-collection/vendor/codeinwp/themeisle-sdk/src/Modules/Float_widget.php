<?php
/**
 * The float widget model class for ThemeIsle SDK
 *
 * Here's how to hook it in your plugin:
 *
 * add_filter( <product_slug>_float_widget_metadata', 'add_float_widget_meta' );
 *
 * function add_float_widget_meta($data) {
 *  return [
 *       'logo'                 => <logo url>,
 *       'nice_name'            => <nice name>, // optional, will default to product name
 *       'primary_color'        => <hex_color_value>, // optional
 *       'pages'                => [ 'page-slugs' ], //pages where the float widget should be displayed
 *       'has_upgrade_menu'     => <condition>,
 *       'upgrade_link'         => <url>,
 *       'documentation_link'   => <url>,
 *       'premium_support_link' => <url>, // optional, provide from pro version
 *       'feature_request_link' => <url>, // optional, provide from pro version
 *       'wizard_link'          => <url>, // optional, provide if a user is available
 *  ]
 * }
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2024, Bogdan Preda
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
 * Float widget module for ThemeIsle SDK.
 */
class Float_Widget extends Abstract_Module {
	/**
	 * Float widget data.
	 *
	 * @var array $float_widget_data Float widget data, received from the filter.
	 *
	 * Shape of the $about_data property array:
	 * [
	 *      'logo'                 => <logo url>,
	 *      'nice_name'            => <nice name>, // optional, will default to product name
	 *      'primary_color'        => <hex_color_value>, // optional
	 *      'pages'                => [ 'page-slugs' ], //pages where the float widget should be displayed
	 *      'has_upgrade_menu'     => <condition>,
	 *      'upgrade_link'         => <url>,
	 *      'documentation_link'   => <url>,
	 *      'premium_support_link' => <url>, // optional, provide from pro version
	 *      'feature_request_link' => <url>, // optional, provide from pro version
	 *      'wizard_link'          => <url>, // optional, provide if a user is available
	 * ]
	 */
	private $float_widget_data = array();

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

		$this->float_widget_data = apply_filters( $product->get_key() . '_float_widget_metadata', array() );

		$can_load = ! empty( $this->float_widget_data );

		$this->float_widget_data = array_merge(
			[
				'logo'                 => '',
				'primary_color'        => '#2271b1', // Default color.
				'nice_name'            => $product->get_name(),
				'documentation_link'   => '',
				'premium_support_link' => '',
				'feature_request_link' => '',
				'wizard_link'          => '',
			],
			$this->float_widget_data
		);

		return $can_load;
	}

	/**
	 * Registers the hooks.
	 *
	 * @param Product $product Product to load.
	 */
	public function load( $product ) {
		$this->product = $product;

		add_action( 'in_admin_footer', [ $this, 'render_float_placeholder' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_float_widget_script' ] );
	}

	/**
	 * Returns the allowed pages for the float widget.
	 *
	 * @return array
	 */
	private function get_allowed_pages() {
		if ( ! isset( $this->float_widget_data['pages'] ) || ! is_array( $this->float_widget_data['pages'] ) ) {
			return [];
		}
		return $this->float_widget_data['pages'];
	}

	/**
	 * Checks if the current screen is allowed for the float widget.
	 *
	 * @return bool
	 */
	private function is_current_screen_allowed() {
		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->id ) ) {
			return false;
		}

		if ( ! in_array( $current_screen->id, $this->get_allowed_pages(), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Renders the float widget placeholder.
	 *
	 * @return void
	 */
	public function render_float_placeholder() {
		if ( ! $this->is_current_screen_allowed() ) {
			return;
		}

		echo '<div id="ti-sdk-float-widget" style="--ti-float-primary-color: ' . esc_attr( $this->float_widget_data['primary_color'] ) . '"></div>';
	}

	/**
	 * Enqueue scripts & styles.
	 *
	 * @return void
	 */
	public function enqueue_float_widget_script() {

		if ( ! $this->is_current_screen_allowed() ) {
			return;
		}

		global $themeisle_sdk_max_path;
		$handle     = 'ti-sdk-float-' . $this->product->get_key();
		$asset_file = require $themeisle_sdk_max_path . '/assets/js/build/float_widget/float.asset.php';
		$deps       = array_merge( $asset_file['dependencies'], [ 'updates' ] );

		wp_register_script( $handle, $this->get_sdk_uri() . 'assets/js/build/float_widget/float.js', $deps, $asset_file['version'], true );
		wp_localize_script( $handle, 'tiSDKFloatData', $this->get_float_localization_data() );

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle, $this->get_sdk_uri() . 'assets/js/build/float_widget/float.css', [ 'wp-components' ], $asset_file['version'] );
	}

	/**
	 * Get the float widget localization data.
	 *
	 * @return array
	 */
	private function get_float_localization_data() {
		return [
			'logoUrl'      => $this->float_widget_data['logo'],
			'primaryColor' => esc_attr( $this->float_widget_data['primary_color'] ),
			'strings'      => [
				'toggleButton' => sprintf( Loader::$labels['float_widget']['button'], $this->float_widget_data['nice_name'] ),
				'panelGreet'   => sprintf( Loader::$labels['float_widget']['panel']['greeting'], $this->float_widget_data['nice_name'] ),
				'panelTitle'   => Loader::$labels['float_widget']['panel']['title'],
				'closeToggle'  => Loader::$labels['float_widget']['panel']['close'],
			],
			'links'        => $this->get_links(),
		];
	}

	/**
	 * Generates the links for the float widget.
	 *
	 * For Free:
	 * - Documentation (redirects to Themeisle doc page)
	 * - Get Support (redirects to WP free support forum)
	 * - Run Setup Wizard (this will trigger the setup wizard) if available
	 * - Upgrade to Pro (redirects to Themeisle upgrade page)
	 * - Rate Us (redirects to WP rating page)
	 *
	 * For Pro:
	 * - Documentation (redirects to Themeisle doc page)
	 * - Get Support (redirects to Themeisle support page to open a ticket)
	 * - Run Setup Wizard (this will trigger the setup wizard) if available
	 * - Feature Request (if available redirect to collect feedback requests)
	 * - Rate Us (redirects to WP rating page)
	 *
	 * @return array
	 */
	private function get_links() {
		$links = [];

		if ( ! empty( $this->float_widget_data['documentation_link'] ) ) {
			$links[] = [
				'icon'  => 'dashicons-book-alt',
				'title' => Loader::$labels['float_widget']['links']['documentation'],
				'link'  => $this->float_widget_data['documentation_link'],
			];
		}

		$support_link = [
			'icon'  => 'dashicons-format-status',
			'title' => Loader::$labels['float_widget']['links']['support'],
			'link'  => 'https://wordpress.org/support/' . $this->product->get_type() . '/' . $this->product->get_slug() . '/',
		];
		if ( ! $this->float_widget_data['has_upgrade_menu'] && ! empty( $this->float_widget_data['premium_support_link'] ) ) {
			$support_link['link'] = $this->float_widget_data['premium_support_link'];
		}
		$links[] = $support_link;

		if ( ! empty( $this->float_widget_data['wizard_link'] ) ) {
			$links[] = [
				'icon'     => 'dashicons-admin-tools',
				'title'    => Loader::$labels['float_widget']['links']['wizard'],
				'link'     => $this->float_widget_data['wizard_link'],
				'internal' => true,
			];
		}

		$pro             = [
			'icon'  => 'dashicons-superhero-alt',
			'title' => Loader::$labels['float_widget']['links']['upgrade'],
			'link'  => $this->float_widget_data['upgrade_link'],
		];
		$featured_or_pro = $pro;
		if ( ! $this->float_widget_data['has_upgrade_menu'] ) {
			$featured_or_pro   = []; // we remove the upgrade link
			$featured          = $pro;
			$featured['title'] = Loader::$labels['float_widget']['links']['feature_request'];
			$featured['link']  = $this->float_widget_data['feature_request_link'];
			if ( ! empty( $featured['link'] ) ) {
				$featured_or_pro = $featured;
			}
		}

		if ( ! empty( $featured_or_pro ) ) {
			$links[] = $featured_or_pro;
		}

		$links[] = [
			'icon'  => 'dashicons-star-filled',
			'title' => Loader::$labels['float_widget']['links']['rate'],
			'link'  => 'https://wordpress.org/support/' . $this->product->get_type() . '/' . $this->product->get_slug() . '/reviews/#new-post',
		];

		return $links;
	}
}
