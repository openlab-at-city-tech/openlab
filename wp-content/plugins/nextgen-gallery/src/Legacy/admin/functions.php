<?php
// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed -- Legacy file structure.

/**
 * Class for admin operation
 *
 * @package NextGEN Gallery
 * @author Alex Rabe
 *
 * @access public
 */
class nggAdmin {

	/**
	 * Scan folder for new images
	 *
	 * @class nggAdmin
	 * @param string $dirname
	 * @return array $files list of image filenames
	 */
	public static function scandir( $dirname = '.' ) {
		$ext = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );

		$files  = [];
		$handle = opendir( $dirname );
		if ( $handle ) {
			// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
			while ( false !== ( $file = readdir( $handle ) ) ) {
				$info = \Imagely\NGG\Display\I18N::mb_pathinfo( $file );
				// just look for images with the correct extension.
				if ( isset( $info['extension'] ) ) {
					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( in_array( strtolower( $info['extension'] ), $ext ) ) {
						// phpcs:ignore WordPress.WP.DeprecatedFunctions.seems_utf8Found -- Fallback for WP < 6.9, where wp_is_valid_utf8() does not exist.
						if ( ! ( function_exists( 'wp_is_valid_utf8' ) ? wp_is_valid_utf8( $file ) : seems_utf8( $file ) ) ) {
							$file = mb_convert_encoding( $file, 'UTF-8', 'ISO-8859-1' );
						}

						$files[] = $file;
					}
				}
			}

			closedir( $handle );
		}
		sort( $files );
		return ( $files );
	}

	/**
	 * Creates or recreates a thumbnail
	 *
	 * @class nggAdmin
	 * @param object|int   $image Contain all information about the image or the id
	 * @param integer      $width optional, falls back to the saved thumbnail width
	 * @param integer      $height optional, falls back to the saved thumbnail height
	 * @param boolean|null $crop optional, falls back to the saved fixed-dimension setting
	 * @return string result code
	 * @since v1.0.0
	 */
	public static function create_thumbnail( $image, $width = 0, $height = 0, $crop = null ) {

		if ( is_object( $image ) ) {
			if ( isset( $image->id ) ) {
				$image = $image->id;
			} elseif ( isset( $image->pid ) ) {
				$image = $image->pid;
			}
		}
		$storage = \Imagely\NGG\DataStorage\Manager::get_instance();

		// XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility.
		$params = [
			'watermark'  => false,
			'reflection' => false,
		];

		// One-off dimensions travel as params, so a caller never has to write them to
		// the site-wide options to get a single thumbnail at a different size.
		if ( $width > 0 ) {
			$params['width'] = (int) $width;
		}
		if ( $height > 0 ) {
			$params['height'] = (int) $height;
		}
		if ( null !== $crop ) {
			$params['crop'] = (bool) $crop;
		}

		$result = $storage->generate_thumbnail( $image, $params );

		if ( ! $result ) {
			// XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status.
			return __( 'Error while creating thumbnail.', 'nggallery' );
		}

		// success.
		return '1';
	}

	/**
	 * Creates a new image, based on the height /width
	 *
	 * @class nggAdmin
	 * @param object|int $image Contain all information about the image or the id
	 * @param integer    $width optional
	 * @param integer    $height optional
	 * @return string result code
	 */
	public static function resize_image( $image, $width = 0, $height = 0 ) {
		if ( is_object( $image ) ) {
			if ( isset( $image->id ) ) {
				$image = $image->id;
			} elseif ( isset( $image->pid ) ) {
				$image = $image->pid;
			}
		}

		$storage = \Imagely\NGG\DataStorage\Manager::get_instance();
		// XXX maybe get rid of this...it's needed to get width/height defaults, placing these directly in generate_image_size could have unwanted consequences.
		$settings = \Imagely\NGG\Settings\Settings::get_instance();

		// XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility.
		$params = [
			'watermark'  => false,
			'reflection' => false,
		];

		if ( $width > 0 ) {
			$params['width'] = $width;
		} else {
			$params['width'] = $settings->get( 'imgWidth' );
		}

		if ( $height > 0 ) {
			$params['height'] = $height;
		} else {
			$params['height'] = $settings->get( 'imgHeight' );
		}

		$result = $storage->generate_image_size( $image, 'full', $params );

		if ( ! $result ) {
			// XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status.
			return __( 'Error while resizing image.', 'nggallery' );
		}

		// success.
		return '1';
	}

	/**
	 * Rotated/Flip an image based on the orientation flag or a definded angle
	 *
	 * @param int|object  $image
	 * @param string|bool $dir (optional) CW (clockwise)or CCW (counter clockwise), if set to false, the exif flag will be used
	 * @param string|bool $flip (optional) Either false | V (flip vertical) | H (flip horizontal)
	 * @return string result code
	 */
	public static function rotate_image( $image, $dir = false, $flip = false ) {
		if ( is_object( $image ) ) {
			if ( isset( $image->id ) ) {
				$image = $image->id;
			} elseif ( isset( $image->pid ) ) {
				$image = $image->pid;
			}
		}
		$storage = \Imagely\NGG\DataStorage\Manager::get_instance();

		// XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility.
		$params   = [
			'watermark'  => false,
			'reflection' => false,
		];
		$rotation = null;

		if ( $dir === 'CW' ) {
			$rotation = 90;
		} elseif ( $dir === 'CCW' ) {
			$rotation = -90;
		} elseif ( $dir === false ) {
			// if you didn't define a rotation, we look for the orientation flag in EXIF.
			$meta = new nggMeta( $image );
			$exif = $meta->get_EXIF();

			if ( isset( $exif['Orientation'] ) ) {

				switch ( $exif['Orientation'] ) {
					case 5: // vertical flip + 90 rotate right.
						$flip = 'V';
						// Fall through to set rotation.
					case 6: // 90 rotate right
						$rotation = 90;
						break;
					case 7: // horizontal flip + 90 rotate right.
						$flip = 'H';
						// Fall through to set rotation.
					case 8: // 90 rotate left.
						$rotation = -90;
						break;
					case 4: // vertical flip.
						$flip = 'V';
						break;
					case 3: // 180 rotate left.
						$rotation = -180;
						break;
					case 2: // horizontal flip.
						$flip = 'H';
						break;
					case 1: // no action in the case it doesn't need a rotation.
					default:
						return '0';
				}
			} else {
				return '0';
			}
		}

		if ( $rotation != null ) {
			$params['rotation'] = $rotation;
		}

		if ( $flip != null ) {
			$params['flip'] = $flip;
		}

		$result = $storage->generate_image_size( $image, 'full', $params );

		if ( ! $result ) {
			// XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status.
			return __( 'Error while rotating image.', 'nggallery' );
		}

		// success.
		return '1';
	}

	/**
	 * Sets the watermark for the image
	 *
	 * @class nggAdmin
	 * @param object|int $image Contain all information about the image or the id
	 * @return string result code
	 */
	public static function set_watermark( $image ) {

		if ( is_object( $image ) ) {
			if ( isset( $image->id ) ) {
				$image = $image->id;
			} elseif ( isset( $image->pid ) ) {
				$image = $image->pid;
			}
		}

		$storage = \Imagely\NGG\DataStorage\Manager::get_instance();

		// XXX NextGEN Legacy was only handling watermarks at this stage, so we're forcefully disabling all else.
		$params = [
			'watermark'  => true,
			'reflection' => false,
			'crop'       => false,
		];
		$result = $storage->generate_image_size( $image, 'full', $params );

		if ( ! $result ) {
			// XXX there isn't any error handling unfortunately at the moment in the generate_thumbnail functions, need a way to return proper error status.
			return __( 'Error while applying watermark to image.', 'nggallery' );
		}

		// success.
		return '1';
	}

	/**
	 * Recover image from backup copy and reprocess it
	 *
	 * @class nggAdmin
	 * @since 1.5.0
	 * @param object|int $image Contain all information about the image or the id
	 * @return string result code
	 */
	public static function recover_image( $image ) {
		return \Imagely\NGG\DataStorage\Manager::get_instance()->recover_image( $image );
	}

	/**
	 * Add images to database
	 *
	 * @class nggAdmin
	 * @param int   $galleryID
	 * @param array $imageslist
	 * @return array $image_ids IDs which have been successfully added
	 */
	public static function add_Images( $galleryID, $imageslist ) {
		global $ngg;

		$image_ids = [];

		if ( is_array( $imageslist ) ) {
			foreach ( $imageslist as $picture ) {

				// filter function to rename/change/modify image before.
				$picture = apply_filters( 'ngg_pre_add_new_image', $picture, $galleryID );

				// strip off the extension of the filename.
				$path_parts = \Imagely\NGG\Display\I18N::mb_pathinfo( $picture );
				$alttext    = ( ! isset( $path_parts['filename'] ) ) ? substr( $path_parts['basename'], 0, strpos( $path_parts['basename'], '.' ) ) : $path_parts['filename'];

				// save it to the database.
				$pic_id = nggdb::add_image( $galleryID, $picture, '', $alttext );

				if ( \Imagely\NGG\Settings\Settings::get_instance()->imgBackup && ! empty( $pic_id ) ) {
					$storage = \Imagely\NGG\DataStorage\Manager::get_instance();
					$storage->backup_image( $pic_id );
				}

				if ( ! empty( $pic_id ) ) {
					$image_ids[] = $pic_id;
				}

				// add the metadata.
				self::import_MetaData( $pic_id );

				// auto rotate.
				self::rotate_image( $pic_id );

				// Autoresize image if required.
				if ( $ngg->options['imgAutoResize'] ) {
					$imagetmp = nggdb::find_image( $pic_id );
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					$sizetmp   = @getimagesize( $imagetmp->imagePath );
					$widthtmp  = $ngg->options['imgWidth'];
					$heighttmp = $ngg->options['imgHeight'];
					if ( ( $sizetmp[0] > $widthtmp && $widthtmp ) || ( $sizetmp[1] > $heighttmp && $heighttmp ) ) {
						self::resize_image( $pic_id );
					}
				}

				// action hook for post process after the image is added to the database.
				$image = [
					'id'        => $pic_id,
					'filename'  => $picture,
					'galleryID' => $galleryID,
				];
				do_action( 'ngg_added_new_image', $image );
			}
		}

		// delete dirsize after adding new images.
		delete_transient( 'dirsize_cache' );

		do_action( 'ngg_after_new_images_added', $galleryID, $image_ids );

		return $image_ids;
	}

	/**
	 * Import some meta data into the database (if avialable)
	 *
	 * @class nggAdmin
	 * @param array|int $imagesIds
	 * @return string result code
	 */
	public static function import_MetaData( $imagesIds ) {

		global $wpdb;

		require_once NGGALLERY_ABSPATH . '/lib/image.php';

		if ( ! is_array( $imagesIds ) ) {
			$imagesIds = [ $imagesIds ];
		}

		foreach ( $imagesIds as $imageID ) {

			// Get the image.
			$image = null;
			if ( is_int( $imageID ) ) {
				$image = \Imagely\NGG\DataMappers\Image::get_instance()->find( $imageID );
			} else {
				$image = $imageID;
			}

			if ( $image ) {

				$meta = self::get_MetaData( $image );

				// get the title.
				$alttext = empty( $meta['title'] ) ? $image->alttext : $meta['title'];

				// get the caption / description field.
				$description = empty( $meta['caption'] ) ? $image->description : $meta['caption'];

				// SECURITY (#1110): IPTC title/caption are attacker-controllable file
				// metadata and were written to nggpictures.alttext/description with a raw
				// prepared UPDATE below, bypassing the sanitisation every other write path
				// applies. The stored value later reaches a JS innerHTML sink in the
				// Shutter lightbox and executes for any visitor. Run both through the same
				// I18N sanitiser the normal admin save path (update_pictures()) uses, so an
				// imported value is neutralised identically to a hand-edited one.
				$alttext     = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $alttext );
				$description = \Imagely\NGG\Display\I18N::ngg_sanitize_text_alt_title_desc( $description );

				// get the file date/time from exif.
				$timestamp = $meta['timestamp'];

				// first update database.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$result = $wpdb->query(
					$wpdb->prepare(
						"UPDATE $wpdb->nggpictures SET
                        alttext = %s,
                        description = %s,
                        imagedate = %s
                    WHERE pid = %d",
						$alttext,
						$description,
						$timestamp,
						$image->pid
					)
				);

				if ( $result === false ) {
					return ' <strong>' . esc_html( $image->filename ) . ' ' . __( '(Error : Couldn\'t not update data base)', 'nggallery' ) . '</strong>';
				}

				// this flag will inform us that the import is already one time performed.
				$meta['common']['saved'] = true;
				$result                  = nggdb::update_image_meta( $image->pid, $meta['common'] );

				if ( $result === false ) {
					return ' <strong>' . esc_html( $image->filename ) . ' ' . __( '(Error : Couldn\'t not update meta data)', 'nggallery' ) . '</strong>';
				}

				// add the tags if we found some.
				if ( $meta['keywords'] ) {
					$taglist = explode( ',', $meta['keywords'] );
					wp_set_object_terms( $image->pid, $taglist, 'ngg_tag' );
				}
			} else {
				return ' <strong>' . esc_html( $image->filename ) . ' ' . __( '(Error : Couldn\'t not find image)', 'nggallery' ) . '</strong>'; // error check.
			}
		}

		return '1';
	}

	/**
	 * Gets metadata for an image
	 *
	 * @class nggAdmin
	 * @require NextGEN Meta class
	 * @param int|object $image_or_id
	 * @return array metadata
	 */
	public static function get_MetaData( $image_or_id ) {

		require_once NGGALLERY_ABSPATH . '/lib/meta.php';

		$meta = [];

		$pdata = new nggMeta( $image_or_id );

		$meta['title']     = trim( $pdata->get_META( 'title' ) );
		$meta['caption']   = trim( $pdata->get_META( 'caption' ) );
		$meta['keywords']  = trim( $pdata->get_META( 'keywords' ) );
		$meta['timestamp'] = $pdata->get_date_time();
		// this contain other useful meta information.
		$meta['common'] = $pdata->get_common_meta();
		// hook for addon plugin to add more meta fields.
		$meta = apply_filters( 'ngg_get_image_metadata', $meta, $pdata );

		return $meta;
	}

	/**
	 * Imports a gallery
	 * TODO: Check permission of existing thumb folder & images
	 *
	 * @param string $galleryfolder contains relative path to the gallery itself
	 * @param int    $gallery_id
	 * @return void
	 */
	public static function import_gallery( $galleryfolder, $gallery_id = null ) {
		global $wpdb, $user_ID;

		// get the current user ID.
		wp_get_current_user();

		$created_msg = '';

		// remove trailing slash at the end, if somebody use it.
		$galleryfolder = untrailingslashit( $galleryfolder );

		$fs = \Imagely\NGG\Util\Filesystem::get_instance();
		if ( is_null( $gallery_id ) ) {
			$gallerypath = $fs->join_paths( $fs->get_document_root( 'content' ), $galleryfolder );
		} else {
			$storage     = \Imagely\NGG\DataStorage\Manager::get_instance();
			$gallerypath = $storage->get_gallery_abspath( $gallery_id );
		}

		if ( ! is_dir( $gallerypath ) ) {
			/* translators: %s: directory path */
			nggGallery::show_error( sprintf( __( 'Directory <strong>%s</strong> doesn&#96;t exist!', 'nggallery' ), esc_html( $gallerypath ) ) );
			return;
		}

		// read list of images.
		$new_imageslist = self::scandir( $gallerypath );

		if ( empty( $new_imageslist ) ) {
			/* translators: %s: directory path */
			nggGallery::show_message( sprintf( __( 'Directory <strong>%s</strong> contains no pictures', 'nggallery' ), esc_html( $gallerypath ) ) );
			return;
		}

		// take folder name as gallery name.
		$galleryname = basename( $galleryfolder );
		$galleryname = apply_filters( 'ngg_gallery_name', $galleryname );

		// check for existing gallery folder.
		if ( is_null( $gallery_id ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$gallery_id = $wpdb->get_var( $wpdb->prepare( "SELECT gid FROM {$wpdb->nggallery} WHERE path = %s", [ $galleryfolder ] ) );
		}

		if ( ! $gallery_id ) {
			// now add the gallery to the database.
			$gallery_id = nggdb::add_gallery( $galleryname, $galleryfolder, '', 0, 0, $user_ID );

			if ( ! $gallery_id ) {
				nggGallery::show_error( __( 'Database error. Could not add gallery!', 'nggallery' ) );
				return;
			} else {
				do_action( 'ngg_created_new_gallery', $gallery_id );
			}

			$created_msg = sprintf(
				/* translators: %s: gallery name */
				_n( 'Gallery <strong>%s</strong> successfully created!', 'Galleries <strong>%s</strong> successfully created!', 1, 'nggallery' ),
				esc_html( $galleryname )
			);
		}

		// Look for existing image list.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$old_imageslist = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `filename` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d",
				[
					$gallery_id,
				]
			)
		);

		// if no images are there, create empty array.
		if ( $old_imageslist == null ) {
			$old_imageslist = [];
		}

		// check difference.
		$new_images = array_diff( $new_imageslist, $old_imageslist );

		// all images must be valid files.
		foreach ( $new_images as $key => $picture ) {
			// filter function to rename/change/modify image before.
			$picture            = apply_filters( 'ngg_pre_add_new_image', $picture, $gallery_id );
			$new_images[ $key ] = $picture;

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( ! @getimagesize( $gallerypath . '/' . $picture ) ) {
				unset( $new_images[ $key ] );
				@unlink( $gallerypath . '/' . $picture ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged
				continue;
			}

			// Re-check existence right before insert: $old_imageslist above can be stale by the
			// time this loop runs (e.g. a concurrent scan request importing the same file), so
			// array_diff() alone isn't a reliable guard against duplicate imports.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$already_imported = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `pid` FROM {$wpdb->nggpictures} WHERE `galleryid` = %d AND `filename` = %s LIMIT 1",
					$gallery_id,
					$picture
				)
			);
			if ( $already_imported ) {
				unset( $new_images[ $key ] );
			}
		}

		// add images to database.
		$image_ids = self::add_Images( $gallery_id, $new_images );
		do_action( 'ngg_after_new_images_added', $gallery_id, $image_ids );

		// add the preview image if needed.
		self::set_gallery_preview( $gallery_id );

		// now create thumbnails.
		self::do_ajax_operation( 'create_thumbnail', $image_ids, __( 'Create new thumbnails', 'nggallery' ) );

		// TODO:Message will not shown, because AJAX routine require more time, message should be passed to AJAX.
		/* translators: %s: number of pictures */
		$message  = $created_msg . sprintf( _n( '%s picture successfully added', '%s pictures successfully added', count( $image_ids ), 'nggallery' ), count( $image_ids ) );
		$message .= ' [<a href="' . admin_url() . 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery_id . '" >';
		$message .= __( 'Edit gallery', 'nggallery' );
		$message .= '</a>]';

		nggGallery::show_message( $message );
	}

	/**
	 * Capability check. Check is the ID fit's to the user_ID
	 *
	 * @class nggAdmin
	 * @param int $check_ID is the user_id
	 * @return bool $result
	 */
	public static function can_manage_this_gallery( $check_ID ) {
		return \Imagely\NGG\Util\Security::can_manage_owned( $check_ID, 'nextgen_edit_gallery' );
	}

	/**
	 * Initate the Ajax operation
	 *
	 * @class nggAdmin
	 * @param string $operation name of the function which should be executed
	 * @param array  $image_array
	 * @param string $title name of the operation
	 * @return string the javascript output
	 */
	public static function do_ajax_operation( $operation, $image_array, $title = '' ) {

		if ( ! is_array( $image_array ) || empty( $image_array ) ) {
			return '';
		}

		// Built with wp_json_encode() rather than implode( '","', ... ) inside a quoted string
		// literal. esc_js() runs _wp_specialchars( $text, ENT_COMPAT ), which entity-encodes the
		// double quotes used as the separator: the ids 9601,9602 became the string
		// 9601&quot;,&quot;9602, so `new Array( "..." )` produced a ONE element array whose single
		// entry was that whole mangled string. The client then posted it as an id, the server
		// absint()-ed it back to 9601, and every dialog-driven bulk operation processed only the
		// first selected image while the progress bar reported completion over maxStep = 1.
		// That is the still-open half of issue #925 (the other half - sanitize_text_field()
		// collapsing the posted doaction[] array to '' - is fixed in manage.php).
		//
		// The ids are integers here, but the JSON is hex-escaped anyway so no value can close the
		// <script> element or break out of the literal.
		$js_array = wp_json_encode(
			array_values( array_map( 'strval', $image_array ) ),
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		);

		// send out some JavaScript, which initate the ajax operation.
		ob_start();
		?>
		<script type="text/javascript">

			Images = <?php echo $js_array; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode with JSON_HEX_* produces a self-contained, script-safe JS array literal; esc_js() would corrupt it. ?>;

			nggAjaxOptions = {
				operation: "<?php echo esc_js( $operation ); ?>",
				ids: Images,
				header: "<?php echo esc_js( $title ); ?>",
				maxStep: Images.length
			};

			(function($) {
				$(function() {
					nggProgressBar.init(nggAjaxOptions);
					nggAjax.init(nggAjaxOptions);
				});
			})(jQuery);
		</script>
		<?php
		$script = ob_get_clean();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $script contains safe JavaScript for AJAX operations
		echo $script;
		return $script;
	}

	/**
	 * Defines a preview pic after the first upload, can be changed in the gallery settings
	 *
	 * @class nggAdmin
	 * @param int $galleryID
	 * @return void
	 */
	public static function set_gallery_preview( $galleryID ) {
		$gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$gallery        = $gallery_mapper->find( $galleryID );
		if ( $gallery ) {
			if ( ! $gallery->previewpic ) {
				$image_mapper = \Imagely\NGG\DataMappers\Image::get_instance();
				$image        = $image_mapper->select()
										->where( [ 'galleryid = %d', $galleryID ] )
										->where( [ 'exclude != 1' ] )
										->order_by( $image_mapper->get_primary_key_column() )
										->limit( 1 )
										->run_query();
				if ( $image ) {
					$gallery->previewpic = $image->{$image->id_field};
					$gallery_mapper->save( $gallery );
				}
			}
		}
	}

	/**
	 * Return a JSON coded array of Image ids for a requested gallery
	 *
	 * @class nggAdmin
	 * @param int $galleryID
	 * @return string|int (JSON)
	 */
	public static function get_image_ids( $galleryID ) {
		if ( ! function_exists( 'json_encode' ) ) {
			return( -2 );
		}

		$gallery = nggdb::get_ids_from_gallery( $galleryID, 'pid', 'ASC', false );

		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );
		return wp_json_encode( $gallery );
	}

	/**
	 * Deprecated function, restored to fix compatibility with "NextGen Public Uploader"
	 *
	 * @deprecated
	 * @class nggAdmin
	 * @param string $filename
	 * @return bool $result
	 */
	public function chmod( $filename = '' ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$stat  = @stat( dirname( $filename ) );
		$perms = $stat['mode'] & 0000666;
		if ( @chmod( $filename, $perms ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod, WordPress.PHP.NoSilencedErrors.Discouraged
			return true;
		}
		return false;
	}
} // END class nggAdmin

// XXX temporary...used as a quick fix to refresh I_Settings_Manager when the nextgen option is updated manually in order to run Hooks etc.
function ngg_refreshSavedSettings(): bool {
	$settings = \Imagely\NGG\Settings\Settings::get_instance();

	if ( $settings != null ) {
		$width         = $settings->get( 'thumbwidth' );
		$height        = $settings->get( 'thumbheight' );
		$new_dimension = "{$width}x{$height}";
		$dimensions    = (array) $settings->get( 'thumbnail_dimensions' );

		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( ! in_array( $new_dimension, $dimensions ) ) {
			$dimensions[] = $new_dimension;
			$settings->set( 'thumbnail_dimensions', $dimensions );
			$settings->save();
			return true;
		}
	}

	return false;
}
