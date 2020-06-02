<?php

class M_WPCLI extends C_Base_Module
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
            'photocrati-wpcli',
            'WP-CLI Integration',
            "Provides additional commands for WP-CLI (https://github.com/wp-cli/wp-cli",
            '3.0.0',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

	function initialize()
	{
		parent::initialize();
	}

    function get_type_list()
    {
        return array();
    }
}

new M_WPCLI();

// WP-CLI makes use of namespaces so we must restrict this to PHP 5.3+
if (defined('WP_CLI')
&&  WP_CLI
&&  class_exists('WP_CLI_Command', FALSE)
&&  version_compare(phpversion(), '5.3.0', '>'))
{
    include_once('include/ngg_wpcli.php');
}
