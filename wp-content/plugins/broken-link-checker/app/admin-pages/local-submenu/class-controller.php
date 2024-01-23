<?php
/**
 * BLC Local_Submenu admin page
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Local_Submenu
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Local_Submenu;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Controllers\Admin_Page;
use WPMUDEV_BLC\Core\Traits\Escape;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Local_Submenu
 */
class Controller extends Admin_Page {
	/**
	 * Use the Escape and Dashboard_API Traits.
	 *
	 * @since 2.1.0
	 */
	use Escape, Dashboard_API;

	/**
	 * The Admin Page's Menu Type.
	 *
	 * @since 2.1.0
	 * @var bool $is_submenu Set to true if page uses submenu.
	 *
	 */
	protected $is_submenu = true;

	/**
	 * Holds local blc object `wsBrokenLinkChecker`.
	 *
	 * @since 2.1.0
	 * @var object $local_blc
	 *
	 */
	protected $local_blc = null;

	/**
	 * Path to local (legacy) blc.
	 *
	 * @since 2.1.0
	 * @var object $local_blc_path
	 *
	 */
	protected $local_blc_path = null;

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function prepare_props() {
		$local_settings    = json_decode( get_option( 'wsblc_options' ) );
		$page_caps         = 'manage_options';
		$this->parent_slug = 'blc_dash';
		$this->is_submenu  = true;
		//$this->unique_id  = Utilities::get_unique_id();
		$this->page_title = __( 'Local Broken Link Checker', 'broken-link-checker' );
		$this->menu_title = __( 'Local [old]', 'broken-link-checker' );
		$this->menu_slug  = 'blc_local';
		$this->position   = 1;

		if ( Settings::instance()->get( 'use_legacy_blc_version' ) && ! empty( $local_settings->dashboard_widget_capability ) ) {
			if ( in_array( $local_settings->dashboard_widget_capability, array(
				'edit_others_posts',
				'manage_options'
			) ) ) {
				$page_caps = $local_settings->dashboard_widget_capability;
			}

			if ( 'do_not_allow' === $local_settings->dashboard_widget_capability ) {
				//$page_caps = 'unfiltered_html';
				$page_caps = 'administrator';
			}
		}

		$this->capability = $page_caps;

		add_action( 'admin_menu', array( $this, 'set_submenu_actions' ), 20 );
	}

	public function set_submenu_actions() {
		$local_blc = $this->get_local_blc();

		if ( $local_blc instanceof \wsBrokenLinkChecker ) {
			if ( $this->is_settings_tab() ) {
				add_action( 'admin_print_styles-' . $this->hook_suffix, array( $local_blc, 'options_page_css' ) );
				add_action( 'admin_print_scripts-' . $this->hook_suffix, array(
					$local_blc,
					'enqueue_settings_scripts'
				) );

				/*
				 * // We don't really need another link to Local links. We have the tabs fo this.
				// Make the Settings page link to the link list.
				add_screen_meta_link(
					'link-checker-settings',
					__( 'Go to Broken Links', 'broken-link-checker' ),
					admin_url( 'admin.php?page=blc_local' ),
					$this->hook_suffix,
					array( 'style' => 'font-weight: bold;' )
				);
				*/
			} else {
				add_action( 'admin_print_styles-' . $this->hook_suffix, array( $local_blc, 'links_page_css' ) );
				add_action( 'admin_print_scripts-' . $this->hook_suffix, array(
					$local_blc,
					'enqueue_link_page_scripts'
				) );
			}
		}
	}

	public function get_local_blc() {
		if ( ! $this->local_blc instanceof \wsBrokenLinkChecker ) {
			global $blc_config_manager;

			$ws_link_checker = null;

			if ( ! class_exists( 'wsBrokenLinkChecker' ) ) {
				require_once BLC_DIRECTORY_LEGACY . '/core/core.php';
			}

			if ( $blc_config_manager instanceof \blcConfigurationManager ) {
				$this->local_blc = new \wsBrokenLinkChecker( BLC_PLUGIN_FILE_LEGACY, $blc_config_manager );
			}
		}

		return $this->local_blc;
	}

	public function is_settings_tab() {
		return current_user_can( 'manage_options' ) && ! empty( $_GET['local-settings'] );
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 2.1.0
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		View::instance()->render(
			array(
				'hook_suffix'     => $this->hook_suffix,
				'local_blc'       => $this->get_local_blc(),
				'is_settings_tab' => $this->is_settings_tab(),
			)
		);
	}

	/**
	 * Register scripts for the blc local page.
	 *
	 * @since 2.1.0
	 * @return array Register scripts for the blc local page.
	 */


	/**
	 * Register css files for blc local page.
	 *
	 * @since 2.1.0
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_sui' => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => $this->scripts_version(),
			),

			'blc_dashboard' => array(
				'src' => $this->scripts_dir . 'style-local-nav.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	protected function scripts_version() {
		static $scripts_version = null;

		if ( is_null( $scripts_version ) ) {
			$script_data     = include WPMUDEV_BLC_DIR . 'assets/dist/local-nav.asset.php';
			$scripts_version = $script_data['version'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
		}

		return $scripts_version;
	}

	/**
	 * Returns the style for Local page.
	 * Useful on Multisites where we render Local page only (even when Cloud is active). We need style for the nave of Links and Settings page.
	 *
	 * @return array
	 */
	public function get_local_style_data() {
		return array(
			'blc_local_style' => array(
				'src' => $this->scripts_dir . 'style-local-nav.css',
				'ver' => $this->scripts_version(),
			),
		);
	}

	public function get_menu_slug() {
		return $this->menu_slug;
	}
}
