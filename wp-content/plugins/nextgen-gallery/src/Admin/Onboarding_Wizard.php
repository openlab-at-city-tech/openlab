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
				printf( esc_html__( '%1$s &rsaquo; Onboarding Wizard', 'nextgen-gallery-gallery' ), esc_html( 'NextGEN Gallery' ) );
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
				<img width="339" src="<?php echo esc_url( trailingslashit( NGG_PLUGIN_URI ) . 'assets/images/logo.svg' ); ?>" alt="nextgen-gallery Gallery" class="nextgen-gallery-onboarding-wizard-logo" style="width:339px;">
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
					<a href="<?php echo esc_url( admin_url( '/admin.php?page=ngg_other_options' ) ); ?>"><?php esc_html_e( 'Close and Exit Wizard Without Saving', 'nextgen-gallery-gallery' ); ?></a>
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

			$stats_sent     = $onboarding_data['usage_stats_init'] ?? false;
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
	 * @since 3.59.4
	 *
	 * @return string
	 */
	public function get_license_type() {

		if ( defined( 'NGG_PRO_PLUGIN_BASENAME' ) ) {
			return 'pro';
		} elseif ( defined( 'NGG_PLUS_PLUGIN_BASENAME' ) ) {
			return 'plus';
		} elseif ( defined( 'NGG_STARTER_PLUGIN_BASENAME' ) ) {
			return 'starter';
		}

		return 'lite';
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
	 * @param string $download_url The download URL.
	 *
	 * @return void
	 */
	public function install_helper( string $download_url ) {

		if ( empty( $download_url ) ) {
			return;
		}

		global $hook_suffix;

		// Set the current screen to avoid undefined notices.
		set_current_screen();

		$method = '';
		$url    = esc_url( admin_url( 'index.php?page=nextgen-gallery-setup-wizard' ) );

		// Start output buffering to catch the filesystem form if credentials are needed.
		ob_start();
		$creds = request_filesystem_credentials( $url, $method, false, false, null );
		if ( false === $creds ) {
			$form = ob_get_clean();
			echo wp_json_encode( [ 'form' => $form ] );
			die;
		}

		// If we are not authenticated, make it happen now.
		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();
			echo wp_json_encode( [ 'form' => $form ] );
			die;
		}

		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		// Create the plugin upgrader with our custom skin.
		$skin      = new \Imagely\NGG\Util\Installer_Skin();
		$installer = new \Plugin_Upgrader( $skin );
		$status    = $installer->install( $download_url );
		if ( is_wp_error( $status ) ) {
			wp_send_json_error( $status->get_error_message() );
		}

		// Flush the cache and return.
		wp_cache_flush();
	}

	/**
	 * Verify the license key.
	 *
	 * @since 3.59.4
	 *
	 * @return void
	 *
	 * Copy of maybe_verify_key in License class.
	 * Modified to return wp_send_json_success and wp_send_json_error.
	 */
	public function ngg_plugin_verify_license_key() {
		if (
			! isset( $_POST['nextgen-gallery-license-key'], $_POST['nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nextgen-galleryOnboardingCheck' )
		) {
			wp_send_json_error( 'Invalid Request', \WP_Http::FORBIDDEN );
			wp_die();
		}

		$url = 'https://members.photocrati.com/wp-json/licensing/v1/register_site';

		$license_key = isset( $_POST['nextgen-gallery-license-key'] ) ? sanitize_text_field( wp_unslash( $_POST['nextgen-gallery-license-key'] ) ) : null;

		if ( empty( $license_key ) ) {
			wp_send_json_error( 'License key is required' );
			wp_die();
		}

		$query_args = [
			'license_key' => $license_key,
			'site_url'    => site_url(),
		];

		$args = [
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'body'        => $query_args,
			'user-agent'  => 'ImagelyUpdates/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,

		];

		$response = wp_safe_remote_post( $url, $args );
 		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			wp_send_json_error( $error_message );
			wp_die();
		} else {
			$http_code = wp_remote_retrieve_response_code( $response );
 			$result    = json_decode( wp_remote_retrieve_body( $response ) );
			// check if the response has error and bail out.
 			// check if the response has error and bail out.
			if ( isset( $result->error ) && '' !== $result->error ) {
				wp_send_json_error( $this->get_error_message( $result->error ) );
				wp_die();
			}

			$valid = in_array( $result->status ?? '', ['active', 'inactive','disabled'], true ) ?? false;

			// Check if status is active/inactive not expired or disabled.
			if ( 200 === $http_code && $valid ) {

				$product = $result->level ?? false;

				if ( ! $product ) {
					wp_send_json_error( 'Product not found.' );
					wp_die();
				}

				// Check if the product is already installed.
				$current_level = $this->get_license_type();
				if ( $current_level === $product ) {
					// If the product is already installed, return success.
					wp_send_json_success( 'Congratulations! This site is now receiving automatic updates.' );
					wp_die();
				}

				// Check if limit is reached.
				if ( '' === $result->is_at_limit ) {
					wp_send_json_error( 'Sorry, you have reached the limit of sites for this license key.' );
					wp_die();
				}

				$url = $this->download_pro( $license_key, $product );

				if ( isset( $url ) ) {
					$this->install_helper( $url );
				}

				wp_send_json_success( 'Congratulations! This site is now receiving automatic updates.' );
				wp_die();
			} else {

				// if license is expired, throw error.
				if ( 'expired' === $result->status ) {
					wp_send_json_error( $this->get_error_message( 'license_expired' ) );
					wp_die();
				}
				// if license is invalid, throw error.
				wp_send_json_error( $this->get_error_message( null ) );
				wp_die();
			}
		}
	}

	/**
	 * Download the pro version of the plugin.
	 *
	 * @param string $key The license key.
	 * @param string $product The product name.
	 *
	 * @return boolean|string
	 */
	public function download_pro( string $key, string $product ) {

		// Check if the product already exist in the installed plugins.
		if( 'no-clicks disabled' === $this->is_recommended_plugin_installed( 'nextgen-gallery-' . $product ) ){
			return false;
		}

		$url      = 'https://members.photocrati.com/wp-json/licensing/v1/get_update?product=nextgen-gallery-' . $product . '&license_key=' . $key . '&site_url=' . site_url();
		$args     = [
			'method'      => 'GET',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'ImagelyUpdates/' . NGG_PLUGIN_VERSION . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
		];
		$response = wp_safe_remote_get( $url, $args );
		if ( ! is_wp_error( $response ) ) {
			$http_code = wp_remote_retrieve_response_code( $response );
			$body      = wp_remote_retrieve_body( $response );
			if ( 200 === $http_code ) {
				return json_decode( $body )->download_url ?? '';
			} else {
				return false;
			}
		}
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
	 * @since 3.59.4
	 *
	 * @param string|null $code The error message.
	 *
	 * @return string
	 */
	public function get_error_message( ?string $code ): string {

		if ( ! isset( $code ) ) {
			return 'Something went wrong, please try again later.';
		}

 		$message = '';
		switch ( $code ) {
			case 'empty_site_url':
				$message = __( 'The site URL is missing. Please provide a valid URL.', 'nextgen-gallery' );
				break;
			case 'license_not_found':
				$message = __( 'The license key was not found. Please verify and try again.', 'nextgen-gallery' );
				break;
			case 'license_status_expired':
			case 'license_expired':
				$message = __( 'The license key has expired. Please renew your license.', 'nextgen-gallery' );
				break;
			case 'license_status_disabled':
			case 'license_disabled':
				$message = __( 'The license key has not been activated yet. Please contact support.', 'nextgen-gallery' );
				break;
			case 'license_status_revoked':
			case 'license_revoked':
				$message = __( 'The license key has been revoked. Please contact support.', 'nextgen-gallery' );
				break;
			case 'license_limit_installations':
				$message = __( 'The license key has reached the maximum number of installations.', 'nextgen-gallery' );
				break;
			default:
				$message = __( 'An unknown error occurred. Please try again.', 'nextgen-gallery' );
				break;

		}

		return $message;
	}
}
