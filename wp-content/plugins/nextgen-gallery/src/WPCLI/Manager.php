<?php

namespace Imagely\NGG\WPCLI;

/**
 * WP-CLI command manager for NextGEN Gallery.
 *
 * Registers and manages WP-CLI commands for NextGEN Gallery operations.
 */
class Manager {

	/**
	 * Registers all NextGEN Gallery WP-CLI commands.
	 */
	public static function register() {
		/**
		 * Add album command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg album', '\Imagely\NGG\WPCLI\Album' );

		/**
		 * Add cache command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg cache', '\Imagely\NGG\WPCLI\Cache' );

		/**
		 * Add gallery command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg gallery', '\Imagely\NGG\WPCLI\Gallery' );

		/**
		 * Add image command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg image', '\Imagely\NGG\WPCLI\Image' );

		/**
		 * Add notifications command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg notifications', '\Imagely\NGG\WPCLI\Notifications' );

		/**
		 * Add settings command.
		 *
		 * @noinspection PhpUndefinedClassInspection
		 */
		\WP_CLI::add_command( 'ngg settings', '\Imagely\NGG\WPCLI\Settings' );
	}
}
