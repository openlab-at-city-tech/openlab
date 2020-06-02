<?php
class M_Cache extends C_Base_Module
{
    /**
     * Defines the module name & version
     */
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
			'photocrati-cache',
			'Cache',
			'Handles clearing of NextGen caches',
			'3.0.0',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
		);
	}

    /**
     * Register utilities
     */
    function _register_utilities()
    {
        $this->get_registry()->add_utility('I_Cache', 'C_Cache');
    }

    /**
     * @return array
     */
    function get_type_list()
    {
        return array(
            'C_Cache' => 'class.cache.php'
        );
    }
}

new M_Cache();
