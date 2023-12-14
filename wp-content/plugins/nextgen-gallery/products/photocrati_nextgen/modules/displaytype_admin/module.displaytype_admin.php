<?php

class M_DisplayType_Admin extends C_Base_Module {

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
			'imagely-displaytype_admin',
			'NextGEN Display Type Manager',
			'Provides display type admin forms',
			NGG_PLUGIN_VERSION,
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	function initialize() {
		parent::initialize();

		if ( is_admin() ) {
			$forms = \Imagely\NGG\Admin\FormManager::get_instance();
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_THUMBNAILS );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_SLIDESHOW );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_IMAGEBROWSER );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_SINGLEPIC );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_TAGCLOUD );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_COMPACT_ALBUM );
			$forms->add_form( NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_EXTENDED_ALBUM );
		}
	}

	function _register_adapters() {
		if ( \Imagely\NGG\IGW\ATPManager::is_atp_url() || is_admin() ) {
			$registry = C_Component_Registry::get_instance();
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_Template_Form' );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_Thumbnail_Form', NGG_BASIC_THUMBNAILS );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_Slideshow_Form', NGG_BASIC_SLIDESHOW );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_ImageBrowser_Form', NGG_BASIC_IMAGEBROWSER );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_SinglePic_Form', NGG_BASIC_SINGLEPIC );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_TagCloud_Form', NGG_BASIC_TAGCLOUD );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_Compact_Album_Form', NGG_BASIC_COMPACT_ALBUM );
			$registry->add_adapter( 'I_Form', 'A_NextGen_Basic_Extended_Album_Form', NGG_BASIC_EXTENDED_ALBUM );

			$registry->add_adapter( 'I_Page_Manager', 'A_Display_Settings_Page' );
			$registry->add_adapter( 'I_NextGen_Admin_Page', 'A_Display_Settings_Controller', NGG_DISPLAY_SETTINGS_SLUG );
		}
	}

	public function _register_hooks() {
		add_action( 'init', [ $this, 'register_resources' ], 12 );
	}

	function register_resources() {
		$router = \Imagely\NGG\Util\Router::get_instance();

		wp_register_script(
			'nextgen_gallery_display_settings',
			$router->get_static_url( 'imagely-displaytype_admin#nextgen_gallery_display_settings.js' ),
			[ 'jquery-ui-accordion', 'jquery-ui-tooltip' ],
			NGG_SCRIPT_VERSION
		);

		wp_register_style(
			'nextgen_gallery_display_settings',
			$router->get_static_url( 'imagely-displaytype_admin#nextgen_gallery_display_settings.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
	}

	function get_type_list() {
		return [
			'A_Display_Settings_Controller'       => 'adapter.display_settings_controller.php',
			'A_Display_Settings_Page'             => 'adapter.display_settings_page.php',
			'A_Nextgen_Basic_Compact_Album_Form'  => 'adapter.compact_album.php',
			'A_Nextgen_Basic_Extended_Album_Form' => 'adapter.extended_album.php',
			'A_Nextgen_Basic_Imagebrowser_Form'   => 'adapter.imagebrowser.php',
			'A_Nextgen_Basic_Singlepic_Form'      => 'adapter.singlepicture.php',
			'A_Nextgen_Basic_Slideshow_Form'      => 'adapter.slideshow.php',
			'A_Nextgen_Basic_Tagcloud_Form'       => 'adapter.tagcloud.php',
			'A_Nextgen_Basic_Template_Form'       => 'adapter.templates.php',
			'A_Nextgen_Basic_Thumbnail_Form'      => 'adapter.thumbnails.php',
			'Mixin_Display_Type_Form'             => 'mixin.display_type_form.php',
			'Mixin_Nextgen_Basic_Album_Form'      => 'mixin.albums.php',
		];
	}
}

new M_DisplayType_Admin();
