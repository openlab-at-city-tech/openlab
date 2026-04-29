<?php
/**
 * REST API endpoints for NextGEN Gallery albums.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataTypes\Album;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\Transient;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;

/**
 * Class AlbumREST
 * Handles REST API endpoints for NextGEN Gallery albums.
 *
 * @package Imagely\NGG\REST\DataMappers
 */
class AlbumREST {

	/**
	 * Sanitize per_page parameter to allow -1 for "all"
	 *
	 * @param mixed $value The value to sanitize.
	 * @return int
	 */
	public static function sanitize_per_page( $value ) {
		$int_value = (int) $value;
		// Allow -1 for "all", otherwise ensure positive
		return ( -1 === $int_value ) ? -1 : absint( $int_value );
	}

	/**
	 * Sanitize pageid parameter - converts null/empty to 0 (not linked)
	 *
	 * @param mixed $value The value to sanitize.
	 * @return int
	 */
	public static function sanitize_pageid( $value ) {
		// null, empty string, or 0 all mean "not linked"
		if ( null === $value || '' === $value || 0 === $value ) {
			return 0;
		}
		return absint( $value );
	}

	/**
	 * Register the REST API routes for albums
	 */
	public static function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/albums',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_albums' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'orderby'  => [
						'type'              => 'string',
						'enum'              => [
							'id',
							'name',
							'date_created',
							'date_modified',
							'slug',
							'previewpic',
						],
						'default'           => 'id',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'order'    => [
						'type'              => 'string',
						'enum'              => [ 'ASC', 'DESC' ],
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'per_page' => [
						'type'              => 'integer',
						'default'           => 25,
						'sanitize_callback' => [ self::class, 'sanitize_per_page' ],
					],
					'page'     => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'search'   => [
						'type'              => 'string',
						'description'       => 'Search albums by name',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Get a single album.
		register_rest_route(
			'imagely/v1',
			'/albums/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_album' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => [ self::class, 'validate_id' ],
					],
				],
			]
		);

		// Create a new album.
		register_rest_route(
			'imagely/v1',
			'/albums',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'create_album' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'name'                  => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => [ self::class, 'validate_album_name' ],
					],
					'description'           => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
						'validate_callback' => [ self::class, 'validate_album_description' ],
					],
					'preview_image_id'      => [
						'required'          => false,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => [ self::class, 'validate_preview_image_id' ],
					],
					'pageid'                => [
						'required'          => false,
						'type'              => 'integer',
						'sanitize_callback' => [ self::class, 'sanitize_pageid' ],
					],
					'display_type'          => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'display_type_settings' => [
						'required'          => false,
						'type'              => 'object',
						'sanitize_callback' => [ self::class, 'sanitize_display_type_settings' ],
					],
					'sortorder'             => [
						'required'          => false,
						'type'              => 'array',
						'sanitize_callback' => [ self::class, 'sanitize_sortorder' ],
						'validate_callback' => [ self::class, 'validate_sortorder' ],
					],
				],
			]
		);

		// Update an album.
		register_rest_route(
			'imagely/v1',
			'/albums/(?P<id>\d+)',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_album' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'id'                    => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => [ self::class, 'validate_id' ],
					],
					'name'                  => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => [ self::class, 'validate_album_name' ],
					],
					'description'           => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
						'validate_callback' => [ self::class, 'validate_album_description' ],
					],
					'preview_image_id'      => [
						'required'          => false,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => [ self::class, 'validate_preview_image_id' ],
					],
					'pageid'                => [
						'required'          => false,
						'type'              => 'integer',
						'sanitize_callback' => [ self::class, 'sanitize_pageid' ],
					],
					'sortorder'             => [
						'required'          => false,
						'type'              => 'array',
						'sanitize_callback' => [ self::class, 'sanitize_sortorder' ],
						'validate_callback' => [ self::class, 'validate_sortorder' ],
					],
					'display_type'          => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'display_type_settings' => [
						'required'          => false,
						'type'              => 'object',
						'sanitize_callback' => [ self::class, 'sanitize_display_type_settings' ],
					],
				],
			]
		);

		// Delete an album.
		register_rest_route(
			'imagely/v1',
			'/albums/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ self::class, 'delete_album' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => [ self::class, 'validate_id' ],
					],
				],
			]
		);
	}

	/**
	 * Validate album ID
	 *
	 * @param mixed $value The value to validate.
	 * @return mixed|\WP_Error
	 */
	public static function validate_id( $value ) {

		if ( $value <= 0 ) {
			return new WP_Error(
				'invalid_id',
				__( 'Album ID must be a positive integer', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $value );
		if ( ! $album ) {
			return new WP_Error(
				'album_not_found',
				// translators: %d is the album ID.
				sprintf( __( 'Album with ID %d not found', 'nggallery' ), $value ),
				[ 'status' => 404 ]
			);
		}

		return true;
	}

	/**
	 * Validate album name
	 *
	 * @param string $value The value to validate.
	 * @return string|\WP_Error
	 */
	public static function validate_album_name( $value ) {
		if ( empty( $value ) ) {
			return new WP_Error(
				'invalid_name',
				__( 'Album name cannot be empty', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		if ( strlen( $value ) > 255 ) {
			return new WP_Error(
				'invalid_name',
				__( 'Album name cannot be longer than 255 characters', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		return true;
	}

	/**
	 * Validate album description
	 *
	 * @param string $value The value to validate.
	 * @return string|\WP_Error
	 */
	public static function validate_album_description( $value ) {
		if ( strlen( $value ) > 65535 ) { // TEXT field limit.
			return new WP_Error(
				'invalid_description',
				__( 'Album description cannot be longer than 65535 characters', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		return true;
	}

	/**
	 * Validate preview image ID
	 *
	 * @param int $value The value to validate.
	 * @return int|\WP_Error
	 */
	public static function validate_preview_image_id( $value ) {
		if ( $value <= 0 ) {
			return new WP_Error(
				'invalid_preview_image_id',
				__( 'Preview image ID must be a positive integer', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Check cache first.
		$cache_key = 'ngg_image_exists_' . $value;
		$exists    = wp_cache_get( $cache_key );

		if ( false === $exists ) {
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$exists = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE pid = %d",
					$value
				)
			);
			wp_cache_set( $cache_key, $exists, '', 3600 ); // Cache for 1 hour.
		}

		if ( ! $exists ) {
			return new WP_Error(
				'invalid_preview_image_id',
				// translators: %d is the image ID.
				sprintf( __( 'Image with ID %d not found', 'nggallery' ), $value ),
				[ 'status' => 404 ]
			);
		}

		return true;
	}

	/**
	 * Check if user has permission to read albums
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Check if user has permission to edit albums
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Edit album' );
	}

	/**
	 * Get all albums
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_albums( WP_REST_Request $request ) {
		global $wpdb;
		$mapper = AlbumMapper::get_instance();

		// Get and validate order parameters.
		$orderby = $request->get_param( 'orderby' ) ?? 'id';
		$order   = strtoupper( $request->get_param( 'order' ) ?? 'ASC' );

		// Map frontend column names to database column names
		$column_mapping = [
			'id'         => 'id',
			'name'       => 'name',
			'created'    => 'date_created',
			'modified'   => 'date_modified',
			'slug'       => 'slug',
			'previewpic' => 'previewpic',
		];

		// Map the orderby parameter to the actual database column
		if ( isset( $column_mapping[ $orderby ] ) ) {
			$orderby = $column_mapping[ $orderby ];
		}

		// Get pagination parameters.
		$per_page_param = (int) $request->get_param( 'per_page' );
		// Normalize all negative values to -1 (treated as "all") for consistency.
		if ( $per_page_param < 0 ) {
			$per_page_param = -1;
		}
		// Handle -1 as "all" (WordPress standard for unlimited pagination)
		$per_page = ( -1 === $per_page_param ) ? PHP_INT_MAX : $per_page_param;
		$page     = $request->get_param( 'page' );
		$offset   = ( $page - 1 ) * $per_page;

		$cache_params  = [
			$orderby,
			$order,
			$per_page_param,
			$page,
			$request->get_param( 'search' ),
		];
		$cache_key     = Transient::create_key( 'rest_albums', $cache_params );
		$cached_result = Transient::fetch( $cache_key, false );

		if ( $cached_result ) {
			// Normalize objects → arrays for REST response
			$response = json_decode( wp_json_encode( $cached_result['response'] ?? [] ), true );
			$result   = new WP_REST_Response( $response, 200 );
			$result->header( 'X-WP-Total', $cached_result['total_items'] ?? 0 );
			$result->header( 'X-WP-TotalPages', $cached_result['total_pages'] ?? 0 );
			return $result;
		}
		// Build the base query and apply filters.
		$query = $mapper->select();

		if ( $request->has_param( 'search' ) ) {
			$search_term = $request->get_param( 'search' );
			$query->where( [ 'name LIKE %s', '%' . $search_term . '%' ] );
		}

		// Calculate total items for pagination using the same filters.
		$where_clauses = [];
		$params        = [];

		if ( $request->has_param( 'search' ) ) {
			$search_term     = '%' . $request->get_param( 'search' ) . '%';
			$where_clauses[] = 'name LIKE %s';
			$params[]        = $search_term;
		}

		$table_name = $wpdb->nggalbum;
		$sql        = "SELECT COUNT(*) FROM {$table_name}";

		if ( ! empty( $where_clauses ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		if ( ! empty( $params ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$sql = $wpdb->prepare( $sql, $params );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
		$total_items = (int) $wpdb->get_var( $sql );

		// Fetch current page of items.
		$query->order_by( $orderby, $order )
			->limit( $per_page, $offset );

		$albums = $query->run_query();

		$response = [];
		foreach ( $albums as $album ) {
			$response[] = self::prepare_album_list_item_for_response( $album );
		}

		$total_pages = ceil( $total_items / $per_page );
		$cache_data  = [
			'response'    => $response,
			'total_items' => $total_items,
			'total_pages' => $total_pages,
		];
		Transient::update( $cache_key, $cache_data );
		$result = new WP_REST_Response( $response, 200 );

		// Add pagination headers.
		$result->header( 'X-WP-Total', $total_items );
		$result->header( 'X-WP-TotalPages', $total_pages );

		return $result;
	}

	/**
	 * Get a single album.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_album( WP_REST_Request $request ) {
		$id     = $request->get_param( 'id' );
		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $id );

		if ( ! $album ) {
			return new WP_Error(
				'album_not_found',
				// translators: %d is the album ID.
				sprintf( __( 'Album with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		return new WP_REST_Response( self::prepare_album_for_response( $album ), 200 );
	}

	/**
	 * Create a new album.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function create_album( WP_REST_Request $request ) {
		$mapper = AlbumMapper::get_instance();
		$album  = new Album();

		$album->name      = $request->get_param( 'name' );
		$album->albumdesc = $request->get_param( 'description' ) ?? '';
		$album->pageid    = $request->get_param( 'pageid' ) ?? 0;
		$album->sortorder = $request->get_param( 'sortorder' ) ?? [];

		// Set preview image: use provided preview_image_id or default to first gallery thumbnail
		if ( $request->has_param( 'preview_image_id' ) && $request->get_param( 'preview_image_id' ) ) {
			$album->previewpic = $request->get_param( 'preview_image_id' );
		} else {
			$album->previewpic = self::get_first_gallery_thumbnail( $album->sortorder );
		}

		// Handle display type fields
		if ( $request->has_param( 'display_type' ) ) {
			$album->display_type = $request->get_param( 'display_type' );
		}
		if ( $request->has_param( 'display_type_settings' ) ) {
			$album->display_type_settings = $request->get_param( 'display_type_settings' );
		}

		try {
			// Ensure defaults are set before saving (including slug generation)
			$mapper->set_defaults( $album );
			$result = $mapper->save( $album );

			Transient::flush( 'rest_albums' );
			return new WP_REST_Response(
				[
					'album'   => self::prepare_album_for_response( $album ),
					'message' => __( 'Album created successfully', 'nggallery' ),
				],
				201
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'save_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Update an existing album.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function update_album( WP_REST_Request $request ) {
		$id     = $request->get_param( 'id' );
		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $id );

		if ( ! $album ) {
			return new WP_Error(
				'album_not_found',
				// translators: %d is the album ID.
				sprintf( __( 'Album with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		// Only update fields that were provided.
		if ( $request->has_param( 'name' ) ) {
			$album->name = $request->get_param( 'name' );
		}
		if ( $request->has_param( 'description' ) ) {
			$album->albumdesc = $request->get_param( 'description' );
		}
		if ( $request->has_param( 'preview_image_id' ) ) {
			$album->previewpic = $request->get_param( 'preview_image_id' );
		}
		if ( $request->has_param( 'pageid' ) ) {
			$album->pageid = $request->get_param( 'pageid' );
		}

		// Handle sortorder field
		if ( $request->has_param( 'sortorder' ) ) {
			$album->sortorder = $request->get_param( 'sortorder' );
		}

		// Handle display type fields
		if ( $request->has_param( 'display_type' ) ) {
			$album->display_type = $request->get_param( 'display_type' );
		}
		if ( $request->has_param( 'display_type_settings' ) ) {
			$album->display_type_settings = $request->get_param( 'display_type_settings' );
		}

		try {
			// Ensure defaults are set before saving (including slug generation if missing)
			$mapper->set_defaults( $album );

			$mapper->save( $album );
			Transient::flush( 'rest_albums' );
			return new WP_REST_Response(
				[
					'album'   => self::prepare_album_for_response( $album ),
					'message' => __( 'Album updated successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'save_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Delete an album.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function delete_album( WP_REST_Request $request ) {
		$id     = $request->get_param( 'id' );
		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $id );

		if ( ! $album ) {
			return new WP_Error(
				'album_not_found',
				// translators: %d is the album ID.
				sprintf( __( 'Album with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		try {
			$mapper->destroy( $album );
			Transient::flush( 'rest_albums' );
			return new WP_REST_Response(
				[
					'message' => __( 'Album deleted successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'delete_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Prepare album list item for API response.
	 *
	 * @param object $album The album object.
	 *
	 * @return array {
	 *     Album data for list view.
	 *
	 *     @type int    $id                  Album ID.
	 *     @type string $albumTitle          Album name.
	 *     @type string $shortcode           Album shortcode.
	 *     @type int    $count               Number of galleries.
	 *     @type string $thumbnail           Preview image URL.
	 *     @type string $created             Creation date in GMT.
	 *     @type string $modified            Last modified date in GMT.
	 *     @type string $displayType         Album display type.
	 * }
	 */
	private static function prepare_album_list_item_for_response( $album ) {
		$thumbnail = '';
		if ( $album->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $album->previewpic, 'thumb' );
		}

		// Count galleries from sortorder (can be array or JSON string)
		$gallery_count = 0;
		if ( ! empty( $album->sortorder ) ) {
			if ( is_array( $album->sortorder ) ) {
				$gallery_count = count( $album->sortorder );
			} elseif ( is_string( $album->sortorder ) ) {
				$sortorder = json_decode( $album->sortorder, true );
				if ( is_array( $sortorder ) ) {
					$gallery_count = count( $sortorder );
				}
			}
		}

		return [
			'id'          => $album->id,
			'albumTitle'  => $album->name,
			'shortcode'   => '[imagely album="' . $album->id . '"]',
			'count'       => $gallery_count,
			'thumbnail'   => $thumbnail,
			'created'     => $album->date_created,
			'modified'    => $album->date_modified,
			'displayType' => $album->display_type ?? 'photocrati-nextgen_basic_thumbnails',
		];
	}

	/**
	 * Prepare album data for API response
	 *
	 * @param object $album The album object.
	 * @return array
	 */
	private static function prepare_album_for_response( $album ) {
		$thumbnail = '';
		if ( $album->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $album->previewpic, 'thumb' );
		}

		// Count galleries from sortorder (can be array or JSON string)
		$gallery_count = 0;
		if ( ! empty( $album->sortorder ) ) {
			if ( is_array( $album->sortorder ) ) {
				$gallery_count = count( $album->sortorder );
			} elseif ( is_string( $album->sortorder ) ) {
				$sortorder = json_decode( $album->sortorder, true );
				if ( is_array( $sortorder ) ) {
					$gallery_count = count( $sortorder );
				}
			}
		}

		return [
			'id'                    => $album->id,
			'name'                  => $album->name,
			'description'           => $album->albumdesc,
			'preview_image_id'      => $album->previewpic,
			'previewpic_url'        => $thumbnail,
			'slug'                  => $album->slug,
			'pageid'                => $album->pageid,
			'date_created'          => $album->date_created,
			'date_modified'         => $album->date_modified,
			'counter'               => $gallery_count,
			'sortorder'             => $album->sortorder,
			'extras_post_id'        => $album->extras_post_id,
			'display_type'          => $album->display_type ?? 'photocrati-nextgen_basic_thumbnails',
			'display_type_settings' => $album->display_type_settings ?? [],
		];
	}

	/**
	 * Sanitize display type settings.
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public static function sanitize_display_type_settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			return [];
		}

		$sanitized = [];
		foreach ( $settings as $display_type => $type_settings ) {
			$display_type = sanitize_text_field( $display_type );

			if ( ! is_array( $type_settings ) ) {
				continue;
			}

			$sanitized_type_settings = [];
			foreach ( $type_settings as $key => $value ) {
				$key = sanitize_text_field( $key );

				switch ( gettype( $value ) ) {
					case 'boolean':
						$sanitized_type_settings[ $key ] = (bool) $value;
						break;
					case 'integer':
						$sanitized_type_settings[ $key ] = (int) $value;
						break;
					case 'double':
						$sanitized_type_settings[ $key ] = (float) $value;
						break;
					case 'string':
						$sanitized_type_settings[ $key ] = wp_kses_post( $value );
						break;
					case 'array':
						$sanitized_type_settings[ $key ] = array_map( 'sanitize_text_field', $value );
						break;
					default:
						$sanitized_type_settings[ $key ] = null;
				}
			}
			$sanitized[ $display_type ] = $sanitized_type_settings;
		}

		return $sanitized;
	}

	/**
	 * Get the thumbnail image ID from the first gallery in the sortorder.
	 *
	 * @param array $sortorder Array of gallery IDs.
	 * @return int Image ID for the album thumbnail.
	 */
	private static function get_first_gallery_thumbnail( $sortorder ) {
		if ( empty( $sortorder ) || ! is_array( $sortorder ) ) {
			return 0;
		}

		$first_gallery_id = absint( $sortorder[0] );
		if ( $first_gallery_id <= 0 ) {
			return 0;
		}

		$gallery_mapper = GalleryMapper::get_instance();
		$gallery        = $gallery_mapper->find( $first_gallery_id );

		if ( ! $gallery ) {
			return 0;
		}

		// If gallery has a preview image set, use that
		if ( ! empty( $gallery->previewpic ) && $gallery->previewpic > 0 ) {
			return $gallery->previewpic;
		}

		// Otherwise, get the first image from this gallery
		$image_mapper = ImageMapper::get_instance();
		$images       = $image_mapper->find_all(
			array(
				'galleryid' => $first_gallery_id,
				'limit'     => 1,
			)
		);

		if ( ! empty( $images ) && isset( $images[0] ) ) {
			return $images[0]->pid;
		}

		return 0;
	}

	/**
	 * Sanitize sortorder array.
	 *
	 * @param array $sortorder The sortorder array to sanitize.
	 * @return array
	 */
	public static function sanitize_sortorder( $sortorder ) {
		if ( ! is_array( $sortorder ) ) {
			return [];
		}

		$gallery_mapper = GalleryMapper::get_instance();
		$album_mapper   = AlbumMapper::get_instance();
		$valid_items    = [];

		foreach ( $sortorder as $item ) {
			// Check if it's an album (starts with 'a')
			if ( is_string( $item ) && strpos( $item, 'a' ) === 0 ) {
				$album_id = absint( substr( $item, 1 ) );
				if ( $album_id > 0 ) {
					// Verify album exists before adding to sortorder
					$album = $album_mapper->find( $album_id );
					if ( $album ) {
						$valid_items[] = 'a' . $album_id;
					}
				}
			} else {
				// It's a gallery ID
				$gallery_id = absint( $item );
				if ( $gallery_id > 0 ) {
					// Verify gallery exists before adding to sortorder
					$gallery = $gallery_mapper->find( $gallery_id );
					if ( $gallery ) {
						$valid_items[] = $gallery_id;
					}
				}
			}
		}

		return $valid_items;
	}

	/**
	 * Validate sortorder array.
	 *
	 * @param array $sortorder The sortorder array to validate.
	 * @return bool|WP_Error
	 */
	public static function validate_sortorder( $sortorder ) {
		if ( ! is_array( $sortorder ) ) {
			return new WP_Error(
				'invalid_sortorder',
				__( 'Sortorder must be an array of gallery and album IDs', 'nggallery' )
			);
		}

		// Basic validation passed
		return true;
	}
}
