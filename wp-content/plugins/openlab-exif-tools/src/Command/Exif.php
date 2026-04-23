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
			if ( ! is_readable( $path ) ) {
				WP_CLI::warning( "Skipping unreadable path {$path}" );

				return;
			}

			// If this is a directory, recurse.
			if ( is_dir( $path ) ) {
				$files = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator(
						$path,
						\FilesystemIterator::SKIP_DOTS
					),
					\RecursiveIteratorIterator::LEAVES_ONLY,
					\RecursiveIteratorIterator::CATCH_GET_CHILD
				);

				foreach ( $files as $file ) {
					if ( $file->isFile() && $file->isReadable() ) {
						$this->delete_gps_data( [ $file->getPathname() ], $assoc_args );
					} elseif ( $file->isFile() ) {
						WP_CLI::warning( "Skipping unreadable path {$file->getPathname()}" );
					}
				}
			} else {

				$image = new Image( $path );

				if ( $image->has_gps_data() ) {
					$deleted = $image->delete_gps_data();
					if ( $deleted ) {
						WP_CLI::log( "Deleted GPS data from {$path}" );
					} elseif ( $image->get_last_error() ) {
						WP_CLI::warning( "Could not delete GPS data from {$path}: {$image->get_last_error()}" );
					} else {
						WP_CLI::log( "Failed to delete GPS data from {$path}" );
					}
				} elseif ( $image->get_last_error() ) {
					WP_CLI::warning( "Could not read EXIF data from {$path}: {$image->get_last_error()}" );
				} else {
					WP_CLI::log( "No GPS data found in {$path}" );
				}
			}
		}
	}
}
