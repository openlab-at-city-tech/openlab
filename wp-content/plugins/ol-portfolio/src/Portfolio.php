<?php

namespace OpenLab\Portfolio;

use OpenLab\Portfolio\Contracts\Registerable;
use OpenLab\Portfolio\Share\Service as ShareService;
use OpenLab\Portfolio\Export\Service as ExportService;
use OpenLab\Portfolio\Import\Service as ImportService;

/**
 * Main plugin class.
 */
final class Portfolio {

	const SERVICE_PROVIDERS = [
		ShareService::class,
		ExportService::class,
		ImportService::class,
	];

	/**
	 * Create and return an instance of the plugin.
	 *
	 * This always returns a shared instance.
	 */
	public static function create() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
			$instance->register_services();
		}

		return $instance;
	}

	/**
	 * Register the individual services of this plugin.
	 *
	 * @return void
	 */
	protected function register_services() {
		$services = array_map( [ $this, 'init_services' ], self::SERVICE_PROVIDERS );

		array_walk( $services, function ( Registerable $service ) {
			$service->register();
		} );
	}

	/**
	 * Instantiate a single service.
	 *
	 * @param object $service
	 * @return object
	 */
	protected function init_services( $service ) {
		return new $service;
	}
}
