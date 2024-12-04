<?php

namespace LottaFramework\Facades;

use LottaFramework\Container\Container;

/**
 * Taken and modified from: https://github.com/illuminate/support
 */
abstract class Facade {
	/**
	 * The application instance being facade.
	 *
	 * @var Container
	 */
	protected static $app;

	/**
	 * The resolved object instances.
	 *
	 * @var array
	 */
	protected static $resolvedInstance;

	/**
	 * Indicates if the resolved instance should be cached.
	 *
	 * @var bool
	 */
	protected static $cached = true;

	/**
	 * Run a Closure when the facade has been resolved.
	 *
	 * @param \Closure $callback
	 *
	 * @return void
	 */
	public static function resolved( \Closure $callback ) {
		$accessor = static::getFacadeAccessor();

		if ( static::$app->resolved( $accessor ) === true ) {
			$callback( static::getFacadeRoot() );
		}

		static::$app->afterResolving( $accessor, function ( $service ) use ( $callback ) {
			$callback( $service );
		} );
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	protected static function getFacadeAccessor() {
		throw new \RuntimeException( 'Facade does not implement getFacadeAccessor method.' );
	}

	/**
	 * Get the root object behind the facade.
	 *
	 * @return mixed
	 */
	public static function getFacadeRoot() {
		return static::resolveFacadeInstance( static::getFacadeAccessor() );
	}

	/**
	 * Resolve the facade root instance from the container.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected static function resolveFacadeInstance( $name ) {
		if ( isset( static::$resolvedInstance[ $name ] ) ) {
			return static::$resolvedInstance[ $name ];
		}

		if ( static::$app ) {
			if ( static::$cached ) {
				return static::$resolvedInstance[ $name ] = static::$app[ $name ];
			}

			return static::$app[ $name ];
		}
	}

	/**
	 * Clear a resolved facade instance.
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public static function clearResolvedInstance( $name ) {
		unset( static::$resolvedInstance[ $name ] );
	}

	/**
	 * Clear all of the resolved instances.
	 *
	 * @return void
	 */
	public static function clearResolvedInstances() {
		static::$resolvedInstance = [];
	}

	/**
	 * Set the application instance.
	 *
	 * @param Container $app
	 *
	 * @return void
	 */
	public static function setFacadeApplication( $app ) {
		static::$app = $app;
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException
	 */
	public static function __callStatic( $method, $args ) {
		$instance = static::getFacadeRoot();

		if ( ! $instance ) {
			throw new \RuntimeException( 'A facade root has not been set.' );
		}

		return $instance->$method( ...$args );
	}
}
