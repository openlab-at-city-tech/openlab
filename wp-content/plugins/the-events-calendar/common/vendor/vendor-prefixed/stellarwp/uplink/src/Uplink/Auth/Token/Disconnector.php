<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Token;

use TEC\Common\StellarWP\Uplink\API\V3\Auth\Token_Authorizer_Cache_Decorator;
use TEC\Common\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

final class Disconnector {

	/**
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * @var Collection
	 */
	private $resources;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @param  Token_Manager  $token_manager  The Token Manager.
	 */
	public function __construct(
		Token_Manager $token_manager,
		Collection $resources,
		Storage $storage
	) {
		$this->token_manager = $token_manager;
		$this->resources     = $resources;
		$this->storage       = $storage;
	}

	/**
	 * Delete a token if the current user is allowed to.
	 *
	 * @param  string  $slug       The plugin or service slug.
	 * @param  string  $cache_key  The token cache key.
	 *
	 * @return bool
	 */
	public function disconnect( string $slug, string $cache_key ): bool {
		$plugin = $this->resources->offsetGet( $slug );

		if ( ! $plugin ) {
			return false;
		}

		$result = $this->token_manager->delete( $slug );

		if ( $result ) {
			// Delete the authorization cache.
			$this->storage->delete( Token_Authorizer_Cache_Decorator::TRANSIENT_PREFIX . $cache_key );
		}

		return $result;
	}

}
