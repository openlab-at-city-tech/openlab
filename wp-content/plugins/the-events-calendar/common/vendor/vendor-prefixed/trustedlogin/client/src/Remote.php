<?php
/**
 * Class Remote
 *
 * @package TEC\Common\TrustedLogin\Client
 *
 * @copyright 2021 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use WP_Error;
use WP_User;
use WP_Admin_Bar;

/**
 * The TrustedLogin all-in-one drop-in class.
 */
final class Remote {

	/**
	 * The API url for the TrustedLogin SaaS Platform (with trailing slash).
	 *
	 * @var string
	 * @since 1.0.0
	 */
	const API_URL = 'https://app.trustedlogin.com/api/v1/';

	/**
	 * Config object.
	 *
	 * @var Config $config
	 */
	private $config;

	/**
	 * Logging object.
	 *
	 * @var Logging $logging
	 */
	private $logging;

	/**
	 * SupportUser constructor.
	 *
	 * @param Config  $config Config object.
	 * @param Logging $logging Logging object.
	 */
	public function __construct( Config $config, Logging $logging ) {
		$this->config  = $config;
		$this->logging = $logging;
	}

	/**
	 * Add hooks for the class to send webhooks when access is created, extended, or revoked, or the user has logged-in.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// If the webhook URL is not set, don't add the actions to speed up initialization.
		if ( ! $this->config->get_setting( 'webhook/url' ) && ! $this->config->get_setting( 'webhook_url' ) ) {
			return;
		}

		add_action( 'trustedlogin/' . $this->config->ns() . '/access/created', array( $this, 'maybe_send_webhook' ) ); // @phpstan-ignore-line
		add_action( 'trustedlogin/' . $this->config->ns() . '/access/extended', array( $this, 'maybe_send_webhook' ) ); // @phpstan-ignore-line
		add_action( 'trustedlogin/' . $this->config->ns() . '/access/revoked', array( $this, 'maybe_send_webhook' ) ); // @phpstan-ignore-line
		add_action( 'trustedlogin/' . $this->config->ns() . '/logged_in', array( $this, 'maybe_send_webhook' ) ); // @phpstan-ignore-line
	}

	/**
	 * POSTs to `webhook/url`, if defined in the configuration array.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 $data now includes the `$access_key` and `$debug_data` keys.
	 * @since 1.5.0 $data now includes the `$ticket` key.
	 *
	 * @param array $data {
	 *   The data to send to the webhook.
	 *   @type string $url The site URL as returned by get_site_url().
	 *   @type string $ns Namespace of the plugin.
	 *   @type string $action "created", "extended", "logged_in", or "revoked".
	 *   @type string $access_key The access key.
	 *   @type string $debug_data (Optional) Site debug data from {@see WP_Debug_Data::debug_data()}, sent if `webhook/debug_data` is true.
	 *   @type string $ref (Optional) Support ticket Reference ID.
	 *   @type array $ticket (Optional) Support ticket provided by customer with `message` key.
	 * }
	 *
	 * @return bool|WP_Error False: webhook setting not defined; True: success; WP_Error: error!
	 */
	public function maybe_send_webhook( $data ) {

		$webhook_url = $this->config->get_setting( 'webhook/url' );

		if ( ! $webhook_url ) {
			// Back compatibility with v1–v1.3.4.
			$webhook_url = $this->config->get_setting( 'webhook_url' );
		}

		if ( ! $webhook_url ) {
			return false;
		}

		if ( ! wp_http_validate_url( $webhook_url ) ) {
			$error = new \WP_Error( 'invalid_webhook_url', 'An invalid `webhook/url` setting was passed to the TrustedLogin Client: ' . esc_attr( $webhook_url ) );

			$this->logging->log( $error, __METHOD__, 'error' );

			return $error;
		}

		try {
			$posted = wp_remote_post( $webhook_url, array( 'body' => $data ) );

			if ( is_wp_error( $posted ) ) {
				$this->logging->log( 'An error encountered while sending a webhook to ' . esc_attr( $webhook_url ), __METHOD__, 'error', $posted );

				return $posted;
			}

			$this->logging->log( 'Webhook was sent to ' . esc_attr( $webhook_url ), __METHOD__, 'debug', $data );

			return true;
		} catch ( Exception $exception ) {
			$this->logging->log( 'A fatal error was triggered while sending a webhook to ' . esc_attr( $webhook_url ) . ': ' . $exception->getMessage(), __METHOD__, 'error' );

			return new \WP_Error( $exception->getCode(), $exception->getMessage() );
		}
	}

	/**
	 * API Function: send the API request
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Path for the REST API request (no initial or trailing slash needed).
	 * @param array  $data Data sent with POST, PUT, or DELETE requests as JSON-encoded body.
	 * @param string $method HTTP method to use for the request.
	 * @param array  $additional_headers Any additional headers to be set with the request. Merged with default headers.
	 *
	 * @return array|WP_Error wp_remote_request() response or WP_Error if something went wrong
	 */
	public function send( $path, $data, $method = 'POST', $additional_headers = array() ) {

		if ( ! is_string( $path ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( sprintf( 'Error: Path not a string (%s)', print_r( $path, true ) ), __METHOD__, 'critical' );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new \WP_Error( 'invalid_path', sprintf( 'Error: Path "%s" is not a string', print_r( $path, true ) ) );
		}

		if ( ! is_string( $method ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( sprintf( 'Error: Method not a string (%s)', print_r( $method, true ) ), __METHOD__, 'critical' );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new \WP_Error( 'invalid_method', sprintf( 'Error: HTTP method "%s" is not a string', print_r( $method, true ) ) );
		}

		$method = strtoupper( $method );

		if ( ! in_array(
			$method,
			array(
				'POST',
				'PUT',
				'GET',
				'HEAD',
				'DELETE',
			),
			true
		) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( sprintf( 'Error: Method not in allowed array list (%s)', print_r( $method, true ) ), __METHOD__, 'critical' );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new \WP_Error( 'invalid_method', sprintf( 'Error: HTTP method "%s" is not in the list of allowed methods', print_r( $method, true ) ) );
		}

		$headers = array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->config->get_setting( 'auth/api_key' ),
		);

		if ( ! empty( $additional_headers ) ) {
			$headers = array_merge( $headers, $additional_headers );
		}

		$request_options = array(
			'method'      => $method,
			'timeout'     => 15,
			'httpversion' => '1.1',
			'headers'     => $headers,
		);

		if ( ! empty( $data ) && ! in_array( $method, array( 'GET', 'HEAD' ), true ) ) {
			$request_options['body'] = wp_json_encode( $data );
		}

		try {
			$api_url = $this->build_api_url( $path );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( sprintf( 'Sending to %s: %s', $api_url, print_r( $request_options, true ) ), __METHOD__, 'debug' );

			$response = wp_remote_request( $api_url, $request_options );
		} catch ( Exception $exception ) {
			$error = new \WP_Error( 'wp_remote_request_exception', sprintf( 'There was an exception during the remote request: %s (%s)', $exception->getMessage(), $exception->getCode() ) );

			$this->logging->log( $error, __METHOD__, 'error' );

			return $error;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->logging->log( sprintf( 'Response: %s', print_r( $response, true ) ), __METHOD__, 'debug' );

		return $response;
	}

	/**
	 * Builds URL to API endpoints
	 *
	 * @since 1.0.0
	 * @since 1.9.0 Throws an exception if the endpoint is not a string.
	 *
	 * @param string $endpoint Endpoint to hit on the API; example "sites" or "sites/{$site_identifier}".
	 *
	 * @throws Exception If the endpoint is not a string.
	 *
	 * @return string
	 */
	private function build_api_url( $endpoint = '' ) {

		if ( ! is_string( $endpoint ) ) {
			// Code for endpoint not being a string.
			throw new Exception( 'Endpoint must be a string.', 400 );
		}

		/**
		 * Modifies the endpoint URL for the TrustedLogin service.
		 *
		 * @internal This allows pointing requests to testing servers.
		 *
		 * @param string $url URL to TrustedLogin API.
		 */
		$base_url = apply_filters( 'trustedlogin/' . $this->config->ns() . '/api_url', self::API_URL );

		return trailingslashit( $base_url ) . $endpoint;
	}

	/**
	 * Translates response codes to more nuanced error descriptions specific to TrustedLogin.
	 *
	 * @param array|WP_Error $api_response Response from HTTP API.
	 *
	 * @return int|WP_Error|null If valid response, the response code ID or null. If error, a WP_Error with a message description.
	 */
	public static function check_response_code( $api_response ) {

		if ( is_wp_error( $api_response ) ) {
			$response_code = $api_response->get_error_code();
		} else {
			$response_code = wp_remote_retrieve_response_code( $api_response );
		}

		switch ( $response_code ) {

			// Successful response, but no sites found.
			case 204:
				return null;

			case 400:
			case 423:
				return new \WP_Error( 'unable_to_verify', esc_html__( 'Unable to verify Pause Mode.', 'trustedlogin' ), $api_response );

			case 401:
				return new \WP_Error( 'unauthenticated', esc_html__( 'Authentication failed.', 'trustedlogin' ), $api_response );

			case 402:
				return new \WP_Error( 'account_error', esc_html__( 'TrustedLogin account issue.', 'trustedlogin' ), $api_response );

			case 403:
				return new \WP_Error( 'invalid_token', esc_html__( 'Invalid tokens.', 'trustedlogin' ), $api_response );

			// the KV store was not found, possible issue with endpoint.
			case 404:
				return new \WP_Error( 'not_found', esc_html__( 'The TrustedLogin vendor was not found.', 'trustedlogin' ), $api_response );

			// The site is a teapot.
			case 418:
				return new \WP_Error( 'teapot', '🫖', $api_response );

			// Server offline.
			case 500:
			case 503:
			case 'http_request_failed':
				return new \WP_Error( 'unavailable', esc_html__( 'The TrustedLogin site is not currently online.', 'trustedlogin' ), $api_response );

			// Server error.
			case 501:
			case 502:
			case 522:
				return new \WP_Error( 'server_error', esc_html__( 'The TrustedLogin site is not currently available.', 'trustedlogin' ), $api_response );

			// wp_remote_retrieve_response_code() couldn't parse the $api_response.
			case '':
				return new \WP_Error( 'invalid_response', esc_html__( 'Invalid response.', 'trustedlogin' ), $api_response );

			default:
				return (int) $response_code;
		}
	}

	/**
	 * API Response Handler
	 *
	 * @since 1.0.0
	 *
	 * @param array|WP_Error $api_response The response from HTTP API.
	 * @param array          $required_keys If the response JSON must have specific keys in it, pass them here.
	 *
	 * @return array|WP_Error|null If successful response, returns array of JSON data. If failed, returns WP_Error. If
	 */
	public function handle_response( $api_response, $required_keys = array() ) {

		$response_code = self::check_response_code( $api_response );

		// Null means a successful response, but does not return any body content (204). We can return early.
		if ( null === $response_code ) {
			return null;
		}

		if ( is_wp_error( $response_code ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( 'Response code check failed: ' . print_r( $response_code, true ), __METHOD__, 'error' );

			return $response_code;
		}

		$response_body = wp_remote_retrieve_body( $api_response );

		if ( empty( $response_body ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->logging->log( 'Response body not set: ' . print_r( $response_body, true ), __METHOD__, 'error' );

			return new \WP_Error( 'missing_response_body', esc_html__( 'The response was invalid.', 'trustedlogin' ), $api_response );
		}

		$response_json = json_decode( $response_body, true );

		if ( empty( $response_json ) ) {
			return new \WP_Error( 'invalid_response', esc_html__( 'Invalid response.', 'trustedlogin' ), $api_response );
		}

		if ( isset( $response_json['errors'] ) ) {
			$errors = '';

			// Multi-dimensional; we flatten.
			foreach ( $response_json['errors'] as $key => $error ) {
				$error   = is_array( $error ) ? reset( $error ) : $error;
				$errors .= $error;
			}

			return new \WP_Error( 'errors_in_response', esc_html( $errors ), $response_body );
		}

		foreach ( (array) $required_keys as $required_key ) {
			if ( ! isset( $response_json[ $required_key ] ) ) {
				// translators: %s is the name of the missing data from the server.
				return new \WP_Error( 'missing_required_key', sprintf( esc_html__( 'Invalid response. Missing key: %s', 'trustedlogin' ), $required_key ), $response_body );
			}
		}

		return $response_json;
	}
}
