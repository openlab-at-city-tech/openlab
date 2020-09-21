<?php

/***
{
    Module:     photocrati-nextgen_block,
}
***/

define('NEXTGEN_BLOCK', 'photocrati-nextgen_block');

class M_NextGEN_Block extends C_Base_Module
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
			'photocrati-nextgen_block',
			'NextGEN Block',
			'Provides a NextGEN Block for the Gutenberg interface.',
			'3.3.7',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
    }
    
    function _register_adapters()
    {
        C_Ngg_Post_Thumbnails::get_instance()->register_adapters();
    }

    function _register_hooks()
    {
        add_action( 'enqueue_block_editor_assets', array($this, 'nextgen_block_editor_assets') );
        C_Ngg_Post_Thumbnails::get_instance()->register_hooks();
    }

    function nextgen_block_editor_assets() {

        $router = C_Router::get_instance();

        wp_enqueue_script(
            'nextgen-block-js', 
            $router->get_static_url(NEXTGEN_BLOCK . '#build/block.min.js'),
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-compose'),
            NGG_SCRIPT_VERSION,
            TRUE
        );

        wp_enqueue_style(
            'nextgen-block-css', 
            $router->get_static_url(NEXTGEN_BLOCK . '#editor.css'),
            array( 'wp-edit-blocks' ),
            NGG_SCRIPT_VERSION
        );
    }

    function get_type_list()
    {
        return array(
            'A_NextGen_Block_Ajax'  => 'adapter.nextgen_block_ajax.php',
            'C_Ngg_Post_Thumbnails' => 'post_thumbnails.php'
        );
    }
}

new M_NextGEN_Block();