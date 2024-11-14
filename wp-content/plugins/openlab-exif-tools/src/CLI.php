<?php

namespace OpenLab\EXIF;

use WP_CLI;

class CLI {
	protected $commands = [
		'exif' => 'Exif',
	];

	private function __construct() {}

	public static function get_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function init() {
		$instance = self::get_instance();

		$instance->add_commands();
	}

	private function add_commands() {
		foreach ( $this->commands as $cli_command => $class ) {
			$command = __NAMESPACE__ . '\\Command\\' . $class;

			WP_CLI::add_command( $cli_command, $command );
		}
	}
}
