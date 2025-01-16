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
		add_action( 'init', array( $this, 'run_request' ), 999 );
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
		if ( empty( $hash ) || empty( $id ) || empty( $json ) ) {
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
	 *
	 * @param string $id Request ID.
	 *
	 * @return bool
	 */
	public function validate_nonce( $id ) {
		// Validation.
		if ( empty( $id ) ) {
			return false;
		}

		// Get nonce from ID.
		list( $id, $timestamp ) = explode( '-', $id );

		// Get saved nonce.
		$nonce = Options::get( 'hub_nonce' );

		if ( floatval( $timestamp ) > $nonce ) {
			// If valid nonce, save it.
			Options::set( 'hub_nonce', floatval( $timestamp ) );

			return true;
		}

		return false;
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

		if ( ! $is_valid && $die_on_failure ) {
			wp_send_json_error(
				array(
					'code'    => 'incorrect_auth',
					'message' => __( 'Incorrect authentication', 'wpmudev' ),
				)
			);
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
		API::get()->logout( false );

		wp_send_json_success();
	}

	/**
	 * Installs a list of plugins and themes by pid or slug.
	 *
	 * Handles multiple, but should normally be called with
	 * only one package at a time.
	 *
	 * @since 1.0.0
	 *
	 * @param object $params Parameters passed in json body.
	 * @param string $action The action name that was called.
	 *
	 * @return void
	 */
	public function action_install( $params, $action ) {
		$errors    = array();
		$installed = array();

		// Set options.
		$options = array(
			// Activation is available only for plugins.
			'activate'  => ! empty( $params->is_activate ),
			// Overwrite if folder already exists.
			'overwrite' => ! isset( $params->overwrite ) || (bool) $params->overwrite,
		);

		// Process plugins.
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				// Perform installation.
				$success = Upgrader::get()->install( $plugin, 'plugin', $options );

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
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				// Perform installation.
				$success = Upgrader::get()->install( $theme, 'theme', $options );

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
				// Check that this is a valid theme.
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
					$allowed_themes           = get_site_option( 'allowedthemes' );
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
				// Check that this is a valid theme.
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
					$allowed_themes = get_site_option( 'allowedthemes' );
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

		wp_send_json_success();
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
}
