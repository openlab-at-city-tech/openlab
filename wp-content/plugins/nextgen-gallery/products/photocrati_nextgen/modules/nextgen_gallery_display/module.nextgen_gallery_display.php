<?php

class M_Gallery_Display extends C_Base_Module {

	public $object;

	public function define(
		$id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-nextgen_gallery_display',
			'Gallery Display',
			'Provides the ability to display gallery of images',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	/**
	 * @TODO Remove this method when the minimum Pro version has an API version of 4.0 or higher.
	 */
	static function enqueue_fontawesome() {
		\Imagely\NGG\Display\DisplayManager::enqueue_fontawesome();
	}

	public function get_type_list() {
		return [];
	}
}

new M_Gallery_Display();
