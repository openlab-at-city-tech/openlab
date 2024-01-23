<?php

class M_NextGen_Pagination extends C_Base_Module {

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
			'photocrati-nextgen_pagination',
			'Pagination',
			'Provides pagination for display types',
			'3.7.0',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function get_type_list() {
		return [
			'Mixin_Nextgen_Basic_Pagination' => 'mixin.nextgen_basic_pagination.php',
		];
	}
}

new M_NextGen_Pagination();
