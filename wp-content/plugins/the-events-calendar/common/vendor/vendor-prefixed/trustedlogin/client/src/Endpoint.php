<?php
/**
 * Class Endpoint
 *
 * @package TEC\Common\TrustedLogin\Client
 *
 * @copyright 2021 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

use WP_Error;

/**
 * Class Endpoint
 */
class Endpoint {

	/**
	 * The query string parameter used to revoke users.
	 *
	 * @var string
	 */
	const REVOKE_SUPPORT_QUERY_PARAM = 'revoke-tl';

	/**
	 * Site option used to track whether permalinks have been flushed.
	 *
	 * @var string
	 */
	const PERMALINK_FLUSH_OPTION_NAME = 'tl_permalinks_flushed';

	/**
	 * Expected value of $_POST['action'] before adding the endpoint and starting a login flow.
	 *
	 * @var string
	 */
	const POST_ACTION_VALUE = 'trustedlogin';

	/**
	 * The $_POST key in the TrustedLogin request related to the action being performed.
	 *
	 * @var string
	 */
	const POST_ACTION_KEY = 'action';

	/**
	 * The $_POST key in the TrustedLogin request that contains the value of the expected endpoint.
	 *
	 * @var string
	 */
	const POST_ENDPOINT_KEY = 'endpoint';

	/**
	 * The $_POST key in the TrustedLogin request related to the action being performed.
	 *
	 * @var string
	 */
	const POST_IDENTIFIER_KEY = 'identifier';

	/**
	 * Config instance.
	 *
	 * @var Config $config
	 */
	private $config;

	/**
	 * The namespaced setting name for storing part of the auto-login endpoint
	 *
	 * @example `tl_{vendor/namespace}_endpoint`.
	 *
	 * @var string $option_name
	 */
	private $option_name;

	/**
	 * Logging instance.
	 *
	 * @var Logging $logging
	 */
	private $logging;

	/**
	 * Endpoint constructor.
	 *
	 * @param Config  $config  Config instance.
	 * @param Logging $logging Logging instance.
	 */
	public function __construct( Config $config, Logging $logging ) {

		$this->config  = $config;
		$this->logging = $logging;

		/**
		 * Filter: Set endpoint setting name
		 *
		 * @since 1.0.0
		 *
		 * @param string $option_name
		 * @param Config $config
		 */
		$this->option_name = apply_filters(
			'trustedlogin/' . $config->ns() . '/options/endpoint',
			'tl_' . $config->ns() . '_endpoint',
			$config
		);
	}

	/**
	 * Add hooks to initialize the endpoint.
	 */
	public function init() {

		if ( did_action( 'init' ) ) {
			$this->add();
		} else {
			add_action( 'init', array( $this, 'add' ) );
		}

		add_action( 'template_redirect', array( $this, 'maybe_login_support' ), 99 );
		add_action( 'init', array( $this, 'maybe_revoke_support' ), 100 );
		add_action( 'admin_init', array( $this, 'maybe_revoke_support' ), 100 );
	}

	/**
	 * Check if the endpoint is hit and has a valid identifier before automatically logging in support agent.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_login_support() {

		// The user's already logged-in; don't override that login.
		if ( is_user_logged_in() ) {
			return;
		}

		$request = $this->get_trustedlogin_request();

		// Not a TrustedLogin request.
		if ( ! $request ) {
			return;
		}

		$endpoint = $this->get();

		// The expected endpoint doesn't match the one in the request.
		if ( $endpoint !== $request[ self::POST_ENDPOINT_KEY ] ) {
			return;
		}

		// The sanitized, unhashed identifier for the support user.
		$user_identifier = $request[ self::POST_IDENTIFIER_KEY ];

		if ( empty( $user_identifier ) ) {
			return;
		}

		/**
		 * Runs before the support user is (maybe) logged-in, but after the endpoint is verified.
		 *
		 * @param string $user_identifier Unique identifier for support user, sanitized using {@see sanitize_text_field}.
		 */
		do_action( 'trustedlogin/' . $this->config->ns() . '/login/before', $user_identifier );

		$security_checks = new SecurityChecks( $this->config, $this->logging );

		// Before logging-in support, let's make sure the site isn't locked-down or that this request is flagged.
		$is_verified = $security_checks->verify( $user_identifier );

		if ( is_wp_error( $is_verified ) ) {

			/**
			 * Runs after the identifier fails security checks.
			 *
			 * @param string $user_identifier Unique identifier for support user.
			 * @param WP_Error $is_verified The error encountered when verifying the identifier.
			 */
			do_action( 'trustedlogin/' . $this->config->ns() . '/login/refused', $user_identifier, $is_verified );

			return;
		}

		$support_user = new SupportUser( $this->config, $this->logging );

		$is_logged_in = $support_user->maybe_login( $user_identifier );

		if ( is_wp_error( $is_logged_in ) ) {

			/**
			 * Runs after the support user fails to log in
			 *
			 * @param string $user_identifier Unique Identifier for support user.
			 * @param WP_Error $is_logged_in The error encountered when logging-in.
			 */
			do_action( 'trustedlogin/' . $this->config->ns() . '/login/error', $user_identifier, $is_logged_in );

			return;
		}

		/**
		 * Runs after the support user is logged-in.
		 *
		 * @param string $user_identifier Unique Identifier for support user.
		 */
		do_action( 'trustedlogin/' . $this->config->ns() . '/login/after', $user_identifier );

		wp_safe_redirect( admin_url() );

		exit();
	}


	/**
	 * Hooked Action to maybe revoke support if the request SupportUser::ID_QUERY_PARAM equals the namespace.
	 *
	 * Can optionally check for request SupportUser::ID_QUERY_PARAM for revoking a specific user by their identifier.
	 *
	 * @since 1.0.0
	 */
	public function maybe_revoke_support() {
		$revoke_param = Utils::get_request_param( self::REVOKE_SUPPORT_QUERY_PARAM );

		if ( $this->config->ns() !== $revoke_param ) {
			return;
		}

		$nonce = Utils::get_request_param( '_wpnonce' );

		if ( ! $nonce ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, self::REVOKE_SUPPORT_QUERY_PARAM ) ) {
			$this->logging->log( 'Removing user failed: Nonce expired.', __METHOD__, 'error' );
			return;
		}

		$support_user = new SupportUser( $this->config, $this->logging );

		// Allow namespaced support team to revoke their own users.
		$support_team = current_user_can( $support_user->role->get_name() );

		// As well as existing users who can delete other users.
		$can_delete_users = current_user_can( 'delete_users' );

		if ( ! $support_team && ! $can_delete_users ) {
			wp_safe_redirect( home_url() );
			return;
		}

		$user_identifier = Utils::get_request_param( SupportUser::ID_QUERY_PARAM );

		if ( ! $user_identifier ) {
			$user_identifier = 'all';
		}

		/**
		 * Trigger action to revoke access based on Support User identifier.
		 *
		 * @used-by Cron::revoke
		 *
		 * @param string $user_identifier Unique ID for TrustedLogin support user or "all".
		 */
		do_action( 'trustedlogin/' . $this->config->ns() . '/access/revoke', $user_identifier );

		$should_be_deleted = $support_user->get( $user_identifier );

		if ( ! empty( $should_be_deleted ) ) {
			$this->logging->log( 'User #' . $should_be_deleted->ID . ' was not removed', __METHOD__, 'error' );
			return; // Don't trigger `access_revoked` if anything fails.
		}

		/**
		 * Only triggered when all access has been successfully revoked and no users exist with identifier $identifier.
		 *
		 * @param string $user_identifier Unique TrustedLogin ID for the Support User or "all"
		 */
		do_action( 'trustedlogin/' . $this->config->ns() . '/admin/access_revoked', $user_identifier );
	}

	/**
	 * Hooked Action: Add a unique endpoint to WP if a support agent exists
	 *
	 * @since 1.0.0
	 * @see Endpoint::init() Called via `init` hook
	 */
	public function add() {

		// Only add the endpoint if a TrustedLogin request is being made.
		if ( ! $this->get_trustedlogin_request() ) {
			return;
		}

		$endpoint = $this->get();

		if ( ! $endpoint ) {
			return;
		}

		add_rewrite_endpoint( $endpoint, EP_ROOT );

		$this->logging->log( "Endpoint {$endpoint} added.", __METHOD__, 'debug' );

		if ( get_site_option( self::PERMALINK_FLUSH_OPTION_NAME ) ) {
			return;
		}

		flush_rewrite_rules( false );

		$this->logging->log( 'Rewrite rules flushed.', __METHOD__, 'info' );

		$updated_option = update_site_option( self::PERMALINK_FLUSH_OPTION_NAME, 1 );

		if ( false === $updated_option ) {
			$this->logging->log( 'Permalink flush option was not properly set.', 'warning' );
		}
	}

	/**
	 * Get the site option value at {@see option_name}
	 *
	 * @return string
	 */
	public function get() {
		return (string) get_site_option( $this->option_name );
	}

	/**
	 * Returns sanitized data from a TrustedLogin login $_POST request.
	 *
	 * Note: This is not a security check. It is only used to determine whether the request contains the expected keys.
	 *
	 * @since 1.1
	 *
	 * @return false|array{action:string, endpoint:string, identifier: string} If false, the request is not from TrustedLogin. If the request is from TrustedLogin, an array with the posted keys, santiized.
	 */
	private function get_trustedlogin_request() {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST[ self::POST_ACTION_KEY ], $_POST[ self::POST_ENDPOINT_KEY ], $_POST[ self::POST_IDENTIFIER_KEY ] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( self::POST_ACTION_VALUE !== $_POST[ self::POST_ACTION_KEY ] ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$_sanitized_post_data = array_map( 'sanitize_text_field', $_POST );

		// Return only the expected keys.
		return array(
			self::POST_ACTION_KEY     => $_sanitized_post_data[ self::POST_ACTION_KEY ],
			self::POST_ENDPOINT_KEY   => $_sanitized_post_data[ self::POST_ENDPOINT_KEY ],
			self::POST_IDENTIFIER_KEY => $_sanitized_post_data[ self::POST_IDENTIFIER_KEY ],
		);
	}

	/**
	 * Generate the secret_id parameter as a hash of the endpoint with the identifier.
	 *
	 * @param string $site_identifier_hash The site identifier hash.
	 * @param string $endpoint_hash Optional. The hash of the endpoint. If not provided, it will be generated.
	 *
	 * @return string|WP_Error This hash will be used as an identifier in TrustedLogin SaaS. Or something went wrong.
	 */
	public function generate_secret_id( $site_identifier_hash, $endpoint_hash = '' ) {

		if ( empty( $endpoint_hash ) ) {
			$endpoint_hash = $this->get_hash( $site_identifier_hash );
		}

		if ( is_wp_error( $endpoint_hash ) ) {
			return $endpoint_hash;
		}

		return Encryption::hash( $endpoint_hash . $site_identifier_hash );
	}

	/**
	 * Generate the endpoint parameter as a hash of the site URL along with the identifier.
	 *
	 * @param string $site_identifier_hash The site identifier hash, used to generate the endpoint hash.
	 *
	 * @return string|WP_Error This hash will be used as the first part of the URL and also a part of $secret_id.
	 */
	public function get_hash( $site_identifier_hash ) {
		return Encryption::hash( get_site_url() . $site_identifier_hash );
	}

	/**
	 * Updates the site's endpoint to listen for logins. Flushes rewrite rules after updating.
	 *
	 * @param string $endpoint The endpoint to add to the site.
	 *
	 * @return bool True: updated; False: didn't change, or didn't update
	 */
	public function update( $endpoint ) {

		$updated = update_site_option( $this->option_name, $endpoint );

		update_site_option( self::PERMALINK_FLUSH_OPTION_NAME, 0 );

		return $updated;
	}

	/**
	 * Deletes the site's endpoint and soft-flushes rewrite rules.
	 *
	 * @return void
	 */
	public function delete() {

		if ( ! get_site_option( $this->option_name ) ) {
			$this->logging->log( 'Endpoint not deleted because it does not exist.', __METHOD__, 'info' );

			return;
		}

		delete_site_option( $this->option_name );

		flush_rewrite_rules( false );

		update_site_option( self::PERMALINK_FLUSH_OPTION_NAME, 0 );

		$this->logging->log( 'Endpoint removed & rewrites flushed', __METHOD__, 'info' );
	}
}
