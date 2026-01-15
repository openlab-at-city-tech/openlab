<?php
/**
 * Class Envelope
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

use WP_Error;

/**
 * Class Envelope
 */
final class Envelope {

	/**
	 * Config instance.
	 *
	 * @var Config $config
	 */
	private $config;

	/**
	 * Encryption instance.
	 *
	 * @var Encryption
	 */
	private $encryption;

	/**
	 * API key set in software.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Envelope constructor.
	 *
	 * @param Config     $config Config instance.
	 * @param Encryption $encryption Encryption instance.
	 */
	public function __construct( Config $config, Encryption $encryption ) {
		$this->config     = $config;
		$this->api_key    = $this->config->get_setting( 'auth/api_key' );
		$this->encryption = $encryption;
	}

	/**
	 * Retrieves the envelope to be sent to the TrustedLogin server.
	 *
	 * @param string $secret_id The unique identifier for this TrustedLogin authorization. {@see Endpoint::generate_secret_id}.
	 * @param string $site_identifier_hash The unique identifier for the WP_User.
	 * @param string $access_key Shareable access key. {@see SiteAccess::get_access_key()}.
	 *
	 * @return array|WP_Error {
	 *   The envelope for the TrustedLogin server.
	 *
	 *   @type string $secretId The unique identifier for this TrustedLogin authorization.
	 *   @type string $identifier The encrypted identifier of support user.
	 *   @type string $siteUrl The site URL.
	 *   @type string $publicKey The API key for the site.
	 *   @type string $accessKey Shareable access key.
	 *   @type int $wpUserId The WordPress User ID.
	 *   @type int $expiresAt The expiration timestamp (GMT).
	 *   @type string $version The version of the TrustedLogin client.
	 *   @type string $nonce The nonce for the envelope.
	 *   @type string $clientPublicKey The {@see sodium_crypto_box_publickey} public key.
	 *   @type array $metaData Custom metadata to be synced via TrustedLogin.
	 * }
	 */
	public function get( $secret_id, $site_identifier_hash, $access_key = '' ) {

		if ( ! is_string( $secret_id ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new WP_Error( 'secret_not_string', 'The secret ID must be a string:' . print_r( $secret_id, true ) );
		}

		if ( ! is_string( $site_identifier_hash ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new WP_Error( 'site_identifier_not_string', 'The site identifier must be a string:' . print_r( $site_identifier_hash, true ) );
		}

		if ( ! is_string( $access_key ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			return new WP_Error( 'access_key_not_string', 'The access key must be a string: ' . print_r( $access_key, true ) );
		}

		if ( ! function_exists( 'sodium_bin2hex' ) ) {
			return new WP_Error( 'sodium_bin2hex_not_available', 'The sodium_bin2hex function is not available.' );
		}

		$e_keys = $this->encryption->generate_keys();

		if ( is_wp_error( $e_keys ) ) {
			return $e_keys;
		}

		$nonce = $this->encryption->get_nonce();

		if ( is_wp_error( $nonce ) ) {
			return $nonce;
		}

		$encrypted_identifier = $this->encryption->encrypt( $site_identifier_hash, $nonce, $e_keys->private_key );

		if ( is_wp_error( $encrypted_identifier ) ) {
			return $encrypted_identifier;
		}

		/**
		 * Adds custom metadata to be synced via TrustedLogin
		 *
		 * WARNING: Metadata is transferred and stored in plain text, and **must not contain any sensitive or identifiable information**!
		 *
		 * @since 1.0.0
		 *
		 * @param array  $metadata
		 * @param Config $config Current TrustedLogin configuration
		 */
		$metadata = apply_filters( 'trustedlogin/' . $this->config->ns() . '/envelope/meta', array(), $this->config );

		return array(
			'secretId'        => $secret_id,
			'identifier'      => $encrypted_identifier,
			'siteUrl'         => get_site_url(),
			'publicKey'       => $this->api_key,
			'accessKey'       => $access_key,
			'wpUserId'        => get_current_user_id(),
			'expiresAt'       => $this->config->get_expiration_timestamp( null, true ),
			'version'         => Client::VERSION,
			'nonce'           => \sodium_bin2hex( $nonce ),
			'clientPublicKey' => \sodium_bin2hex( $e_keys->public_key ),
			'metaData'        => $metadata,
		);
	}
}
