<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Auth;

use TEC\Common\StellarWP\Uplink\API\V3\Contracts\Client_V3;
use TEC\Common\StellarWP\Uplink\Traits\With_Debugging;
use WP_Error;

/**
 * Fetch a Product's Auth URL, which is a custom URL on the Origin's
 * server where authorization would occur.
 */
final class Auth_Url implements Contracts\Auth_Url {

	use With_Debugging;

	/**
	 * @var Client_V3
	 */
	private $client;

	/**
	 * @param  Client_V3  $client  The V3 API Client.
	 */
	public function __construct( Client_V3 $client ) {
		$this->client = $client;
	}

	/**
	 * Retrieve an Origin's auth url, if it exists.
	 *
	 * @param  string  $slug  The product slug.
	 *
	 * @return string
	 */
	public function get( string $slug ): string {
		$response = $this->client->get( 'tokens/auth_url', [
			'slug' => $slug,
		] );

		if ( $response instanceof WP_Error ) {
			if ( $this->is_wp_debug() ) {
				error_log( sprintf(
					'Token auth failed for slug: "%s". Errors: %s',
					$slug,
					implode( ', ', $response->get_error_messages() )
				) );
			}

			return '';
		}

		return $response['body']['data']['auth_url'] ?? '';
	}

}
