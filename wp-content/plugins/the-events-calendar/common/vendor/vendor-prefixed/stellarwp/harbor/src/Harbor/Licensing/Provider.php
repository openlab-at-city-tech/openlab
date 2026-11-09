<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing;

use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Harbor;
use TEC\Common\LiquidWeb\Harbor\Licensing\Registry\Product_Registry;
use TEC\Common\LiquidWeb\Harbor\Licensing\Repositories\License_Repository;
use TEC\Common\LiquidWeb\LicensingApiClient\Config as LicensingConfig;
use TEC\Common\LiquidWeb\LicensingApiClient\Contracts\LicensingClientInterface;
use TEC\Common\LiquidWeb\LicensingApiClientWordPress\Http\WordPressHttpClient;
use TEC\Common\LiquidWeb\LicensingApiClientWordPress\WordPressApiFactory;
use TEC\Common\Nyholm\Psr7\Factory\Psr17Factory;

/**
 * Registers the Licensing subsystem in the DI container.
 *
 * @since 1.0.0
 */
final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton(
			LicensingClientInterface::class,
			function () {
				$psr17   = $this->container->get( Psr17Factory::class );
				$factory = new WordPressApiFactory(
					$this->container->get( WordPressHttpClient::class ),
					$psr17,
					$psr17
				);
				return $factory->make(
					new LicensingConfig(
						Config::get_licensing_base_url(),
						null,
						'lw-harbor/' . Harbor::VERSION
					)
				);
			}
		);

		$this->container->singleton( License_Repository::class );
		$this->container->singleton( Product_Registry::class );
		$this->container->singleton( License_Manager::class );

		add_action(
			'lw-harbor/unified_license_key_changed',
			function () {
				/** @var License_Repository $license_repository */
				$license_repository = $this->container->get( License_Repository::class );
				$license_repository->delete_products();
				$license_repository->delete_validation_state();
			}
		);

		add_action(
			'activated_plugin',
			function () {
				/** @var License_Manager $license_manager */
				$license_manager = $this->container->get( License_Manager::class );
				$license_manager->store_embedded_key_if_present();
			}
		);
	}
}
