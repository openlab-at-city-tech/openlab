<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\Display\I18N;

class Image {

	/**
	 * Import an image from the filesystem into NextGen
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @synopsis --filename=<absolute-path> --gallery=<gallery-id>
	 */
	public function import( $args, $assoc_args ) {
		$mapper  = GalleryMapper::get_instance();
		$storage = StorageManager::get_instance();

		if ( ( $gallery = $mapper->find( $assoc_args['gallery'], true ) ) ) {
			$file_data = @file_get_contents( $assoc_args['filename'] );
			$file_name = I18N::mb_basename( $assoc_args['filename'] );

			if ( empty( $file_data ) ) {
				\WP_CLI::error( 'Could not load file' );
			}

			$image_id = $storage->upload_base64_image( $gallery, $file_data, $file_name );

			if ( ! $image_id ) {
				\WP_CLI::error( 'Could not import image' );
			} else {
				\WP_CLI::success( "Imported image with id #{$image_id}" );
			}
		} else {
			\WP_CLI::error( "Gallery not found (with id #{$assoc_args['gallery']}" );
		}
	}

	/**
	 * Change image attributes
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <image_id> [--description=<description>] [--title=<title>]
	 */
	public function edit( $args, $assoc_args ) {
		$mapper = ImageMapper::get_instance();
		$image  = $mapper->find( $args[0] );
		if ( ! $image ) {
			\WP_CLI::error( "Unable to find image {$args[0]}" );
		}

		if ( empty( $assoc_args['description'] ) && empty( $assoc_args['title'] ) ) {
			\WP_CLI::error( 'You must provide a new description or title' );
		}

		if ( ! empty( $assoc_args['description'] ) ) {
			$image->description = $assoc_args['description'];
		}
		if ( ! empty( $assoc_args['title'] ) ) {
			$image->alttext = $assoc_args['title'];
		}

		$mapper->save( $image );
		\WP_CLI::success( "Image with id #{$image->pid} has been modified" );
	}
}
