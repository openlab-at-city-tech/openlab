<?php

class M_Router extends C_Base_Module
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
			'photocrati-router',
			'Router for Pope',
			'Provides routing capabilities for Pope modules',
			'3.13',
			'https://www.imagely.com',
			'Imagely',
			'https://www.imagely.com'
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_Router_Installer');
	}

	function _register_utilities()
	{
		$this->get_registry()->add_utility('I_Router', 'C_Router');
	}

	function _register_adapters()
	{
		$this->get_registry()->add_adapter('I_Component_Factory', 'A_Routing_App_Factory');
	}

    function get_type_list()
    {
        return array(
			'C_Router_Installer'	=> 'class.router_installer.php',
            'A_Routing_App_Factory' => 'adapter.routing_app_factory.php',
            'C_Router' => 'class.router.php',
            'C_Http_Response_Controller' => 'class.http_response_controller.php',
            'C_Routing_App' => 'class.routing_app.php',
            'Mixin_Url_Manipulation' => 'mixin.url_manipulation.php'
        );
    }
}

class C_Router_Installer
{
	function install()
	{
		$settings = C_NextGen_Settings::get_instance();
		$settings->set_default_value('router_param_separator', '--');
		$settings->set_default_value('router_param_prefix', '');
		$settings->set_default_value('router_param_slug', 'nggallery');
	}
}

new M_Router;
