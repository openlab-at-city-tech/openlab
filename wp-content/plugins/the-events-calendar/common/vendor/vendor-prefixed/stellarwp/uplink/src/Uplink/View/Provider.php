<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\View;

use TEC\Common\StellarWP\Uplink\Contracts\Abstract_Provider;
use TEC\Common\StellarWP\Uplink\View\Contracts\View;

final class Provider extends Abstract_Provider {

	/**
	 * Configure the View Renderer.
	 */
	public function register() {
		$this->container->singleton(
			WordPress_View::class,
			new WordPress_View( __DIR__ . '/../../views' )
		);

		$this->container->bind( View::class, $this->container->get( WordPress_View::class ) );
	}
}
