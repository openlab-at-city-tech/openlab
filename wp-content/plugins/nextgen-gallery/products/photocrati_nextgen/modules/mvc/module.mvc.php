<?php

class M_MVC extends C_Base_Module {

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
			'photocrati-mvc',
			'MVC Framework',
			'Provides an MVC architecture for the plugin to use',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function _register_utilities() {
		$this->get_registry()->add_utility( 'I_MVC_Controller', 'C_MVC_Controller' );
	}

	public function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_Component_Factory', 'A_MVC_Factory' );
	}

	public function get_type_list() {
		return [
			'A_Mvc_Factory'      => 'adapter.mvc_factory.php',
			'C_Mvc_Installer'    => 'class.mvc_installer.php',
			'C_Mvc_Controller'   => 'class.mvc_controller.php',
			'C_Mvc_View'         => 'class.mvc_view.php',
			'C_Mvc_View_Element' => 'class.mvc_view_element.php',
		];
	}
}

new M_MVC();
