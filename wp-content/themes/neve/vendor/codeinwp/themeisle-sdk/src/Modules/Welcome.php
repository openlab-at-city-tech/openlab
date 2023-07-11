<?php
/**
 * The welcome model class for ThemeIsle SDK
 *
 * Here's how to hook it in your plugin or theme:
 * ```php
 *      add_filter( '<product_slug>_welcome_metadata', function() {
 *          return [
 *               'is_enabled' => <condition_if_pro_available>,
 *               'pro_name' => 'Product PRO name',
 *               'logo' => '<path_to_logo>',
 *               'cta_link' => tsdk_utmify( 'https://link_to_upgrade.with/?discount=<discountCode>')
 *          ];
 *      } );
 * ```
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2023, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

// Exit if accessed directly.
use ThemeisleSDK\Common\Abstract_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promotions module for ThemeIsle SDK.
 */
class Welcome extends Abstract_Module {

	/**
	 * Debug mode.
	 *
	 * @var bool
	 */
	private $debug = false;

	/**
	 * Welcome metadata.
	 *
	 * @var array
	 */
	private $welcome_discounts = array();

	/**
	 * Check that we can load this module.
	 *
	 * @param \ThemeisleSDK\Product $product The product.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		$this->debug      = apply_filters( 'themeisle_sdk_welcome_debug', $this->debug );
		$welcome_metadata = apply_filters( $product->get_key() . '_welcome_metadata', array() );

		$is_welcome_enabled = $this->is_welcome_meta_valid( $welcome_metadata );

		if ( $is_welcome_enabled ) {
			$this->welcome_discounts[ $product->get_key() ] = $welcome_metadata;
		}

		return $this->debug || $is_welcome_enabled;
	}

	/**
	 * Check that the metadata is valid and the welcome is enabled.
	 *
	 * @param array $welcome_metadata The metadata to validate.
	 *
	 * @return bool
	 */
	private function is_welcome_meta_valid( $welcome_metadata ) {
		return ! empty( $welcome_metadata ) && isset( $welcome_metadata['is_enabled'] ) && $welcome_metadata['is_enabled'];
	}

	/**
	 * Load the module.
	 *
	 * @param \ThemeisleSDK\Product $product The product.
	 *
	 * @return $this
	 */
	public function load( $product ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$this->product = $product;
		if ( ! $this->is_time_to_show_welcome() && $this->debug === false ) {
			return;
		}

		add_filter( 'themeisle_sdk_registered_notifications', [ $this, 'add_notification' ], 99, 1 );

		return $this;
	}

	/**
	 * Check if it's time to show the welcome.
	 *
	 * @return bool
	 */
	private function is_time_to_show_welcome() {
		// if 7 days from install have not passed, don't show the welcome.
		if ( $this->product->get_install_time() + 7 * DAY_IN_SECONDS > time() ) {
			return false;
		}

		// if 12 days from install have passed, don't show the welcome ( after 7 days for 5 days ).
		if ( $this->product->get_install_time() + 12 * DAY_IN_SECONDS < time() ) {
			return false;
		}

		return true;
	}

	/**
	 * Add the welcome notification.
	 * Will block all other notifications if a welcome notification is present.
	 *
	 * @return array
	 */
	public function add_notification( $all_notifications ) {
		if ( empty( $this->welcome_discounts ) ) {
			return $all_notifications;
		}

		if ( ! isset( $this->welcome_discounts[ $this->product->get_key() ] ) ) {
			return $all_notifications;
		}

		// filter out the notifications that are not welcome upsells
		// if we arrived here we will have at least one welcome upsell
		$all_notifications = array_filter(
			$all_notifications,
			function( $notification ) {
				return strpos( $notification['id'], '_welcome_upsell_flag' ) !== false;
			}
		);

		$offer = $this->welcome_discounts[ $this->product->get_key() ];

		$response = [];
		$logo     = isset( $offer['logo'] ) ? $offer['logo'] : '';
		$pro_name = isset( $offer['pro_name'] ) ? $offer['pro_name'] : $this->product->get_friendly_name() . ' PRO';

		$link = $offer['cta_link'];

		$message = apply_filters( $this->product->get_key() . '_welcome_upsell_message', '<p>You\'ve been using <b>{product}</b> for 7 days now and we appreciate your loyalty! We also want to make sure you\'re getting the most out of our product. That\'s why we\'re offering you a special deal - upgrade to <b>{pro_product}</b> in the next 5 days and receive a discount of <b>up to 30%</b>. <a href="{cta_link}" target="_blank">Upgrade now</a> and unlock all the amazing features of <b>{pro_product}</b>!</p>' );

		$button_submit = apply_filters( $this->product->get_key() . '_feedback_review_button_do', 'Upgrade Now!' );
		$button_cancel = apply_filters( $this->product->get_key() . '_feedback_review_button_cancel', 'No, thanks.' );
		$message       = str_replace(
			[ '{product}', '{pro_product}', '{cta_link}' ],
			[
				$this->product->get_friendly_name(),
				$pro_name,
				$link,
			],
			$message
		);

		$all_notifications[] = [
			'id'      => $this->product->get_key() . '_welcome_upsell_flag',
			'message' => $message,
			'img_src' => $logo,
			'ctas'    => [
				'confirm' => [
					'link' => $link,
					'text' => $button_submit,
				],
				'cancel'  => [
					'link' => '#',
					'text' => $button_cancel,
				],
			],
			'type'    => 'info',
		];

		$key        = array_rand( $all_notifications );
		$response[] = $all_notifications[ $key ];

		return $response;
	}

}
