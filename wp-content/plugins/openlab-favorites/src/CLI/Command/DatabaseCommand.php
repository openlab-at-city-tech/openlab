<?php

namespace OpenLab\Favorites\CLI\Command;

use \OpenLab\Favorites\Schema;

use \WP_CLI;
use \WP_CLI_Command;

class DatabaseCommand extends WP_CLI_Command {
	/**
	 * Install the database table.
	 */
	public function install( $args, $assoc_args ) {
		Schema::install_table();
		WP_CLI::success( 'Successfully installed table!' );
	}
}
