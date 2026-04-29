<?php
/**
 * Onboarding class.
 *
 * @since 3.59.4
 *
 * @package NextGEN Gallery
 *
 * @author  Imagely
 */

namespace Imagely\NGG\Admin;

// Exit if accessed directly.

use Braintree\Http;
use Imagely\NGG\Util\Installer_Skin;
use Imagely\NGG\Util\UsageTracking;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that holds our setup wizard.
 *
 * @since 3.59.4
 */
class Onboarding_Wizard {

	/**
	 * Holds base singleton.
	 *
	 * @since 3.59.4
	 *
	 * @var object
	 */
	public $base = null;

	/**
	 * Class constructor.
	 *
	 * @since 3.59.4
	 */
	public function __construct() {
		if ( ! is_admin() || wp_doing_cron() || wp_doing_ajax() ) {
			return;
		}

		// Load the base class object.
	}

	/**
	 * Setup our hooks.
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'add_dashboard_page' ] );
		add_action( 'admin_head', [ $this, 'hide_dashboard_page_from_menu' ] );
		add_action( 'admin_init', [ $this, 'maybeload_onboarding_wizard' ] );
		// Ajax actions.
		add_action( 'wp_ajax_save_onboarding_data', [ $this, 'save_onboarding_data' ], 10, 1 );
		add_action( 'wp_ajax_install_recommended_plugins', [ $this, 'install_recommended_plugins' ], 10, 1 );
		add_action( 'wp_ajax_save_selected_addons', [ $this, 'save_selected_addons' ], 10, 1 );

		add_action( 'wp_ajax_ngg_plugin_verify_license_key', [ $this, 'ngg_plugin_verify_license_key' ], 10, 1 );
	}

	/**
	 * Adds a dashboard page for our setup wizard.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	public function add_dashboard_page() {
		add_dashboard_page( '', '', 'manage_options', 'nextgen-gallery-setup-wizard', '' );
	}

	/**
	 * Hide the dashboard page from the menu.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	public function hide_dashboard_page_from_menu() {
		remove_submenu_page( 'index.php', 'nextgen-gallery-setup-wizard' );
	}

	/**
	 * Checks to see if we should load the setup wizard.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	public function maybeload_onboarding_wizard() {
		// Don't load the interface if doing an ajax call.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// Check for wizard-specific parameter
		// Allow plugins to disable the setup wizard
		// Check if current user is allowed to save settings.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Recommended
		if (
			! isset( $_GET['page'] )
			|| 'nextgen-gallery-setup-wizard' !== sanitize_text_field( wp_unslash( $_GET['page'] ) )
			|| ! current_user_can( 'manage_options' )
		) {
			return;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Recommended

		set_current_screen();

		// Remove an action in the Gutenberg plugin ( not core Gutenberg ) which throws an error.
		remove_action( 'admin_print_styles', 'gutenberg_block_editor_admin_print_styles' );

		// If we are redirecting, clear the transient so it only happens once.

		$this->load_onboarding_wizard();
	}

	/**
	 * Load the Onboarding Wizard template.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	private function load_onboarding_wizard() {
		$this->enqueue_scripts();
		$this->onboarding_wizard_header();
		$this->onboarding_wizard_content();
		$this->onboarding_wizard_footer();
		exit;
	}

	/**
	 * Enqueue scripts for the setup wizard.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	private function enqueue_scripts() {
		// We don't want any plugin adding notices to our screens. Let's clear them out here.
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		$router = \Imagely\NGG\Util\Router::get_instance();

		// TODO: Add minified js file and check nonces.
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_register_script(
			'nextgen-gallery-onboarding-wizard',
			plugins_url( 'assets/js/dist/onboarding-wizard.js', NGG_PLUGIN_FILE ),
			[ 'jquery' ],
			NGG_PLUGIN_VERSION,
			true
		);
		wp_localize_script(
			'nextgen-gallery-onboarding-wizard',
			'nggOnboardingWizard',
			[
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'nextgen-galleryOnboardingCheck' ),
				'connect_nonce' => wp_create_nonce( 'nextgen-gallery-key-nonce' ),
				'plugins_list'  => $this->get_installed_plugins(),
			]
		);
		wp_register_style(
			'nextgen-gallery-onboarding-wizard',
			plugins_url( 'assets/css/onboarding-wizard.css', NGG_PLUGIN_FILE ),
			[],
			NGG_PLUGIN_VERSION
		);
		wp_enqueue_style( 'nextgen-gallery-onboarding-wizard' );
		wp_enqueue_style( 'common' );
		wp_enqueue_media();
	}

	/**
	 * Setup the wizard header.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	private function onboarding_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> dir="ltr">
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>
				<?php
				// translators: %s is the plugin name.
				printf( esc_html__( '%1$s &rsaquo; Onboarding Wizard', 'nggallery' ), esc_html( 'Imagely' ) );
				?>
			</title>
		</head>
		<body class="" style="visibility: hidden;">
		<div class="nextgen-gallery-onboarding-wizard">

		<?php
	}

	/**
	 * Outputs the content of the current step.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	public function onboarding_wizard_content() {
		?>
		<div class="nextgen-gallery-onboarding-wizard-wrapper ">
			<div class="nextgen-gallery-onboarding-wizard-intro " id="welcome">
				<?php
				// Admin page controller render_partial - welcome template.
				include_once NGG_PLUGIN_DIR . 'src/Admin/Views/onboarding-wizard/welcome.php';
				?>
			</div>
			<div class="nextgen-gallery-onboarding-wizard-pages" style="display: none">
				<!-- logo -->
				<img width="339" src="<?php echo esc_url( trailingslashit( NGG_PLUGIN_URI ) . 'assets/images/logo.png' ); ?>" alt="nextgen-gallery Gallery" class="nextgen-gallery-onboarding-wizard-logo" style="width:339px;">
				<!-- Progress Bar  -->
				<div class="nextgen-gallery-onboarding-progressbar">
					<div class="nextgen-gallery-onboarding-progress" id="nextgen-gallery-onboarding-progress"></div>
					<div class="nextgen-gallery-onboarding-progress-step nextgen-gallery-onboarding-progress-step-active"></div>
					<div class="nextgen-gallery-onboarding-spacer"></div>
					<div class="nextgen-gallery-onboarding-progress-step" ></div>
					<div class="nextgen-gallery-onboarding-spacer"></div>
					<div class="nextgen-gallery-onboarding-progress-step" ></div>
					<div class="nextgen-gallery-onboarding-spacer"></div>
					<div class="nextgen-gallery-onboarding-progress-step" ></div>
					<div class="nextgen-gallery-onboarding-spacer"></div>
					<div class="nextgen-gallery-onboarding-progress-step" ></div>
				</div>
				<?php
				// Load template partials for each step based on URL hash.
				for ( $i = 1; $i <= 5; $i++ ) {
					$step = 'step-' . $i;
					include_once NGG_PLUGIN_DIR . 'src/Admin/Views/onboarding-wizard/' . $step . '.php';
				}
				?>
				<div class="nextgen-gallery-onboarding-close-and-exit">
					<a href="<?php echo esc_url( admin_url( '/admin.php?page=imagely&tab=general' ) ); ?>"><?php esc_html_e( 'Close and Exit Wizard Without Saving', 'nextgen-gallery-gallery' ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Outputs the simplified footer used for the Onboarding Wizard.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 */
	public function onboarding_wizard_footer() {
		?>
		<?php

		wp_print_scripts( 'nextgen-gallery-onboarding-wizard' );
		do_action( 'admin_footer', '' );
		do_action( 'admin_print_footer_scripts' );
		?>
		</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Get a list of recommended plugins on step 3.
	 *
	 * @return array
	 */
	public function get_recommended_plugins(): array {
		$plugins = [
			'all-in-one-seo-pack'            => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'wpforms-lite'                   => 'wpforms-lite/wpforms.php',
			'google-analytics-for-wordpress' => 'google-analytics-for-wordpress/googleanalytics.php',
			'duplicator'                     => 'duplicator/duplicator.php',
			'wp-mail-smtp'                   => 'wp-mail-smtp/wp_mail_smtp.php',
		];

		return $plugins;
	}

	/**
	 * Check if a recommended plugin is installed.
	 *
	 * @param string $recommended The plugin slug.
	 *
	 * @return string
	 */
	public function is_recommended_plugin_installed( string $recommended ): string {
		// Check if these plugins are installed already or not.
		$all_plugins = get_plugins();
		$plugins     = $this->get_recommended_plugins();
		$plugin      = '';
		$plus        = 'nextgen-gallery-plus/ngg-plus.php';
		$pro         = 'nextgen-gallery-pro/nggallery-pro.php';
		$starter     = 'nextgen-gallery-pro/ngg-starter.php';
		// Switch case to check pro, plus and starter plugins.
		switch ( $recommended ) {
			// if $recommended contains plus, then set $plugin to plus.
			case strpos( $recommended, '-plus' ) !== false:
				$plugin = $plus;
				break;
			// if $recommended contains pro, then set $plugin to pro.
			case strpos( $recommended, 'gallery-pro' ) !== false:
				$plugin = $pro;
				break;
			// if $recommended contains starter, then set $plugin to starter.
			case strpos( $recommended, '-starter' ) !== false:
				$plugin = $starter;
				break;
		}

		// Check if $recommended is a NextGEN Gallery plugin.
		if ( array_key_exists( $recommended, $plugins ) && '' === $plugin ) {
			// check if key exists in the array.
			$plugin = $plugins[ $recommended ];
		}

		if ( in_array( $plugin, array_keys( $all_plugins ), true ) ) {
			return 'no-clicks disabled';
		}

		return '';
	}

	/**
	 * Get saved onboarding data.
	 *
	 * @param string $key The key to get the data.
	 *
	 * @return mixed
	 */
	public function get_onboarding_data( string $key ) {
		if ( ! empty( $key ) ) {
			$onboarding_data = get_option( 'ngg_onboarding_data' );
			if ( ! empty( $onboarding_data ) && isset( $onboarding_data[ $key ] ) ) {
				return $onboarding_data[ $key ];
			}
		}

		return '';
	}

	/**
	 * Save the onboarding data.
	 *
	 * @return void
	 */
	public function save_onboarding_data() {

		// check for nonce nextgen-galleryOnboardingCheck.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nextgen-galleryOnboardingCheck' ) ) {
			wp_send_json_error( 'Invalid nonce' );
			wp_die();
		}

		// check if the current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have permission to save data' );
			wp_die();
		}

		if ( ! empty( $_POST['eow'] ) ) {
			// Sanitize data and merge to existing data.
			$onboarding_data = get_option( 'ngg_onboarding_data', [] );

			$onboarding_data = $this->sanitize_and_assign( '_usage_tracking', 'sanitize_text_field', $onboarding_data );
			$onboarding_data = $this->sanitize_and_assign( '_email_address', 'sanitize_email', $onboarding_data );
			$onboarding_data = $this->sanitize_and_assign( '_email_opt_in', 'sanitize_text_field', $onboarding_data );
			$onboarding_data = $this->sanitize_and_assign( '_user_type', 'sanitize_text_field', $onboarding_data );
			$onboarding_data = $this->sanitize_and_assign( '_others', 'sanitize_text_field', $onboarding_data );

			$stats_sent     = isset( $onboarding_data['usage_stats_init'] ) ? $onboarding_data['usage_stats_init'] : false;
			$usage_tracking = filter_var( $onboarding_data['_usage_tracking'], FILTER_VALIDATE_BOOLEAN );

			if ( $usage_tracking && ! $stats_sent ) {
				// Send usage tracking on onboarding settings save.
				( new UsageTracking() )->send_checkin( true );
				$onboarding_data['usage_stats_init'] = true;
			}

			update_option( 'ngg_onboarding_data', $onboarding_data );

			// Send data to Drip.
			$this->save_to_drip( $onboarding_data );

			wp_send_json_success( 'Data saved successfully' );
			wp_die();
		}

		wp_send_json_error( 'Something went wrong. Please try again.' );
		wp_die();
	}

	/**
	 * Sanitize and assign the data.
	 *
	 * @param string $key The key to get the data.
	 * @param string $sanitize_function The sanitize function.
	 * @param array  $onboarding_data The onboarding data.
	 *
	 * @return array
	 */
	public function sanitize_and_assign( string $key, string $sanitize_function, array $onboarding_data ): array {
		if ( ! function_exists( $sanitize_function ) ) {
			_doing_it_wrong( __METHOD__, 'Invalid sanitize function', '3.59.4' );

			return $onboarding_data;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST['eow'][ $key ] ) ) { // Nonce is verified in the parent function.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$onboarding_data[ $key ] = $sanitize_function( wp_unslash( $_POST['eow'][ $key ] ) );
		} else {
			unset( $onboarding_data[ $key ] );
		}

		return $onboarding_data;
	}

	/**
	 * Save the onboarding data to Drip.
	 *
	 * @param array $onboarding_data The onboarding data.
	 *
	 * @return void
	 */
	public function save_to_drip( array $onboarding_data ) {

		$url = 'https://imagely.com/wp-json/imagely/v1/get_opt_in_data';

		$email = sanitize_email( $onboarding_data['_email_address'] );

		if ( empty( $email ) ) {
			return;
		}

		$tags     = [];
		$position = '';

		$tags[] = 'im-' . $this->get_license_type();

		if ( isset( $onboarding_data['_user_type'] ) ) {
			$position = $onboarding_data['_user_type'];
		}

		$body_data = [
			'imagely-drip-email'    => base64_encode( $email ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			'imagely-drip-tags'     => $tags,
			'imagely-drip-position' => $position,
		];

		$body = wp_json_encode( $body_data );

		$args = [
			'method'      => 'POST',
			'headers'     => [
				'Content-Type' => 'application/json',
				'user-agent'   => 'ENVIRA/IMAGELY/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' ),
			],
			'body'        => $body,
			'timeout'     => '5', // Timeout in seconds.
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'data_format' => 'body',
		];

		$response = wp_safe_remote_post( $url, $args );
	}

	/**
	 * Save selected addons to database.
	 */
	public function save_selected_addons() {
		// check for nonce nextgen-galleryOnboardingCheck.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nextgen-galleryOnboardingCheck' ) ) {
			wp_send_json_error( 'Invalid nonce' );
			wp_die();
		}

		// check if the current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have permission to save data' );
			wp_die();
		}

		if ( ! empty( $_POST['addons'] ) ) {

			$addons = explode( ',', sanitize_text_field( wp_unslash( $_POST['addons'] ) ) );

			// Sanitize data and merge to existing data.
			$onboarding_data = get_option( 'ngg_onboarding_data' );
			if ( empty( $onboarding_data ) ) {
				$onboarding_data = [];
			}

			// Save addons as _addons key.
			$onboarding_data['_addons'] = $addons;

			$updated = update_option( 'ngg_onboarding_data', $onboarding_data );

			wp_send_json_success( 'Addons saved successfully' );
			wp_die();
		}

		wp_send_json_error( 'Something went wrong. Please try again.' );
		wp_die();
	}

	/**
	 * Get the license type for the current plugin.
	 *
	 * @deprecated Use \Imagely\NGG\Util\LicenseHelper::get_license_type() instead
	 * @since 3.59.4
	 *
	 * @return string
	 */
	public function get_license_type() {
		return \Imagely\NGG\Util\LicenseHelper::get_license_type();
	}

	/**
	 * Install the recommended plugins and add-ons.
	 *
	 * @return void
	 */
	public function install_recommended_plugins() {
		// check for nonce nextgen-galleryOnboardingCheck.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nextgen-galleryOnboardingCheck' ) ) {
			wp_send_json_error( 'Invalid nonce' );
			wp_die();
		}

		// check if the current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have permission to install plugins' );
			wp_die();
		}

		if ( ! empty( $_POST['plugins'] ) ) {
			// Sanitize data, plugins is a string delimited by comma.

			$plugins = explode( ',', sanitize_text_field( wp_unslash( $_POST['plugins'] ) ) );
			// Install the plugins.
			foreach ( $plugins as $plugin ) {
				if ( '' !== $this->is_recommended_plugin_installed( $plugin ) ) {
					continue; // Skip the plugin if it is already installed.
				}
				// Generate the plugin URL by slug.
				$url = 'https://downloads.wordpress.org/plugin/' . $plugin . '.zip';
				$this->install_helper( $url );

			}
		}
		wp_send_json_success( 'Installed the recommended plugins successfully.' );
		wp_die();
	}

	/**
	 * Helper function to install the free plugins.
	 *
	 * @deprecated Use \Imagely\NGG\Util\LicenseHelper::install_plugin() instead
	 * @param string $download_url The download URL.
	 *
	 * @return void
	 */
	public function install_helper( string $download_url ) {
		if ( empty( $download_url ) ) {
			return;
		}

		// Install without activation (silent mode, no activation)
		// Free plugins are not auto-activated to let users configure them first
		$result = \Imagely\NGG\Util\LicenseHelper::install_plugin( $download_url, false, false );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
			die;
		}
	}

	/**
	 * Verify the license key.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 *
	 * Uses shared LicenseHelper utility for license verification and installation.
	 */
	public function ngg_plugin_verify_license_key() {
		if (
			! isset( $_POST['nextgen-gallery-license-key'], $_POST['nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nextgen-galleryOnboardingCheck' )
		) {
			wp_send_json_error( 'Invalid Request', \WP_Http::FORBIDDEN );
			wp_die();
		}

		$license_key = isset( $_POST['nextgen-gallery-license-key'] ) ? sanitize_text_field( wp_unslash( $_POST['nextgen-gallery-license-key'] ) ) : null;

		if ( empty( $license_key ) ) {
			wp_send_json_error( 'License key is required' );
			wp_die();
		}

		// Verify license with external server using shared helper
		$verification_result = \Imagely\NGG\Util\LicenseHelper::verify_license_with_server( $license_key );

		if ( is_wp_error( $verification_result ) ) {
			wp_send_json_error( $verification_result->get_error_message() );
			wp_die();
		}

		$product = $verification_result['level'];

		// Check if the product is already active
		$current_level = \Imagely\NGG\Util\LicenseHelper::get_license_type();
		if ( $current_level === $product ) {
			wp_send_json_success( 'Congratulations! This site is now receiving automatic updates.' );
			wp_die();
		}

		// Check if the product is installed but not activated
		$plugin_basenames = [
			'pro'     => 'nextgen-gallery-pro/nggallery-pro.php',
			'plus'    => 'nextgen-gallery-plus/nggallery-plus.php',
			'starter' => 'nextgen-gallery-starter/nggallery-starter.php',
		];

		if ( \Imagely\NGG\Util\LicenseHelper::is_product_installed( $product ) ) {
			// Plugin is installed but not active - just activate it
			$plugin_basename = $plugin_basenames[ $product ];
			$activate        = activate_plugin( $plugin_basename, false, false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error( $activate->get_error_message() );
				wp_die();
			}

			wp_send_json_success( 'Congratulations! This site is now receiving automatic updates.' );
			wp_die();
		}

		// Get download URL using shared helper
		$download_url = \Imagely\NGG\Util\LicenseHelper::get_download_url( $license_key, $product );

		if ( is_wp_error( $download_url ) ) {
			wp_send_json_error( $download_url->get_error_message() );
			wp_die();
		}

		// Install and activate Pro plugin (non-silent mode for AJAX, with activation)
		// Pro plugins should be activated immediately since user has valid license
		$install_result = \Imagely\NGG\Util\LicenseHelper::install_plugin( $download_url, false, true );

		if ( is_wp_error( $install_result ) ) {
			wp_send_json_error( $install_result->get_error_message() );
			wp_die();
		}

		wp_send_json_success( 'Congratulations! This site is now receiving automatic updates.' );
		wp_die();
	}

	/**
	 * Download the pro version of the plugin.
	 *
	 * @deprecated Use \Imagely\NGG\Util\LicenseHelper::get_download_url() instead
	 * @param string $key The license key.
	 * @param string $product The product name.
	 *
	 * @return boolean|string
	 */
	public function download_pro( string $key, string $product ) {
		// Check if the product already exist in the installed plugins.
		if ( 'no-clicks disabled' === $this->is_recommended_plugin_installed( 'nextgen-gallery-' . $product ) ) {
			return false;
		}

		$result = \Imagely\NGG\Util\LicenseHelper::get_download_url( $key, $product );
		return is_wp_error( $result ) ? false : $result;
	}

	/**
	 * Get a list of installed recommended plugins and addons.
	 *
	 * @return array
	 */
	public function get_installed_plugins(): array {

		$plugins = $this->get_recommended_plugins();

		// Check if these plugins are installed already or not.
		$all_plugins = get_plugins();
		$installed   = [];

		foreach ( $plugins as $plugin ) {
			if ( in_array( $plugin, array_keys( $all_plugins ), true ) ) {
				// Get array key of $plugins.
				$installed[] = array_search( $plugin, $plugins, true );
			}
		}

		return $installed;
	}

	/**
	 * Get error messages.
	 *
	 * @deprecated Use \Imagely\NGG\Util\LicenseHelper::get_error_message() instead
	 * @since 3.59.4
	 *
	 * @param string|null $code The error message.
	 *
	 * @return string
	 */
	public function get_error_message( $code ) {
		return \Imagely\NGG\Util\LicenseHelper::get_error_message( $code );
	}
}
