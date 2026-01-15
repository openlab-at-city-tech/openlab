<?php
/**
 * WPMUDEV Black Friday common module
 *
 * Used by wordpress.org free plugins only to show Black Friday deal on admin dashboard.
 *
 * @since   1.0
 * @author  WPMUDEV
 * @package WPMUDEV\BlackFriday
 */

namespace WPMUDEV\Modules\BlackFriday;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Sub-module version.
if ( ! defined( 'WPMUDEV_MODULE_BLACK_FRIDAY_VERSION' ) ) {
	define( 'WPMUDEV_MODULE_BLACK_FRIDAY_VERSION', '2.0.0' );
}

// Sub-module directory.
if ( ! defined( 'WPMUDEV_MODULE_BLACK_FRIDAY_DIR' ) ) {
	define( 'WPMUDEV_MODULE_BLACK_FRIDAY_DIR', plugin_dir_path( __FILE__ ) );
}

// Sub-module url.
if ( ! defined( 'WPMUDEV_MODULE_BLACK_FRIDAY_URL' ) ) {
	define( 'WPMUDEV_MODULE_BLACK_FRIDAY_URL', plugin_dir_url( __FILE__ ) );
}

// Sub-module Assets url.
if ( ! defined( 'WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL' ) ) {
	define( 'WPMUDEV_MODULE_BLACK_FRIDAY_ASSETS_URL', untrailingslashit( WPMUDEV_MODULE_BLACK_FRIDAY_URL ) . '/build' );
}

if ( ! class_exists( __NAMESPACE__ . '\\Campaign' ) ) {
	/**
	 * Class Load.
	 *
	 * @since    1.0
	 * @package  WPMUDEV\BlackFriday\Campaign
	 */
	class Campaign {
		/**
		 * Start date of the campaign. Date format : dd-mm-yyyy.
		 *
		 * @var string
		 */
		protected $campaign_start_date = '21-11-2025';

		/**
		 * End date of the campaign. Date format : dd-mm-yyyy.
		 *
		 * @var string
		 */
		protected $campaign_end_date = '03-12-2025';

		/**
		 * Construct handler class.
		 *
		 * @since 1.0
		 *
		 * @param array $props   Campaign props.
		 *
		 * @return void
		 */
		public function __construct( array $props = array() ) {
			$props['campaign_url'] = $props['campaign_url'] ?? 'https://wpmudev.com/black-friday/';
			$props['utm_campaign'] = $props['utm_campaign'] ?? 'black-friday-2025';
			$props['utm_medium']   = $props['utm_medium'] ?? 'plugin';
			$props['priority']     = $props['priority'] ?? 10;

			add_action( 'init', array( $this, 'add_textdomain' ) );

			$this->load_modules( $props );
		}

		/**
		 * Load required modules.
		 *
		 * @since 2.0.0
		 *
		 * @param array $props   Campaign props.
		 *
		 * @return void
		 */
		public function load_modules( array $props = array() ) {
			static $loaded = false;
			if ( $loaded || ! $this->can_load() ) {
				return;
			}
			$loaded = true;

			// Load Utils so other classes can use it.
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-utils.php';

			require_once 'includes/class-banner.php';

			if ( class_exists( 'WPMUDEV\Modules\BlackFriday\Banner' ) ) {
				new Banner( $props );
			}

			require_once 'includes/class-admin-menu.php';

			if ( class_exists( 'WPMUDEV\Modules\BlackFriday\Admin_Menu' ) ) {
				new Admin_Menu( $props );
			}

			require_once 'includes/class-action-links.php';
			if ( class_exists( 'WPMUDEV\Modules\BlackFriday\Action_Links' ) ) {
				new Action_Links( $props );
			}
		}

		/**
		 * Checks if plugin's Black Friday deal can be loaded.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function can_load() {
			if (
				! current_user_can( 'manage_options' ) ||
				$this->event_expired() ||
				$this->dashboard_plugin_installed()
			) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if offer has expired.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function event_expired() {
			$current_date = apply_filters( 'wpmudev_blackfriday_current_date', 'd-m-Y' );
			$start_date   = apply_filters( 'wpmudev_blackfriday_start_date', $this->campaign_start_date );
			$expire_date  = apply_filters( 'wpmudev_blackfriday_expire_date', $this->campaign_end_date );

			// Expires on 29 Nov 2025.
			return (
				date_create( date_i18n( $current_date ) )->getTimestamp() < date_create( date_i18n( $start_date ) )->getTimestamp() ||
				date_create( date_i18n( $current_date ) )->getTimestamp() >= date_create( date_i18n( $expire_date ) )->getTimestamp()
			);
		}

		/**
		 * Checks if Dashboard plugin is installed.
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function dashboard_plugin_installed() {
			return class_exists( 'WPMUDEV_Dashboard' );
		}

		/**
		 * Load sub-module textdomain.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public function add_textdomain() {
			load_plugin_textdomain(
				'wpmudev-black-friday',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		}
	}
}
