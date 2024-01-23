<?php
/**
 * BLC admin notice for legacy screens
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Legacy
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Legacy;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Admin_Notice;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 */
class Controller extends Admin_Notice {
	/**
	 * Admin Menu Callback.
	 *
	 * @return void The callback function of the Admin Menu Page.
	 * @since 1.0.0
	 *
	 */
	public function output() {
		View::instance()->render();
	}

	/**
	 * Register css files for admin page.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_legacy_notice' => [
				'src' => $this->styles_dir . 'legacy-notice.min.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			],
		);
	}

	/**
	 * Adds Page specific hooks. Extends $this->actions().
	 *
	 * @return void
	 * @since 2.0.0
	 *
	 */
	public function notice_hooks() {
		if ( $this->can_boot() ) {
			add_filter( 'admin_body_class', array( $this, 'admin_dash_page_classes' ), 999 );
		}
	}

	/**
	 * Returns true if module component should load, else returns false.
	 *
	 * @return void
	 * @since 2.0.0
	 *
	 */
	public function can_boot() {
		// TODO: Probably remove this notice completely.
		// For now not allowing to boot.
		return false;

		/**
		 * Until multisites are officially supported, BLC v2 menus are disabled in subsites.
		 * Legacy menus are loaded instead. Lagecy admin notification does not need to appear on subsites at this point.
		 */
		if ( Utilities::is_subsite() ) {
			return;
		}

		/* We use the Utilities::$value_provider array variable.
		   This variable can hold values that can be used from different classes which should help avoid checking
		same conditions multiple times.
		   In this case we are using `boot_admin_legacy_pages` key which is also used in
		WPMUDEV_BLC\App\Admin_Modals\Legacy\Controller class.
		*/
		if ( ! isset( Utilities::$value_provider[ 'boot_admin_legacy_pages' ] ) ) {
			Utilities::$value_provider['boot_admin_legacy_pages'] = Utilities::is_admin_screen( $this->admin_pages );
		}

		return Utilities::$value_provider[ 'boot_admin_legacy_pages' ];
	}

	public function admin_dash_page_classes( $classes ) {
		return $classes . ' blc-show-legacy-notice';
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function prepare_props() {
		/*
		 * Se the admin pages the notice will be visible at.
		 */
		$this->admin_pages = array(
			'view-broken-links',
			'link-checker-settings',
			'blc_local',
		);
	}
}
