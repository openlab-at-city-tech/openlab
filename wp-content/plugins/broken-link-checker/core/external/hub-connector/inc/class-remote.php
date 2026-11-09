<?php
/**
 * The remote class to perform actions from the Hub.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

use WPMUDEV\Hub\Connector\Vendor\phpseclib3\Crypt\PublicKeyLoader;
use WPMUDEV\Hub\Connector\Vendor\phpseclib3\Crypt\RSA;
use WPMUDEV\Hub\Connector\Vendor\phpseclib3\Crypt\RSA\PublicKey;

// nature of the callbacks workflow.
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

/**
 * Class Remote
 */
class Remote {

	use Singleton;

	/**
	 * Stores current action being processed
	 *
	 * @var string
	 */
	protected $current_action = '';

	/**
	 * Stores current action params being processed
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $current_params = array();

	/**
	 * Stores registered remote access actions and their callbacks.
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Set up the remote module.
	 *
	 * Here we load and initialize the API request from Hub.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		// Only when API key is available.
		if ( ! API::get()->has_api_key() ) {
			return;
		}

		// Using priority because some plugins may initialize updates with low priority.
		add_action( 'init', array( $this, 'run_request' ), 1000 );
	}

	/**
	 * Setup current request data.
	 *
	 * Set current action name and params to be processed.
	 * If action and params are invalid, we will die with json
	 * error message.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function run_request() {
		// Do nothing if we don't.
		if ( ! $this->is_hub_request() ) {
			return;
		}

		// Register actions.
		$this->register_internal_actions();
		$this->register_plugin_actions();

		// Get the json data.
		$raw_json = file_get_contents( 'php://input' );

		// Get body.
		$body = json_decode( $raw_json );

		// Validate hash.
		$this->validate_request_hash( $_GET['wpmudev-hub'], $raw_json ); // phpcs:ignore

		// Action name is required.
		if ( ! isset( $body->action ) ) {
			wp_send_json_error(
				array(
					'code'    => 'invalid_params',
					'message' => __( 'The "action" parameter is missing', 'wpmudev' ),
				)
			);
		}

		// Params are required.
		if ( ! isset( $body->params ) ) {
			wp_send_json_error(
				array(
					'code'    => 'invalid_params',
					'message' => __( 'The "params" object is missing', 'wpmudev' ),
				)
			);
		}

		// Set request data.
		$this->current_action = $body->action;
		$this->current_params = $body->params;

		// Now process the action.
		$this->process_action();
	}

	/**
	 * Run current request.
	 *
	 * First we will register all actions and then check if current action
	 * is a valid one. If not we will send a json error and die.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function process_action() {
		// Continue only if valid action.
		if ( isset( $this->actions[ $this->current_action ] ) ) {
			// Execute request action.
			call_user_func(
				$this->actions[ $this->current_action ],
				$this->current_params,
				$this->current_action,
				$this
			);

			// Send success in case the callback didn't respond.
			wp_send_json_success();
		} else {
			// Invalid action.
			wp_send_json_error(
				array(
					'code'    => 'unregistered_action',
					'message' => __( 'This action is not registered. The required plugin is not installed, updated, or configured properly.', 'wpmudev' ),
				)
			);
		}
	}

	/**
	 * Validate the hash for the request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hash Hash from header.
	 * @param string $id   Request ID.
	 * @param string $json Json string.
	 *
	 * @return bool
	 */
	public function validate_hash( $hash, $id, $json ) {
		// Validation.
		if ( ! API::get()->has_api_key() || empty( $hash ) || empty( $id ) || empty( $json ) ) {
			return false;
		}

		// Get API key.
		$api_key = API::get()->get_api_key();

		// Combine ID and json string.
		$hash_string = $id . $json;

		// Generate hash.
		$new_hash = hash_hmac( 'sha256', $hash_string, $api_key );

		// Timing attack safe string comparison, PHP <5.6 compat added in WP 3.9.2.
		return hash_equals( $new_hash, $hash );
	}

	/**
	 * Validate the nonce for the request.
	 *
	 * Check nonce to prevent replay attacks.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Validate timestamp age and claim it atomically.
	 *
	 * @param string $id Request ID.
	 *
	 * @return bool
	 */
	protected function validate_nonce( $id ) {
		if ( ! is_string( $id ) || '' === $id ) {
			return false;
		}

		$id_parts = explode( '-', $id, 2 );
		if ( 2 !== count( $id_parts ) || '' === $id_parts[0] || ! is_numeric( $id_parts[1] ) ) {
			return false;
		}

		$timestamp    = (float) $id_parts[1];
		$current_time = microtime( true );
		if ( $timestamp < ( $current_time - ( 3 * MINUTE_IN_SECONDS ) ) || $timestamp > ( $current_time + ( 3 * MINUTE_IN_SECONDS ) ) ) {
			return false;
		}

		return Options::claim_hub_nonce( $timestamp );
	}

	/**
	 * Check signature hash of the request.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @param string $req_id         The request id as passed by Hub.
	 * @param string $json           The full json body that hash was created on.
	 * @param bool   $die_on_failure If set to false the function returns a bool.
	 *
	 * @return bool True on success.
	 */
	protected function validate_request_hash( $req_id, $json, $die_on_failure = true ) {
		if ( defined( '\WPMUDEV_IS_REMOTE' ) && ! \WPMUDEV_IS_REMOTE ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array(
						'code'    => 'remote_disabled',
						'message' => __( 'Remote calls are disabled in wp-config.php', 'wpmudev' ),
					)
				);
			} else {
				return false;
			}
		}

		if ( empty( $_SERVER['HTTP_WDP_AUTH'] ) ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array(
						'code'    => 'missing_auth_header',
						'message' => __( 'Missing authentication header', 'wpmudev' ),
					)
				);
			} else {
				return false;
			}
		}

		// phpcs:ignore
		$hash = $_SERVER['HTTP_WDP_AUTH'];

		// Validate auth hash.
		$is_valid = $this->validate_hash( $hash, $req_id, $json );

		if ( ! $is_valid ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array(
						'code'    => 'incorrect_auth',
						'message' => __( 'Incorrect authentication', 'wpmudev' ),
					)
				);
			}

			return false;
		}

		// Check nonce to prevent replay attacks.
		if ( ! $this->validate_nonce( $req_id ) ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array(
						'code'    => 'nonce_failed',
						'message' => __( 'Nonce check failed', 'wpmudev' ),
					)
				);
			} else {
				return false;
			}
		}

		if ( ! defined( '\WPMUDEV_IS_REMOTE' ) ) {
			define( 'WPMUDEV_IS_REMOTE', $is_valid );
		}

		return $is_valid;
	}

	/**
	 * Registers a Hub api action and callback for it.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $action   Action name.
	 * @param callable $callback The name of the function you wish to be called.
	 *
	 * @return void
	 */
	public function register_action( $action, $callback ) {
		$this->actions[ $action ] = $callback;
	}

	/**
	 * Logout of this site, removing it from the Hub.
	 *
	 * @since 1.0.0
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_logout( $params, $action ) {
		// Logout the site.
		API::get()->logout();

		wp_send_json_success();
	}

	/**
	 * Installs a list of plugins and themes by pid or slug.
	 *
	 * Handles multiple, but should normally be called with
	 * only one package at a time.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Force signature verifications on plugins/themes list requests
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_install( $params, $action ) {
		$errors       = array();
		$installed    = array();
		$items        = array_merge(
			isset( $params->plugins ) && is_array( $params->plugins ) ? $params->plugins : array(),
			isset( $params->themes ) && is_array( $params->themes ) ? $params->themes : array()
		);
		$only_wpmudev = ! empty( $items );
		foreach ( $items as $item ) {
			if ( ! is_numeric( $item ) ) {
				$only_wpmudev = false;
				break;
			}
		}

		// Set options.
		$options = array(
			// Activation is available only for plugins.
			'activate'  => ! empty( $params->is_activate ),
			// Overwrite if folder already exists.
			'overwrite' => ! isset( $params->overwrite ) || (bool) $params->overwrite,
		);

		// Process plugins.
		if ( ! empty( $params->plugins ) && is_array( $params->plugins ) ) {
			$verified = $this->verify_signed_data( $params->plugins, $params->signed_plugins ?? '' );
			if ( ! $verified ) {
				$errors[] = array(
					'file'    => 'plugins',
					'code'    => 'INS.99',
					'message' => 'Invalid plugins signature',
					'log'     => false,
				);
				// bail.
				wp_send_json_error( compact( 'installed', 'errors' ) );
			}
			foreach ( $params->plugins as $plugin ) {
				// Perform installation.
				$success = Upgrader::get()->install_signed( $plugin, 'plugin', $params->plugins, $params->signed_plugins ?? '', $options );

				// If successfully installed.
				if ( $success ) {
					$installed[] = array(
						'file' => $plugin,
						'log'  => array(),
					);
				} else {
					// Set error response.
					$errors[] = array(
						'file'    => $plugin,
						'code'    => Upgrader::get()->get_error()->get_error_code(),
						'message' => Upgrader::get()->get_error()->get_error_message(),
						'log'     => array(),
					);
				}
			}
		}

		// Process themes.
		if ( ! empty( $params->themes ) && is_array( $params->themes ) ) {
			$verified = $this->verify_signed_data( $params->themes, $params->signed_themes ?? '' );
			if ( ! $verified ) {
				$errors[] = array(
					'file'    => 'themes',
					'code'    => 'INS.99',
					'message' => 'Invalid themes signature',
					'log'     => false,
				);
				// bail.
				wp_send_json_error( compact( 'installed', 'errors' ) );
			}
			foreach ( $params->themes as $theme ) {
				// Perform installation.
				$success = Upgrader::get()->install_signed( $theme, 'theme', $params->themes, $params->signed_themes ?? '', $options );

				// Prepare success response.
				if ( $success ) {
					$installed[] = array(
						'file' => $theme,
						'log'  => array(),
					);
				} else {
					// Prepare error response.
					$errors[] = array(
						'file'    => $theme,
						'code'    => Upgrader::get()->get_error()->get_error_code(),
						'message' => Upgrader::get()->get_error()->get_error_message(),
						'log'     => array(),
					);
				}
			}
		}

		if ( count( $installed ) ) {
			if ( $only_wpmudev && ! defined( '\WPMUDEV_REMOTE_SKIP_SYNC' ) ) {
				define( 'WPMUDEV_REMOTE_SKIP_SYNC', true );
			}

			// If at least one project installed.
			wp_send_json_success( compact( 'installed', 'errors' ) );
		} else {
			// Errors only :(.
			wp_send_json_error( compact( 'installed', 'errors' ) );
		}
	}

	/**
	 * Activates a list of plugins and themes by slug.
	 *
	 * Handles multiple, but should normally be called with only one package at a time.
	 *
	 * @since 1.0.0
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_activate( $params, $action ) {
		if ( ! defined( '\WPMUDEV_REMOTE_SKIP_SYNC' ) ) {
			define( 'WPMUDEV_REMOTE_SKIP_SYNC', true );
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$errors    = array();
		$activated = array();

		// Process plugins.
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				// This checks if it's valid already.
				$result = activate_plugin( $plugin, '', is_multisite() );
				if ( is_wp_error( $result ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $result->get_error_code(),
						'message' => $result->get_error_message(),
					);
				} else {
					// Do Hub sync.
					Actions::get()->set_shutdown_sync();

					$activated[] = array( 'file' => $plugin );
				}
			}
		}

		// Process themes.
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( ! is_scalar( $theme ) || '' === trim( (string) $theme ) ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => 'invalid_theme',
						'message' => __( 'Invalid theme slug.', 'wpmudev' ),
					);
					continue;
				}

				// Check that this is a valid theme.
				$theme       = (string) $theme;
				$check_theme = wp_get_theme( $theme );

				if ( ! $check_theme->exists() ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $check_theme->errors()->get_error_code(),
						'message' => $check_theme->errors()->get_error_message(),
					);
					continue;
				}

				if ( is_multisite() ) {
					// Allow theme network wide.
					$allowed_themes           = (array) get_site_option( 'allowedthemes', array() );
					$allowed_themes[ $theme ] = true;
					update_site_option( 'allowedthemes', $allowed_themes );
				} else {
					switch_theme( $theme );
				}

				// Do Hub sync.
				Actions::get()->set_shutdown_sync();

				$activated[] = array( 'file' => $theme );
			}
		}

		if ( count( $activated ) ) {
			wp_send_json_success( compact( 'activated', 'errors' ) );
		} else {
			wp_send_json_error( compact( 'activated', 'errors' ) );
		}
	}

	/**
	 * Deactivates a list of plugins and themes by pid or slug.
	 *
	 * Handles multiple, but should normally be called with only one package at a time.
	 *
	 * @since 1.0.0
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_deactivate( $params, $action ) {
		if ( ! defined( '\WPMUDEV_REMOTE_SKIP_SYNC' ) ) {
			define( 'WPMUDEV_REMOTE_SKIP_SYNC', true );
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$errors      = array();
		$deactivated = array();

		// Process plugins.
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				// Check that it's a valid plugin.
				$valid = validate_plugin( $plugin );
				if ( is_wp_error( $valid ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $valid->get_error_code(),
						'message' => $valid->get_error_message(),
					);
					continue;
				}

				deactivate_plugins( $plugin, false, is_multisite() );

				// Do hub sync.
				Actions::get()->set_shutdown_sync();

				$deactivated[] = array( 'file' => $plugin );
			}
		}

		// Process themes.
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( ! is_scalar( $theme ) || '' === trim( (string) $theme ) ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => 'invalid_theme',
						'message' => __( 'Invalid theme slug.', 'wpmudev' ),
					);
					continue;
				}

				// Check that this is a valid theme.
				$theme       = (string) $theme;
				$check_theme = wp_get_theme( $theme );
				if ( ! $check_theme->exists() ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $check_theme->errors()->get_error_code(),
						'message' => $check_theme->errors()->get_error_message(),
					);
					continue;
				}

				if ( is_multisite() ) {
					// Disallow theme network wide.
					$allowed_themes = (array) get_site_option( 'allowedthemes', array() );
					unset( $allowed_themes[ $theme ] );
					update_site_option( 'allowedthemes', $allowed_themes );

					// Do hub sync.
					Actions::get()->set_shutdown_sync();

					$deactivated[] = array( 'file' => $theme );
				}
			}
		}

		if ( count( $deactivated ) ) {
			wp_send_json_success( compact( 'deactivated', 'errors' ) );
		} else {
			wp_send_json_error( compact( 'deactivated', 'errors' ) );
		}
	}

	/**
	 * Get a list of registered Hub actions that can be called.
	 *
	 * @since 1.0.0
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_registered( $params, $action ) {
		$actions = $this->actions;

		// Make class names human-readable.
		foreach ( $actions as $action => $callback ) {
			if ( is_array( $callback ) ) {
				$actions[ $action ] = array( get_class( $callback[0] ), $callback[1] );
			} elseif ( is_object( $callback ) ) {
				$actions[ $action ] = 'Closure';
			} else {
				$actions[ $action ] = trim( $callback ); // Cleans up lambda function names.
			}
		}

		wp_send_json_success( $actions );
	}

	/**
	 * Register actions that are used by the Dashboard plugin.
	 *
	 * These are the internal actions which act as API endpoints
	 * between Dash plugin and Hub for communication.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_internal_actions() {
		$actions = array(
			'registered_actions' => 'action_registered',
			'logout'             => 'action_logout',
			'activate'           => 'action_activate',
			'deactivate'         => 'action_deactivate',
			'install'            => 'action_install',
		);

		foreach ( $actions as $action => $callback ) {
			// Register action.
			$this->register_action( $action, array( $this, $callback ) );
		}
	}

	/**
	 * Registers custom Hub actions from other DEV plugins
	 *
	 * Other plugins should use the wdp_register_hub_action
	 * filter to add an item to the associative array as
	 * 'action_name' => 'callback'
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_plugin_actions() {
		/**
		 * Registers a Hub api action and callback for it.
		 *
		 * Support for existing hooks for Dash plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param string   $action   Action name.
		 * @param callable $callback The name of the function you wish to be called.
		 */
		$actions = apply_filters( 'wdp_register_hub_action', array() );

		/**
		 * Registers a Hub api action and callback for it.
		 *
		 * @since 1.0.0
		 *
		 * @param string   $action   Action name.
		 * @param callable $callback The name of the function you wish to be called.
		 */
		$actions = apply_filters( 'wpmudev_hub_connector_register_action', $actions );

		foreach ( $actions as $action => $callback ) {
			// Check action is not already registered and valid.
			if ( ! isset( $this->actions[ $action ] ) && is_callable( $callback ) ) {
				$this->register_action( $action, $callback );
			}
		}
	}

	/**
	 * Check if current request is from Hub.
	 *
	 * Currently, we need this class only for API requests from
	 * Hub. So checking for a 'wpmudev-hub' param is useful to
	 * identify the request.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function is_hub_request() {
		return ! empty( $_GET['wpmudev-hub'] ); // phpcs:ignore
	}

	/**
	 * Validate Signed Data
	 *
	 * @since 1.1.0
	 *
	 * @param mixed  $data        raw data.
	 * @param string $signed_data base64_encoded and json_encoded.
	 *
	 * @return bool
	 */
	public function verify_signed_data( $data, string $signed_data ): bool {
		try {
			if ( '' === $signed_data ) {
				return false;
			}

			$signature = base64_decode( $signed_data, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( false === $signature || '' === $signature ) {
				return false;
			}

			$public_key_file = apply_filters( 'wpmudev_hub_connector_remote_verify_sign_pub_file_path', plugin_dir_path( WPMUDEV_HUB_CONNECTOR_FILE ) . 'keys/dashboard.pub' );
			if ( ! is_readable( $public_key_file ) ) {
				return false;
			}

			$public_key = file_get_contents( $public_key_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( false === $public_key ) {
				return false;
			}

			$rsa = PublicKeyLoader::loadPublicKey( $public_key );
			if ( ! $rsa instanceof PublicKey ) {
				return false;
			}

			$rsa = $rsa->withHash( 'sha256' )->withMGFHash( 'sha256' )->withSaltLength( 32 )->withPadding( RSA::SIGNATURE_PSS );

			// signature is encoded. retain slashes as is from the data.
			if ( ! $rsa->verify( wp_json_encode( $data, JSON_UNESCAPED_SLASHES ), $signature ) ) {
				return false;
			}

			return true;
		} catch ( \Throwable $e ) {
			return false;
		}
	}
}

// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
