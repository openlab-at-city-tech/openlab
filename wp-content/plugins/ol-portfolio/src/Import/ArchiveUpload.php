<?php
/**
 * Import Upload handler.
 *
 * Based on `File_Upload_Upgrader`
 * @see https://developer.wordpress.org/reference/classes/file_upload_upgrader/.
 */

namespace OpenLab\Portfolio\Import;

use WP_Error;

class ArchiveUpload {

	/**
	 * Supported archive extensions
	 *
	 * @var array
	 */
	private $supported_archives = [
		'zip',
	];

	/**
	 * The full path to the file package.
	 *
	 * @var string
	 */
	public $package;

	/**
	 * The name of the file.
	 *
	 * @var string $filename
	 */
	public $filename;

	/**
	 * The ID of the attachment post for this file.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Name for the form.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * Construct the uploader for a form.
	 *
	 * @param string $form      The name of the form the file was uploaded from.
	 */
	public function __construct( $form ) {
		$this->form = $form;
	}

	/**
	 * Handle file upload.
	 *
	 * @return WP_Error|int
	 */
	public function handle() {
		if ( empty( $_FILES[ $this->form ]['name'] ) ) {
			return new WP_Error( 'import.upload.handle', 'Please select an OpenLab Portfolio archive file.' );
		}

		if ( ! $this->is_archive( pathinfo( $_FILES[ $this->form ]['name'] ) ) ) {
			return new WP_Error( 'import.upload.handle', 'Incorrect format. Please choose an OpenLab Portfolio archive file.' );
		}

		$overrides = [
			'test_form' => false,
			'test_type' => false,
		];
		$file      = wp_handle_upload( $_FILES[ $this->form ], $overrides );

		if ( isset( $file['error'] ) ) {
			return new WP_Error( 'import.upload.handle', $file['error'] );
		}

		$this->filename = $_FILES[ $this->form ]['name'];
		$this->package  = $file['file'];

		// Construct the object array
		$attachment = [
			'post_title'     => $this->filename,
			'post_content'   => $file['url'],
			'post_mime_type' => $file['type'],
			'guid'           => $file['url'],
			'context'        => 'import',
			'post_status'    => 'private',
		];

		// Save the data.
		$this->id = wp_insert_attachment( $attachment, $file['file'] );

		return $this->id;
	}

	/**
	 * Check if uploaded file is archive and is supported.
	 *
	 * @param array $pathparts pathinfo
	 */
	public function is_archive( $path_parts ) {
		if ( ! isset( $path_parts['extension'] ) ) {
			return false;
		}

		return in_array( $path_parts['extension'], $this->supported_archives, true );
	}
}
