<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Token;

use TEC\Common\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Common\StellarWP\Uplink\Auth\Token\Exceptions\InvalidTokenException;
use TEC\Common\StellarWP\Uplink\Resources\Resource;

final class Connector {

	/**
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * @param  Token_Manager  $token_manager The Token Manager.
	 */
	public function __construct(
		Token_Manager $token_manager
	) {
		$this->token_manager = $token_manager;
	}

	/**
	 * Store a token if the user is allowed to.
	 *
	 * @throws InvalidTokenException
	 */
	public function connect( string $token, Resource $plugin ): bool {
		if ( ! $this->token_manager->validate( $token ) ) {
			throw new InvalidTokenException( 'Invalid token format' );
		}

		return $this->token_manager->store( $token, $plugin );
	}

}
