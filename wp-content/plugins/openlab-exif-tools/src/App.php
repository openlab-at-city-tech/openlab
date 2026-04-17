<?php

namespace OpenLab\EXIF;

class App {
	private $cli;

	private function __construct() {
		add_action( 'bp_core_pre_avatar_handle_crop', [ $this, 'delete_gps_data_prior_to_avatar_crop' ], 10, 2 );

		// Strip GPS data from all media uploads.
		add_filter( 'wp_handle_upload', [ $this, 'delete_gps_data_on_upload' ], 10, 2 );
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

	/**
	 * Deletes GPS data from images uploaded via the standard WordPress upload process.
	 *
	 * @param array  $upload Array of upload data (file, url, type).
	 * @param string $context The type of upload action ('upload', 'sideload').
	 * @return array The (unmodified) upload data.
	 */
	public function delete_gps_data_on_upload( $upload, $context = 'upload' ) {
		// Only process images that can contain EXIF data.
		$supported_types = [ 'image/jpeg', 'image/tiff' ];
		if ( ! isset( $upload['type'] ) || ! in_array( $upload['type'], $supported_types, true ) ) {
			return $upload;
		}

		if ( empty( $upload['file'] ) || ! file_exists( $upload['file'] ) ) {
			return $upload;
		}

		$image = new Image( $upload['file'] );
		$image->delete_gps_data();

		return $upload;
	}
}
