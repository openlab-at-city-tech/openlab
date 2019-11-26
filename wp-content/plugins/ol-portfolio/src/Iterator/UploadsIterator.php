<?php
/**
 * Uploads directory iterator.
 */

namespace OpenLab\Portfolio\Iterator;

use FilesystemIterator;
use RecursiveFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UploadsIterator extends RecursiveFilterIterator {

	/**
	 * Create iterator instance.
	 *
	 * @param string $dir
	 * @return \RecursiveIteratorIterator
	 */
	public static function create( $dir ) {
		return new RecursiveIteratorIterator(
			new UploadsIterator(
				new RecursiveDirectoryIterator(
					$dir,
					FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
				)
			)
		);
	}

	/**
	 * Apply custom filters.
	 *
	 * @return bool
	 */
	public function accept() {
		// So we can iterate again on subdirs.
		if ( $this->isDir() ) {
			return true;
		}

		$info = wp_check_filetype( $this->current()->getFilename() );

		return ! empty( $info['ext'] );
	}
}
