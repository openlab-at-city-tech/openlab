<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Notice;

use TEC\Common\StellarWP\Uplink\Contracts\Abstract_Provider;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton( Notice_Controller::class, Notice_Controller::class );
		$this->container->singleton( Notice_Handler::class, static function ( $c ): Notice_Handler {
			return new Notice_Handler( $c->get( Notice_Controller::class ), $c->get( Storage::class ) );
		} );

		add_action( 'admin_notices', [ $this->container->get( Notice_Handler::class ), 'display' ], 12, 0 );
	}

}
