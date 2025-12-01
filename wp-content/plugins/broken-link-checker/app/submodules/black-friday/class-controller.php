<?php
/**
 * Black Friday Campaign Loader
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.4.9
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Submodules\Black_Friday
 *
 */

namespace WPMUDEV_BLC\App\Submodules\Black_Friday;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Submodules\Black_Friday
 */
class Controller extends Base {

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
     * @since 2.4.9
     *
     * @return void
     */
    public function maybe_load_bf_campaign() {
		if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
			return;
		}
		$black_friday_path = WPMUDEV_BLC_DIR . 'core/external/wpmudev-black-friday/campaign.php';
		if ( ! file_exists( $black_friday_path ) ) {
			return;
		}
		if ( ! class_exists( 'WPMUDEV\Modules\BlackFriday\Campaign' ) ) {
			require_once $black_friday_path;
			new \WPMUDEV\Modules\BlackFriday\Campaign();
		}
	}
}