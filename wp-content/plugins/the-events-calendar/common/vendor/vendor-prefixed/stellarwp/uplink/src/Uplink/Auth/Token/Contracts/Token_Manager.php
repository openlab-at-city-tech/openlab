<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Token\Contracts;

use TEC\Common\StellarWP\Uplink\Resources\Resource;

interface Token_Manager {

	/**
	 * This makes up the suffix of the option name when combined
	 * with the custom token prefix.
	 *
	 * @see Config::set_token_auth_prefix()
	 */
	public const TOKEN_SUFFIX = 'uplink_auth_token';

	/**
	 * Returns the option_name/network_option_name that is used to store tokens.
	 *
	 * @return string
	 */
	public function option_name(): string;

	/**
	 * Validates a token is in the accepted UUIDv4 format.
	 *
	 * @param  string  $token
	 *
	 * @return bool
	 */
	public function validate( string $token ): bool;

	/**
	 * Stores the token in the database.
	 *
	 * @param  string    $token
	 * @param  Resource  $plugin
	 *
	 * @return bool
	 */
	public function store( string $token, Resource $plugin ): bool;

	/**
	 * Retrieves the stored token.
	 *
	 * @note This will return a single legacy token if one exists in the database.
	 *
	 * @param  Resource  $plugin
	 *
	 * @return string
	 */
	public function get( Resource $plugin ): ?string;

	/**
	 * Retrieve all store tokens, indexed by their slug.
	 *
	 * @since 2.0.0
	 *
	 * @note If a legacy token previously existed, it will be indexed by the key `legacy`.
	 *
	 * @return array<string, string>
	 */
	public function get_all(): ?array;

	/**
	 * Deletes the token from the database.
	 *
	 * @param  string  $slug
	 *
	 * @return bool
	 */
	public function delete( string $slug = '' ): bool;
}
