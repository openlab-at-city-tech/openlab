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
					'path'    => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
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
					'path'                  => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'author'                => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
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
					'slug'                  => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					],
					'extras_post_id'        => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
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
				if ( get_current_user_id() !== $gallery->author && ! Security::is_allowed( 'NextGEN Manage others gallery' ) ) {
					return false;
				}
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

		// Get and validate order parameters.
		$orderby = $request->get_param( 'orderby' ) ?? 'gid';
		$order   = strtoupper( $request->get_param( 'order' ) ?? 'ASC' );

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

		$cache_params  = [
			get_current_user_id(),
			$orderby,
			$order,
			$per_page_param,
			$page,
			$request->get_param( 'ecommerce_filter' ),
			$request->get_param( 'is_private_filter' ),
			$request->get_param( 'search' ),
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

		// Fetch current page of items.
		$query->order_by( $orderby, $order )
			->limit( $per_page, $offset );

		$galleries = $query->run_query();

		$response = [];
		foreach ( $galleries as $gallery ) {
			$response[] = self::prepare_gallery_list_item_for_response( $gallery );
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
		$conditions    = [];
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

		if ( $request->has_param( 'search' ) ) {
			$search_term          = $request->get_param( 'search' );
			$search_term_wildcard = '%' . $search_term . '%';

			$conditions[]    = [ 'title LIKE %s', $search_term_wildcard ];
			$where_clauses[] = 'title LIKE %s';
			$params[]        = $search_term_wildcard;
		}

		return [
			'conditions'    => $conditions,
			'where_clauses' => $where_clauses,
			'params'        => $params,
		];
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

		// Check if user can view this gallery.
		// Users can only view galleries they own unless they have "nextgen_edit_gallery_unowned" capability.
		$current_user_id = get_current_user_id();
		$can_manage      = ( (int) $current_user_id === (int) $gallery->author ) || Security::is_allowed( 'nextgen_edit_gallery_unowned' );

		if ( ! $can_manage ) {
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

		$mapper          = GalleryMapper::get_instance();
		$current_user_id = get_current_user_id();
		$can_edit_all    = Security::is_allowed( 'nextgen_edit_gallery_unowned' );

		$galleries = [];
		foreach ( $ids as $id ) {
			$gallery = $mapper->find( $id );

			if ( ! $gallery ) {
				// Skip galleries that don't exist instead of failing entire request
				continue;
			}

			// Check permissions for each gallery
			$can_manage = ( (int) $current_user_id === (int) $gallery->author ) || $can_edit_all;

			if ( ! $can_manage ) {
				// Skip galleries user doesn't have permission to view
				continue;
			}

			$galleries[] = self::prepare_gallery_for_response( $gallery );
		}

		return new WP_REST_Response( $galleries, 200 );
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

		$gallery->name    = sanitize_title( $request->get_param( 'title' ) );
		$gallery->title   = $request->get_param( 'title' );
		$gallery->path    = $request->get_param( 'path' );
		$gallery->author  = get_current_user_id();
		$gallery->galdesc = $request->get_param( 'galdesc' );

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

		if ( $request->has_param( 'name' ) ) {
			$gallery->name = sanitize_title( $request->get_param( 'name' ) );
		}
		if ( $request->has_param( 'title' ) ) {
			$gallery->title = $request->get_param( 'title' );
		}
		if ( $request->has_param( 'path' ) ) {
			$gallery->path = $request->get_param( 'path' );
		}
		if ( $request->has_param( 'author' ) ) {
			$gallery->author = $request->get_param( 'author' );
		}
		if ( $request->has_param( 'previewpic' ) ) {
			$gallery->previewpic = $request->get_param( 'previewpic' );
		}
		if ( $request->has_param( 'pageid' ) ) {
			$gallery->pageid = $request->get_param( 'pageid' );
		}
		if ( $request->has_param( 'galdesc' ) ) {
			$gallery->galdesc = $request->get_param( 'galdesc' );
		}
		if ( $request->has_param( 'slug' ) ) {
			$gallery->slug = $request->get_param( 'slug' );
		}
		if ( $request->has_param( 'extras_post_id' ) ) {
			$gallery->extras_post_id = $request->get_param( 'extras_post_id' );
		}
		if ( $request->has_param( 'parent_id' ) ) {
			$gallery->parent_id = $request->get_param( 'parent_id' );
		}
		if ( $request->has_param( 'pricelist_id' ) ) {
			$gallery->pricelist_id = $request->get_param( 'pricelist_id' );

			// Also update the WordPress post meta for ecommerce requirements check
			if ( $gallery->extras_post_id ) {
				update_post_meta( $gallery->extras_post_id, 'pricelist_id', $request->get_param( 'pricelist_id' ) );
			}
		}
		if ( $request->has_param( 'display_type' ) ) {
			$gallery->display_type = $request->get_param( 'display_type' );
		}
		if ( $request->has_param( 'display_type_settings' ) ) {
			$gallery->display_type_settings = $request->get_param( 'display_type_settings' );
		}
		if ( $request->has_param( 'external_source' ) ) {
			$gallery->external_source = $request->get_param( 'external_source' );
		}
		if ( $request->has_param( 'is_private' ) ) {
			$gallery->is_private = (bool) $request->get_param( 'is_private' );
		}
		if ( $request->has_param( 'is_ecommerce_enabled' ) ) {
			$gallery->is_ecommerce_enabled = $request->get_param( 'is_ecommerce_enabled' );
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
	private static function prepare_gallery_list_item_for_response( $gallery ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$gallery->counter = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE galleryid = %d",
				$gallery->{$gallery->id_field}
			)
		);

		if ( $gallery->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $gallery->previewpic, 'thumb' );
		}

		// Check if current user can manage this gallery
		// User can manage if they own it OR have "NextGEN Manage others gallery" capability
		$current_user_id = get_current_user_id();
		$can_manage      = ( (int) $current_user_id === (int) $gallery->author ) || Security::is_allowed( 'nextgen_edit_gallery_unowned' );

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
	private static function prepare_gallery_for_response( $gallery ) {
		global $wpdb;

		if ( $gallery->previewpic ) {
			$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
			$thumbnail = $storage->get_image_url( $gallery->previewpic, 'thumb' );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$gallery->counter = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->nggpictures} WHERE galleryid = %d",
				$gallery->{$gallery->id_field}
			)
		);

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
			'display_type_settings' => $gallery->display_type_settings ?? [],
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

		$sanitized = [];
		foreach ( $settings as $key => $value ) {
			$key = sanitize_text_field( $key );

			switch ( gettype( $value ) ) {
				case 'boolean':
					$sanitized[ $key ] = (bool) $value;
					break;
				case 'integer':
					$sanitized[ $key ] = (int) $value;
					break;
				case 'double':
					$sanitized[ $key ] = (float) $value;
					break;
				case 'string':
					$sanitized[ $key ] = wp_kses_post( $value );
					break;
				case 'array':
					$sanitized[ $key ] = array_map( 'sanitize_text_field', $value );
					break;
				default:
					$sanitized[ $key ] = null;
			}
		}

		return $sanitized;
	}
}
