<?php
/**
 * Class Encryption
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
 * Class Encryption
 */
final class Encryption {

	/**
	 * Config instance.
	 *
	 * @var Config $config
	 */
	private $config;

	/**
	 * Remote instance.
	 *
	 * @var Remote $remote
	 */
	private $remote;

	/**
	 * Logging instance.
	 *
	 * @var Logging
	 */
	private $logging;

	/**
	 * Where the plugin should store the public key for encrypting data
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $vendor_public_key_option;

	/**
	 * Endpoint path to Vendor public key.
	 *
	 * @var string
	 */
	private $vendor_public_key_endpoint = '/trustedlogin/v1/public_key';

	/**
	 * How long to store the Vendor public key in the database.
	 *
	 * @since 1.7.0
	 *
	 * @var int
	 */
	const VENDOR_PUBLIC_KEY_EXPIRY = 600; // 10 minutes.

	/**
	 * Encryption constructor.
	 *
	 * @param Config  $config Config instance.
	 * @param Remote  $remote Remote instance.
	 * @param Logging $logging Logging instance.
	 */
	public function __construct( Config $config, Remote $remote, Logging $logging ) {

		$this->config  = $config;
		$this->remote  = $remote;
		$this->logging = $logging;

		/**
		 * Filter: Sets the site option name for the Public Key for encryption functions
		 *
		 * @since 1.0.0
		 *
		 * @param string $vendor_public_key_option
		 * @param Config $config
		 */
		$this->vendor_public_key_option = apply_filters(
			'trustedlogin/' . $this->config->ns() . '/options/vendor_public_key',
			'tl_' . $this->config->ns() . '_vendor_public_key',
			$this->config
		);
	}

	/**
	 * Returns true if the site supports encryption using the required Sodium functions.
	 *
	 * These functions are available by extension in PHP 7.0 & 7.1, built-in to PHP 7.2+ and WordPress 5.2+.
	 *
	 * @since 1.4.0
	 *
	 * @return bool True: supports encryption. False: does not support encryption.
	 */
	public static function meets_requirements() {

		$required_functions = array(
			'random_bytes',
			'sodium_hex2bin',
			'sodium_crypto_box',
			'sodium_crypto_secretbox',
			'sodium_crypto_generichash',
			'sodium_crypto_box_keypair_from_secretkey_and_publickey',
		);

		foreach ( $required_functions as $function ) {
			if ( ! function_exists( $function ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Generates a random hash 64 characters long.
	 *
	 * If random_bytes() and openssl_random_pseudo_bytes() don't exist, returns WP_Error with code generate_hash_failed.
	 *
	 * If random_bytes() does not exist and openssl_random_pseudo_bytes() is unable to return a strong result,
	 * returns a WP_Error with code `openssl_not_strong_crypto`.
	 *
	 * @uses random_bytes
	 * @uses openssl_random_pseudo_bytes Only used if random_bytes() does not exist.
	 *
	 * @param Logging $logging The logging object.
	 *
	 * @return string|WP_Error 64-character random hash or a WP_Error object explaining what went wrong. See docblock.
	 */
	public static function get_random_hash( $logging ) {

		$byte_length = 64;

		$hash = false;

		if ( function_exists( 'random_bytes' ) ) {
			try {
				$bytes = random_bytes( $byte_length );
				$hash  = bin2hex( $bytes );
			} catch ( \TypeError $e ) {
				$logging->log( $e->getMessage(), __METHOD__, 'error' );
			} catch ( \Error $e ) {
				$logging->log( $e->getMessage(), __METHOD__, 'error' );
			} catch ( \Exception $e ) {
				$logging->log( $e->getMessage(), __METHOD__, 'error' );
			}
		} else {
			$logging->log( 'This site does not have the random_bytes() function.', __METHOD__, 'debug' );
		}

		if ( $hash ) {
			return $hash;
		}

		if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
			return new WP_Error( 'generate_hash_failed', 'Could not generate a secure hash with random_bytes or openssl.' );
		}

		$crypto_strong = false;
		$hash          = openssl_random_pseudo_bytes( $byte_length, $crypto_strong );

		if ( ! $crypto_strong ) {
			return new WP_Error( 'openssl_not_strong_crypto', 'Site could not generate a secure hash with OpenSSL.' );
		}

		return $hash;
	}

	/**
	 * Creates a hash of a string using the Sodium library.
	 *
	 * @uses sodium_bin2hex
	 * @uses sodium_crypto_generichash
	 *
	 * @param string $string_to_hash The string to hash.
	 * @param int    $length The length of the hash to return.
	 *
	 * @return string|WP_Error
	 */
	public static function hash( $string_to_hash, $length = 16 ) {

		if ( ! function_exists( 'sodium_crypto_generichash' ) ) {
			return new WP_Error( 'sodium_crypto_generichash_not_available', 'sodium_crypto_generichash not available' );
		}

		try {
			$hash_bin = sodium_crypto_generichash( $string_to_hash, '', (int) $length );
			$hash     = sodium_bin2hex( $hash_bin );
			// @phpstan-ignore-next-line
		} catch ( \TypeError $e ) {
			return new WP_Error(
				'encryption_failed_generichash_typeerror',
				sprintf( 'Error while generating hash: %s (%s)', $e->getMessage(), $e->getCode() )
			);
			// @phpstan-ignore-next-line
		} catch ( \Error $e ) {
			return new WP_Error(
				'encryption_failed_generichash_error',
				sprintf( 'Error while generating hash: %s (%s)', $e->getMessage(), $e->getCode() )
			);
		} catch ( \SodiumException $e ) {
			return new WP_Error(
				'encryption_failed_generichash_sodium',
				sprintf( 'Error while generating hash: %s (%s)', $e->getMessage(), $e->getCode() )
			);
			// @phpstan-ignore-next-line
		} catch ( \Exception $e ) {
			return new WP_Error(
				'encryption_failed_generichash',
				sprintf( 'Error while generating hash: %s (%s)', $e->getMessage(), $e->getCode() )
			);
		}

		return $hash;
	}

	/**
	 * Fetches the Public Key from local or db
	 *
	 * @since 1.0.0
	 *
	 * @return string|WP_Error  If found, it returns the publicKey, if not a WP_Error
	 */
	public function get_vendor_public_key() {

		// Already stored as transient.
		$public_key = Utils::get_transient( $this->vendor_public_key_option );

		if ( $public_key ) {
			// Documented below.
			return apply_filters( 'trustedlogin/' . $this->config->ns() . '/vendor_public_key', $public_key, $this->config );
		}

		// Fetch a key from Vendor site.
		$remote_key = $this->get_remote_encryption_key();

		if ( is_wp_error( $remote_key ) ) {
			$this->logging->log( sprintf( '(%s) %s', $remote_key->get_error_code(), $remote_key->get_error_message() ), __METHOD__, 'error' );

			return $remote_key;
		}

		// Store Vendor public key in the DB for ten minutes.
		$saved = Utils::set_transient( $this->vendor_public_key_option, $remote_key, self::VENDOR_PUBLIC_KEY_EXPIRY );

		if ( ! $saved ) {
			$this->logging->log( 'Public key not saved after being fetched remotely.', __METHOD__, 'warning' );
		}

		/**
		 * Filter: Override the public key functions.
		 *
		 * @since 1.0.0
		 *
		 * @param string $remote_key The public key fetched from the vendor's site.
		 * @param Config $config          The TrustedLogin configuration object.
		 *
		 * @return string
		 */
		return apply_filters( 'trustedlogin/' . $this->config->ns() . '/vendor_public_key', $remote_key, $this->config );
	}

	/**
	 * Returns the URL for the vendor public key endpoint.
	 *
	 * @since 1.5.0
	 *
	 * @return string URL for the vendor public key endpoint, after being filtered.
	 */
	public function get_remote_encryption_key_url() {

		$vendor_website = $this->config->get_setting( 'vendor/website', '' );

		/**
		 * Override the path to TrustedLogin's WordPress REST API website URL.
		 *
		 * @see https://docs.trustedlogin.com/Client/hooks#trustedloginnamespacevendorpublic_keywebsite
		 *
		 * @since 1.3.2
		 *
		 * @param string $public_key_website Root URL of the website from where the vendor's public key is fetched. May be different than the vendor/website configuration setting.
		 */
		$public_key_website = apply_filters( 'trustedlogin/' . $this->config->ns() . '/vendor/public_key/website', $vendor_website );

		/**
		 * Override the path to TrustedLogin's WordPress REST API endpoint.
		 *
		 * @see https://docs.trustedlogin.com/Client/hooks#trustedloginnamespacevendorpublic_keyendpoint
		 *
		 * @param string $key_endpoint Endpoint path on vendor (software vendor's) site.
		 */
		$key_endpoint = apply_filters( 'trustedlogin/' . $this->config->ns() . '/vendor/public_key/endpoint', $this->vendor_public_key_endpoint );

		$public_key_url = add_query_arg( array( 'rest_route' => $key_endpoint ), trailingslashit( $public_key_website ) );

		return $public_key_url;
	}

	/**
	 * Fetches the Public Key from the `TrustedLogin-vendor` plugin on support website.
	 *
	 * @since 1.0.0
	 *
	 * @return string|WP_Error  If successful, will return the Public Key string. Otherwise WP_Error on failure.
	 */
	private function get_remote_encryption_key() {

		$headers = array(
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		);

		$request_options = array(
			'method'      => 'GET',
			'timeout'     => 45,
			'httpversion' => '1.1',
			'headers'     => $headers,
		);

		$url = $this->get_remote_encryption_key_url();

		$response = wp_remote_request( $url, $request_options );

		$response_json = $this->remote->handle_response( $response, array( 'publicKey' ) );

		if ( is_wp_error( $response_json ) ) {
			if ( 'not_found' === $response_json->get_error_code() ) {
				return new WP_Error( 'not_found', __( 'Encryption key could not be fetched, Vendor site returned 404.', 'trustedlogin' ) );
			}

			return $response_json;
		}

		return $response_json['publicKey'];
	}

	/**
	 * Encrypts a string using the public bey provided by the plugin/theme developers' server.
	 *
	 * @since 1.0.0
	 * @uses \sodium_crypto_box_keypair_from_secretkey_and_publickey() to generate key.
	 * @uses \sodium_crypto_secretbox() to encrypt.
	 *
	 * @param string $data Data to encrypt.
	 * @param string $nonce The nonce generated for this encryption.
	 * @param string $alice_secret_key The key to use when generating the encryption key.
	 *
	 * @return string|WP_Error  Encrypted envelope, base64-encoded, or WP_Error on failure.
	 */
	public function encrypt( $data, $nonce, $alice_secret_key ) {

		if ( empty( $data ) ) {
			return new WP_Error( 'no_data', 'No data provided.' );
		}

		if ( ! function_exists( 'sodium_crypto_secretbox' ) ) {
			return new WP_Error( 'sodium_crypto_secretbox_not_available', 'lib_sodium not available' );
		}

		$bob_public_key = $this->get_vendor_public_key();

		if ( is_wp_error( $bob_public_key ) ) {
			return $bob_public_key;
		}

		try {
			$alice_to_bob_kp = sodium_crypto_box_keypair_from_secretkey_and_publickey( $alice_secret_key, \sodium_hex2bin( $bob_public_key ) );
			$encrypted       = sodium_crypto_box( $data, $nonce, $alice_to_bob_kp );
		} catch ( \SodiumException $e ) {
			return new WP_Error(
				'encryption_failed_cryptobox',
				sprintf( 'Error while encrypting the envelope: %s (%s)', $e->getMessage(), $e->getCode() )
			);
			// @phpstan-ignore-next-line
		} catch ( \TypeError $e ) {
			return new WP_Error(
				'encryption_failed_cryptobox_typeerror',
				sprintf( 'Error while encrypting the envelope: %s (%s)', $e->getMessage(), $e->getCode() )
			);
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode( $encrypted );
	}

	/**
	 * Gets and returns a random nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return string|WP_Error  Nonce if created, otherwise WP_Error
	 */
	public function get_nonce() {

		if ( ! function_exists( 'random_bytes' ) ) {
			return new WP_Error( 'missing_function', 'No random_bytes function installed.' );
		}

		try {
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		} catch ( \Exception $e ) {
			return new WP_Error( 'encryption_failed_randombytes', sprintf( 'Unable to generate encryption nonce: %s (%s)', $e->getMessage(), $e->getCode() ) );
		}

		return $nonce;
	}

	/**
	 * Generate unique Client encryption keys.
	 *
	 * @since 1.0.0
	 *
	 * @uses sodium_crypto_box_keypair()
	 * @uses sodium_crypto_box_publickey()
	 * @uses sodium_crypto_box_secretkey()
	 *
	 * @return object|WP_Error $alice_keys or WP_Error if there's any issues.
	 *   $alice_keys = [
	 *      'public_key'  =>  (string)  The public key.
	 *      'private_key' =>  (string)  The private key.
	 *   ]
	 */
	public function generate_keys() {

		if ( ! function_exists( 'sodium_crypto_box_keypair' ) ) {
			return new WP_Error( 'sodium_crypto_secretbox_not_available', 'lib_sodium not available' );
		}

		// In our build Alice = Client & Bob = Vendor.
		$alice_keypair = sodium_crypto_box_keypair();

		$alice_keys = array(
			'public_key'  => sodium_crypto_box_publickey( $alice_keypair ),
			'private_key' => sodium_crypto_box_secretkey( $alice_keypair ),
		);

		return (object) $alice_keys;
	}
}
