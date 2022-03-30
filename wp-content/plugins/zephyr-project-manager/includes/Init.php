<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

final class Init {

	/* 
	* Store all the classes inside an array
	* @return array full list of classes
	*/
	public static function get_services() {
		return [
			Pages\Admin::class,
			Base\Heartbeat::class,
			Base\SettingsLinks::class,
			Base\EnqueueScripts::class,
			Base\AjaxHandler::class,
			Api\RestApi::class,
			Core\Core::class
		];
	}

	/* 
	* Loop through the classes, initialize them and call the register method if it exists
	*/
	public static function register_services() {
		foreach ( self::get_services() as $class ) {

			$service = self::instantiate( $class );

			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/* 
	* Initialize the class
	* @param class $class class from the services array
	* @return class instance returns the class instance
	*/
	private static function instantiate( $class ) {
		$service = new $class();
		return $service;
	}
}
