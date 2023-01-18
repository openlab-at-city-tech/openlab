<?php

namespace OpenLab\Portfolio\Import;

use WP_Error;
use ZipArchive;

class Decompressor {

	/**
	 * Attachement ID.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Archive file path.
	 *
	 * @var string
	 */
	protected $archive = '';

	/**
	 * Archive directory.
	 *
	 * @var string
	 */
	protected $archive_path = '';

	/**
	 * Extract directory.
	 *
	 * @var string
	 */
	protected $extract_path = '';

	/**
	 * Decompress Constructor
	 *
	 * @param int $id
	 */
	public function __construct( $id ) {
		$this->id           = $id;
		$this->archive      = get_attached_file( $id );
		$this->archive_path = pathinfo( realpath( $this->archive ), PATHINFO_DIRNAME );
		$this->extract_path = $this->archive_path . '/extract';
	}


	/**
	 * Extract import archive.
	 *
	 * @return WP_Error|string
	 */
	public function extract() {
		$zip = new ZipArchive;

		if ( ! $zip->open( $this->archive ) ) {
			return new WP_Error(
				'ol.importer.archive',
				'Unable to extract export file.'
			);
		}

		// Extract File.
		$zip->extractTo( $this->extract_path );
		$zip->close();

		// Delete non-permitted files.
		$this->sanitize_extracted_files();

		return $this->extract_path;
	}

	/**
	 * Sanitizes extracted files to remove those that are not permitted by this WP installation.
	 *
	 * @return bool
	 */
	protected function sanitize_extracted_files() {
		$rdi = new \RecursiveDirectoryIterator( $this->extract_path );
		$rii = new \RecursiveIteratorIterator( $rdi, \RecursiveIteratorIterator::SELF_FIRST );

		$filesystem = \OpenLab\ImportExport\openlab_get_filesystem();

		foreach ( $rii as $file_info ) {
			$file_name = $file_info->getFilename();
			if ( '.' === $file_name || '..' === $file_name ) {
				continue;
			}

			if ( $file_info->isDir() ) {
				continue;
			}

			// We allow these plugin-generated non-executables.
			if ( 'readme.md' === $file_name || 'wordpress.xml' === $file_name ) {
				continue;
			}

			$wp_filetype = wp_check_filetype( $file_name );
			if ( ! $wp_filetype['ext'] ) {
				$filesystem->delete( $file_info->getRealPath(), true );
			}
		}
	}

	/**
	 * Delete import attachement and extract dir..
	 *
	 * @return bool
	 */
	public function cleanup() {
		$filesystem = openlab_get_filesystem();

		wp_delete_attachment( $this->id );

		// Recursively delete the extract directory.
		return $filesystem->delete( $this->extract_path, true );
	}
}
