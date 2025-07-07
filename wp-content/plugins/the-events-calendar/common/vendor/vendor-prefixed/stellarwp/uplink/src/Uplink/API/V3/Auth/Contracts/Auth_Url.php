<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts;

interface Auth_Url {

	/**
	 * Retrieve an Origin's auth url, if it exists.
	 *
	 * @param  string  $slug  The product slug.
	 *
	 * @return string
	 */
	public function get( string $slug ): string;

}
