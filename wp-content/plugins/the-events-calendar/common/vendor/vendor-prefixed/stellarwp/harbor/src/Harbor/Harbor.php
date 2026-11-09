<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor;

use RuntimeException;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;

class Harbor {

	/**
	 * The Harbor library version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const VERSION = '2.0.2';

	/**
	 * Initializes the service provider.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 - Moves the initialization of the container to the init hook.
	 *
	 * @throws RuntimeException If the container has not been configured.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! Config::has_container() ) {
			throw new RuntimeException(
				__( 'You must call LiquidWeb\Harbor\Config::set_container() before calling LiquidWeb\Harbor::init().', 'tribe-common' )
			);
		}

		$container = Config::get_container();

		$container->bind( ContainerInterface::class, $container );
		$container->singleton( View\Provider::class );
		$container->singleton( Site\Data::class );
		$container->singleton( Admin\Provider::class );
		$container->singleton( Legacy\Provider::class );
		$container->singleton( Features\Provider::class );
		$container->singleton( Http\Provider::class );
		$container->singleton( Licensing\Provider::class );
		$container->singleton( Portal\Provider::class );
		$container->singleton( API\REST\V1\Provider::class );
		$container->singleton( API\Functions\Provider::class );
		$container->singleton( CLI\Provider::class );
		$container->singleton( Cron\Provider::class );
		$container->singleton( Premium_Plugin_Registry::class );

		// API\Functions\Provider owns loading global-functions.php and registering
		// the user-facing global function callbacks. Run it synchronously here -
		// before register_instance_hooks() - so _lw_harbor_instance_registry() is
		// defined when this instance registers itself into the cross-instance registry.
		$container->get( API\Functions\Provider::class )->register();

		if ( $container->get( Premium_Plugin_Registry::class )->any() ) {
			$container->get( View\Provider::class )->register();
			$container->get( Admin\Provider::class )->register();
			$container->get( Legacy\Provider::class )->register();
			$container->get( Features\Provider::class )->register();
			$container->get( Http\Provider::class )->register();
			$container->get( Licensing\Provider::class )->register();
			$container->get( Portal\Provider::class )->register();
			$container->get( API\REST\V1\Provider::class )->register();
			$container->get( CLI\Provider::class )->register();
			$container->get( Cron\Provider::class )->register();

			/**
			 * Fires when Harbor is loaded.
			 *
			 * @since 1.2.0
			 *
			 * @return void
			 */
			do_action( 'lw_harbor/loaded' );
		}

		static::register_instance_hooks();
	}

	/**
	 * Registers shared, non-prefixed WordPress hooks that enable cross-instance
	 * communication between vendor-prefixed copies of Harbor.
	 *
	 * @since 1.0.0
	 * @since 1.6.2 Skip the registry when the host boots after wp_loaded.
	 *
	 * @return void
	 */
	protected static function register_instance_hooks(): void {
		/*
		 * The registry only accepts registrations before wp_loaded. A host that
		 * boots later, such as a plugin running its activation hook, cannot join
		 * the registry on that request, so it skips the call.
		 */
		if ( ! did_action( 'wp_loaded' ) ) {
			_lw_harbor_instance_registry( self::VERSION, Config::get_plugin_basename() ?? '' );
		}

		Version::register_debug_info();
	}
}
