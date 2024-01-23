<?php

namespace Imagely\NGG\DynamicThumbnails;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\StaticAssets;

class Controller {

	public function index_action( $return = false ) {
		@set_time_limit( 0 );
		@ini_set( 'memory_limit', '-1' );

		$dynthumbs = Manager::get_instance();

		$uri            = $_SERVER['REQUEST_URI'];
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
					readfile( $filename );
				}
			}

			if ( $valid ) {
				$storage->render_image( $image_id, $size );
			}
		}
	}
}
