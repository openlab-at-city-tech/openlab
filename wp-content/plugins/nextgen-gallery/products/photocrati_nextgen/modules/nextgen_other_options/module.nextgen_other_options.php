<?php
/*
{
	Module: photocrati-nextgen_other_options,
	Depends: { photocrati-nextgen_admin }
}
 */

define('NGG_OTHER_OPTIONS_SLUG', 'ngg_other_options');

class M_NextGen_Other_Options extends C_Base_Module
{
	function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-nextgen_other_options',
			'Other Options',
			'NextGEN Gallery Others Options Page',
			'3.2.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

    function _register_hooks()
    {
        add_action('admin_bar_menu', array(&$this, 'add_admin_bar_menu'), 101);
        add_action('init', array(&$this, 'register_forms'));
    }

    function register_forms()
    {
        $forms = array(
            'image_options'     => 'A_Image_Options_Form',
            'thumbnail_options' => 'A_Thumbnail_Options_Form',
            'lightbox_effects'  => 'A_Lightbox_Manager_Form',
            'watermarks'        => 'A_Watermarks_Form'
        );

        if (!is_multisite() || (is_multisite() && C_NextGen_Settings::get_instance()->get('wpmuStyle')))
            $forms['styles'] = 'A_Styles_Form';

        if (is_super_admin() && (!is_multisite() || (is_multisite() && C_NextGen_Settings::get_instance()->get('wpmuRoles'))))
            $forms['roles_and_capabilities'] = 'A_Roles_Form';

        $forms += array(
            'miscellaneous'			=>	'A_Miscellaneous_Form',
            'reset'                 =>  'A_Reset_Form'
        );

        $form_manager = C_Form_Manager::get_instance();
        foreach ($forms as $form => $adapter) {
            $form_manager->add_form(
                NGG_OTHER_OPTIONS_SLUG,
                $form
            );
        }
    }

    function add_admin_bar_menu()
    {
        global $wp_admin_bar;

        if ( current_user_can('NextGEN Change options') ) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'ngg-menu',
                'id' => 'ngg-menu-other_options',
                'title' => __('Other Options', 'nggallery'),
                'href' => admin_url('admin.php?page=ngg_other_options')
            ));
        }
    }

	function _register_adapters()
	{
        $this->get_registry()->add_adapter('I_Ajax_Controller', 'A_Watermarking_Ajax_Actions');
        $this->get_registry()->add_adapter('I_Ajax_Controller', 'A_Stylesheet_Ajax_Actions');

        if (is_admin()) {
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
                'A_Styles_Form',
                'styles'
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

    function get_type_list()
    {
        return array(
            'A_Image_Options_Form' => 'adapter.image_options_form.php',
            'A_Lightbox_Manager_Form' => 'adapter.lightbox_manager_form.php',
            'A_Miscellaneous_Form' => 'adapter.miscellaneous_form.php',
            'A_Other_Options_Controller' => 'adapter.other_options_controller.php',
            'A_Other_Options_Page' => 'adapter.other_options_page.php',
            'A_Reset_Form' => 'adapter.reset_form.php',
            'A_Roles_Form' => 'adapter.roles_form.php',
            'A_Styles_Form' => 'adapter.styles_form.php',
            'A_Thumbnail_Options_Form' => 'adapter.thumbnail_options_form.php',
            'A_Watermarking_Ajax_Actions' => 'adapter.watermarking_ajax_actions.php',
            'A_Watermarks_Form' => 'adapter.watermarks_form.php',
            'A_Stylesheet_Ajax_Actions' => 'adapter.stylesheet_ajax_actions.php',
			'C_Settings_Model'	=>	'class.settings_model.php',
            'A_Custom_Lightbox_Form' => 'adapter.custom_lightbox_form.php',
			'C_Settings_Model'	=>	'class.settings_model.php'
        );
    }
}

new M_NextGen_Other_Options;
