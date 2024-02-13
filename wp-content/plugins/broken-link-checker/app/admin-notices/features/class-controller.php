<?php
/**
 * BLC admin notice for local screens displaying new Features.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Features
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Features;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Controllers\Admin_Notice;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Notices\Features
 */
class Controller extends Admin_Notice {
	/**
	 * Admin Menu Callback.
	 *
	 * @return void The callback function of the Admin Menu Page.
	 * @since 2.2
	 *
	 */
	public function output() {
		//View::instance()->render();
		add_action( 'wpmudev-blc-local-nav-before', array( View::instance(), 'render' ) );
	}

	/**
	 * Returns true if module component should load, else returns false.
	 *
	 * @return void
	 * @since 2.2
	 *
	 */
	public function can_boot() {
		/**
		 * Until multisites are officially supported, BLC v2 menus are disabled in subsites.
		 * Local menus are loaded instead. Local admin notification does not need to appear on subsites at this point.
		 */
		if ( Utilities::is_subsite() || ! Settings::instance()->get( 'use_legacy_blc_version' ) ) {
			return;
		}

		$version_highlights = Settings::instance()->get( 'version_highlights' );
		$version_highlights_shown = ! empty( $version_highlights['2_2_0'] );

		return Utilities::is_admin_screen( $this->admin_pages ) && ! $version_highlights_shown;
	}

	public function admin_dash_page_classes( $classes ) {
		return $classes . ' blc-show-features-notice';
	}


	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/dist/local-features.asset.php';
			$scripts_version = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	public function set_admin_styles() {
		return array(
			'blc_sui'          => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			),
			'blc_features_notice' => array(
				'src' => $this->scripts_dir . 'style-local-features.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			),
		);
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @return void
	 * @since 2.2
	 *
	 */
	public function prepare_props() {
		if ( isset( $_GET['highlights_shown'] ) && $_GET['highlights_shown'] && ! empty( $_GET['nonce'] ) && ( wp_verify_nonce( $_GET['nonce'], 'blc_highlights_shown' ) ) ) {
			Settings::instance()->set(
				array(
					'version_highlights' => array(
						'2_2_0' => true,
					),
				)
			);

			Settings::instance()->save();

			if ( isset( $_GET['redirect'] ) ) {
				wp_safe_redirect( $_GET['redirect'] );
			}
		}

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
