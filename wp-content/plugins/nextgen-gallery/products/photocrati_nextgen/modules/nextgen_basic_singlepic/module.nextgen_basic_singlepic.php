<?php

/***
{
        Module:     photocrati-nextgen_basic_singlepic,
        Depends:    { photocrati-nextgen_gallery_display }
}
 ***/

define('NGG_BASIC_SINGLEPIC', 'photocrati-nextgen_basic_singlepic');

class M_NextGen_Basic_Singlepic extends C_Base_Module
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
            NGG_BASIC_SINGLEPIC,
            'NextGen Basic Singlepic',
            'Provides a singlepic gallery for NextGEN Gallery',
            '3.1.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Basic_SinglePic_Installer');
    }

    function initialize()
    {
        parent::initialize();
        if (is_admin()) {
            $forms = C_Form_Manager::get_instance();
            $forms->add_form(
                NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_SINGLEPIC
            );
        }
    }


    function _register_adapters()
    {
		// Provides default values for the display type
		$this->get_registry()->add_adapter(
			'I_Display_Type_Mapper',
			'A_NextGen_Basic_Singlepic_Mapper'
		);

        if (M_Attach_To_Post::is_atp_url() || is_admin())
        {
            // Provides the display settings form for the SinglePic display type
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_SinglePic_Form',
                $this->module_id
            );
        }

        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
        {
            // Provides settings fields and frontend rendering
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Singlepic_Controller',
                $this->module_id
            );
        }
    }

	function _register_hooks()
	{
        if (apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id)
        && (!defined('NGG_DISABLE_LEGACY_SHORTCODES') || !NGG_DISABLE_LEGACY_SHORTCODES))
        {
            C_NextGen_Shortcode_Manager::add('singlepic', array(&$this, 'render_singlepic'));
            C_NextGen_Shortcode_Manager::add('nggsinglepic', array(&$this, 'render_singlepic'));

            // TODO - why aren't we using the singlepic controller for this instead?
            // enqueue the singlepic CSS if an inline image has the ngg-singlepic class
            if (!$this->is_rest_request()) add_filter('the_content', array(&$this, 'enqueue_singlepic_css'), PHP_INT_MAX, 1);
        }
	}

	function is_rest_request()
	{
		return defined('REST_REQUEST') || strpos($_SERVER['REQUEST_URI'], 'wp-json') !== FALSE;
	}

    /**
     * Examines 'the_content' string for img.ngg-singlepic and enqueues styling when found
     *
     * @param string $content
     * @return string $content
     */
    function enqueue_singlepic_css($content)
    {
        if (preg_match("#<img.*ngg-singlepic.*>#", $content, $matches)) {
            $router = C_Router::get_instance();
            wp_enqueue_style(
                'nextgen_basic_singlepic_style',
                $router->get_static_url(NGG_BASIC_SINGLEPIC . '#nextgen_basic_singlepic.css'),
                array(),
                NGG_SCRIPT_VERSION
            );
        }

        return $content;
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

	function render_singlepic($params, $inner_content=NULL)
	{
		$params['display_type'] = $this->_get_param('display_type', NGG_BASIC_SINGLEPIC, $params);
        $params['image_ids'] = $this->_get_param('id', NULL, $params);
        unset($params['id']);

		$renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
	}

    function get_type_list()
    {
        return array(
            'A_Nextgen_Basic_Singlepic' => 'adapter.nextgen_basic_singlepic.php',
            'A_Nextgen_Basic_Singlepic_Controller' => 'adapter.nextgen_basic_singlepic_controller.php',
            'A_Nextgen_Basic_Singlepic_Form' => 'adapter.nextgen_basic_singlepic_form.php',
            'C_NextGen_Basic_SinglePic_Installer' => 'class.nextgen_basic_singlepic_installer.php',
            'A_Nextgen_Basic_Singlepic_Mapper' => 'adapter.nextgen_basic_singlepic_mapper.php'
        );
    }
}

class C_NextGen_Basic_SinglePic_Installer extends C_Gallery_Display_Installer
{
	function install($reset = FALSE)
	{
		$this->install_display_type(
			NGG_BASIC_SINGLEPIC, array(
				'title'					=>	__('NextGEN Basic SinglePic', 'nggallery'),
				'entity_types'			=>	array('image'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_singlepic#preview.gif',
				'default_source'		=>	'galleries',
				'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 60,
				'hidden_from_ui'        =>  TRUE, // todo remove this, use hidden_from_igw instead
                'hidden_from_igw'       =>  TRUE,
                'aliases'               =>  array(
                    'basic_singlepic',
                    'singlepic',
                    'nextgen_basic_singlepic'
                )
			));
	}
}

new M_NextGen_Basic_Singlepic();
