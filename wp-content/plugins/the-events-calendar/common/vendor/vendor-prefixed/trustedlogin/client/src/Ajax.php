<?php
/**
 * Class Ajax
 *
 * @package GravityView\TrustedLogin\Client
 *
 * @copyright 2024 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Ajax
 */
final class Ajax {

	/**
	 * Config instance.
	 *
	 * @var \TEC\Common\TrustedLogin\Config
	 */
	private $config;

	/**
	 * Logging instance.
	 *
	 * @var null|\TEC\Common\TrustedLogin\Logging $logging
	 */
	private $logging;

	/**
	 * Fields that may be included in the support data.
	 *
	 * @var string[]
	 * @see grantAccess() in trustedlogin.js
	 */
	private $generate_support_fields = array(
		'action',
		'vendor',
		'_nonce',
		'reference_id',
		'debug_data_consent',
		'ticket',
	);

	/**
	 * Cron constructor.
	 *
	 * @param Config  $config Config instance.
	 * @param Logging $logging Logging instance.
	 */
	public function __construct( Config $config, Logging $logging ) {
		$this->config  = $config;
		$this->logging = $logging;
	}

	/**
	 * Add hooks to process the AJAX requests.
	 */
	public function init() {
		add_action( 'wp_ajax_tl_' . $this->config->ns() . '_gen_support', array( $this, 'ajax_generate_support' ) );
	}

	/**
	 * AJAX handler for maybe generating a Support User
	 *
	 * @since 1.0.0
	 *
	 * @return void Sends a JSON success or error message based on what happens
	 */
	public function ajax_generate_support() {

		// Remove any fields that are not in the $ajax_fields array.
		$posted_data = array_intersect_key( $_POST, array_flip( $this->generate_support_fields ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( empty( $posted_data['vendor'] ) ) {
			$this->logging->log( 'Vendor not defined in TrustedLogin configuration.', __METHOD__, 'critical' );

			wp_send_json_error( array( 'message' => 'Vendor not defined in TrustedLogin configuration.' ) );
		}

		// There are multiple TrustedLogin instances, and this is not the one being called.
		// This should not occur, since the AJAX action is namespaced.
		if ( $this->config->ns() !== $posted_data['vendor'] ) {
			$this->logging->log( 'Vendor does not match TrustedLogin configuration.', __METHOD__, 'critical' );

			wp_send_json_error( array( 'message' => 'Vendor does not match.' ) );
		}

		if ( empty( $posted_data['_nonce'] ) ) {
			wp_send_json_error( array( 'message' => 'Nonce not sent in the request.' ) );
		}

		if ( ! check_ajax_referer( 'tl_nonce-' . get_current_user_id(), '_nonce', false ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Verification issue: Request could not be verified. Please reload the page.', 'trustedlogin' ) ) );
		}

		if ( ! current_user_can( 'create_users' ) ) {
			$this->logging->log( 'Current user does not have `create_users` capability when trying to grant access.', __METHOD__, 'error' );

			wp_send_json_error( array( 'message' => esc_html__( 'You do not have the ability to create users.', 'trustedlogin' ) ) );
		}

		$client = new Client( $this->config, false );

		// Passed from grantAccess() in trustedlogin.js.
		$include_debug_data = ! empty( $posted_data['debug_data_consent'] );

		// Passed from grantAccess() in trustedlogin.js.
		$ticket_data = ! empty( $posted_data['ticket'] ) ? $posted_data['ticket'] : null;

		$response = $client->grant_access( $include_debug_data, $ticket_data );

		if ( is_wp_error( $response ) ) {
			$error_data  = $response->get_error_data();
			$status_code = isset( $error_data['status_code'] ) ? $error_data['status_code'] : 500;

			wp_send_json_error( array( 'message' => $response->get_error_message() ), $status_code );
		}

		wp_send_json_success( $response, 201 );
	}
}
