<?php

/***
	{
		Module: photocrati-simple_html_dom
	}
***/

if (!function_exists(('file_get_html'))) require_once('simplehtmldom/simple_html_dom.php');

class M_Simple_Html_Dom extends C_Base_Module
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
            'photocrati-simple_html_dom',
            'Simple HTML Dom',
            'Provides the simple_html_dom utility for other modules to use',
            '3.0.0',
            'https://www.imagely.com',
            'Imagely',
            'https://www.imagely.com'
        );
    }

		function get_type_list()
		{
			return array(
			);
		}
}

new M_Simple_Html_Dom();
