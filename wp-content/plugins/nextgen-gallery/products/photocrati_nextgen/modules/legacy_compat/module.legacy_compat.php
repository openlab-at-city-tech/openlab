<?php

class M_Legacy_Compat extends C_Base_Module {

	public $object;

	function define(
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
			'imagely-legacy_compat',
			'Legacy Compat',
			'Misc wrappers and stay-behinds for legacy compatibility',
			NGG_PLUGIN_VERSION,
			'https://www.imagely.com',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	function _register_utilities() {
		$registry = $this->get_registry();
		$registry->add_utility( 'I_Display_Type_Controller', 'C_Display_Type_Controller' );
		$registry->add_utility( 'I_Display_Type_Mapper', 'C_Display_Type_Mapper' );
		$registry->add_utility( 'I_Displayed_Gallery_Mapper', 'C_Displayed_Gallery_Mapper' );
		$registry->add_utility( 'I_Gallery_Storage', 'C_Gallery_Storage' );
		$registry->add_utility( 'I_Router', 'C_Router_Wrapper' );
	}

	function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_Component_Factory', 'A_Gallery_Display_Factory' );
	}

	public function get_type_list() {
		return [
			'A_Gallery_Display_Factory'            => 'adapter.gallery_display_factory.php',
			'C_Display_Type'                       => 'class.display_type.php',
			'C_Display_Type_Controller'            => 'class.display_type_controller.php',
			'C_Display_Type_Mapper'                => 'class.display_type_mapper.php',
			'C_Displayed_Gallery'                  => 'class.displayed_gallery.php',
			'C_Displayed_Gallery_Mapper'           => 'class.displayed_gallery_mapper.php',
			'C_Displayed_Gallery_Source_Manager'   => 'class.displayed_gallery_source_manager.php',
			'C_Displayed_Gallery_Trigger'          => 'class.displayed_gallery_trigger.php',
			'C_Gallery_Storage'                    => 'class.gallery_storage.php',
			'C_NextGEN_Wizard_Manager'             => 'class.nextgen_wizard_manager.php',
			'C_Router_Wrapper'                     => 'class.router_wrapper.php',
			'Mixin_GalleryStorage_Base_Dynamic'    => 'mixin.gallerystorage_base_dynamic.php',
			'Mixin_GalleryStorage_Base_Getters'    => 'mixin.gallerystorage_base_getters.php',
			'Mixin_GalleryStorage_Base_Management' => 'mixin.gallerystorage_base_management.php',
			'Mixin_GalleryStorage_Base_Upload'     => 'mixin.gallerystorage_base_upload.php',
			'Mixin_Validation'                     => 'mixin.validation.php',
		];
	}
}

new M_Legacy_Compat();
