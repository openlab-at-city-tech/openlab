<?php
/*
{
	Module: photocrati-nextgen_other_options,
	Depends: { photocrati-nextgen_admin }
}
 */

define( 'NGG_OTHER_OPTIONS_SLUG', 'ngg_other_options' );

class M_NextGen_Other_Options extends C_Base_Module {

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
			'photocrati-nextgen_other_options',
			'Other Options',
			'NextGEN Gallery Others Options Page',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function _register_hooks() {
		add_action( 'admin_bar_menu', [ &$this, 'add_admin_bar_menu' ], 101 );
		add_action( 'init', [ &$this, 'register_forms' ] );
	}

	public function register_forms() {
		$forms = [
			'image_options'     => 'A_Image_Options_Form',
			'thumbnail_options' => 'A_Thumbnail_Options_Form',
			'lightbox_effects'  => 'A_Lightbox_Manager_Form',
			'watermarks'        => 'A_Watermarks_Form',
		];

		if ( is_super_admin() && ( ! is_multisite() || ( is_multisite() && \Imagely\NGG\Settings\Settings::get_instance()->get( 'wpmuRoles' ) ) ) ) {
			$forms['roles_and_capabilities'] = 'A_Roles_Form';
		}

		$forms += [
			'miscellaneous' => 'A_Miscellaneous_Form',
			'reset'         => 'A_Reset_Form',
		];

		$form_manager = \Imagely\NGG\Admin\FormManager::get_instance();
		foreach ( $forms as $form => $adapter ) {
			$form_manager->add_form(
				NGG_OTHER_OPTIONS_SLUG,
				$form
			);
		}
	}

	public function add_admin_bar_menu() {
		global $wp_admin_bar;

		if ( current_user_can( 'NextGEN Change options' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-other_options',
					'title'  => __( 'Other Options', 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=ngg_other_options' ),
				]
			);
		}
	}

	public function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_Ajax_Controller', 'A_Watermarking_Ajax_Actions' );
		$this->get_registry()->add_adapter( 'I_Ajax_Controller', 'A_Other_Options_Misc_Tab_Ajax' );

		if ( is_admin() ) {
			$this->get_registry()->add_adapter(
				'I_Page_Manager',
				'A_Other_Options_Page'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Custom_Lightbox_Form',
				'custom_lightbox'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Image_Options_Form',
				'image_options'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Thumbnail_Options_Form',
				'thumbnail_options'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Lightbox_Manager_Form',
				'lightbox_effects'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Watermarks_Form',
				'watermarks'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Roles_Form',
				'roles_and_capabilities'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Miscellaneous_Form',
				'miscellaneous'
			);

			$this->get_registry()->add_adapter(
				'I_Form',
				'A_Reset_Form',
				'reset'
			);

			$this->get_registry()->add_adapter(
				'I_NextGen_Admin_Page',
				'A_Other_Options_Controller',
				NGG_OTHER_OPTIONS_SLUG
			);
		}
	}

	public function get_type_list() {
		return [
			'A_Other_Options_Misc_Tab_Ajax' => 'adapter.other_options_misc_tab_ajax.php',
			'A_Image_Options_Form'          => 'adapter.image_options_form.php',
			'A_Lightbox_Manager_Form'       => 'adapter.lightbox_manager_form.php',
			'A_Miscellaneous_Form'          => 'adapter.miscellaneous_form.php',
			'A_Other_Options_Controller'    => 'adapter.other_options_controller.php',
			'A_Other_Options_Page'          => 'adapter.other_options_page.php',
			'A_Reset_Form'                  => 'adapter.reset_form.php',
			'A_Roles_Form'                  => 'adapter.roles_form.php',
			'A_Thumbnail_Options_Form'      => 'adapter.thumbnail_options_form.php',
			'A_Watermarking_Ajax_Actions'   => 'adapter.watermarking_ajax_actions.php',
			'A_Watermarks_Form'             => 'adapter.watermarks_form.php',
			'A_Custom_Lightbox_Form'        => 'adapter.custom_lightbox_form.php',
			'C_Settings_Model'              => 'class.settings_model.php',
		];
	}
}

new M_NextGen_Other_Options();
