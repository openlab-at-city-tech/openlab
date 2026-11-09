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
use lsolesen\pel\PelJpegMarker;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryShort;

use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelInvalidDataException;

/**
 * Handles EXIF metadata reading and writing operations for JPEG images.
 *
 * This class provides methods to read, write, and copy EXIF metadata
 * from JPEG files using the PEL (PHP EXIF Library) package.
 */
class EXIFWriter {

	/**
	 * Outcome of read_exif(): not a JPEG, so a TIFF parse is the only thing left to try.
	 */
	const NOT_JPEG = 'not-jpeg';

	/**
	 * Outcome of read_exif(): a JPEG whose marker chain is truncated or otherwise malformed.
	 */
	const MALFORMED_JPEG = 'malformed-jpeg';

	/**
	 * Outcome of read_exif(): a JPEG whose marker chain holds no parseable Exif.
	 */
	const NO_EXIF = 'no-exif';

	/**
	 * How many marker segments the walk will step through before giving up.
	 *
	 * A real JPEG header carries tens of segments, and the widest legitimate case is an ICC
	 * profile split across the 255 APP2 chunks the format allows, so this sits well clear of
	 * anything a camera or editor produces. The bound matters because SOI and EOI carry no
	 * length: without it a crafted run of them advances a single byte per iteration, turning
	 * the walk into one pass per byte of the file.
	 */
	const MAX_MARKERS = 1024;

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
			$exif = self::read_exif( $filename );

			if ( self::MALFORMED_JPEG === $exif ) {
				// PEL abandons such a file once it has parsed the whole thing, and parsing the
				// whole thing is the cost being avoided here, so refuse it on the same terms
				// without reading the rest of it.
				return null;
			}

			if ( self::NOT_JPEG === $exif ) {
				// A TIFF's directories carry offsets into anywhere in the file, so unlike a
				// JPEG it cannot be parsed from a bounded window and the whole file is needed.
				// is_jpeg_file() above only admits JPEG extensions, so this branch exists to
				// preserve the previous behaviour for a mislabelled file rather than because
				// it is expected to be taken.
				// PelTiff::isValid() reads only the first eight bytes, so settle the question
				// on those. Without this a file that merely carries a JPEG extension is loaded
				// in full and then thrown away, which is the allocation being avoided here.
				$head = self::read_file_head( $filename, 8 );

				if ( false === $head || ! PelTiff::isValid( new PelDataWindow( $head ) ) ) {
					return null;
				}

				$exif = new PelExif();
				$tiff = new PelTiff();
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$tiff->load( new PelDataWindow( (string) @file_get_contents( $filename ) ) );
			} else {
				if ( self::NO_EXIF === $exif ) {
					$exif = new PelExif();
				}

				$tiff = $exif->getTiff();

				if ( null === $tiff ) {
					$tiff = new PelTiff();
				}
			}

			$ifd0 = $tiff->getIfd();
			if ( null === $ifd0 ) {
				$ifd0 = new PelIfd( PelIfd::IFD0 );
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
		} catch ( \Throwable $exception ) {
			// Metadata is best-effort: callers resize and save images with whatever comes back,
			// so an unreadable or malformed source must degrade to "no metadata" instead of
			// aborting the generation. PEL raises both Exception and Error subclasses on
			// malformed input, hence Throwable rather than a list of its exception types.
			return null;
		}

		return $retval;
	}

	/**
	 * Reads the leading bytes of a file, leaving the rest of it on disk.
	 *
	 * @param string $filename Path to the file.
	 * @param int    $length   How many bytes to read.
	 * @return string|false The bytes read, or false when the file could not be opened.
	 */
	private static function read_file_head( $filename, $length ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		$handle = @fopen( $filename, 'rb' );

		if ( ! $handle ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		$head = (string) fread( $handle, $length );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		fclose( $handle );

		return $head;
	}

	/**
	 * Reads a JPEG's Exif by parsing only the segment that carries it.
	 *
	 * Walks the marker chain and parses APP1 candidates one at a time, stepping over the
	 * entropy coded image data rather than through it. The whole point is what never gets
	 * read: a marker's length is a 16 bit field, so this costs at most 64KB however large the
	 * file is, and callers hold the result live while they resize and save an image.
	 *
	 * Two details keep the outcome the same as parsing the entire file with PEL. A candidate
	 * that announces itself as Exif but does not parse is skipped and the walk continues, so a
	 * later valid APP1 is still found, mirroring PelJpeg keeping an unparseable APP1 as plain
	 * content and letting getExif() return the first one that did parse. And a marker code
	 * outside the range PelJpegMarker::isValid() accepts abandons the walk, because PEL
	 * abandons the file.
	 *
	 * @param string $filename Path to the file.
	 * @return PelExif|string The Exif that was found, or one of self::NOT_JPEG,
	 *                        self::MALFORMED_JPEG and self::NO_EXIF.
	 */
	private static function read_exif( $filename ) {
		// WP_Filesystem only offers whole-file reads, which is precisely the cost this method
		// exists to avoid, so the stream functions are used directly throughout.
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fopen, WordPress.WP.AlternativeFunctions.file_system_operations_fread, WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$handle = @fopen( $filename, 'rb' );

		if ( ! $handle ) {
			return self::NOT_JPEG;
		}

		try {
			$stat = fstat( $handle );
			$size = isset( $stat['size'] ) ? (int) $stat['size'] : 0;

			// A JPEG opens with SOI, which may be preceded by fill bytes. Tolerate the same
			// number of them PEL's own validity check does, so the two agree on what is a JPEG.
			$head  = (string) fread( $handle, 8 );
			$start = 0;
			while ( $start < 7 && isset( $head[ $start ] ) && "\xFF" === $head[ $start ] ) {
				++$start;
			}

			if ( ! isset( $head[ $start ] ) || "\xD8" !== $head[ $start ] ) {
				return self::NOT_JPEG;
			}

			if ( -1 === fseek( $handle, $start + 1 ) ) {
				return self::MALFORMED_JPEG;
			}

			$markers = 0;

			while ( true ) {
				if ( ++$markers > self::MAX_MARKERS ) {
					// Far past any real header, so the chain is not worth following further.
					return self::MALFORMED_JPEG;
				}

				$offset = ftell( $handle );

				if ( $offset >= $size ) {
					// Ran out of sections without meeting Exif.
					return self::NO_EXIF;
				}

				// PelJpeg::getJpgSectionStart() steps over at most seven fill bytes and reads
				// the first byte that is not 0xFF as the marker, without requiring a 0xFF ahead
				// of it. Mirroring that keeps the two agreeing on where a marker begins, and
				// bounds the scan so a long run of fill bytes cannot become a byte at a time
				// walk of the file.
				$peek = (string) fread( $handle, 8 );
				$skip = 0;
				while ( $skip < 7 && isset( $peek[ $skip ] ) && "\xFF" === $peek[ $skip ] ) {
					++$skip;
				}

				if ( ! isset( $peek[ $skip ] ) ) {
					return self::MALFORMED_JPEG;
				}

				$marker = ord( $peek[ $skip ] );

				if ( -1 === fseek( $handle, $offset + $skip + 1 ) ) {
					return self::MALFORMED_JPEG;
				}

				// The range PelJpegMarker::isValid() accepts. A code outside it makes PEL give
				// up on the file, so the chain is treated as malformed rather than walked past.
				if ( $marker < PelJpegMarker::SOF0 || $marker > PelJpegMarker::COM ) {
					return self::MALFORMED_JPEG;
				}

				// SOS begins the entropy coded data, and Exif never appears beyond it.
				if ( PelJpegMarker::SOS === $marker ) {
					return self::NO_EXIF;
				}

				// SOI and EOI are the two markers PEL reads without a length, and it walks on
				// past both rather than stopping, so an image carrying Exif after an EOI is
				// read the same way here.
				if ( PelJpegMarker::SOI === $marker || PelJpegMarker::EOI === $marker ) {
					continue;
				}

				$length_bytes = (string) fread( $handle, 2 );

				if ( 2 !== strlen( $length_bytes ) ) {
					return self::MALFORMED_JPEG;
				}

				// The length counts its own two bytes.
				$length = unpack( 'n', $length_bytes )[1] - 2;

				// A segment running past the end of the file means a truncated or malformed
				// image, which is where parsing the whole file used to fail.
				if ( $length < 0 || ftell( $handle ) + $length > $size ) {
					return self::MALFORMED_JPEG;
				}

				if ( PelJpegMarker::APP1 !== $marker ) {
					if ( $length > 0 && -1 === fseek( $handle, $length, SEEK_CUR ) ) {
						return self::MALFORMED_JPEG;
					}
					continue;
				}

				$payload = $length > 0 ? (string) fread( $handle, $length ) : '';

				if ( strlen( $payload ) !== $length ) {
					return self::MALFORMED_JPEG;
				}

				// APP1 also carries XMP, and only the Exif flavour is wanted here.
				if ( 0 !== strncmp( $payload, PelExif::EXIF_HEADER, strlen( PelExif::EXIF_HEADER ) ) ) {
					continue;
				}

				try {
					$exif = new PelExif();
					$exif->load( new PelDataWindow( $payload ) );

					return $exif;
				} catch ( PelInvalidDataException $exception ) {
					// Keep walking, since a later APP1 may still carry parseable Exif.
					continue;
				}
			}
		} finally {
			fclose( $handle );
		}
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_operations_fopen, WordPress.WP.AlternativeFunctions.file_system_operations_fread, WordPress.WP.AlternativeFunctions.file_system_operations_fclose
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
		} catch ( \Exception $exception ) {
			// Best-effort metadata copy must never abort generation (e.g. PEL "Offset -1" on a JPEG missing its EOI marker).
			error_log( "Could not write metadata to {$filename}: " . $exception->getMessage() );
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
