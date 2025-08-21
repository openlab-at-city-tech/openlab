<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Auth;

use TEC\Common\StellarWP\Uplink\API\V3\Provider;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

/**
 * Token Authorizer Cache Decorator.
 *
 * @see Config::set_auth_cache_expiration() to enable.
 */
final class Token_Authorizer_Cache_Decorator implements Contracts\Token_Authorizer {

	public const TRANSIENT_PREFIX = 'stellarwp_auth_';

	/**
	 * @var Token_Authorizer
	 */
	private $authorizer;

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
	 * @see Config::set_auth_cache_expiration()
	 * @see Provider::register_token_authorizer()
	 *
	 * @param  Token_Authorizer  $authorizer The original authorizer.
	 * @param  int               $expiration The expiration time in seconds.
	 */
	public function __construct(
		Token_Authorizer $authorizer,
		Storage $storage,
		int $expiration = 21600
	) {
		$this->authorizer = $authorizer;
		$this->storage    = $storage;
		$this->expiration = $expiration;
	}

	/**
	 * Check if a license is authorized and cache successful responses.
	 *
	 * @see Config::set_auth_cache_expiration()
	 * @see is_authorized()
	 * @see Token_Authorizer
	 *
	 * @param  string  $license  The license key.
	 * @param  string  $slug     The plugin/service slug.
	 * @param  string  $token    The stored token.
	 * @param  string  $domain   The user's domain.
	 *
	 * @return bool
	 */
	public function is_authorized( string $license, string $slug, string $token, string $domain ): bool {
		$transient     = $this->build_transient( [ $license, $token, $domain ] );
		$is_authorized = $this->storage->get( $transient );

		if ( $is_authorized === true ) {
			return true;
		}

		$is_authorized = $this->authorizer->is_authorized( $license, $slug, $token, $domain );

		// Only cache successful responses.
		if ( $is_authorized ) {
			$this->storage->set( $transient, true, $this->expiration );
		}

		return $is_authorized;
	}

	/**
	 * Build a transient key.
	 *
	 * @param  array<int, string>  $args
	 *
	 * @return string
	 */
	public function build_transient( array $args ): string {
		return self::TRANSIENT_PREFIX . $this->build_transient_no_prefix( $args );
	}

	/**
	 * Build a transient key without the prefix.
	 *
	 * @param  array  $args
	 *
	 * @return string
	 */
	public function build_transient_no_prefix( array $args ): string {
		return hash( 'sha256', json_encode( $args ) );
	}

}
