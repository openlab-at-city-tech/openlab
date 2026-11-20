<?php
/**
 * Black Friday Campaign Loader
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.4.9
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Submodules\Black_Friday
 */

namespace WPMUDEV_BLC\App\Submodules\Black_Friday;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Submodules\Black_Friday
 */
class Controller extends Base {
	/**
	 * Use the Dashboard_API Traits.
	 *
	 * @since 2.4.15.1
	 */
	use Dashboard_API;

	/**
	 * Init Black Friday Campaign Loader
	 *
	 * @since 2.4.9
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'maybe_load_bf_campaign' ) );
	}

	/**
	 * Maybe load Black Friday Campaign
	 *
	 * Dates, locations, dismissals, multisite and user role are handled by the sub module itself.
	 *
	 * @since 2.4.9
	 *
	 * @return void
	 */
	public function maybe_load_bf_campaign() {
		if ( ! $this->can_load() ) {
			return;
		}

		$black_friday_path = WPMUDEV_BLC_DIR . 'core/external/wpmudev-black-friday/campaign.php';

		if ( ! file_exists( $black_friday_path ) ) {
			return;
		}

		// Another WPMU DEV plugin may have already loaded the module.
		if ( class_exists( '\WPMUDEV\Modules\BlackFriday\Campaign' ) ) {
			return;
		}

		require_once $black_friday_path;
		new \WPMUDEV\Modules\BlackFriday\Campaign();
	}

	/**
	 * Checks if the Black Friday sub module should load.
	 *
	 * It should not load when the WPMU DEV Dashboard is active, or when the site is connected to a paid membership.
	 *
	 * @since 2.4.15.1
	 *
	 * @return bool
	 */
	protected function can_load(): bool {
		$can_load = ! class_exists( 'WPMUDEV_Dashboard' ) && ! $this->has_paid_membership();

		/**
		 * Filters if the Black Friday sub module should load.
		 *
		 * @since 2.4.15.1
		 *
		 * @param bool $can_load True if the sub module should load.
		 */
		return boolval( apply_filters( 'wpmudev_blc_load_black_friday_module', $can_load ) );
	}

	/**
	 * Checks if the site is connected to a paid WPMU DEV membership.
	 *
	 * Free, expired or no membership (not connected) can still see the deal.
	 *
	 * @since 2.4.15.1
	 *
	 * @return bool
	 */
	protected function has_paid_membership(): bool {
		// Without the Hub Connector the site can't be connected, so there is no membership.
		if ( ! class_exists( '\WPMUDEV\Hub\Connector\API' ) || ! $this->site_connected() ) {
			return false;
		}

		return in_array( $this->get_membership_type(), array( 'full', 'unit', 'single', 'paused' ), true );
	}
}
