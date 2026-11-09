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
					'orderby'          => [
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
					'order'            => [
						'type'              => 'string',
						'enum'              => [ 'ASC', 'DESC' ],
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'per_page'         => [
						'type'              => 'integer',
						'default'           => 25,
						'sanitize_callback' => [ self::class, 'sanitize_per_page' ],
					],
					'page'             => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'search'           => [
						'type'              => 'string',
						'description'       => 'Search albums by name',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'exclude_ids'      => [
						'type'              => 'array',
						'description'       => 'Album IDs to exclude from results (e.g. the album currently being edited or albums already added).',
						'items'             => [
							'type' => 'integer',
						],
						'sanitize_callback' => function ( $value ) {
							if ( ! is_array( $value ) ) {
								return [];
							}
							return array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
						},
					],
					'include_ids'      => [
						'type'              => 'array',
						'description'       => 'Restrict results to these album IDs. Lets callers fetch a known, bounded set (e.g. the albums nested in the album being edited) in one request instead of loading every album with per_page=-1.',
						'items'             => [
							'type' => 'integer',
						],
						'sanitize_callback' => function ( $value ) {
							if ( ! is_array( $value ) ) {
								return [];
							}
							return array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
						},
					],
					'exclude_album_id' => [
						'type'              => 'integer',
						'description'       => 'Exclude this album and the albums nested inside it. Bounded alternative to exclude_ids for albums with many members (the server derives the IDs from the album\'s sortorder).',
						'sanitize_callback' => 'absint',
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
						// Cast keeping the sign (not absint) so a negative ID stays negative and hits the
						// fallback in update_album() instead of flipping to a real image (-42 -> 42). Wrapped
						// in a closure because WP passes ($value, $request, $param) to sanitize_callback and
						// the built-in intval() rejects the extra args (ArgumentCountError -> HTTP 500).
						'sanitize_callback' => static function ( $value ) {
							return (int) $value;
						},
						// Existence checked in update_album() instead of here, so a stale unchanged
						// previewpic can't block an edit (#875).
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

		if ( ! self::image_exists( $value ) ) {
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
	 * Whether an image with the given pid exists.
	 *
	 * @param int $pid The image ID to check.
	 * @return bool
	 */
	public static function image_exists( $pid ) {
		$pid = (int) $pid;
		if ( $pid <= 0 ) {
			return false;
		}

		// Via the mapper (not a standalone COUNT + cache key) so image deletion invalidates the result
		// and a preview set to a just-deleted image self-heals instead of hitting a stale positive.
		return (bool) ImageMapper::get_instance()->find( $pid );
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
	 * Check if user has permission to edit albums.
	 *
	 * Per-album ownership gate: holders of 'NextGEN Edit album' may create albums, but PUT/DELETE
	 * on `/albums/{id}` additionally require the user to own the album or hold the cross-author cap.
	 * Mirrors the ownership pattern PR #805 applied on `ImageREST` so authors cannot mutate albums
	 * belonging to other authors (IDOR).
	 *
	 * @param \WP_REST_Request|null $request The REST request (passed by WP for permission_callback).
	 * @return bool
	 */
	public static function check_edit_permission( $request = null ) {
		if ( ! Security::is_allowed( 'NextGEN Edit album' ) ) {
			return false;
		}

		if ( ! $request instanceof WP_REST_Request ) {
			return true;
		}

		$url_params = $request->get_url_params();
		$album_id   = isset( $url_params['id'] ) ? (int) $url_params['id'] : 0;
		if ( $album_id <= 0 ) {
			// POST /albums (create) — no per-album id, base cap is sufficient.
			return true;
		}

		return self::current_user_can_manage_album( $album_id );
	}

	/**
	 * Determine whether the current user may manage a specific album.
	 *
	 * Shared object-level ownership gate for every entry point that accepts an album id from a
	 * request. The REST routes here, the XML-RPC album methods (`XMLRPC\Controller::get_album()`,
	 * reached by `ngg.editAlbum` / `ngg.deleteAlbum`) and the Lightroom album tasks all call it so
	 * authors cannot mutate or delete albums belonging to other authors (IDOR). See #1096.
	 *
	 * Holders of the cross-author `nextgen_edit_gallery_unowned` capability may manage any album.
	 * Otherwise ownership is derived from the album's linked `extras_post_id` row in `wp_posts`
	 * (post_author), because the `ngg_album` table has no `author` column. When no extras post
	 * exists yet (orphan), only cross-author-cap holders may manage it.
	 *
	 * Callers must still enforce the base `nextgen_edit_album` capability themselves; this predicate
	 * only narrows that capability to the albums the caller owns.
	 *
	 * @param int $album_id Album primary key.
	 * @return bool
	 */
	public static function current_user_can_manage_album( $album_id ) {
		// Cross-author cap holders may manage any album.
		if ( Security::is_allowed( 'nextgen_edit_gallery_unowned' ) ) {
			return true;
		}

		$mapper = AlbumMapper::get_instance();
		$album  = $mapper->find( $album_id );
		if ( ! $album ) {
			// Unknown id — let downstream return 404 via validate_id. Don't grant access here.
			return false;
		}

		$extras_post_id = isset( $album->extras_post_id ) ? (int) $album->extras_post_id : 0;
		$post           = $extras_post_id > 0 ? get_post( $extras_post_id ) : null;

		// An orphan album has no author, so an id of 0 leaves the gate on the cross-author cap.
		return Security::can_manage_owned( $post ? $post->post_author : 0, 'NextGEN Edit album' );
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

		// When exclude_album_id is supplied, exclude that album itself plus the albums nested inside it,
		// derived server-side. This keeps the request URL bounded for albums with many members (one
		// integer instead of one exclude_ids[] entry per nested album).
		$exclude_album_id = (int) $request->get_param( 'exclude_album_id' );
		if ( $exclude_album_id > 0 ) {
			$existing_excludes = (array) $request->get_param( 'exclude_ids' );
			$request->set_param(
				'exclude_ids',
				array_values( array_unique( array_merge( $existing_excludes, self::get_album_child_album_ids( $exclude_album_id ) ) ) )
			);
		}

		// Get and validate order parameters.
		// Security fix (SQLi): normalize orderby input via sanitize_key before mapping so only safe identifier chars are considered.
		$orderby = sanitize_key( (string) ( $request->get_param( 'orderby' ) ?? 'id' ) );
		// Security fix (SQLi): restrict order direction to ASC/DESC; anything else defaults to ASC.
		$order = strtoupper( (string) ( $request->get_param( 'order' ) ?? 'ASC' ) );
		$order = in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'ASC';

		// Map frontend column names to database column names
		$column_mapping = [
			'id'         => 'id',
			'name'       => 'name',
			'created'    => 'date_created',
			'modified'   => 'date_modified',
			'slug'       => 'slug',
			'previewpic' => 'previewpic',
		];

		// Security fix (SQLi): require orderby to be a known mapping key; unmapped values fall back to 'id' instead of passing through raw (previously missing else allowed ORDER BY injection).
		if ( isset( $column_mapping[ $orderby ] ) ) {
			$orderby = $column_mapping[ $orderby ];
		} else {
			$orderby = 'id';
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

		// Restrict to an explicit ID set when include_ids is supplied. Present-but-empty means the caller
		// wants a known set that happens to be empty (e.g. an album with no nested albums), so it must
		// resolve to zero rows rather than falling through to "all albums".
		$has_include_ids = $request->has_param( 'include_ids' );
		$include_ids     = $request->get_param( 'include_ids' );
		$include_ids     = is_array( $include_ids ) ? array_values( array_unique( array_filter( array_map( 'absint', $include_ids ) ) ) ) : [];

		$cache_params  = [
			$orderby,
			$order,
			$per_page_param,
			$page,
			$request->get_param( 'search' ),
			$request->get_param( 'exclude_ids' ),
			$has_include_ids ? $include_ids : 'none',
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

		// include_ids was supplied but resolved to nothing: the caller asked for a specific, empty set,
		// so return no albums instead of every album.
		if ( $has_include_ids && empty( $include_ids ) ) {
			$result = new WP_REST_Response( [], 200 );
			$result->header( 'X-WP-Total', 0 );
			$result->header( 'X-WP-TotalPages', 0 );
			return $result;
		}

		// Build the base query and apply filters.
		$query = $mapper->select();

		// Skipped when empty: "name LIKE '%%'" would drop every row with a NULL name. esc_like()
		// keeps a typed % or _ literal. The COUNT query below must build the same predicate.
		$search_term = trim( (string) $request->get_param( 'search' ) );
		if ( '' !== $search_term ) {
			$search_wildcard = '%' . $wpdb->esc_like( $search_term ) . '%';
			if ( ctype_digit( $search_term ) ) {
				// A digit-only term matches the ID as well as the name, so albums saved without a
				// name (listed by their ID in the picker) stay reachable. The driver parenthesises
				// each where_clauses entry before AND-joining, so precedence is preserved.
				$query->add_where_clause(
					[
						$query->_parse_where_clause( 'name LIKE %s', [ $search_wildcard ] ),
						$query->_parse_where_clause( 'id = %d', [ (int) $search_term ] ),
					],
					'OR'
				);
			} else {
				$query->where( [ 'name LIKE %s', $search_wildcard ] );
			}
		}

		// Restrict to an explicit set of album IDs when requested (bounded fetch, avoids per_page=-1).
		if ( ! empty( $include_ids ) ) {
			$query->where( [ 'id IN %s', $include_ids ] );
		}

		// Exclude specific album IDs (e.g. the album being edited) server-side so pagination
		// and the per_page budget are only consumed by genuinely available albums.
		$exclude_ids = $request->get_param( 'exclude_ids' );
		$exclude_ids = is_array( $exclude_ids ) ? array_values( array_unique( array_filter( array_map( 'absint', $exclude_ids ) ) ) ) : [];
		if ( ! empty( $exclude_ids ) ) {
			$query->where( [ 'id NOT IN %s', $exclude_ids ] );
		}

		// Calculate total items for pagination using the same filters.
		$where_clauses = [];
		$params        = [];

		if ( '' !== $search_term ) {
			if ( ctype_digit( $search_term ) ) {
				$where_clauses[] = '( name LIKE %s OR id = %d )';
				$params[]        = '%' . $wpdb->esc_like( $search_term ) . '%';
				$params[]        = (int) $search_term;
			} else {
				$where_clauses[] = 'name LIKE %s';
				$params[]        = '%' . $wpdb->esc_like( $search_term ) . '%';
			}
		}

		if ( ! empty( $include_ids ) ) {
			$placeholders    = implode( ',', array_fill( 0, count( $include_ids ), '%d' ) );
			$where_clauses[] = "id IN ( {$placeholders} )";
			foreach ( $include_ids as $include_id ) {
				$params[] = $include_id;
			}
		}

		if ( ! empty( $exclude_ids ) ) {
			$placeholders    = implode( ',', array_fill( 0, count( $exclude_ids ), '%d' ) );
			$where_clauses[] = "id NOT IN ( {$placeholders} )";
			foreach ( $exclude_ids as $exclude_id ) {
				$params[] = $exclude_id;
			}
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
		// A failed COUNT casts to 0, which is indistinguishable from an empty library and would
		// otherwise be cached as the real total. The page rows come from a separate query, so the
		// response is still served; it just must not persist a total nobody can trust.
		$count_failed = '' !== (string) $wpdb->last_error;

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
		if ( ! $count_failed ) {
			Transient::update( $cache_key, $cache_data );
		}
		$result = new WP_REST_Response( $response, 200 );

		// Add pagination headers.
		$result->header( 'X-WP-Total', $total_items );
		$result->header( 'X-WP-TotalPages', $total_pages );

		return $result;
	}

	/**
	 * Get the album IDs to exclude for a given album: the album itself plus any albums nested in it.
	 *
	 * Album sortorder stores nested albums as strings like "a12" and galleries as plain integers,
	 * so only the "a"-prefixed entries are sub-albums.
	 *
	 * @param int $album_id The album ID.
	 * @return int[] Album IDs (self + nested albums).
	 */
	private static function get_album_child_album_ids( $album_id ) {
		$album_id = (int) $album_id;
		if ( $album_id <= 0 ) {
			return [];
		}

		// Always exclude the album itself (an album cannot be added to itself).
		$album_ids = [ $album_id ];

		$album = AlbumMapper::get_instance()->find( $album_id );
		if ( $album && ! empty( $album->sortorder ) ) {
			$sortorder = is_array( $album->sortorder ) ? $album->sortorder : json_decode( $album->sortorder, true );
			if ( is_array( $sortorder ) ) {
				foreach ( $sortorder as $entry ) {
					if ( is_string( $entry ) && isset( $entry[0] ) && 'a' === $entry[0] && ctype_digit( substr( $entry, 1 ) ) ) {
						$album_ids[] = (int) substr( $entry, 1 );
					}
				}
			}
		}

		return array_values( array_unique( array_filter( $album_ids ) ) );
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
		if ( $request->has_param( 'pageid' ) ) {
			$album->pageid = $request->get_param( 'pageid' );
		}

		// Handle sortorder field
		if ( $request->has_param( 'sortorder' ) ) {
			$album->sortorder = $request->get_param( 'sortorder' );
		}

		// Preview image: only touch it when the caller actually picked a different image. Re-sending the
		// stored previewpic unchanged (the admin app round-trips the whole album on save) must never fail,
		// even if that image was later deleted. When a genuinely new value doesn't resolve, self-heal to a
		// valid first-gallery thumbnail instead of rejecting the whole update. See GH issue #875.
		// Placed after sortorder so the fallback uses the incoming galleries, not the stale ones.
		if ( $request->has_param( 'preview_image_id' ) ) {
			$new_pid = (int) $request->get_param( 'preview_image_id' );
			if ( $new_pid !== (int) $album->previewpic ) {
				$album->previewpic = self::image_exists( $new_pid )
					? $new_pid
					: self::get_first_gallery_thumbnail( $album->sortorder );
			}
		}

		// Handle display type fields
		if ( $request->has_param( 'display_type' ) ) {
			$album->display_type = $request->get_param( 'display_type' );
		}
		if ( $request->has_param( 'display_type_settings' ) ) {
			$incoming = (array) $request->get_param( 'display_type_settings' );
			$edited   = $request->has_param( 'display_type' )
				? $request->get_param( 'display_type' )
				: $album->display_type;
			$stored   = is_array( $album->display_type_settings ) ? $album->display_type_settings : [];

			// Persist only the display type the user edited; the payload may carry every type, so key
			// off display_type. The fallback merges onto stored settings so other customized types survive.
			if ( '' !== (string) $edited && array_key_exists( $edited, $incoming ) ) {
				$stored[ $edited ]            = $incoming[ $edited ];
				$album->display_type_settings = $stored;
			} else {
				$album->display_type_settings = array_merge( $stored, $incoming );
			}
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
			'displayType' => $album->display_type ? $album->display_type : 'photocrati-nextgen_basic_compact_album',
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
			'display_type'          => $album->display_type ? $album->display_type : 'photocrati-nextgen_basic_compact_album',
			// Fill uncustomized display types with current global values for the admin UI (not persisted).
			'display_type_settings' => AlbumMapper::get_instance()->with_display_type_defaults( $album->display_type_settings ?? [] ),
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
