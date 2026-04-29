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
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataTypes\Image;
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Manage gallery' );
	}

	/**
	 * Check if user has permission to delete images
	 *
	 * @return bool
	 */
	public static function check_delete_permission() {
		return Security::is_allowed( 'NextGEN Manage gallery' );
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

		// Add ordering and pagination
		$query->order_by( $orderby, $order );
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

		foreach ( $images_data as $image_data ) {
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

		foreach ( $attachment_ids as $id ) {
			try {
				$abspath   = get_attached_file( $id );
				$file_data = file_get_contents( $abspath );

				if ( empty( $file_data ) ) {
					return new WP_REST_Response(
						[
							'error' => __( 'Image generation failed', 'nggallery' ),
						],
						500
					);
				}
				$file_name  = \Imagely\NGG\Display\I18N::mb_basename( $abspath );
				$attachment = get_post( $id );
				$image      = $storage->upload_image( $gallery_id, $file_name, $file_data );

				if ( $image ) {
					// Potentially import metadata from WordPress.
					$image = $image_mapper->find( $image );
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
					return new WP_REST_Response(
						[
							'error' => __( 'Image generation failed', 'nggallery' ),
						],
						500
					);
				}
			} catch ( \RuntimeException $ex ) {
				if ( $created_gallery ) {
					$gallery_mapper->destroy( $gallery_id );
				}
				return new WP_REST_Response(
					[
						'error' => $ex->getMessage(),
					],
					500
				);
			} catch ( \Exception $ex ) {
				return new WP_REST_Response(
					[
						'error'         => __( 'An unexpected error occurred.', 'nggallery' ),
						'error_details' => $ex->getMessage(),
					],
					500
				);
			}
		}

		// If images were imported successfully, set the first one as preview if gallery has no preview
		if ( ! empty( $retval['image_ids'] ) ) {
			self::set_gallery_preview_if_empty( $gallery_id, $retval['image_ids'][0] );
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
			$retval['error'] = $ex->getMessage();
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
			$blog_upload_base = trailingslashit( wp_normalize_path( realpath( $upload_dir['basedir'] ) ?: $upload_dir['basedir'] ) );
			$resolved_root    = wp_normalize_path( realpath( $root ) ?: $root );
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

		// Security: ensure path is within root.
		if ( false === strpos( realpath( $browse_path ), realpath( $root ) ) ) {
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

		// Check if the folder is within the allowed root path.
		if ( strpos( realpath( $import_path ), realpath( $root ) ) === false ) {
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

			// If folder imported successfully with images, set the first one as preview if gallery has no preview
			if ( ! empty( $retval['gallery_id'] ) && ! empty( $retval['image_ids'] ) ) {
				self::set_gallery_preview_if_empty( $retval['gallery_id'], $retval['image_ids'][0] );
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
		$thumb_url = $storage->get_image_url( $image, 'thumb' );
		$image_url = $storage->get_image_url( $image, 'full' );

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
