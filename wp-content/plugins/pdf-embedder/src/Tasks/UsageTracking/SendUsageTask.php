<?php

namespace PDFEmbedder\Tasks\UsageTracking;

use PDFEmbedder\Options;
use PDFEmbedder\Tasks\Task;
use PDFEmbedder\Helpers\Check;
use PDFEmbedder\Admin\License;
use PDFEmbedder\Helpers\Multisite;

/**
 * Class SendUsageTask.
 *
 * @since 4.7.0
 */
class SendUsageTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 4.7.0
	 */
	const ACTION = 'pdfemb_send_usage_data';

	/**
	 * Server URL to send requests to.
	 *
	 * @since 4.7.0
	 */
	const TRACK_URL = 'https://wpauthusagetracking.com/v1/pdf';

	/**
	 * Option name to store the timestamp of the last run.
	 *
	 * @since 4.7.0
	 */
	const LAST_RUN = 'pdfemb_send_usage_last_run';

	/**
	 * Class constructor.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task with all the proper checks.
	 *
	 * @since 4.7.0
	 */
	public function init() {

		// Register various listeners.
		$this->hooks();

		if ( ! $this->is_enabled() ) {
			return;
		}

		$tasks = pdf_embedder()->tasks();

		// Add a new one if it does not exist.
		if ( $tasks::is_scheduled( self::ACTION ) !== false ) {
			return;
		}

		$this->recurring( $this->generate_start_date(), WEEK_IN_SECONDS )->register();
	}

	/**
	 * Whether the task is enabled.
	 *
     * @since 4.7.0
	 */
	private function is_enabled(): bool {

		$options = pdf_embedder()->options()->get();

		/**
		 * Filter to enable/disable the usage tracking.
		 *
		 * @since 4.7.0
		 *
		 * @param bool $enabled Whether the usage tracking is enabled.
		 */
		return (bool) apply_filters(
			'pdfemb_usage_tracking_enabled',
			Options::is_on( $options['usagetracking'] )
		);
	}

	/**
	 * Add hooks.
	 *
	 * @since 4.7.0
	 */
	private function hooks() {

		add_action( self::ACTION, [ $this, 'process' ] );

		// Cancel the task if option is disabled.
		add_action(
			'update_option_' . Options::KEY,
			// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			function ( $old_value, $value, $option ) {

				if ( ! $this->is_enabled() ) {
					$this->cancel();
				}
			},
			10,
			3
		);
	}

	/**
	 * Randomly pick a timestamp which is not more than 1 week in the future.
	 *
	 * @since 4.7.0
	 *
	 * @return int
	 */
	private function generate_start_date(): int {

		$tracking = [];

		$tracking['days']    = wp_rand( 0, 6 ) * DAY_IN_SECONDS;
		$tracking['hours']   = wp_rand( 0, 23 ) * HOUR_IN_SECONDS;
		$tracking['minutes'] = wp_rand( 0, 59 ) * MINUTE_IN_SECONDS;
		$tracking['seconds'] = wp_rand( 0, 59 );

		return time() + array_sum( $tracking );
	}

	/**
	 * Send the actual data in a POST request.
	 *
	 * @since 4.7.0
	 */
	public function process() {

		$last_run = get_option( self::LAST_RUN );

		// Make sure we do not run it more than once a day
		// even though the schedule says "weekly".
		if (
			$last_run !== false &&
			( time() - $last_run ) < DAY_IN_SECONDS
		) {
			return;
		}

		wp_remote_post(
			self::TRACK_URL,
			[
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking'    => true,
				'body'        => $this->get_data(),
				'sslverify'   => apply_filters( 'https_local_ssl_verify', false ), // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName,WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
				'user-agent'  => $this->get_user_agent(),
			]
		);

		// Update the last run option to the current timestamp.
		update_option( self::LAST_RUN, time(), false );
	}

	/**
	 * Get the User Agent string that will be sent to the API.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	private function get_user_agent(): string {

		return 'PDFEmbedder/' . PDFEMB_VERSION . ' ' . ucwords( License::get_type() ) . '; ' . get_bloginfo( 'url' );
	}

	/**
	 * Get data for sending to the server.
	 *
	 * @since        4.7.0
	 *
	 * @noinspection PhpUndefinedConstantInspection
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function get_data(): array {

		global $wpdb;

		$theme_data     = wp_get_theme();
		$activated_time = (int) get_option( 'wppdf_emb_activation', 0 );

		$data = [
			// Generic data (environment).
			'url'                        => home_url(),
			'php_version'                => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
			'wp_version'                 => get_bloginfo( 'version' ),
			'mysql_version'              => $wpdb->db_version(),
			'server_version'             => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
			'is_ssl'                     => is_ssl(),
			'is_multisite'               => is_multisite(),
			'is_network_activated'       => Multisite::is_network_activated(),
			'is_wpcom'                   => defined( 'IS_WPCOM' ) && IS_WPCOM,
			'is_wpcom_vip'               => ( defined( 'WPCOM_IS_VIP_ENV' ) && WPCOM_IS_VIP_ENV ) || ( function_exists( 'wpcom_is_vip' ) && wpcom_is_vip() ),
			'is_wp_cache'                => defined( 'WP_CACHE' ) && WP_CACHE,
			'is_wp_rest_api_enabled'     => $this->is_rest_api_enabled(),
			'sites_count'                => $this->get_sites_total(),
			'active_plugins'             => $this->get_active_plugins(),
			'theme_name'                 => $theme_data->name,
			'theme_version'              => $theme_data->version,
			'locale'                     => get_locale(),
			'timezone_offset'            => wp_timezone_string(),
			'pdf_files_count'            => $this->get_number_of_pdfs(),
			// Plugin-specific data.
			'pdfemb_version'             => PDFEMB_VERSION,
			'pdfemb_license_key'         => License::get_key(),
			'pdfemb_license_type'        => License::get_type(),
			'pdfemb_license_status'      => License::get_status(),
			'pdfemb_is_pro'              => false,
			'pdfemb_lite_installed_date' => $activated_time,
			'pdfemb_settings'            => $this->get_settings(),
		];

		if ( $data['is_multisite'] ) {
			$data['url_primary'] = network_site_url();
		}

		return $data;
	}

	/**
	 * Get all settings, except those with sensitive data.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	private function get_settings(): array {
		// Remove keys with exact names that we don't need.
		return array_diff_key(
			pdf_embedder()->options()->get(),
			array_flip(
				[
					'pdfemb_version',
				]
			)
		);
	}

	/**
	 * Get the list of active plugins.
	 * Result is the array where key - plugin slug, and value - plugin version.
	 *
	 * @since 4.7.0
	 */
	private function get_active_plugins(): array {

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$active  = is_multisite() ?
			array_merge( get_option( 'active_plugins', [] ), array_flip( get_site_option( 'active_sitewide_plugins', [] ) ) ) :
			get_option( 'active_plugins', [] );
		$plugins = array_intersect_key( get_plugins(), array_flip( $active ) );

		return array_map(
			static function ( $plugin ) {
				if ( isset( $plugin['Version'] ) ) {
					return $plugin['Version'];
				}

				return 'Not Set';
			},
			$plugins
		);
	}

	/**
	 * Total number of sites.
	 *
	 * @since 4.7.0
	 */
	private function get_sites_total(): int {

		return function_exists( 'get_blog_count' ) ? (int) get_blog_count() : 1;
	}

	/**
	 * Test if the REST API is accessible.
	 *
	 * The REST API might be inaccessible due to various security measures,
	 * or it might be completely disabled by a plugin.
	 *
	 * @since 4.7.0
	 */
	private function is_rest_api_enabled(): bool {

		// phpcs:disable WPForms.PHP.ValidateHooks.InvalidHookName
		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );
		// phpcs:enable WPForms.PHP.ValidateHooks.InvalidHookName

		$url      = rest_url( 'wp/v2/types/post' );
		$response = wp_remote_get(
			$url,
			[
				'timeout'   => 10,
				'cookies'   => is_user_logged_in() ? wp_unslash( $_COOKIE ) : [],
				'sslverify' => $sslverify,
				'headers'   => [
					'Cache-Control' => 'no-cache',
					'X-WP-Nonce'    => wp_create_nonce( 'wp_rest' ),
				],
			]
		);

		// When testing the REST API, an error was encountered, leave early.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// When testing the REST API, an unexpected result was returned, leave early.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}

		// The REST API did not behave correctly, leave early.
		if ( ! Check::is_json( wp_remote_retrieve_body( $response ) ) ) {
			return false;
		}

		// We are all set. Confirm the connection.
		return true;
	}

	/**
	 * Get the number of PDF files.
	 *
	 * @since 4.7.0
	 */
	private function get_number_of_pdfs(): int {

		$posts = get_posts(
			[
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_type'      => 'attachment',
				'post_mime_type' => 'application/pdf',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			]
		);

		return count( $posts );
	}
}
