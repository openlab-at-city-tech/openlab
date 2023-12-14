<?php

namespace Imagely\NGG\REST\Admin;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataTypes\DisplayedGallery;

class AttachToPost extends \WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'ngg/v1';
		$this->rest_base = 'admin/attach_to_post/';
	}

	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . 'galleries',
			[
				[
					'methods'             => \WP_REST_Server::ALLMETHODS,
					'callback'            => [ $this, 'get_galleries' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . 'albums',
			[
				[
					'methods'             => \WP_REST_Server::ALLMETHODS,
					'callback'            => [ $this, 'get_albums' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . 'tags',
			[
				[
					'methods'             => \WP_REST_Server::ALLMETHODS,
					'callback'            => [ $this, 'get_tags' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . 'images',
			[
				[
					'methods'             => \WP_REST_Server::ALLMETHODS,
					'callback'            => [ $this, 'get_images' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
			]
		);
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'NextGEN Attach Interface' );
	}

	public function get_galleries( $request ) {
		return new \WP_REST_Response(
			[
				'items' => GalleryMapper::get_instance()->find_all(),
			]
		);
	}

	public function get_albums( $request ) {
		return new \WP_REST_Response(
			[
				'items' => AlbumMapper::get_instance()->find_all(),
			]
		);
	}

	public function get_tags( $request ) {
		$response = [];

		$response['items'] = [];
		$params            = [ 'fields' => 'names' ];
		foreach ( \get_terms( 'ngg_tag', $params ) as $term ) {
			$response['items'][] = [
				'id'    => $term,
				'title' => $term,
				'name'  => $term,
			];
		}

		return new \WP_REST_Response( $response );
	}

	public function get_images( $request ) {
		global $wpdb;

		$response = [];

		$params = $request->get_param( 'displayed_gallery' );

		$storage      = StorageManager::get_instance();
		$image_mapper = ImageMapper::get_instance();

		$displayed_gallery = new DisplayedGallery();

		foreach ( $params as $key => $value ) {
			$key = $wpdb->_escape( $key );
			if ( ! in_array( $key, [ 'container_ids', 'entity_ids', 'sortorder' ] ) ) {
				$value = esc_sql( $value );
			}
			$displayed_gallery->$key = $value;
		}

		$response['items'] = $displayed_gallery->get_entities( false, false, false, 'both' );

		foreach ( $response['items'] as &$entity ) {
			$image = $entity;
			if ( in_array( $displayed_gallery->source, [ 'album','albums' ] ) ) {
				// Set the alttext of the preview image to the name of the gallery or album
				if ( ( $image = $image_mapper->find( $entity->previewpic ) ) ) {
					if ( $entity->is_album ) {
						$image->alttext = sprintf( \__( 'Album: %s', 'nggallery' ), $entity->name );
					} else {
						$image->alttext = sprintf( \__( 'Gallery: %s', 'nggallery' ), $entity->title );
					}
				}

				// Prefix the id of an album with 'a'
				if ( $entity->is_album ) {
					$id                          = $entity->{$entity->id_field};
					$entity->{$entity->id_field} = 'a' . $id;
				}
			}

			// Get the thumbnail
			$entity->thumb_url  = $storage->get_image_url( $image, 'thumb', true );
			$entity->thumb_html = $storage->get_image_html( $image, 'thumb' );
		}

		return new \WP_REST_Response( $response );
	}
}
