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

/**
 * Handles EXIF metadata reading and writing operations for JPEG images.
 *
 * This class provides methods to read, write, and copy EXIF metadata
 * from JPEG files using the PEL (PHP EXIF Library) package.
 */
class EXIFWriter {

	/**
	 * Reads EXIF and IPTC metadata from a JPEG file.
	 *
	 * @param string $filename Path to the JPEG file.
	 * @return array|null Array containing 'exif' and 'iptc' data, or null on failure.
	 */
	public static function read_metadata( $filename ) {
		if ( ! self::is_jpeg_file( $filename ) ) {
			return null;
		}

		$retval = null;

		try {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$data = new PelDataWindow( @file_get_contents( $filename ) );
			$exif = new PelExif();

			if ( PelJpeg::isValid( $data ) ) {
				$jpeg = new PelJpeg();
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
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

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
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
	 * Copies EXIF metadata from one JPEG file to another.
	 *
	 * @param string $origin_file      Path to the source JPEG file.
	 * @param string $destination_file Path to the destination JPEG file.
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
	 * Writes EXIF and IPTC metadata to a JPEG file.
	 *
	 * @param string $filename Path to the JPEG file.
	 * @param array  $metadata Array containing 'exif' and 'iptc' metadata.
	 * @return bool|int FALSE on failure or (int) number of bytes written.
	 */
	public static function write_metadata( $filename, $metadata ) {
		if ( ! self::is_jpeg_file( $filename ) || ! is_array( $metadata ) ) {
			return false;
		}

		try {
			// Prevent the orientation tag from ever being anything other than normal horizontal.
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
				return self::write_iptc( $filename, $metadata['iptc'] );
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

	/**
	 * Wrapper for bcadd function with fallback for systems without bcmath.
	 *
	 * @param string|float $one   First number.
	 * @param string|float $two   Second number.
	 * @param int|null     $scale Number of decimal places.
	 * @return string|float Result of addition.
	 */
	public static function bcadd( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcadd' ) ) {
			return floatval( $one ) + floatval( $two );
		} else {
			return bcadd( $one, $two, $scale ); } }

	/**
	 * Wrapper for bcmul function with fallback for systems without bcmath.
	 *
	 * @param string|float $one   First number.
	 * @param string|float $two   Second number.
	 * @param int|null     $scale Number of decimal places.
	 * @return string|float Result of multiplication.
	 */
	public static function bcmul( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcmul' ) ) {
			return floatval( $one ) * floatval( $two );
		} else {
			return bcmul( $one, $two, $scale ); } }

	/**
	 * Wrapper for bcpow function with fallback for systems without bcmath.
	 *
	 * @param string|float $one   Base number.
	 * @param string|float $two   Exponent.
	 * @param int|null     $scale Number of decimal places.
	 * @return string|float Result of exponentiation.
	 */
	public static function bcpow( $one, $two, $scale = null ) {
		if ( ! function_exists( 'bcpow' ) ) {
			return floatval( $one ) ** floatval( $two );
		} else {
			return bcpow( $one, $two, $scale ); } }

	/**
	 * Use bcmath as a replacement to hexdec() to handle numbers than PHP_INT_MAX. Also validates the $hex parameter using ctypes.
	 *
	 * @param string $hex Hexadecimal string to convert to decimal.
	 * @return float|int|string|null Decimal equivalent or null if invalid hex.
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
	 * Writes IPTC data to a JPEG file.
	 *
	 * @param string $filename Path to the JPEG file.
	 * @param array  $data     IPTC data to write.
	 * @return bool|int FALSE on failure or (int) number of bytes written
	 */
	public static function write_iptc( $filename, $data ) {
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

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
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

		$file = @fopen( $filename, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen, WordPress.PHP.NoSilencedErrors.Discouraged
		if ( $file ) {
			return @fwrite( $file, $new_iptc . $new_file_contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite, WordPress.PHP.NoSilencedErrors.Discouraged
		} else {
			return false;
		}
	}

	/**
	 * Determines if the file extension is .jpg or .jpeg
	 *
	 * @param string $filename The filename to check.
	 * @return bool True if the file is a JPEG, false otherwise.
	 */
	public static function is_jpeg_file( $filename ) {
		$extension = I18N::mb_pathinfo( $filename, PATHINFO_EXTENSION );
  // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		return in_array( strtolower( $extension ), [ 'jpeg', 'jpg', 'jpeg_backup', 'jpg_backup' ] );
	}
}
