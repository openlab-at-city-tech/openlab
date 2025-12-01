<?php
/**
 * The logger model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
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
 * Logger module for ThemeIsle SDK.
 */
class Logger extends Abstract_Module {
	/**
	 * Endpoint where to collect logs.
	 */
	const TRACKING_ENDPOINT = 'https://api.themeisle.com/tracking/log';

	/**
	 * Endpoint where to collect telemetry.
	 */
	const TELEMETRY_ENDPOINT = 'https://api.themeisle.com/tracking/events';


	/**
	 * Check if we should load the module for this product.
	 *
	 * @param Product $product Product to load the module for.
	 *
	 * @return bool Should we load ?
	 */
	public function can_load( $product ) {
		return apply_filters( $product->get_slug() . '_sdk_enable_logger', true );
	}

	/**
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Logger Module object.
	 */
	public function load( $product ) {
		$this->product = $product;
		add_action( 'wp_loaded', array( $this, 'setup_actions' ) );
		add_action( 'admin_init', array( $this, 'setup_notification' ) );
		return $this;
	}

	/**
	 * Setup notification on admin.
	 */
	public function setup_notification() {
		if ( $this->is_logger_active() ) {
			return;
		}
		add_filter( 'themeisle_sdk_registered_notifications', [ $this, 'add_notification' ] );
	}

	/**
	 * Setup tracking actions.
	 */
	public function setup_actions() {
		if ( ! $this->is_logger_active() ) {
			return;
		}
		add_action(
			'admin_enqueue_scripts',
			function() {
				if ( ! apply_filters( 'themeisle_sdk_enable_telemetry', false ) ) {
					return;
				}

				$this->load_telemetry();
			},
			PHP_INT_MAX
		);

		$action_key = $this->product->get_key() . '_log_activity';
		if ( ! wp_next_scheduled( $action_key ) ) {
			wp_schedule_single_event( time() + ( wp_rand( 1, 24 ) * 3600 ), $action_key );
		}
		add_action( $action_key, array( $this, 'send_log' ) );
	}

	/**
	 * Check if the logger is active.
	 *
	 * @return bool Is logger active?
	 */
	private function is_logger_active() {
		if ( apply_filters( 'themeisle_sdk_disable_telemetry', false ) ) {
			return false;
		}
		$default = 'no';

		if ( ! $this->product->is_wordpress_available() ) {
			$default = 'yes';
		} else {
			$all_products = Loader::get_products();
			foreach ( $all_products as $product ) {
				if ( $product->requires_license() ) {
					$default = 'yes';
					break;
				}
			}
		}


		return ( get_option( $this->product->get_key() . '_logger_flag', $default ) === 'yes' );
	}
	/**
	 * Add notification to queue.
	 *
	 * @param array $all_notifications Previous notification.
	 *
	 * @return array All notifications.
	 */
	public function add_notification( $all_notifications ) {

		$message = apply_filters( $this->product->get_key() . '_logger_heading', Loader::$labels['logger']['notice'] );

		$message       = str_replace(
			array( '{product}' ),
			$this->product->get_friendly_name(),
			$message
		);
		$button_submit = apply_filters( $this->product->get_key() . '_logger_button_submit', Loader::$labels['logger']['cta_y'] );
		$button_cancel = apply_filters( $this->product->get_key() . '_logger_button_cancel', Loader::$labels['logger']['cta_n'] );

		$all_notifications[] = [
			'id'      => $this->product->get_key() . '_logger_flag',
			'message' => $message,
			'ctas'    => [
				'confirm' => [
					'link' => '#',
					'text' => $button_submit,
				],
				'cancel'  => [
					'link' => '#',
					'text' => $button_cancel,
				],
			],
		];

		return $all_notifications;
	}

	/**
	 * Send the statistics to the api endpoint.
	 */
	public function send_log() {
		$environment                    = array();
		$theme                          = wp_get_theme();
		$environment['theme']           = array();
		$environment['theme']['name']   = $theme->get( 'Name' );
		$environment['theme']['author'] = $theme->get( 'Author' );
		$environment['theme']['parent'] = $theme->parent() !== false ? $theme->parent()->get( 'Name' ) : $theme->get( 'Name' );
		$environment['plugins']         = get_option( 'active_plugins' );
		global $wp_version;
		wp_remote_post(
			self::TRACKING_ENDPOINT,
			array(
				'method'      => 'POST',
				'timeout'     => 3,
				'redirection' => 5,
				'body'        => array(
					'site'         => get_site_url(),
					'slug'         => $this->product->get_slug(),
					'version'      => $this->product->get_version(),
					'wp_version'   => $wp_version,
					'install_time' => $this->product->get_install_time(),
					'locale'       => get_locale(),
					'data'         => apply_filters( $this->product->get_key() . '_logger_data', array() ),
					'environment'  => $environment,
					'license'      => apply_filters( $this->product->get_key() . '_license_status', '' ),
				),
			)
		);
	}

	/**
	 * Load telemetry.
	 *
	 * @return void
	 */
	public function load_telemetry() {
		// See which products have telemetry enabled.
		try {
			$products_with_telemetry                    = array();
			$all_products                               = Loader::get_products();
			$all_products[ $this->product->get_slug() ] = $this->product; // Add current product to the list of products to check for telemetry.

			// Register telemetry params for eligible products.
			foreach ( $all_products as $product_slug => $product ) {

				// Ignore PRO products.
				if ( false !== strstr( $product_slug, 'pro' ) ) {
					continue;
				}

				$pro_slug   = $product->get_pro_slug();
				$logger_key = $product->get_key() . '_logger_flag';

				// If the product is not available in the WordPress store, or it's PRO version is installed, activate the logger if it was not initialized -- Pro users are opted in by default.
				if ( ! $product->is_wordpress_available() || ( ! empty( $pro_slug ) && isset( $all_products[ $pro_slug ] ) ) ) {
					$logger_flag = get_option( $logger_key );

					if ( false === $logger_flag ) {
						update_option( $logger_key, 'yes' );
					}
				}

				if ( 'yes' === get_option( $product->get_key() . '_logger_flag', 'no' ) ) {

					$main_slug  = explode( '-', $product_slug );
					$main_slug  = $main_slug[0];
					$track_hash = Licenser::create_license_hash( str_replace( '-', '_', ! empty( $pro_slug ) ? $pro_slug : $product_slug ) );

					// Check if product was already tracked.
					$active_telemetry = false;
					foreach ( $products_with_telemetry as $product_with_telemetry ) {
						if ( $product_with_telemetry['slug'] === $main_slug ) {
							$active_telemetry = true;
							break;
						}
					}

					if ( $active_telemetry ) {
						continue;
					}

					$products_with_telemetry[] = array(
						'slug'      => $main_slug,
						'trackHash' => $track_hash ? $track_hash : 'free',
						'consent'   => true,
					);
				}
			}

			$products_with_telemetry = apply_filters( 'themeisle_sdk_telemetry_products', $products_with_telemetry );

			if ( 0 === count( $products_with_telemetry ) ) {
				return;
			}

			$tracking_handler = apply_filters( 'themeisle_sdk_dependency_script_handler', 'tracking' );
			if ( ! empty( $tracking_handler ) ) {
				do_action( 'themeisle_sdk_dependency_enqueue_script', 'tracking' );
				wp_localize_script(
					$tracking_handler,
					'tiTelemetry',
					array(
						'products' => $products_with_telemetry,
						'endpoint' => self::TELEMETRY_ENDPOINT,
					)
				);
			}
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( $e->getMessage() ); // phpcs:ignore
			}
		} finally {
			return;
		}
	}
}
