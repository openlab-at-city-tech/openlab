<?php

namespace ElementsKit_Lite;

defined('ABSPATH') || exit;

class ElementsKit_Json_Handler {

	const MIME_TYPE = 'application/json';
	const EXT = 'json';


	/**
	 * The plugin instance.
	 */
	public static $instance = null;


	/**
	 * constructor function.
	 */
	public function __construct() {
		add_filter('upload_mimes', array($this, 'upload_mimes'));
		add_filter('wp_handle_upload_prefilter', array($this, 'wp_handle_upload_prefilter'));
		add_filter('wp_check_filetype_and_ext', array($this, 'wp_check_filetype_and_ext'), 10, 4);
	}


	/**
	 * Adds json file format
	 */
	public function upload_mimes($allowed_types) {
		$allowed_types[self::EXT] = self::MIME_TYPE;

		return $allowed_types;
	}


	public function wp_handle_upload_prefilter($file) {
		if(self::MIME_TYPE !== $file['type']) {
			return $file;
		}

		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);

		if(self::EXT !== $ext) {
			$file['error'] = sprintf(
				__('The uploaded %s file is not supported. Please upload a valid JSON file', 'elementskit-lite'),
				$file['name']
			);

			return $file;
		}

		return $file;
	}


	public function wp_check_filetype_and_ext($data, $file, $filename, $mimes) {
		if(!empty($data['ext']) && !empty($data['type'])) {
			return $data;
		}

		$filetype = wp_check_filetype($filename, $mimes);

		if(self::EXT === $filetype['ext']) {
			$data['ext']  = self::EXT;
			$data['type'] = self::MIME_TYPE;
		}

		return $data;
	}


	/**
	 * Instance.
	 */
	public static function instance() {
		if(is_null(self::$instance)) {
			// Fire when ElementsKit_Lite instance.
			self::$instance = new self();
		}

		return self::$instance;
	}
}

// Run the instance.
ElementsKit_Json_Handler::instance();
