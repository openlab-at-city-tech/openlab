<?php
/**
 * Controller for admin notices.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Enqueue;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Models\Option;
use WPMUDEV_BLC\Core\Activation;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation
 */
class Controller extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 2.0.0
	 */
	use Enqueue;

	/**
	 * @var null|array The plugin settings
	 */
	private $settings = null;

	/**
	 * Init Admin_Modal
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		// This modal is only for new features.
		return false;

		$this->settings = new Settings();

		add_action( 'load-plugins.php', array( $this, 'boot' ) );
	}

	/**
	 * Show the modal on plugins.php page if allowed.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function boot() {
		if ( ! $this->can_boot() ) {
			return;
		}

		add_action( 'admin_footer', array( $this, 'show_footer_output' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_classes' ), 999 );

		$this->unique_id = Utilities::get_unique_id();

		$this->prepare_scripts();
	}

	/**
	 * Check if we can show model output in footer.
	 * Called only on plugins.php page.
	 */
	public function show_footer_output() {
		$this->settings->set( wp_parse_args( $this->settings->get(), $this->settings->default ) );
		$this->settings->set( array( 'activation_modal_shown' => true ) );
		$this->settings->save();
		$this->output();
	}

	/**
	 * Checks if modal can load.
	 *
	 * @since 2.0.0
	 *
	 * @return boolean Checks if modal can load. Useful for enqueuing scripts.
	 */
	protected function can_boot() {
		return empty( $this->settings->get( 'activation_modal_shown' ) ) && ! Utilities::is_subsite();
	}

	/**
	 * Modal output.
	 *
	 * @since 1.0.0
	 *
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		View::instance()->render(
			array(
				'unique_id' => $this->unique_id,
			)
		);
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return array Register scripts for the admin page.
	 */
	public function set_admin_scripts() {
		$script_data       = include WPMUDEV_BLC_DIR . 'assets/js/activation-popup/main.asset.php';
		$dependencies      = $script_data['dependencies'] ?? array(
			'react',
			'wp-element',
			'wp-i18n',
			'wp-is-shallow-equal',
			'wp-polyfill',
		);
		$version           = $script_data['version'] ?? WPMUDEV_BLC_SCIPTS_VERSION;
		$legacy_option_key = 'wsblc_options';
		$legacy_option     = new Option( array( 'name' => $legacy_option_key ) );
		// Defines if the legacy link at the very bottom of the modal
		$show_legacy_link       = ! empty( $legacy_option->get() );
		$legacy_installation_dt = $legacy_option->get( 'first_installation_timestamp' );
		$installation_dt        = intval( $this->settings->get( 'installation_timestamp' ) );
		$legacy_pre_installed   = ( ! empty( $legacy_installation_dt ) && 1 < round( abs( $legacy_installation_dt - $installation_dt ) / 60 ) );

		return array(
			'blc_activation_popup' => array(
				'src'       => $this->scripts_dir . 'activation-popup/main.js',
				'deps'      => $dependencies,
				'ver'       => $version,
				'in_footer' => true,
				'localize'  => array(
					'blc_activation_popup' => array(
						'data'   => array(
							'rest_url'             => esc_url_raw( rest_url() ),
							'settings_endpoint'    => '/wpmudev_blc/v1/settings',
							'unique_id'            => $this->unique_id,
							'nonce'                => wp_create_nonce( 'wp_rest' ),
							'site_connected'       => false,
							'show_legacy_link'     => $show_legacy_link,
							'legacy_pre_installed' => $legacy_pre_installed,
							'legacy_blc_url'       => admin_url( 'admin.php?page=view-broken-links' ),
							'blc_url'              => admin_url( 'admin.php?page=blc_dash' ),
							'dash_api_active'      => ( Utilities::get_dashboard_api() instanceof \WPMUDEV_Dashboard_Api || Utilities::get_dashboard_api() instanceof WPMUDEV_Dashboard_Api ),
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
			// END OF blc_activation_popup
		);
	}

	/**
	 * Register css files for admin page.
	 *
	 * @return array
	 */
	public function set_admin_styles() {
		return array(
			'blc_sui'       => array(
				'src' => $this->styles_dir . 'shared-ui-' . BLC_SHARED_UI_VERSION_NUMBER . '.min.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			),
			'blc_dashboard' => array(
				'src' => $this->styles_dir . 'plugin-activation.min.css',
				'ver' => WPMUDEV_BLC_SCIPTS_VERSION,
			),
		);
	}

	/**
	 * Adds SUI admin body class. It will be used in all admin pages.
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function admin_body_classes( $classes ) {
		$sui_classes   = explode( ' ', $classes );
		$sui_classes[] = BLC_SHARED_UI_VERSION;

		if ( apply_filters( 'wpmudev_branding_hide_branding', false ) ) {
			$sui_classes[] = 'wpmudev-hide-branding';
		}

		return join( ' ', $sui_classes );
	}
}
