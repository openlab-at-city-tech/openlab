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

		return $this->extract_path;
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
