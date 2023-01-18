<?php

namespace ElementsKit_Lite\Modules\Widget_Builder;

use ElementsKit_Lite\Modules\Widget_Builder\Controls\Widget_Writer;

defined( 'ABSPATH' ) || exit;


class Widget_File {

	private static $instance;


	public function get_file_path() {

		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_dir = $upload_dir . '/elementskit/custom_widgets';

		if ( ! is_dir( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}

		return $upload_dir;
	}


	public static function load_filesystem() {

		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem();
	}


	public function create( $wObj, $id ) {

		self::load_filesystem();

		global $wp_filesystem;

		$writer = new Widget_Writer( $wObj, $id, 'elementskit-lite' );

		$writer->start_backing( $wp_filesystem );
		$writer->finish_backing( $wp_filesystem );

		return true;
	}


	public static function get_wp_filesystem_pointer() {

		self::load_filesystem();

		global $wp_filesystem;

		return $wp_filesystem;
	}

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
