<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Handles WP Rest API Keys and Key Generation
 *
 * @package DLM_Key_Generation
 */
class DLM_Key_Generation {
	/**
	 * Holds the class object.
	 *
	 * @since 5.0.0
	 */
	public static $instance;

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 */
	private function __construct() {
		// Load admin hooks.
		$this->load_admin_hooks();
		// Load frontend hooks.
		$this->load_frontend_hooks();
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Key_Generation object.
	 * @since 5.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Key_Generation ) ) {
			self::$instance = new DLM_Key_Generation();
		}

		return self::$instance;
	}

	/**
	 * Load admin hooks.
	 *
	 * @since 5.0.0
	 */
	private function load_admin_hooks() {
		// Add the API keys section to the settings page.
		add_filter( 'dlm_settings', array( $this, 'add_api_section' ) );
	}

	/**
	 * Load frontend hooks.
	 *
	 * @since 5.0.0
	 */
	private function load_frontend_hooks() {
	}

	/**
	 * Generate new API keys for a user
	 *
	 * @param  int   $user_id     User ID the key is being generated for.
	 * @param  bool  $regenerate  Regenerate the key for the user.
	 *
	 * @return boolean True if (re)generated successfully, false otherwise.
	 * @since 5.0.0
	 *
	 */
	public function generate_api_key( $user_id = 0, $regenerate = false ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		$public_key = $this->get_user_public_key( $user_id );

		if ( empty( $public_key ) || true === $regenerate ) {
			$new_public_key = $this->generate_public_key( $user->user_email );
			$new_secret_key = $this->generate_private_key( $user->ID );
		} else {
			return false;
		}

		if ( true === $regenerate ) {
			$this->revoke_api_key( $user->ID );
		}

		$api_key = new DLM_API_Key();
		$api_key->set_public_key( $new_public_key );
		$api_key->set_secret_key( $new_secret_key );
		$api_key->set_user_id( $user->ID );
		$api_key->create_key();

		return true;
	}

	/**
	 * Generate the public key for a user
	 *
	 * @param  string  $user_email  The user's email address.
	 *
	 * @return string
	 * @since  5.0.0
	 *
	 */
	public function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );

		return $public;
	}

	/**
	 * Generate the secret key for a user
	 *
	 * @param  int  $user_id  The user's ID.
	 *
	 * @return string
	 * @since 5.0.0
	 *
	 */
	public function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );

		return $secret;
	}

	/**
	 * Revoke a users API keys
	 *
	 * @param  int  $user_id  User ID of user to revoke key for.
	 *
	 * @return string
	 * @throws Exception
	 * @since 5.0.0
	 *
	 */
	public function revoke_api_key( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		$public_key = $this->get_user_public_key( $user_id );
		if ( ! empty( $public_key ) ) {
			$api_key = new DLM_API_Key();
			if ( $api_key->get_key_by_public_key( $public_key ) ) {
				$api_key->delete_key();
				delete_transient( md5( 'dlm_api_user_public_key' . $user_id ) );
				delete_transient( md5( 'dlm_api_user_secret_key' . $user_id ) );
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Get a user's public key.
	 *
	 * @param  int  $user_id  User ID.
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public function get_user_public_key( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}
		$cache_key       = md5( 'dlm_api_user_public_key' . $user_id );
		$user_public_key = get_transient( $cache_key );
		if ( empty( $user_public_key ) ) {
			$sql             = $wpdb->prepare( "SELECT public_key FROM {$wpdb->prefix}dlm_api_keys WHERE user_id = %s", absint( $user_id ) );
			$user_public_key = $wpdb->get_var( $sql );
			set_transient( $cache_key, $user_public_key, HOUR_IN_SECONDS );
		}

		return $user_public_key;
	}

	/**
	 * Get a user's secret key.
	 *
	 * @param  int  $user_id  User ID.
	 *
	 * @return string
	 * @since 5.0.0
	 */
	public function get_user_secret_key( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return '';
		}

		$cache_key       = md5( 'dlm_api_user_secret_key' . $user_id );
		$user_secret_key = get_transient( $cache_key );

		if ( empty( $user_secret_key ) ) {
			$sql             = $wpdb->prepare( "SELECT secret_key FROM {$wpdb->prefix}dlm_api_keys WHERE user_id = %s", absint( $user_id ) );
			$user_secret_key = $wpdb->get_var( $sql );
			set_transient( $cache_key, $user_secret_key, HOUR_IN_SECONDS );
		}

		return $user_secret_key;
	}

	/**
	 * Add setting field.
	 *
	 * @param  array  $settings  Array of settings.
	 *
	 * @since 5.0.0
	 */
	public function add_api_section( $settings ) {

		$settings['advanced']['sections']['rest'] = array(
			'title'         => __( 'REST API', 'download-monitor' ),
			'fields'        => array(
				array(
					'name'     => '',
					'type'     => 'api_keys_table',
					'priority' => 90,
				),
			),
			'show_upsells'  => false,
			'contend_class' => 'dlm-content-tab-full',
		);

		return $settings;
	}
}
