<?php

namespace Imagely\NGG\DataStorage;

use Imagely\NGG\DataMappers\Image as ImageMapper;

class MetaData {

	// Image data.
	public $image     = '';    // The image object.
	public $file_path = '';    // Path to the image file.
	public $size      = false; // The image size.
	public $exif_data = false; // EXIF data array.
	public $iptc_data = false; // IPTC data array.
	public $xmp_data  = false; // XMP data array.

	// Filtered Data.
	public $exif_array = false; // EXIF data array.
	public $iptc_array = false; // IPTC data array.
	public $xmp_array  = false; // XMP data array.

	public $sanitize = false; // sanitize meta data on request.

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

		if ( ! @file_exists( $this->file_path ) ) {
			return false;
		}

		$this->size = @getimagesize( $this->file_path, $metadata );

		if ( $this->size && is_array( $metadata ) ) {
			// get exif - data.
			if ( is_callable( 'exif_read_data' ) ) {
				$this->exif_data = @exif_read_data( $this->file_path, null, true );
			}

			// stop here if we didn't need other meta data.
			if ( $onlyEXIF ) {
				return true;
			}

			// get the iptc data - should be in APP13.
			if ( is_callable( 'iptcparse' ) && isset( $metadata['APP13'] ) ) {
				$this->iptc_data = @iptcparse( $metadata['APP13'] );
			}

			// get the xmp data in a XML format.
			if ( is_callable( 'xml_parser_create' ) ) {
				$this->xmp_data = $this->extract_XMP( $this->file_path );
			}

			return true;
		}

		return false;
	}

	/**
	 * return the saved metadata from the database
	 *
	 * @since 1.4.0
	 * @param string $object (optional)
	 * @return array|mixed return either the complete array or the single object
	 */
	public function get_saved_meta( $object = false ) {

		$meta = $this->image->meta_data;

		// Check if we already import the meta data to the database.
		if ( ! is_array( $meta ) || ! isset( $meta['saved'] ) || ( $meta['saved'] !== true ) ) {
			return false;
		}

		// return one element if requested.
		if ( $object ) {
			return $meta[ $object ];
		}

		// removed saved parameter we don't need that to show.
		unset( $meta['saved'] );

		// and remove empty tags or arrays.
		foreach ( $meta as $key => $value ) {
			if ( empty( $value ) or is_array( $value ) ) {
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
	 * nggMeta::get_EXIF()
	 * See also http://trac.wordpress.org/changeset/6313
	 *
	 * @return bool|array
	 */
	public function get_EXIF( $object = false ) {
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
					$meta['shutter_speed']  = ( $meta['shutter_speed'] > 0.0 and $meta['shutter_speed'] < 1.0 ) ? ( '1/' . round( 1 / $meta['shutter_speed'], -1 ) ) : ( $meta['shutter_speed'] );
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
		if ( $object == true ) {
			$value = isset( $this->exif_array[ $object ] ) ? $this->exif_array[ $object ] : false;
			return $value;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->exif_array, 'esc_html' );
		}

		return $this->exif_array;
	}

	// convert a fraction string to a decimal.
	public function exif_frac2dec( $str ) {
		@list( $n, $d ) = explode( '/', $str );
		if ( ! empty( $d ) ) {
			return $n / $d;
		}
		return $str;
	}

	// convert the exif date format to a unix timestamp.
	public function exif_date2ts( $str ) {
		$retval = is_numeric( $str ) ? $str : @strtotime( $str );
		if ( ! $retval && $str ) {
			@list( $date, $time ) = explode( ' ', trim( $str ) );
			@list( $y, $m, $d )   = explode( ':', $date );
			$retval               = strtotime( "{$y}-{$m}-{$d} {$time}" );

		}
		return $retval;
	}

	/**
	 * nggMeta::readIPTC() - IPTC Data Information for EXIF Display
	 *
	 * @param object $object (optional)
	 * @return null|bool|array
	 */
	public function get_IPTC( $object = false ) {
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
			foreach ( $iptcTags as $key => $value ) {
				if ( isset( $this->iptc_data[ $key ] ) ) {
					$meta[ $value ] = trim( $this->utf8_encode( implode( ', ', $this->iptc_data[ $key ] ) ) );
				}
			}
			$this->iptc_array = $meta;
		}

		// return one element if requested.
		if ( $object ) {
			return ( isset( $this->iptc_array[ $object ] ) ) ? $this->iptc_array[ $object ] : null;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->iptc_array, 'esc_html' );
		}

		return $this->iptc_array;
	}

	/**
	 * nggMeta::extract_XMP()
	 * get XMP DATA
	 * code by Pekka Saarinen http://photography-on-the.net
	 *
	 * @param mixed $filename
	 * @return bool|string
	 */
	public function extract_XMP( $filename ) {

		// TODO:Require a lot of memory, could be better.
		ob_start();
		@readfile( $filename );
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
	 * nggMeta::get_XMP()
	 *
	 * @package Taken from http://php.net/manual/en/function.xml-parse-into-struct.php
	 * @author Alf Marius Foss Olsen & Alex Rabe
	 * @return bool|array
	 */
	public function get_XMP( $object = false ) {

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
						$this->setArrayValue( $xmlarray, $stack, $value );
					} else {
						array_push( $stack, $val['tag'] );
						// do not parse empty tags.
						if ( ! empty( $val['value'] ) ) {
							$this->setArrayValue( $xmlarray, $stack, $val['value'] );
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
			$xmpTags = [
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

			foreach ( $xmpTags as $key => $value ) {
				// if the kex exist.
				if ( isset( $xmlarray[ $key ] ) ) {
					switch ( $key ) {
						case 'xap:CreateDate':
						case 'xap:ModifyDate':
							$this->xmp_array[ $value ] = strtotime( $xmlarray[ $key ] );
							break;
						default:
							$this->xmp_array[ $value ] = $xmlarray[ $key ];
					}
				}
			}
		}

		// return one element if requested.
		if ( $object != false ) {
			return isset( $this->xmp_array[ $object ] ) ? $this->xmp_array[ $object ] : false;
		}

		// on request sanitize the output.
		if ( $this->sanitize == true ) {
			array_walk( $this->xmp_array, 'esc_html' );
		}

		return $this->xmp_array;
	}

	public function setArrayValue( &$array, $stack, $value ) {
		if ( $stack ) {
			$key = array_shift( $stack );
			$this->setArrayValue( $array[ $key ], $stack, $value );
			return $array;
		} else {
			$array = $value;
		}

		return $array;
	}

	/**
	 * nggMeta::get_META() - return a meta value form the available list
	 *
	 * @param string $object
	 * @return mixed $value
	 */
	public function get_META( $object = false ) {
		if ( $value = $this->get_saved_meta( $object ) ) {
			return $value;
		}

		if ( $object == 'created_timestamp' && ( $d = $this->get_IPTC( 'created_date' ) ) && ( $t = $this->get_IPTC( 'created_time' ) ) ) {
			return $this->exif_date2ts( $d . ' ' . $t );
		}

		$order = apply_filters( 'ngg_metadata_parse_order', [ 'XMP', 'IPTC', 'EXIF' ] );

		foreach ( $order as $method ) {
			$method = 'get_' . $method;
			if ( method_exists( $this, $method ) && $value = $this->$method( $object ) ) {
				return $value;
			}
		}

		return false;
	}

	/**
	 * nggMeta::i8n_name() -  localize the tag name
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
		$date = $this->exif_date2ts( $this->get_META( 'created_timestamp' ) );
		if ( ! $date ) {
			$image_path = Manager::get_instance()->get_backup_abspath( $this->image );
			$date       = @filectime( $image_path );
		}

		// Failback.
		if ( ! $date ) {
			$date = time();
		}

		// Return the MySQL format.
		$date_time = date( 'Y-m-d H:i:s', $date );

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
			$meta[ $key ] = $this->get_META( $key );
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
			utf8_encode( $str );
		}
		return $str;
	}
}
