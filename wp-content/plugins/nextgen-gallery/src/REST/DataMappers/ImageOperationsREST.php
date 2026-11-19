<?php
/**
 * REST API endpoints for NextGEN Gallery image operations.
 *
 * @package Imagely\NGG\REST\DataMappers
 */

namespace Imagely\NGG\REST\DataMappers;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataTypes\Image;
use Imagely\NGG\Util\Security;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataStorage\EXIFWriter;

// Include the nggAdmin class
require_once NGG_LEGACY_MOD_DIR . '/admin/functions.php';

/**
 * Class ImageOperationsREST
 * Handles REST API endpoints for NextGEN Gallery image operations like cropping, rotating, etc.
 *
 * @package Imagely\NGG\REST\DataMappers
 */
class ImageOperationsREST {

	/**
	 * Sanitize a value to float
	 *
	 * @param mixed           $value   The value to sanitize.
	 * @param WP_REST_Request $request The request object.
	 * @param string          $param   The parameter name.
	 * @return float
	 */
	public static function sanitize_float( $value, $request, $param ) {
		return (float) $value;
	}

	/**
	 * Register the REST API routes for image operations
	 */
	public static function register_routes() {
		// Create new thumbnail with custom cropping
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/crop-thumbnail',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'create_new_thumbnail' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'x'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'y'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'w'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'h'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'rr' => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
				],
			]
		);

		// Rotate image
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/rotate',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'rotate_image' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id'       => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'rotation' => [
						'required' => true,
						'type'     => 'string',
						'enum'     => [ 'cw', 'ccw', 'fv', 'fh' ],
					],
				],
			]
		);

		// Create thumbnail
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/create-thumbnail',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'create_thumbnail' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id'            => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'width'         => [
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'Custom width for thumbnail (optional)',
					],
					'height'        => [
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'Custom height for thumbnail (optional)',
					],
					'fix_dimension' => [
						'type'        => 'boolean',
						// No default: the handler has to tell "omitted" from "false" so that
						// omitting it keeps the saved cropping setting.
						'description' => 'Whether to ignore aspect ratio (true) or maintain it (false)',
					],
				],
			]
		);

		// Resize image
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/resize',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'resize_image' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Set watermark
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/set-watermark',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'set_watermark' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Recover image
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/recover',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'recover_image' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Import metadata
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/import-metadata',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'import_metadata' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Strip orientation tag
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/strip-orientation',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'strip_orientation_tag' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Bulk resize images
		register_rest_route(
			'imagely/v1',
			'/images/bulk-resize',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_resize_images' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids' => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to resize',
					],
					'width'     => [
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'New width for images (optional, will use global setting if not provided)',
					],
					'height'    => [
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'New height for images (optional, will use global setting if not provided)',
					],
				],
			]
		);

		// Bulk import metadata
		register_rest_route(
			'imagely/v1',
			'/images/bulk-import-metadata',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_import_metadata' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids' => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to import metadata for',
					],
				],
			]
		);

		// Bulk copy images to gallery
		register_rest_route(
			'imagely/v1',
			'/images/bulk-copy',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_copy_images' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids'              => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to copy',
					],
					'destination_gallery_id' => [
						'required'          => true,
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'ID of the destination gallery',
					],
				],
			]
		);

		// Bulk move images to gallery
		register_rest_route(
			'imagely/v1',
			'/images/bulk-move',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_move_images' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids'              => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to move',
					],
					'destination_gallery_id' => [
						'required'          => true,
						'type'              => 'integer',
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'description'       => 'ID of the destination gallery',
					],
				],
			]
		);

		// Crop and regenerate all sizes
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/crop',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'crop_image' ],
				'permission_callback' => [ self::class, 'check_image_edit_permission' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'x'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'y'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'w'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'h'  => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
					'rr' => [
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => [ self::class, 'sanitize_float' ],
					],
				],
			]
		);

		// Bulk add tags to images
		register_rest_route(
			'imagely/v1',
			'/images/bulk-add-tags',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_add_tags' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids' => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to add tags to',
					],
					'tags'      => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'string',
						],
						'description' => 'Array of tag names to add to images',
					],
					'append'    => [
						'type'        => 'boolean',
						'default'     => true,
						'description' => 'Whether to append tags (true) or replace existing tags (false)',
					],
				],
			]
		);

		// Bulk remove all tags from images
		register_rest_route(
			'imagely/v1',
			'/images/bulk-remove-tags',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'bulk_remove_tags' ],
				'permission_callback' => [ self::class, 'check_edit_permission' ],
				'args'                => [
					'image_ids' => [
						'required'    => true,
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
						'description' => 'Array of image IDs to remove all tags from',
					],
				],
			]
		);
	}

	/**
	 * Check if user has permission to edit a specific image (single-image operation routes).
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

		return ImageREST::current_user_can_manage_image( $image_id );
	}

	/**
	 * Check if user has permission to edit images
	 *
	 * @param \WP_REST_Request|null $request Optional REST request — when supplied with an `id`, per-image ownership is enforced.
	 * @return bool
	 */
	public static function check_edit_permission( $request = null ) {
		if ( ! Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			return false;
		}

		if ( null === $request ) {
			return true;
		}

		$image_id = $request->get_param( 'id' );
		if ( $image_id ) {
			return self::current_user_owns_image( (int) $image_id );
		}

		// Bulk endpoints accept arrays of image ids; per-row ownership is enforced inside the handler.
		return true;
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
	public static function current_user_owns_image( $image_id ) {
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
	 * Create a new thumbnail with custom cropping
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function create_new_thumbnail( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$x        = $request->get_param( 'x' );
		$y        = $request->get_param( 'y' );
		$w        = $request->get_param( 'w' );
		$h        = $request->get_param( 'h' );
		$rr       = $request->get_param( 'rr' );

		try {
			$storage = StorageManager::get_instance();

			// Calculate the crop frame using the same logic as createNewThumb
			$x = round( $x * $rr, 0 );
			$y = round( $y * $rr, 0 );
			$w = round( $w * $rr, 0 );
			$h = round( $h * $rr, 0 );

			$crop_frame = [
				'x'      => $x,
				'y'      => $y,
				'width'  => $w,
				'height' => $h,
			];

			// Use the same parameters as createNewThumb
			$params = [
				'watermark'  => false,
				'reflection' => false,
				'crop'       => true,
				'crop_frame' => $crop_frame,
			];

			$result = $storage->generate_thumbnail( $image_id, $params );

			if ( $result ) {
				$storage->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Thumbnail created successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'thumbnail_creation_failed',
				__( 'Failed to create thumbnail', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'thumbnail_creation_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Rotate an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function rotate_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$rotation = $request->get_param( 'rotation' );

		try {
			$result = false;
			switch ( $rotation ) {
				case 'cw':
					$result = \nggAdmin::rotate_image( $image_id, 'CW' );
					break;
				case 'ccw':
					$result = \nggAdmin::rotate_image( $image_id, 'CCW' );
					break;
				case 'fv':
					// Note: H/V have been inverted here to make it more intuitive
					$result = \nggAdmin::rotate_image( $image_id, 0, 'H' );
					break;
				case 'fh':
					// Note: H/V have been inverted here to make it more intuitive
					$result = \nggAdmin::rotate_image( $image_id, 0, 'V' );
					break;
			}

			if ( $result ) {
				\nggAdmin::create_thumbnail( $image_id );

				$storage = StorageManager::get_instance();
				$storage->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Image rotated successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'rotation_failed',
				__( 'Failed to rotate image', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'rotation_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Create a thumbnail for an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function create_thumbnail( WP_REST_Request $request ) {
		$image_id      = $request->get_param( 'id' );
		$width         = $request->get_param( 'width' );
		$height        = $request->get_param( 'height' );
		$fix_dimension = $request->get_param( 'fix_dimension' );

		// Dimensions go straight to the generator: round-tripping them through the
		// site-wide options would write a setting the options capability governs.
		// A request that omits fix_dimension leaves crop null so the generator keeps
		// using the saved setting, rather than being told not to crop.
		$crop = ( null === $fix_dimension ) ? null : (bool) $fix_dimension;

		try {
			$result = \nggAdmin::create_thumbnail( $image_id, (int) $width, (int) $height, $crop );

			// The generator reports failure by returning its error message, which is
			// truthy, so the success marker has to be compared exactly.
			if ( '1' === $result ) {
				// Matches the sibling operations in this class: the thumbnail is rebuilt
				// under thumbs/, and the dynamic derivatives keep serving the old size
				// until the gallery's cache is dropped.
				StorageManager::get_instance()->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Thumbnail created successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'thumbnail_creation_failed',
				is_string( $result ) ? $result : __( 'Failed to create thumbnail', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'thumbnail_creation_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Resize an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function resize_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );

		try {
			$result = \nggAdmin::resize_image( $image_id );

			// resize_image() returns '1' on success and a translated error string on failure.
			if ( '1' === $result ) {
				$storage = StorageManager::get_instance();
				$storage->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Image resized successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'resize_failed',
				is_string( $result ) && '' !== $result ? $result : __( 'Failed to resize image', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'resize_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Set watermark for an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function set_watermark( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );

		try {
			$result = \nggAdmin::set_watermark( $image_id );

			// set_watermark() returns '1' on success and an error message otherwise; the
			// message is a non-empty string, so a truthiness check reported every failure
			// as "applied successfully" (#1104).
			if ( '1' === $result ) {
				$storage = StorageManager::get_instance();
				$storage->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Watermark applied successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'watermark_failed',
				is_string( $result ) && '' !== $result ? $result : __( 'Failed to apply watermark', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'watermark_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Recover an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function recover_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );

		try {
			$result = \nggAdmin::recover_image( $image_id );

			if ( $result ) {
				$storage = StorageManager::get_instance();
				$storage->flush_image_cache( $image_id );

				return new WP_REST_Response(
					[
						'message' => __( 'Image recovered successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'recovery_failed',
				__( 'Failed to recover image', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'recovery_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Import metadata for an image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function import_metadata( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );

		try {
			$result = \nggAdmin::import_MetaData( $image_id );

			if ( $result ) {
				return new WP_REST_Response(
					[
						'message' => __( 'Metadata imported successfully', 'nggallery' ),
					]
				);
			}

			return new WP_Error(
				'metadata_import_failed',
				__( 'Failed to import metadata', 'nggallery' ),
				[ 'status' => 500 ]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'metadata_import_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Strip orientation tag from image
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function strip_orientation_tag( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$storage  = StorageManager::get_instance();

		try {
			$image_path = $storage->get_image_abspath( $image_id );
			// The untouched original is the better metadata source, and since #1107 it no longer
			// sits beside the served file - resolve_backup_abspath() finds it in either location.
			$backup_path = $storage->resolve_backup_abspath( $image_id );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$exif_abspath = ( $backup_path && @file_exists( $backup_path ) ) ? $backup_path : $image_path;
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$exif_iptc = @EXIFWriter::read_metadata( $exif_abspath );

			foreach ( $storage->get_image_sizes( $image_id ) as $size ) {
				if ( 'backup' === $size ) {
					continue;
				}
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@EXIFWriter::write_metadata( $storage->get_image_abspath( $image_id, $size ), $exif_iptc );
			}

			return new WP_REST_Response(
				[
					'message' => __( 'Orientation tag stripped successfully', 'nggallery' ),
				]
			);
		} catch ( \Exception $e ) {
			return new WP_Error(
				'strip_orientation_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Bulk resize multiple images
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_resize_images( WP_REST_Request $request ) {
		$image_ids = $request->get_param( 'image_ids' );
		$width     = $request->get_param( 'width' );
		$height    = $request->get_param( 'height' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Initialize counters
		$successful_resizes = [];
		$failed_resizes     = [];
		$settings           = \Imagely\NGG\Settings\Settings::get_instance();

		// Use provided dimensions or fall back to global settings
		$resize_width  = $width > 0 ? $width : $settings->get( 'imgWidth' );
		$resize_height = $height > 0 ? $height : $settings->get( 'imgHeight' );

		// Dimensions are passed to each resize call below, so this handler never writes
		// them to the site-wide options and no snapshot has to survive a mid-loop fatal.
		try {
			// Process each image
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_resizes[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_resizes[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to edit this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image_mapper = ImageMapper::get_instance();
				$image        = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_resizes[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage this image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_resizes[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				// Attempt to resize the image using the legacy method
				$result = \nggAdmin::resize_image( $image_id, $resize_width, $resize_height );

				if ( '1' === $result ) {
					$storage = StorageManager::get_instance();
					$storage->flush_image_cache( $image_id );

					$successful_resizes[] = [
						'id'      => $image_id,
						'width'   => $resize_width,
						'height'  => $resize_height,
						'message' => __( 'Image resized successfully', 'nggallery' ),
					];
				} else {
					$failed_resizes[] = [
						'id'    => $image_id,
						'error' => is_string( $result ) ? $result : __( 'Failed to resize image', 'nggallery' ),
					];
				}
			}

			// Prepare response
			$total_processed = count( $successful_resizes ) + count( $failed_resizes );
			$response_data   = [
				'processed'          => $total_processed,
				'successful_resizes' => $successful_resizes,
				'failed_resizes'     => $failed_resizes,
				'success_count'      => count( $successful_resizes ),
				'failure_count'      => count( $failed_resizes ),
				'dimensions'         => [
					'width'  => $resize_width,
					'height' => $resize_height,
				],
				'message'            => sprintf(
					// translators: %1$d is the number of successful resizes, %2$d is the total number of images processed.
					__( 'Bulk resize completed: %1$d of %2$d images resized successfully', 'nggallery' ),
					count( $successful_resizes ),
					$total_processed
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_resizes ) > 0 && count( $successful_resizes ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_resizes ) > 0 && 0 === count( $successful_resizes ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_resize_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk resize operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Crop an image and regenerate all sizes
	 *
	 * The full-size file is cropped in place, the way rotate and resize change it, and every other
	 * size is then rebuilt from the cropped file. When the crop fails the full-size file is put back
	 * exactly as this request found it, from a snapshot taken just before the crop. The upload
	 * backup is never used for that: it holds the image as uploaded, so restoring it would also
	 * undo every rotate, resize or watermark applied since (#1179).
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function crop_image( WP_REST_Request $request ) {
		$image_id = $request->get_param( 'id' );
		$x        = $request->get_param( 'x' );
		$y        = $request->get_param( 'y' );
		$w        = $request->get_param( 'w' );
		$h        = $request->get_param( 'h' );
		$rr       = $request->get_param( 'rr' );

		$image         = null;
		$meta_before   = null;
		$original_path = null;
		$snapshot_path = null;
		$cropped       = false;

		try {
			$storage = StorageManager::get_instance();
			$image   = ImageMapper::get_instance()->find( $image_id );

			if ( ! $image ) {
				return new WP_Error(
					'image_not_found',
					__( 'Image not found', 'nggallery' ),
					[ 'status' => 404 ]
				);
			}

			// Get the original image path
			$original_path = $storage->get_image_abspath( $image_id );
			if ( ! $original_path || ! file_exists( $original_path ) ) {
				return new WP_Error(
					'original_not_found',
					__( 'Original image file not found', 'nggallery' ),
					[ 'status' => 404 ]
				);
			}

			$dimensions = @getimagesize( $original_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! $dimensions ) {
				return new WP_Error(
					'crop_failed',
					__( 'The image file could not be read', 'nggallery' ),
					[ 'status' => 500 ]
				);
			}

			// Calculate the crop frame using the resize ratio
			$x = (int) round( $x * $rr, 0 );
			$y = (int) round( $y * $rr, 0 );
			$w = (int) round( $w * $rr, 0 );
			$h = (int) round( $h * $rr, 0 );

			// A frame that starts off the image, or has no area, has nothing to crop, and a zero width
			// or height divides by zero in the generator. One that runs past the far edge (a rounding
			// overshoot from the resize ratio, typically) is trimmed to the edge.
			if ( $x < 0 || $y < 0 || $x >= $dimensions[0] || $y >= $dimensions[1] ) {
				return self::invalid_crop_frame_error();
			}

			$w = min( $w, $dimensions[0] - $x );
			$h = min( $h, $dimensions[1] - $y );

			if ( $w < 1 || $h < 1 ) {
				return self::invalid_crop_frame_error();
			}

			// The generator scales the frame by its width over final_width; equal values crop the
			// frame as given, and the output is the frame's own size.
			$crop_frame = [
				'x'            => $x,
				'y'            => $y,
				'width'        => $w,
				'height'       => $h,
				'final_width'  => $w,
				'final_height' => $h,
			];

			// Back the original up before cropping over it. This goes to the originals root
			// outside the web root, not beside the served image: written there it would be an
			// anonymously fetchable copy of the uncropped master (#1107).
			//
			// Fail closed, unlike the other backup sites. backup_image() and
			// generate_image_size() lose nothing by skipping a backup, but this endpoint crops
			// over the full-size image, so proceeding without one destroys the only copy of the
			// user's master. get_backup_write_abspath() returns null when the originals directory
			// cannot be created, which is a real outcome on a host that only makes uploads
			// writable, and the restore paths below cannot help once the source is gone.
			$backup_path = $storage->resolve_backup_abspath( $image_id );

			if ( ! $backup_path || ! file_exists( $backup_path ) ) {
				$backup_path = $storage->get_backup_write_abspath( $image_id );

				// Whether anything was already at the write target, captured immediately before
				// the copy: a copy that fails part-way leaves a truncated file there, and
				// resolve_backup_abspath() would hand that back on the next attempt as though it
				// were a real original. The crop would then proceed over the master and the
				// restore below would put the truncated file back.
				$backup_pre_existed = $backup_path && file_exists( $backup_path );

				if ( ! $backup_path || ! copy( $original_path, $backup_path ) ) {
					// Only our own part-written file is removed. A backup that was already there
					// is somebody's real original and must never be touched on a failure path.
					if ( $backup_path && ! $backup_pre_existed ) {
						@unlink( $backup_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
					}

					return new WP_Error(
						'backup_failed',
						__( 'The original image could not be backed up, so it was not cropped. Check that the uploads directory is writable.', 'nggallery' ),
						[ 'status' => 500 ]
					);
				}

				$storage->discard_legacy_backup( $image_id );
			}

			// The state to return to if the crop fails. It lives in the system temp directory rather
			// than beside the image, so it is never served from the gallery directory.
			if ( ! function_exists( 'wp_tempnam' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$snapshot_path = wp_tempnam( 'ngg-crop-' . $image_id );
			if ( ! $snapshot_path || ! copy( $original_path, $snapshot_path ) ) {
				return new WP_Error(
					'crop_failed',
					__( 'The image could not be prepared for cropping. Check that the temporary directory is writable.', 'nggallery' ),
					[ 'status' => 500 ]
				);
			}

			// Crop the original image
			$params = [
				'watermark'  => false,
				'reflection' => false,
				'crop'       => true,
				'crop_frame' => $crop_frame,
				'width'      => $w,
				'height'     => $h,
			];

			// Crop and save the original
			$meta_before = $image->meta_data;
			$result      = $storage->generate_image_size( $image, 'full', $params );
			if ( ! $result ) {
				self::restore_crop_snapshot( $snapshot_path, $original_path, $image, $meta_before );
				return new WP_Error(
					'crop_failed',
					__( 'Failed to crop image', 'nggallery' ),
					[ 'status' => 500 ]
				);
			}
			if ( is_object( $result ) ) {
				$result->destruct();
			}
			$cropped = true;

			// Every stored crop frame, including the one just recorded for the full size, is in the
			// coordinates of the uncropped image. Left in place, the next thumbnail rebuild would cut
			// that old area out of the new, smaller file.
			if ( is_array( $image->meta_data ) ) {
				$frames_cleared = false;
				foreach ( $image->meta_data as $size => $size_meta ) {
					if ( is_array( $size_meta ) && isset( $size_meta['crop_frame'] ) ) {
						unset( $image->meta_data[ $size ]['crop_frame'] );
						$frames_cleared = true;
					}
				}
				if ( $frames_cleared ) {
					ImageMapper::get_instance()->save( $image );
				}
			}

			// Regenerate every other size from the cropped file, each with its own settings.
			foreach ( $storage->get_image_sizes( $image ) as $size ) {
				if ( 'full' === $size || 'backup' === $size ) {
					continue;
				}
				$sized = $storage->generate_image_size( $image, $size );
				if ( is_object( $sized ) ) {
					$sized->destruct();
				}
			}

			$storage->flush_image_cache( $image_id );

			return new WP_REST_Response(
				[
					'message' => __( 'Image cropped and all sizes regenerated successfully', 'nggallery' ),
				]
			);
		} catch ( \Throwable $e ) {
			// Once the crop is saved the full-size file and its stored dimensions agree, so a failure
			// while rebuilding the other sizes must not put the uncropped file back under them.
			if ( ! $cropped ) {
				self::restore_crop_snapshot( $snapshot_path, $original_path, $image, $meta_before );
			}
			return new WP_Error(
				'crop_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
		} finally {
			if ( $snapshot_path && file_exists( $snapshot_path ) ) {
				wp_delete_file( $snapshot_path );
			}
		}
	}

	/**
	 * The error for a crop frame that does not select part of the image.
	 *
	 * @return WP_Error
	 */
	private static function invalid_crop_frame_error() {
		return new WP_Error(
			'invalid_crop_frame',
			__( 'The crop area must have a width and height and start inside the image.', 'nggallery' ),
			[ 'status' => 400 ]
		);
	}

	/**
	 * Put the full-size file, and the dimensions stored for it, back as they were before the crop.
	 *
	 * The generator saves the new dimensions before its ngg_generated_image action runs, so a failure
	 * raised from that action would otherwise leave the restored file described as the cropped one.
	 *
	 * @param string|null       $snapshot_path Copy of the full-size file taken before the crop.
	 * @param string|null       $original_path The full-size file.
	 * @param Image|object|null $image         The image being cropped.
	 * @param array|null        $meta_before   Its meta_data before the crop.
	 */
	private static function restore_crop_snapshot( $snapshot_path, $original_path, $image, $meta_before ) {
		if ( ! $snapshot_path || ! $original_path || ! file_exists( $snapshot_path ) ) {
			return;
		}

		copy( $snapshot_path, $original_path );
		clearstatcache( true, $original_path );

		if ( $image && null !== $meta_before && $image->meta_data !== $meta_before ) {
			$image->meta_data = $meta_before;
			ImageMapper::get_instance()->save( $image );
		}
	}

	/**
	 * Bulk import metadata for multiple images
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_import_metadata( WP_REST_Request $request ) {
		$image_ids = $request->get_param( 'image_ids' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		try {
			// Initialize counters
			$successful_imports = [];
			$failed_imports     = [];

			// Process each image
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_imports[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_imports[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to edit this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image_mapper = ImageMapper::get_instance();
				$image        = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_imports[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage this image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_imports[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				// Attempt to import metadata
				$result = \nggAdmin::import_MetaData( $image_id );

				if ( $result ) {
					$successful_imports[] = [
						'id'      => $image_id,
						'message' => __( 'Metadata imported successfully', 'nggallery' ),
					];
				} else {
					$failed_imports[] = [
						'id'    => $image_id,
						'error' => __( 'Failed to import metadata', 'nggallery' ),
					];
				}
			}

			// Prepare response
			$total_processed = count( $successful_imports ) + count( $failed_imports );
			$response_data   = [
				'processed'          => $total_processed,
				'successful_imports' => $successful_imports,
				'failed_imports'     => $failed_imports,
				'success_count'      => count( $successful_imports ),
				'failure_count'      => count( $failed_imports ),
				'message'            => sprintf(
					// translators: %1$d is the number of successful imports, %2$d is the total number of images processed.
					__( 'Bulk import completed: %1$d of %2$d images metadata imported successfully', 'nggallery' ),
					count( $successful_imports ),
					$total_processed
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_imports ) > 0 && count( $successful_imports ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_imports ) > 0 && 0 === count( $successful_imports ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_import_metadata_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk import metadata operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Bulk copy images to gallery
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_copy_images( WP_REST_Request $request ) {
		$image_ids              = $request->get_param( 'image_ids' );
		$destination_gallery_id = $request->get_param( 'destination_gallery_id' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Verify destination gallery exists
		$gallery_mapper      = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$destination_gallery = $gallery_mapper->find( $destination_gallery_id );

		if ( ! $destination_gallery ) {
			return new WP_Error(
				'gallery_not_found',
				__( 'Destination gallery not found', 'nggallery' ),
				[ 'status' => 404 ]
			);
		}

		// Verify user can manage the destination gallery.
		$can_manage_dest = ImageREST::current_user_can_manage_gallery( $destination_gallery_id );
		if ( is_wp_error( $can_manage_dest ) || ! $can_manage_dest ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to add images to this gallery.', 'nggallery' ), [ 'status' => 403 ] );
		}

		try {
			// Initialize counters
			$successful_copies = [];
			$failed_copies     = [];
			$storage           = StorageManager::get_instance();
			$image_mapper      = ImageMapper::get_instance();

			// Validate all images exist first
			$valid_images = [];
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_copies[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_copies[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to access this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_copies[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage the source image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_copies[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				$valid_images[] = $image;
			}

			// Copy all valid images at once using the storage manager
			if ( ! empty( $valid_images ) ) {
				// Keyed by source image id, not position - a skipped source shifts nothing.
				$copied_image_ids = $storage->copy_images( $valid_images, $destination_gallery_id );

				// Mark successful copies
				foreach ( $valid_images as $image ) {
					if ( isset( $copied_image_ids[ $image->pid ] ) && $copied_image_ids[ $image->pid ] > 0 ) {
						$successful_copies[] = [
							'original_id' => $image->pid,
							'new_id'      => $copied_image_ids[ $image->pid ],
							'message'     => __( 'Image copied successfully', 'nggallery' ),
						];
					} else {
						$failed_copies[] = [
							'id'    => $image->pid,
							'error' => __( 'Failed to copy image', 'nggallery' ),
						];
					}
				}
			}

			// Prepare response
			$total_processed = count( $successful_copies ) + count( $failed_copies );
			$response_data   = [
				'processed'                 => $total_processed,
				'successful_copies'         => $successful_copies,
				'failed_copies'             => $failed_copies,
				'success_count'             => count( $successful_copies ),
				'failure_count'             => count( $failed_copies ),
				'destination_gallery_id'    => $destination_gallery_id,
				'destination_gallery_title' => $destination_gallery->title,
				'message'                   => sprintf(
					// translators: %1$d is the number of successful copies, %2$d is the total number of images processed, %3$s is the destination gallery title.
					__( 'Bulk copy completed: %1$d of %2$d images copied successfully to "%3$s"', 'nggallery' ),
					count( $successful_copies ),
					$total_processed,
					$destination_gallery->title
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_copies ) > 0 && count( $successful_copies ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_copies ) > 0 && 0 === count( $successful_copies ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_copy_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk copy operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Bulk move images to gallery
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_move_images( WP_REST_Request $request ) {
		$image_ids              = $request->get_param( 'image_ids' );
		$destination_gallery_id = $request->get_param( 'destination_gallery_id' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Verify destination gallery exists
		$gallery_mapper      = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$destination_gallery = $gallery_mapper->find( $destination_gallery_id );

		if ( ! $destination_gallery ) {
			return new WP_Error(
				'gallery_not_found',
				__( 'Destination gallery not found', 'nggallery' ),
				[ 'status' => 404 ]
			);
		}

		// Verify user can manage the destination gallery.
		$can_manage_dest = ImageREST::current_user_can_manage_gallery( $destination_gallery_id );
		if ( is_wp_error( $can_manage_dest ) || ! $can_manage_dest ) {
			return new WP_Error( 'rest_forbidden', __( 'You do not have permission to add images to this gallery.', 'nggallery' ), [ 'status' => 403 ] );
		}

		try {
			// Initialize counters
			$successful_moves = [];
			$failed_moves     = [];
			$storage          = StorageManager::get_instance();
			$image_mapper     = ImageMapper::get_instance();

			// Validate all images exist first
			$valid_images = [];
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_moves[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_moves[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to move this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_moves[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage the source image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_moves[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				$valid_images[] = $image;
			}

			// Move all valid images at once using the storage manager
			if ( ! empty( $valid_images ) ) {
				// Keyed by source image id, not position - a skipped source shifts nothing.
				$moved_image_ids = $storage->move_images( $valid_images, $destination_gallery_id );

				// Mark successful moves
				foreach ( $valid_images as $image ) {
					if ( isset( $moved_image_ids[ $image->pid ] ) && $moved_image_ids[ $image->pid ] > 0 ) {
						$successful_moves[] = [
							'original_id' => $image->pid,
							'new_id'      => $moved_image_ids[ $image->pid ],
							'message'     => __( 'Image moved successfully', 'nggallery' ),
						];
					} else {
						$failed_moves[] = [
							'id'    => $image->pid,
							'error' => __( 'Failed to move image', 'nggallery' ),
						];
					}
				}
			}

			// Prepare response
			$total_processed = count( $successful_moves ) + count( $failed_moves );
			$response_data   = [
				'processed'                 => $total_processed,
				'successful_moves'          => $successful_moves,
				'failed_moves'              => $failed_moves,
				'success_count'             => count( $successful_moves ),
				'failure_count'             => count( $failed_moves ),
				'destination_gallery_id'    => $destination_gallery_id,
				'destination_gallery_title' => $destination_gallery->title,
				'message'                   => sprintf(
					// translators: %1$d is the number of successful moves, %2$d is the total number of images processed, %3$s is the destination gallery title.
					__( 'Bulk move completed: %1$d of %2$d images moved successfully to "%3$s"', 'nggallery' ),
					count( $successful_moves ),
					$total_processed,
					$destination_gallery->title
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_moves ) > 0 && count( $successful_moves ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_moves ) > 0 && 0 === count( $successful_moves ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_move_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk move operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Bulk add tags to images
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_add_tags( WP_REST_Request $request ) {
		$image_ids = $request->get_param( 'image_ids' );
		$tags      = $request->get_param( 'tags' );
		$append    = $request->get_param( 'append' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		if ( empty( $tags ) || ! is_array( $tags ) ) {
			return new WP_Error(
				'invalid_tags',
				__( 'Invalid or empty tags array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		// Force each tag to a sanitized scalar string; blocks array/object injection into wp_set_object_terms and strips control chars.
		$tags = array_values(
			array_filter(
				array_map(
					static function ( $t ) {
						return is_scalar( $t ) ? sanitize_text_field( (string) $t ) : '';
					},
					$tags
				),
				static function ( $t ) {
					return '' !== $t;
				}
			)
		);
		if ( empty( $tags ) ) {
			return new WP_Error(
				'invalid_tags',
				__( 'Invalid or empty tags array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		try {
			// Initialize counters
			$successful_additions = [];
			$failed_additions     = [];

			// Process each image
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_additions[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_additions[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to edit this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image_mapper = ImageMapper::get_instance();
				$image        = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_additions[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage this image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_additions[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				// Attempt to add tags to the image
				$result = wp_set_object_terms( $image_id, $tags, 'ngg_tag', $append );

				if ( ! is_wp_error( $result ) ) {
					$successful_additions[] = [
						'id'      => $image_id,
						'tags'    => $tags,
						'message' => __( 'Tags added successfully', 'nggallery' ),
					];
				} else {
					$failed_additions[] = [
						'id'    => $image_id,
						'error' => $result->get_error_message(),
					];
				}
			}

			// Prepare response
			$total_processed = count( $successful_additions ) + count( $failed_additions );
			$response_data   = [
				'processed'            => $total_processed,
				'successful_additions' => $successful_additions,
				'failed_additions'     => $failed_additions,
				'success_count'        => count( $successful_additions ),
				'failure_count'        => count( $failed_additions ),
				'message'              => sprintf(
					// translators: %1$d is the number of successful additions, %2$d is the total number of images processed.
					__( 'Bulk add tags completed: %1$d of %2$d images tags added successfully', 'nggallery' ),
					count( $successful_additions ),
					$total_processed
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_additions ) > 0 && count( $successful_additions ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_additions ) > 0 && 0 === count( $successful_additions ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_add_tags_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk add tags operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * Bulk remove all tags from images
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function bulk_remove_tags( WP_REST_Request $request ) {
		$image_ids = $request->get_param( 'image_ids' );

		if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
			return new WP_Error(
				'invalid_image_ids',
				__( 'Invalid or empty image IDs array', 'nggallery' ),
				[ 'status' => 400 ]
			);
		}

		try {
			// Initialize counters
			$successful_removals = [];
			$failed_removals     = [];

			// Process each image
			foreach ( $image_ids as $image_id ) {
				$image_id = absint( $image_id );

				if ( 0 === $image_id ) {
					$failed_removals[] = [
						'id'    => $image_id,
						'error' => __( 'Invalid image ID', 'nggallery' ),
					];
					continue;
				}

				if ( ! self::current_user_owns_image( $image_id ) ) {
					$failed_removals[] = [
						'id'    => $image_id,
						'error' => __( 'You are not allowed to edit this image', 'nggallery' ),
					];
					continue;
				}

				// Verify image exists
				$image_mapper = ImageMapper::get_instance();
				$image        = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_removals[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				// Verify user can manage this image.
				$can_manage = ImageREST::current_user_can_manage_image( $image_id );
				if ( is_wp_error( $can_manage ) || ! $can_manage ) {
					$failed_removals[] = [
						'id'    => $image_id,
						'error' => __( 'Permission denied', 'nggallery' ),
					];
					continue;
				}

				// Attempt to remove all tags from the image
				$result = wp_delete_object_term_relationships( $image_id, 'ngg_tag' );

				if ( false !== $result && ! is_wp_error( $result ) ) {
					$successful_removals[] = [
						'id'      => $image_id,
						'message' => __( 'All tags removed successfully', 'nggallery' ),
					];
				} else {
					$error_message     = is_wp_error( $result ) ? $result->get_error_message() : __( 'Failed to remove tags', 'nggallery' );
					$failed_removals[] = [
						'id'    => $image_id,
						'error' => $error_message,
					];
				}
			}

			// Prepare response
			$total_processed = count( $successful_removals ) + count( $failed_removals );
			$response_data   = [
				'processed'           => $total_processed,
				'successful_removals' => $successful_removals,
				'failed_removals'     => $failed_removals,
				'success_count'       => count( $successful_removals ),
				'failure_count'       => count( $failed_removals ),
				'message'             => sprintf(
					// translators: %1$d is the number of successful removals, %2$d is the total number of images processed.
					__( 'Bulk remove tags completed: %1$d of %2$d images tags removed successfully', 'nggallery' ),
					count( $successful_removals ),
					$total_processed
				),
			];

			// Determine response status
			$status = 200;
			if ( count( $failed_removals ) > 0 && count( $successful_removals ) > 0 ) {
				$status = 207; // Multi-status (partial success)
			} elseif ( count( $failed_removals ) > 0 && 0 === count( $successful_removals ) ) {
				$status = 500; // Complete failure
			}

			return new WP_REST_Response( $response_data, $status );

		} catch ( \Exception $e ) {
			return new WP_Error(
				'bulk_remove_tags_failed',
				sprintf(
					// translators: %s is the error message.
					__( 'Bulk remove tags operation failed: %s', 'nggallery' ),
					$e->getMessage()
				),
				[ 'status' => 500 ]
			);
		}
	}
}
