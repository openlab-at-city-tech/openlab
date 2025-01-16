<?php
/**
 * The Admin class to process admin side actions.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

/**
 * Class Admin
 */
class Admin {

	use Singleton;

	/**
	 * Plugin identifier.
	 *
	 * @var string $plugin
	 */
	protected $plugin = 'default';

	/**
	 * Admin page screen IDs.
	 *
	 * @var array Screen IDs.
	 */
	protected $screens = array();

	/**
	 * Extra query arguments for auth URLs.
	 *
	 * @var array Arguments.
	 */
	protected array $extra_args = array();

	/**
	 * Initialize admin class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// Add required classes to body.
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		// Register UI for a plugin.
		add_action( 'wpmudev_hub_connector_ui', array( $this, 'render' ) );
		// Process auth callback from hub.
		add_action( 'admin_init', array( $this, 'process_auth_callback' ) );
		// Include WDP ID header.
		add_filter( 'extra_plugin_headers', array( $this, 'include_wdp_id_header' ) );
	}

	/**
	 * Get extra query arguments for plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin Plugin identifier.
	 *
	 * @return array
	 */
	public function get_plugin_extra_args( string $plugin ): array {
		if ( ! empty( $plugin ) && ! empty( $this->extra_args[ $plugin ] ) ) {
			return $this->extra_args[ $plugin ];
		}

		return array();
	}

	/**
	 * Get screen IDs of plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin Plugin identifier.
	 *
	 * @return array
	 */
	public function get_plugin_screens( string $plugin ): array {
		if ( ! empty( $plugin ) && ! empty( $this->screens[ $plugin ] ) ) {
			return $this->screens[ $plugin ];
		}

		return array();
	}

	/**
	 * Set extra query arguments URL redirects.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin  Plugin identifier.
	 * @param array  $options Options.
	 *
	 * @return void
	 */
	public function set_plugin_options( string $plugin, array $options = array() ) {
		if ( empty( $plugin ) ) {
			return;
		}

		$options = wp_parse_args(
			$options,
			array(
				'screens'    => array(),
				'extra_args' => array(),
			)
		);

		// Set options.
		$this->set_plugin_screens( $plugin, $options['screens'] );
		$this->set_plugin_extra_args( $plugin, $options['extra_args'] );
	}

	/**
	 * Set admin screens to process.
	 *
	 * UI will be rendered only on these screens.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin  Plugin identifier.
	 * @param array  $screens Screen IDs.
	 *
	 * @return void
	 */
	public function set_plugin_screens( string $plugin, array $screens = array() ) {
		if ( ! empty( $screens ) ) {
			foreach ( $screens as $screen ) {
				$this->screens[ $screen ] = $plugin;
			}
		}
	}

	/**
	 * Set extra query arguments for login and register redirects.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin Plugin identifier.
	 * @param array  $args   Extra arguments.
	 *
	 * @return void
	 */
	public function set_plugin_extra_args( string $plugin, array $args = array() ) {
		$this->extra_args[ $plugin ] = $args;
	}

	/**
	 * Make sure we get WDP ID in plugins data.
	 *
	 * NOTE: We NEED TO keep using WDP ID as the
	 * key, because that's how the WP filter works.
	 *
	 * @since 1.0.0
	 *
	 * @param array $headers Existing headers.
	 *
	 * @return array
	 */
	public function include_wdp_id_header( $headers ) {
		// Include WDP ID.
		$headers[] = 'WDP ID';

		return $headers;
	}

	/**
	 * Get plugin identifier.
	 *
	 * Get an identifier for plugins to identify if the
	 * module was initialised by their plugin.
	 * If no identifier was being passed from plugin, it will have
	 * a default value "default".
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_id() {
		return $this->plugin;
	}

	/**
	 * Render hub connector UI.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin Plugin identifier.
	 */
	public function render( $plugin = 'default' ) {
		if ( ! empty( $plugin ) ) {
			$this->plugin = $plugin;
		}

		echo '<div id="wpmudev-hub-connector"></div>';

		// Enqueue assets.
		$this->enqueue_assets();
	}

	/**
	 * Add required classes to HTML body.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Class names.
	 */
	public function add_body_class( $classes ) {
		if ( $this->is_allowed_screen() ) {
			$classes .= ' ' . \WPMUDEV_HUB_CONNECTOR_SUI_VERSION;
		}

		return $classes;
	}

	/**
	 * Check if current admin page is one of the allowed pages.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_allowed_screen() {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			// Allowed screens.
			$screens = array_keys( $this->screens );

			// Check if current screen found in list.
			return ! empty( $screen->id ) && in_array( $screen->id, $screens, true );
		}

		return false;
	}

	/**
	 * Enqueue styles and scripts for UI.
	 *
	 * This should be called only when required.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		// SUI and custom styles.
		wp_enqueue_style(
			'hub-connector',
			plugin_dir_url( \WPMUDEV_HUB_CONNECTOR_FILE ) . 'assets/css/hub-connector.min.css',
			array(),
			\WPMUDEV_HUB_CONNECTOR_VERSION
		);

		// Script.
		wp_enqueue_script(
			'hub-connector',
			plugin_dir_url( \WPMUDEV_HUB_CONNECTOR_FILE ) . 'assets/js/hub-connector.min.js',
			array( 'wp-element', 'wp-i18n' ),
			\WPMUDEV_HUB_CONNECTOR_VERSION,
			true
		);

		// Get current URL.
		$current_url = remove_query_arg(
			array(
				'api_error',
				'set_apikey',
				'auth_error',
				'connection_error',
				'invalid_key',
				'site_limit_exceeded',
				'site_limit',
				'available_hosting_sites',
				'hub_connector_callback',
				'is_multi_auth',
				'user_apikey',
				'auth_nonce',
				'type',
			),
			Data::get()->current_url()
		);

		// Prepare redirect URL.
		$redirect_url = add_query_arg( array( 'hub_connector_callback' => 1 ), $current_url );

		$auth_nonce = wp_create_nonce( 'auth_nonce' );

		// Extra arguments.
		$extra_args = $this->get_plugin_extra_args_from_screen();

		// URL arguments for registration URL.
		$register_args = array(
			'signup'           => 'site-connect',
			'site_connect_url' => urlencode( add_query_arg( 'auth_nonce', $auth_nonce, $redirect_url ) ),
		);
		// Include extra args.
		if ( ! empty( $extra_args['register'] ) ) {
			$register_args = array_merge( $register_args, $extra_args['register'] );
		}

		// Vars for javascript.
		$vars = array(
			'api_url'           => rest_url( 'hub-connector/v1/' ),
			'nonce'             => wp_create_nonce( 'wp_rest' ),
			'is_syncing'        => false,
			'is_team_selection' => false,
			'has_access'        => current_user_can( 'manage_options' ),
			'is_logged_in'      => API::get()->is_logged_in(),
			'current_tab'       => isset( $_GET['hub_connector_callback'] ) ? 'login' : 'register',
			'login'             => array(
				'hub_auth_url'    => add_query_arg(
					$extra_args['auth'] ?? array(),
					API::get()->rest_url( 'site-authenticate' )
				),
				'team_auth_url'   => add_query_arg(
					$extra_args['team_auth'] ?? array(),
					API::get()->rest_url( 'site-authenticate-team' )
				),
				'google_auth_url' => add_query_arg(
					$extra_args['google_auth'] ?? array(),
					API::get()->rest_url( 'google-auth' )
				),
				'redirect_url'    => $redirect_url,
				'domain'          => Data::get()->network_site_url(),
				'auth_nonce'      => $auth_nonce,
				'current_url'     => $current_url,
				'forgot_url'      => add_query_arg(
					$extra_args['forgot_password'] ?? array(),
					Data::get()->server_url( 'forgot-password' )
				),
				'register_url'    => add_query_arg(
					$register_args,
					Data::get()->server_url( 'register' )
				),
			),
			'texts'             => $this->text_vars(),
		);

		/**
		 * Filter hook to modify script vars based on actions.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $vars   Vars.
		 * @param string $plugin Plugin identifier.
		 */
		$vars = apply_filters( 'wpmudev_hub_connector_localize_vars', $vars, $this->get_plugin_id() );

		// Localized vars.
		wp_localize_script( 'hub-connector', 'hubConnectorVars', $vars );
	}

	/**
	 * Process auth callback from the API.
	 *
	 * This method handles the dynamic script vars required
	 * for the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function process_auth_callback() {
		// Only for Hub connector callback.
		if ( ! isset( $_REQUEST['hub_connector_callback'] ) ) {
			return false;
		}

		// Should be capable to perform the actions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return $this->update_vars( array( 'has_access' => false ) );
		}

		// If not logged in.
		if ( ! API::get()->is_logged_in() ) {
			$error = $this->get_auth_error();
			if ( ! empty( $error ) ) {
				// Set auth errors.
				return $this->update_vars( array( 'auth_error' => $error ) );
			}

			// Auth nonce verification.
			if ( ! $this->verify_nonce() ) {
				// Failed. So no access.
				return $this->update_vars( array( 'has_access' => false ) );
			}

			// Is team selection callback.
			if ( $this->is_team_selection() ) {
				// Get the teams for API key.
				$teams = API::get()->get_hub_teams( trim( $_REQUEST['user_apikey'] ) );

				// Set team selection page vars.
				return $this->update_vars(
					array(
						'api_key'           => trim( $_REQUEST['user_apikey'] ),
						'hub_teams'         => $teams,
						'is_team_selection' => true,
					)
				);
			}

			// Is the set API key page.
			if ( ! empty( $_REQUEST['set_apikey'] ) ) {
				// Set API key.
				API::get()->set_api_key( trim( $_REQUEST['set_apikey'] ) );

				// Make sure to start syncing.
				return $this->update_vars(
					array(
						'is_syncing'   => true,
						'is_logged_in' => true,
					)
				);
			}
		}

		return false;
	}

	/**
	 * Get authentication error messages.
	 *
	 * Based on the error code, prepare different error messages.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_auth_error() {
		$error               = '';
		$reset_url           = Data::get()->server_url( 'wp-login.php?action=lostpassword' );
		$skip_trial_url      = Data::get()->server_url( 'hub/account/?skip_trial' );
		$trial_info_url      = Data::get()->server_url( 'docs/getting-started/how-free-trials-work/' );
		$websites_url        = Data::get()->server_url( 'hub2/' );
		$security_info_url   = Data::get()->server_url( 'manuals/hub-security/' );
		$support_url         = Data::get()->server_url( 'hub/support/' );
		$account_details_url = Data::get()->server_url( 'hub2/account/details/' );

		if ( isset( $_GET['api_error'] ) ) {
			// Get errors.
			$api_error  = esc_html( $_GET['api_error'] );
			$auth_error = isset( $_GET['auth_error'] ) ? esc_html( $_GET['auth_error'] ) : '';

			if ( 1 === (int) $api_error || 'auth' === $api_error ) {
				switch ( $auth_error ) {
					case 'google_linked':
						$error = sprintf(
						// translators: %s Account detail URL.
							__( 'You are currently using your Google account as your preferred login method. If you wish to login with your WPMU DEV email & password instead, please change the <strong>Login Method</strong> in <a href="%s" target="_blank">your WPMU DEV account</a>.', 'wpmudev' ),
							$account_details_url
						);
						break;
					case 'google_unlinked':
						$error = sprintf(
						// translators: %s Account detail URL.
							__( 'You are currently using your WPMU DEV email & password as your preferred login method. If you wish to login with your Google account instead, please change the <strong>Login Method</strong> in <a href="%s" target="_blank">your WPMU DEV account</a>.', 'wpmudev' ),
							$account_details_url
						);
						break;
					case 'reauth_google':
						$error = sprintf(
						// translators: %1$s Account detail URL, %2$s Reset URL.
							__( 'Due to security improvements, you will need to re-link your Google account in the Hub. Please log in with your WPMU DEV email & password for now, then set up your preferred <strong>Login Method</strong> in <a href="%1$s" target="_blank">your WPMU DEV account</a>. Forgot your password? You can <a href="%2$s" target="_blank">reset it here</a>.', 'wpmudev' ),
							$account_details_url,
							$reset_url
						);
						break;
					default:
						// Invalid credentials.
						$error = sprintf(
							'%s<br><a href="%s" target="_blank">%s</a>',
							esc_html__( 'Your login details were incorrect. Please make sure you\'re using your WPMU DEV email and password and try again.', 'wpmudev' ),
							$reset_url,
							esc_html__( 'Forgot your password?', 'wpmudev' )
						);
						break;
				}
			} else {
				switch ( $api_error ) {
					case 'in_trial':
						$error = sprintf(
							'%s<br><a href="%s" target="_blank">%s</a>',
							sprintf(
							// translators: %1$s Rest URL, %2$s Upgrade URL, %3$s Trial URL.
								__( 'This domain has previously been registered with us by the user %1$s. To use WPMU DEV on this domain, you can either log in with the original account (you can <a target="_blank" href="%2$s">reset your password</a>) or <a target="_blank" href="%3$s">upgrade your trial</a> to a full membership. Trial accounts can\'t use previously registered domains - <a target="_blank" href="%4$s">here\'s why</a>.', 'wpmudev' ),
								'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // phpcs:ignore
								$reset_url,
								$skip_trial_url,
								$trial_info_url
							),
							$support_url,
							__( 'Contact support if you need further assistance &raquo;', 'wpmudev' )
						);
						break;
					case 'already_registered':
						$error = sprintf(
						// translators: %1$d Account name, %2$s Security info, %3$s Hub URL, %4$s Support URL.
							__( 'This site is currently registered to %1$s. For <a target="_blank" href="%2$s">security reasons</a> they will need to go to the <a target="_blank" href="%3$s">WPMU DEV Hub</a> and remove this domain before you can log in. If you do not have access to that account, and have no way of contacting that user, please <a target="_blank" href="%4$s">contact support for assistance</a>.', 'wpmudev' ),
							'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // phpcs:ignore.
							$security_info_url,
							$websites_url,
							$support_url
						);
						break;
					case 'banned_account':
						$error = sprintf(
						// translators: %s Support URL.
							__( 'This domain cannot be registered to your WPMU DEV account.<br><a href="%s">Contact Accounts & Billing if you need further assistance »</a>', 'wpmudev' ),
							Data::get()->server_url( 'hub2/#ask-question' )
						);
						break;
					case 'invalid_nonce':
					case 'invalid_double_submit_cookie':
					case 'invalid_google_creds':
					case '':
						$error = __( 'Google login failed. Please try again.', 'wpmudev' );
						break;
					default:
						// This in case we add new error types in the future.
						$error = __( 'Unknown error. Please update the WPMU DEV Dashboard plugin and try again.', 'wpmudev' );
						break;
				}
			}
		} elseif ( ! empty( $_REQUEST['connection_error'] ) ) {
			// Variable `$connection_error` is set by the UI function `render_dashboard`.
			$error = sprintf(
				'%s<br>%s<br><em>%s</em>',
				__( 'Your server had a problem connecting to WPMU DEV. Please try again.', 'wpmudev' ),
				__( 'If this problem continues, please contact your host with this error message and ask:', 'wpmudev' ),
				sprintf(
				// translators: url to API.
					__( '"Is PHP on my server properly configured to be able to contact %s with a POST HTTP request via fsockopen or CURL?"', 'wpmudev' ),
					Data::get()->server_url()
				)
			);
		} elseif ( ! empty( $_REQUEST['invalid_key'] ) ) {
			// Invalid API key.
			$error = __( 'Your API Key was invalid. Please try again.', 'wpmudev' );
		}

		/**
		 * Filter to modify auth error text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $error  Error message.
		 * @param string $plugin Plugin identifier.
		 */
		return apply_filters( 'wpmudev_hub_connector_get_auth_error', $error, $this->get_plugin_id() );
	}

	/**
	 * Check if current request is team selection after auth.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_team_selection() {
		return (
			// Should have multi auth param.
			isset( $_REQUEST['is_multi_auth'] )
			&& 1 === (int) $_REQUEST['is_multi_auth']
			// Should have an API key.
			&& ! empty( $_REQUEST['user_apikey'] )
		);
	}

	/**
	 * Verify auth nonce.
	 *
	 * After or during the hub auth process, we need to make sure
	 * the nonce is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function verify_nonce() {
		return wp_verify_nonce( ( isset( $_REQUEST['auth_nonce'] ) ? $_REQUEST['auth_nonce'] : '' ), 'auth_nonce' );
	}

	/**
	 * Script vars to assign.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Script vars.
	 *
	 * @return bool
	 */
	private function update_vars( $data ) {
		// Update the script vars array.
		add_filter(
			'wpmudev_hub_connector_localize_vars',
			function ( $vars ) use ( $data ) {
				return array_merge( $vars, $data );
			}
		);

		return true;
	}

	/**
	 * Localized text strings for UI.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function text_vars() {
		$strings = array(
			'login_title'  => __( 'Let’s connect your site', 'wpmudev' ),
			'login_desc'   => __( 'To manage your site from The Hub, log in with your WPMU DEV account email and password.', 'wpmudev' ),
			'sync_error'   => __( 'Could not sync with Hub. Please try again.', 'wpmudev' ),
			'sync_desc1'   => __( 'The Hub connects WPMU DEV to your website and unlocks all the power of our all-in-one platform services.', 'wpmudev' ),
			'sync_desc2'   => __( 'Once your website is connected to the Hub, you will be able to perform updates, managing services - all from one place.', 'wpmudev' ),
			'sync_loading' => __( 'Please wait a few moments while we connect your website.', 'wpmudev' ),
			'team_title'   => __( 'Choose The Hub Team', 'wpmudev' ),
			'team_desc'    => __( 'We\'ve noticed that you are a member of multiple teams in The Hub. Which team would you like to connect to this site?', 'wpmudev' ),
			'team_error'   => __( 'Unknown API error occurred. Please try again.', 'wpmudev' ),
		);

		/**
		 * Filter hook to modify text string vars.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $strings Vars.
		 * @param string $plugin  Plugin identifier.
		 */
		return apply_filters( 'wpmudev_hub_connector_localize_text_vars', $strings, $this->get_plugin_id() );
	}

	/**
	 * Get extra arguments for plugin.
	 *
	 * @return array
	 */
	private function get_plugin_extra_args_from_screen(): array {
		$screen = get_current_screen();
		// We need screen ID.
		if ( empty( $screen->id ) ) {
			return array();
		}

		$screens = $this->screens;
		// Current screen is not allowed.
		if ( ! isset( $screens[ $screen->id ] ) ) {
			return array();
		}

		// Get plugin ID.
		$plugin_id = $screens[ $screen->id ];
		// Get plugin's extra args.
		return $this->get_plugin_extra_args( $plugin_id );
	}
}
