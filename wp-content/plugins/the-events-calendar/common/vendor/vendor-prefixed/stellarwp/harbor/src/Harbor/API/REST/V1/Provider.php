<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;

/**
 * Registers all v1 WP REST API controllers and hooks routes.
 *
 * @since 1.0.0
 */
final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton( Feature_Controller::class );
		$this->container->singleton( License_Controller::class );
		$this->container->singleton( Catalog_Controller::class );
		$this->container->singleton( Legacy_License_Controller::class );
		$this->container->singleton( Harbor_Hosts_Controller::class );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Registers all v1 REST API routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		if ( Version::should_handle( 'register_rest_routes_v1' ) ) {
			$this->container->get( Feature_Controller::class )->register_routes();
			$this->container->get( License_Controller::class )->register_routes();
			$this->container->get( Catalog_Controller::class )->register_routes();
			$this->container->get( Legacy_License_Controller::class )->register_routes();
			$this->container->get( Harbor_Hosts_Controller::class )->register_routes();
		}
	}
}
