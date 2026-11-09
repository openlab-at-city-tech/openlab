<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Auth;

use TEC\Common\StellarWP\Uplink\API\V3\Contracts\Client_V3;
use TEC\Common\StellarWP\Uplink\Traits\With_Debugging;
use WP_Error;
use WP_Http;

use function TEC\Common\StellarWP\Uplink\is_authorized;

/**
 * Manages authorization.
 */
class Token_Authorizer implements Contracts\Token_Authorizer {

	use With_Debugging;

	/**
	 * @var Client_V3
	 */
	private $client;

	public function __construct( Client_V3 $client ) {
		$this->client = $client;
	}

	/**
	 * Manually check if a license is authorized.
	 *
	 * @see is_authorized()
	 * @see Token_Authorizer_Cache_Decorator
	 *
	 * @param  string  $license  The license key.
	 * @param  string  $slug     The plugin/service slug.
	 * @param  string  $token    The stored token.
	 * @param  string  $domain   The user's domain.
	 *
	 * @return bool
	 *
	 * @see is_authorized()
	 */
	public function is_authorized( string $license, string $slug, string $token, string $domain ): bool {
		$response = $this->client->get( 'tokens/auth', [
			'license' => $license,
			'slug'    => $slug,
			'token'   => $token,
			'domain'  => $domain,
		] );

		if ( $response instanceof WP_Error ) {
			if ( $this->is_wp_debug() ) {
				error_log( sprintf(
					__( 'Authorization error occurred: License: "%s", Token: "%s", Domain: "%s". Errors: %s', 'tribe-common' ),
					$license,
					$token,
					$domain,
					implode( ', ', $response->get_error_messages() )
				) );
			}

			return false;
		}

		return $response['response']['code'] === WP_Http::OK;
	}

}
