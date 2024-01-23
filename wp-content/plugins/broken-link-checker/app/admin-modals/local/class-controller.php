<?php
/**
 * BLC admin modal for legacy screens. Called from WPMUDEV_BLC\App\Admin_Pages\Local_Submenu\View.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Legacy
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Modals\Local;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Enqueue;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Modals\Local
 */
class Controller extends Base {
	/**
	 * Use the Enqueue and Dashboard_API Traits.
	 *
	 * @since 2.1
	 */
	use Enqueue, Dashboard_API;

	/**
	 * The unique id that can be used by react.
	 *
	 * @since 2.1
	 * @var int $unique_id
	 *
	 */
	public static $unique_id = null;

	/**
	 * The admin pages the notice will be visible at.
	 *
	 * @since 2.1
	 * @var array $admin_pages
	 *
	 */
	protected $admin_pages = array();

	/**
	 * Init function.
	 *
	 * @since 2.1
	 */
	public function init() {
		add_action( 'current_screen', array( $this, 'boot' ), 11 );
	}

	/**
	 * Boots modal parts.
	 */
	public function boot() {
		if ( $this->can_boot() ) {
			add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );
			//add_action( 'admin_footer', array( $this, 'output' ) );

			self::$unique_id = Utilities::get_unique_id();

			View::instance()::$unique_id = self::$unique_id;

			$this->prepare_props();
			$this->prepare_scripts();
		}
	}

	/**
	 * Checks if admin page actions/scripts should load in current screen.
	 *
	 * @since 2.1
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueuing scripts.
	 */
	public function can_boot() {
		/*
		 * Show modal only when Local BLC is active.
		 */
		//if ( boolval( self::site_connected() ) || empty( boolval( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) ) {
		if ( ! current_user_can( 'manage_options' ) || empty( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) {
			return false;
		}

		/*
		We use the Utilities::$value_provider array variable.
		This variable can hold values that can be used from different classes which should help avoid checking
		same conditions multiple times.
		In this case we are using `boot_admin_local_page` key which is also used in
		WPMUDEV_BLC\App\Admin_Notices\Legacy\Controller class.
		*/
		if ( ! isset( Utilities::$value_provider['boot_admin_local_page'] ) ) {
			Utilities::$value_provider['boot_admin_local_page'] = Utilities::is_admin_screen( $this->admin_pages() );
		}

		$legacy_option = Settings::instance()->get( 'use_legacy_blc_version' );

		return Utilities::$value_provider['boot_admin_local_page'] && ! empty( $legacy_option );
	}

	protected function admin_pages() {
		return array(
			'blc_local',
		);
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 2.1
	 * @return void
	 */
	public function prepare_props() {

	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 2.1
	 * @return array Register scripts for the admin page.
	 */
	public function set_admin_scripts() {
		$script_data  = include WPMUDEV_BLC_DIR . 'assets/dist/local.asset.php';
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
			'blc_local_modal' => array(
				'src'       => $this->scripts_dir . 'local-topnav.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_local_modal' => array(
						'data'   => array(
							'rest_url'           => esc_url_raw( rest_url() ),
							'settings_endpoint'  => '/wpmudev_blc/v1/settings',
							'unique_id'          => self::$unique_id,
							'nonce'              => wp_create_nonce( 'wp_rest' ),
							'legacy_active'      => boolval( Settings::instance()->get( 'use_legacy_blc_version' ) ),
							'local_blc_url'      => admin_url( 'admin.php?page=blc_local' ),
							'cloud_blc_url'      => admin_url( 'admin.php?page=blc_dash' ),
							'assets_url'         => WPMUDEV_BLC_ASSETS_URL,
							'site_connected'     => (bool) self::site_connected(),
							'expired_membership' => boolval( Utilities::membership_expired() ),
							'hub_signup_url'     => $signup_url,
							'dash_installed'     => boolval( Utilities::dash_plugin_installed() ),
							'dash_active'        => boolval( Utilities::dash_plugin_active() ),
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

	/**
	 * Register css files for admin page.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_sui'          => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => $this->scripts_version(),
			),
			'blc_legacy_modal' => array(
				//'src' => $this->styles_dir . 'local-modal.min.css',
				'src' => $this->scripts_dir . 'style-local-topnav.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/dist/local-topnav.asset.php';
			$scripts_version = $script_data['version'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 2.1
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		View::instance()->render( array( 'unique_id' => self::$unique_id ) );
	}

	/**
	 * Adds the blc legacy class to body classes for styling purposes.
	 *
	 * @param string $classes The body classes.
	 *
	 * @since 2.1
	 * @return string Returns the body classes.
	 */
	public function admin_body_classes( string $classes = '' ) {
		return $classes . ' blc-show-local-modal';
	}
}
