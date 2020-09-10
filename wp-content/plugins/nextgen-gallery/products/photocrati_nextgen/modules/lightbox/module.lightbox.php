<?php
/***
{
Module: photocrati-lightbox,
Depends: { photocrati-nextgen_admin }
}
 ***/

define('NGG_LIGHTBOX_OPTIONS_SLUG', 'ngg_lightbox_options');

class M_Lightbox extends C_Base_Module
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
            'photocrati-lightbox',
            'Lightbox',
            "Provides integration with several JavaScript lightbox effect libraries",
            '3.3.11',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    /**
     * Registers hooks for the WordPress framework
     */
    function _register_hooks()
    {
        if (!is_admin())
            add_action('wp_enqueue_scripts', array(C_Lightbox_Library_Manager::get_instance(), 'maybe_enqueue'));
        add_action('init', array(&$this, '_register_custom_post_type'));
    }

    /**
     * Registers the custom post type saved for lightbox libraries
     */
    function _register_custom_post_type()
    {
        register_post_type('lightbox_library', array(
            'label'					=>	'Lightbox Library',
            'publicly_queryable'	=>	FALSE,
            'exclude_from_search'	=>	TRUE,
        ));
    }

    function get_type_list()
    {
        return array(
            'C_Lightbox_Installer'       => 'class.lightbox_legacy_installer.php',
            'C_Lightbox_Library_Manager' => 'class.lightbox_library_manager.php',
            'C_NGG_Lightbox'             => 'class.ngg_lightbox.php'
        );
    }
}

new M_Lightbox();
