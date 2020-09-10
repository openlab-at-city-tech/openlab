<?php
/*
{
	Module: photocrati-nextgen_pro_upgrade,
	Depends: { photocrati-nextgen_admin }
}
*/

class M_NextGen_Pro_Upgrade extends C_Base_Module
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
            'photocrati-nextgen_pro_upgrade',
            'NextGEN Pro Page',
            'NextGEN Gallery Pro Upgrade Page',
            '3.3.7',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    function _register_adapters()
    {
        if (is_admin())
        {
            $this->get_registry()->add_adapter('I_Page_Manager', 'A_NextGen_Pro_Upgrade_Page');
            $this->get_registry()->add_adapter('I_Page_Manager', 'A_NextGen_Pro_Plus_Upgrade_Page');
            $this->get_registry()->add_adapter('I_NextGen_Admin_Page', 'A_NextGen_Pro_Upgrade_Controller', 'ngg_pro_upgrade');
        }
    }

    function get_type_list()
    {
        return array(
            'A_NextGen_Pro_Upgrade_Controller' => 'adapter.nextgen_pro_upgrade_controller.php',
            'A_NextGen_Pro_Plus_Upgrade_Page' => 'adapter.nextgen_pro_plus_upgrade_page.php',
            'A_NextGen_Pro_Upgrade_Page' => 'adapter.nextgen_pro_upgrade_page.php'
        );
    }
}

new M_NextGen_Pro_Upgrade;
