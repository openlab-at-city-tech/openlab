<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;

class Gallery {

	/**
	 * Create a new gallery
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @synopsis <gallery_name> [--description=<description>] --author=<user_login>
	 */
	public function create( $args, $assoc_args ) {
		$mapper = GalleryMapper::get_instance();
		$user   = get_user_by( 'login', $assoc_args['author'] );
		if ( ! $user ) {
			\WP_CLI::error( "Unable to find user {$assoc_args['author']}" );
		}

		$description = ! empty( $assoc_args['description'] ) ? $assoc_args['description'] : '';

		$gallery = $mapper->create(
			[
				'title'   => $args[0],
				'galdesc' => $description,
				'author'  => $user->ID,
			]
		);

		if ( $gallery && $gallery->save() ) {
			$gallery_id = $retval = $gallery->id();
			\WP_CLI::success( "Created gallery with id #{$gallery_id}" );
		} else {
			\WP_CLI::error( 'Unable to create gallery' );
		}
	}

	/**
	 * Deletes the requested gallery
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <gallery_id> [--delete-files]
	 */
	public function delete( $args, $assoc_args ) {
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $args[0] );
		if ( ! $gallery ) {
			\WP_CLI::error( "Unable to find gallery {$args[0]}" );
		}

		$remove_files = ! empty( $assoc_args['delete-files'] ) ? true : false;

		$mapper->destroy( $gallery, $remove_files );
		\WP_CLI::success( "Gallery with id #{$gallery->gid} has been deleted" );
	}

	/**
	 * Change gallery attributes
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <gallery_id> [--description=<description>] [--title=<title>]
	 */
	public function edit( $args, $assoc_args ) {
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $args[0] );
		if ( ! $gallery ) {
			\WP_CLI::error( "Unable to find gallery {$args[0]}" );
		}

		if ( empty( $assoc_args['description'] && empty( $assoc_args['title'] ) ) ) {
			\WP_CLI::error( 'You must provide a new description or title' );
		}

		if ( ! empty( $assoc_args['description'] ) ) {
			$gallery->galdesc = $assoc_args['description'];
		}
		if ( ! empty( $assoc_args['title'] ) ) {
			$gallery->name = $assoc_args['title'];
		}

		$mapper->save( $gallery );
		\WP_CLI::success( "Gallery with id #{$gallery->gid} has been modified" );
	}

	/**
	 * @param array $args
	 * @param array $assoc_args
	 * @subcommand list
	 */
	public function _list( $args, $assoc_args ) {
		$mapper  = GalleryMapper::get_instance();
		$display = [];
		foreach ( $mapper->find_all() as $gallery ) {
			$display[] = [
				'id'          => $gallery->gid,
				'title'       => $gallery->name,
				'path'        => $gallery->path,
				'description' => $gallery->galdesc,
			];
		}

		\WP_CLI\Utils\format_items( 'table', $display, [ 'id', 'title', 'path', 'description' ] );
	}

	/**
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <gallery_id>
	 */
	public function list_children( $args, $assoc_args ) {
		$gallery_mapper = GalleryMapper::get_instance();
		$image_mapper   = ImageMapper::get_instance();

		$gallery = $gallery_mapper->find( $args[0] );
		if ( ! $gallery ) {
			\WP_CLI::error( "Unable to find gallery {$args[0]}" );
		}

		$images  = $image_mapper->select()->where( [ 'galleryid = %s', $gallery->gid ] )->run_query();
		$display = [];

		foreach ( $images as $image ) {
			$display[] = [
				'id'          => $image->pid,
				'title'       => $image->alttext,
				'excluded'    => 0 === boolval( $image->exclude ) ? 'no' : 'yes',
				'sort order'  => $image->sortorder,
				'description' => $image->description,
			];
		}

		\WP_CLI\Utils\format_items( 'table', $display, [ 'id', 'title', 'excluded', 'sort order', 'description' ] );
	}
}
