<?php

namespace OpenLab\EXIF;

class App {
	private $cli;

	private function __construct() {
		add_action( 'bp_core_pre_avatar_handle_crop', [ $this, 'delete_gps_data_prior_to_avatar_crop' ], 10, 2 );
	}

	public static function get_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function init() {
		$instance = self::get_instance();

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$instance->cli = CLI::get_instance();
			$instance->cli::init();
		}
	}

	public function delete_gps_data_prior_to_avatar_crop( $retval, $r ) {
		$image_path = bp_core_avatar_upload_path() . $r['original_file'];

		if ( ! file_exists( $image_path ) ) {
			return $retval;
		}

		$image = new Image( $image_path );
		$image->delete_gps_data();

		return $retval;
	}
}
