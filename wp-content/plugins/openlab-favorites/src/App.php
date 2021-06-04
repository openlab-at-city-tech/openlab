<?php

namespace OpenLab\Favorites;

use OpenLab\Favorites\ROOT_DIR;

class App {
	public static function init() {
		require ROOT_DIR . '/includes/functions.php';

		if ( defined( 'WP_CLI' ) ) {
			self::set_up_cli_commands();
		}

		Frontend::init();
	}

	protected function set_up_cli_commands() {
		\WP_CLI::add_command( 'ol-favorites database', '\OpenLab\Favorites\CLI\Command\DatabaseCommand' );
	}
}
