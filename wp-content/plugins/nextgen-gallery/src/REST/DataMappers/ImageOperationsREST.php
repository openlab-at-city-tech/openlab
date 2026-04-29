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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
						'default'     => false,
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

		// Set watermark
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/set-watermark',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'set_watermark' ],
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

		// Recover image
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/recover',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'recover_image' ],
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

		// Import metadata
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/import-metadata',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'import_metadata' ],
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

		// Strip orientation tag
		register_rest_route(
			'imagely/v1',
			'/images/(?P<id>\d+)/strip-orientation',
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'strip_orientation_tag' ],
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
				'permission_callback' => [ self::class, 'check_edit_permission' ],
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
	 * Check if user has permission to edit images
	 *
	 * @return bool
	 */
	public static function check_edit_permission() {
		return Security::is_allowed( 'NextGEN Manage gallery' );
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

		try {
			// If custom parameters are provided, temporarily update the global settings
			$original_options = null;
			if ( $width || $height || $fix_dimension !== null ) {
				$ngg_options      = get_option( 'ngg_options', [] );
				$original_options = $ngg_options;

				if ( $width ) {
					$ngg_options['thumbwidth'] = $width;
				}
				if ( $height ) {
					$ngg_options['thumbheight'] = $height;
				}
				// Always set fix_dimension if it's provided (even if false)
				if ( $fix_dimension !== null ) {
					$ngg_options['thumbfix'] = $fix_dimension ? 1 : 0;
				}

				update_option( 'ngg_options', $ngg_options );
			}

			$result = \nggAdmin::create_thumbnail( $image_id );

			// Restore original settings if they were modified
			if ( $original_options !== null ) {
				update_option( 'ngg_options', $original_options );
			}

			if ( $result ) {
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
			// Restore original settings in case of exception
			if ( isset( $original_options ) && $original_options !== null ) {
				update_option( 'ngg_options', $original_options );
			}

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

			if ( $result ) {
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
				__( 'Failed to resize image', 'nggallery' ),
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

			if ( $result ) {
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
				__( 'Failed to apply watermark', 'nggallery' ),
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
			$image_path  = $storage->get_image_abspath( $image_id );
			$backup_path = $image_path . '_backup';
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_path;
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

		// TODO This is temp
		// Update global settings if custom dimensions were provided
		if ( $width > 0 || $height > 0 ) {
			$ngg_options = get_option( 'ngg_options', [] );
			if ( $width > 0 ) {
				$ngg_options['imgWidth'] = $resize_width;
			}
			if ( $height > 0 ) {
				$ngg_options['imgHeight'] = $resize_height;
			}
			update_option( 'ngg_options', $ngg_options );
		}

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

			// Calculate the crop frame using the resize ratio
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

			// Get the original image path
			$original_path = $storage->get_image_abspath( $image_id );
			if ( ! file_exists( $original_path ) ) {
				return new WP_Error(
					'original_not_found',
					__( 'Original image file not found', 'nggallery' ),
					[ 'status' => 404 ]
				);
			}

			// Create a backup of the original
			$backup_path = $original_path . '_backup';
			if ( ! file_exists( $backup_path ) ) {
				copy( $original_path, $backup_path );
			}

			// Crop the original image
			$params = [
				'watermark'  => false,
				'reflection' => false,
				'crop'       => true,
				'crop_frame' => $crop_frame,
			];

			// Crop and save the original
			$result = $storage->generate_image( $image_id, $params );
			if ( ! $result ) {
				// Restore from backup if crop fails
				if ( file_exists( $backup_path ) ) {
					copy( $backup_path, $original_path );
				}
				return new WP_Error(
					'crop_failed',
					__( 'Failed to crop image', 'nggallery' ),
					[ 'status' => 500 ]
				);
			}

			// Regenerate all sizes
			$sizes = $storage->get_image_sizes( $image_id );
			foreach ( $sizes as $size ) {
				if ( 'backup' === $size ) {
					continue;
				}
				$storage->generate_image( $image_id, $params, $size );
			}

			$storage->flush_image_cache( $image_id );

			return new WP_REST_Response(
				[
					'message' => __( 'Image cropped and all sizes regenerated successfully', 'nggallery' ),
				]
			);
		} catch ( \Exception $e ) {
			// Restore from backup if any error occurs
			if ( isset( $backup_path ) && file_exists( $backup_path ) ) {
				copy( $backup_path, $original_path );
			}
			return new WP_Error(
				'crop_failed',
				$e->getMessage(),
				[ 'status' => 500 ]
			);
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

				// Verify image exists
				$image = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_copies[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				$valid_images[] = $image;
			}

			// Copy all valid images at once using the storage manager
			if ( ! empty( $valid_images ) ) {
				$copied_image_ids = $storage->copy_images( $valid_images, $destination_gallery_id );

				// Mark successful copies
				foreach ( $valid_images as $index => $image ) {
					if ( isset( $copied_image_ids[ $index ] ) && $copied_image_ids[ $index ] > 0 ) {
						$successful_copies[] = [
							'original_id' => $image->pid,
							'new_id'      => $copied_image_ids[ $index ],
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

				// Verify image exists
				$image = $image_mapper->find( $image_id );

				if ( ! $image ) {
					$failed_moves[] = [
						'id'    => $image_id,
						'error' => __( 'Image not found', 'nggallery' ),
					];
					continue;
				}

				$valid_images[] = $image;
			}

			// Move all valid images at once using the storage manager
			if ( ! empty( $valid_images ) ) {
				$moved_image_ids = $storage->move_images( $valid_images, $destination_gallery_id );

				// Mark successful moves
				foreach ( $valid_images as $index => $image ) {
					if ( isset( $moved_image_ids[ $index ] ) && $moved_image_ids[ $index ] > 0 ) {
						$successful_moves[] = [
							'original_id' => $image->pid,
							'new_id'      => $moved_image_ids[ $index ],
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
