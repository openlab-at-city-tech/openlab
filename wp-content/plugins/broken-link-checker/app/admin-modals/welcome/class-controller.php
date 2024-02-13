<?php
/**
 * BLC welcome modal.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notices\Welcome
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Modals\Welcome;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Enqueue;
/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Welcome
 */
class Controller extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 2.2.0
	 */
	use Enqueue;

	/**
	 * The unique id that can be used by react.
	 *
	 * @var int $unique_id
	 *
	 * @since 2.2.0
	 */
	public $unique_id = null;

	/**
	 * The admin pages the notice will be visible at.
	 *
	 * @var array $admin_pages
	 *
	 * @since 2.2.0
	 */
	protected $admin_pages = array();

	/**
	 * Init function.
	 *
	 * @since 2.2.0
	 */
	public function init() {
		Settings::instance()->init();
		add_action( 'current_screen', array( $this, 'boot' ), 11 );
	}

	/**
	 * Boots modal parts.
	 */
	public function boot() {
		if ( $this->can_boot() ) {
			//add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );
			add_action( 'admin_footer', array( $this, 'output' ) );

			$this->unique_id = Utilities::get_unique_id();

			$this->prepare_props();
			$this->prepare_scripts();
		}
	}

	/**
	 * Checks if admin page actions/scripts should load in current screen.
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueuing scripts.
	 * @since 2.2.0
	 */
	public function can_boot() {
		$admin_pages = array(
			'toplevel_page_blc_dash',
			//'link-checker_page_blc_local',
		);

		$version_highlights = Settings::instance()->get( 'version_highlights' );
		$version_highlights_shown = ! empty( $version_highlights['2_2_0'] );

		return Utilities::is_admin_screen( $admin_pages ) && ! $version_highlights_shown;
	}

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @return void
	 * @since 2.2.0
	 */
	public function prepare_props() {
		/*
		 * Set the admin pages the notice will be visible at.
		 */
		/*$this->admin_pages = array(
			'blc_dash',
			'link-checker_page_blc_local',
		);*/
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @return void The callback function of the Admin Menu Page.
	 * @since 2.2.0
	 */
	public function output() {
		View::instance()->render( array( 'unique_id' => $this->unique_id ) );
	}

	/**
	 * Register scripts for the admin modal.
	 *
	 * @return array Register scripts for the admin modal.
	 * @since 2.2.0
	 */
	public function set_admin_scripts() {
		$script_data  = include WPMUDEV_BLC_DIR . 'assets/dist/welcome.asset.php';
		$dependencies = $script_data['dependencies'] ?? array(
			'react',
			'wp-element',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-polyfill',
		);
		$version      = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;

		return array(
			'blc_welcome_modal' => array(
				'src'       => $this->scripts_dir . 'welcome.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_welcome_modal' => array(
						'data'   => array(
							'rest_url'          => esc_url_raw( rest_url() ),
							'settings_endpoint' => '/wpmudev_blc/v1/settings',
							'unique_id'         => $this->unique_id,
							'nonce'             => wp_create_nonce( 'wp_rest' ),
						),
					),
				),
				'translate' => true,
			),
		);
	}

	/**
	 * Register css files for admin modal.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_sui'          => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => $this->scripts_version(),
			),
			'blc_welcome_modal' => array(
				'src' => $this->scripts_dir . 'style-welcome.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/dist/welcome.asset.php';
			$scripts_version = ! empty( $script_data['version'] ) ? WPMUDEV_BLC_SCIPTS_VERSION . '-' . $script_data['version'] : WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}
}
