<?php

/***
{
        Module:     photocrati-nextgen_basic_tagcloud,
        Depends:    { photocrati-nextgen_gallery_display }
}
 ***/

define('NGG_BASIC_TAGCLOUD', 'photocrati-nextgen_basic_tagcloud');

class M_NextGen_Basic_Tagcloud extends C_Base_Module
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
			NGG_BASIC_TAGCLOUD,
            'NextGen Basic Tagcloud',
            'Provides a tagcloud for NextGEN Gallery',
            '3.1.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Basic_Tagcloud_Installer');
    }

    function initialize()
    {
        parent::initialize();
        if (is_admin()) {
            $forms = C_Form_Manager::get_instance();
            $forms->add_form(
                NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_TAGCLOUD
            );
        }

    }

    function _register_utilities()
    {
        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
            $this->get_registry()->add_utility('I_Taxonomy_Controller', 'C_Taxonomy_Controller');
    }

    function _register_adapters()
    {
        // Provides validation for the display type
        $this->get_registry()->add_adapter(
            'I_Display_Type',
            'A_NextGen_Basic_Tagcloud'
        );

		// Provides default values for the display type
		$this->get_registry()->add_adapter(
			'I_Display_Type_Mapper',
			'A_NextGen_Basic_TagCloud_Mapper'
		);

        if (M_Attach_To_Post::is_atp_url() || is_admin())
        {
            // Adds a display settings form
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_TagCloud_Form',
                $this->module_id
            );
        }

        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
        {
            // Provides settings fields and frontend rendering
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Tagcloud_Controller',
                $this->module_id
            );

            // Add legacy urls
            $this->get_registry()->add_adapter(
                'I_Routing_App',
                'A_NextGen_Basic_TagCloud_Urls'
            );
        }
    }

	function _register_hooks()
	{
        if (apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id)
        && (!defined('NGG_DISABLE_LEGACY_SHORTCODES') || !NGG_DISABLE_LEGACY_SHORTCODES))
        {
            C_NextGen_Shortcode_Manager::add('tagcloud', array(&$this, 'render_shortcode'));
            C_NextGen_Shortcode_Manager::add('nggtagcloud', array(&$this, 'render_shortcode'));

            add_filter(
                'the_posts',
                array(
                    C_Taxonomy_Controller::get_instance(),
                    'detect_ngg_tag'
                ),
                -10,
                2
            );
        }

        add_action('ngg_routes', array(&$this, 'define_routes'));
	}

    function define_routes($router)
    {
        $slug = '/'.C_NextGen_Settings::get_instance()->router_param_slug;
        $router->rewrite("{*}{$slug}{*}/tags/{\\w}{*}", "{1}{$slug}{2}/gallerytag--{3}{4}");
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
     * Short-cut for rendering a thumbnail gallery based on tags
     * @param array $params
     * @param null $inner_content
     * @return string
     */
	function render_shortcode($params, $inner_content=NULL)
    {
	    $params['tagcloud']     = $this->_get_param('tagcloud', 'yes', $params);
        $params['source']       = $this->_get_param('source', 'tags', $params);
        $params['display_type'] = $this->_get_param('display_type', NGG_BASIC_TAGCLOUD, $params);

		$renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
    }

    function get_type_list()
    {
        return array(
            'A_Nextgen_Basic_Tagcloud' => 'adapter.nextgen_basic_tagcloud.php',
            'A_Nextgen_Basic_Tagcloud_Controller' => 'adapter.nextgen_basic_tagcloud_controller.php',
            'A_Nextgen_Basic_Tagcloud_Form' => 'adapter.nextgen_basic_tagcloud_form.php',
            'C_NextGen_Basic_Tagcloud_Installer' => 'class.nextgen_basic_tagcloud_installer.php',
            'A_Nextgen_Basic_Tagcloud_Mapper' => 'adapter.nextgen_basic_tagcloud_mapper.php',
            'A_Nextgen_Basic_Tagcloud_Urls' => 'adapter.nextgen_basic_tagcloud_urls.php',
            'C_Taxonomy_Controller' => 'class.taxonomy_controller.php'
        );
    }
}

class C_NextGen_Basic_Tagcloud_Installer extends C_Gallery_Display_Installer
{
	/**
	 * Installs the display type for NextGEN Basic Tagcloud
     * @param bool $reset (optional) Unused
	 */
	function install($reset = FALSE)
	{
		$this->install_display_type(
			NGG_BASIC_TAGCLOUD, array(
				'title'					=>	__('NextGEN Basic TagCloud', 'nggallery'),
				'entity_types'			=>	array('image'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_tagcloud#preview.gif',
				'default_source'		=>	'tags',
				'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 100,
                'aliases'               => array(
                    'basic_tagcloud',
                    'tagcloud',
                    'nextgen_basic_tagcloud'
                )
			)

		);
	}
}

new M_NextGen_Basic_Tagcloud();
