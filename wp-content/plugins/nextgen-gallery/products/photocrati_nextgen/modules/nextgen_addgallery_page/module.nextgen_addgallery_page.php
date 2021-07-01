<?php
/**
{
    Module: photocrati-nextgen_addgallery_page
}
**/

define('NGG_ADD_GALLERY_SLUG', 'ngg_addgallery');
if (!defined('NGG_UPLOAD_LIMIT')) define('NGG_UPLOAD_LIMIT', 6);
if (!defined('NGG_UPLOAD_TIMEOUT')) define('NGG_UPLOAD_TIMEOUT', 40);

class M_NextGen_AddGallery_Page extends C_Base_Module
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
            'photocrati-nextgen_addgallery_page',
            'NextGEN Add Gallery Page',
            'Provides admin page for adding a gallery and uploading images',
            '3.5.0.4',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    function initialize()
    {
        $forms = C_Form_Manager::get_instance();
        $settings = C_NextGen_Settings::get_instance();
        $forms->add_form(NGG_ADD_GALLERY_SLUG, 'upload_images');
        if (!is_multisite() || (is_multisite() && $settings->get('wpmuImportFolder')))
        {
            $forms->add_form(NGG_ADD_GALLERY_SLUG, 'import_media_library');
            $forms->add_form(NGG_ADD_GALLERY_SLUG, 'import_folder');
        }
    }

    public function check_upload_dir_permissions_requirement()
    {
        return wp_is_writable(C_Gallery_Storage::get_instance()->get_upload_abspath());
    }

    public function check_domdocument_requirement()
    {
        return class_exists('DOMDocument');
    }
    
    function get_type_list()
    {
    	return array(
            'A_Import_Media_Library_Form' => 'adapter.import_media_library_form.php',
    		'A_Import_Folder_Form' => 'adapter.import_folder_form.php',
    		'A_Nextgen_Addgallery_Ajax' => 'adapter.nextgen_addgallery_ajax.php',
    		'A_Nextgen_Addgallery_Controller' => 'adapter.nextgen_addgallery_controller.php',
    		'A_Nextgen_Addgallery_Pages' => 'adapter.nextgen_addgallery_pages.php',
    		'A_Upload_Images_Form' => 'adapter.upload_images_form.php',
    	);
    }

    function _register_adapters()
    {
        // AJAX operations aren't admin requests
        $this->get_registry()->add_adapter('I_Ajax_Controller', 'A_NextGen_AddGallery_Ajax');

        if (is_admin()) {
            $this->get_registry()->add_adapter('I_Page_Manager', 'A_NextGen_AddGallery_Pages');
            $this->get_registry()->add_adapter('I_NextGen_Admin_Page', 'A_NextGen_AddGallery_Controller', NGG_ADD_GALLERY_SLUG);
            $this->get_registry()->add_adapter('I_Form', 'A_Upload_Images_Form', 'upload_images');
            if (!is_multisite() || (is_multisite() && C_NextGen_Settings::get_instance()->get('wpmuImportFolder')))
            {
                $this->get_registry()->add_adapter('I_Form', 'A_Import_Folder_Form', 'import_folder');
                $this->get_registry()->add_adapter('I_Form', 'A_Import_Media_Library_Form', 'import_media_library');
            }
        }
    }

    function _register_hooks()
    {
        add_action('admin_init', array($this, 'register_requirements'));
        add_action('admin_init', array($this, 'register_scripts'));
    }

    public function register_requirements()
    {
        C_Admin_Requirements_Manager::get_instance()->add(
            'nextgen_addgallery_xmlcheck',
            'phpext',
            array($this, 'check_domdocument_requirement'),
            array('message' => __('XML is strongly encouraged for safely uploading images', 'nggallery'))
        );

        $directory = C_Gallery_Storage::get_instance()->get_upload_abspath();
        C_Admin_Requirements_Manager::get_instance()->add(
            'add_gallery_upload_dir_permission',
            'dirperms',
            array($this, 'check_upload_dir_permissions_requirement'),
            array('message' => sprintf(__('Cannot write to %s: new galleries cannot be created', 'nggallery'), $directory))
        );
    }

    function register_scripts()
    {
        if (is_admin())
        {
            $router = C_Router::get_instance();
            $add_gallery_page_id = 'photocrati-nextgen_addgallery_page';
            wp_register_style(
                'nextgen_addgallery_page',
                $router->get_static_url($add_gallery_page_id . '#styles.css'),
                array(),
                NGG_SCRIPT_VERSION
            );

            wp_register_script(
                'uppy',
                $router->get_static_url($add_gallery_page_id . '#uppy/uppy.min.js'),
                [],
                '1.27.0'
            );
            wp_register_style(
                'uppy',
                $router->get_static_url($add_gallery_page_id . '#uppy/uppy.min.css'),
                [],
                '1.21.1'
            );
            wp_register_script(
                'uppy_i18n',
                $router->get_static_url($add_gallery_page_id . '#uppy/i18n.min.js'),
                ['uppy'],
                '1.21.1'
            );

            wp_register_script(
                'toastify',
                $router->get_static_url($add_gallery_page_id . '#toastify.js'),
                [],
                '1.9.2'
            );
            wp_register_style(
                'toastify',
                $router->get_static_url($add_gallery_page_id . '#toastify.min.css'),
                [],
                '1.9.2'
            );

            wp_register_script(
                'jquery.filetree',
                $router->get_static_url($add_gallery_page_id . '#jquery.filetree/jquery.filetree.js'),
                array('jquery'),
                NGG_SCRIPT_VERSION
            );
            wp_register_style(
                'jquery.filetree',
                $router->get_static_url($add_gallery_page_id . '#jquery.filetree/jquery.filetree.css'),
                array(),
                NGG_SCRIPT_VERSION
            );

            wp_register_script(
                'nextgen_media_library_import-js',
                $router->get_static_url($add_gallery_page_id . '#media-library-import.js'),
                array('jquery', 'ngg_progressbar'),
                NGG_SCRIPT_VERSION
            );
            wp_register_style(
                'nextgen_media_library_import-css',
                $router->get_static_url($add_gallery_page_id . '#media-library-import.css'),
                array(),
                NGG_SCRIPT_VERSION
            );
        }
    }
}
new M_NextGen_AddGallery_Page();
