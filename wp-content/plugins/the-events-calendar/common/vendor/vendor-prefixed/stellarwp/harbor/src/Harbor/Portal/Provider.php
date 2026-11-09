<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

use TEC\Common\LiquidWeb\Harbor\Portal\Clients\Portal_Client;
use TEC\Common\LiquidWeb\Harbor\Portal\Clients\Http_Client;
use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\LicensingApiClientWordPress\Http\WordPressHttpClient;
use TEC\Common\Nyholm\Psr7\Factory\Psr17Factory;

/**
 * Registers the Catalog subsystem in the DI container.
 *
 * @since 1.0.0
 */
final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton(
			Portal_Client::class,
			function () {
				return new Http_Client(
					$this->container->get( WordPressHttpClient::class ),
					$this->container->get( Psr17Factory::class ),
					Config::get_portal_base_url()
				);
			}
		);

		$this->container->singleton( Catalog_Repository::class );
		$this->container->singleton( Herald_Url_Builder::class );
		$this->container->singleton( Herald_Legacy_Url_Builder::class );
		$this->container->singleton( Herald_Routing_Url_Builder::class );
		$this->container->singleton( Download_Url_Builder::class, Herald_Routing_Url_Builder::class );

		add_action(
			'lw-harbor/unified_license_key_changed',
			function () {
				$this->container->get( Catalog_Repository::class )->delete_catalog();
			}
		);
	}
}
