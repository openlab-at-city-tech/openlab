<?php
/**
 * The API class.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

use WP_Error;

/**
 * Class API
 */
class API {

	use Singleton;

	/**
	 * Request keys hidden from debug logs.
	 *
	 * @since 1.1.1
	 */
	private const LOG_REDACTED_KEYS = array( 'authorization', 'api_key', 'auth_key', 'auth_cookies', 'cookies', 'signature', 'wdp_auth' );

	/**
	 * Initialize API hooks.
	 *
	 * @since 1.1.1
	 */
	protected function __construct() {
		add_filter( 'http_request_args', array( $this, 'filter_package_request_args' ), 10, 2 );
	}

	/**
	 * Check if member is logged in.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_logged_in() {
		$membership_type = Data::get()->membership_type();

		return $this->has_api_key() && ! empty( $membership_type );
	}

	/**
	 * Check if an API key is set.
	 *
	 * @since 1.0.0
	 * @sice  1.0.6 Added force param.
	 *
	 * @param bool $force Force clear cache.
	 *
	 * @return bool
	 */
	public function has_api_key( $force = false ) {
		$key = $this->get_api_key( $force );

		return ! empty( $key );
	}

	/**
	 * Get API key for current member.
	 *
	 * @since 1.0.0
	 * @sice  1.0.6 Added force param.
	 *
	 * @param bool $force Force clear cache.
	 *
	 * @return string
	 */
	public function get_api_key( $force = false ) {
		if ( defined( '\WPMUDEV_APIKEY' ) && \WPMUDEV_APIKEY ) {
			return \WPMUDEV_APIKEY;
		} else {
			/**
			 * If forced, clear notoptions cache.
			 *
			 * Workaround W3 total Cache, due to possibility of discrepancies where object cache is used/allowed ( wp-admin vs non wp-admin ).
			 *
			 * @see https://incsub.atlassian.net/browse/HUB-10174?focusedCommentId=312567
			 */
			if ( $force ) {
				if ( defined( 'W3TC' ) ) {
					if ( is_multisite() ) {
						$network_id  = get_current_network_id();
						$cache_key   = "$network_id:notoptions";
						$cache_group = 'site-options';
					} else {
						$cache_key   = 'notoptions';
						$cache_group = 'options';
					}
					$notoptions = wp_cache_get( $cache_key, $cache_group );
					unset( $notoptions['wpmudev_apikey'] );
					wp_cache_set( $cache_key, $notoptions, $cache_group );
					wp_cache_delete( 'wpmudev_apikey', $cache_group );
				}
			}

			do_action( 'wpmudev_hub_connector_before_get_api_key', $force );

			// If 'clear_key' is present in URL then do not load the key from DB.
			return get_site_option( 'wpmudev_apikey', '' );
		}
	}

	/**
	 * Set API key for current member.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key API key.
	 *
	 * @return bool
	 */
	public function set_api_key( $key ) {
		return update_site_option( 'wpmudev_apikey', $key );
	}

	/**
	 * Returns the full URL to the specified REST API endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint The endpoint to call on the server.
	 *
	 * @return string The full URL to the requested endpoint.
	 */
	public function rest_url( $endpoint ) {
		return Data::get()->server_url( 'api/dashboard/v2/' . $endpoint );
	}

	/**
	 * Returns a credential-free package API URL.
	 *
	 * Uses the function `rest_url()` to build the URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint The endpoint to call on the server.
	 *
	 * @return string The full URL to the requested endpoint.
	 */
	public function rest_url_auth( $endpoint ) {
		$url = $this->rest_url( $endpoint );

		// Add hub site id if available.
		$site_id = Data::get()->hub_site_id();
		if ( ! empty( $site_id ) ) {
			$url = add_query_arg( 'site_id', $site_id, $url );
		}

		return $url;
	}

	/**
	 * Check whether a URL is a WPMU DEV package endpoint.
	 *
	 * @since 1.1.1
	 *
	 * @param string $url URL to validate.
	 *
	 * @return bool
	 */
	public function is_package_url( string $url ): bool {
		$url_parts   = wp_parse_url( $url );
		$server_url  = $this->rest_url( '' );
		$server_port = wp_parse_url( $server_url, PHP_URL_PORT );
		$url_port    = is_array( $url_parts ) ? ( $url_parts['port'] ?? null ) : null;

		if (
			! is_array( $url_parts )
			|| preg_match( '/[\x00-\x20\x7F]/', $url )
			|| isset( $url_parts['user'] )
			|| isset( $url_parts['pass'] )
			|| isset( $url_parts['fragment'] )
			|| $url_port !== $server_port
			|| 'https' !== strtolower( $url_parts['scheme'] ?? '' )
			|| strtolower( $url_parts['host'] ?? '' ) !== strtolower( (string) wp_parse_url( $server_url, PHP_URL_HOST ) )
		) {
			return false;
		}

		$path      = (string) wp_parse_url( $url, PHP_URL_PATH );
		$rest_base = (string) wp_parse_url( $server_url, PHP_URL_PATH );

		return '' !== $rest_base && (bool) preg_match( '!^' . preg_quote( $rest_base, '!' ) . '(install|download)/[0-9]+/?$!', $path );
	}

	/**
	 * Add package credentials only to validated WPMU DEV download requests.
	 *
	 * @since 1.1.2
	 *
	 * @param array  $args HTTP request arguments.
	 * @param string $url  Request URL.
	 *
	 * @return array
	 */
	public function filter_package_request_args( $args, $url = '' ) {
		if ( ! is_array( $args ) || ! is_string( $url ) || ! $this->is_package_url( $url ) || ! $this->has_api_key() ) {
			return $args;
		}

		if ( ! is_array( $args['headers'] ?? null ) ) {
			$args['headers'] = array();
		}

		$args['headers']['Authorization'] = $this->get_api_key();

		return $args;
	}

	/**
	 * Contacts the API to sync the latest data from this site.
	 *
	 * Returns the membership status if things are working out.
	 * In case the API call fails the function returns boolean false and does
	 * not update the update
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force      Optional forces a sync.
	 * @param bool $auth_check Should check for API key.
	 *
	 * @return array|WP_Error|bool
	 */
	public function sync_site( $force = false, $auth_check = true ) {
		global $wp_version;

		if ( defined( '\WP_INSTALLING' ) ) {
			return false;
		}

		// Only when logged in.
		if ( $auth_check && ! $this->has_api_key( $force ) ) {
			return new WP_Error(
				'not_logged_in',
				__( 'Not logged in.', 'wpmudev' )
			);
		}

		// New request object.
		$request = new Request();

		// Last sync timestamp.
		$last_run = Options::get( 'timestamp_sync', array() );

		$data = array(
			'call_version' => \WPMUDEV_HUB_CONNECTOR_VERSION,
			'domain'       => Data::get()->network_site_url(),
			'blog_count'   => is_multisite() ? get_blog_count() : 1,
			'wp_version'   => is_multisite() ? "WordPress Multisite $wp_version" : "WordPress $wp_version",
			'projects'     => wp_json_encode( Data::get()->wpmudev_projects() ),
			'admin_url'    => Data::get()->network_admin_url(),
			'home_url'     => Data::get()->network_home_url(),
			'sso_status'   => false,
			'repo_updates' => wp_json_encode( array() ),
			'packages'     => wp_json_encode(
				array(
					'plugins' => Data::get()->plugins(),
					'themes'  => Data::get()->themes(),
				)
			),
		);

		/**
		 * Filter hook to modify final API data.
		 *
		 * @param array $data Data.
		 */
		$data = apply_filters( 'wpmudev_hub_connector_get_api_data', $data );

		// Get a hash of the data to see if it changed.
		$data_hash = md5( wp_json_encode( $data ) );

		// Clear API cache if forcing an update.
		if ( $force || empty( $last_run ) ) {
			$data['call_version'] = microtime( true );
		} else {
			$membership_data = Data::get()->membership_data();
			if ( ! empty( $membership_data['membership'] ) ) {
				// This is the main check to prevent pinging unless the data is changed or 6 hrs have passed.
				if ( isset( $last_run['hash'], $last_run['time'] ) && $last_run['hash'] === $data_hash && $last_run['time'] > ( time() - ( \HOUR_IN_SECONDS * 6 ) ) ) {
					$this->maybe_log( '[WPMUDEV API] Skipped sync due to unchanged local data.' );

					return $membership_data;
				} elseif ( $last_run['fails'] ) { // Check for exponential backoff.
					// 5, 25, 125, 625, 3125, 3600 max.
					$backoff = min( pow( 5, $last_run['fails'] ), \HOUR_IN_SECONDS );
					if ( $last_run['time'] > ( time() - $backoff ) ) {
						$this->maybe_log( '[WPMUDEV API] Skipped sync due to API error exponential backoff.' );

						return $membership_data;
					}
				}
			}
		}

		// Make a hub sync request.
		$response = $request->post( 'hub-sync', true, $data );

		// Sync success.
		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			// Get membership data.
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $data['membership'] ) && empty( $data['membership'] ) && ! defined( '\WPMUDEV_APIKEY' ) && $this->get_api_key() ) {
				Options::reset();
				// Clear API key.
				$this->set_api_key( '' );

				return new WP_Error(
					'invalid_api_key_or_expired',
					__( 'Invalid API Key or Expired membership.', 'wpmudev' )
				);
			} elseif ( ! empty( $data['membership'] ) ) {
				// Update membership data.
				$this->update_membership_data( $data );

				// Update sync timestamps.
				Options::set(
					'timestamp_sync',
					array(
						'time'  => time(),
						'hash'  => $data_hash,
						'fails' => 0,
					)
				);

				$result = $data;
			} else {
				return new WP_Error(
					'invalid_api_response',
					__( 'Invalid remote API response.', 'wpmudev' )
				);
			}
		} else {
			// For network errors, perform exponential backoff.
			Options::set(
				'timestamp_sync',
				array(
					'time'  => time(),
					'fails' => 1,
				)
			);

			$api_error = $this->get_api_error( $response );
			// Format error messages.
			$api_error = $this->format_error_messages( $api_error );

			return new WP_Error(
				$api_error['code'],
				$api_error['message']
			);
		}

		/**
		 * Action hook fired after a sync is completed.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wpmudev_hub_connector_sync_completed' );

		// If first time sync.
		if ( empty( $last_run ) ) {
			/**
			 * Action hook fired after the first sync is completed.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wpmudev_hub_connector_first_sync_completed' );
		}

		return $result;
	}

	/**
	 * Unsync site
	 *
	 * @return mixed|WP_Error
	 */
	public function unsync_site() {
		// Only when logged in.
		if ( ! $this->has_api_key() ) {
			return new WP_Error(
				'not_logged_in',
				__( 'Not logged in.', 'wpmudev' )
			);
		}

		// New request object.
		$request = new Request();
		// Make a hub unsync request.
		$response = $request->delete(
			'hub-unsync',
			true,
			array(
				'call_version' => \WPMUDEV_HUB_CONNECTOR_VERSION,
				'domain'       => Data::get()->network_site_url(),
			)
		);

		// to trigger logging.
		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			$this->get_api_error( $response );
			$response = $this->format_error_messages( $response );
		}

		return $response;
	}

	/**
	 * Logout and disconnect the site from Hub.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error
	 */
	public function logout() {
		// Not logged in.
		if ( ! $this->is_logged_in() ) {
			return new WP_Error( 'not_logged_in', __( 'Not logged in.', 'wpmudev' ) );
		}

		// whatever happens, reset + remove api key.
		$response = $this->unsync_site();

		// Reset settings.
		Options::reset();
		// Remove API key.
		$this->set_api_key( '' );

		return $response;
	}

	/**
	 * Get available teams for the authenticated user.
	 *
	 * This list is used to select the team after login.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key API key.
	 *
	 * @return array|bool
	 */
	public function get_hub_teams( $key ) {
		$request = new Request();

		// Sets up special auth header.
		$request->add_header_argument( 'Authorization', $key );

		// Send API request.
		$response = $request->get( 'site-authenticate-teams' );

		// Error.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		} else {
			// Get team list.
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $data['data'] ) ) {
				return $data['data'];
			}
		}

		return array();
	}

	/**
	 * Get the currently logged in member user profile data.
	 *
	 * If not found in db, get it from the API.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force Force update from API.
	 *
	 * @return array|WP_Error
	 */
	public function get_profile( $force = false ) {
		// Only when API key is available.
		if ( ! $this->has_api_key() ) {
			return new WP_Error(
				'not_logged_in',
				__( 'Not logged in.', 'wpmudev' )
			);
		}

		$profile = Options::get( 'profile_data' );

		if ( ! empty( $profile ) && ! $force ) {
			return $profile;
		}

		// New request object.
		$request = new Request();

		// Make an API request.
		$response = $request->get( 'user-info', true );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $data['profile'] ) ) {
				// Set profile data.
				Options::set( 'profile_data', $data['profile'] );

				return $data['profile'];
			} else {
				return new WP_Error(
					'api_request_error',
					__( 'Error unserializing remote response.', 'wpmudev' )
				);
			}
		} else {
			$api_error = $this->get_api_error( $response );

			return new WP_Error(
				$api_error['code'],
				$api_error['message']
			);
		}
	}

	/**
	 * Parse API error and get a readable error message.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response API response.
	 *
	 * @return array
	 */
	private function get_api_error( $response ) {
		// Default error code is 500.
		$error_code = wp_remote_retrieve_response_code( $response );
		if ( ! $error_code ) {
			$error_code = 500;
		}

		$error = array(
			'code'    => 'api_error',
			'message' => '',
		);

		// Attempt to retrieve http response body.
		$body = is_array( $response ) ? wp_remote_retrieve_body( $response ) : false;

		// Get error message.
		if ( is_scalar( $response ) ) {
			$error['message'] = $response;
		} elseif ( is_wp_error( $response ) ) {
			$error['message'] = $response->get_error_message();
		} elseif ( is_array( $response ) && ! empty( $body ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $data ) ) {
				if ( ! empty( $data['message'] ) ) {
					$error['message'] = $data['message'];
				}
				if ( ! empty( $data['code'] ) ) {
					$error['code'] = $data['code'];
				}
			}
		}

		$url = '(unknown URL)';
		if ( is_array( $response ) && isset( $response['request_url'] ) ) {
			$url = $this->scrub_log_message( $response['request_url'] );
		}

		if ( empty( $error['message'] ) ) {
			$error['message'] = sprintf(
				'HTTP Error: %s "%s"',
				$error_code,
				wp_remote_retrieve_response_message( $response )
			);
		}

		// Only enabled with constants.
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
		if ( defined( '\WPMUDEV_API_DEBUG' ) && \WPMUDEV_API_DEBUG ) {
			$trace       = wp_debug_backtrace_summary( null, null, false );
			$caller_dump = "\n\t# " . implode( "\n\t# ", $trace );

			if ( is_array( $response ) && isset( $response['request_url'] ) ) {
				$caller_dump = "\n\tURL: " . $this->scrub_log_message( $response['request_url'] ) . $caller_dump;
			}

			// Log the error to PHP error log.
			error_log(
				sprintf(
					'[WPMUDEV API Error] %s | %s (%s [%s]) %s',
					\WPMUDEV_HUB_CONNECTOR_VERSION,
					$this->scrub_log_message( $error['message'] ),
					$url,
					$error_code,
					$caller_dump
				),
				0
			);
		}
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log

		// If error was "invalid API key" then log out the user. (we don't call logout here to avoid infinite loop).
		if ( 401 === (int) $error_code && ! defined( '\WPMUDEV_APIKEY' ) && ! defined( '\WPMUDEV_OVERRIDE_LOGOUT' ) ) {
			$this->set_api_key( '' );
		}

		return $error;
	}

	/**
	 * Update membership data from API data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to update.
	 *
	 * @return void
	 */
	private function update_membership_data( $data ) {
		if (
			isset( $data['membership'] )
			&& empty( $data['membership'] )
			&& ! defined( '\WPMUDEV_APIKEY' )
			&& $this->get_api_key()
		) {
			// Clear API key.
			$this->set_api_key( '' );
		}

		// Update membership data.
		Options::set( 'membership_data', $data );
	}

	/**
	 * Write data to error log if log is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data to log.
	 *
	 * @return void
	 */
	private function maybe_log( $data ) {
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

		// Only if logging is enabled.
		if ( defined( '\WPMUDEV_API_DEBUG' ) && \WPMUDEV_API_DEBUG ) {
			error_log( $this->scrub_log_message( $data ) );
		}
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Build a log-safe JSON payload.
	 *
	 * @since 1.1.1
	 *
	 * @param mixed $data Payload to log.
	 *
	 * @return string
	 */
	public function prepare_log_payload( $data ): string {
		if ( is_object( $data ) ) {
			$data = json_decode( (string) wp_json_encode( $data ), true );
		}

		$json = wp_json_encode( $this->redact_for_log( $data ), JSON_PRETTY_PRINT );

		return is_string( $json ) ? $this->scrub_log_message( $json ) : '[unencodable payload]';
	}

	/**
	 * Recursively redact known credential fields.
	 *
	 * @since 1.1.1
	 *
	 * @param mixed $value Value to redact.
	 *
	 * @return mixed
	 */
	private function redact_for_log( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		foreach ( $value as $key => $item ) {
			$is_secret     = is_string( $key ) && ( in_array( strtolower( $key ), self::LOG_REDACTED_KEYS, true ) || 0 === strpos( strtolower( $key ), 'signed_' ) );
			$value[ $key ] = $is_secret ? $this->redact_value_for_log( $item ) : $this->redact_for_log( $item );
		}

		return $value;
	}

	/**
	 * Redact a credential value while preserving its shape.
	 *
	 * @since 1.1.1
	 *
	 * @param mixed $value Credential value.
	 *
	 * @return mixed
	 */
	private function redact_value_for_log( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'redact_value_for_log' ), $value );
		}

		return is_scalar( $value ) ? '[****]' : $value;
	}

	/**
	 * Remove the current API key from an assembled log message.
	 *
	 * @since 1.1.1
	 *
	 * @param string $message Log message.
	 *
	 * @return string
	 */
	private function scrub_log_message( $message ) {
		$key = $this->get_api_key();

		return is_string( $message ) && '' !== $key ? str_replace( $key, '[****]', $message ) : $message;
	}

	/**
	 * Format error messages before showing to public.
	 *
	 * @since 1.0.0
	 *
	 * @param array $error Error data.
	 *
	 * @return array
	 */
	private function format_error_messages( $error ) {
		// Need both code and message.
		if ( ! isset( $error['code'], $error['message'] ) ) {
			return $error;
		}

		// Already registered error.
		if ( 'already_registered' === $error['code'] ) {
			$error['message'] = sprintf(
			// translators: %s Support URL.
				__( 'This site is currently registered to a different user. Please <a target="_blank" href="%s">contact support for assistance</a>.', 'wpmudev' ),
				Data::get()->server_url( 'hub/support/' )
			);
		} elseif ( 'expired_membership' === $error['code'] ) {
			$error['message'] = sprintf(
			// translators: %1$s Hub Account URL, %2$s: Switch to Free URL.
				__(
					'Login failed — your WPMU DEV membership has expired. Renew now to regain full access, or switch to our free plan to continue managing all your site in the Hub.<br/><br/><a class="sui-button sui-button-blue" href="%1$s" target="_blank">Renew Membership</a>&nbsp;<a class="sui-button sui-button-ghost" href="%2$s" target="_blank">Switch to Free</a>',
					'wpmudev'
				),
				Data::get()->server_url( 'hub2/account/' ),
				Data::get()->server_url( 'hub2/?switch-free=1 ' )
			);
		}

		return $error;
	}
}
