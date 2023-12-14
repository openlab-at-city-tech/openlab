<?php

namespace Imagely\NGG\WPCLI;

class Manager {

	public static function register() {
		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg album', '\Imagely\NGG\WPCLI\Album' );

		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg cache', '\Imagely\NGG\WPCLI\Cache' );

		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg gallery', '\Imagely\NGG\WPCLI\Gallery' );

		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg image', '\Imagely\NGG\WPCLI\Image' );

		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg notifications', '\Imagely\NGG\WPCLI\Notifications' );

		/** @noinspection PhpUndefinedClassInspection */
		\WP_CLI::add_command( 'ngg settings', '\Imagely\NGG\WPCLI\Settings' );
	}
}
