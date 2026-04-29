<?php

namespace Imagely\NGG\DataStorage;

use Imagely\NGG\DataMappers\Image as ImageMapper;

/**
 * Handles reading and processing of image metadata including EXIF, IPTC, and XMP data.
 *
 * This class extracts and processes various metadata formats from image files
 * and provides sanitized access to the metadata information.
 */
class MetaData {

	/**
	 * The image object.
	 *
	 * @var mixed
	 */
	public $image = '';

	/**
	 * Path to the image file.
	 *
	 * @var string
	 */
	public $file_path = '';

	/**
	 * The image size.
	 *
	 * @var array|false
	 */
	public $size = false;

	/**
	 * EXIF data array.
	 *
	 * @var array|false
	 */
	public $exif_data = false;

	/**
	 * IPTC data array.
	 *
	 * @var array|false
	 */
	public $iptc_data = false;

	/**
	 * XMP data array.
	 *
	 * @var array|false
	 */
	public $xmp_data = false;

	/**
	 * Filtered EXIF data array.
	 *
	 * @var array|false
	 */
	public $exif_array = false;

	/**
	 * Filtered IPTC data array.
	 *
	 * @var array|false
	 */
	public $iptc_array = false;

	/**
	 * Filtered XMP data array.
	 *
	 * @var array|false
	 */
	public $xmp_array = false;

	/**
	 * Flag to sanitize meta data on request.
	 *
	 * @var bool
	 */
	public $sanitize = false;

	/**
	 * Class constructor
	 *
	 * @param int  $image Image ID
	 * @param bool $onlyEXIF TRUE = will parse only EXIF data
	 * @return bool FALSE if the file does not exist or metadat could not be read
	 */
	public function __construct( $image, $onlyEXIF = false ) {
		if ( is_numeric( $image ) ) {
			$image = ImageMapper::get_instance()->find( $image );
		}

		$this->image = \apply_filters( 'ngg_find_image_meta', $image );

		$this->file_path = Manager::get_instance()->get_image_abspath( $this->image );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! @file_exists( $this->file_path ) ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->size = @getimagesize( $this->file_path, $metadata );

		if ( $this->size && is_array( $metadata ) ) {
			// get exif - data.
			if ( is_callable( 'exif_read_data' ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$this->exif_data = @exif_read_data( $this->file_path, null, true );
			}

			// stop here if we didn't need other meta data.
			if ( $onlyEXIF ) {
				return;
			}

			// get the iptc data - should be in APP13.
			if ( is_callable( 'iptcparse' ) && isset( $metadata['APP13'] ) ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$this->iptc_data = @iptcparse( $metadata['APP13'] );
			}

			// get the xmp data in a XML format.
			if ( is_callable( 'xml_parser_create' ) ) {
				$this->xmp_data = $this->extract_XMP( $this->file_path );
			}
		}
	}

	/**
	 * Return the saved metadata from the database.
	 *
	 * @since 1.4.0
	 * @param string $key (optional)
	 * @return array|mixed Return either the complete array or the single object.
	 */
	public function get_saved_meta( $key = false ) {

		$meta = $this->image->meta_data;

		// Check if we already import the meta data to the database.
		if ( ! is_array( $meta ) || ! isset( $meta['saved'] ) || ( $meta['saved'] !== true ) ) {
			return false;
		}

		// return one element if requested.
		if ( $key ) {
			return $meta[ $key ];
		}

		// removed saved parameter we don't need that to show.
		unset( $meta['saved'] );

		// and remove empty tags or arrays.
		foreach ( $meta as $key => $value ) {
			if ( empty( $value ) || is_array( $value ) ) {
				unset( $meta[ $key ] );
			}
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $meta, 'esc_html' );
		}

		return $meta;
	}

	/**
	 * Get EXIF data from the image.
	 *
	 * See also http://trac.wordpress.org/changeset/6313
	 *
	 * @param string|false $key Optional object key to get specific EXIF data.
	 * @return bool|array Returns EXIF array or false if no data available.
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- EXIF is an acronym
	public function get_EXIF( $key = false ) {
		if ( ! $this->exif_data ) {
			return false;
		}

		if ( ! is_array( $this->exif_array ) ) {
			$meta = [];

			if ( isset( $this->exif_data['EXIF'] ) ) {
				$exif = $this->exif_data['EXIF'];

				if ( ! empty( $exif['FNumber'] ) ) {
					$meta['aperture'] = 'F ' . round( $this->exif_frac2dec( $exif['FNumber'] ), 2 );
				}
				if ( ! empty( $exif['Model'] ) ) {
					$meta['camera'] = trim( $exif['Model'] );
				}
				if ( ! empty( $exif['DateTimeDigitized'] ) ) {
					$meta['created_timestamp'] = $this->exif_date2ts( $exif['DateTimeDigitized'] );
				} elseif ( ! empty( $exif['DateTimeOriginal'] ) ) {
					$meta['created_timestamp'] = $this->exif_date2ts( $exif['DateTimeOriginal'] );
				} elseif ( ! empty( $exif['FileDateTime'] ) ) {
					$meta['created_timestamp'] = $this->exif_date2ts( $exif['FileDateTime'] );
				}
				if ( ! empty( $exif['FocalLength'] ) ) {
					$meta['focal_length'] = $this->exif_frac2dec( $exif['FocalLength'] ) . __( ' mm', 'nggallery' );
				}
				if ( ! empty( $exif['ISOSpeedRatings'] ) ) {
					$meta['iso'] = $exif['ISOSpeedRatings'];
				}
				if ( ! empty( $exif['ExposureTime'] ) ) {
					$meta['shutter_speed']  = $this->exif_frac2dec( $exif['ExposureTime'] );
					$meta['shutter_speed']  = ( $meta['shutter_speed'] > 0.0 && $meta['shutter_speed'] < 1.0 ) ? ( '1/' . round( 1 / $meta['shutter_speed'], -1 ) ) : ( $meta['shutter_speed'] );
					$meta['shutter_speed'] .= __( ' sec', 'nggallery' );
				}

				// Bit 0 indicates the flash firing status. On some images taken on older iOS versions, this may be
				// incorrectly stored as an array.
				if ( isset( $exif['Flash'] ) && is_array( $exif['Flash'] ) ) {
					$meta['flash'] = __( 'Fired', 'nggallery' );
				} elseif ( ! empty( $exif['Flash'] ) ) {
					$meta['flash'] = ( $exif['Flash'] & 1 ) ? __( 'Fired', 'nggallery' ) : __( 'Not fired', 'nggallery' );
				}
			}

			// additional information.
			if ( isset( $this->exif_data['IFD0'] ) ) {
				$exif = $this->exif_data['IFD0'];

				if ( ! empty( $exif['Model'] ) ) {
					$meta['camera'] = $exif['Model'];
				}
				if ( ! empty( $exif['Make'] ) ) {
					$meta['make'] = $exif['Make'];
				}
				if ( ! empty( $exif['ImageDescription'] ) ) {
					$meta['title'] = $this->utf8_encode( $exif['ImageDescription'] );
				}
				if ( ! empty( $exif['Orientation'] ) ) {
					$meta['Orientation'] = $exif['Orientation'];
				}
			}

			// this is done by Windows.
			if ( isset( $this->exif_data['WINXP'] ) ) {
				$exif = $this->exif_data['WINXP'];

				if ( ! empty( $exif['Title'] ) && empty( $meta['title'] ) ) {
					$meta['title'] = $this->utf8_encode( $exif['Title'] );
				}
				if ( ! empty( $exif['Author'] ) ) {
					$meta['author'] = $this->utf8_encode( $exif['Author'] );
				}
				if ( ! empty( $exif['Keywords'] ) ) {
					$meta['keywords'] = $this->utf8_encode( $exif['Keywords'] );
				}
				if ( ! empty( $exif['Subject'] ) ) {
					$meta['subject'] = $this->utf8_encode( $exif['Subject'] );
				}
				if ( ! empty( $exif['Comments'] ) ) {
					$meta['caption'] = $this->utf8_encode( $exif['Comments'] );
				}
			}

			$this->exif_array = $meta;
		}

		// return one element if requested.
		if ( $key == true ) {
			$value = isset( $this->exif_array[ $key ] ) ? $this->exif_array[ $key ] : false;
			return $value;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->exif_array, 'esc_html' );
		}

		return $this->exif_array;
	}

	/**
	 * Convert a fraction string to a decimal.
	 *
	 * @param string $str Fraction string in format "numerator/denominator".
	 * @return float|string Decimal value or original string if invalid.
	 */
	public function exif_frac2dec( $str ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@list( $n, $d ) = explode( '/', $str );
		if ( ! empty( $d ) ) {
			return $n / $d;
		}
		return $str;
	}

	/**
	 * Convert the EXIF date format to a Unix timestamp.
	 *
	 * @param string $str Date string in EXIF format.
	 * @return int|false Unix timestamp or false if conversion fails.
	 */
	public function exif_date2ts( $str ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$retval = is_numeric( $str ) ? $str : @strtotime( $str );
		if ( ! $retval && $str ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@list( $date, $time ) = explode( ' ', trim( $str ) );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@list( $y, $m, $d ) = explode( ':', $date );
			$retval             = strtotime( "{$y}-{$m}-{$d} {$time}" );

		}
		return $retval;
	}

	/**
	 * Get IPTC Data Information for EXIF display.
	 *
	 * @param string|false $key Optional object key to get specific IPTC data.
	 * @return null|bool|array Returns IPTC array, specific value, or false if no data.
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- IPTC is an acronym
	public function get_IPTC( $key = false ) {
		if ( ! $this->iptc_data ) {
			return false;
		}

		if ( ! is_array( $this->iptc_array ) ) {
			// --------- Set up Array Functions --------- //
			$iptcTags = [
				'2#005' => 'title',
				'2#007' => 'status',
				'2#012' => 'subject',
				'2#015' => 'category',
				'2#025' => 'keywords',
				'2#055' => 'created_date',
				'2#060' => 'created_time',
				'2#080' => 'author',
				'2#085' => 'position',
				'2#090' => 'city',
				'2#092' => 'location',
				'2#095' => 'state',
				'2#100' => 'country_code',
				'2#101' => 'country',
				'2#105' => 'headline',
				'2#110' => 'credit',
				'2#115' => 'source',
				'2#116' => 'copyright',
				'2#118' => 'contact',
				'2#120' => 'caption',
			];

			$meta = [];
			foreach ( $iptcTags as $iptc_tag => $iptc_field ) {
				if ( isset( $this->iptc_data[ $iptc_tag ] ) ) {
					$meta[ $iptc_field ] = trim( $this->utf8_encode( implode( ', ', $this->iptc_data[ $iptc_tag ] ) ) );
				}
			}
			$this->iptc_array = $meta;
		}

		// return one element if requested.
		if ( $key ) {
			return ( isset( $this->iptc_array[ $key ] ) ) ? $this->iptc_array[ $key ] : null;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->iptc_array, 'esc_html' );
		}

		return $this->iptc_array;
	}

	/**
	 * Get XMP data from image file.
	 *
	 * Code by Pekka Saarinen http://photography-on-the.net
	 *
	 * @param string $filename Path to the image file.
	 * @return bool|string XMP data string or false if not found.
	 */
	public function extract_XMP( $filename ) {

		// TODO:Require a lot of memory, could be better.
		ob_start();
		@readfile( $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile, WordPress.PHP.NoSilencedErrors.Discouraged
		$source = ob_get_contents();
		ob_end_clean();

		$start = strpos( $source, '<x:xmpmeta' );
		$end   = strpos( $source, '</x:xmpmeta>' );
		if ( ( ! $start === false ) && ( ! $end === false ) ) {
			$lenght   = $end - $start;
			$xmp_data = substr( $source, $start, $lenght + 12 );
			unset( $source );
			return $xmp_data;
		}

		unset( $source );
		return false;
	}

	/**
	 * Get XMP metadata from the image.
	 *
	 * Taken from http://php.net/manual/en/function.xml-parse-into-struct.php
	 *
	 * @author Alf Marius Foss Olsen & Alex Rabe
	 * @param string|false $key Optional object key to get specific XMP data.
	 * @return bool|array XMP data array or false if no data available.
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- XMP is an acronym
	public function get_XMP( $key = false ) {

		if ( ! $this->xmp_data ) {
			return false;
		}

		if ( ! is_array( $this->xmp_array ) ) {
			$parser = xml_parser_create();
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 ); // Dont mess with my cAsE sEtTings.
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 ); // Dont bother with empty info.
			xml_parse_into_struct( $parser, $this->xmp_data, $values );
			xml_parser_free( $parser );

			$xmlarray        = [];  // The XML array.
			$this->xmp_array = [];  // The returned array.
			$stack           = [];  // tmp array used for stacking.
			$list_array      = [];  // tmp array for list elements.
			$list_element    = false;    // rdf:li indicator.

			foreach ( $values as $val ) {

				if ( $val['type'] == 'open' ) {
					array_push( $stack, $val['tag'] );

				} elseif ( $val['type'] == 'close' ) {
					// reset the compared stack.
					if ( $list_element == false ) {
						array_pop( $stack );
					}
					// reset the rdf:li indicator & array.
					$list_element = false;
					$list_array   = [];

				} elseif ( $val['type'] == 'complete' ) {
					if ( $val['tag'] == 'rdf:li' ) {
						// first go one element back.
						if ( $list_element == false ) {
							array_pop( $stack );
						}
						$list_element = true;
						// do not parse empty tags.
						if ( empty( $val['value'] ) ) {
							continue;
						}
						// save it in our temp array.
						$list_array[] = $val['value'];
						// in the case it's a list element we seralize it.
						$value = implode( ',', $list_array );
						$this->set_array_value( $xmlarray, $stack, $value );
					} else {
						array_push( $stack, $val['tag'] );
						// do not parse empty tags.
						if ( ! empty( $val['value'] ) ) {
							$this->set_array_value( $xmlarray, $stack, $val['value'] );
						}
						array_pop( $stack );
					}
				}
			} // foreach

			// don't parse a empty array.
			if ( empty( $xmlarray ) || empty( $xmlarray['x:xmpmeta'] ) ) {
				return false;
			}

			// cut off the useless tags.
			$xmlarray = $xmlarray['x:xmpmeta']['rdf:RDF']['rdf:Description'];

			// --------- Some values from the XMP format--------- //
			$xmp_tags = [
				'xap:CreateDate'            => 'created_timestamp',
				'xap:ModifyDate'            => 'last_modfied',
				'xap:CreatorTool'           => 'tool',
				'dc:format'                 => 'format',
				'dc:title'                  => 'title',
				'dc:creator'                => 'author',
				'dc:subject'                => 'keywords',
				'dc:description'            => 'caption',
				'photoshop:AuthorsPosition' => 'position',
				'photoshop:City'            => 'city',
				'photoshop:Country'         => 'country',
			];

			foreach ( $xmp_tags as $xmp_tag => $xmp_field ) {
				if ( isset( $xmlarray[ $xmp_tag ] ) ) {
					switch ( $xmp_tag ) {
						case 'xap:CreateDate':
						case 'xap:ModifyDate':
							$this->xmp_array[ $xmp_field ] = strtotime( $xmlarray[ $xmp_tag ] );
							break;
						default:
							$this->xmp_array[ $xmp_field ] = $xmlarray[ $xmp_tag ];
					}
				}
			}
		}

		// return one element if requested.
		if ( $key != false ) {
			return isset( $this->xmp_array[ $key ] ) ? $this->xmp_array[ $key ] : false;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->xmp_array, 'esc_html' );
		}

		return $this->xmp_array;
	}

	/**
	 * Set array value using a stack of keys.
	 *
	 * @param array $data Array to modify (passed by reference).
	 * @param array $stack Stack of keys for nested array access.
	 * @param mixed $value Value to set.
	 * @return array The modified array.
	 */
	public function set_array_value( &$data, $stack, $value ) {
		if ( $stack ) {
			$key = array_shift( $stack );
			$this->set_array_value( $data[ $key ], $stack, $value );
			return $data;
		} else {
			$data = $value;
		}

		return $data;
	}

	/**
	 * Return a meta value from the available list.
	 *
	 * @param string|false $key The meta key to retrieve.
	 * @return mixed The meta value or false if not found.
	 */
	public function get_meta( $key = false ) {
		$value = $this->get_saved_meta( $key );
		if ( $value ) {
			return $value;
		}

		$d = $this->get_IPTC( 'created_date' );
		$t = $this->get_IPTC( 'created_time' );
		if ( 'created_timestamp' == $key && $d && $t ) {
			return $this->exif_date2ts( $d . ' ' . $t );
		}

		$order = apply_filters( 'ngg_metadata_parse_order', [ 'XMP', 'IPTC', 'EXIF' ] );

		foreach ( $order as $method ) {
			$method = 'get_' . $method;
			$value  = null;
			if ( method_exists( $this, $method ) ) {
				$value = $this->$method( $key );
			}
			if ( $value ) {
				return $value;
			}
		}

		return false;
	}

	/**
	 * Localizes the tag name
	 *
	 * @param mixed $key
	 * @return string Translated $key
	 */
	public function i18n_name( $key ) {

		$tagnames = [
			'aperture'          => __( 'Aperture', 'nggallery' ),
			'author'            => __( 'Author', 'nggallery' ),
			'camera'            => __( 'Camera', 'nggallery' ),
			'caption'           => __( 'Caption', 'nggallery' ),
			'category'          => __( 'Category', 'nggallery' ),
			'city'              => __( 'City', 'nggallery' ),
			'contact'           => __( 'Contact', 'nggallery' ),
			'copyright'         => __( 'Copyright Notice', 'nggallery' ),
			'country'           => __( 'Country', 'nggallery' ),
			'country_code'      => __( 'Country code', 'nggallery' ),
			'created_date'      => __( 'Date Created', 'nggallery' ),
			'created_time'      => __( 'Time Created', 'nggallery' ),
			'created_timestamp' => __( 'Date/Time', 'nggallery' ),
			'credit'            => __( 'Credit', 'nggallery' ),
			'flash'             => __( 'Flash', 'nggallery' ),
			'focal_length'      => __( 'Focal length', 'nggallery' ),
			'format'            => __( 'Format', 'nggallery' ),
			'headline'          => __( 'Headline', 'nggallery' ),
			'height'            => __( 'Image Height', 'nggallery' ),
			'iso'               => __( 'ISO', 'nggallery' ),
			'keywords'          => __( 'Keywords', 'nggallery' ),
			'last_modfied'      => __( 'Last modified', 'nggallery' ),
			'location'          => __( 'Location', 'nggallery' ),
			'make'              => __( 'Make', 'nggallery' ),
			'position'          => __( 'Author Position', 'nggallery' ),
			'shutter_speed'     => __( 'Shutter speed', 'nggallery' ),
			'source'            => __( 'Source', 'nggallery' ),
			'state'             => __( 'Province/State', 'nggallery' ),
			'status'            => __( 'Edit Status', 'nggallery' ),
			'subject'           => __( 'Subject', 'nggallery' ),
			'tags'              => __( 'Tags', 'nggallery' ),
			'title'             => __( 'Title', 'nggallery' ),
			'tool'              => __( 'Program tool', 'nggallery' ),
			'width'             => __( 'Image Width', 'nggallery' ),
		];

		if ( isset( $tagnames[ $key ] ) ) {
			$key = $tagnames[ $key ];
		}

		return( $key );
	}

	/**
	 * Return the Timestamp from the image , if possible it's read from exif data
	 *
	 * @return string
	 */
	public function get_date_time() {
		// Try getting the created_timestamp field.
		$date = $this->exif_date2ts( $this->get_meta( 'created_timestamp' ) );
		if ( ! $date ) {
			$image_path = Manager::get_instance()->get_backup_abspath( $this->image );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$date = @filectime( $image_path );
		}

		// Failback.
		if ( ! $date ) {
			$date = time();
		}

		// Return the MySQL format.
		$date_time = gmdate( 'Y-m-d H:i:s', $date );

		return $date_time;
	}

	/**
	 * This function return the most common metadata, via a filter we can add more
	 * Reason : GD manipulation removes that options
	 *
	 * @since V1.4.0
	 * @return array|false
	 */
	public function get_common_meta() {
		global $wpdb;

		$meta = [
			'aperture'          => 0,
			'credit'            => '',
			'camera'            => '',
			'caption'           => '',
			'created_timestamp' => 0,
			'copyright'         => '',
			'focal_length'      => 0,
			'iso'               => 0,
			'shutter_speed'     => 0,
			'flash'             => 0,
			'title'             => '',
			'keywords'          => '',
		];

		$meta = apply_filters( 'ngg_read_image_metadata', $meta );

		// meta should be still an array.
		if ( ! is_array( $meta ) ) {
			return false;
		}

		foreach ( $meta as $key => $value ) {
			$meta[ $key ] = $this->get_meta( $key );
		}

		// let's add now the size of the image.
		$meta['width']  = $this->size[0];
		$meta['height'] = $this->size[1];

		return $meta;
	}

	/**
	 * If needed sanitize each value before output
	 *
	 * @return void
	 */
	public function sanitize() {
		$this->sanitize = true;
	}

	/**
	 * Wrapper to utf8_encode() that avoids double encoding
	 *
	 * Regex adapted from http://www.w3.org/International/questions/qa-forms-utf-8.en.php
	 * to determine if the given string is already UTF-8. mb_detect_encoding() is not
	 * always available and is limited in accuracy
	 *
	 * @param string $str
	 * @return string
	 */
	public function utf8_encode( $str ) {
		$is_utf8 = preg_match(
			'%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%xs',
			$str
		);
		if ( ! $is_utf8 ) {
			$str = mb_convert_encoding( $str, 'UTF-8', 'ISO-8859-1' );
		}
		return $str;
	}
}
