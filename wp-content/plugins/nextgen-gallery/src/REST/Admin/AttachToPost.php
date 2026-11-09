<?php

namespace Imagely\NGG\REST\Admin;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataTypes\DisplayedGallery;

/**
 * REST API controller for Attach to Post functionality.
 */
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
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		return current_user_can( 'NextGEN Attach Interface' );
	}

	public function get_galleries( $request ) {
		$galleries    = GalleryMapper::get_instance()->find_all();
		$storage      = StorageManager::get_instance();
		$image_mapper = ImageMapper::get_instance();

		if ( ! is_array( $galleries ) ) {
			$galleries = [];
		}

		$pic_table = $image_mapper->get_table_name();

		// Image counts for all galleries in one query (avoids an N+1 per-gallery loop).
		// Routed through the mapper's run_query() so it uses the same DB/cache layer.
		$counts     = [];
		$count_rows = $image_mapper->run_query( "SELECT galleryid, COUNT(*) AS cnt FROM `{$pic_table}` GROUP BY galleryid", false, true );
		foreach ( (array) $count_rows as $row ) {
			$counts[ (int) $row->galleryid ] = (int) $row->cnt;
		}

		// Preview image records for all galleries in one IN() query.
		$preview_ids = array_unique(
			array_filter(
				array_map(
					static function ( $gallery ) {
						return (int) $gallery->previewpic;
					},
					$galleries
				)
			)
		);
		$preview_map = [];
		if ( $preview_ids ) {
			$preview_rows = $image_mapper->select()->where( [ 'pid IN %s', $preview_ids ] )->run_query( false, true );
			foreach ( (array) $preview_rows as $preview_image ) {
				$preview_map[ (int) $preview_image->pid ] = $preview_image;
			}
		}

		foreach ( $galleries as &$gallery ) {
			$gallery->image_count = $counts[ (int) $gallery->gid ] ?? 0;

			if ( $gallery->previewpic && isset( $preview_map[ (int) $gallery->previewpic ] ) ) {
				$gallery->previewpic_image_url = $storage->get_cache_busted_image_url( $preview_map[ (int) $gallery->previewpic ], 'thumb' );
			}

			// Drop the heavy serialized blob the picker never reads; keeps the payload small.
			unset( $gallery->display_type_settings );
		}
		unset( $gallery );

		return new \WP_REST_Response(
			[
				'items' => $galleries,
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
		foreach ( \get_terms( array_merge( [ 'taxonomy' => 'ngg_tag' ], $params ) ) as $term ) {
			$response['items'][] = [
				'id'    => $term,
				'title' => $term,
				'name'  => $term,
			];
		}

		return new \WP_REST_Response( $response );
	}

	public function get_images( $request ) {
		$response = [];

		$params = $request->get_param( 'displayed_gallery' );
		if ( ! is_array( $params ) ) {
			$params = [];
		}

		$storage      = StorageManager::get_instance();
		$image_mapper = ImageMapper::get_instance();

		$displayed_gallery = new DisplayedGallery();

		// Per-property allowlist + type cast for DisplayedGallery params arriving from the REST body.
		// Anything outside the allowlist is silently dropped; values are cast per group rather than
		// piped through esc_sql() (which is not a substitute for $wpdb->prepare()).
		// sortorder belongs with the ID arrays: it is the user's drag-and-drop order, a list of pids.
		// Classifying it as a string ran every array payload through is_scalar() and coerced it to '',
		// wiping the custom order so the modal reopened with the default gallery sort.
		$id_array_props = [ 'container_ids', 'entity_ids', 'excluded_container_ids', 'gallery_ids', 'image_ids', 'tag_ids', 'album_ids', 'ids', 'exclusions', 'sortorder' ];
		$string_props   = [ 'display_type', 'order_by', 'order_direction', 'returns', 'source', 'src', 'slug', 'transient_id' ];
		$int_props      = [ 'ID', 'id', 'maximum_entity_count', 'images_list_count' ];
		$bool_props     = [ 'is_album_gallery', 'skip_excluding_globally_excluded_images', 'tagcloud' ];
		$assoc_props    = [ 'display_settings' ];
		$text_props     = [ 'effect_code', 'inner_content', 'display' ];

		// Normalize keys once so the source lookup and the assignment loop key off identical values.
		$normalized = [];
		foreach ( $params as $raw_key => $raw_value ) {
			if ( ! is_string( $raw_key ) ) {
				continue;
			}
			$key = sanitize_key( $raw_key );
			if ( '' === $key ) {
				continue;
			}
			$normalized[ $key ] = $raw_value;
		}

		// container_ids holds numeric ids for gallery/album sources but tag names for tag sources, so its
		// sanitization depends on the source. Resolve it up front: key order is not guaranteed.
		$source = '';
		foreach ( [ 'source', 'src' ] as $source_key ) {
			if ( isset( $normalized[ $source_key ] ) && is_scalar( $normalized[ $source_key ] ) ) {
				$source = sanitize_text_field( (string) $normalized[ $source_key ] );
				break;
			}
		}

		// Tag sources (and aliases) address containers by name, not id; absint() would wipe them. Tag
		// names are escaped downstream in get_term_ids_for_tags().
		$is_tag_source  = in_array( $source, [ 'tags', 'tag', 'image_tags', 'image_tag' ], true );
		$tag_name_props = [ 'container_ids', 'excluded_container_ids' ];

		foreach ( $normalized as $key => $raw_value ) {
			if ( in_array( $key, $id_array_props, true ) ) {
				if ( $is_tag_source && in_array( $key, $tag_name_props, true ) ) {
					$raw_list = is_array( $raw_value )
						? $raw_value
						: ( is_string( $raw_value ) ? explode( ',', $raw_value ) : [] );

					// 'strlen' drops empty strings while keeping a tag literally named '0'.
					$value = array_values(
						array_filter(
							array_map(
								'sanitize_text_field',
								array_map( 'trim', array_map( 'strval', array_filter( $raw_list, 'is_scalar' ) ) )
							),
							'strlen'
						)
					);
				} elseif ( is_array( $raw_value ) ) {
					$value = array_values( array_filter( array_map( 'absint', $raw_value ) ) );
				} elseif ( is_string( $raw_value ) ) {
					$value = array_values(
						array_filter(
							array_map(
								'absint',
								array_map( 'trim', explode( ',', $raw_value ) )
							)
						)
					);
				} else {
					$value = [];
				}
			} elseif ( in_array( $key, $string_props, true ) ) {
				$value = is_scalar( $raw_value ) ? sanitize_text_field( (string) $raw_value ) : '';
			} elseif ( in_array( $key, $int_props, true ) ) {
				$value = is_scalar( $raw_value ) ? (int) $raw_value : 0;
			} elseif ( in_array( $key, $bool_props, true ) ) {
				$value = (bool) $raw_value;
			} elseif ( in_array( $key, $assoc_props, true ) ) {
				if ( is_array( $raw_value ) ) {
					$value = $raw_value;
				} elseif ( is_string( $raw_value ) ) {
					$decoded = json_decode( $raw_value, true );
					$value   = is_array( $decoded ) ? $decoded : [];
				} else {
					$value = [];
				}
			} elseif ( in_array( $key, $text_props, true ) ) {
				$value = is_scalar( $raw_value ) ? wp_kses_post( (string) $raw_value ) : '';
			} else {
				continue;
			}

			$displayed_gallery->$key = $value;
		}

		$response['items'] = $displayed_gallery->get_entities( false, false, false, 'both' );

		foreach ( $response['items'] as &$entity ) {
			$image = $entity;
   // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( in_array( $displayed_gallery->source, [ 'album','albums' ] ) ) {
				// Set the alttext of the preview image to the name of the gallery or album
				$image = $image_mapper->find( $entity->previewpic );
				if ( $image ) {
					if ( $entity->is_album ) {
						/* translators: %s: album name */
						$image->alttext = sprintf( \__( 'Album: %s', 'nggallery' ), $entity->name );
					} else {
						/* translators: %s: gallery title */
						$image->alttext = sprintf( \__( 'Gallery: %s', 'nggallery' ), $entity->title );
					}
				}

				// Prefix the id of an album with 'a'
				if ( $entity->is_album ) {
					$id                          = $entity->{$entity->id_field};
					$entity->{$entity->id_field} = 'a' . $id;
				}
			}

			// Get the thumbnail. A gallery/album with no previewpic resolves to null above.
			if ( $image ) {
				$entity->thumb_url  = $storage->get_cache_busted_image_url( $image, 'thumb' );
				$entity->thumb_html = $storage->get_image_html( $image, 'thumb' );
			} else {
				$entity->thumb_url  = '';
				$entity->thumb_html = '';
			}
		}

		return new \WP_REST_Response( $response );
	}
}
