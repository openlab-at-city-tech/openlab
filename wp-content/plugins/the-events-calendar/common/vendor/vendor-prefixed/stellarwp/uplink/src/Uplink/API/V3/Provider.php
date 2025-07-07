<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3;

use TEC\Common\StellarWP\Uplink\API\V3\Auth\Auth_Url_Cache_Decorator;
use TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts\Auth_Url;
use TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts\Token_Authorizer;
use TEC\Common\StellarWP\Uplink\API\V3\Auth\Token_Authorizer_Cache_Decorator;
use TEC\Common\StellarWP\Uplink\API\V3\Contracts\Client_V3;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Contracts\Abstract_Provider;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;
use WP_Http;

final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->container->bind( Auth_Url::class, Auth_Url_Cache_Decorator::class );

		$this->container->singleton( Client_V3::class, static function (): Client {
			$prefix   = 'stellarwp/uplink/' . Config::get_hook_prefix();
			$api_root = '/api/stellarwp/v3/';

			if ( defined( 'STELLARWP_UPLINK_V3_API_ROOT' ) && STELLARWP_UPLINK_V3_API_ROOT ) {
				$api_root = STELLARWP_UPLINK_V3_API_ROOT;
			}

			$base_url = 'https://licensing.stellarwp.com';

			if ( defined( 'STELLARWP_UPLINK_API_BASE_URL' ) && STELLARWP_UPLINK_API_BASE_URL ) {
				$base_url = preg_replace( '!/$!', '', STELLARWP_UPLINK_API_BASE_URL );
			}

			/**
			 * Filter the V3 api root.
			 *
			 * @param  string  $api_root  The base endpoint for the v3 API.
			 */
			$api_root = apply_filters( $prefix . '/v3/client/api_root', $api_root );

			/**
			 * Filter the V3 api base URL.
			 *
			 * @param  string  $base_url  The base URL for the v3 API.
			 */
			$base_url = apply_filters( $prefix . '/v3/client/base_url', $base_url );

			$request_args = apply_filters( $prefix . '/v3/client/request_args', [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'timeout' => 15, // Seconds.
			] );

			return new Client( $api_root, $base_url, $request_args, new WP_Http() );
		} );

		$this->register_token_authorizer();
	}

	/**
	 * Based on the developer's configuration, determine if we will enable Token Authorization caching.
	 *
	 * @return void
	 */
	private function register_token_authorizer(): void {
		$expiration = Config::get_auth_cache_expiration();

		if ( $expiration >= 0 ) {
			$this->container->bind(
				Token_Authorizer::class,
				static function ( $c ) use ( $expiration ): Token_Authorizer {
					return new Token_Authorizer_Cache_Decorator(
						$c->get( Auth\Token_Authorizer::class ),
						$c->get( Storage::class ),
						$expiration
					);
				}
			);

			return;
		}

		$this->container->bind(
			Token_Authorizer::class,
			Auth\Token_Authorizer::class
		);
	}

}
