<?php
class M_Validation extends C_Base_Module
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
			'photocrati-validation',
			'Validation',
			'Provides validation support for objects',
            '3.1.4.2',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

    function get_type_list()
    {
        return array(
            'Mixin_Validation' => 'mixin.validation.php'
        );
    }
}

new M_Validation();