<?php
/**
 * Gallery REST API
 *
 * @package NextGEN Gallery
 * @subpackage REST API
 * @since 2.0.0
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;

use Imagely\NGG\DataTypes\Gallery;
use Imagely\NGG\Util\MassAssignment;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\Transient;

/**
 * Gallery REST API
 */
class GalleryREST {
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
	 * Register the REST API routes
	 */
	public static function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/galleries',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_galleries' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'orderby'           => [
						'type'              => 'string',
						'enum'              => [
							'gid',
							'title',
							'author',
							'is_ecommerce_enabled',
							'is_private',
							'date_created',
							'date_modified',
						],
						'default'           => 'gid',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'order'             => [
						'type'              => 'string',
						'enum'              => [ 'ASC', 'DESC' ],
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'per_page'          => [
						'type'              => 'integer',
						'default'           => 25,
						'sanitize_callback' => [ self::class, 'sanitize_per_page' ],
					],
					'page'              => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint', // Keep absint for page (always positive)
					],
					'ecommerce_filter'  => [
						'type'              => 'string',
						'enum'              => [ 'enabled', 'disabled' ],
						'sanitize_callback' => 'sanitize_text_field',
					],
					'is_private_filter' => [
						'type'              => 'integer',
						'enum'              => [ 0, 1 ],
						'sanitize_callback' => 'absint',
					],
					'search'            => [
						'type'              => 'string',
						'description'       => 'Search galleries by title',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'exclude_ids'       => [
						'type'              => 'array',
						'description'       => 'Gallery IDs to exclude from results (e.g. galleries already in an album).',
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
					'exclude_album_id'  => [
						'type'              => 'integer',
						'description'       => 'Exclude the galleries already contained in this album. Bounded alternative to exclude_ids for large albums (the server derives the IDs from the album\'s sortorder).',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Get a single gallery.
		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_gallery' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Get multiple galleries by IDs (batch endpoint for performance).
		register_rest_route(
			'imagely/v1',
			'/galleries/batch',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'get_galleries_batch' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'ids' => [
						'required'          => true,
						'type'              => 'array',
						'items'             => [
							'type' => 'integer',
						],
						'sanitize_callback' => function ( $value ) {
							if ( ! is_array( $value ) ) {
								return [];
							}
							return array_map( 'absint', array_filter( $value ) );
						},
					],
				],
			]
		);

		// Create a new gallery.
		register_rest_route(
			'imagely/v1',
			'/galleries',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'create_gallery' ],
				'permission_callback' => [ self::class, 'check_create_permission' ],
				'args'                => [
					'title'   => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'galdesc' => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post', // TODO get correct sanitize callback.
					],
				],
			]
		);

		// Update a gallery.
		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_gallery' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'id'                    => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'name'                  => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'title'                 => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'previewpic'            => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'pageid'                => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'galdesc'               => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
					],
					'parent_id'             => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'pricelist_id'          => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'display_type'          => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'display_type_settings' => [
						'type'              => 'object',
						'sanitize_callback' => [ self::class, 'sanitize_display_type_settings' ],
					],
					'external_source'       => [
						'type'              => 'object',
						'sanitize_callback' => [ self::class, 'sanitize_external_source' ],
					],
					'is_private'            => [
						'type'              => 'integer',
						'enum'              => [ 0, 1 ],
						'sanitize_callback' => 'absint',
					],
					'is_ecommerce_enabled'  => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Delete a gallery.
		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ self::class, 'delete_gallery' ],
				'permission_callback' => [ self::class, 'check_delete_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Scan folder for new images.
		register_rest_route(
			'imagely/v1',
			'/galleries/(?P<id>\d+)/scan-folder',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'scan_folder' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);
	}

	/**
	 * Check if user has permission to read galleries
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Check if user has permission to create galleries
	 *
	 * @return bool
	 */
	public static function check_create_permission() {
		return Security::is_allowed( 'NextGEN Upload images' );
	}

	/**
	 * Check if user has permission to edit galleries
	 *
	 * @param WP_REST_Request $request Optional. The REST request object.
	 * @return bool
	 */
	public static function check_edit_permission( $request = null ) {
		if ( ! Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			return false;
		}

		// If editing a specific gallery, check if user can manage it.
		if ( $request && $request->get_param( 'id' ) ) {
			$gallery = GalleryMapper::get_instance()->find( $request->get_param( 'id' ) );
			if ( $gallery ) {
				return Security::can_manage_owned( $gallery->author, 'nextgen_edit_gallery' );
			}
		}

		return true;
	}

	/**
	 * Check if user has permission to delete galleries
	 *
	 * @param WP_REST_Request $request Optional.
	 *
	 * @return bool
	 */
	public static function check_delete_permission( $request = null ) {
		return self::check_edit_permission( $request );
	}

	/**
	 * Get all galleries
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response
	 */
	public static function get_galleries( WP_REST_Request $request ) {
		global $wpdb;
		$mapper = GalleryMapper::get_instance();

		// When exclude_album_id is supplied, derive the galleries already in that album server-side and
		// fold them into exclude_ids. This keeps the request URL bounded for large albums (one integer
		// instead of one exclude_ids[] entry per gallery, which can overflow web-server request-line limits).
		$exclude_album_id = (int) $request->get_param( 'exclude_album_id' );
		if ( $exclude_album_id > 0 ) {
			$existing_excludes = (array) $request->get_param( 'exclude_ids' );
			$request->set_param(
				'exclude_ids',
				array_values( array_unique( array_merge( $existing_excludes, self::get_album_gallery_ids( $exclude_album_id ) ) ) )
			);
		}

		// Get and validate order parameters.
		// Security fix (SQLi): whitelist orderby against the same enum declared in register_rest_route() args for this endpoint; unknown input falls back to 'gid' to block ORDER BY injection even if the REST enum check is bypassed.
		$allowed_orderby   = [ 'gid', 'title', 'author', 'is_ecommerce_enabled', 'is_private', 'date_created', 'date_modified' ];
		$requested_orderby = sanitize_key( (string) ( $request->get_param( 'orderby' ) ?? 'gid' ) );
		$orderby           = in_array( $requested_orderby, $allowed_orderby, true ) ? $requested_orderby : 'gid';
		// Security fix (SQLi): restrict order direction to ASC/DESC; anything else defaults to ASC.
		$order = strtoupper( (string) ( $request->get_param( 'order' ) ?? 'ASC' ) );
		$order = in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'ASC';

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

		// Build filter conditions from request.
		$filters = self::build_filter_conditions( $request );

		// Author scope: callers without 'nextgen_edit_gallery_unowned' see only their own galleries (matches get_gallery / get_galleries_batch).
		$current_user_id   = get_current_user_id();
		$author_scope_user = Security::is_allowed( 'nextgen_edit_gallery_unowned' ) ? 0 : (int) $current_user_id;
		if ( $author_scope_user > 0 ) {
			$filters['conditions'][]    = [ 'author = %d', $author_scope_user ];
			$filters['where_clauses'][] = 'author = %d';
			$filters['params'][]        = $author_scope_user;
		}

		$cache_params  = [
			$current_user_id,
			$author_scope_user, // Distinct cache slot per scope to avoid leaking unowned-cap responses to non-cap callers.
			$orderby,
			$order,
			$per_page_param,
			$page,
			$request->get_param( 'ecommerce_filter' ),
			$request->get_param( 'is_private_filter' ),
			$request->get_param( 'search' ),
			$request->get_param( 'exclude_ids' ),
		];
		$cache_key     = Transient::create_key( 'rest_galleries', $cache_params );
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

		foreach ( $filters['conditions'] as $condition ) {
			$query->where( $condition );
		}

		// OR group (match the search term against the title or the ID). The driver parenthesises
		// each where_clauses entry before AND-joining them, so precedence is preserved.
		if ( ! empty( $filters['or_conditions'] ) ) {
			$or_clauses = [];
			foreach ( $filters['or_conditions'] as $or_condition ) {
				$clause       = array_shift( $or_condition );
				$or_clauses[] = $query->_parse_where_clause( $clause, $or_condition );
			}
			$query->add_where_clause( $or_clauses, 'OR' );
		}

		// Calculate total items for pagination using the same filters.
		$table_name = $wpdb->nggallery;
		$sql        = "SELECT COUNT(*) FROM {$table_name}";

		if ( ! empty( $filters['where_clauses'] ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $filters['where_clauses'] );
		}

		if ( ! empty( $filters['params'] ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$sql = $wpdb->prepare( $sql, $filters['params'] );
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

		$galleries = $query->run_query();

		// Batch the per-gallery image counts into a single grouped query to avoid an N+1
		// (previously one COUNT(*) per gallery inside prepare_gallery_list_item_for_response — the
		// hot spot behind the large-install timeouts in #726 / #792).
		$page_gallery_ids = [];
		foreach ( $galleries as $gallery ) {
			$page_gallery_ids[] = (int) $gallery->{$gallery->id_field};
		}
		$image_counts = self::get_image_counts_for_galleries( $page_gallery_ids );

		$response = [];
		foreach ( $galleries as $gallery ) {
			$gallery_id = (int) $gallery->{$gallery->id_field};
			$response[] = self::prepare_gallery_list_item_for_response( $gallery, $image_counts[ $gallery_id ] ?? 0 );
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
	 * Build filter conditions from request parameters.
	 *
	 * Extracts filter logic to ensure consistency between query builder
	 * and count query.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return array {
	 *     Filter conditions in multiple formats.
	 *
	 *     @type array $conditions    Array of conditions for query builder.
	 *                                Each element is [ 'clause', value ].
	 *     @type array $where_clauses Array of WHERE clause strings for COUNT query.
	 *     @type array $params        Array of parameters for COUNT query.
	 * }
	 */
	private static function build_filter_conditions( WP_REST_Request $request ) {
		global $wpdb;

		$conditions    = [];
		$or_conditions = [];
		$where_clauses = [];
		$params        = [];

		if ( $request->has_param( 'ecommerce_filter' ) ) {
			$ecommerce_filter = $request->get_param( 'ecommerce_filter' );
			$is_enabled       = 'enabled' === $ecommerce_filter ? 1 : 0;

			$conditions[]    = [ 'is_ecommerce_enabled = %d', $is_enabled ];
			$where_clauses[] = 'is_ecommerce_enabled = %d';
			$params[]        = $is_enabled;
		}

		if ( $request->has_param( 'is_private_filter' ) ) {
			$is_private = (int) $request->get_param( 'is_private_filter' );

			$conditions[]    = [ 'is_private = %d', $is_private ];
			$where_clauses[] = 'is_private = %d';
			$params[]        = $is_private;
		}

		// Skipped when empty: "title LIKE '%%'" would drop every row with a NULL title. esc_like()
		// keeps a typed % or _ literal.
		$search_term = trim( (string) $request->get_param( 'search' ) );
		if ( '' !== $search_term ) {
			$search_term_wildcard = '%' . $wpdb->esc_like( $search_term ) . '%';

			if ( ctype_digit( $search_term ) ) {
				// A digit-only term matches the ID as well as the title. Galleries saved without
				// a title are listed by their ID, so a title match alone can never reach them.
				$or_conditions[] = [ 'title LIKE %s', $search_term_wildcard ];
				$or_conditions[] = [ 'gid = %d', (int) $search_term ];

				$where_clauses[] = '( title LIKE %s OR gid = %d )';
				$params[]        = $search_term_wildcard;
				$params[]        = (int) $search_term;
			} else {
				$conditions[]    = [ 'title LIKE %s', $search_term_wildcard ];
				$where_clauses[] = 'title LIKE %s';
				$params[]        = $search_term_wildcard;
			}
		}

		// Exclude specific gallery IDs (e.g. galleries already in an album) server-side so
		// pagination and the per_page budget are only consumed by genuinely available galleries.
		$exclude_ids = $request->get_param( 'exclude_ids' );
		if ( is_array( $exclude_ids ) ) {
			$exclude_ids = array_values( array_unique( array_filter( array_map( 'absint', $exclude_ids ) ) ) );
			if ( ! empty( $exclude_ids ) ) {
				$placeholders = implode( ',', array_fill( 0, count( $exclude_ids ), '%d' ) );

				// Query builder understands the "NOT IN %s" array-bind form.
				$conditions[] = [ 'gid NOT IN %s', $exclude_ids ];
				// COUNT query uses individual %d placeholders bound to the same IDs.
				$where_clauses[] = "gid NOT IN ( {$placeholders} )";
				foreach ( $exclude_ids as $exclude_id ) {
					$params[] = $exclude_id;
				}
			}
		}

		return [
			'conditions'    => $conditions,
			'or_conditions' => $or_conditions,
			'where_clauses' => $where_clauses,
			'params'        => $params,
		];
	}

	/**
	 * Get the gallery IDs contained in an album's sortorder.
	 *
	 * Album sortorder mixes gallery IDs (integers) and nested-album references (strings like "a12");
	 * only the integer entries are galleries.
	 *
	 * @param int $album_id The album ID.
	 * @return int[] Gallery IDs contained in the album.
	 */
	private static function get_album_gallery_ids( $album_id ) {
		$album_id = (int) $album_id;
		if ( $album_id <= 0 ) {
			return [];
		}

		$album = \Imagely\NGG\DataMappers\Album::get_instance()->find( $album_id );
		if ( ! $album || empty( $album->sortorder ) ) {
			return [];
		}

		$sortorder = is_array( $album->sortorder ) ? $album->sortorder : json_decode( $album->sortorder, true );
		if ( ! is_array( $sortorder ) ) {
			return [];
		}

		$gallery_ids = [];
		foreach ( $sortorder as $entry ) {
			// Integer entries are galleries; string entries like "a12" are nested albums, skip those.
			if ( is_int( $entry ) || ( is_string( $entry ) && ctype_digit( $entry ) ) ) {
				$gallery_ids[] = (int) $entry;
			}
		}

		return array_values( array_unique( array_filter( $gallery_ids ) ) );
	}

	/**
	 * Get image counts for multiple galleries in a single grouped query.
	 *
	 * Shared by the list (get_galleries) and batch (get_galleries_batch) endpoints so neither runs a
	 * per-gallery COUNT(*) (the N+1 behind the large-install timeouts in #726 / #792).
	 *
	 * @param int[] $gallery_ids Gallery IDs.
	 * @return array<int,int> Map of gallery ID => image count. Galleries with no images are absent
	 *                        from the map (callers should default to 0).
	 */
	private static function get_image_counts_for_galleries( array $gallery_ids ) {
		global $wpdb;

		$counts        = [];
		$sanitized_ids = array_values( array_unique( array_filter( array_map( 'absint', $gallery_ids ) ) ) );
		if ( empty( $sanitized_ids ) ) {
			return $counts;
		}

		$placeholders = implode( ',', array_fill( 0, count( $sanitized_ids ), '%d' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
		$count_rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- {$placeholders} is a list of %d built from count( $sanitized_ids ), and $sanitized_ids is passed to prepare().
				"SELECT galleryid, COUNT(*) AS total FROM {$wpdb->nggpictures} WHERE galleryid IN ( {$placeholders} ) GROUP BY galleryid",
				$sanitized_ids
			)
		);
		foreach ( (array) $count_rows as $count_row ) {
			$counts[ (int) $count_row->galleryid ] = (int) $count_row->total;
		}

		return $counts;
	}

	/**
	 * Get a single gallery
	 *
	 * @param WP_REST_Request $request Optional. The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_gallery( WP_REST_Request $request ) {
		$id      = $request->get_param( 'id' );
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $id );

		if ( ! $gallery ) {
			return new WP_Error(
				'gallery_not_found',
				// translators: %d is the numeric ID of the gallery.
				sprintf( __( 'Gallery with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		// Read surface, so the required capability is the overview one the route already gates on.
		// That leaves this gate's effective rule unchanged; requiring the edit capability here
		// would stop an overview-only role from reading its own galleries.
		if ( ! Security::can_manage_owned( $gallery->author, 'NextGEN Gallery overview' ) ) {
			return new WP_Error(
				'gallery_forbidden',
				__( 'Sorry, you do not have permission to view this gallery', 'nggallery' ),
				[ 'status' => 403 ]
			);
		}

		return new WP_REST_Response( self::prepare_gallery_for_response( $gallery ), 200 );
	}

	/**
	 * Get multiple galleries by IDs in a single batch request (performance optimization)
	 *
	 * @param WP_REST_Request $request The REST request object containing array of gallery IDs.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_galleries_batch( WP_REST_Request $request ) {
		$ids = $request->get_param( 'ids' );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return new WP_Error(
				'invalid_ids',
				__( 'Invalid gallery IDs provided', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Limit batch size to prevent abuse (max 100 galleries per request)
		if ( count( $ids ) > 100 ) {
			return new WP_Error(
				'batch_too_large',
				__( 'Maximum 100 galleries can be requested at once', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		$mapper = GalleryMapper::get_instance();

		// Batch the per-gallery image counts into a single grouped query. Previously each gallery
		// triggered its own COUNT(*) in prepare_gallery_for_response — an N+1 that made albums with
		// 50+ galleries fire 50+ COUNT queries on every editor load.
		$counts = self::get_image_counts_for_galleries( $ids );

		$galleries = [];
		foreach ( $ids as $id ) {
			$gallery = $mapper->find( $id );

			if ( ! $gallery ) {
				// Skip galleries that don't exist instead of failing entire request
				continue;
			}

			// Same read gate as get_gallery(), so its effective rule is likewise unchanged. The
			// capability lookups read the already-loaded WP_User, so there is no per-row query
			// to hoist out of the loop.
			if ( ! Security::can_manage_owned( $gallery->author, 'NextGEN Gallery overview' ) ) {
				// Skip galleries user doesn't have permission to view
				continue;
			}

			$gallery_id  = (int) $gallery->{$gallery->id_field};
			$galleries[] = self::prepare_gallery_for_response( $gallery, $counts[ $gallery_id ] ?? 0 );
		}

		return new WP_REST_Response( $galleries, 200 );
	}

	/**
	 * Collected on purpose so MassAssignment refuses them rather than this endpoint ignoring
	 * them silently.
	 *
	 * @var string[]
	 */
	const NEVER_EDITABLE_PROPERTIES = [
		'path',
		'author',
		'slug',
		'extras_post_id',
	];

	/**
	 * Properties create_gallery() accepts.
	 *
	 * @var string[]
	 */
	const CREATE_PROPERTIES = [
		'title',
		'galdesc',
	];

	/**
	 * Properties update_gallery() accepts. `display_type_settings` needs the merge below rather
	 * than a straight assignment, and `parent_id` is not a gallery column.
	 *
	 * @var string[]
	 */
	const UPDATE_PROPERTIES = [
		'name',
		'title',
		'galdesc',
		'previewpic',
		'pageid',
		'is_private',
		'display_type',
		'external_source',
		'is_ecommerce_enabled',
		'pricelist_id',
	];

	/**
	 * Collects the gallery properties the request supplied. Read by name, not from the route
	 * schema: an unregistered parameter is still readable from the body.
	 *
	 * @param WP_REST_Request $request  The REST request object.
	 * @param string[]        $accepted Property names this endpoint accepts.
	 * @return array
	 */
	private static function collect_request_properties( WP_REST_Request $request, array $accepted ) {
		$properties = [];

		foreach ( array_merge( $accepted, self::NEVER_EDITABLE_PROPERTIES ) as $key ) {
			if ( $request->has_param( $key ) ) {
				$properties[ $key ] = $request->get_param( $key );
			}
		}

		return $properties;
	}

	/**
	 * Refuses the request and names the properties. Nothing is saved, so the caller is not told
	 * an edit succeeded that never stored the value.
	 *
	 * @param array $refused Refused property keys.
	 * @return WP_Error
	 */
	private static function refused_property_error( array $refused ) {
		return new WP_Error(
			'rest_gallery_property_not_editable',
			sprintf(
				// translators: %s is a comma-separated list of property names.
				__( 'These gallery properties may not be set through this endpoint and nothing was saved: %s', 'nggallery' ),
				MassAssignment::describe_refused( $refused )
			),
			[
				'status'  => 403,
				'refused' => $refused,
			]
		);
	}

	/**
	 * Create a new gallery
	 *
	 * @param WP_REST_Request $request Optional. The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function create_gallery( WP_REST_Request $request ) {
		$mapper  = GalleryMapper::get_instance();
		$gallery = new Gallery();

		$assignable = MassAssignment::filter(
			self::collect_request_properties( $request, self::CREATE_PROPERTIES ),
			'gallery',
			$refused
		);

		if ( $refused ) {
			return self::refused_property_error( $refused );
		}

		foreach ( $assignable as $key => $value ) {
			$gallery->$key = $value;
		}

		// Server-derived, never taken from the request. The mapper builds the folder from these.
		$gallery->name   = sanitize_title( $request->get_param( 'title' ) );
		$gallery->author = get_current_user_id();

		try {
			$mapper->save( $gallery );
			Transient::flush( 'rest_galleries' );
			return new WP_REST_Response(
				self::prepare_gallery_for_response( $gallery ),
				201
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'create_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Update a gallery
	 *
	 * @param WP_REST_Request $request Optional. The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function update_gallery( WP_REST_Request $request ) {
		$id      = $request->get_param( 'id' );
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $id );

		if ( ! $gallery ) {
			return new WP_Error(
				'gallery_not_found',
				// translators: %d is the numeric ID of the gallery.
				sprintf( __( 'Gallery with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		$assignable = MassAssignment::filter(
			self::collect_request_properties( $request, self::UPDATE_PROPERTIES ),
			'gallery',
			$refused
		);

		if ( $refused ) {
			return self::refused_property_error( $refused );
		}

		if ( isset( $assignable['name'] ) ) {
			$assignable['name'] = sanitize_title( $assignable['name'] );
		}

		foreach ( $assignable as $key => $value ) {
			$gallery->$key = $value;
		}

		if ( $request->has_param( 'parent_id' ) ) {
			$gallery->parent_id = $request->get_param( 'parent_id' );
		}
		if ( isset( $assignable['pricelist_id'] ) ) {
			// Also update the WordPress post meta for ecommerce requirements check
			if ( $gallery->extras_post_id ) {
				update_post_meta( $gallery->extras_post_id, 'pricelist_id', $assignable['pricelist_id'] );
			}
		}
		if ( $request->has_param( 'display_type_settings' ) ) {
			$incoming = (array) $request->get_param( 'display_type_settings' );
			$edited   = $request->has_param( 'display_type' )
				? $request->get_param( 'display_type' )
				: $gallery->display_type;
			$stored   = is_array( $gallery->display_type_settings ) ? $gallery->display_type_settings : [];

			// Persist only the display type the user edited; the payload may carry every type, so key
			// off display_type. The fallback merges onto stored settings so other customized types survive.
			if ( '' !== (string) $edited && array_key_exists( $edited, $incoming ) ) {
				$stored[ $edited ]              = $incoming[ $edited ];
				$gallery->display_type_settings = $stored;
			} else {
				$gallery->display_type_settings = array_merge( $stored, $incoming );
			}
		}
		try {
			$mapper->save( $gallery );
			Transient::flush( 'rest_galleries' );
			return new WP_REST_Response(
				[
					'gallery' => self::prepare_gallery_for_response( $gallery ),
					'message' => __( 'Gallery updated successfully', 'nggallery' ),
				],
				200
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'update_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Delete a gallery
	 *
	 * @param WP_REST_Request $request Optional. The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function delete_gallery( WP_REST_Request $request ) {
		$id      = $request->get_param( 'id' );
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $id );

		if ( ! $gallery ) {
			return new WP_Error(
				'gallery_not_found',
				// translators: %d is the numeric ID of the gallery.
				sprintf( __( 'Gallery with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		try {
			$mapper->destroy( $gallery, true );
			Transient::flush( 'rest_galleries' );
			return new WP_REST_Response(
				[
					'message' => __( 'Gallery deleted successfully', 'nggallery' ),
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
	 * Scan gallery folder for new images that were added to the filesystem.
	 *
	 * This imports any images that exist in the gallery's folder but are not
	 * yet in the database (e.g., images added via FTP).
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function scan_folder( WP_REST_Request $request ) {
		global $wpdb;

		$id      = $request->get_param( 'id' );
		$mapper  = GalleryMapper::get_instance();
		$gallery = $mapper->find( $id );

		if ( ! $gallery ) {
			return new WP_Error(
				'gallery_not_found',
				// translators: %d is the numeric ID of the gallery.
				sprintf( __( 'Gallery with ID %d not found', 'nggallery' ), $id ),
				[ 'status' => 404 ]
			);
		}

		// Get the gallery path from the storage manager.
		$storage      = \Imagely\NGG\DataStorage\Manager::get_instance();
		$gallery_path = $storage->get_gallery_abspath( $id );

		if ( ! is_dir( $gallery_path ) ) {
			return new WP_Error(
				'folder_not_found',
				// translators: %s is the gallery folder path.
				sprintf( __( 'Gallery folder not found: %s', 'nggallery' ), $gallery_path ),
				[ 'status' => 404 ]
			);
		}

		// Scan folder for image files.
		$new_images_list = \nggAdmin::scandir( $gallery_path );

		if ( empty( $new_images_list ) ) {
			return new WP_REST_Response(
				[
					'success'        => true,
					'message'        => __( 'No images found in the gallery folder.', 'nggallery' ),
					'images_added'   => 0,
					'images_skipped' => 0,
				],
				200
			);
		}

		// Get existing images in the database for this gallery.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$old_images_list = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `filename` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d",
				$id
			)
		);

		if ( null === $old_images_list ) {
			$old_images_list = [];
		}

		// Find new images (exist in folder but not in database).
		$new_images = array_diff( $new_images_list, $old_images_list );

		if ( empty( $new_images ) ) {
			return new WP_REST_Response(
				[
					'success'        => true,
					'message'        => __( 'No new images found. All images in the folder are already in the gallery.', 'nggallery' ),
					'images_added'   => 0,
					'images_skipped' => count( $old_images_list ),
				],
				200
			);
		}

		// Import the new images.
		$image_mapper = \Imagely\NGG\DataMappers\Image::get_instance();
		$added_count  = 0;
		$errors       = [];

		foreach ( $new_images as $filename ) {
			// Apply filter for renaming/modifying image before import.
			$filename = apply_filters( 'ngg_pre_add_new_image', $filename, $id );

			// Verify the file exists and is readable.
			$filepath = trailingslashit( $gallery_path ) . $filename;
			if ( ! file_exists( $filepath ) || ! is_readable( $filepath ) ) {
				$errors[] = sprintf(
					// translators: %s is the filename.
					__( 'File not readable: %s', 'nggallery' ),
					$filename
				);
				continue;
			}

			// Re-check existence right before insert: the $old_images_list snapshot above can be
			// stale by the time this loop runs (e.g. a concurrent scan request importing the same
			// file), so array_diff() alone isn't a reliable guard against duplicate imports.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$already_imported = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `pid` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d AND `filename` = %s LIMIT 1",
					$id,
					$filename
				)
			);
			if ( $already_imported ) {
				continue;
			}

			try {
				// Create a new image entity.
				$image             = new \Imagely\NGG\DataTypes\Image();
				$image->filename   = $filename;
				$image->galleryid  = $id;
				$image->alttext    = \Imagely\NGG\Display\I18N::mb_basename( $filename );
				$image->image_slug = sanitize_title( $image->alttext );
				$image->exclude    = 0;

				// Save the image to the database.
				$result = $image_mapper->save( $image );

				if ( $result ) {
					// Generate thumbnail for the newly imported image.
					\nggAdmin::create_thumbnail( $image );

					// Import metadata from the image file.
					\nggAdmin::import_MetaData( $image->pid );

					++$added_count;
				} else {
					$errors[] = sprintf(
						// translators: %s is the filename.
						__( 'Failed to save image: %s', 'nggallery' ),
						$filename
					);
				}
			} catch ( \Exception $e ) {
				$errors[] = sprintf(
					// translators: %1$s is the filename, %2$s is the error message.
					__( 'Error importing %1$s: %2$s', 'nggallery' ),
					$filename,
					$e->getMessage()
				);
			}
		}

		// Set gallery preview image if none is set and we added images.
		if ( $added_count > 0 && empty( $gallery->previewpic ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$first_image = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `pid` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d ORDER BY `sortorder` ASC LIMIT 1",
					$id
				)
			);
			if ( $first_image ) {
				$gallery->previewpic = (int) $first_image;
				$mapper->save( $gallery );
			}
		}

		// Build response message.
		if ( $added_count > 0 ) {
			$message = sprintf(
				// translators: %d is the number of images imported.
				_n(
					'Successfully imported %d new image.',
					'Successfully imported %d new images.',
					$added_count,
					'nggallery'
				),
				$added_count
			);
		} else {
			$message = __( 'No new images were imported.', 'nggallery' );
		}

		return new WP_REST_Response(
			[
				'success'        => true,
				'message'        => $message,
				'images_added'   => $added_count,
				'images_skipped' => count( $old_images_list ),
				'errors'         => $errors,
			],
			200
		);
	}

	/**
	 * Prepare gallery list item for API response.
	 *
	 * @param Gallery $gallery The gallery object.
	 *
	 * @return array {
	 *     Gallery data.
	 *
	 *     @type int    $id                  Gallery ID.
	 *     @type string $galleryTitle        Gallery title.
	 *     @type string $shortcode           Gallery shortcode.
	 *     @type int    $count               Number of images.
	 *     @type bool   $eCommerce           Whether eCommerce is enabled.
	 *     @type bool   $is_private          Whether the gallery is private.
	 *     @type string $thumbnail           Preview image URL.
	 *     @type string $created             Creation date in GMT.
	 *     @type string $modified            Last modified date in GMT.
	 *     @type string $displayType         Gallery display type.
	 * }
	 */
	private static function prepare_gallery_list_item_for_response( $gallery, $precomputed_count = null ) {
		global $wpdb;

		if ( null !== $precomputed_count ) {
			// Caller supplied the image count (get_galleries batches the counts into a single grouped
			// query to avoid an N+1 of COUNT(*) queries). Cast to string to match the wpdb::get_var()
			// path below and the frontend `count: string` contract.
			$gallery->counter = (string) (int) $precomputed_count;
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$gallery->counter = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE galleryid = %d",
					$gallery->{$gallery->id_field}
				)
			);
		}

		if ( $gallery->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $gallery->previewpic, 'thumb' );
		}

		// Mirrors check_edit_permission() so the flag cannot promise the adminApp an edit the
		// write routes will refuse once the owner's gallery-management capability is revoked.
		$can_manage = Security::can_manage_owned( $gallery->author, 'nextgen_edit_gallery' );

		return [
			'id'           => $gallery->gid,
			'galleryTitle' => $gallery->title,
			'shortcode'    => '[imagely id="' . $gallery->gid . '"]',
			'count'        => $gallery->counter,
			'eCommerce'    => $gallery->is_ecommerce_enabled,
			'is_private'   => (bool) $gallery->is_private,
			'thumbnail'    => $thumbnail ?? '',
			'created'      => $gallery->date_created,
			'modified'     => $gallery->date_modified,
			'displayType'  => $gallery->display_type,
			'canManage'    => $can_manage,
			'author'       => $gallery->author,
		];
	}

	/**
	 * Prepare gallery data for API response
	 *
	 * @param Gallery $gallery The gallery object.
	 * @return array
	 */
	private static function prepare_gallery_for_response( $gallery, $precomputed_count = null ) {
		global $wpdb;

		if ( $gallery->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $gallery->previewpic, 'thumb' );
		}

		if ( null !== $precomputed_count ) {
			// Caller supplied the image count (e.g. get_galleries_batch batches the counts into a
			// single grouped query to avoid an N+1 of COUNT(*) queries when an album loads many galleries).
			// Cast to string to match the wpdb::get_var() path below and the frontend `counter: string` contract.
			$gallery->counter = (string) (int) $precomputed_count;
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$gallery->counter = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE galleryid = %d",
					$gallery->{$gallery->id_field}
				)
			);
		}

		return [
			'gid'                   => $gallery->gid,
			'name'                  => $gallery->name,
			'title'                 => $gallery->title,
			'path'                  => $gallery->path,
			'author'                => $gallery->author,
			'previewpic'            => $gallery->previewpic,
			'pageid'                => $gallery->pageid,
			'galdesc'               => $gallery->galdesc,
			'slug'                  => $gallery->slug,
			'extras_post_id'        => $gallery->extras_post_id,
			'parent_id'             => $gallery->parent_id ?? null,
			'pricelist_id'          => $gallery->pricelist_id ?? null,
			'counter'               => $gallery->counter ?? 0,
			'previewpic_url'        => $thumbnail ?? '',
			'display_type'          => $gallery->display_type ?? 'photocrati-nextgen_basic_thumbnails',
			// Fill uncustomized display types with current global values for the admin UI (not persisted).
			'display_type_settings' => GalleryMapper::get_instance()->with_display_type_defaults( $gallery->display_type_settings ?? [] ),
			'external_source'       => $gallery->external_source ?? [],
			'is_private'            => (bool) ( $gallery->is_private ?? false ),
			'is_ecommerce_enabled'  => $gallery->is_ecommerce_enabled ?? false,
			'date_created'          => $gallery->date_created,
			'date_modified'         => $gallery->date_modified,
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
	 * Sanitize external source settings.
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public static function sanitize_external_source( $settings ) {
		if ( ! is_array( $settings ) ) {
			return [];
		}

		$allowed_types         = [ 'video', 'tiktok', 'instagram', 'dribbble', 'youtube', 'google_photos', 'dropbox' ];
		$allowed_settings_mode = [ 'default', 'custom', 'global' ];

		// Flat keys (cast rule).
		$schema = [
			'type'                     => 'string',
			'settings_mode'            => 'string',
			'show_video_controls'      => 'bool',
			'show_play_pause_controls' => 'bool',
			'autoplay_videos'          => 'bool',
			'google_photos_account'    => 'int',
		];

		// Per-integration prefixed keys — one rule per field, applied to each
		// allowed addon prefix (tiktok/instagram/dribbble).
		$per_type_fields = [
			'_account'          => 'string',
			'_number'           => 'int',
			'_link'             => 'int',
			'_link_target'      => 'int_bool',
			'_image_size'       => 'string',
			'_caption'          => 'int_bool',
			'_caption_length'   => 'int',
			'_cache'            => 'int_bool',
			'_show_play_button' => 'int_bool',
		];
		foreach ( [ 'tiktok', 'instagram', 'dribbble' ] as $prefix ) {
			foreach ( $per_type_fields as $suffix => $cast ) {
				$schema[ $prefix . $suffix ] = $cast;
			}
		}

		$filtered  = array_intersect_key( $settings, $schema );
		$sanitized = [];

		foreach ( $schema as $key => $cast ) {
			if ( ! array_key_exists( $key, $filtered ) ) {
				continue;
			}
			$value = $filtered[ $key ];

			switch ( $cast ) {
				case 'bool':
					$sanitized[ $key ] = (bool) $value;
					break;
				case 'int':
					$sanitized[ $key ] = (int) $value;
					break;
				case 'int_bool':
					$sanitized[ $key ] = ( (int) $value ) ? 1 : 0;
					break;
				case 'url':
					$sanitized[ $key ] = esc_url_raw( (string) $value );
					break;
				case 'string':
				default:
					$clean = sanitize_text_field( (string) $value );
					if ( 'type' === $key && '' !== $clean && ! in_array( $clean, $allowed_types, true ) ) {
						break;
					}
					if ( 'settings_mode' === $key && ! in_array( $clean, $allowed_settings_mode, true ) ) {
						break;
					}
					$sanitized[ $key ] = $clean;
					break;
			}
		}

		return $sanitized;
	}
}
