<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Token;

use InvalidArgumentException;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Resource;

/**
 * Manages storing authorization tokens in a network.
 *
 * @note All *_network_option() functions will fall back to
 * single site functions if multisite is not enabled.
 */
final class Token_Manager implements Contracts\Token_Manager {

	/**
	 * The index used in get_all() for any legacy token.
	 */
	public const LEGACY_INDEX = 'legacy';

	/**
	 * The option name to store the token in wp_options table.
	 *
	 * @see Config::set_token_auth_prefix()
	 *
	 * @var string
	 */
	protected $option_name;

	/**
	 * @param  string  $option_name  The option name as set via Config::set_token_auth_prefix().
	 */
	public function __construct( string $option_name ) {
		if ( ! $option_name ) {
			throw new InvalidArgumentException(
				__( 'You must set a token prefix with StellarWP\Uplink\Config::set_token_auth_prefix() before using the token manager.', 'tribe-common' )
			);
		}

		$this->option_name = $option_name;
	}

	/**
	 * Returns the option_name that is used to store tokens.
	 *
	 * @return string
	 */
	public function option_name(): string {
		return $this->option_name;
	}

	/**
	 * Validates a token is in the accepted UUIDv4 format.
	 *
	 * @param  string  $token
	 *
	 * @return bool
	 */
	public function validate( string $token ): bool {
		$pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

		return preg_match( $pattern, $token ) === 1;
	}

	/**
	 * Store the token.
	 *
	 * @since 2.0.0 Added $plugin param.
	 *
	 * @param  string    $token   The token to store.
	 * @param  Resource  $plugin  The Product to store the token for.
	 *
	 * @return bool
	 */
	public function store( string $token, Resource $plugin ): bool {
		if ( ! $token ) {
			return false;
		}

		$current = $tokens = $this->get_all();

		$tokens[ $plugin->get_slug() ] = $token;

		// WordPress would otherwise return false if the items match.
		if ( $tokens === $current ) {
			return true;
		}

		return update_network_option( get_current_network_id(), $this->option_name, $tokens );
	}

	/**
	 * Get the token.
	 *
	 * @since 2.0.0 Added $plugin param.
	 *
	 * @note  This will fallback to the legacy token, if it exists.
	 *
	 * @param  Resource  $plugin  The Product to retrieve the token for.
	 *
	 * @return string|null
	 */
	public function get( Resource $plugin ): ?string {
		$tokens = $this->get_all();

		return $tokens[ $plugin->get_slug() ] ?? $tokens[ self::LEGACY_INDEX ] ?? null;
	}

	/**
	 * Get all the tokens, indexed by their slug.
	 *
	 * @note Legacy tokens are stored as a string, and will be returned with the `legacy` slug.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string, string>
	 */
	public function get_all(): array {
		$tokens = (array) get_network_option( get_current_network_id(), $this->option_name, [] );

		// Index the legacy token by `legacy`.
		if ( array_key_exists( 0, $tokens ) ) {
			$tokens[ self::LEGACY_INDEX ] = $tokens[0];
			unset( $tokens[0] );
		}

		return $tokens;
	}

	/**
	 * Revoke the token.
	 *
	 * @param  string  $slug The Product to retrieve the token for.
	 *
	 * @return bool
	 */
	public function delete( string $slug = '' ): bool {
		$current = $tokens = $this->get_all();

		// We'll always delete the legacy token.
		if ( isset( $tokens[ self::LEGACY_INDEX ] ) ) {
			unset( $tokens[ self::LEGACY_INDEX ] );
		}

		// Delete the specified token if it exists.
		if ( isset( $tokens[ $slug ] ) ) {
			unset( $tokens[ $slug ] );
		}

		// No change, return true, otherwise WordPress would return false here.
		if ( $tokens === $current ) {
			return true;
		}

		return update_network_option( get_current_network_id(), $this->option_name, $tokens );
	}

}
