<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Storage;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Contracts\Abstract_Provider;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;
use TEC\Common\StellarWP\Uplink\Storage\Drivers\Option_Storage;

final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->container->singleton( Option_Storage::class, function () {
			$option_name = Config::get_hook_prefix() . '_storage';

			return new Option_Storage( $option_name );
		} );

		$this->container->singleton( Storage::class, static function( $c ): Storage {
			return $c->get( Config::get_storage_driver() );
		} );
	}

}
