<?php
/**
 * Prepares and loads the Pages Cross Sell submodule.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.4.3
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Submodules\Cross_Sell
 *
 * @copyright (c) 2025, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Submodules\Cross_Sell;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Submodules\Cross_Sell
 */
class Controller extends Base {
	/**
	 * Use the Dashboard_API Traits.
	 *
	 * @since 2.4.3
	 */
	use Dashboard_API;

    /**
	 * Init function.
	 *
	 * @since 2.4.3
	 */
	public function init() {
		add_action( 'init', array( $this, 'load_submodule' ) );
	}

    /**
     * Load the submodule.
     * 
     * @return \WPMUDEV\Modules\Plugin_Cross_Sell|null
     */
    public function load_submodule(): void {
        if ( ! file_exists( WPMUDEV_BLC_DIR . 'core/external/plugins-cross-sell-page/plugin-cross-sell.php' ) || ! $this->can_load() ) {
            return;
        }

		static $cross_sell = null;

		if ( ! is_null( $cross_sell ) ) {
			return;
		} 

		if ( ! class_exists( '\WPMUDEV\Modules\Plugin_Cross_Sell' ) ) {
			require_once WPMUDEV_BLC_DIR . 'core/external/plugins-cross-sell-page/plugin-cross-sell.php';
		}

		$submenu_params = array(
			'slug'        => 'broken-link-checker',
			'parent_slug' => 'blc_dash',
			'capability'  => 'manage_options',
			'menu_slug'   => 'plugins_cross_sell',
			'position'    => 2,
		);

		$cross_sell = new \WPMUDEV\Modules\Plugin_Cross_Sell( $submenu_params );
    }

    /**
     * Check if the submodule should load
     * 
     * @return bool
     */
    protected function can_load(): bool {
        return boolval( 
            apply_filters( 
                'wpmudev_blc_dashboard_load_cross_sell_module',  
                ! class_exists( 'WPMUDEV_Dashboard' ) || 'free' === $this->get_membership_type() ) 
        );
    }
}