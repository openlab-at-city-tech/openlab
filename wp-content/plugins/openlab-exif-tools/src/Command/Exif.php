<?php

namespace OpenLab\EXIF\Command;

use OpenLab\EXIF\Image;

use WP_CLI;

class Exif {
	/**
	 * Delete GPS-related EXIF data from one or more images.
	 *
	 * ## options
	 *
	 * [<path>]
	 * : Path to the image file.
	 *
	 * @subcommand delete-gps-data
	 */
	public function delete_gps_data( $args, $assoc_args ) {
		$path = isset( $args[0] ) ? $args[0] : null;

		if ( null !== $path ) {

			// If this is a directory, recurse.
			if ( is_dir( $path ) ) {
				$files = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator( $path )
				);

				foreach ( $files as $file ) {
					if ( $file->isFile() ) {
						$this->delete_gps_data( [ $file->getPathname() ], $assoc_args );
					}
				}
			} else {

				if ( false === strpos( $path, '/103/' ) ) {
					return;
				}

				$image = new Image( $path );

				if ( $image->has_gps_data() ) {
					$deleted = $image->delete_gps_data();
					if ( $deleted ) {
						WP_CLI::log( "Deleted GPS data from {$path}" );
					} else {
						WP_CLI::log( "Failed to delete GPS data from {$path}" );
					}
				} else {
					WP_CLI::log( "No GPS data found in {$path}" );
				}
			}
		}
	}
}
