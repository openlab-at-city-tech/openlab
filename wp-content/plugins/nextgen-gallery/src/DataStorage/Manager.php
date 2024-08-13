<?php

namespace Imagely\NGG\DataStorage;

use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;

use Imagely\NGG\DataTypes\{ Gallery, Image, LegacyThumbnail };
use Imagely\NGG\Display\I18N;
use Imagely\NGG\IGW\EventPublisher;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\{ Filesystem, Router, Security };

class Manager {

	public static $instance = null;

	protected $gallery_mapper;
	protected $image_mapper;

	/** @deprecated */
	public $_image_mapper;

	/** @deprecated */
	public $object;

	protected static $gallery_abspath_cache = [];
	protected static $image_abspath_cache   = [];
	protected static $image_url_cache       = [];

	public function __construct() {
		$this->gallery_mapper = GalleryMapper::get_instance();
		$this->image_mapper   = ImageMapper::get_instance();

		/**
		 * @TODO Remove in a later release - this fixes an issue with Imagify at the time of 3.50's release.
		 */
		$this->object        = $this;
		$this->_image_mapper = $this->image_mapper;
	}

	/**
	 * @return Manager
	 */
	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Manager();
		}
		return self::$instance;
	}

	public function has_method( $name ) {
		return method_exists( $this, $name );
	}

	/**
	 * @TODO: Remove this 'magic' method so that our code is always understandable without needing deep context
	 * @param string $method
	 * @param array  $args
	 * @return mixed
	 * @throws \Exception
	 */
	public function __call( $method, $args ) {
		if ( preg_match( '/^get_(\w+)_(abspath|url|dimensions|html|size_params)$/', $method, $match ) ) {
			if ( isset( $match[1] ) && isset( $match[2] ) && ! method_exists( $this, $method ) ) {
				$method = 'get_image_' . $match[2];
				$args[] = $match[1];
				return $this->$method( $args );
			}
		}

		return $this->$method( $args );
	}

	/**
	 * Remove after Pro attains level 1 compatibility with the POPE removal
	 */
	public function get_wrapped_instance() {
		return $this;
	}

	/**
	 * Remove after Pro attains level 1 compatibility with the POPE removal
	 */
	public function add_mixin( $unused = '' ) {}

	/**
	 * Backs up an image file
	 *
	 * @param int|object $image
	 * @param bool       $save
	 * @return bool
	 */
	public function backup_image( $image, $save = true ) {
		$retval     = false;
		$image_path = $this->get_image_abspath( $image );

		if ( $image_path && @file_exists( $image_path ) ) {
			$retval = copy( $image_path, $this->get_backup_abspath( $image ) );

			// Store the dimensions of the image.
			if ( function_exists( 'getimagesize' ) ) {
				$mapper = ImageMapper::get_instance();
				if ( ! is_object( $image ) ) {
					$image = $mapper->find( $image );
				}
				if ( $image ) {
					if ( empty( $image->meta_data ) || ! is_array( $image->meta_data ) ) {
						$image->meta_data = [];
					}
					$dimensions                 = getimagesize( $image_path );
					$image->meta_data['backup'] = [
						'filename'  => basename( $image_path ),
						'width'     => $dimensions[0],
						'height'    => $dimensions[1],
						'generated' => microtime(),
					];
					if ( $save ) {
						$mapper->save( $image );
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * @param string $zipfile
	 * @param string $dest_path
	 * @return bool false on failure
	 */
	public function extract_zip( $zipfile, $dest_path ) {
		wp_mkdir_p( $dest_path );

		if ( class_exists( 'ZipArchive', false ) && apply_filters( 'unzip_file_use_ziparchive', true ) ) {
			$zipObj = new \ZipArchive();
			if ( $zipObj->open( $zipfile ) === false ) {
				return false;
			}

			for ( $i = 0; $i < $zipObj->numFiles; $i++ ) {
				$filename = $zipObj->getNameIndex( $i );
				if ( ! $this->is_allowed_image_extension( $filename ) ) {
					continue;
				}
				$zipObj->extractTo( $dest_path, [ $zipObj->getNameIndex( $i ) ] );
			}
		} else {
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
			$zipObj           = new \PclZip( $zipfile );
			$zipContent       = $zipObj->listContent();
			$indexesToExtract = [];

			foreach ( $zipContent as $zipItem ) {
				if ( $zipItem['folder'] ) {
					continue;
				}
				if ( ! $this->is_allowed_image_extension( $zipItem['stored_filename'] ) ) {
					continue;
				}
				$indexesToExtract[] = $zipItem['index'];
			}

			if ( ! $zipObj->extractByIndex( implode( ',', $indexesToExtract ), $dest_path ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets the id of a gallery, regardless of whether an integer or object was passed as an argument
	 *
	 * @param mixed $gallery_obj_or_id
	 * @return null|int
	 */
	public function get_gallery_id( $gallery_obj_or_id ) {
		$retval      = null;
		$gallery_key = $this->gallery_mapper->get_primary_key_column();

		if ( is_object( $gallery_obj_or_id ) ) {
			if ( isset( $gallery_obj_or_id->$gallery_key ) ) {
				$retval = $gallery_obj_or_id->$gallery_key;
			}
		} elseif ( is_numeric( $gallery_obj_or_id ) ) {
			$retval = $gallery_obj_or_id;
		}

		return $retval;
	}

	/**
	 * Empties the gallery cache directory of content
	 *
	 * @param object $gallery
	 */
	public function flush_cache( $gallery ) {
		$fs = Filesystem::get_instance();
		$fs->flush_directory( $this->get_cache_abspath( $gallery ) );
	}

	/**
	 * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
	 *
	 * @param object|int $image Image ID or an image object
	 * @param string     $size
	 * @param array      $params
	 * @param bool       $skip_defaults
	 * @return bool|array
	 */
	public function calculate_image_size_dimensions( $image, $size, $params = null, $skip_defaults = false ) {
		$retval = false;

		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {
			$params = $this->get_image_size_params( $image, $size, $params, $skip_defaults );

			// Get the image filename.
			$image_path = $this->get_image_abspath( $image, 'full', true );
			$clone_path = $this->get_image_abspath( $image, $size );

			$retval = $this->calculate_image_clone_dimensions( $image_path, $clone_path, $params );
		}

		return $retval;
	}

	/**
	 * Generates a "clone" for an existing image, the clone can be altered using the $params array
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|object
	 */
	public function generate_image_clone( $image_path, $clone_path, $params ) {
		$crop       = isset( $params['crop'] ) ? $params['crop'] : null;
		$watermark  = isset( $params['watermark'] ) ? $params['watermark'] : null;
		$reflection = isset( $params['reflection'] ) ? $params['reflection'] : null;
		$rotation   = isset( $params['rotation'] ) ? $params['rotation'] : null;
		$flip       = isset( $params['flip'] ) ? $params['flip'] : '';
		$destpath   = null;
		$thumbnail  = null;

		$result = $this->calculate_image_clone_result( $image_path, $clone_path, $params );

		// XXX this should maybe be removed and extra settings go into $params?
		$settings = apply_filters( 'ngg_settings_during_image_generation', Settings::get_instance()->to_array() );

		// Ensure we have a valid image.
		if ( $image_path && @file_exists( $image_path ) && null != $result && ! isset( $result['error'] ) ) {
			$image_dir    = dirname( $image_path );
			$clone_path   = $result['clone_path'];
			$clone_dir    = $result['clone_directory'];
			$clone_format = $result['clone_format'];
			$format_list  = $this->get_image_format_list();

			// Ensure target directory exists, but only create 1 subdirectory.
			if ( ! @file_exists( $clone_dir ) ) {
				if ( strtolower( realpath( $image_dir ) ) != strtolower( realpath( $clone_dir ) ) ) {
					if ( strtolower( realpath( $image_dir ) ) == strtolower( realpath( dirname( $clone_dir ) ) ) ) {
						wp_mkdir_p( $clone_dir );
					}
				}
			}

			$method  = $result['method'];
			$width   = $result['width'];
			$height  = $result['height'];
			$quality = $result['quality'];

			if ( null === $quality ) {
				$quality = 100;
			}

			// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
			if ( $method == 'wordpress' ) {
				$original = wp_get_image_editor( $image_path );
				$destpath = $clone_path;
				if ( ! is_wp_error( $original ) ) {
					$original->resize( $width, $height, $crop );
					$original->set_quality( $quality );
					$original->save( $clone_path );
				}
			} elseif ( $method == 'nextgen' ) {
				$destpath  = $clone_path;
				$thumbnail = new LegacyThumbnail( $image_path, true );
				if ( ! $thumbnail->error ) {
					if ( $crop ) {
						$crop_area   = $result['crop_area'];
						$crop_x      = $crop_area['x'];
						$crop_y      = $crop_area['y'];
						$crop_width  = $crop_area['width'];
						$crop_height = $crop_area['height'];

						$thumbnail->crop( $crop_x, $crop_y, $crop_width, $crop_height );
					}

					$thumbnail->resize( $width, $height );
				} else {
					$thumbnail = null;
				}
			}

			// We successfully generated the thumbnail.
			if ( is_string( $destpath ) && ( @file_exists( $destpath ) || $thumbnail != null ) ) {
				if ( $clone_format != null ) {
					if ( isset( $format_list[ $clone_format ] ) ) {
						$clone_format_extension     = $format_list[ $clone_format ];
						$clone_format_extension_str = null;

						if ( $clone_format_extension != null ) {
							$clone_format_extension_str = '.' . $clone_format_extension;
						}

						$destpath_info      = I18N::mb_pathinfo( $destpath );
						$destpath_extension = $destpath_info['extension'];

						if ( strtolower( $destpath_extension ) != strtolower( $clone_format_extension ) ) {
							$destpath_dir      = $destpath_info['dirname'];
							$destpath_basename = $destpath_info['filename'];
							$destpath_new      = $destpath_dir . DIRECTORY_SEPARATOR . $destpath_basename . $clone_format_extension_str;

							if ( ( @file_exists( $destpath ) && rename( $destpath, $destpath_new ) ) || $thumbnail != null ) {
								$destpath = $destpath_new;
							}
						}
					}
				}

				if ( is_null( $thumbnail ) ) {
					$thumbnail = new LegacyThumbnail( $destpath, true );

					if ( $thumbnail->error ) {
						$thumbnail = null;

						return null;
					}
				} else {
					$thumbnail->fileName = $destpath;
				}

				// This is quite odd, when watermark equals int(0) it seems all statements below ($watermark == 'image') and ($watermark == 'text') both evaluate as true
				// so we set it at null if it evaluates to any null-like value.
				if ( null === $watermark ) {
					$watermark = null;
				}

				if ( 1 == $watermark || true === $watermark ) {
					$watermark_setting_keys = [
						'wmFont',
						'wmType',
						'wmPos',
						'wmXpos',
						'wmYpos',
						'wmPath',
						'wmText',
						'wmOpaque',
						'wmFont',
						'wmSize',
						'wmColor',
					];
					foreach ( $watermark_setting_keys as $watermark_key ) {
						if ( ! isset( $params[ $watermark_key ] ) ) {
							$params[ $watermark_key ] = $settings[ $watermark_key ];
						}
					}

					if ( in_array( strval( $params['wmType'] ), [ 'image', 'text' ], true ) ) {
						$watermark = $params['wmType'];
					} else {
						$watermark = 'text';
					}
				}

				$watermark = strval( $watermark );

				if ( $watermark == 'image' ) {
					$thumbnail->watermarkImgPath = $params['wmPath'];
					$thumbnail->watermarkImage( $params['wmPos'], $params['wmXpos'], $params['wmYpos'] );
				} elseif ( $watermark == 'text' ) {
					$thumbnail->watermarkText = $params['wmText'];
					$thumbnail->watermarkCreateText( $params['wmColor'], $params['wmFont'], $params['wmSize'], $params['wmOpaque'] );
					$thumbnail->watermarkImage( $params['wmPos'], $params['wmXpos'], $params['wmYpos'] );
				}

				if ( $rotation && in_array( abs( $rotation ), [ 90, 180, 270 ], true ) ) {
					$thumbnail->rotateImageAngle( $rotation );
				}

				$flip = strtolower( $flip );

				if ( $flip && in_array( $flip, [ 'h', 'v', 'hv' ], true ) ) {
					$flip_h = in_array( $flip, [ 'h', 'hv' ], true );
					$flip_v = in_array( $flip, [ 'v', 'hv' ], true );

					$thumbnail->flipImage( $flip_h, $flip_v );
				}

				if ( $reflection ) {
					$thumbnail->createReflection( 40, 40, 50, false, '#a4a4a4' );
				}

				// Force format.
				if ( $clone_format != null && isset( $format_list[ $clone_format ] ) ) {
					$thumbnail->format = strtoupper( $format_list[ $clone_format ] );
				}

				$thumbnail = apply_filters( 'ngg_before_save_thumbnail', $thumbnail );

				// Always retrieve metadata from the backup when possible.
				$backup_path  = $image_path . '_backup';
				$exif_abspath = @file_exists( $backup_path ) ? $backup_path : $image_path;

				$exif_iptc = EXIFWriter::read_metadata( $exif_abspath );

				$thumbnail->save( $destpath, $quality );

				@EXIFWriter::write_metadata( $destpath, $exif_iptc );
			}
		}

		return $thumbnail;
	}

	/**
	 * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|array
	 */
	public function calculate_image_clone_dimensions( $image_path, $clone_path, $params ) {
		$retval = null;
		$result = $this->calculate_image_clone_result( $image_path, $clone_path, $params );

		if ( $result != null ) {
			$retval = [
				'width'       => $result['width'],
				'height'      => $result['height'],
				'real_width'  => $result['real_width'],
				'real_height' => $result['real_height'],
			];
		}

		return $retval;
	}

	/**
	 * Returns an array of properties of a resulting clone image if and when generated
	 *
	 * @param string $image_path
	 * @param string $clone_path
	 * @param array  $params
	 * @return null|array
	 */
	public function calculate_image_clone_result( $image_path, $clone_path, $params ) {
		$width      = isset( $params['width'] ) ? $params['width'] : null;
		$height     = isset( $params['height'] ) ? $params['height'] : null;
		$quality    = isset( $params['quality'] ) ? $params['quality'] : null;
		$type       = isset( $params['type'] ) ? $params['type'] : null;
		$crop       = isset( $params['crop'] ) ? $params['crop'] : null;
		$watermark  = isset( $params['watermark'] ) ? $params['watermark'] : null;
		$rotation   = isset( $params['rotation'] ) ? $params['rotation'] : null;
		$reflection = isset( $params['reflection'] ) ? $params['reflection'] : null;
		$crop_frame = isset( $params['crop_frame'] ) ? $params['crop_frame'] : null;
		$result     = null;

		// Ensure we have a valid image.
		if ( $image_path && @file_exists( $image_path ) ) {
			// Ensure target directory exists, but only create 1 subdirectory.
			$image_dir           = dirname( $image_path );
			$clone_dir           = dirname( $clone_path );
			$image_extension     = I18N::mb_pathinfo( $image_path, PATHINFO_EXTENSION );
			$image_extension_str = null;
			$clone_extension     = I18N::mb_pathinfo( $clone_path, PATHINFO_EXTENSION );
			$clone_extension_str = null;

			if ( $image_extension != null ) {
				$image_extension_str = '.' . $image_extension;
			}

			if ( $clone_extension != null ) {
				$clone_extension_str = '.' . $clone_extension;
			}

			$image_basename = I18N::mb_basename( $image_path );
			$clone_basename = I18N::mb_basename( $clone_path );
			// We use a default suffix as passing in null as the suffix will make WordPress use a default.
			$clone_suffix = null;
			$format_list  = $this->get_image_format_list();
			$clone_format = null; // format is determined below and based on $type otherwise left to null.

			// suffix is only used to reconstruct paths for image_resize function.
			if ( strpos( $clone_basename, $image_basename ) === 0 ) {
				$clone_suffix = substr( $clone_basename, strlen( $image_basename ) );
			}

			if ( $clone_suffix != null && $clone_suffix[0] == '-' ) {
				// WordPress adds '-' on its own.
				$clone_suffix = substr( $clone_suffix, 1 );
			}

			// Get original image dimensions.
			$dimensions = getimagesize( $image_path );

			if ( $width == null && $height == null ) {
				if ( $dimensions != null ) {

					if ( $width == null ) {
						$width = $dimensions[0];
					}

					if ( $height == null ) {
						$height = $dimensions[1];
					}
				} else {
					// XXX Don't think there's any other option here but to fail miserably...use some hard-coded defaults maybe?
					return null;
				}
			}

			if ( $dimensions != null ) {
				$dimensions_ratio = $dimensions[0] / $dimensions[1];

				if ( $width == null ) {
					$width = (int) round( $height * $dimensions_ratio );

					if ( $width == ( $dimensions[0] - 1 ) ) {
						$width = $dimensions[0];
					}
				} elseif ( $height == null ) {
					$height = (int) round( $width / $dimensions_ratio );

					if ( $height == ( $dimensions[1] - 1 ) ) {
						$height = $dimensions[1];
					}
				}

				if ( $width > $dimensions[0] ) {
					$width = $dimensions[0];
				}

				if ( $height > $dimensions[1] ) {
					$height = $dimensions[1];
				}

				$image_format = $dimensions[2];

				if ( $type != null ) {
					if ( is_string( $type ) ) {
						$type = strtolower( $type );

						// Indexes in the $format_list array correspond to IMAGETYPE_XXX values appropriately.
						if ( ( $index = array_search( $type, $format_list ) ) !== false ) {
							$type = $index;

							if ( $type != $image_format ) {
								// Note: this only changes the FORMAT of the image but not the extension.
								$clone_format = $type;
							}
						}
					}
				}
			}

			if ( $width == null || $height == null ) {
				// Something went wrong...
				return null;
			}

			// We now need to estimate the 'quality' or level of compression applied to the original JPEG: *IF* the
			// original image has a quality lower than the $quality parameter we will end up generating a new image
			// that is MUCH larger than the original. 'Quality' as an EXIF or IPTC property is quite unreliable
			// and not all software honors or treats it the same way. This calculation is simple: just compare the size
			// that our image could become to what it currently is. '3' is important here as JPEG uses 3 bytes per pixel.
			//
			// First we attempt to use ImageMagick if we can; it has a more robust method of calculation.
			if ( ! empty( $dimensions['mime'] ) && $dimensions['mime'] == 'image/jpeg' ) {
				$possible_quality = null;
				$try_image_magick = true;

				if ( ( defined( 'NGG_DISABLE_IMAGICK' ) && NGG_DISABLE_IMAGICK )
				|| ( function_exists( 'is_wpe' ) && ( $dimensions[0] >= 8000 || $dimensions[1] >= 8000 ) ) ) {
					$try_image_magick = false;
				}

				if ( $try_image_magick && extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
					$img = new \Imagick( $image_path );
					if ( method_exists( $img, 'getImageCompressionQuality' ) ) {
						$possible_quality = $img->getImageCompressionQuality();
					}
				}

				// ImageMagick wasn't available so we guess it from the dimensions and filesize.
				if ( $possible_quality === null ) {
					$filesize         = filesize( $image_path );
					$possible_quality = ( 101 - ( ( $width * $height ) * 3 ) / $filesize );
				}

				if ( $possible_quality !== null && $possible_quality < $quality ) {
					$quality = $possible_quality;
				}
			}

			$result['clone_path']      = $clone_path;
			$result['clone_directory'] = $clone_dir;
			$result['clone_suffix']    = $clone_suffix;
			$result['clone_format']    = $clone_format;
			$result['base_width']      = $dimensions[0];
			$result['base_height']     = $dimensions[1];

			// image_resize() has limitations:
			// - no easy crop frame support
			// - fails if the dimensions are unchanged
			// - doesn't support filename prefix, only suffix so names like thumbs_original_name.jpg for $clone_path are not supported
			// also suffix cannot be null as that will make WordPress use a default suffix...we could use an object that returns empty string from __toString() but for now just fallback to ngg generator.
			if ( false ) {
				// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
				$result['method'] = 'wordpress';

				$new_dims = image_resize_dimensions( $dimensions[0], $dimensions[1], $width, $height, $crop );

				if ( $new_dims ) {
					list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $new_dims;

					$width  = $dst_w;
					$height = $dst_h;
				} else {
					$result['error'] = new \WP_Error( 'error_getting_dimensions', __( 'Could not calculate resized image dimensions', 'nggallery' ) );
				}
			} else {
				$result['method'] = 'nextgen';
				$original_width   = $dimensions[0];
				$original_height  = $dimensions[1];
				$aspect_ratio     = $width / $height;

				$orig_ratio_x = $original_width / $width;
				$orig_ratio_y = $original_height / $height;

				if ( $crop ) {
					$algo = 'shrink'; // either 'adapt' or 'shrink'.

					if ( $crop_frame != null ) {
						$crop_x            = (int) round( $crop_frame['x'] );
						$crop_y            = (int) round( $crop_frame['y'] );
						$crop_width        = (int) round( $crop_frame['width'] );
						$crop_height       = (int) round( $crop_frame['height'] );
						$crop_final_width  = (int) round( $crop_frame['final_width'] );
						$crop_final_height = (int) round( $crop_frame['final_height'] );

						$crop_width_orig  = $crop_width;
						$crop_height_orig = $crop_height;

						$crop_factor_x = $crop_width / $crop_final_width;
						$crop_factor_y = $crop_height / $crop_final_height;

						$crop_ratio_x = $crop_width / $width;
						$crop_ratio_y = $crop_height / $height;

						if ( $algo == 'adapt' ) {
							// XXX not sure about this...don't use for now
							// $crop_width = (int) round($width * $crop_factor_x);
							// $crop_height = (int) round($height * $crop_factor_y);.
						} elseif ( $algo == 'shrink' ) {
							if ( $crop_ratio_x < $crop_ratio_y ) {
								$crop_width  = max( $crop_width, $width );
								$crop_height = (int) round( $crop_width / $aspect_ratio );
							} else {
								$crop_height = max( $crop_height, $height );
								$crop_width  = (int) round( $crop_height * $aspect_ratio );
							}

							if ( $crop_width == ( $crop_width_orig - 1 ) ) {
								$crop_width = $crop_width_orig;
							}

							if ( $crop_height == ( $crop_height_orig - 1 ) ) {
								$crop_height = $crop_height_orig;
							}
						}

						$crop_diff_x = (int) round( ( $crop_width_orig - $crop_width ) / 2 );
						$crop_diff_y = (int) round( ( $crop_height_orig - $crop_height ) / 2 );

						$crop_x += $crop_diff_x;
						$crop_y += $crop_diff_y;

						$crop_max_x = ( $crop_x + $crop_width );
						$crop_max_y = ( $crop_y + $crop_height );

						// Check if we're overflowing borders.
						//
						if ( $crop_x < 0 ) {
							$crop_x = 0;
						} elseif ( $crop_max_x > $original_width ) {
							$crop_x -= ( $crop_max_x - $original_width );
						}

						if ( $crop_y < 0 ) {
							$crop_y = 0;
						} elseif ( $crop_max_y > $original_height ) {
							$crop_y -= ( $crop_max_y - $original_height );
						}
					} else {
						if ( $orig_ratio_x < $orig_ratio_y ) {
							$crop_width  = $original_width;
							$crop_height = (int) round( $height * $orig_ratio_x );

						} else {
							$crop_height = $original_height;
							$crop_width  = (int) round( $width * $orig_ratio_y );
						}

						if ( $crop_width == ( $width - 1 ) ) {
							$crop_width = $width;
						}

						if ( $crop_height == ( $height - 1 ) ) {
							$crop_height = $height;
						}

						$crop_x = (int) round( ( $original_width - $crop_width ) / 2 );
						$crop_y = (int) round( ( $original_height - $crop_height ) / 2 );
					}

					$result['crop_area'] = [
						'x'      => $crop_x,
						'y'      => $crop_y,
						'width'  => $crop_width,
						'height' => $crop_height,
					];
				} else {
					// Just constraint dimensions to ensure there's no stretching or deformations.
					list($width, $height) = wp_constrain_dimensions( $original_width, $original_height, $width, $height );
				}
			}

			$result['width']   = $width;
			$result['height']  = $height;
			$result['quality'] = $quality;

			$real_width  = $width;
			$real_height = $height;

			if ( $rotation && in_array( abs( $rotation ), [ 90, 270 ], true ) ) {
				$real_width  = $height;
				$real_height = $width;
			}

			if ( $reflection ) {
				// default for nextgen was 40%, this is used in generate_image_clone as well.
				$reflection_amount = 40;
				// Note, round() would probably be best here but using the same code that LegacyThumbnail uses for compatibility.
				$reflection_height = intval( $real_height * ( $reflection_amount / 100 ) );
				$real_height       = $real_height + $reflection_height;
			}

			$result['real_width']  = $real_width;
			$result['real_height'] = $real_height;
		}

		return $result;
	}

	public function generate_resized_image( $image, $save = true ) {
		$image_abspath = $this->get_image_abspath( $image, 'full' );

		$generated = $this->generate_image_clone(
			$image_abspath,
			$image_abspath,
			$this->get_image_size_params( $image, 'full' )
		);

		if ( $generated && $save ) {
			$this->update_image_dimension_metadata( $image, $image_abspath );
		}

		if ( $generated ) {
			$generated->destruct();
		}
	}

	public function update_image_dimension_metadata( $image, $image_abspath ) {
		// Ensure that fullsize dimensions are added to metadata array.
		$dimensions = getimagesize( $image_abspath );
		$full_meta  = [
			'width'  => $dimensions[0],
			'height' => $dimensions[1],
			'md5'    => $this->get_image_checksum( $image, 'full' ),
		];

		if ( ! isset( $image->meta_data ) or ( is_string( $image->meta_data ) && strlen( $image->meta_data ) == 0 ) or is_bool( $image->meta_data ) ) {
			$image->meta_data = [];
		}

		$image->meta_data         = array_merge( $image->meta_data, $full_meta );
		$image->meta_data['full'] = $full_meta;

		// Don't forget to append the 'full' entry in meta_data in the db.
		$this->image_mapper->save( $image );
	}

	/**
	 * Most major browsers do not honor the Orientation meta found in EXIF. To prevent display issues we inspect
	 * the EXIF data and rotate the image so that the EXIF field is not necessary to display the image correctly.
	 * Note: generate_image_clone() will handle the removal of the Orientation tag inside the image EXIF.
	 * Note: This only handles single-dimension rotation; at the time this method was written there are no known
	 * camera manufacturers that both rotate and flip images.
	 *
	 * @param $image
	 * @param bool  $save
	 */
	public function correct_exif_rotation( $image, $save = true ) {
		$image_abspath = $this->get_image_abspath( $image, 'full' );

		if ( ! EXIFWriter::is_jpeg_file( $image_abspath ) ) {
			return;
		}

		// This method is necessary.
		if ( ! function_exists( 'exif_read_data' ) ) {
			return;
		}

		// We only need to continue if the Orientation tag is set.
		$exif = @exif_read_data( $image_abspath, 'exif' );
		if ( empty( $exif['Orientation'] ) || $exif['Orientation'] == 1 ) {
			return;
		}

		$degree = 0;
		if ( $exif['Orientation'] == 3 ) {
			$degree = 180;
		}
		if ( $exif['Orientation'] == 6 ) {
			$degree = 90;
		}
		if ( $exif['Orientation'] == 8 ) {
			$degree = 270;
		}

		$parameters = [ 'rotation' => $degree ];

		$generated = $this->generate_image_clone(
			$image_abspath,
			$image_abspath,
			$this->get_image_size_params( $image, 'full', $parameters ),
			$parameters
		);

		if ( $generated && $save ) {
			$this->update_image_dimension_metadata( $image, $image_abspath );
		}

		if ( $generated ) {
			$generated->destruct();
		}
	}

	/**
	 * Flushes the cache we use for path/url calculation for galleries
	 */
	public function flush_gallery_path_cache( $gallery ) {
		$gallery = is_numeric( $gallery ) ? $gallery : $gallery->gid;
		unset( self::$gallery_abspath_cache[ $gallery ] );
	}

	/**
	 * Returns the absolute path to the cache directory of a gallery.
	 *
	 * Without the gallery parameter the legacy (pre 2.0) shared directory is returned.
	 *
	 * @param int|object|false|Gallery $gallery (optional)
	 * @return string Absolute path to cache directory
	 */
	public function get_cache_abspath( $gallery = false ) {
		return path_join( $this->get_gallery_abspath( $gallery ), 'cache' );
	}

	/**
	 * Gets the absolute path where the full-sized image is stored
	 *
	 * @param int|object $image
	 * @return null|string
	 */
	public function get_full_abspath( $image ) {
		return $this->get_image_abspath( $image, 'full' );
	}

	/**
	 * Alias to get_image_dimensions()
	 *
	 * @param int|object $image
	 * @return array
	 */
	public function get_full_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'full' );
	}

	/**
	 * Alias for get_original_url()
	 *
	 * @param Image $image
	 * @return string
	 */
	public function get_full_url( $image ) {
		return $this->get_image_url( $image, 'full' );
	}

	public function get_gallery_root() {
		return wp_normalize_path( Filesystem::get_instance()->get_document_root( 'galleries' ) );
	}

	public function get_computed_gallery_abspath( $gallery ) {
		$retval       = null;
		$gallery_root = $this->get_gallery_root();

		// Get the gallery entity from the database.
		if ( $gallery ) {
			if ( is_numeric( $gallery ) ) {
				$gallery = $this->gallery_mapper->find( $gallery );
			}
		}

		// It just doesn't exist.
		if ( ! $gallery ) {
			return $retval;
		}

		// We we have a gallery, determine it's path.
		if ( $gallery ) {
			if ( isset( $gallery->path ) ) {
				$retval = $gallery->path;
			} elseif ( isset( $gallery->slug ) ) {
				$basepath = wp_normalize_path( Settings::get_instance()->gallerypath );
				$retval   = path_join( $basepath, $this->sanitize_directory_name( sanitize_title( $gallery->slug ) ) );
			}

			// Normalize the gallery path. If the gallery path starts with /wp-content, and
			// NGG_GALLERY_ROOT_TYPE is set to 'content', then we need to strip out the /wp-content
			// from the start of the gallery path.
			if ( NGG_GALLERY_ROOT_TYPE === 'content' ) {
				$retval = preg_replace( '#^/?wp-content#', '', $retval );
			}

			// Ensure that the path is absolute.
			if ( strpos( $retval, $gallery_root ) !== 0 ) {

				// path_join() behaves funny - if the second argument starts with a slash,
				// it won't join the two paths together.
				$retval = preg_replace( '#^/#', '', $retval );
				$retval = path_join( $gallery_root, $retval );
			}

			$retval = wp_normalize_path( $retval );
		}

		return $retval;
	}

	/**
	 * Get the abspath to the gallery folder for the given gallery
	 * The gallery may or may not already be persisted
	 *
	 * @param int|object|Gallery $gallery
	 *
	 * @return string
	 */
	public function get_gallery_abspath( $gallery ) {
		$gallery_id = is_numeric( $gallery ) ? $gallery : ( is_object( $gallery ) && isset( $gallery->gid ) ? $gallery->gid : null );

		if ( ! $gallery_id || ! isset( self::$gallery_abspath_cache[ $gallery_id ] ) ) {
			self::$gallery_abspath_cache[ $gallery_id ] = $this->get_computed_gallery_abspath( $gallery );
		}

		return self::$gallery_abspath_cache[ $gallery_id ];
	}

	public function get_gallery_relpath( $gallery ) {
		// Special hack for home.pl: their document root is just '/'.
		$root = $this->get_gallery_root();
		if ( $root === '/' ) {
			return $this->get_gallery_abspath( $gallery );
		}

		return str_replace( $this->get_gallery_root(), '', $this->get_gallery_abspath( $gallery ) );
	}

	/**
	 * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
	 *
	 * @param int|object $image
	 * @param string     $size (optional) Default = full
	 * @return string
	 */
	public function get_computed_image_abspath( $image, $size = 'full', $check_existance = false ) {
		$retval = null;

		// If we have the id, get the actual image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have the image entity - user could have passed in an incorrect id.
		if ( is_object( $image ) ) {
			if ( ( $gallery_path = $this->get_gallery_abspath( $image->galleryid ) ) ) {
				$folder = $prefix = $size;
				switch ( $size ) {

					// Images are stored in the associated gallery folder.
					case 'full':
						$retval = \path_join( $gallery_path, $image->filename );
						break;

					case 'backup':
						$retval = \path_join( $gallery_path, $image->filename . '_backup' );
						if ( ! @file_exists( $retval ) ) {
							$retval = \path_join( $gallery_path, $image->filename );
						}
						break;

					case 'thumbnail':
						$size   = 'thumbnail';
						$folder = 'thumbs';
						$prefix = 'thumbs';
						// deliberately no break here.
					default:
						// NGG 2.0 stores relative filenames in the meta data of
						// an image. It does this because it uses filenames
						// that follow conventional WordPress naming scheme.
						$image_path = null;
						$dynthumbs  = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
						if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) && isset( $image->meta_data[ $size ]['filename'] ) ) {
							if ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
								$image_path = \path_join( $this->get_cache_abspath( $image->galleryid ), $image->meta_data[ $size ]['filename'] );
							} else {
								$image_path = \path_join( $gallery_path, $folder );
								$image_path = \path_join( $image_path, $image->meta_data[ $size ]['filename'] );
							}
						}

						// Filename not found in meta, but is dynamic.
						elseif ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
							$params     = $dynthumbs->get_params_from_name( $size, true );
							$image_path = \path_join( $this->get_cache_abspath( $image->galleryid ), $dynthumbs->get_image_name( $image, $params ) );

							// Filename is not found in meta, nor dynamic.
						} else {
							$settings = Settings::get_instance();

							// This next bit is annoying but necessary for legacy reasons. NextGEN until 3.19 stored thumbnails
							// with a filename of "thumbs_(whatever.jpg)" which Google indexes as "thumbswhatever.jpg" which is
							// not good for SEO. From 3.19 on the default setting is "thumbs-" but we must account for legacy
							// sites.
							$image_path     = \path_join( $gallery_path, $folder );
							$new_thumb_path = \path_join( $image_path, "{$prefix}-{$image->filename}" );
							$old_thumb_path = \path_join( $image_path, "{$prefix}_{$image->filename}" );

							if ( $settings->get( 'dynamic_image_filename_separator_use_dash', false ) ) {
								// Check for thumbs- first.
								if ( file_exists( $new_thumb_path ) ) {
									$image_path = $new_thumb_path;
								} elseif ( file_exists( $old_thumb_path ) ) {
									// Check for thumbs_ as a fallback.
									$image_path = $old_thumb_path;
								} else { // The thumbnail file does not exist, default to thumbs-.
									$image_path = $new_thumb_path;
								}
							} else {
								// Reversed: the option is disabled so check for thumbs_.
								if ( file_exists( $old_thumb_path ) ) {
									$image_path = $old_thumb_path;
								} elseif ( file_exists( $new_thumb_path ) ) {
									// In case the user has switched back and forth, check for thumbs-.
									$image_path = $new_thumb_path;
								} else { // Default to thumbs_ per the site setting.
									$image_path = $old_thumb_path;
								}
							}
						}

						$retval = $image_path;
						break;
				}
			}
		}
		if ( $retval && $check_existance && ! @file_exists( $retval ) ) {
			$retval = null;
		}
		return $retval;
	}

	public function get_image_checksum( $image, $size = 'full' ) {
		$retval = null;
		if ( ( $image_abspath = $this->get_image_abspath( $image, $size, true ) ) ) {
			$retval = md5_file( $image_abspath );
		}
		return $retval;
	}

	/**
	 * Gets the dimensions for a particular-sized image
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @return null|array
	 */
	public function get_image_dimensions( $image, $size = 'full' ) {
		$retval = null;

		// If an image id was provided, get the entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {

			$size = $this->normalize_image_size_name( $size );
			if ( ! $size ) {
				$size = 'full';
			}

			// Image dimensions are stored in the $image->meta_data
			// property for all implementations.
			if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) ) {
				$retval = $image->meta_data[ $size ];
			}

			// Didn't exist for meta data. We'll have to compute
			// dimensions in the meta_data after computing? This is most likely
			// due to a dynamic image size being calculated for the first time.
			else {
				$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
				$abspath   = $this->get_image_abspath( $image, $size, true );
				if ( $abspath ) {
					$dims = @getimagesize( $abspath );
					if ( $dims ) {
						$retval['width']  = $dims[0];
						$retval['height'] = $dims[1];
					}
				} elseif ( $size == 'backup' ) {
					$retval = $this->get_image_dimensions( $image, 'full' );
				}

				if ( ! $retval && $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
					$new_dims = $this->calculate_image_size_dimensions( $image, $size );
					// Prevent a possible PHP warning if the sizes weren't calculated.
					if ( isset( $new_dims['real_width'] ) && isset( $new_dims['real_height'] ) ) {
						$retval = [
							'width'  => $new_dims['real_width'],
							'height' => $new_dims['real_height'],
						];
					}
				}
			}
		}

		return $retval;
	}

	public function get_image_format_list() {
		$format_list = [
			IMAGETYPE_GIF  => 'gif',
			IMAGETYPE_JPEG => 'jpg',
			IMAGETYPE_PNG  => 'png',
			IMAGETYPE_WEBP => 'webp',
		];

		return $format_list;
	}

	/**
	 * Gets the HTML for an image
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @param array      $attributes (optional)
	 * @return string
	 */
	public function get_image_html( $image, $size = 'full', $attributes = [] ) {
		$retval = '';

		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {

			// Set alt text if not already specified.
			if ( ! isset( $attributes['alttext'] ) ) {
				$attributes['alt'] = esc_attr( $image->alttext );
			}

			// Set the title if not already set.
			if ( ! isset( $attributes['title'] ) ) {
				$attributes['title'] = esc_attr( $image->alttext );
			}

			// Set the dimensions if not set already.
			if ( ! isset( $attributes['width'] ) or ! isset( $attributes['height'] ) ) {
				$dimensions = $this->get_image_dimensions( $image, $size );
				if ( ! isset( $attributes['width'] ) ) {
					$attributes['width'] = $dimensions['width'];
				}
				if ( ! isset( $attributes['height'] ) ) {
					$attributes['height'] = $dimensions['height'];
				}
			}

			// Set the url if not already specified.
			if ( ! isset( $attributes['src'] ) ) {
				$attributes['src'] = $this->get_image_url( $image, $size );
			}

			// Format attributes.
			$attribs = [];
			foreach ( $attributes as $attrib => $value ) {
				$attribs[] = "{$attrib}=\"{$value}\"";
			}
			$attribs = implode( ' ', $attribs );

			// Return HTML string.
			$retval = "<img {$attribs} />";
		}

		return $retval;
	}

	public function get_computed_image_url( $image, $size = 'full' ) {
		$retval    = null;
		$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();

		// Get the image abspath.
		$image_abspath = $this->get_image_abspath( $image, $size );
		if ( $dynthumbs->is_size_dynamic( $size ) && ! file_exists( $image_abspath ) ) {
			if ( defined( 'NGG_DISABLE_DYNAMIC_IMG_URLS' ) && constant( 'NGG_DISABLE_DYNAMIC_IMG_URLS' ) ) {
				$params = [
					'watermark'  => false,
					'reflection' => false,
					'crop'       => true,
				];
				$result = $this->generate_image_size( $image, $size, $params );
				if ( $result ) {
					$image_abspath = $this->get_image_abspath( $image, $size );
				}
			} else {
				return null;
			}
		}

		// Assuming we have an abspath, we can translate that to a url.
		if ( $image_abspath ) {

			// Replace the gallery root with the proper url segment.
			$gallery_root = preg_quote( $this->get_gallery_root(), '#' );
			$image_uri    = preg_replace(
				"#^{$gallery_root}#",
				'',
				$image_abspath
			);

			// Url encode each uri segment.
			$segments  = explode( '/', $image_uri );
			$segments  = array_map( 'rawurlencode', $segments );
			$image_uri = preg_replace( '#^/#', '', implode( '/', $segments ) );

			// Join gallery root and image uri.
			$gallery_root = trailingslashit( NGG_GALLERY_ROOT_TYPE == 'site' ? site_url() : WP_CONTENT_URL );
			$gallery_root = is_ssl() ? str_replace( 'http:', 'https:', $gallery_root ) : $gallery_root;
			$retval       = $gallery_root . $image_uri;
		}

		return $retval;
	}

	public function normalize_image_size_name( $size = 'full' ) {
		switch ( $size ) {
			case 'full':
			case 'image':
			case 'orig':
			case 'original':
			case 'resized':
				$size = 'full';
				break;
			case 'thumb':
			case 'thumbnail':
			case 'thumbnails':
			case 'thumbs':
				$size = 'thumbnail';
				break;
		}
		return $size;
	}

	/**
	 * Returns the named sizes available for images
	 *
	 * @return array
	 */
	public function get_image_sizes( $image = false ) {
		$retval = [ 'full', 'thumbnail' ];

		if ( is_numeric( $image ) ) {
			$image = ImageMapper::get_instance()->find( $image );
		}

		if ( $image ) {
			if ( $image->meta_data ) {
				$meta_data = is_object( $image->meta_data ) ? get_object_vars( $image->meta_data ) : $image->meta_data;
				foreach ( $meta_data as $key => $value ) {
					if ( is_array( $value ) && isset( $value['width'] ) && ! in_array( $key, $retval ) ) {
						$retval[] = $key;
					}
				}
			}
		}

		return $retval;
	}

	public function get_image_size_params( $image, $size, $params = [], $skip_defaults = false ) {
		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
		if ( $dynthumbs && $dynthumbs->is_size_dynamic( $size ) ) {
			$named_params = $dynthumbs->get_params_from_name( $size, true );
			if ( ! $params ) {
				$params = [];
			}
			$params = array_merge( $params, $named_params );
		}

		$params = apply_filters( 'ngg_get_image_size_params', $params, $size, $image );

		// Ensure we have a valid image.
		if ( $image ) {
			$settings = Settings::get_instance();

			if ( ! $skip_defaults ) {
				// Get default settings.
				if ( $size == 'full' ) {
					if ( ! isset( $params['quality'] ) ) {
						$params['quality'] = $settings->get( 'imgQuality' );
					}
				} else {
					if ( ! isset( $params['crop'] ) ) {
						$params['crop'] = $settings->get( 'thumbfix' );
					}

					if ( ! isset( $params['quality'] ) ) {
						$params['quality'] = $settings->get( 'thumbquality' );
					}
				}
			}

			// width and height when omitted make generate_image_clone create a clone with original size, so try find defaults regardless of $skip_defaults.
			if ( ! isset( $params['width'] ) || ! isset( $params['height'] ) ) {

				// First test if this is a "known" image size, i.e. if we store these sizes somewhere when users re-generate these sizes from the UI...this is required to be compatible with legacy.
				// try the 2 default built-in sizes, first thumbnail...
				if ( $size == 'thumbnail' ) {
					if ( ! isset( $params['width'] ) ) {
						$params['width'] = $settings->thumbwidth;
					}

					if ( ! isset( $params['height'] ) ) {
						$params['height'] = $settings->thumbheight;
					}
				}
				// ...and then full, which is the size specified in the global resize options.
				elseif ( $size == 'full' ) {
					if ( ! isset( $params['width'] ) ) {
						if ( $settings->imgAutoResize ) {
							$params['width'] = $settings->imgWidth;
						}
					}

					if ( ! isset( $params['height'] ) ) {
						if ( $settings->imgAutoResize ) {
							$params['height'] = $settings->imgHeight;
						}
					}
				}
				// Only re-use old sizes as last resort.
				elseif ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) ) {
					$dimensions = $image->meta_data[ $size ];

					if ( ! isset( $params['width'] ) ) {
						$params['width'] = $dimensions['width'];
					}

					if ( ! isset( $params['height'] ) ) {
						$params['height'] = $dimensions['height'];
					}
				}
			}

			if ( ! isset( $params['crop_frame'] ) ) {
				$crop_frame_size_name = 'thumbnail';

				if ( isset( $image->meta_data[ $size ]['crop_frame'] ) ) {
					$crop_frame_size_name = $size;
				}

				if ( isset( $image->meta_data[ $crop_frame_size_name ]['crop_frame'] ) ) {
					$params['crop_frame'] = $image->meta_data[ $crop_frame_size_name ]['crop_frame'];

					if ( ! isset( $params['crop_frame']['final_width'] ) ) {
						$params['crop_frame']['final_width'] = $image->meta_data[ $crop_frame_size_name ]['width'];
					}

					if ( ! isset( $params['crop_frame']['final_height'] ) ) {
						$params['crop_frame']['final_height'] = $image->meta_data[ $crop_frame_size_name ]['height'];
					}
				}
			} else {
				if ( ! isset( $params['crop_frame']['final_width'] ) ) {
					$params['crop_frame']['final_width'] = $params['width'];
				}

				if ( ! isset( $params['crop_frame']['final_height'] ) ) {
					$params['crop_frame']['final_height'] = $params['height'];
				}
			}
		}

		return $params;
	}

	/**
	 * Alias to get_image_dimensions()
	 *
	 * @param int|object $image
	 * @return array
	 */
	public function get_original_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'full' );
	}

	/**
	 * @param object|bool $gallery (optional)
	 * @return string
	 */
	public function get_upload_abspath( $gallery = false ) {
		// Base upload path.
		$retval = Settings::get_instance()->get( 'gallerypath' );
		$fs     = Filesystem::get_instance();

		// Append the slug if a gallery has been specified.
		if ( $gallery ) {
			$retval = $this->get_gallery_abspath( $gallery );
		}

		// We need to make this an absolute path.
		if ( ! empty( $retval ) && strpos( $retval, $fs->get_document_root( 'gallery' ) ) !== 0 ) {
			$retval = rtrim( $fs->join_paths( $fs->get_document_root( 'gallery' ), $retval ), '/\\' );
		}

		// Convert slashes.
		return wp_normalize_path( $retval );
	}

	/**
	 * Gets the upload path, optionally for a particular gallery
	 *
	 * @param int|Gallery|object|false $gallery (optional)
	 *
	 * @return string
	 */
	public function get_upload_relpath( $gallery = false ) {
		$fs = Filesystem::get_instance();

		$retval = str_replace(
			$fs->get_document_root( 'gallery' ),
			'',
			$this->get_upload_abspath( $gallery )
		);

		return '/' . wp_normalize_path( ltrim( $retval, '/' ) );
	}

	public function delete_gallery_directory( $abspath ) {
		// Remove all image files and purge all empty directories left over.
		$iterator = new \DirectoryIterator( $abspath );

		// Only delete image files! Other files may be stored incorrectly but it's not our place to delete them.
		$removable_extensions = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );
		foreach ( $removable_extensions as $extension ) {
			$removable_extensions[] = $extension . '_backup';
		}

		foreach ( $iterator as $file ) {
			if ( in_array( $file->getBasename(), [ '.', '..' ] ) ) {
				continue;

			} elseif ( $file->isFile() || $file->isLink() ) {
				$extension = strtolower( pathinfo( $file->getPathname(), PATHINFO_EXTENSION ) );
				if ( in_array( $extension, $removable_extensions, true ) ) {
					@unlink( $file->getPathname() );
				}
			} elseif ( $file->isDir() ) {
				$this->delete_gallery_directory( $file->getPathname() );
			}
		}

		// DO NOT remove directories that still have files in them. Note: '.' and '..' are included with getSize().
		$empty = true;
		foreach ( $iterator as $file ) {
			if ( in_array( $file->getBasename(), [ '.', '..' ] ) ) {
				continue;
			}
			$empty = false;
		}
		if ( $empty ) {
			@rmdir( $iterator->getPath() );
		}
	}

	/**
	 * @param Image[]     $images
	 * @param Gallery|int $dst_gallery
	 *
	 * @return int[]
	 */
	public function copy_images( $images, $dst_gallery ) {
		$retval = [];

		// Ensure that the image ids we have are valid.
		$image_mapper = ImageMapper::get_instance();
		foreach ( $images as $image ) {
			if ( is_numeric( $image ) ) {
				$image = $image_mapper->find( $image );
			}

			$image_abspath = $this->get_image_abspath( $image, 'backup' ) ?: $this->get_image_abspath( $image );

			if ( $image_abspath ) {
				// Import the image; this will copy the main file.
				$new_image_id = $this->import_image_file( $dst_gallery, $image_abspath, $image->filename );

				if ( $new_image_id ) {
					// Copy the properties of the old image.
					$new_image = $image_mapper->find( $new_image_id );
					foreach ( get_object_vars( $image ) as $key => $value ) {
						if ( in_array( $key, [ 'pid', 'galleryid', 'meta_data', 'filename', 'sortorder', 'extras_post_id' ] ) ) {
							continue;
						}
						$new_image->$key = $value;
					}
					$image_mapper->save( $new_image );

					// Copy tags.
					$tags = wp_get_object_terms( $image->pid, 'ngg_tag', 'fields=ids' );
					$tags = array_map( 'intval', $tags );
					wp_set_object_terms( $new_image_id, $tags, 'ngg_tag', true );

					// Copy all of the generated versions (resized versions, watermarks, etc).
					foreach ( $this->get_image_sizes( $image ) as $named_size ) {
						if ( in_array( $named_size, [ 'full', 'thumbnail' ] ) ) {
							continue;
						}
						$old_abspath = $this->get_image_abspath( $image, $named_size );
						$new_abspath = $this->get_image_abspath( $new_image, $named_size );
						if ( is_array( @stat( $old_abspath ) ) ) {
							$new_dir = dirname( $new_abspath );
							// Ensure the target directory exists.
							if ( @stat( $new_dir ) === false ) {
								wp_mkdir_p( $new_dir );
							}
							@copy( $old_abspath, $new_abspath );
						}
					}

					// Mark as done.
					$retval[] = $new_image_id;
				}
			}
		}

		return $retval;
	}

	/**
	 * Moves images from to another gallery
	 *
	 * @param array      $images
	 * @param int|object $gallery
	 * @return int[]
	 */
	public function move_images( $images, $gallery ) {
		$retval = $this->copy_images( $images, $gallery );

		if ( $images ) {
			foreach ( $images as $image_id ) {
				$this->delete_image( $image_id );
			}
		}

		return $retval;
	}

	/**
	 * @param string $abspath
	 * @return bool
	 */
	public function delete_directory( $abspath ) {
		$retval = false;

		if ( @file_exists( $abspath ) ) {
			$files = scandir( $abspath );
			array_shift( $files );
			array_shift( $files );
			foreach ( $files as $file ) {
				$file_abspath = implode( DIRECTORY_SEPARATOR, [ rtrim( $abspath, '/\\' ), $file ] );
				if ( is_dir( $file_abspath ) ) {
					$this->delete_directory( $file_abspath );
				} else {
					unlink( $file_abspath );
				}
			}
			rmdir( $abspath );
			$retval = @file_exists( $abspath );
		}

		return $retval;
	}

	public function delete_gallery( $gallery ) {
		$fs        = Filesystem::get_instance();
		$safe_dirs = [
			DIRECTORY_SEPARATOR,
			$fs->get_document_root( 'plugins' ),
			$fs->get_document_root( 'plugins_mu' ),
			$fs->get_document_root( 'templates' ),
			$fs->get_document_root( 'stylesheets' ),
			$fs->get_document_root( 'content' ),
			$fs->get_document_root( 'galleries' ),
			$fs->get_document_root(),
		];

		$abspath = $this->get_gallery_abspath( $gallery );

		if ( $abspath && file_exists( $abspath ) && ! in_array( stripslashes( $abspath ), $safe_dirs ) ) {
			$this->delete_gallery_directory( $abspath );
		}
	}

	/**
	 * @param Image        $image
	 * @param string|false $size
	 * @return bool
	 */
	public function delete_image( $image, $size = false ) {
		$retval = false;

		// Ensure that we have the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {
			$image_id = $image->{$image->id_field};
			do_action( 'ngg_delete_image', $image_id, $size );

			// Delete only a particular image size.
			if ( $size ) {
				$abspath = $this->get_image_abspath( $image, $size );
				if ( $abspath && @file_exists( $abspath ) ) {
					@unlink( $abspath );
				}
				if ( isset( $image->meta_data ) && isset( $image->meta_data[ $size ] ) ) {
					unset( $image->meta_data[ $size ] );
					$this->image_mapper->save( $image );
				}
			}
			// Delete all sizes of the image.
			else {
				foreach ( $this->get_image_sizes( $image ) as $named_size ) {

					$image_abspath = $this->get_image_abspath( $image, $named_size );
					@unlink( $image_abspath );
				}

				// Delete the entity.
				$this->image_mapper->destroy( $image );
			}
			$retval = true;
		}

		return $retval;
	}

	/**
	 * Outputs/renders an image
	 *
	 * @param Image $image
	 * @return bool
	 */
	public function render_image( $image, $size = false ) {
		$format_list = $this->get_image_format_list();
		$abspath     = $this->get_image_abspath( $image, $size, true );

		if ( $abspath == null ) {
			$thumbnail = $this->generate_image_size( $image, $size );

			if ( $thumbnail != null ) {
				$abspath = $thumbnail->fileName;

				$thumbnail->destruct();
			}
		}

		if ( $abspath != null ) {
			$data   = @getimagesize( $abspath );
			$format = 'jpg';

			if ( $data != null && is_array( $data ) && isset( $format_list[ $data[2] ] ) ) {
				$format = $format_list[ $data[2] ];
			}

			// Clear output.
			while ( ob_get_level() > 0 ) {
				ob_end_clean();
			}

			$format = strtolower( $format );

			// output image and headers.
			header( 'Content-type: image/' . $format );
			readfile( $abspath );

			return true;
		}

		return false;
	}

	/**
	 * Recover image from backup copy and reprocess it
	 *
	 * @param Image $image
	 * @return bool|string result code
	 */
	public function recover_image( $image ) {
		$retval = false;

		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		if ( $image ) {
			$full_abspath   = $this->get_image_abspath( $image );
			$backup_abspath = $this->get_image_abspath( $image, 'backup' );

			if ( $backup_abspath != $full_abspath && @file_exists( $backup_abspath ) ) {
				if ( is_writable( $full_abspath ) && is_writable( dirname( $full_abspath ) ) ) {
					// Copy the backup.
					if ( @copy( $backup_abspath, $full_abspath ) ) {
						// Backup images are not altered at all; we must re-correct the EXIF/Orientation tag.
						$this->correct_exif_rotation( $image, true );

						// Re-create non-fullsize image sizes.
						foreach ( $this->get_image_sizes( $image ) as $named_size ) {
							if ( in_array( $named_size, [ 'full', 'backup' ] ) ) {
								continue;
							}

							// Reset thumbnail cropping set by 'Edit thumb' dialog.
							if ( $named_size === 'thumbnail' ) {
								unset( $image->meta_data[ $named_size ]['crop_frame'] );
							}

							$thumbnail = $this->generate_image_clone(
								$full_abspath,
								$this->get_image_abspath( $image, $named_size ),
								$this->get_image_size_params( $image, $named_size )
							);
							if ( $thumbnail ) {
								$thumbnail->destruct();
							}
						}

						do_action( 'ngg_recovered_image', $image );

						// Reimport all metadata.
						$retval = $this->image_mapper->reimport_metadata( $image );
					}
				}
			}
		}

		return $retval;
	}

	/**
	 * Copies a NGG image to the media library and returns the attachment_id
	 *
	 * @param Image $image
	 * @return false|int attachment_id
	 */
	public function copy_to_media_library( $image ) {
		$retval = false;

		// Get the image.
		if ( is_int( $image ) ) {
			$imageId = $image;
			$mapper  = ImageMapper::get_instance();
			$image   = $mapper->find( $imageId );
		}

		if ( $image ) {
			$subdir = apply_filters( 'ngg_import_to_media_library_subdir', 'nggallery_import' );

			$wordpress_upload_dir = wp_upload_dir();
			$path                 = $wordpress_upload_dir['path'] . DIRECTORY_SEPARATOR . $subdir;

			if ( ! file_exists( $path ) ) {
				wp_mkdir_p( $path );
			}

			$image_abspath = $this->get_image_abspath( $image, 'full' );
			$new_file_path = $path . DIRECTORY_SEPARATOR . $image->filename;

			$image_data    = getimagesize( $image_abspath );
			$new_file_mime = $image_data['mime'];

			$i = 1;
			while ( file_exists( $new_file_path ) ) {
				++$i;
				$new_file_path = $path . DIRECTORY_SEPARATOR . $i . '-' . $image->filename;
			}

			if ( @copy( $image_abspath, $new_file_path ) ) {
				$upload_id = wp_insert_attachment(
					[
						'guid'           => $new_file_path,
						'post_mime_type' => $new_file_mime,
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $image->alttext ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					],
					$new_file_path
				);

				update_post_meta( $upload_id, '_ngg_image_id', intval( $image->pid ) );

				// wp_generate_attachment_metadata() comes from this file.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				$image_meta = wp_generate_attachment_metadata( $upload_id, $new_file_path );

				// Generate and save the attachment metas into the database.
				wp_update_attachment_metadata( $upload_id, $image_meta );

				$retval = $upload_id;
			}
		}

		return $retval;
	}

	/**
	 * Delete the given NGG image from the media library
	 *
	 * @var int|stdClass $imageId
	 */
	public function delete_from_media_library( $imageId ) {
		// Get the image.
		if ( ! is_int( $imageId ) ) {
			$image   = $imageId;
			$imageId = $image->pid;
		}

		if ( ( $postId = $this->is_in_media_library( $imageId ) ) ) {
			wp_delete_post( $postId );
		}
	}

	/**
	 * Determines if the given NGG image id has been uploaded to the media library
	 *
	 * @param integer $imageId
	 * @return false|int attachment_id
	 */
	public function is_in_media_library( $imageId ) {
		$retval = false;

		// Get the image.
		if ( is_object( $imageId ) ) {
			$image   = $imageId;
			$imageId = $image->pid;
		}

		// Try to find an attachment for the given image_id.
		if ( $imageId ) {
			$query = new \WP_Query(
				[
					'post_type'      => 'attachment',
					'meta_key'       => '_ngg_image_id',
					'meta_value_num' => $imageId,
				]
			);

			foreach ( $query->get_posts() as $post ) {
				$retval = $post->ID;
			}
		}

		return $retval;
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public function is_allowed_image_extension( $filename ) {
		$extension = pathinfo( $filename, PATHINFO_EXTENSION );
		$extension = strtolower( $extension );

		$allowed_extensions = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );

		foreach ( $allowed_extensions as $extension ) {
			$allowed_extensions[] = $extension . '_backup';
		}

		return in_array( $extension, $allowed_extensions );
	}

	public function is_current_user_over_quota() {
		$retval   = false;
		$settings = Settings::get_instance();

		if ( ( is_multisite() ) && $settings->get( 'wpmuQuotaCheck' ) ) {
			require_once ABSPATH . 'wp-admin/includes/ms.php';
			$retval = upload_is_user_over_quota( false );
		}

		return $retval;
	}

	/**
	 * @param string? $filename
	 * @return bool
	 */
	public function is_image_file( $filename = null ): bool {
		$retval = false;

		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! $filename
			&& isset( $_FILES['file']['error'] )
			&& isset( $_FILES['file']['tmp_name'] )
			&& 0 === $_FILES['file']['error']
			&& isset( $_REQUEST['nonce'] )
			&& Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
			// Windows' use of backslash characters for file paths means wp_unslash() here is destructive.
			if ( 0 === strncasecmp( PHP_OS, 'WIN', 3 ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$filename = sanitize_text_field( $_FILES['file']['tmp_name'] );
			} else {
				$filename = sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$allowed_mime = apply_filters( 'ngg_allowed_mime_types', NGG_DEFAULT_ALLOWED_MIME_TYPES );

		// If we can, we'll verify the mime type.
		if ( function_exists( 'exif_imagetype' ) ) {
			if ( ( $image_type = @exif_imagetype( $filename ) ) !== false ) {
				$retval = in_array( image_type_to_mime_type( $image_type ), $allowed_mime );
			}
		} else {
			$file_info = @getimagesize( $filename );
			if ( isset( $file_info[2] ) ) {
				$retval = in_array( image_type_to_mime_type( $file_info[2] ), $allowed_mime );
			}
		}

		return $retval;
	}

	public function is_zip(): bool {
		$retval = false;

		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_FILES['file']['error'] )
			&& 0 === $_FILES['file']['error']
			&& isset( $_REQUEST['nonce'] )
			&& Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
			$file_info = $_FILES['file'];

			if ( isset( $file_info['type'] ) ) {
				$type       = $file_info['type'];
				$type_parts = explode( '/', $type );

				if ( strtolower( $type_parts[0] ) == 'application' ) {
					$spec       = $type_parts[1];
					$spec_parts = explode( '-', $spec );
					$spec_parts = array_map( 'strtolower', $spec_parts );

					if ( in_array( $spec, [ 'zip', 'octet-stream' ] ) || in_array( 'zip', $spec_parts ) ) {
						$retval = true;
					}
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $retval;
	}

	public function get_unique_abspath( $file_abspath ) {
		$filename    = basename( $file_abspath );
		$dir_abspath = dirname( $file_abspath );
		$num         = 1;

		$pattern = path_join( $dir_abspath, "*_{$filename}" );
		if ( ( $found = glob( $pattern ) ) ) {
			natsort( $found );
			$last = array_pop( $found );
			$last = basename( $last );
			if ( preg_match( '/^(\d+)_/', $last, $match ) ) {
				$num = intval( $match[1] ) + 1;
			}
		}

		return path_join( $dir_abspath, "{$num}_{$filename}" );
	}

	/**
	 * Determines whether a WebP image is animated which GD does not support.
	 *
	 * @see https://developers.google.com/speed/webp/docs/riff_container
	 * @param string $filename
	 * @return bool
	 */
	public function is_animated_webp( $filename ) {
		$retval = false;
		$handle = fopen( $filename, 'rb' );
		fseek( $handle, 12 );
		if ( fread( $handle, 4 ) === 'VP8X' ) {
			fseek( $handle, 20 );
			$flag   = fread( $handle, 1 );
			$retval = (bool) ( ( ( ord( $flag ) >> 1 ) & 1 ) );
		}
		fclose( $handle );

		return $retval;
	}

	public function import_image_file( $dst_gallery, $image_abspath, $filename = null, $image = false, $override = false, $move = false ) {
		$image_abspath = wp_normalize_path( $image_abspath );

		if ( $this->is_current_user_over_quota() ) {
			$message = sprintf( __( 'Sorry, you have used your space allocation. Please delete some files to upload more files.', 'nggallery' ) );
			throw new \E_NoSpaceAvailableException( esc_html( $message ) );
		}

		// Do we have a gallery to import to?
		if ( $dst_gallery ) {
			// Get the gallery abspath. This is where we will put the image files.
			$gallery_abspath = $this->get_gallery_abspath( $dst_gallery );

			// If we can't write to the directory, then there's no point in continuing.
			if ( ! @file_exists( $gallery_abspath ) ) {
				@wp_mkdir_p( $gallery_abspath );
			}
			if ( ! is_writable( $gallery_abspath ) ) {
				throw new \E_InsufficientWriteAccessException( false, $gallery_abspath, false );
			}

			// Sanitize the filename for storing in the DB.
			$filename = $this->sanitize_filename_for_db( $filename );

			// Ensure that the filename is valid.
			$extensions   = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );
			$extensions[] = '_backup';
			$ext_list     = implode( '|', $extensions );

			if ( ! preg_match( "/({$ext_list})\$/i", $filename ) ) {
				throw new \E_UploadException( __( 'Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery' ) );
			}
			// GD does not support animated WebP and will generate a fatal error when we try to create thumbnails or resize.
			if ( $this->is_animated_webp( $image_abspath ) ) {
				throw new \E_UploadException( __( 'Animated WebP images are not supported.', 'nggallery' ) );
			}

			// Compute the destination folder.
			$new_image_abspath = path_join( $gallery_abspath, $filename );

			// Are the src and dst the same? If so, we don't have to copy or move files.
			if ( $image_abspath != $new_image_abspath ) {
				// If we're not to override, ensure that the filename is unique.
				if ( ! $override && @file_exists( $new_image_abspath ) ) {
					$new_image_abspath = $this->get_unique_abspath( $new_image_abspath );
					$filename          = $this->sanitize_filename_for_db( basename( $new_image_abspath ) );
				}

				// Try storing the file.
				$copied = copy( $image_abspath, $new_image_abspath );
				if ( $copied && $move ) {
					unlink( $image_abspath );
				}

				// Ensure that we're not vulerable to CVE-2017-2416 exploit.
				if ( ( $dimensions = getimagesize( $new_image_abspath ) ) !== false ) {
					if ( ( isset( $dimensions[0] ) && intval( $dimensions[0] ) > 30000 )
						|| ( isset( $dimensions[1] ) && intval( $dimensions[1] ) > 30000 ) ) {
						unlink( $new_image_abspath );
						throw new \E_UploadException( esc_html( __( 'Image file too large. Maximum image dimensions supported are 30k x 30k.' ) ) );
					}
				}
			}

			// Save the image in the DB.
			$image_mapper            = ImageMapper::get_instance();
			$image_mapper->use_cache = false;
			if ( $image ) {
				if ( is_numeric( $image ) ) {
					$image = $image_mapper->find( $image );
				}
			}
			if ( ! $image ) {
				$image = $image_mapper->create();
			}
			$image->alttext    = preg_replace( '#\.\w{2,4}$#', '', $filename );
			$image->galleryid  = is_numeric( $dst_gallery ) ? $dst_gallery : $dst_gallery->gid;
			$image->filename   = $filename;
			$image->image_slug = \nggdb::get_unique_slug( sanitize_title_with_dashes( $image->alttext ), 'image' );
			$image_id          = $image_mapper->save( $image );

			if ( ! $image_id ) {
				$exception  = '';
				$validation = $image->validation();
				if ( is_array( $validation ) ) {
					foreach ( $validation as $field => $errors ) {
						foreach ( $errors as $error ) {
							if ( ! empty( $exception ) ) {
								$exception .= '<br/>';
							}
							$exception .= __( sprintf( 'Error while uploading %s: %s', $filename, $error ), 'nextgen-gallery' );
						}
					}
					throw new \E_UploadException( $exception );
				}
			}

			// Important: do not remove this line. The image mapper's save() routine imports metadata
			// meaning we must re-acquire a new $image object after saving it above; if we do not our
			// existing $image object will lose any metadata retrieved during said save() method.
			$image = $image_mapper->find( $image_id );

			$image_mapper->use_cache = true;
			$settings                = Settings::get_instance();

			// Backup the image.
			if ( $settings->get( 'imgBackup', false ) ) {
				$this->backup_image( $image, true );
			}

			// Most browsers do not honor EXIF's Orientation header: rotate the image to prevent display issues.
			$this->correct_exif_rotation( $image, true );

			// Create resized version of image.
			if ( $settings->get( 'imgAutoResize', false ) ) {
				$this->generate_resized_image( $image, true );
			}

			// Generate a thumbnail for the image.
			$this->generate_thumbnail( $image );

			// Set gallery preview image if missing.
			GalleryMapper::get_instance()->set_preview_image( $dst_gallery, $image_id, true );

			// Automatically watermark the main image if requested.
			if ( $settings->get( 'watermark_automatically_at_upload', 0 ) ) {
				$image_abspath = $this->get_image_abspath( $image, 'full' );
				$this->generate_image_clone( $image_abspath, $image_abspath, [ 'watermark' => true ] );
			}

			// Notify other plugins that an image has been added.
			do_action( 'ngg_added_new_image', $image );

			// delete dirsize after adding new images.
			delete_transient( 'dirsize_cache' );

			// Seems redundant to above hook. Maintaining for legacy purposes.
			do_action(
				'ngg_after_new_images_added',
				is_numeric( $dst_gallery ) ? $dst_gallery : $dst_gallery->gid,
				[ $image_id ]
			);

			return $image_id;

		} else {
			throw new \E_EntityNotFoundException();
		}

		return null;
	}

	/**
	 * Generates a specific size for an image
	 *
	 * @param Image      $image
	 * @param string     $size
	 * @param array|null $params (optional)
	 * @param bool       $skip_defaults (optional)
	 * @return bool|object
	 */
	public function generate_image_size( $image, $size, $params = null, $skip_defaults = false ) {
		$retval = false;

		// Get the image entity.
		if ( is_numeric( $image ) ) {
			$image = $this->image_mapper->find( $image );
		}

		// Ensure we have a valid image.
		if ( $image ) {
			$params   = $this->get_image_size_params( $image, $size, $params, $skip_defaults );
			$settings = Settings::get_instance();

			// Get the image filename.
			$filename = $this->get_image_abspath( $image, 'full' );

			if ( ! @file_exists( $filename ) ) {
				return false; // bail out if the file doesn't exist.
			}

			$thumbnail = null;

			if ( $size == 'full' && $settings->get( 'imgBackup' ) == 1 ) {
				$backup_path = $this->get_backup_abspath( $image );

				if ( ! @file_exists( $backup_path ) ) {
					@copy( $filename, $backup_path );
				}
			}

			// Generate the thumbnail using WordPress.
			$existing_image_abpath = $this->get_image_abspath( $image, $size );
			$existing_image_dir    = dirname( $existing_image_abpath );

			\wp_mkdir_p( $existing_image_dir );

			$clone_path = $existing_image_abpath;
			$thumbnail  = $this->generate_image_clone( $filename, $clone_path, $params );

			// We successfully generated the thumbnail.
			if ( $thumbnail != null ) {
				$clone_path = $thumbnail->fileName;

				if ( function_exists( 'getimagesize' ) ) {
					$dimensions = getimagesize( $clone_path );
				} else {
					$dimensions = [ $params['width'], $params['height'] ];
				}

				if ( ! isset( $image->meta_data ) ) {
					$image->meta_data = [];
				}

				$size_meta = [
					'width'     => $dimensions[0],
					'height'    => $dimensions[1],
					'filename'  => I18N::mb_basename( $clone_path ),
					'generated' => microtime(),
				];

				if ( isset( $params['crop_frame'] ) ) {
					$size_meta['crop_frame'] = $params['crop_frame'];
				}

				$image->meta_data[ $size ] = $size_meta;

				if ( $size == 'full' ) {
					$image->meta_data['width']  = $size_meta['width'];
					$image->meta_data['height'] = $size_meta['height'];
				}

				$retval = $this->image_mapper->save( $image );

				\do_action( 'ngg_generated_image', $image, $size, $params );

				if ( $retval == 0 ) {
					$retval = false;
				}

				if ( $retval ) {
					$retval = $thumbnail;
				}
			}
		}

		return $retval;
	}

	/**
	 * Generates a thumbnail for an image
	 *
	 * @param Image $image
	 * @return bool
	 */
	public function generate_thumbnail( $image, $params = null, $skip_defaults = false ) {
		$sized_image = $this->generate_image_size( $image, 'thumbnail', $params, $skip_defaults );
		$retval      = false;

		if ( $sized_image != null ) {
			$retval = true;
			$sized_image->destruct();
		}

		if ( is_admin() && ( $image = ImageMapper::get_instance()->find( $image ) ) ) {
			$app = Router::get_instance()->get_routed_app();

			$image->thumb_url = $app->set_parameter_value(
				'timestamp',
				time(),
				null,
				$this->get_image_url( $image, 'thumb' ),
				$app->get_routed_url( true )
			);

			$event            = new \stdClass();
			$event->pid       = $image->{$image->id_field};
			$event->id_field  = $image->id_field;
			$event->thumb_url = $image->thumb_url;

			EventPublisher::get_instance()->add_event(
				[
					'event' => 'thumbnail_modified',
					'image' => $event,
				]
			);
		}

		return $retval;
	}

	/**
	 * Gets the absolute path of the backup of an original image
	 *
	 * @param object|string $image
	 * @return null|string
	 */
	public function get_backup_abspath( $image ) {
		$retval = null;

		if ( ( $image_path = $this->get_image_abspath( $image ) ) ) {
			$retval = $image_path . '_backup';
		}

		return $retval;
	}

	public function get_backup_dimensions( $image ) {
		return $this->get_image_dimensions( $image, 'backup' );
	}

	/**
	 * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image
	 *
	 * @param int|object $image
	 * @param string     $size (optional) Default = full
	 * @param bool       $check_existence (optional) Default = false
	 * @return string
	 */
	public function get_image_abspath( $image, $size = 'full', $check_existence = false ) {
		$image_id = is_numeric( $image ) ? $image : $image->pid;
		$size     = $this->normalize_image_size_name( $size );
		$key      = strval( $image_id ) . $size;

		if ( $check_existence || ! isset( self::$image_abspath_cache[ $key ] ) ) {
			self::$image_abspath_cache[ $key ] = $this->get_computed_image_abspath( $image, $size, $check_existence );
		}

		return self::$image_abspath_cache[ $key ];
	}

	/**
	 * Gets the url of a particular-sized image
	 *
	 * @param int|object $image
	 * @param string     $size
	 * @return string
	 */
	public function get_image_url( $image, $size = 'full' ) {
		$retval   = null;
		$image_id = is_numeric( $image ) ? $image : $image->pid;
		$key      = strval( $image_id ) . $size;
		$success  = true;

		if ( ! isset( self::$image_url_cache[ $key ] ) ) {
			$url = $this->get_computed_image_url( $image, $size );
			if ( $url ) {
				self::$image_url_cache[ $key ] = $url;
				$success                       = true;
			} else {
				$success = false;
			}
		}
		if ( $success ) {
			$retval = self::$image_url_cache[ $key ];
		} else {
			$dynthumbs = \Imagely\NGG\DynamicThumbnails\Manager::get_instance();
			if ( $dynthumbs->is_size_dynamic( $size ) ) {
				$params = $dynthumbs->get_params_from_name( $size );
				$retval = Router::get_instance()->get_url(
					$dynthumbs->get_image_uri( $image, $params ),
					false,
					'root'
				);
			}
		}

		return apply_filters( 'ngg_get_image_url', $retval, $image, $size );
	}

	/**
	 * Flushes the cache we use for path/url calculation for images
	 */
	public function flush_image_path_cache( $image, $size ) {
		$image = is_numeric( $image ) ? $image : $image->pid;
		$key   = strval( $image ) . $size;

		unset( self::$image_abspath_cache[ $key ] );
		unset( self::$image_url_cache[ $key ] );
	}

	/**
	 * @param string        $abspath
	 * @param int           $gallery_id
	 * @param bool          $create_new_gallerypath
	 * @param null|string   $gallery_title
	 * @param array[string] $filenames
	 * @return array|bool false on failure
	 */
	public function import_gallery_from_fs( $abspath, $gallery_id = null, $create_new_gallerypath = true, $gallery_title = null, $filenames = [] ) {
		if ( @ ! file_exists( $abspath ) ) {
			return false;
		}

		$fs = Filesystem::get_instance();

		$retval = [ 'image_ids' => [] ];

		// Ensure that this folder has images.
		$files       = [];
		$directories = [];
		foreach ( scandir( $abspath ) as $file ) {
			if ( $file == '.' || $file == '..' || strtoupper( $file ) == '__MACOSX' ) {
				continue;
			}

			$file_abspath = $fs->join_paths( $abspath, $file );

			// Omit 'hidden' directories prefixed with a period.
			if ( is_dir( $file_abspath ) && strpos( $file, '.' ) !== 0 ) {
				$directories[] = $file_abspath;
			} elseif ( $this->is_image_file( $file_abspath ) ) {
				if ( $filenames && array_search( $file_abspath, $filenames ) !== false ) {
					$files[] = $file_abspath;
				} elseif ( ! $filenames ) {
					$files[] = $file_abspath;
				}
			}
		}

		if ( empty( $files ) && empty( $directories ) ) {
			return false;
		}

		// Get needed utilities.
		$gallery_mapper = GalleryMapper::get_instance();

		// Recurse through the directory and pull in all of the valid images we find.
		if ( ! empty( $directories ) ) {
			foreach ( $directories as $dir ) {
				$subImport = $this->import_gallery_from_fs( $dir, $gallery_id, $create_new_gallerypath, $gallery_title, $filenames );
				if ( $subImport ) {
					$retval['image_ids'] = array_merge( $retval['image_ids'], $subImport['image_ids'] );
				}
			}
		}

		// If no gallery has been specified, then use the directory name as the gallery name.
		if ( ! $gallery_id ) {
			// Create the gallery.
			$gallery = $gallery_mapper->create(
				[
					'title' => $gallery_title ? $gallery_title : I18N::mb_basename( $abspath ),
				]
			);

			if ( ! $create_new_gallerypath ) {
				$gallery_root  = $fs->get_document_root( 'gallery' );
				$gallery->path = str_ireplace( $gallery_root, '', $abspath );
			}

			// Save the gallery.
			if ( $gallery->save() ) {
				$gallery_id = $gallery->id();
			}
		}

		// Ensure that we have a gallery id.
		if ( ! $gallery_id ) {
			return false;
		} else {
			$retval['gallery_id'] = $gallery_id;
		}

		// Remove full sized image if backup is included.
		$files_to_import = [];
		foreach ( $files as $file_abspath ) {

			if ( preg_match( '#_backup$#', $file_abspath ) ) {
				$files_to_import[] = $file_abspath;
				continue;
			} elseif ( in_array( [ $file_abspath . '_backup', 'thumbs_' . $file_abspath, 'thumbs-' . $file_abspath ], $files ) ) {
				continue;
			}

			$files_to_import[] = $file_abspath;
		}

		foreach ( $files_to_import as $file_abspath ) {
			$basename = preg_replace( '#_backup$#', '', pathinfo( $file_abspath, PATHINFO_BASENAME ) );
			if ( $this->is_image_file( $file_abspath ) ) {
				if ( ( $image_id = $this->import_image_file( $gallery_id, $file_abspath, $basename, false, false, false ) ) ) {
					$retval['image_ids'][] = $image_id;
				}
			}
		}

		// Add the gallery name to the result.
		if ( ! isset( $gallery ) ) {
			$gallery = $gallery_mapper->find( $gallery_id );
		}

		$retval['gallery_name'] = $gallery->title;
		return $retval;
	}

	public function maybe_base64_decode( $data ) {
		$decoded = base64_decode( $data );
		if ( $decoded === false ) {
			return $data;
		} elseif ( base64_encode( $decoded ) == $data ) {
			return base64_decode( $data );
		}
		return $data;
	}

	public static function register_custom_post_types() {
		$types = [
			'ngg_album'    => 'NextGEN Gallery - Album',
			'ngg_gallery'  => 'NextGEN Gallery - Gallery',
			'ngg_pictures' => 'NextGEN Gallery - Image',
		];

		foreach ( $types as $type => $label ) {
			\register_post_type(
				$type,
				[
					'label'               => $label,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
				]
			);
		}
	}

	/**
	 * Sanitizes a directory path, replacing whitespace with dashes.
	 *
	 * Taken from WP' sanitize_file_name() and modified to not act on file extensions.
	 *
	 * Removes special characters that are illegal in filenames on certain
	 * operating systems and special characters requiring special escaping
	 * to manipulate at the command line. Replaces spaces and consecutive
	 * dashes with a single dash. Trims period, dash and underscore from beginning
	 * and end of filename. It is not guaranteed that this function will return a
	 * filename that is allowed to be uploaded.
	 *
	 * @param string $dirname The directory name to be sanitized
	 * @return string The sanitized directory name
	 */
	public function sanitize_directory_name( $dirname ) {
		$dirname_raw   = $dirname;
		$special_chars = [ '?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+', chr( 0 ) ];
		$special_chars = apply_filters( 'sanitize_file_name_chars', $special_chars, $dirname_raw );
		$dirname       = preg_replace( "#\x{00a0}#siu", ' ', $dirname );
		$dirname       = str_replace( $special_chars, '', $dirname );
		$dirname       = str_replace( [ '%20', '+' ], '-', $dirname );
		$dirname       = preg_replace( '/[\r\n\t -]+/', '-', $dirname );
		$dirname       = trim( $dirname, '.-_' );
		return $dirname;
	}

	public function sanitize_filename_for_db( $filename = null ) {
		$filename = $filename ? $filename : uniqid( 'nextgen-gallery' );
		$filename = preg_replace( '#^/#', '', $filename );
		$filename = sanitize_file_name( $filename );
		if ( preg_match( '/\-(png|jpg|gif|jpeg|jpg_backup)$/i', $filename, $match ) ) {
			$filename = str_replace( $match[0], '.' . $match[1], $filename );
		}
		return $filename;
	}

	/**
	 * Sets a NGG image as a post thumbnail for the given post
	 *
	 * @param int   $postId
	 * @param Image $image
	 * @param bool  $only_create_attachment
	 * @return int
	 */
	public function set_post_thumbnail( $postId, $image, $only_create_attachment = false ) {
		$retval = false;

		// Get the post ID.
		if ( is_object( $postId ) ) {
			$post   = $postId;
			$postId = isset( $post->ID ) ? $post->ID : $post->post_id;
		}

		// Get the image.
		if ( is_int( $image ) ) {
			$imageId = $image;
			$mapper  = ImageMapper::get_instance();
			$image   = $mapper->find( $imageId );
		}

		if ( $image && $postId ) {
			$attachment_id = $this->is_in_media_library( $image->pid );

			if ( $attachment_id === false ) {
				$attachment_id = $this->copy_to_media_library( $image );
			}

			if ( $attachment_id ) {
				if ( ! $only_create_attachment ) {
					set_post_thumbnail( $postId, $attachment_id );
				}
				$retval = $attachment_id;
			}
		}

		return $retval;
	}

	/**
	 * Uploads an image for a particular gallery
	 *
	 * @param int|object|Gallery $gallery
	 * @param string|bool        $filename (optional) Specifies the name of the file
	 * @param string|bool        $data (optional) If specified, expects base64 encoded string of data
	 *
	 * @return array|array[]|bool|int $image
	 */
	public function upload_image( $gallery, $filename = false, $data = false ) {

		// Ensure that we have the data present that we require.
		//
		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_FILES['file'] )
			&& 0 === $_FILES['file']['error']
			&& isset( $_FILES['file']['tmp_name'] )
			&& isset( $_REQUEST['nonce'] )
			&& Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
			$file = $_FILES['file'];

			if ( $this->is_zip() ) {
				$retval = $this->upload_zip( $gallery );
			} elseif ( $this->is_image_file() ) {
				$retval = $this->import_image_file(
					$gallery,
					$file['tmp_name'],
					$filename ? $filename : ( isset( $file['name'] ) ? $file['name'] : false ),
					false,
					false,
					true
				);
			} else {
				// Remove the non-valid (and potentially insecure) file from the PHP upload directory.
				if ( isset( $_FILES['file']['tmp_name'] ) ) {
					$filename = $_FILES['file']['tmp_name'];
					@unlink( $filename );
				}
				throw new \E_UploadException( __( 'Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery' ) );
			}
		} elseif ( $data ) {
			$retval = $this->upload_base64_image(
				$gallery,
				$data,
				$filename
			);
		} else {
			throw new \E_UploadException();
		}

		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $retval;
	}

	/**
	 * Uploads base64 file to a gallery
	 *
	 * @param int|\stdClass|Gallery   $gallery
	 * @param string                  $data base64-encoded string of data representing the image
	 * @param string|false (optional) $filename specifies the name of the file
	 * @param int|false               $image_id (optional)
	 * @param bool                    $override (optional)
	 *
	 * @return bool|int
	 */
	public function upload_base64_image( $gallery, $data, $filename = false, $image_id = false, $override = false, $move = false ) {
		$temp_abspath = tempnam( sys_get_temp_dir(), '' );

		// Try writing the image.
		$fp = fopen( $temp_abspath, 'wb' );
		fwrite( $fp, $this->maybe_base64_decode( $data ) );
		fclose( $fp );

		return $this->import_image_file( $gallery, $temp_abspath, $filename, $image_id, $override, $move );
	}

	/**
	 * @param int $gallery_id
	 * @return array|bool
	 */
	public function upload_zip( $gallery_id ) {
		if ( ! $this->is_zip() ) {
			return false;
		}

		// Security::verify_nonce() is a wrapper to wp_verify_nonce().
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['nonce'] )
			|| ! Security::verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'nextgen_upload_image' ) ) {
			return false;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$retval = false;

		$memory_limit = intval( ini_get( 'memory_limit' ) );
		if ( ! extension_loaded( 'suhosin' ) && $memory_limit < 256 ) {
			@ini_set( 'memory_limit', '256M' );
		}

		$fs = Filesystem::get_instance();

		// Uses the WordPress ZIP abstraction API.
		include_once $fs->join_paths( ABSPATH, 'wp-admin', 'includes', 'file.php' );
		WP_Filesystem( false, get_temp_dir(), true );

		// Ensure that we truly have the gallery id.
		$gallery_id = $this->get_gallery_id( $gallery_id );

		// The nonce was already checked above, by Security::verify_nonce(). Also PHP-CS still flags this particular
		// line when using phpcs:ignore, thus the disable/enable pairing found here.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$zipfile = $_FILES['file']['tmp_name'];
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$dest_path = implode(
			DIRECTORY_SEPARATOR,
			[
				rtrim( get_temp_dir(), '/\\' ),
				'unpacked-' . I18N::mb_basename( $zipfile ),
			]
		);

		// Attempt to extract the zip file into the normal system directory.
		$extracted = $this->extract_zip( $zipfile, $dest_path );

		// Now verify it worked. get_temp_dir() will check each of the following directories to ensure they are
		// a directory and against wp_is_writable(). Should ALL of those options fail we will fallback to wp_upload_dir().
		$size  = 0;
		$files = glob( $dest_path . DIRECTORY_SEPARATOR . '*' );
		foreach ( $files as $file ) {
			if ( is_array( stat( $file ) ) ) {
				$size += filesize( $file );
			}
		}

		// Extraction failed; attempt again with wp_upload_dir().
		if ( $size == 0 ) {
			// Remove the empty directory we may have possibly created but could not write to.
			$this->delete_directory( $dest_path );

			$destination      = wp_upload_dir();
			$destination_path = $destination['basedir'];
			$dest_path        = implode(
				DIRECTORY_SEPARATOR,
				[
					rtrim( $destination_path, '/\\' ),
					rand(),
					'unpacked-' . I18N::mb_basename( $zipfile ),
				]
			);

			$extracted = $this->extract_zip( $zipfile, $dest_path );
		}

		if ( $extracted ) {
			$retval = $this->import_gallery_from_fs( $dest_path, $gallery_id );
		}

		$this->delete_directory( $dest_path );

		if ( ! extension_loaded( 'suhosin' ) ) {
			@ini_set( 'memory_limit', $memory_limit . 'M' );
		}

		return $retval;
	}

	public static function wp_query_order_by( $order_by, $wp_query ) {
		if ( $wp_query->get( 'datamapper_attachment' ) ) {
			$order_parts = explode( ' ', $order_by );
			$order_name  = array_shift( $order_parts );
			$order_by    = 'ABS(' . $order_name . ') ' . implode( ' ', $order_parts ) . ', ' . $order_by;
		}

		return $order_by;
	}


	/**
	 * Gets the id of an image, regardless of whether an integer or object was passed as an argument.
	 *
	 * This method is, as of 3.50's release, used by EWWW and WP-SmushIt
	 *
	 * @param object|int $image_obj_or_id
	 * @return null|int
	 * @deprecated
	 */
	function _get_image_id( $image_obj_or_id ) {
		$retval = null;

		$image_key = $this->_image_mapper->get_primary_key_column();
		if ( is_object( $image_obj_or_id ) ) {
			if ( isset( $image_obj_or_id->$image_key ) ) {
				$retval = $image_obj_or_id->$image_key;
			}
		} elseif ( is_numeric( $image_obj_or_id ) ) {
			$retval = $image_obj_or_id;
		}

		return $retval;
	}
}
