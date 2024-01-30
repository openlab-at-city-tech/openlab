<?php
/**
 * BLC admin notice for local screens when Cloud BLC is active.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Local
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Local;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Controllers\Admin_Notice;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Notices\Local
 */
class Controller extends Admin_Notice {
	/**
	 * Admin Menu Callback.
	 *
	 * @return void The callback function of the Admin Menu Page.
	 * @since 2.1
	 *
	 */
	public function output() {
		//View::instance()->render();
	}

	/**
	 * Returns true if module component should load, else returns false.
	 *
	 * @return void
	 * @since 2.1
	 *
	 */
	public function can_boot() {
		/**
		 * Until multisites are officially supported, BLC v2 menus are disabled in subsites.
		 * Local menus are loaded instead. Local admin notification does not need to appear on subsites at this point.
		 */
		if ( Utilities::is_subsite() || Settings::instance()->get( 'use_legacy_blc_version' ) ) {
			return;
		}

		/* We use the Utilities::$value_provider array variable.
		   This variable can hold values that can be used from different classes which should help avoid checking
		same conditions multiple times.
		   In this case we are using `boot_admin_local_page` key which is also used in
		WPMUDEV_BLC\App\Admin_Modals\Local\Controller class.
		*/
		if ( ! isset( Utilities::$value_provider[ 'boot_admin_local_page' ] ) ) {
			Utilities::$value_provider['boot_admin_local_page'] = Utilities::is_admin_screen( $this->admin_pages );
		}

		return Utilities::$value_provider[ 'boot_admin_local_page' ];
	}

	public function admin_dash_page_classes( $classes ) {
		return $classes . ' blc-show-local-notice';
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 2.1
	 * @return array Register scripts for the admin page.
	 */
	/*
	public function set_admin_scripts() {
		$script_data  = include WPMUDEV_BLC_DIR . 'assets/js/local-modal/main.asset.php';
		$dependencies = $script_data['dependencies'] ?? array(
			'react',
			'wp-element',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-polyfill',
		);
		$version      = $script_data['version'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
		$signup_url   = add_query_arg(
			array(
				'utm_source'   => 'blc',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'blc_plugin_onboarding',
			),
			Utilities::signup_url()
		);

		return array(
			'blc_local_notice' => array(
				'src'       => $this->scripts_dir . 'local-notice.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_local_notice' => array(
						'data'   => array(
							'rest_url'           => esc_url_raw( rest_url() ),
							'settings_endpoint'  => '/wpmudev_blc/v1/settings',
							'nonce'              => wp_create_nonce( 'wp_rest' ),
						),
						'labels' => array(
							'error_messages' => array(
								'general' => __( 'Something went wrong here.', 'broken-link-checker' ),
							),
						),
					),
				),

				'translate' => true,
			),
			// END OF blc_activation_popup.
		);
	}
	*/

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/js/local-notice/main.asset.php';
			$scripts_version = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @return void
	 * @since 2.1
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
