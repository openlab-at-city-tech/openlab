<?php
/*
{
	Module: photocrati-fs
}
 */
class M_Fs extends C_Base_Module
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
			'photocrati-fs',
			'Filesystem',
			'Provides a filesystem abstraction layer for Pope modules',
			'3.1.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
		);
	}

	function _register_utilities()
	{
		$this->get_registry()->add_utility('I_Fs', 'C_Fs');
	}

    function get_type_list()
    {
        return array(
            'C_Fs' => 'class.fs.php'
        );
    }
}

new M_Fs;