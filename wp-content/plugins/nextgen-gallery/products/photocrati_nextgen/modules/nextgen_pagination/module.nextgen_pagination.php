<?php
/*
{
    Module: photocrati-nextgen_pagination
}
*/
class M_NextGen_Pagination extends C_Base_Module
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
            'photocrati-nextgen_pagination',
            "Pagination",
            "Provides pagination for display types",
            '3.0.0.2',
            "https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/",
            "Imagely",
            "https://www.imagely.com"
        );
    }

		function get_type_list()
		{
			return array(
				'Mixin_Nextgen_Basic_Pagination' => 'mixin.nextgen_basic_pagination.php'
			);
		}
}

new M_NextGen_Pagination;
