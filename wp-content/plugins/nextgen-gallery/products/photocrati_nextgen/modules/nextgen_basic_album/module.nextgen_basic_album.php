<?php

/*
{
    Module:		photocrati-nextgen_basic_album,
    Depends:  	{ photocrati-nextgen_gallery_display, photocrati-nextgen_basic_templates, photocrati-nextgen_pagination }
}
 */

define('NGG_BASIC_COMPACT_ALBUM', 'photocrati-nextgen_basic_compact_album');
define('NGG_BASIC_EXTENDED_ALBUM', 'photocrati-nextgen_basic_extended_album');
define('NGG_BASIC_ALBUM', 'photocrati-nextgen_basic_album');

class M_NextGen_Basic_Album extends C_Base_Module
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
            NGG_BASIC_ALBUM,
            'NextGEN Basic Album',
            "Provides support for NextGEN's Basic Album",
            '3.3.6',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Basic_Album_Installer');
    }

    function initialize()
    {
        parent::initialize();

        if (is_admin()) {
            $forms = C_Form_Manager::get_instance();
            $forms->add_form(
                NGG_DISPLAY_SETTINGS_SLUG,
                NGG_BASIC_COMPACT_ALBUM
            );
            $forms->add_form(
                NGG_DISPLAY_SETTINGS_SLUG,
                NGG_BASIC_EXTENDED_ALBUM
            );
        }
    }


    function _register_adapters()
    {
		// Add validation for album display settings
        $this->get_registry()->add_adapter(
			'I_Display_Type',
			'A_NextGen_Basic_Album'
		);

        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
        {
            // Add a controller for displaying albums on the front-end
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Album_Controller',
                array(
                    NGG_BASIC_COMPACT_ALBUM,
                    NGG_BASIC_EXTENDED_ALBUM,
                    $this->module_id
                )
            );

            // Add a generic adapter for display types to do late url rewriting
            $this->get_registry()->add_adapter(
                'I_Displayed_Gallery_Renderer',
                'A_NextGen_Basic_Album_Routes'
            );

            $this->get_registry()->add_adapter('I_MVC_View', 'A_NextGen_Album_Breadcrumbs');
            $this->get_registry()->add_adapter('I_MVC_View', 'A_NextGen_Album_Descriptions');
            $this->get_registry()->add_adapter('I_MVC_View', 'A_NextGen_Album_Child_Entities');
        }


		// Add a mapper for setting the defaults for the album
        $this->get_registry()->add_adapter(
			'I_Display_Type_Mapper',
			'A_NextGen_Basic_Album_Mapper'
		);

        if (M_Attach_To_Post::is_atp_url() || is_admin())
        {
            // Add a display settings form for each display type
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_Compact_Album_Form',
                NGG_BASIC_COMPACT_ALBUM
            );
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_Extended_Album_Form',
                NGG_BASIC_EXTENDED_ALBUM
            );
        }

        // Creates special parameter segments
        $this->get_registry()->add_adapter(
            'I_Routing_App',
            'A_NextGen_Basic_Album_Urls'
        );
    }

	function _register_hooks()
	{
        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id)
        && (!defined('NGG_DISABLE_LEGACY_SHORTCODES') || !NGG_DISABLE_LEGACY_SHORTCODES))
        {
            C_NextGen_Shortcode_Manager::add('album', array(&$this, 'ngglegacy_shortcode'));
            C_NextGen_Shortcode_Manager::add('nggalbum', array(&$this, 'ngglegacy_shortcode'));
        }

        add_filter('ngg_atp_show_display_type', array($this, 'atp_show_basic_albums'), 10, 2);

        add_filter('ngg_' . NGG_BASIC_COMPACT_ALBUM . '_template_dirs', array($this, 'filter_compact_view_dir'));

        add_filter('ngg_' . NGG_BASIC_EXTENDED_ALBUM . '_template_dirs', array($this, 'filter_extended_view_dir'));
    }

    /**
     * ATP filters display types by not displaying those whose name attribute isn't an active POPE module. This
     * is a workaround/hack to compensate for basic albums sharing a module.
     *
     * @param bool $available
     * @param C_Display_Type $display_type
     * @return bool
     */
    function atp_show_basic_albums($available, $display_type)
    {
        if (in_array($display_type->name, array(NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM)))
            $available = TRUE;
        return $available;
    }

    /**
     * Gets a value from the parameter array, and if not available, uses the default value
     *
     * @param string $name
     * @param mixed $default
     * @param array $params
     * @return mixed
     */
    function _get_param($name, $default, $params)
    {
        return (isset($params[$name])) ? $params[$name] : $default;
    }

	/**
     * Renders the shortcode for rendering an album
     * @param array $params
     * @param null $inner_content
     * @return string
     */
	function ngglegacy_shortcode($params, $inner_content=NULL)
    {
        $params['source']           = $this->_get_param('source', 'albums', $params);
        $params['container_ids']    = $this->_get_param('id', NULL, $params);
        $params['display_type']     = $this->_get_param('display_type', NGG_BASIC_COMPACT_ALBUM, $params);

        unset($params['id']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
    }

    function get_type_list()
    {
        return array(
            'A_NextGen_Album_Breadcrumbs' => 'adapter.nextgen_album_breadcrumbs.php',
            'A_NextGen_Album_Descriptions' => 'adapter.nextgen_album_descriptions.php',
            'A_NextGen_Album_Child_Entities' => 'adapter.nextgen_album_child_entities.php',
            'A_Nextgen_Basic_Album' => 'adapter.nextgen_basic_album.php',
            'A_Nextgen_Basic_Album_Controller' => 'adapter.nextgen_basic_album_controller.php',
            'A_Nextgen_Basic_Album_Mapper' => 'adapter.nextgen_basic_album_mapper.php',
            'A_Nextgen_Basic_Album_Routes' => 'adapter.nextgen_basic_album_routes.php',
            'A_Nextgen_Basic_Album_Urls' => 'adapter.nextgen_basic_album_urls.php',
            'A_Nextgen_Basic_Compact_Album_Form' => 'adapter.nextgen_basic_compact_album_form.php',
            'A_Nextgen_Basic_Extended_Album_Form' => 'adapter.nextgen_basic_extended_album_form.php',
            'Mixin_Nextgen_Basic_Album_Form' => 'mixin.nextgen_basic_album_form.php'
        );
    }

    function filter_compact_view_dir($dirs) 
    {
        $dirs['default'] = C_Component_Registry::get_instance()->get_module_dir(NGG_BASIC_ALBUM) . DIRECTORY_SEPARATOR . 'templates/compact';
        return $dirs;
    }

    function filter_extended_view_dir($dirs) 
    {
        $dirs['default'] = C_Component_Registry::get_instance()->get_module_dir(NGG_BASIC_ALBUM) . DIRECTORY_SEPARATOR . 'templates/extended';
        return $dirs;
    }

}

class C_NextGen_Basic_Album_Installer extends C_Gallery_Display_Installer
{
	function install($reset = FALSE)
	{
		$this->install_display_type(
			NGG_BASIC_COMPACT_ALBUM, array(
				'title'					=>	__('NextGEN Basic Compact Album', 'nggallery'),
                'module_id'             =>  NGG_BASIC_ALBUM, 
				'entity_types'			=>	array('album', 'gallery'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_album#compact_preview.jpg',
				'default_source'		=>	'albums',
				'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 200,
                'aliases'               =>  array(
                    'basic_compact_album',
                    'nextgen_basic_album',
                    'basic_album_compact',
                    'compact_album'
                )
			));

		$this->install_display_type(
			NGG_BASIC_EXTENDED_ALBUM, array(
				'title'					=>	__('NextGEN Basic Extended Album', 'nggallery'),
                'module_id'             =>  NGG_BASIC_ALBUM, 
				'entity_types'			=>	array('album', 'gallery'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_album#extended_preview.jpg',
				'default_source'		=>	'albums',
				'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 210,
                'aliases'               =>  array(
                    'basic_extended_album',
                    'nextgen_basic_extended_album',
                    'extended_album'
                )
			));
	}
}

function nggShowAlbum($albumID, $template = 'extend', $gallery_template = '')
{
	$renderer = C_Displayed_Gallery_Renderer::get_instance();
	$retval = $renderer->display_images(array(
		'album_ids' => array($albumID),
		'display_type' => 'photocrati-nextgen_basic_extended_album',
		'template' => $template,
		'gallery_display_template' => $gallery_template
	));

	return apply_filters('ngg_show_album_content', $retval, $albumID);
}

new M_NextGen_Basic_Album();