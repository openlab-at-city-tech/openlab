<?php
/*
{
	Module: photocrati-imagify,
	Depends: { photocrati-nextgen_admin }
}
*/

define('IMAGELY_IMAGIFY_PARTNER_ID' , 'nextgen-gallery');

require_once('lib' . DIRECTORY_SEPARATOR . 'class-imagify-partner.php');

class M_Imagify extends C_Base_Module
{
    protected static $_imagify_client = NULL;

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
            'photocrati-imagify',
            'NextGEN Imagify Integration',
            'NextGen Gallery / Imagify Integration',
            '3.3.21',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    static function get_imagify_client()
    {
		if (!self::$_imagify_client) {
			self::$_imagify_client = new Imagify_Partner(IMAGELY_IMAGIFY_PARTNER_ID);
			self::$_imagify_client->init();
		}
		return self::$_imagify_client;
    }

    function _register_adapters()
    {
        // TODO: check for PHP 5.2
        if (is_admin())
        {
//            $this->get_registry()
//                 ->add_adapter('I_Page_Manager', 'A_Imagify_Admin_Page');
//            $this->get_registry()
//                 ->add_adapter('I_NextGen_Admin_Page', 'A_Imagify_Admin_Page_Controller', 'ngg_imagify');
        }
    }

    function _register_hooks()
    {
	    if (($client = self::get_imagify_client())) {
		    add_filter(
			    'imagify_partner_success_url_' . $client->get_partner(),
			    array($this, 'set_imagify_success_redirect_url')
		    );
	    }
    }

    function set_imagify_success_redirect_url($url)
    {
        return get_admin_url(NULL, 'admin.php?page=imagify-ngg-bulk-optimization');
    }

    function get_type_list()
    {
        return array(
            'A_Imagify_Admin_Page_Controller' => 'adapter.imagify_admin_page_controller.php',
            'A_Imagify_Admin_Page' => 'adapter.imagify_admin_page.php'
        );
    }
}

new M_Imagify;