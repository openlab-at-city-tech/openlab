<?php
/**
 * BLC admin notice for main site's onboarding page
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Multisite
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Multisite;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Admin_Notice;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Notices\Multisite
 */
class Controller extends Admin_Notice {
	/**
	 * Use the Dashboard_API Trait.
	 *
	 * @since 2.0.0
	 */
	use Dashboard_API;

	public function init() {
		parent::init();
		add_action( 'wp_ajax_wpmudev_blc_multisite_notification_dismiss', array(
			$this,
			'dismiss_multisite_notification'
		) );
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		View::instance()->render( array(
			'site_connected' => (bool) self::site_connected(),
			'use_legacy'     => (bool) Settings::instance()->get( 'use_legacy_blc_version' )
		) );
	}

	/**
	 * Register css files for admin page.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_multisite_notice' => [
				'src' => $this->styles_dir . 'multisite-notice.min.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			],
		);
	}

	/**
	 * Adds Page specific hooks. Extends $this->actions().
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function notice_hooks() {
		if ( $this->can_boot() ) {
			add_filter( 'admin_body_class', array( $this, 'admin_dash_page_classes' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'inline_script' ) );

		}
	}

	/**
	 * Adds the inline script that handles the notification dismiss. Loads it from View.
	 * @return void
	 */
	public function inline_script() {
		wp_add_inline_script( 'blc_dashboard', View::instance()->render_inline_script() );
	}

	public function dismiss_multisite_notification() {
		check_ajax_referer( 'wpmudev-blc-multisite-notification-dismiss-nonce', 'security' );

		Settings::instance()->init();
		Settings::instance()->set( array( 'show_multisite_notice' => false ) );
		Settings::instance()->save();
	}

	/**
	 * Returns true if module component should load, else returns false.
	 *
	 * @since 2.0.0
	 *
	 * @return boolean
	 */
	public function can_boot() {
		if ( is_multisite() && Settings::instance()->get( 'show_multisite_notice' ) && Utilities::is_admin_screen( 'blc_dash' ) ) {
			return true;
		}

		return false;
	}

	public function admin_dash_page_classes( $classes ) {
		return $classes . ' blc-show-multisite-notice';
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function prepare_props() {
	}
}
