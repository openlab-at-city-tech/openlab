<?php
/**
 * REST API endpoints for NextGEN Gallery images.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataTypes\Image;
use Imagely\NGG\Settings\GlobalSettings;
use Imagely\NGG\Util\Filesystem;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\Transient;

/**
 * Class ImageREST
 * Handles REST API endpoints for NextGEN Gallery images.
 *
 * @package Imagely\NGG\REST\DataMappers
 */
class ImageREST {
	/**
	 * Register the REST API routes for images
	 */
	public static function register_routes() {
		register_rest_route(
			'imagely/v1',
			'/images',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_images' ],
				'permission_callback' => [ self::class, 'check_read_permission' ],
				'args'                => [
					'gallery_id' => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'per_page'   => [
						'type'              => 'integer',
						'default'           => 100,
						'sanitize_callback' => function ( $value ) {
							// Allow -1 for "all images", otherwise ensure minimum of 1
							$int_value = (int) $value;
							if ( $int_value < 0 ) {
								return -1; // Normalize to -1 for "all"
							}
							return max( 1, $int_value );
						},
					],
					'page'       => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'orderby'    => [
						'type'              => 'string',
						'enum'              => [ 'sortorder', 'pid', 'filename', 'imagedate' ],
						'default'           => 'sortorder',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'order'      => [
						'type'              => 'string',
						'enum'              => [ 'ASC', 'DESC' ],
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Get a single image.
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_image' ],
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

		// Update an image.
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'update_image' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id'           => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'alttext'      => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'description'  => [
						'type'              => 'string',
						'sanitize_callback' => 'wp_kses_post',
					],
					'exclude'      => [
						'type' => 'boolean',
					],
					'image_slug'   => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					],
					'sortorder'    => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'tags'         => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'pricelist_id' => [
						'type'              => 'integer',
						'sanitize_callback' => function ( $value ) {
							// Allow -1 for "not for sale", 0 for "use gallery pricelist", positive integers for actual pricelists
							$int_value = (int) $value;
							return ( $int_value === -1 || $int_value >= 0 ) ? $int_value : 0;
						},
						'description'       => 'Pricelist ID for ecommerce functionality',
					],
				],
			]
		);

		// Delete an image.
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ self::class, 'delete_image' ],
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

		// Move an image to a new sortorder slot (cross-page or in-page).
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/move',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'move_image' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'id'          => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'gallery_id'  => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'after_pid'   => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'before_pid'  => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'target_page' => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'per_page'    => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Bulk update images.
		register_rest_route(
			'imagely/v1',
			'/images/bulk',
			[
				'methods'             => 'PUT',
				'callback'            => [ self::class, 'bulk_update_images' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'images' => [
						'required' => true,
						'type'     => 'array',
						'items'    => [
							'type'       => 'object',
							'properties' => [
								'id'           => [
									'required' => true,
									'type'     => 'integer',
								],
								'alttext'      => [
									'type' => 'string',
								],
								'description'  => [
									'type' => 'string',
								],
								'exclude'      => [
									'type' => 'boolean',
								],
								'image_slug'   => [
									'type' => 'string',
								],
								'sortorder'    => [
									'type' => 'integer',
								],
								'tags'         => [
									'type' => 'string',
								],
								'pricelist_id' => [
									'type'        => 'integer',
									'description' => 'Pricelist ID for ecommerce functionality',
								],
							],
						],
					],
				],
			]
		);

		// Import images from the media library.
		register_rest_route(
			'imagely/v1',
			'/images/import-media-library',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'import_media_library' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'gallery_id'     => [
						'type'              => 'integer',
						'required'          => false,
						'sanitize_callback' => 'absint',
					],
					'gallery_name'   => [
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
					'attachment_ids' => [
						'type'     => 'array',
						'items'    => [ 'type' => 'integer' ],
						'required' => true,
					],
				],
			]
		);

		// Upload an image to a gallery (single image or zip).
		register_rest_route(
			'imagely/v1',
			'/images/upload',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'upload_image' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'gallery_id'   => [
						'type'              => 'integer',
						'required'          => false,
						'sanitize_callback' => 'absint',
					],
					'gallery_name' => [
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Browse a folder for import.
		register_rest_route(
			'imagely/v1',
			'/folders/browse',
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'browse_folder' ],
				'permission_callback' => [ self::class, 'check_folder_import_permission' ],
				'args'                => [
					'dir' => [
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Import a folder as a gallery.
		register_rest_route(
			'imagely/v1',
			'/folders/import',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'import_folder' ],
				'permission_callback' => [ self::class, 'check_folder_import_permission' ],
				'args'                => [
					'folder'        => [
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
					'keep_location' => [
						'type'     => 'boolean',
						'required' => false,
					],
					'gallery_title' => [
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	/**
	 * Check if user has permission to read images
	 *
	 * @return bool
	 */
	public static function check_read_permission() {
		return Security::is_allowed( 'NextGEN Gallery overview' );
	}

	/**
	 * Check if user has permission to edit images
	 *
	 * @param \WP_REST_Request|null $request Optional REST request — when supplied, per-gallery ownership is enforced.
	 * @return bool
	 */
	public static function check_edit_permission( $request = null ) {
		if ( ! Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			return false;
		}

		if ( null === $request ) {
			return true;
		}

		// Upload/import routes: verify the user owns (or can manage) the target gallery.
		// Authorization is gallery-level: ownership of the destination, not the images.
		$gallery_id = (int) $request->get_param( 'gallery_id' );
		if ( $gallery_id > 0 ) {
			return self::current_user_can_manage_gallery( $gallery_id );
		}

		// Bulk endpoints have no single id; per-row ownership is enforced inside the handler.
		return true;
	}

	/**
	 * Check if user has permission to edit a specific image (single PUT).
	 * Resolves image_id from URL params only and verifies gallery ownership.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return bool|\WP_Error
	 */
	public static function check_image_edit_permission( WP_REST_Request $request ) {
		if ( ! Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			return false;
		}

		$url_params = $request->get_url_params();
		$image_id   = isset( $url_params['id'] ) ? (int) $url_params['id'] : 0;
		if ( $image_id <= 0 ) {
			return false;
		}

		return self::current_user_can_manage_image( $image_id );
	}

	/**
	 * Folder browse/import: NextGEN Manage gallery, multisite wpmuImportFolder when applicable,
	 * and legacy nggGallery::current_user_can( 'NextGEN Import image folder' ) (cap may be unregistered → allowed).
	 *
	 * @return bool
	 */
	public static function check_folder_import_permission() {
		if ( ! self::check_edit_permission() ) {
			return false;
		}
		if ( is_multisite() && ! GlobalSettings::get_instance()->get( 'wpmuImportFolder', false ) ) {
			return false;
		}
		if ( class_exists( 'nggGallery', false ) ) {
			return \nggGallery::current_user_can( 'NextGEN Import image folder' );
		}
		// nggGallery should always be loaded; if it isn't, fall back to the
		// management-cap gate already passed above rather than denying on an
		// unregistered capability.
		return true;
	}

	/**
	 * Check if user has permission to delete images
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return bool
	 */
	public static function check_delete_permission( WP_REST_Request $request ) {
		if ( ! Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			return false;
		}

		$url_params = $request->get_url_params();
		$image_id   = isset( $url_params['id'] ) ? (int) $url_params['id'] : 0;
		if ( $image_id <= 0 ) {
			return false;
		}

		return self::current_user_can_manage_image( $image_id );
	}

	/**
	 * Check if the current user can manage the gallery that contains a given image.
	 * Users can manage an image if they own its gallery or have "NextGEN Manage others gallery".
	 *
	 * @param int $image_id The image ID.
	 * @return bool|\WP_Error
	 */
	public static function current_user_can_manage_image( int $image_id ) {
		$image = ImageMapper::get_instance()->find( $image_id );
		if ( ! $image ) {
			return new \WP_Error( 'rest_image_not_found', __( 'Image not found.', 'nggallery' ), [ 'status' => 404 ] );
		}

		$gallery = GalleryMapper::get_instance()->find( $image->galleryid );
		if ( ! $gallery ) {
			// Orphaned image: deny unless the user has explicit cross-gallery capability.
			return Security::is_allowed( 'NextGEN Manage others gallery' );
		}

		return ( (int) get_current_user_id() === (int) $gallery->author )
			|| Security::is_allowed( 'NextGEN Manage others gallery' );
	}

	/**
	 * Check if the current user can manage a specific gallery.
	 * Used to authorize upload/import operations targeting an existing gallery.
	 *
	 * @param int $gallery_id The gallery ID.
	 * @return bool
	 */
	public static function current_user_can_manage_gallery( int $gallery_id ) {
		$gallery = GalleryMapper::get_instance()->find( $gallery_id );
		if ( ! $gallery ) {
			return false;
		}

		return ( (int) get_current_user_id() === (int) $gallery->author )
			|| Security::is_allowed( 'NextGEN Manage others gallery' );
	}

	/**
	 * Resolve an image id to its parent gallery's author and verify the current user may edit it.
	 *
	 * Returns true when the user owns the parent gallery or holds 'NextGEN Manage others gallery'.
	 * Missing image returns true so the handler can emit a 404 instead of a 403.
	 *
	 * @param int $image_id NGG image id (pid).
	 * @return bool
	 */
	protected static function current_user_owns_image( $image_id ) {
		$image = ImageMapper::get_instance()->find( $image_id );
		if ( ! $image ) {
			return true;
		}

		$gallery_id = isset( $image->galleryid ) ? (int) $image->galleryid : 0;
		if ( $gallery_id <= 0 ) {
			return Security::is_allowed( 'NextGEN Manage others gallery' );
		}

		$gallery = GalleryMapper::get_instance()->find( $gallery_id );
		if ( ! $gallery ) {
			return Security::is_allowed( 'NextGEN Manage others gallery' );
		}

		if ( get_current_user_id() === (int) $gallery->author ) {
			return true;
		}

		return Security::is_allowed( 'NextGEN Manage others gallery' );
	}

	/**
	 * Get all images with optional filtering and pagination.
	 *
	 * @param WP_REST_Request $request The request object containing gallery_id, per_page, page, orderby, order parameters.
	 * @return WP_REST_Response
	 */
	public static function get_images( WP_REST_Request $request ) {
		global $wpdb;
		$gallery_id = $request->get_param( 'gallery_id' );

		// Get pagination parameters
		$per_page_param = (int) $request->get_param( 'per_page' );
		// Normalize all negative values to -1 (treated as "all") for consistency
		if ( $per_page_param < 0 ) {
			$per_page_param = -1;
		}
		// Handle -1 as "all" (WordPress standard for unlimited pagination)
		$per_page = ( -1 === $per_page_param ) ? PHP_INT_MAX : $per_page_param;
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$offset   = ( $page - 1 ) * $per_page;

		// Get ordering parameters
		$orderby = $request->get_param( 'orderby' ) ?? 'sortorder';
		$order   = strtoupper( $request->get_param( 'order' ) ?? 'ASC' );

		$mapper = ImageMapper::get_instance();
		$query  = $mapper->select();

		if ( $gallery_id ) {
			$query->where( [ 'galleryid = %d', $gallery_id ] );
		}

		// Calculate total items for pagination
		$count_query = $mapper->select();
		if ( $gallery_id ) {
			$count_query->where( [ 'galleryid = %d', $gallery_id ] );
		}
		$total_items = count( $count_query->run_query( false, false, true ) );

		// Add ordering and pagination. Append a deterministic pid tiebreaker so the
		// displayed order matches the order ensure_sequential_sortorders() renumbers in
		// (sortorder ASC, pid ASC). Without it, healing tied sortorders could reshuffle
		// rows relative to what the user saw, landing a drag-drop on the wrong side.
		$query->order_by( $orderby, $order );
		if ( 'pid' !== $orderby ) {
			$query->order_by( 'pid', 'ASC' );
		}
		if ( -1 !== $per_page_param ) {
			$query->limit( $per_page, $offset );
		}

		$images      = $query->run_query();
		$images_data = array_map( [ self::class, 'prepare_image_for_response' ], $images );

		$response = new WP_REST_Response( $images_data );

		// Add pagination headers
		if ( -1 !== $per_page_param ) {
			$total_pages = ceil( $total_items / $per_page );
			$response->header( 'X-WP-Total', $total_items );
			$response->header( 'X-WP-TotalPages', $total_pages );
		} else {
			// When returning all items, set total pages to 1
			$response->header( 'X-WP-Total', $total_items );
			$response->header( 'X-WP-TotalPages', 1 );
		}

		return $response;
	}

	/**
	 * Get a single image by ID.
	 *
	 * @param WP_REST_Request $request The request object containing the image ID.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$mapper   = ImageMapper::get_instance();
		$image    = $mapper->find( $image_id );

		if ( ! $image ) {
			return new WP_Error( 'not_found', __( 'Image not found', 'nggallery' ), [ 'status' => 404 ] );
		}

		return new WP_REST_Response( self::prepare_image_for_response( $image ) );
	}

	/**
	 * Update an image.
	 *
	 * @param WP_REST_Request $request The request object containing image data to update.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function update_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$mapper   = ImageMapper::get_instance();
		$image    = $mapper->find( $image_id );

		if ( ! $image ) {
			return new WP_Error( 'not_found', __( 'Image not found', 'nggallery' ), [ 'status' => 404 ] );
		}

		$fields = [ 'alttext', 'description', 'exclude', 'image_slug', 'sortorder', 'pricelist_id' ];
		foreach ( $fields as $field ) {
			if ( $request->has_param( $field ) ) {
				$value = $request->get_param( $field );
				if ( 'exclude' === $field ) {
					$value = $value ? 1 : 0;
				}
				$image->$field = $value;
			}
		}

		try {
			$mapper->save( $image );

			// Handle tags separately using WordPress taxonomy functions
			if ( $request->has_param( 'tags' ) ) {
				$tags = $request->get_param( 'tags' );
				if ( ! is_string( $tags ) ) {
					return new WP_Error( 'invalid_tags', __( 'Tags must be a string', 'nggallery' ), [ 'status' => 400 ] );
				}
				$tags = array_filter(
					array_map( 'trim', explode( ',', $tags ) ),
					function ( $tag ) {
						return ! empty( $tag );
					}
				);
				$tags = array_values( array_unique( $tags ) );
				wp_set_object_terms( $image->pid, $tags, 'ngg_tag' );
			}

			// Reload image from database to get fresh data
			$fresh_image = $mapper->find( $image_id );

			return new WP_REST_Response( self::prepare_image_for_response( $fresh_image ) );
		} catch ( \Exception $e ) {
			return new WP_Error( 'update_failed', $e->getMessage(), [ 'status' => 500 ] );
		}
	}

	/**
	 * Move an image to a new sortorder slot via a single range-shift.
	 *
	 * Accepts one of three target modes in the request body:
	 *   - after_pid: place the moved image immediately after another image.
	 *   - before_pid: place it immediately before another image.
	 *   - target_page (+ per_page): place it at the end of the given page,
	 *     clamped to the end of the gallery if the page is beyond the total.
	 *
	 * Executes at most two SQL statements: a range UPDATE that shifts every
	 * affected row's sortorder by ±1, then a single UPDATE that positions the
	 * moved row. Both rely on InnoDB row locks for concurrency safety.
	 *
	 * @param WP_REST_Request $request The request carrying the move parameters.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function move_image( WP_REST_Request $request ) {
		global $wpdb;

		$pid        = (int) $request->get_param( 'id' );
		$gallery_id = (int) $request->get_param( 'gallery_id' );

		$mapper = ImageMapper::get_instance();
		$image  = $mapper->find( $pid );
		if ( ! $image || (int) $image->galleryid !== $gallery_id ) {
			return new WP_Error(
				'ngg_image_not_found',
				__( 'Image not found in this gallery', 'nggallery' ),
				[ 'status' => 404 ]
			);
		}

		$table = $mapper->get_table_name();

		// Heal galleries whose sortorders default to 0; only refetch when the heal actually ran.
		$healed = self::ensure_sequential_sortorders( $gallery_id, $table );
		if ( is_wp_error( $healed ) ) {
			return $healed;
		}
		if ( $healed ) {
			$mapper->flush_query_cache();
			Transient::flush( 'displayed_gallery_rendering' );

			$image = $mapper->find( $pid );
		}

		$current_sortorder = (int) $image->sortorder;
		$target_sortorder  = self::resolve_target_sortorder( $request, $gallery_id, $current_sortorder );
		if ( is_wp_error( $target_sortorder ) ) {
			return $target_sortorder;
		}

		if ( $target_sortorder === $current_sortorder ) {
			return new WP_REST_Response( self::prepare_image_for_response( $image ), 200 );
		}

		// Atomic: roll back the shift if the final positional update fails.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			return new WP_Error(
				'ngg_move_failed',
				__( 'Failed to start transaction', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		if ( $current_sortorder < $target_sortorder ) {
			// Forward move: pull rows in (S, N] down by 1 to close the gap left
			// by the moved row, then drop the moved row into N.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$shift_result = $wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"UPDATE {$table} SET sortorder = sortorder - 1 WHERE galleryid = %d AND sortorder > %d AND sortorder <= %d",
					$gallery_id,
					$current_sortorder,
					$target_sortorder
				)
			);
		} else {
			// Backward move: push rows in [N, S) up by 1, then drop the moved row into N.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$shift_result = $wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"UPDATE {$table} SET sortorder = sortorder + 1 WHERE galleryid = %d AND sortorder >= %d AND sortorder < %d",
					$gallery_id,
					$target_sortorder,
					$current_sortorder
				)
			);
		}

		if ( false === $shift_result ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error(
				'ngg_move_failed',
				__( 'Failed to reorder images', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$update_result = $wpdb->update( $table, [ 'sortorder' => $target_sortorder ], [ 'pid' => $pid ] );

		if ( false === $update_result ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error(
				'ngg_move_failed',
				__( 'Failed to reorder images', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $wpdb->query( 'COMMIT' ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error(
				'ngg_move_failed',
				__( 'Failed to commit transaction', 'nggallery' ),
				[ 'status' => 500 ]
			);
		}

		$mapper->flush_query_cache();
		Transient::flush( 'displayed_gallery_rendering' );

		$fresh = $mapper->find( $pid );
		return new WP_REST_Response( self::prepare_image_for_response( $fresh ), 200 );
	}

	/**
	 * Resolve the target sortorder for a move based on which mode the request used.
	 *
	 * @param WP_REST_Request $request           The request carrying after_pid / before_pid / target_page.
	 * @param int             $gallery_id        ID of the gallery the move is scoped to.
	 * @param int             $current_sortorder Sortorder of the image being moved.
	 * @return int|WP_Error Target sortorder, or WP_Error on invalid input.
	 */
	private static function resolve_target_sortorder( WP_REST_Request $request, $gallery_id, $current_sortorder ) {
		global $wpdb;

		$mapper = ImageMapper::get_instance();
		$table  = $mapper->get_table_name();

		$after  = (int) $request->get_param( 'after_pid' );
		$before = (int) $request->get_param( 'before_pid' );
		$page   = (int) $request->get_param( 'target_page' );

		// Require exactly one target mode; reject ambiguous combinations.
		$mode_count = ( $after > 0 ? 1 : 0 ) + ( $before > 0 ? 1 : 0 ) + ( $page > 0 ? 1 : 0 );
		if ( $mode_count > 1 ) {
			return new WP_Error(
				'ngg_conflicting_target',
				__( 'Provide exactly one of after_pid, before_pid, or target_page', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		if ( $after > 0 || $before > 0 ) {
			$reference_pid = $after > 0 ? $after : $before;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$row = $wpdb->get_row(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT sortorder FROM {$table} WHERE pid = %d AND galleryid = %d",
					$reference_pid,
					$gallery_id
				)
			);
			if ( ! $row ) {
				return new WP_Error(
					'ngg_reference_not_found',
					__( 'Reference image is not in this gallery', 'nggallery' ),
					[ 'status' => 400 ]
				);
			}
			$neighbor       = (int) $row->sortorder;
			$moving_forward = $current_sortorder < $neighbor;
			// The range-shift in move_image() vacates one neighboring slot, so the
			// target lands on the neighbor itself in the "shift-toward" direction
			// and on the next/prev slot in the "shift-away" direction.
			if ( $after > 0 ) {
				return $moving_forward ? $neighbor : $neighbor + 1;
			}
			return $moving_forward ? $neighbor - 1 : $neighbor;
		}

		if ( $page > 0 ) {
			$per_page = (int) $request->get_param( 'per_page' );
			if ( $per_page < 1 ) {
				$per_page = 50;
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$max_row = $wpdb->get_row(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT COALESCE(MAX(sortorder), 0) AS max_sort FROM {$table} WHERE galleryid = %d",
					$gallery_id
				)
			);
			$end_of_page    = $page * $per_page;
			$end_of_gallery = (int) $max_row->max_sort;
			return min( $end_of_page, $end_of_gallery );
		}

		return new WP_Error(
			'ngg_missing_target',
			__( 'Must provide after_pid, before_pid, or target_page', 'nggallery' ),
			[ 'status' => 400 ]
		);
	}

	/**
	 * Renumber a gallery's sortorders to 1..N when they are not unique.
	 *
	 * The range-shift in move_image() shifts a contiguous band of sortorder values
	 * by ±1, so its one hard requirement is that sortorders are UNIQUE — on ties the
	 * shift collapses two rows onto the same slot. Gaps and a non-1 start are harmless
	 * to the shift, so we deliberately do NOT renumber for those: doing so would rewrite
	 * the entire gallery on the first move after any delete (which leaves a gap) for no
	 * benefit. We only heal duplicates — e.g. freshly-uploaded images that all default
	 * to sortorder 0 — renumbering in display order (sortorder, then pid as tie-breaker).
	 *
	 * @param int    $gallery_id ID of the gallery to renumber.
	 * @param string $table      Fully-qualified images table name.
	 * @return bool|WP_Error True if renumbered, false if already unique, WP_Error on write failure.
	 */
	private static function ensure_sequential_sortorders( $gallery_id, $table ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$stats = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) AS cnt, COUNT(DISTINCT sortorder) AS distinct_cnt FROM {$table} WHERE galleryid = %d",
				$gallery_id
			)
		);
		if ( ! $stats ) {
			return false;
		}
		$cnt = (int) $stats->cnt;
		if ( $cnt === 0 ) {
			return false;
		}
		// Sortorders are already safe for the range-shift as long as they are unique.
		// Only a duplicate (distinct count below the row count) forces a renumber.
		if ( $cnt === (int) $stats->distinct_cnt ) {
			return false;
		}

		// Prefetch + per-row update: MySQL forbids ORDER BY on multi-table UPDATE,
		// and user-defined vars in UPDATE are deprecated in 8.0+.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT pid FROM {$table} WHERE galleryid = %d ORDER BY sortorder ASC, pid ASC",
				$gallery_id
			)
		);
		if ( empty( $rows ) ) {
			return false;
		}

		// Wrap the per-row renumber in its own transaction: a mid-loop write failure
		// must not leave the gallery half-renumbered (a worse, possibly newly-duplicated
		// state). This is separate from the move's own transaction, which has not started
		// yet at the call site.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'START TRANSACTION' );

		$sortorder = 1;
		foreach ( $rows as $row ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$update_result = $wpdb->update( $table, [ 'sortorder' => $sortorder ], [ 'pid' => (int) $row->pid ] );
			if ( false === $update_result ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query( 'ROLLBACK' );
				return new WP_Error(
					'ngg_renumber_failed',
					__( 'Failed to renumber gallery sortorders', 'nggallery' ),
					[ 'status' => 500 ]
				);
			}
			++$sortorder;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'COMMIT' );

		return true;
	}

	/**
	 * Delete an image.
	 *
	 * @param WP_REST_Request $request The request object containing the image ID to delete.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function delete_image( WP_REST_Request $request ) {
		global $wpdb;
		$image_id = $request->get_param( 'id' );
		$mapper   = ImageMapper::get_instance();
		$image    = $mapper->find( $image_id );

		if ( ! $image ) {
			return new WP_Error( 'not_found', __( 'Image not found', 'nggallery' ), [ 'status' => 404 ] );
		}

		// Get the gallery ID before deletion
		$gallery_id = $image->galleryid;

		try {
			// Fire the action hook for other plugins to respond to image deletion
			// This must be called BEFORE any deletion so that listening plugins can access the database record
			do_action( 'ngg_delete_picture', $image_id, $image );

			// Check if we should delete image files from filesystem
			$settings = \Imagely\NGG\Settings\Settings::get_instance();
			$storage  = \Imagely\NGG\DataStorage\Manager::get_instance();

			if ( $settings->get( 'deleteImg' ) ) {
				// Delete image files from filesystem and database
				$delete_success = $storage->delete_image( $image );
				if ( ! $delete_success ) {
					return new WP_Error(
						'delete_files_failed',
						__( 'Could not delete image file(s) from disk', 'nggallery' ),
						[ 'status' => 500 ]
					);
				}
			} else {
				// Only remove from database, keep files on disk
				$mapper->destroy( $image );
			}

			// Check if the deleted image was the gallery's preview image
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$gallery_preview = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT previewpic FROM {$wpdb->nggallery} WHERE gid = %d AND previewpic = %d",
					$gallery_id,
					$image_id
				)
			);

			// If this was the preview image, update the gallery to use the first available image
			if ( $gallery_preview ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$first_image_id = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT pid FROM {$wpdb->nggpictures} WHERE galleryid = %d ORDER BY sortorder ASC, pid ASC LIMIT 1",
						$gallery_id
					)
				);

				// Update the gallery's previewpic field
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->update(
					$wpdb->nggallery,
					[ 'previewpic' => $first_image_id ? (int) $first_image_id : 0 ],
					[ 'gid' => $gallery_id ],
					[ '%d' ],
					[ '%d' ]
				);
			}

			Transient::flush( 'rest_galleries' );
			return new WP_REST_Response(
				[
					'message'       => __( 'Image deleted successfully', 'nggallery' ),
					'deleted'       => true,
					'id'            => $image_id,
					'files_deleted' => (bool) $settings->get( 'deleteImg' ),
				]
			);
		} catch ( \Exception $e ) {
			return new WP_Error( 'delete_failed', $e->getMessage(), [ 'status' => 500 ] );
		}
	}

	/**
	 * Bulk update images.
	 *
	 * @param WP_REST_Request $request The request object containing an array of image data to update.
	 * @return WP_REST_Response
	 */
	public static function bulk_update_images( WP_REST_Request $request ) {
		$images_data = $request->get_param( 'images' );
		$mapper      = ImageMapper::get_instance();
		$updated     = [];
		$errors      = [];

		// Fast path: sort-only payloads (id + sortorder) skip the costly per-image save_entity()
		// and update the column directly. Avoids the timeout that left large galleries half-sorted.
		$sortorder_only = ! empty( $images_data );
		foreach ( $images_data as $image_data ) {
			$data       = (array) $image_data;
			$extra_keys = array_diff( array_keys( $data ), [ 'id', 'sortorder' ] );
			if ( ! empty( $extra_keys ) || ! isset( $data['id'], $data['sortorder'] ) ) {
				$sortorder_only = false;
				break;
			}
		}

		if ( $sortorder_only ) {
			$db    = $mapper->_wpdb();
			$table = $mapper->get_table_name();
			$now   = time();

			foreach ( $images_data as $image_data ) {
				$pid = absint( $image_data['id'] ?? 0 );
				if ( ! $pid ) {
					continue;
				}

				$can_manage = self::current_user_can_manage_image( $pid );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					// translators: %d is the image ID.
					$errors[] = sprintf( __( 'Permission denied for image ID %d', 'nggallery' ), $pid );
					continue;
				}

				$sortorder = absint( $image_data['sortorder'] );

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$result = $db->update(
					$table,
					[
						'sortorder'  => $sortorder,
						'updated_at' => $now,
					],
					[ 'pid' => $pid ]
				);

				// $wpdb->update() returns false on error, or the (possibly 0) number of affected rows on success.
				if ( false === $result ) {
					// translators: %d is the image ID.
					$errors[] = sprintf( __( 'Failed to update sort order for image ID %d', 'nggallery' ), $pid );
					continue;
				}

				$updated[] = [
					'pid'       => $pid,
					'sortorder' => $sortorder,
				];
			}

			// Flush once for the whole batch, not per image.
			Transient::flush( 'displayed_gallery_rendering' );

			return new WP_REST_Response(
				[
					'updated' => $updated,
					'errors'  => $errors,
					'message' => sprintf(
						// translators: %1$d is the number of updated images, %2$d is the number of errors.
						__( 'Updated %1$d images with %2$d errors', 'nggallery' ),
						count( $updated ),
						count( $errors )
					),
				],
				count( $errors ) > 0 ? 207 : 200
			);
		}

		foreach ( $images_data as $image_data ) {
			$can_manage = self::current_user_can_manage_image( (int) $image_data['id'] );
			if ( is_wp_error( $can_manage ) || ! $can_manage ) {
				// translators: %d is the image ID.
				$errors[] = sprintf( __( 'Permission denied for image ID %d', 'nggallery' ), $image_data['id'] );
				continue;
			}

			$image = $mapper->find( $image_data['id'] );
			if ( ! $image ) {
				// translators: %d is the image ID.
				$errors[] = sprintf( __( 'Image with ID %d not found', 'nggallery' ), $image_data['id'] );
				continue;
			}

			$fields = [ 'alttext', 'description', 'exclude', 'image_slug', 'sortorder', 'pricelist_id' ];
			foreach ( $fields as $field ) {
				if ( isset( $image_data[ $field ] ) ) {
					$value = $image_data[ $field ];
					if ( 'exclude' === $field ) {
						$value = $value ? 1 : 0;
					}
					$image->$field = $value;
				}
			}

			try {
				$mapper->save( $image );

				// Handle tags separately using WordPress taxonomy functions
				if ( isset( $image_data['tags'] ) ) {
					$tags = $image_data['tags'];
					if ( is_string( $tags ) ) {
						$tags = array_filter(
							array_map( 'trim', explode( ',', $tags ) ),
							function ( $tag ) {
								return ! empty( $tag );
							}
						);
						$tags = array_values( array_unique( $tags ) );
					}
					wp_set_object_terms( $image->pid, $tags, 'ngg_tag' );
				}

				$updated[] = self::prepare_image_for_response( $image );
			} catch ( \Exception $e ) {
				$errors[] = sprintf(
					// translators: %1$d is the image ID, %2$s is the error message.
					__( 'Failed to update image %1$d: %2$s', 'nggallery' ),
					$image_data['id'],
					$e->getMessage()
				);
			}
		}

		return new WP_REST_Response(
			[
				'updated' => $updated,
				'errors'  => $errors,
				'message' => sprintf(
					// translators: %1$d is the number of updated images, %2$d is the number of errors.
					__( 'Updated %1$d images with %2$d errors', 'nggallery' ),
					count( $updated ),
					count( $errors )
				),
			],
			count( $errors ) > 0 ? 207 : 200
		);
	}

	/**
	 * A name for an attachment that is usable in a message shown to an operator.
	 *
	 * Offloaded media has no local path, so get_attached_file() returns false for every id in
	 * such a request - falling back to the raw id would name the whole failure list in database
	 * numbers, and mb_basename( '' ) would name it in empty strings. The attachment URL still
	 * resolves when the file is off-server, and the post title is the last thing that survives.
	 *
	 * @param int $id Attachment id.
	 * @return string
	 */
	private static function attachment_display_name( $id ) {
		$abspath = get_attached_file( $id );
		if ( $abspath ) {
			return \Imagely\NGG\Display\I18N::mb_basename( $abspath );
		}

		$url = wp_get_attachment_url( $id );
		if ( $url ) {
			return \Imagely\NGG\Display\I18N::mb_basename( strtok( $url, '?' ) );
		}

		// The title is only safe to echo back for a real attachment this user may read: ids
		// arrive unvalidated, get_the_title() applies no capability check, and over REST
		// is_admin() is false, so a draft or private post would otherwise have its title
		// returned - prefixed "Private:" - to anyone who can manage galleries.
		if ( 'attachment' === get_post_type( $id ) && current_user_can( 'read_post', $id ) ) {
			$title = get_the_title( $id );

			if ( '' !== trim( (string) $title ) ) {
				return $title;
			}
		}

		return '#' . (int) $id;
	}

	/**
	 * Import images from the WordPress media library into a NextGEN gallery.
	 *
	 * @param WP_REST_Request $request The request object containing gallery_id, gallery_name, and attachment_ids parameters.
	 * @return WP_REST_Response
	 */
	public static function import_media_library( WP_REST_Request $request ) {
		// Image imports can require significant memory for decoding/resizing.
		// In REST requests, WordPress may still be running at WP_MEMORY_LIMIT (often low).
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			\wp_raise_memory_limit( 'image' );
		}

		$retval          = [];
		$created_gallery = false;
		$gallery_id      = (int) $request->get_param( 'gallery_id' );
		$gallery_name    = $request->get_param( 'gallery_name' );
		$attachment_ids  = $request->get_param( 'attachment_ids' );

		$gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$image_mapper   = \Imagely\NGG\DataMappers\Image::get_instance();

		if ( empty( $attachment_ids ) || ! is_array( $attachment_ids ) ) {
			return new WP_REST_Response(
				[
					'error' => __( 'An unexpected error occurred.', 'nggallery' ),
				],
				400
			);
		}

		if ( 0 === $gallery_id ) {
			if ( ! empty( $gallery_name ) ) {
				$gallery = $gallery_mapper->create( [ 'title' => $gallery_name ] );
				if ( ! $gallery->save() ) {
					return new WP_REST_Response(
						[
							'error' => $gallery->validate(),
						],
						400
					);
				} else {
					$created_gallery = true;
					$gallery_id      = $gallery->id();
				}
			} else {
				return new WP_REST_Response(
					[
						'error' => __( 'No gallery name specified', 'nggallery' ),
					],
					400
				);
			}
		}
		Transient::flush( 'rest_galleries' );

		$retval['gallery_id'] = $gallery_id;
		$storage              = \Imagely\NGG\DataStorage\Manager::get_instance();
		$retval['image_ids']  = [];

		$skipped = [];

		foreach ( $attachment_ids as $id ) {
			try {
				// Each id is an object reference from the request, so it needs an object-level
				// check of its own. check_edit_permission() authorized the *destination* - the
				// gallery these bytes land in - and says nothing about the attachment they are
				// read from, so without this a caller who may manage one gallery can read any
				// file in the media library, including attachments on other users' private and
				// draft posts, and have it republished at a public URL inside that gallery.
				//
				// Reported by id rather than by name: attachment_display_name() resolves the
				// basename through get_attached_file(), which answers for an attachment this
				// caller may not read, so naming the file here would leak the very thing the
				// check just refused. The *reason* leaks nothing and is included, otherwise this
				// reads to an admin as "those files are corrupt" rather than "those files are not
				// yours" - a different support ticket and a different user action.
				if ( ! Security::can_read_attachment( $id ) ) {
					$skipped[] = sprintf(
						/* translators: %d: attachment ID. */
						__( '#%d (you do not have permission to import this file)', 'nggallery' ),
						(int) $id
					);
					continue;
				}

				// Import the attached file: for a downscaled upload that is the smaller
				// derivative, which keeps thumbnail generation within reach of a modest
				// memory limit. The untouched original is promoted into the backup below.
				$abspath = get_attached_file( $id );

				// get_attached_file() returns false for an offloaded attachment or a bad id,
				// and file_get_contents() raises an uncatchable ValueError on an empty path, so
				// this has to be refused before the read. Collected and skipped rather than
				// returned: one unresolvable attachment must not discard the ones already
				// imported in this request, nor the post-loop cleanup.
				if ( empty( $abspath ) || ! is_readable( $abspath ) ) {
					$skipped[] = self::attachment_display_name( $id );
					continue;
				}

				$file_data = file_get_contents( $abspath );

				if ( empty( $file_data ) ) {
					$skipped[] = \Imagely\NGG\Display\I18N::mb_basename( $abspath );
					continue;
				}
				// Name the gallery file after the original so the '-scaled' suffix does not leak.
				$file_name  = \Imagely\NGG\Display\I18N::mb_basename( Filesystem::get_attachment_original_abspath( $id ) );
				$attachment = get_post( $id );
				$image      = $storage->upload_image( $gallery_id, $file_name, $file_data );

				if ( $image ) {
					// Potentially import metadata from WordPress.
					$image = $image_mapper->find( $image );

					$storage->store_attachment_original_as_backup( $image, $id );
					if ( ! empty( $attachment->post_excerpt ) ) {
						$image->alttext = $attachment->post_excerpt;
					}
					if ( ! empty( $attachment->post_content ) ) {
						$image->description = $attachment->post_content;
					}

					$image = apply_filters( 'ngg_medialibrary_imported_image', $image, $attachment );
					$image_mapper->save( $image );
					$retval['image_ids'][] = $image->{$image->id_field};
				} else {
					$skipped[] = $file_name;
				}
			} catch ( \Exception $ex ) {
				// One attachment failing (disk-full, unreadable image, etc.) must not discard
				// attachments already imported earlier in this same request, nor destroy a
				// gallery this same request just created for them.
				//
				// Resolved through Sanitization::exception_message() rather than getMessage():
				// E_EntityNotFoundException is an empty class thrown bare, so its message is ''
				// and both the log line and the reported reason would otherwise say nothing.
				$reason = \Imagely\NGG\Util\Sanitization::exception_message(
					$ex,
					__( 'The image could not be imported.', 'nggallery' )
				);

				error_log( 'NextGEN Gallery: could not import media library attachment #' . $id . ': ' . $reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

				// The reason travels with the filename, not just to the log, so the response can
				// say why a file was dropped.
				$skipped[] = sprintf(
					/* translators: 1: image filename, 2: reason the image could not be imported */
					__( '%1$s (%2$s)', 'nggallery' ),
					self::attachment_display_name( $id ),
					$reason
				);
			}
		}

		if ( empty( $retval['image_ids'] ) ) {
			// Not when a row survived: import_image_file() keeps one whose files it could not
			// remove, and destroying its gallery makes it unreachable from every admin screen
			// while the files stay served.
			if ( $created_gallery && ! $image_mapper->find_all_for_gallery( $gallery_id ) ) {
				$gallery_mapper->destroy( $gallery_id );
			}

			// Names the files rather than answering a bare "Image generation failed": on an
			// offloaded-media site get_attached_file() returns false for every id, so every one
			// is refused before the read and this is the only response the admin sees.
			$error = ! empty( $skipped )
				? sprintf(
					/* translators: %s: comma-separated list of files that could not be imported, each with its reason. */
					__( 'No images were imported. These files could not be imported: %s.', 'nggallery' ),
					implode( ', ', $skipped )
				)
				: __( 'Image generation failed', 'nggallery' );

			return new WP_REST_Response(
				[
					'error'         => $error,
					'skipped_files' => array_map( 'esc_html', $skipped ),
				],
				500
			);
		}

		// If images were imported successfully, set the first one as preview if gallery has no preview
		self::set_gallery_preview_if_empty( $gallery_id, $retval['image_ids'][0] );

		if ( ! empty( $skipped ) ) {
			$retval['skipped_files'] = array_map( 'esc_html', $skipped );
			$retval['warning']       = esc_html(
				sprintf(
					/* translators: %s: comma-separated list of file names that could not be imported. */
					__( 'These files were skipped and could not be imported: %s.', 'nggallery' ),
					implode( ', ', $skipped )
				)
			);
		}

		$retval['gallery_name'] = esc_html( $gallery_name );
		return new WP_REST_Response( $retval );
	}

	/**
	 * Upload an image or zip file to a gallery.
	 *
	 * @param WP_REST_Request $request The request object containing gallery_id, gallery_name, and file parameters.
	 * @return WP_REST_Response
	 */
	public static function upload_image( WP_REST_Request $request ) {
		// Image uploads can require significant memory for decoding/resizing.
		// In REST requests, WordPress may still be running at WP_MEMORY_LIMIT (often low).
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			\wp_raise_memory_limit( 'image' );
		}

		$created_gallery = false;
		$gallery_id      = (int) $request->get_param( 'gallery_id' );
		$gallery_name    = $request->get_param( 'gallery_name' );
		$gallery_mapper  = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$retval          = [ 'gallery_name' => esc_html( $gallery_name ) ];

		if ( ! class_exists( 'DOMDocument' ) ) {
			$retval['error'] = __( 'Please ask your hosting provider or system administrator to enable the PHP XML module which is required for image uploads', 'nggallery' );
			return new WP_REST_Response( $retval, 500 );
		}

		if ( 0 === $gallery_id ) {
			if ( ! empty( $gallery_name ) ) {
				$gallery = $gallery_mapper->create( [ 'title' => $gallery_name ] );
				if ( ! $gallery->save() ) {
					$retval['error'] = $gallery->validate();
					return new WP_REST_Response( $retval, 400 );
				} else {
					$created_gallery = true;
					$gallery_id      = $gallery->id();
				}
			} else {
				$retval['error'] = __( 'No gallery name specified', 'nggallery' );
				return new WP_REST_Response( $retval, 400 );
			}
		}

		$retval['gallery_id'] = $gallery_id;
		$settings             = \Imagely\NGG\Settings\Settings::get_instance();
		$storage              = \Imagely\NGG\DataStorage\Manager::get_instance();

		try {
			// Pass true to skip nonce check since REST API has its own authentication
			if ( $storage->is_zip( true ) ) {
				$results = $storage->upload_zip( $gallery_id, true );
				if ( $results ) {
					$retval = $results;
					// If ZIP uploaded successfully and has images, set preview if empty
					if ( ! empty( $results['image_ids'] ) && is_array( $results['image_ids'] ) ) {
						self::set_gallery_preview_if_empty( $gallery_id, $results['image_ids'][0] );
					}
				} else {
					$retval['error'] = __( 'Failed to extract images from ZIP', 'nggallery' );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
			} elseif ( isset( $_FILES['file'] ) ) {
				$image_id = $storage->upload_image( $gallery_id );
				if ( $image_id ) {
					$retval['image_ids'] = [ $image_id ];
					// check if image was resized correctly.
					if ( $settings->get( 'imgAutoResize' ) ) {
						$image_path  = $storage->get_full_abspath( $image_id );
						$image_thumb = new \Imagely\NGG\DataTypes\LegacyThumbnail( $image_path, true );
						if ( $image_thumb->error ) {
							// translators: %s is the error message.
							$retval['error'] = sprintf( __( 'Automatic image resizing failed [%1$s].', 'nggallery' ), $image_thumb->errmsg );
						}
					}
					// check if thumb was generated correctly.
					$thumb_path = $storage->get_image_abspath( $image_id, 'thumb' );
					if ( ! file_exists( $thumb_path ) ) {
						$retval['error'] = __( 'Thumbnail generation failed.', 'nggallery' );
					}

					// If gallery has no preview image, set this as the preview
					self::set_gallery_preview_if_empty( $gallery_id, $image_id );
				} else {
					$retval['error'] = __( 'Image generation failed', 'nggallery' );
				}
			} else {
				$retval['error'] = __( 'No file uploaded', 'nggallery' );
			}
		} catch ( \RuntimeException $ex ) {
			// Non-empty by construction: $retval['error'] decides the 500 below, and a bare
			// E_EntityNotFoundException from import_image_file() carries no message.
			$retval['error'] = \Imagely\NGG\Util\Sanitization::exception_message(
				$ex,
				__( 'The image could not be imported.', 'nggallery' )
			);
			if ( $created_gallery ) {
				$gallery_mapper->destroy( $gallery_id );
			}
		} catch ( \Exception $ex ) {
			// translators: %s is the error message.
			$retval['error'] = sprintf( __( 'An unexpected error occurred: %s', 'nggallery' ), $ex->getMessage() );
		}

		$status = ! empty( $retval['error'] ) ? 500 : 200;
		if ( $status === 200 ) {
			Transient::flush( 'rest_galleries' );
		}
		return new WP_REST_Response( $retval, $status );
	}

	/**
	 * Returns the absolute root path allowed for folder browsing and importing.
	 *
	 * On single-site this is NGG_IMPORT_ROOT (wp-content by default).
	 * On multisite subsites the path is validated against the current blog's
	 * upload directory so that one subsite cannot reach another's files.
	 *
	 * @return string | \WP_Error
	 */
	private static function get_import_root() {
		$root = is_multisite()
			? \Imagely\NGG\DataStorage\Manager::get_instance()->get_upload_abspath()
			: NGG_IMPORT_ROOT;
		$root = str_replace( '/', DIRECTORY_SEPARATOR, $root );
		$root = untrailingslashit( $root );

		if ( is_multisite() && ! is_main_site() ) {
			$upload_dir       = wp_upload_dir();
			$basedir_realpath = realpath( $upload_dir['basedir'] );
			$blog_upload_base = trailingslashit( wp_normalize_path( $basedir_realpath ? $basedir_realpath : $upload_dir['basedir'] ) );
			$root_realpath    = realpath( $root );
			$resolved_root    = wp_normalize_path( $root_realpath ? $root_realpath : $root );
			$normalized_root  = trailingslashit( $resolved_root );

			if ( strpos( $normalized_root, $blog_upload_base ) !== 0 ) {
				return new \WP_Error(
					'invalid_gallery_path',
					__( 'Access denied. You can only browse your own upload directory.', 'nggallery' ),
					[ 'status' => 403 ]
				);
			}
		}

		return $root;
	}

	/**
	 * Browse a folder for import.
	 *
	 * @param WP_REST_Request $request The request object containing dir parameter.
	 * @return WP_REST_Response
	 */
	public static function browse_folder( WP_REST_Request $request ) {
		$retval = [];
		$dir    = $request->get_param( 'dir' );
		$root   = self::get_import_root();
		if ( is_wp_error( $root ) ) {
			return new WP_REST_Response( [ 'error' => $root->get_error_message() ], 403 );
		}
		$browse_path = $root;
		if ( ! empty( $dir ) ) {
			$browse_path = $root . DIRECTORY_SEPARATOR . ltrim( $dir, DIRECTORY_SEPARATOR );
		}

		// Use WP_Filesystem for directory listing.
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		if ( ! $wp_filesystem ) {
			$retval['error'] = __( 'Could not initialize WordPress filesystem API.', 'nggallery' );
			return new WP_REST_Response( $retval, 500 );
		}

		if ( ! $wp_filesystem->exists( $browse_path ) ) {
				$retval['error'] = __( 'Directory does not exist.', 'nggallery' );
			return new WP_REST_Response( $retval, 404 );
		}

		// Security: ensure path is within root using true prefix match; reject when realpath() fails (non-existent or symlink outside root).
		$real_browse = realpath( $browse_path );
		$real_root   = realpath( $root );
		if (
			false === $real_browse
			|| false === $real_root
			|| 0 !== strpos(
				trailingslashit( wp_normalize_path( $real_browse ) ),
				trailingslashit( wp_normalize_path( $real_root ) )
			)
		) {
			$retval['error'] = __( 'No permissions to browse folders. Try refreshing the page or ensuring that your user account has sufficient roles/privileges.', 'nggallery' );
				return new WP_REST_Response( $retval, 403 );
		}

		$dirlist = $wp_filesystem->dirlist( $browse_path );
		if ( ! is_array( $dirlist ) ) {
			$retval['error'] = __( 'Could not list directory contents.', 'nggallery' );
			return new WP_REST_Response( $retval, 500 );
		}

		$is_wp_content_path = untrailingslashit( WP_CONTENT_DIR ) === untrailingslashit( $browse_path );
		$is_wp_content_path = apply_filters( 'imagely_is_wp_content_path', $is_wp_content_path );

		$directories = [];
		foreach ( $dirlist as $name => $info ) {
			if ( 'd' !== $info['type'] ) {
				continue;
			}

			if ( $is_wp_content_path && in_array( $info['name'], [ 'plugins', 'upgrade', 'themes' ], true ) ) {
				continue;
			}

			$rel_file_path = ltrim( str_replace( $root, '', $browse_path . DIRECTORY_SEPARATOR . $name ), DIRECTORY_SEPARATOR );
			$directories[] = [
				'name'          => $name,
				'relative_path' => $rel_file_path . DIRECTORY_SEPARATOR,
			];
		}
		$retval['directories'] = $directories;
		return new WP_REST_Response( $retval, 200 );
	}

	/**
	 * Import a folder as a gallery.
	 *
	 * @param WP_REST_Request $request The request object containing folder, keep_location, and gallery_title parameters.
	 * @return WP_REST_Response
	 */
	public static function import_folder( WP_REST_Request $request ) {
		$retval        = [];
		$folder        = stripslashes( $request->get_param( 'folder' ) );
		$keep_location = $request->get_param( 'keep_location' ) === true || $request->get_param( 'keep_location' ) === 'on';
		$gallery_title = $request->get_param( 'gallery_title', null );

		// SECURITY: refuse stream-wrapper syntax ("://") on the raw folder parameter
		// before path_join()/path_is_absolute() touch it. A value such as
		// "phar:///path/to/image.png/x.php" makes the filesystem call open the named
		// Phar archive; on PHP <= 7.4 opening a Phar unserialises its metadata object
		// (object injection), while PHP 8.0+ defers that to an explicit getMetadata()
		// call. An import folder is always a plain path under the import root, never a
		// wrapper, so the value is refused regardless of PHP version.
		if ( false !== strpos( str_replace( '\\', '/', (string) $folder ), '://' ) ) {
			return new WP_REST_Response( [ 'error' => __( 'Invalid import folder.', 'nggallery' ) ], 400 );
		}

		if ( empty( $gallery_title ) ) {
			$gallery_title = null;
		}

		$root = self::get_import_root();

		if ( is_wp_error( $root ) ) {
			return new WP_REST_Response( [ 'error' => $root->get_error_message() ], 403 );
		}

		$import_path = str_replace( '//', DIRECTORY_SEPARATOR, path_join( $root, $folder ) );

		// First check if the path exists and is accessible.
		if ( ! file_exists( $import_path ) ) {
			return new WP_REST_Response(
				[
					'error' => sprintf(
						// translators: %s is the folder path.
						__( 'The folder "%s" does not exist.', 'nggallery' ),
						$folder
					),
				],
				404
			);
		}

		if ( ! is_dir( $import_path ) ) {
			return new WP_REST_Response(
				[
					'error' => sprintf(
						// translators: %s is the folder path.
						__( 'The path "%s" is not a directory.', 'nggallery' ),
						$folder
					),
				],
				400
			);
		}

		if ( ! is_readable( $import_path ) ) {
			return new WP_REST_Response(
				[
					'error' => sprintf(
						// translators: %s is the folder path.
						__( 'The folder "%s" is not readable. Please check permissions.', 'nggallery' ),
						$folder
					),
				],
				403
			);
		}

		// Check if the folder is within the allowed root path (true prefix match; reject when realpath() fails to prevent traversal).
		$real_import = realpath( $import_path );
		$real_root   = realpath( $root );
		if (
			false === $real_import
			|| false === $real_root
			|| 0 !== strpos(
				trailingslashit( wp_normalize_path( $real_import ) ),
				trailingslashit( wp_normalize_path( $real_root ) )
			)
		) {
			return new WP_REST_Response(
				[
					'error' => sprintf(
						// translators: %s is the folder path.
						__( 'The folder "%s" is outside of the allowed import root path.', 'nggallery' ),
						$folder
					),
				],
				403
			);
		}

		$storage = \Imagely\NGG\DataStorage\Manager::get_instance();
		$storage->reset_skipped_zip_entries();

		try {
			$retval = $storage->import_gallery_from_fs(
				$import_path,
				false,
				! $keep_location,
				$gallery_title
			);

			if ( ! $retval ) {
				return new WP_REST_Response(
					[
						'error' => sprintf(
							// translators: %s is the folder path.
							__( 'No images were found in the folder "%s". Please ensure the folder contains supported image files (JPEG, PNG, GIF).', 'nggallery' ),
							$folder
						),
					],
					400
				);
			}

			// Every file failing is a failed import, not a success with an empty gallery. The
			// per-file catch in import_gallery_from_fs() means the exception that used to reach
			// the handler below no longer does, so this array arrives truthy with no ids and the
			// 200 at the end would report success. Mirrors the guard import_media_library() has.
			if ( empty( $retval['image_ids'] ) ) {
				$skipped = $storage->get_skipped_zip_entries();

				// Only tear the galleries down when the per-image rollback left nothing behind.
				// It deliberately keeps a row whose files it could not delete so the orphan stays
				// visible in Manage Gallery, and that screen authorizes an image through its
				// gallery - destroying the gallery would make such a row unreachable from every
				// admin screen while its files stay web-served.
				$image_mapper   = ImageMapper::get_instance();
				$surviving_rows = [];

				$created_gallery_ids = ! empty( $retval['created_gallery_ids'] )
					? array_unique( array_map( 'intval', $retval['created_gallery_ids'] ) )
					: array_filter( [ (int) ( $retval['gallery_id'] ?? 0 ) ] );

				foreach ( $created_gallery_ids as $created_gallery_id ) {
					if ( $image_mapper->find_all_for_gallery( $created_gallery_id ) ) {
						$surviving_rows[] = $created_gallery_id;
					}
				}

				if ( empty( $surviving_rows ) ) {
					$gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();

					// Sweep only a directory this request actually created. An adopted one - the
					// customer's own folder on a keep-in-place import, an FTP'd folder, or one
					// left by a renamed gallery - holds files this import never put there.
					$created_dir_ids = ! empty( $retval['created_gallery_dir_ids'] )
						? array_map( 'intval', $retval['created_gallery_dir_ids'] )
						: [];

					foreach ( $created_gallery_ids as $created_gallery_id ) {
						$gallery_mapper->destroy( $created_gallery_id, in_array( $created_gallery_id, $created_dir_ids, true ) );
					}
				}

				return new WP_REST_Response(
					[
						'error'         => ! empty( $skipped )
							? sprintf(
								/* translators: %s: comma-separated list of file names that could not be imported. */
								__( 'No images were imported. These files could not be imported: %s.', 'nggallery' ),
								implode( ', ', $skipped )
							)
							: sprintf(
								// translators: %s is the folder path.
								__( 'No images were imported from the folder "%s".', 'nggallery' ),
								$folder
							),
						'skipped_files' => array_map( 'esc_html', $skipped ),
					],
					500
				);
			}

			// If folder imported successfully with images, set the first one as preview if gallery has no preview
			if ( ! empty( $retval['gallery_id'] ) ) {
				self::set_gallery_preview_if_empty( $retval['gallery_id'], $retval['image_ids'][0] );
			}

			// import_gallery_from_fs() tolerates one file failing without aborting the rest, but
			// unlike upload_zip() this endpoint never read back which files that was - the only
			// trace was a WP_DEBUG log line. Surface it the same way upload_zip() does.
			$skipped = $storage->get_skipped_zip_entries();
			if ( ! empty( $skipped ) && is_array( $retval ) ) {
				$retval['skipped_files'] = array_map( 'esc_html', $skipped );
				$retval['warning']       = esc_html(
					sprintf(
						/* translators: %s: comma-separated list of file names that could not be imported. */
						__( 'These files were skipped and could not be imported: %s.', 'nggallery' ),
						implode( ', ', $skipped )
					)
				);
			}
		} catch ( \Exception $ex ) {
			return new WP_REST_Response(
				[
					'error' => sprintf(
						// translators: %1$s is the folder path, %2$s is the error message.
						__( 'Failed to import folder "%1$s": %2$s', 'nggallery' ),
						$folder,
						$ex->getMessage()
					),
				],
				500
			);
		}
		Transient::flush( 'rest_galleries' );

		return new WP_REST_Response( $retval, 200 );
	}

	/**
	 * Prepare image data for API response.
	 *
	 * @param Image $image The image object to prepare for response.
	 * @return array
	 */
	private static function prepare_image_for_response( $image ) {
		$tags = wp_get_object_terms( $image->pid, 'ngg_tag', [ 'fields' => 'names' ] );

		// Enhance meta_data with live EXIF data if missing critical fields like Orientation
		$meta_data = $image->meta_data;
		if ( ! is_array( $meta_data ) ) {
			$meta_data = [];
		}

		// If Orientation is missing from stored metadata, try to get it from live EXIF
		if ( ! isset( $meta_data['Orientation'] ) || empty( $meta_data['Orientation'] ) ) {
			require_once NGGALLERY_ABSPATH . '/lib/meta.php';
			$meta        = new \nggMeta( $image );
			$orientation = $meta->get_EXIF( 'Orientation' );
			if ( $orientation ) {
				$meta_data['Orientation'] = $orientation;
			}
		}

		// Get properly formatted image URLs using the storage manager
		$storage   = \Imagely\NGG\DataStorage\Manager::get_instance();
		$thumb_url = $storage->get_cache_busted_image_url( $image, 'thumb' );
		$image_url = $storage->get_cache_busted_image_url( $image, 'full' );

		return [
			'pid'            => $image->pid,
			'filename'       => $image->filename,
			'description'    => $image->description,
			'alttext'        => $image->alttext,
			'image_slug'     => $image->image_slug,
			'galleryid'      => $image->galleryid,
			'meta_data'      => $meta_data,
			'post_id'        => $image->post_id,
			'imagedate'      => $image->imagedate,
			'exclude'        => (bool) $image->exclude,
			'sortorder'      => (int) $image->sortorder,
			'tags'           => is_array( $tags ) ? $tags : [],
			'updated_at'     => $image->updated_at,
			'extras_post_id' => $image->extras_post_id,
			'pricelist_id'   => isset( $image->pricelist_id ) ? (int) $image->pricelist_id : 0,
			'thumb_url'      => $thumb_url ?? '',
			'image_url'      => $image_url ?? '',
		];
	}

	/**
	 * Set the gallery's preview image if it doesn't have one.
	 *
	 * @param int $gallery_id The gallery ID.
	 * @param int $image_id The image ID to set as preview.
	 * @return void
	 */
	private static function set_gallery_preview_if_empty( $gallery_id, $image_id ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$current_preview = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT previewpic FROM {$wpdb->nggallery} WHERE gid = %d",
				$gallery_id
			)
		);

		// Only set if gallery has no preview image (0 or empty)
		if ( empty( $current_preview ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update(
				$wpdb->nggallery,
				[ 'previewpic' => (int) $image_id ],
				[ 'gid' => $gallery_id ],
				[ '%d' ],
				[ '%d' ]
			);
		}
	}
}
