<?php
/**
 * Class Cron
 *
 * @package TEC\Common\TrustedLogin\Client
 */

namespace TEC\Common\TrustedLogin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cron
 */
final class Cron {

	/**
	 * Config instance.
	 *
	 * @var \TEC\Common\TrustedLogin\Config
	 */
	private $config;

	/**
	 * The hook name for the cron job.
	 *
	 * @var string
	 */
	private $hook_name;

	/**
	 * Logging instance.
	 *
	 * @var null|\TEC\Common\TrustedLogin\Logging $logging
	 */
	private $logging;

	/**
	 * Cron constructor.
	 *
	 * @param Config  $config Config instance.
	 * @param Logging $logging Logging instance.
	 */
	public function __construct( Config $config, Logging $logging ) {
		$this->config  = $config;
		$this->logging = $logging;

		$this->hook_name = 'trustedlogin/' . $this->config->ns() . '/access/revoke';
	}

	/**
	 * Add hooks to revoke access using cron.
	 *
	 * The cron job is scheduled by {@see schedule()} and revoked by {@see revoke()}.
	 */
	public function init() {
		add_action( $this->hook_name, array( $this, 'revoke' ), 1 );
	}

	/**
	 * Schedule a cron job to revoke access for a specific support user.
	 *
	 * @param int    $expiration_timestamp The timestamp when the cron job should run.
	 * @param string $identifier_hash The unique identifier for the WP_User created {@see Encryption::get_random_hash()}.
	 *
	 * @return bool True if the cron job was scheduled, false if not.
	 */
	public function schedule( $expiration_timestamp, $identifier_hash ) {

		$hash = Encryption::hash( $identifier_hash );

		if ( is_wp_error( $hash ) ) {
			$this->logging->log( $hash, __METHOD__ );

			return false;
		}

		$args = array( $hash );

		/**
		 * Whether the event was scheduled.
		 *
		 * @var false|\WP_Error $scheduled_expiration
		 */
		$scheduled_expiration = wp_schedule_single_event( $expiration_timestamp, $this->hook_name, $args );

		if ( is_wp_error( $scheduled_expiration ) ) {
			$this->logging->log( 'Scheduling expiration failed: ' . sanitize_text_field( $scheduled_expiration->get_error_message() ), __METHOD__, 'error' );

			return false;
		}

		$this->logging->log( 'Scheduled Expiration succeeded for identifier ' . $identifier_hash, __METHOD__, 'info' );

		return $scheduled_expiration;
	}

	/**
	 * Reschedule a cron job to revoke access for a specific support user.
	 *
	 * @param int    $expiration_timestamp The timestamp when the cron job should run.
	 * @param string $site_identifier_hash The unique identifier for the WP_User created {@see Encryption::get_random_hash()}.
	 *
	 * @return bool
	 */
	public function reschedule( $expiration_timestamp, $site_identifier_hash ) {

		$hash = Encryption::hash( $site_identifier_hash );

		if ( is_wp_error( $hash ) ) {
			$this->logging->log( $hash, __METHOD__ );

			return false;
		}

		$unschedule_expiration = wp_clear_scheduled_hook( $this->hook_name, array( $hash ) );

		switch ( $unschedule_expiration ) {
			case false:
				$this->logging->log( sprintf( 'Could not clear scheduled hook for %s', $this->hook_name ), __METHOD__, 'error' );
				return false;
			case 0:
				$this->logging->log( sprintf( 'Cron event not found for %s', $this->hook_name ), __METHOD__, 'error' );
				return false;
		}

		return $this->schedule( $expiration_timestamp, $site_identifier_hash );
	}

	/**
	 * Hooked Action: Revokes access for a specific support user
	 *
	 * @since 1.0.0
	 *
	 * @param string $identifier_hash Identifier hash for the user associated with the cron job.
	 *
	 * @return void
	 */
	public function revoke( $identifier_hash ) {

		$this->logging->log( 'Running cron job to disable user. ID: ' . $identifier_hash, __METHOD__, 'notice' );

		$client = new Client( $this->config, false );

		$client->revoke_access( $identifier_hash );
	}
}
