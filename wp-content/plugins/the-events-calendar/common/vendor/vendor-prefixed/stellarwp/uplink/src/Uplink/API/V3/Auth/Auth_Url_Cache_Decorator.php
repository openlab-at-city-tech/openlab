<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Auth;

use InvalidArgumentException;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

/**
 * Auth URL cache decorator.
 */
final class Auth_Url_Cache_Decorator implements Contracts\Auth_Url {

	public const TRANSIENT_PREFIX = 'stellarwp_auth_url_';

	/**
	 * @var Auth_Url
	 */
	private $auth_url;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * The cache expiration in seconds.
	 *
	 * @var int
	 */
	private $expiration;

	/**
	 * @param  Auth_Url  $auth_url  Remotely fetch the Origin's Auth URL.
	 * @param  int  $expiration  The cache expiration in seconds.
	 */
	public function __construct( Auth_Url $auth_url, Storage $storage, int $expiration = DAY_IN_SECONDS ) {
		$this->auth_url   = $auth_url;
		$this->storage    = $storage;
		$this->expiration = $expiration;
	}

	/**
	 * Cache the auth url response.
	 *
	 * @param  string  $slug  The product slug.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return string
	 */
	public function get( string $slug ): string {
		if ( ! $slug ) {
			throw new InvalidArgumentException( __( 'The Product Slug cannot be empty', 'tribe-common' ) );
		}

		$transient = $this->build_transient( $slug );

		$url = $this->storage->get( $transient );

		if ( $url !== null ) {
			return $url;
		}

		$url = $this->auth_url->get( $slug );

		// We'll cache empty auth URLs to prevent further remote requests.
		$this->storage->set( $transient, $url, $this->expiration );

		return $url;
	}

	/**
	 * Build the transient key based on the provided slug.
	 *
	 * @param  string  $slug
	 *
	 * @return string
	 */
	private function build_transient( string $slug ): string {
		return self::TRANSIENT_PREFIX . str_replace( '-', '_', $slug );
	}

}
