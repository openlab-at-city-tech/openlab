<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;

class Album {

	/**
	 * Create a new album
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @synopsis <album_name> [--description=<description>] --author=<user_login>
	 */
	public function create( $args, $assoc_args ) {
		$mapper = AlbumMapper::get_instance();
		$user   = get_user_by( 'login', $assoc_args['author'] );
		if ( ! $user ) {
			\WP_CLI::error( "Unable to find user {$assoc_args['author']}" );
		}

		$description = ! empty( $assoc_args['description'] ) ? $assoc_args['description'] : '';

		$album = $mapper->create(
			[
				'name'      => $args[0],
				'albumdesc' => $description,
				'author'    => $user->ID,
			]
		);

		if ( $album && $album->save() ) {
			$album_id = $retval = $album->id();
			\WP_CLI::success( "Created album with id #{$album_id}" );
		} else {
			\WP_CLI::error( 'Unable to create album' );
		}
	}

	/**
	 * Deletes the requested album
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <album_id>
	 */
	public function delete( $args, $assoc_args ) {
		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $args[0] );
		if ( ! $album ) {
			\WP_CLI::error( "Unable to find album {$args[0]}" );
		}

		$mapper->destroy( $album );
		\WP_CLI::success( "Album with id #{$album->id} has been deleted" );
	}

	/**
	 * Change album attributes
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <album_id> [--description=<description>] [--name=<name>]
	 */
	public function edit( $args, $assoc_args ) {
		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $args[0] );
		if ( ! $album ) {
			\WP_CLI::error( "Unable to find album {$args[0]}" );
		}

		if ( empty( $assoc_args['description'] ) && empty( $assoc_args['name'] ) ) {
			\WP_CLI::error( 'You must provide a new description or title' );
		}

		if ( ! empty( $assoc_args['description'] ) ) {
			$album->albumdesc = $assoc_args['description'];
		}
		if ( ! empty( $assoc_args['name'] ) ) {
			$album->name = $assoc_args['name'];
		}

		$mapper->save( $album );
		\WP_CLI::success( "Album with id #{$album->id} has been modified" );
	}

	/**
	 * @param array $args
	 * @param array $assoc_args
	 * @subcommand list
	 */
	public function _list( $args, $assoc_args ) {
		$mapper  = AlbumMapper::get_instance();
		$display = [];
		foreach ( $mapper->find_all() as $album ) {
			$display[] = [
				'id'            => $album->id,
				'name'          => $album->name,
				'# of children' => count( $album->sortorder ),
				'description'   => $album->albumdesc,
			];
		}

		\WP_CLI\Utils\format_items( 'table', $display, [ 'id', 'name', '# of children', 'description' ] );
	}

	/**
	 * Adds child galleries or albums to an album
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <album_id> [--galleries=<galleries>] [--albums=<albums>]
	 */
	public function add_children( $args, $assoc_args ) {
		$album_mapper = AlbumMapper::get_instance();
		$album        = $album_mapper->find( $args[0] );
		if ( ! $album ) {
			\WP_CLI::error( "Unable to find album {$args[0]}" );
		}

		if ( empty( $assoc_args['galleries'] ) && empty( $assoc_args['albums'] ) ) {
			\WP_CLI::error( 'You must provide new child galleries or albums' );
		}

		if ( ! empty( $assoc_args['galleries'] ) ) {
			$new            = explode( ',', $assoc_args['galleries'] );
			$gallery_mapper = GalleryMapper::get_instance();
			foreach ( $new as $gallery_id ) {
				$gallery = $gallery_mapper->find( $gallery_id );
				if ( ! $gallery ) {
					\WP_CLI::error( "Unable to find gallery {$gallery_id}" );
				}
				if ( in_array( $gallery_id, $album->sortorder ) ) {
					\WP_CLI::error( "Gallery with id {$gallery_id} already belongs to this album" );
				}
				$album->sortorder[] = $gallery_id;
			}
		}

		if ( ! empty( $assoc_args['albums'] ) ) {
			$new = explode( ',', $assoc_args['albums'] );
			foreach ( $new as $album_id ) {
				$new_album = $album_mapper->find( $album_id );
				if ( ! $new_album ) {
					\WP_CLI::error( "Unable to find album {$album_id}" );
				}
				if ( in_array( $album_id, $album->sortorder ) ) {
					\WP_CLI::error( "Album with id {$album_id} already belongs to this album" );
				}
				if ( $album_id == $args[0] ) {
					\WP_CLI::error( 'Cannot add an album to itself' );
				}
				$album->sortorder[] = 'a' . $album_id;
			}
		}

		$album_mapper->save( $album );
		\WP_CLI::success( "Album with id #{$album->id} has been modified" );
	}

	/**
	 * Removes child galleries or albums attached to an album
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <album_id> [--galleries=<galleries>] [--albums=<albums>]
	 */
	public function delete_children( $args, $assoc_args ) {
		$album_mapper = AlbumMapper::get_instance();
		$parent_album = $album_mapper->find( $args[0] );
		if ( ! $parent_album ) {
			\WP_CLI::error( "Unable to find album {$args[0]}" );
		}

		if ( empty( $assoc_args['galleries'] ) && empty( $assoc_args['albums'] ) ) {
			\WP_CLI::error( 'You must provide a child gallery or album to remove' );
		}

		$galleries = [];
		$albums    = [];

		if ( ! empty( $assoc_args['galleries'] ) ) {
			$galleries = explode( ',', $assoc_args['galleries'] );
		}
		if ( ! empty( $assoc_args['albums'] ) ) {
			$albums = explode( ',', $assoc_args['albums'] );
		}

		foreach ( $parent_album->sortorder as $ndx => $child ) {
			foreach ( $galleries as $gallery ) {
				if ( $gallery == $child ) {
					unset( $parent_album->sortorder[ $ndx ] );
				}
			}
			foreach ( $albums as $album ) {
				if ( 'a' . $album == $child ) {
					unset( $parent_album->sortorder[ $ndx ] );
				}
			}
		}

		$album_mapper->save( $parent_album );
		\WP_CLI::success( "Album with id #{$parent_album->id} has been modified" );
	}

	/**
	 * Lists all child galleries and albums belonging to an album
	 *
	 * @param $args
	 * @param $assoc_args
	 * @synopsis <album_id>
	 */
	public function list_children( $args, $assoc_args ) {
		$album_mapper   = AlbumMapper::get_instance();
		$gallery_mapper = GalleryMapper::get_instance();

		$album = $album_mapper->find( $args[0] );
		if ( ! $album ) {
			\WP_CLI::error( "Unable to find album {$args[0]}" );
		}

		$display = [];
		foreach ( $album->sortorder as $child ) {
			$is_album = ( strpos( $child, 'a' ) === 0 ) ? true : false;
			if ( $is_album ) {
				$child       = str_replace( 'a', '', $child );
				$child_album = $album_mapper->find( $child );
				$display[]   = [
					'id'          => $child_album->id,
					'type'        => 'album',
					'title'       => $child_album->name,
					'description' => $child_album->albumdesc,
				];
			} else {
				$child_gallery = $gallery_mapper->find( $child );
				$display[]     = [
					'id'          => $child_gallery->gid,
					'type'        => 'gallery',
					'title'       => $child_gallery->title,
					'description' => $child_gallery->galdesc,
				];
			}
		}

		\WP_CLI\Utils\format_items( 'table', $display, [ 'id', 'type', 'title', 'description' ] );
	}
}
