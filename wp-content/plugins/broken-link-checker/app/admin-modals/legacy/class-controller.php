<?php
/**
 * BLC admin modal for legacy screens. This is a modal that appears when Cloud is active and contains options to switch to local.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Legacy
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Modals\Legacy;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Enqueue;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Dashboard
 */
class Controller extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 2.0.0
	 */
	use Enqueue;

	/**
	 * The unique id that can be used by react.
	 *
	 * @var int $unique_id
	 *
	 * @since 2.0.0
	 */
	public $unique_id = null;

	/**
	 * The admin pages the notice will be visible at.
	 *
	 * @var array $admin_pages
	 *
	 * @since 2.0.0
	 */
	protected $admin_pages = array();

	/**
	 * Init function.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'current_screen', array( $this, 'boot' ), 11 );
	}

	/**
	 * Boots modal parts.
	 */
	public function boot() {
		$this->prepare_props();
		
		if ( $this->can_boot() ) {
			add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );
			add_action( 'admin_footer', array( $this, 'output' ) );

			$this->unique_id = Utilities::get_unique_id();
			$this->prepare_scripts();
		}
	}

	/**
	 * Checks if admin page actions/scripts should load in current screen.
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueuing scripts.
	 * @since 2.0.0
	 */
	public function can_boot() {
		/*
		We use the Utilities::$value_provider array variable.
		This variable can hold values that can be used from different classes which should help avoid checking
		same conditions multiple times.
		In this case we are using `boot_admin_legacy_pages` key which is also used in
		WPMUDEV_BLC\App\Admin_Notices\Legacy\Controller class.
		*/
		if ( ! isset( Utilities::$value_provider['boot_admin_legacy_pages'] ) ) {
			Utilities::$value_provider['boot_admin_legacy_pages'] = Utilities::is_admin_screen( $this->admin_pages );
		}

		$legacy_option = Settings::instance()->get( 'use_legacy_blc_version' );

		return Utilities::$value_provider['boot_admin_legacy_pages'] && empty( $legacy_option );
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function prepare_props() {
		/*
		 * Set the admin pages the notice will be visible at.
		 */
		$this->admin_pages = array(
			'view-broken-links',
			'link-checker-settings',
			'blc_local',
		);

		// We should not show the Local Modal on subsites.
		if ( is_multisite() && ! is_main_site() ) {
			unset( $this->admin_pages[ array_search( 'blc_local', $this->admin_pages ) ] );
		}
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @return void The callback function of the Admin Menu Page.
	 * @since 2.0.0
	 */
	public function output() {
		if ( ! $this->can_boot() ) {
			return;
		}
		
		View::instance()->render( array( 'unique_id' => $this->unique_id ) );
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
				'src' => $this->scripts_dir . 'style-local.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/dist/local.asset.php';
			$scripts_version = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	/**
	 * Adds the blc legacy class to body classes for styling purposes.
	 *
	 * @param string $classes The body classes.
	 * @return string Returns the body classes.
	 * @since 2.0.0
	 */
	public function admin_body_classes( string $classes = '' ) {
		return $classes . ' blc-show-legacy-popup';
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @return array Register scripts for the admin page.
	 * @since 2.0.0
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
		$version      = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;

		return array(
			'blc_legacy_popup' => array(
				'src'       => $this->scripts_dir . 'local.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_legacy_popup' => array(
						'data'   => array(
							'rest_url'          => esc_url_raw( rest_url() ),
							'settings_endpoint' => '/wpmudev_blc/v1/settings',
							'unique_id'         => $this->unique_id,
							'nonce'             => wp_create_nonce( 'wp_rest' ),
							'site_connected'    => false,
							'show_legacy_link'  => boolval( Settings::instance()->get( 'use_legacy_blc_version' ) ),
							'legacy_blc_url'    => admin_url( 'admin.php?page=view-broken-links' ),
							'blc_url'           => admin_url( 'admin.php?page=blc_dash' ),
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

}
