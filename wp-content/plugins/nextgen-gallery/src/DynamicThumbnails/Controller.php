<?php

namespace Imagely\NGG\DynamicThumbnails;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\StaticAssets;

/**
 * Dynamic thumbnails controller.
 */
class Controller {

	public function index_action( $return_output = false ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@set_time_limit( 0 );
		wp_raise_memory_limit();

		$dynthumbs = Manager::get_instance();

		$uri            = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$params         = $dynthumbs->get_params_from_uri( $uri );
		$request_params = $params;

		if ( $params != null ) {
			$storage = StorageManager::get_instance();

			$image_id = $params['image'];
			$size     = $dynthumbs->get_size_name( $params );
			$abspath  = $storage->get_image_abspath( $image_id, $size, true );
			$valid    = true;

			// Render invalid image if hash check fails
			if ( $abspath == null ) {
				$uri_plain = $dynthumbs->get_uri_from_params( $request_params );
				$hash      = \wp_hash( $uri_plain );

				if ( strpos( $uri, $hash ) === false ) {
					$valid    = false;
					$filename = StaticAssets::get_abspath( 'DynamicThumbnails/invalid_image.png' );
					readfile( $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
				}
			}

			if ( $valid ) {
				$storage->render_image( $image_id, $size );
			}
		}
	}
}
