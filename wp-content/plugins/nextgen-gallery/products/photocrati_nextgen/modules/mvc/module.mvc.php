<?php

/***
	{
		Module: photocrati-mvc,
		Depends: { photocrati-router, photocrati-nextgen_settings }
	}
***/

/**
 * TODO: The file below should be deprecated. We should use an existing template
 * engine, such as Twig
 */

class M_MVC extends C_Base_Module
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
            'photocrati-mvc',
            'MVC Framework',
            'Provides an MVC architecture for the plugin to use',
            '3.1.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery',
            'Imagely',
            'https://www.imagely.com'
        );

		C_NextGen_Settings::get_instance()->add_option_handler('C_Mvc_Option_Handler', array(
            'mvc_template_dir',
            'mvc_template_dirname',
            'mvc_static_dir',
            'mvc_static_dirname'
		));

        if (is_multisite()) C_NextGen_Global_Settings::get_instance()->add_option_handler('C_Mvc_Option_Handler', array(
            'mvc_template_dir',
            'mvc_template_dirname',
            'mvc_static_dir',
            'mvc_static_dirname'
        ));

    }

    function _register_utilities()
    {
		$this->get_registry()->add_utility('I_Http_Response', 'C_Http_Response_Controller');
    }

    function _register_adapters()
    {
            $this->get_registry()->add_adapter('I_Fs', 'A_MVC_Fs');
            $this->get_registry()->add_adapter('I_Component_Factory', 'A_MVC_Factory');
    }

    function get_type_list()
    {
        return array(
            'A_Mvc_Factory' => 'adapter.mvc_factory.php',
            'A_Mvc_Fs' => 'adapter.mvc_fs.php',
            'C_Mvc_Installer' => 'class.mvc_installer.php',
            'C_Mvc_Controller' => 'class.mvc_controller.php',
            'C_Mvc_View' => 'class.mvc_view.php',
            'C_Mvc_View_Element' => 'class.mvc_view_element.php'
        );
    }
}

class C_Mvc_Option_Handler
{
	function get($option, $default=NULL)
	{
		$retval = $default;

		switch ($option) {
			case 'mvc_template_dir':
			case 'mvc_template_dirname':
				$retval = '/templates';
				break;
			case 'mvc_static_dirname':
			case 'mvc_static_dir':
				$retval = '/static';
				break;
		}

		return $retval;
	}
}

// These functions do NOT work when the Adminer plugin is installed, and being
// viewed. As there's no need to use these functions when viewing Adminer, we'll
// just skip this
if (strpos($_SERVER['REQUEST_URI'], 'adminer') === FALSE) {

    if (!function_exists('echo_safe_html')) {
        function echo_safe_html($html, $extra_tags = null)
        {
            $tags = array('<a>', '<abbr>', '<acronym>', '<address>', '<b>', '<base>', '<basefont>', '<big>', '<blockquote>', '<br>', '<br/>', '<caption>', '<center>', '<cite>', '<code>', '<col>', '<colgroup>', '<dd>', '<del>', '<dfn>', '<dir>', '<div>', '<dl>', '<dt>', '<em>', '<fieldset>', '<font>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<hr>', '<i>', '<ins>', '<label>', '<legend>', '<li>', '<menu>', '<noframes>', '<noscript>', '<ol>', '<optgroup>', '<option>', '<p>', '<pre>', '<q>', '<s>', '<samp>', '<select>', '<small>', '<span>', '<strike>', '<strong>', '<sub>', '<sup>', '<table>', '<tbody>', '<td>', '<tfoot>', '<th>', '<thead>', '<tr>', '<tt>', '<u>', '<ul>');
            $html = preg_replace('/\\s+on\\w+=(["\']).*?\\1/i', '', $html);
            $html = preg_replace('/(<\/[^>]+?>)(<[^>\/][^>]*?>)/', '$1 $2', $html);
            $html = strip_tags($html, implode('', $tags));
            echo $html;
        }
    }
}

new M_MVC();
