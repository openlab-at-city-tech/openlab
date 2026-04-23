<?php

namespace OpenLab\EXIF;

use lsolesen\pel\PelException;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelTag;

class Image {
	protected $path;
	protected $exif_data_loaded = false;
	protected $exif_data = [];
	protected $last_error = '';

	public function __construct( $path ) {
		$this->path = $path;
	}

	/**
	 * Check whether an image file exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return file_exists( $this->path );
	}

	/**
	 * Gets image type.
	 *
	 * @return string
	 */
	public function get_type() {
		if ( ! $this->exists() ) {
			return '';
		}

		$image_info = getimagesize( $this->path );

		if ( ! $image_info || ! isset( $image_info['mime'] ) ) {
			return '';
		}

		return $image_info['mime'];
	}

	/**
	 * Get the image's EXIF data.
	 *
	 * @return array
	 */
	public function get_exif_data() {
		if ( $this->exif_data_loaded ) {
			return $this->exif_data;
		}

		$this->exif_data_loaded = true;
		$this->exif_data        = [];
		$this->last_error       = '';

		if ( ! $this->exists() || ! function_exists( 'exif_read_data' ) ) {
			return [];
		}

		$warnings = [];
		set_error_handler(
			static function ( $errno, $errstr ) use ( &$warnings ) {
				$warnings[] = $errstr;

				return true;
			}
		);

		try {
			$exif = exif_read_data( $this->path );
		} finally {
			restore_error_handler();
		}

		if ( $warnings ) {
			$this->last_error = implode( '; ', array_unique( $warnings ) );
		}

		if ( is_array( $exif ) ) {
			$this->exif_data = $exif;
		}

		return $this->exif_data;
	}

	/**
	 * Check whether the image has GPS data.
	 *
	 * @return bool
	 */
	public function has_gps_data() {
		return ! empty( $this->get_gps_data() );
	}

	/**
	 * Gets the image's GPS data.
	 *
	 * @return array
	 */
	public function get_gps_data() {
		// Only JPEG and TIFF files have EXIF data.
		if ( ! $this->exists() || ! in_array( $this->get_type(), [ 'image/jpeg', 'image/tiff' ], true ) ) {
			return [];
		}

		$exif = $this->get_exif_data();

		$exif_gps = [];
		foreach ( $exif as $key => $value ) {
			if ( 0 === strpos( $key, 'GPS' ) ) {
				$exif_gps[ $key ] = $value;
			}
		}

		return $exif_gps;
	}

	/**
	 * Gets the most recent EXIF-related error.
	 *
	 * @return string
	 */
	public function get_last_error() {
		return $this->last_error;
	}

	/**
	 * Deletes the image's GPS data.
	 *
	 * @return bool
	 */
	public function delete_gps_data() {
		$this->last_error = '';

		if ( ! $this->exists() || ! $this->has_gps_data() ) {
			return false;
		}

		try {
			switch ( $this->get_type() ) {
				case 'image/jpeg':
					return $this->delete_jpeg_gps_data();

				case 'image/tiff':
					return $this->delete_tiff_gps_data();
			}
		} catch ( PelException $e ) {
			$this->last_error = $e->getMessage();
		}

		return false;
	}

	/**
	 * Deletes GPS data from a JPEG image.
	 *
	 * @return bool
	 */
	protected function delete_jpeg_gps_data() {
		$jpeg = new PelJpeg( $this->path );
		$exif = $jpeg->getExif();

		if ( ! $exif ) {
			return false;
		}

		$tiff = $exif->getTiff();
		$ifd0 = $tiff ? $tiff->getIfd() : null;

		if ( ! $ifd0 ) {
			return false;
		}

		$new_exif = new PelExif();
		$new_tiff = new PelTiff();
		$new_tiff->setIfd( $this->clone_ifd_without_gps_data( $ifd0 ) );
		$new_exif->setTiff( $new_tiff );
		$jpeg->setExif( $new_exif );

		return $this->save_jpeg( $jpeg );
	}

	/**
	 * Deletes GPS data from a TIFF image.
	 *
	 * @return bool
	 */
	protected function delete_tiff_gps_data() {
		$tiff = new PelTiff( $this->path );
		$ifd0 = $tiff->getIfd();

		if ( ! $ifd0 ) {
			return false;
		}

		$new_tiff = new PelTiff();
		$new_tiff->setIfd( $this->clone_ifd_without_gps_data( $ifd0 ) );

		return $this->save_tiff( $new_tiff );
	}

	/**
	 * Clones an IFD tree while omitting GPS data.
	 *
	 * @param PelIfd $ifd IFD to clone.
	 * @return PelIfd
	 */
	protected function clone_ifd_without_gps_data( PelIfd $ifd ) {
		$new_ifd = new PelIfd( $ifd->getType() );

		foreach ( $ifd->getEntries() as $entry ) {
			if ( PelTag::GPS_INFO_IFD_POINTER === $entry->getTag() ) {
				continue;
			}

			$new_ifd->addEntry( $entry );
		}

		foreach ( $ifd->getSubIfds() as $sub_ifd ) {
			if ( PelIfd::GPS === $sub_ifd->getType() ) {
				continue;
			}

			$new_ifd->addSubIfd( $this->clone_ifd_without_gps_data( $sub_ifd ) );
		}

		$next_ifd = $ifd->getNextIfd();
		if ( $next_ifd ) {
			$new_ifd->setNextIfd( $this->clone_ifd_without_gps_data( $next_ifd ) );
		}

		return $new_ifd;
	}

	/**
	 * Saves a modified JPEG image and resets cached EXIF data.
	 *
	 * @param PelJpeg $jpeg JPEG image to save.
	 * @return bool
	 */
	protected function save_jpeg( PelJpeg $jpeg ) {
		if ( false === $jpeg->saveFile( $this->path ) ) {
			$this->last_error = 'Could not save image after removing GPS data.';

			return false;
		}

		$this->reset_exif_cache();

		return true;
	}

	/**
	 * Saves a modified TIFF image and resets cached EXIF data.
	 *
	 * @param PelTiff $tiff TIFF image to save.
	 * @return bool
	 */
	protected function save_tiff( PelTiff $tiff ) {
		if ( false === $tiff->saveFile( $this->path ) ) {
			$this->last_error = 'Could not save image after removing GPS data.';

			return false;
		}

		$this->reset_exif_cache();

		return true;
	}

	/**
	 * Resets cached EXIF metadata.
	 */
	protected function reset_exif_cache() {
		$this->exif_data_loaded = false;
		$this->exif_data        = [];
		$this->last_error       = '';
	}
}
