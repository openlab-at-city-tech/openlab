<?php

class M_NextGen_Basic_Album extends C_Base_Module {

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
			NGG_BASIC_ALBUM,
			'NextGEN Basic Album',
			"Provides support for NextGEN's Basic Album",
			'3.9.0',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_MVC_View', 'A_NextGen_Album_Breadcrumbs' );
		$this->get_registry()->add_adapter( 'I_MVC_View', 'A_NextGen_Album_Descriptions' );
		$this->get_registry()->add_adapter( 'I_MVC_View', 'A_NextGen_Album_Child_Entities' );
	}

	public function get_type_list() {
		return [
			'A_NextGen_Album_Breadcrumbs'    => 'adapter.nextgen_album_breadcrumbs.php',
			'A_NextGen_Album_Child_Entities' => 'adapter.nextgen_album_child_entities.php',
			'A_NextGen_Album_Descriptions'   => 'adapter.nextgen_album_descriptions.php',
		];
	}
}

new M_NextGen_Basic_Album();
