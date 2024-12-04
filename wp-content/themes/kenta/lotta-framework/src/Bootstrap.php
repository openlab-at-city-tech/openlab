<?php

namespace LottaFramework;

use LottaFramework\Facades\Facade;

class Bootstrap {

	/**
	 * All default singletons
	 *
	 * @var array
	 */
	protected static $singletons = [
		\LottaFramework\Customizer\Customizer::class,
		\LottaFramework\Css::class,
		\LottaFramework\Query::class,
		\LottaFramework\Extensions\Breadcrumbs::class,
	];

	/**
	 * All default alias
	 *
	 * @var array
	 */
	protected static $aliases = [
		'CZ'          => \LottaFramework\Customizer\Customizer::class,
		'css'         => \LottaFramework\Css::class,
		'query'       => \LottaFramework\Query::class,
		'breadcrumbs' => \LottaFramework\Extensions\Breadcrumbs::class,
	];

	/**
	 * @param string $id
	 * @param string $uri
	 */
	public static function run( string $id, string $uri ) {
		$app = new Application( $id, $uri );

		$app->instance( Application::class, $app );

		Facade::setFacadeApplication( $app );

		foreach ( self::$singletons as $singleton ) {
			$app->singleton( $singleton );
		}

		foreach ( self::$aliases as $alias => $abs ) {
			$app->alias( $abs, $alias );
		}
	}

}