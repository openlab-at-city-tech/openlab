<?php

namespace Imagely\NGG\DataStorage;

// 0.9.10 is compatible with PHP 8.0 but requires 7.2.0 as its minimum.
if ( version_compare( phpversion(), '7.2.0', '>=' ) ) {
	require_once NGG_PLUGIN_DIR . 'lib' . DIRECTORY_SEPARATOR . 'pel-0.9.12/autoload.php';
} else {
	require_once NGG_PLUGIN_DIR . 'lib' . DIRECTORY_SEPARATOR . 'pel-0.9.9/autoload.php';
}

use Imagely\NGG\Display\I18N;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryShort;

use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpegInvalidMarkerException;

class EXIFWriter {

	/**
	 * @param $filename
	 * @return array|null
	 */
	public static function read_metadata( $filename ) {
		if ( ! self::is_jpeg_file( $filename ) ) {
			return null;
		}

		$retval = null;

		try {
			$data = new PelDataWindow( @file_get_contents( $filename ) );
			$exif = new PelExif();

			if ( PelJpeg::isValid( $data ) ) {
				$jpeg = new PelJpeg();
				@$jpeg->load( $data );
				$exif = $jpeg->getExif();

				if ( null === $exif ) {
					$exif = new PelExif();
					$jpeg->setExif( $exif );

					$tiff = new PelTiff();
					$exif->setTiff( $tiff );
				} else {
					$tiff = $exif->getTiff();
				}
			} elseif ( PelTiff::isValid( $data ) ) {
				$tiff = new PelTiff();
				$tiff->load( $data );
			} else {
				return null;
			}

			$ifd0 = $tiff->getIfd();
			if ( null === $ifd0 ) {
				$ifd0 = new PelIfd( PelIfd::IFD0 );
				$tiff->setIfd( $ifd0 );
			}
			$tiff->setIfd( $ifd0 );
			$exif->setTiff( $tiff );

			$retval = [
				'exif' => $exif,
				'iptc' => null,
			];

			@getimagesize( $filename, $iptc );
			if ( ! empty( $iptc['APP13'] ) ) {
				$retval['iptc'] = $iptc['APP13'];
			}
		} catch ( PelIfdException $exception ) {
			return null; } catch ( PelInvalidArgumentException $exception ) {
			return null; } catch ( PelInvalidDataException $exception ) {
				return null; } catch ( PelJpegInvalidMarkerException $exception ) {
				return null; } catch ( \Exception $exception ) {
					return null; } finally {
						return $retval;
					}
	}

	/**
	 * @param $origin_file
	 * @param $destination_file
	 * @return bool|int FALSE on failure or (int) number of bytes written
	 */
	public static function copy_metadata( $origin_file, $destination_file ) {
		if ( ! self::is_jpeg_file( $origin_file ) ) {
			return false;
		}

		// Read existing data from the source file.
		$metadata = self::read_metadata( $origin_file );
		if ( ! empty( $metadata ) && is_array( $metadata ) ) {
			return self::write_metadata( $destination_file, $metadata );
		} else {
			return false;
		}
	}

	/**
	 * @param $filename
	 * @param $metadata
	 * @return bool|int FALSE on failure or (int) number of bytes written.
	 */
	public static function write_metadata( $filename, $metadata ) {
		if ( ! self::is_jpeg_file( $filename ) || ! is_array( $metadata ) ) {
			return false;
		}

		try {
			// Prevent the orientation tag from ever being anything other than normal horizontal.
			/** @var PelExif $exif */
			$exif = $metadata['exif'];
			$tiff = $exif->getTiff();
			$ifd0 = $tiff->getIfd();

			$orientation = new PelEntryShort( PelTag::ORIENTATION, 1 );

			$ifd0->addEntry( $orientation );
			$tiff->setIfd( $ifd0 );
			$exif->setTiff( $tiff );
			$metadata['exif'] = $exif;

			// Copy EXIF data to the new image and write it.
			$new_image = new PelJpeg( $filename );
			$new_image->setExif( $metadata['exif'] );
			$new_image->saveFile( $filename );

			// Copy IPTC / APP13 to the new image and write it.
			if ( $metadata['iptc'] ) {
				return self::write_IPTC( $filename, $metadata['iptc'] );
			}
		} catch ( PelInvalidArgumentException $exception ) {
			return false;
		} catch ( PelInvalidDataException $exception ) {
			error_log( "Could not write data to {$filename}" );
			error_log( print_r( $exception, true ) );
			return false;
		}

		// This should never happen, but this line satisfies phpstan.
		return false;
	}

	// In case bcmath isn't enabled we use these simple wrappers.
	static function bcadd( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcadd' ) ) {
			return floatval( $one ) + floatval( $two );
		} else {
			return bcadd( $one, $two, $scale ); } }
	static function bcmul( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcmul' ) ) {
			return floatval( $one ) * floatval( $two );
		} else {
			return bcmul( $one, $two, $scale ); } }
	static function bcpow( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcpow' ) ) {
			return floatval( $one ) ** floatval( $two );
		} else {
			return bcpow( $one, $two, $scale ); } }

	/**
	 * Use bcmath as a replacement to hexdec() to handle numbers than PHP_INT_MAX. Also validates the $hex parameter using ctypes.
	 *
	 * @param string $hex
	 * @return float|int|string|null
	 */
	public static function bchexdec( $hex ) {
		// Ensure $hex is actually a valid hex number and won't generate deprecated conversion warnings on PHP 7.4+.
		if ( ! ctype_xdigit( $hex ) ) {
			return null;
		}

		$decimal = 0;
		$length  = strlen( $hex );
		for ( $i = 1; $i <= $length; $i++ ) {
			$decimal = self::bcadd( $decimal, self::bcmul( strval( hexdec( $hex[ $i - 1 ] ) ), self::bcpow( '16', strval( $length - $i ) ) ) );
		}

		return $decimal;
	}

	/**
	 * @param string $filename
	 * @param array  $data
	 * @return bool|int FALSE on failure or (int) number of bytes written
	 */
	public static function write_IPTC( $filename, $data ) {
		if ( ! self::is_jpeg_file( $filename ) ) {
			return false;
		}

		$length = strlen( $data ) + 2;

		// Avoid invalid APP13 regions.
		if ( $length > 0xFFFF ) {
			return false;
		}

		// Wrap existing data in segment container we can embed new content in.
		$data = chr( 0xFF ) . chr( 0xED ) . chr( ( $length >> 8 ) & 0xFF ) . chr( $length & 0xFF ) . $data;

		$new_file_contents = @file_get_contents( $filename );

		if ( ! $new_file_contents || strlen( $new_file_contents ) <= 0 ) {
			return false;
		}

		$new_file_contents = substr( $new_file_contents, 2 );

		// Create new image container wrapper.
		$new_iptc = chr( 0xFF ) . chr( 0xD8 );

		// Track whether content was modified.
		$new_fields_added = ! $data;

		// This can cause errors if incorrectly pointed at a non-JPEG file.
		try {
			// Loop through each JPEG segment in search of region 13.
			while ( ( self::bchexdec( substr( $new_file_contents, 0, 2 ) ) & 0xFFF0 ) === 0xFFE0 ) {

				$segment_length = ( hexdec( substr( $new_file_contents, 2, 2 ) ) & 0xFFFF );
				$segment_number = ( hexdec( substr( $new_file_contents, 1, 1 ) ) & 0x0F );

				// Not a segment we're interested in.
				if ( $segment_length <= 2 ) {
					return false;
				}

				$current_segment = substr( $new_file_contents, 0, $segment_length + 2 );

				if ( ( 13 <= $segment_number ) && ( ! $new_fields_added ) ) {
					$new_iptc        .= $data;
					$new_fields_added = true;
					if ( 13 === $segment_number ) {
						$current_segment = '';
					}
				}

				$new_iptc         .= $current_segment;
				$new_file_contents = substr( $new_file_contents, $segment_length + 2 );
			}
		} catch ( \Exception $exception ) {
			return false;
		}

		if ( ! $new_fields_added ) {
			$new_iptc .= $data;
		}

		if ( $file = @fopen( $filename, 'wb' ) ) {
			return @fwrite( $file, $new_iptc . $new_file_contents );
		} else {
			return false;
		}
	}

	/**
	 * Determines if the file extension is .jpg or .jpeg
	 *
	 * @param $filename
	 * @return bool
	 */
	public static function is_jpeg_file( $filename ) {
		$extension = I18N::mb_pathinfo( $filename, PATHINFO_EXTENSION );
		return in_array( strtolower( $extension ), [ 'jpeg', 'jpg', 'jpeg_backup', 'jpg_backup' ] );
	}
}
