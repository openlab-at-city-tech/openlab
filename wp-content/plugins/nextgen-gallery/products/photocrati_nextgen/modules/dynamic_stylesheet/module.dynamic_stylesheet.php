<?php

/*
{
	Module: photocrati-dynamic_stylesheet,
	Depends: { photocrati-mvc }
}
 */

if (!defined('NGG_INLINE_DYNAMIC_CSS')) define('NGG_INLINE_DYNAMIC_CSS', TRUE);

class M_Dynamic_Stylesheet extends C_Base_Module
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
			'photocrati-dynamic_stylesheet',
			'Dynamic Stylesheet',
			'Provides the ability to generate and enqueue a dynamic stylesheet',
			'3.0.0',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com',
			$context
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_Dynamic_Stylesheet_Installer');
	}

	function _register_utilities()
	{
		$this->get_registry()->add_utility(
			"I_Dynamic_Stylesheet", 'C_Dynamic_Stylesheet_Controller'
		);
	}

    function _register_hooks()
    {
        add_action('ngg_routes', array(&$this, 'define_routes'));
	    add_filter('ngg_non_minified_files', array(&$this, 'do_not_minify'), 10, 2);
    }

	function do_not_minify($path, $module)
	{
		$retval = FALSE;

		if ($module == $this->module_id) $retval = TRUE;

		return $retval;
	}

    function define_routes($router)
    {
        $app = $router->create_app('/nextgen-dcss');
        $app->rewrite('/{\d}/{*}', '/index--{1}/data--{2}');
        $app->route('/', 'I_Dynamic_Stylesheet#index');
    }

    function get_type_list()
    {
        return array(
			'C_Dynamic_Stylesheet_Installer'	=> 'class.dynamic_stylesheet_installer.php',
            'C_Dynamic_Stylesheet_Controller' 	=> 'class.dynamic_stylesheet_controller.php'
        );
    }
}

class C_Dynamic_Stylesheet_Installer
{
	function __construct()
	{
		$this->settings = C_NextGen_Settings::get_instance();
	}

	function install()
	{
		$this->settings->set_default_value('dynamic_stylesheet_slug', 'nextgen-dcss');
	}
}

new M_Dynamic_Stylesheet;
