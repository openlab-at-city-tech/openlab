<?php

namespace OpenLab\EXIF;

use lsolesen\pel;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelTag;

class Image {
	protected $path;

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

		return $image_info['mime'];
	}

	/**
	 * Get the image's EXIF data.
	 *
	 * @return array
	 */
	public function get_exif_data() {
		if ( ! $this->exists() ) {
			return [];
		}

		$exif = exif_read_data( $this->path );

		return $exif;
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
	 * Deletes the image's GPS data.
	 *
	 * @return bool
	 */
	public function delete_gps_data() {
		// Check if the image exists and has GPS data
        if ( ! $this->exists() || ! $this->has_gps_data() ) {
            return false;
        }

		// Get list of GPS-related keys from get_gps_data()
        $gps_keys = array_keys( $this->get_gps_data() );

        // Load the JPEG image
        $jpeg = new PelJpeg( $this->path );
        $exif = $jpeg->getExif();

        // If no EXIF data exists, nothing to remove
        if ( ! $exif ) {
            return false;
        }

        // Get the TIFF structure and IFD0
        $tiff = $exif->getTiff();
        $ifd0 = $tiff->getIfd();

        // Create a new EXIF structure without GPS data
        $new_exif = new PelExif();
        $new_tiff = new PelTiff();
        $new_ifd0 = new PelIfd( PelIfd::IFD0 );

        // Clone entries from IFD0, excluding GPS entries based on keys
        foreach ( $ifd0->getEntries() as $entry ) {
            $tag_name = PelTag::getName( PelIfd::GPS, $entry->getTag() );
            if ( ! in_array( $tag_name, $gps_keys, true ) ) {
                $new_ifd0->addEntry( $entry ); // Add only non-GPS entries
            }
        }

        // Clone sub-IFDs, excluding GPS IFD
        foreach ( $ifd0->getSubIfds() as $sub_ifd ) {
            if ( $sub_ifd->getType() !== PelIfd::GPS ) {  // Skip GPS IFD
                $new_ifd0->addSubIfd( $sub_ifd );
            }
        }

        // Assemble the new EXIF data structure
        $new_tiff->setIfd( $new_ifd0 );
        $new_exif->setTiff( $new_tiff );
        $jpeg->setExif( $new_exif );

        // Save the modified image
        $jpeg->saveFile( $this->path );

        return true;
	}
}
