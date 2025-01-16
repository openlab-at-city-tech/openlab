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
	 *
	 * @return bool
	 */
	public function has_api_key() {
		$key = $this->get_api_key();

		return ! empty( $key );
	}

	/**
	 * Get API key for current member.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_api_key() {
		if ( defined( '\WPMUDEV_APIKEY' ) && \WPMUDEV_APIKEY ) {
			return \WPMUDEV_APIKEY;
		} else {
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
	 * Returns the full URL to the specified REST API endpoint and includes
	 * the API key as last element in URL.
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
		$api_key = $this->get_api_key();

		// Append API key.
		if ( false === strpos( $endpoint, '/' . $api_key ) ) {
			$endpoint .= '/' . $api_key;
		}

		// Get full URL.
		$url = $this->rest_url( $endpoint );

		// Add hub site id if available.
		$site_id = Data::get()->hub_site_id();
		if ( ! empty( $site_id ) ) {
			$url = add_query_arg( 'site_id', $site_id, $url );
		}

		return $url;
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
	 * @param bool $force Optional forces a sync.
	 *
	 * @return array|WP_Error
	 */
	public function sync_site( $force = false ) {
		global $wp_version;

		// Only when logged in.
		if ( ! $this->has_api_key() ) {
			return new WP_Error(
				'',
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
			// This is the main check to prevent pinging unless the data is changed or 6 hrs have passed.
			if ( isset( $last_run['hash'], $last_run['time'] ) && $last_run['hash'] === $data_hash && $last_run['time'] > ( time() - ( \HOUR_IN_SECONDS * 6 ) ) ) {
				$this->maybe_log( '[WPMUDEV API] Skipped sync due to unchanged local data.' );

				return Data::get()->membership_data();
			} elseif ( $last_run['fails'] ) { // Check for exponential backoff.
				// 5, 25, 125, 625, 3125, 3600 max.
				$backoff = min( pow( 5, $last_run['fails'] ), \HOUR_IN_SECONDS );
				if ( $last_run['time'] > ( time() - $backoff ) ) {
					$this->maybe_log( '[WPMUDEV API] Skipped sync due to API error exponential backoff.' );

					return Data::get()->membership_data();
				}
			}
		}

		// Make a hub sync request.
		$response = $request->post( 'hub-sync', true, $data );

		// Sync success.
		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			// Get membership data.
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $data ) ) {
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
					'',
					__( 'Error unserializing remote response.', 'wpmudev' )
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
	 * Contacts the API to sync the latest data from this site.
	 *
	 * Returns the membership status if things are working out.
	 * In case the API call fails the function returns boolean false and does
	 * not update the update
	 *
	 * @since 1.0.0
	 *
	 * @return bool|array|WP_Error
	 */
	public function logout() {
		// Not logged in.
		if ( ! $this->is_logged_in() ) {
			return false;
		}

		// Reset settings.
		Options::reset();
		// Remove API key.
		$this->set_api_key( '' );

		// Do a sync to remove site.
		return $this->sync_site( true );
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
				'',
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
					'',
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
			'code'    => '',
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
			$url = $response['request_url'];
		}

		if ( empty( $error['message'] ) ) {
			$error['message'] = sprintf(
				'HTTP Error: %s "%s"',
				$error_code,
				wp_remote_retrieve_response_message( $response )
			);
		}

		if ( defined( '\WPMUDEV_API_DEBUG' ) && \WPMUDEV_API_DEBUG ) {
			$trace     = debug_backtrace();
			$caller    = array();
			$last_line = '';
			foreach ( $trace as $level => $item ) {
				if ( ! isset( $item['class'] ) ) {
					$item['class'] = '';
				}
				if ( ! isset( $item['type'] ) ) {
					$item['type'] = '';
				}
				if ( ! isset( $item['function'] ) ) {
					$item['function'] = '<function>';
				}
				if ( ! isset( $item['line'] ) ) {
					$item['line'] = '?';
				}

				if ( $level > 0 ) {
					$caller[] = $item['class'] . $item['type'] . $item['function'] . ':' . $last_line;
				}
				$last_line = $item['line'];
			}
			$caller_dump = "\n\t# " . implode( "\n\t# ", $caller );

			if ( is_array( $response ) && isset( $response['request_url'] ) ) {
				$caller_dump = "\n\tURL: " . $response['request_url'] . $caller_dump;
			}

			// Log the error to PHP error log.
			error_log(
				sprintf(
					'[WPMUDEV API Error] %s | %s (%s [%s]) %s',
					\WPMUDEV_HUB_CONNECTOR_VERSION,
					$error['message'],
					$url,
					$error_code,
					$caller_dump
				),
				0
			);
		}

		// If error was "invalid API key" then log out the user. (we don't call logout here to avoid infinite loop).
		if ( 401 == $error_code && ! defined( '\WPMUDEV_APIKEY' ) && ! defined( '\WPMUDEV_OVERRIDE_LOGOUT' ) ) {
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
			isset( $data['membership'] ) &&
			empty( $data['membership'] ) &&
			! defined( '\WPMUDEV_APIKEY' ) && $this->get_api_key()
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
		// Only if logging is enabled.
		if ( defined( '\WPMUDEV_API_DEBUG' ) && \WPMUDEV_API_DEBUG ) {
			error_log( $data );
		}
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
		}

		return $error;
	}
}
