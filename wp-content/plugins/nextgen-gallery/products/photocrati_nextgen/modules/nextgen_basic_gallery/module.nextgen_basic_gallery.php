<?php
/*
{
    Module: photocrati-nextgen_basic_gallery,
    Depends: { photocrati-nextgen_pagination }
}
*/

define(
    'NGG_BASIC_THUMBNAILS',
    'photocrati-nextgen_basic_thumbnails'
);

define(
    'NGG_BASIC_SLIDESHOW',
    'photocrati-nextgen_basic_slideshow'
);

define(
    'NGG_BASIC_GALLERY',
    'photocrati-nextgen_basic_gallery'
);


class M_NextGen_Basic_Gallery extends C_Base_Module
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
            NGG_BASIC_GALLERY,
            'NextGEN Basic Gallery',
            "Provides NextGEN Gallery's basic thumbnail/slideshow integrated gallery",
            '3.1.8',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Basic_Gallery_Installer');
    }

    function initialize()
    {
        parent::initialize();
        if (is_admin()) {
            $forms = C_Form_Manager::get_instance();
            $forms->add_form(NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_THUMBNAILS);
            $forms->add_form(NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_SLIDESHOW);
        }

    }

    function get_type_list()
    {
        return array(
            'C_Nextgen_Basic_Gallery_Installer' => 'class.nextgen_basic_gallery_installer.php',
            'A_Nextgen_Basic_Gallery_Mapper' => 'adapter.nextgen_basic_gallery_mapper.php',
            'A_Nextgen_Basic_Gallery_Urls' => 'adapter.nextgen_basic_gallery_urls.php',
            'A_Nextgen_Basic_Gallery_Validation' => 'adapter.nextgen_basic_gallery_validation.php',
            'A_Nextgen_Basic_Slideshow_Controller' => 'adapter.nextgen_basic_slideshow_controller.php',
            'A_Nextgen_Basic_Slideshow_Form' => 'adapter.nextgen_basic_slideshow_form.php',
            'A_Nextgen_Basic_Thumbnail_Form' => 'adapter.nextgen_basic_thumbnail_form.php',
            'A_Nextgen_Basic_Thumbnails_Controller' => 'adapter.nextgen_basic_thumbnails_controller.php',
            'Mixin_Nextgen_Basic_Gallery_Controller' => 'mixin.nextgen_basic_gallery_controller.php',
            'A_NextGen_Basic_Gallery_Controller'    =>  'adapter.nextgen_basic_gallery_controller.php'
        );
    }
    
   
    function _register_adapters()
    {
        if (M_Attach_To_Post::is_atp_url() || is_admin())
        {
            // Provides the display type forms
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_Slideshow_Form',
                NGG_BASIC_SLIDESHOW
            );
            $this->get_registry()->add_adapter(
                'I_Form',
                'A_NextGen_Basic_Thumbnail_Form',
                NGG_BASIC_THUMBNAILS
            );
        }

        // Frontend-only components
        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
        {
            // Provides the controllers for the display types
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Slideshow_Controller',
                NGG_BASIC_SLIDESHOW
            );
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Thumbnails_Controller',
                NGG_BASIC_THUMBNAILS
            );

            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Gallery_Controller',
                NGG_BASIC_SLIDESHOW
            );
            $this->get_registry()->add_adapter(
                'I_Display_Type_Controller',
                'A_NextGen_Basic_Gallery_Controller',
                NGG_BASIC_THUMBNAILS
            );
        }
        
        // Provide defaults for the display types
        $this->get_registry()->add_adapter(
            'I_Display_Type_Mapper',
            'A_NextGen_Basic_Gallery_Mapper'
        );
        
        // Provides validation for the display types
        $this->get_registry()->add_adapter(
            'I_Display_Type',
            'A_NextGen_Basic_Gallery_Validation'
        );
        
        // Provides url generation support for the display types
        $this->get_registry()->add_adapter(
			'I_Routing_App',
			'A_NextGen_Basic_Gallery_Urls'
		);
    }
    
    function _register_hooks()
	{
        if (apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id)
        && (!defined('NGG_DISABLE_LEGACY_SHORTCODES') || !NGG_DISABLE_LEGACY_SHORTCODES))
        {
            C_NextGen_Shortcode_Manager::add('random',    array(&$this, 'render_random_images'));
            C_NextGen_Shortcode_Manager::add('recent',    array(&$this, 'render_recent_images'));
            C_NextGen_Shortcode_Manager::add('thumb',     array(&$this, 'render_thumb_shortcode'));
            C_NextGen_Shortcode_Manager::add('slideshow', array(&$this, 'render_slideshow'));
            C_NextGen_Shortcode_Manager::add('nggallery',    array(&$this, 'render'));
            C_NextGen_Shortcode_Manager::add('nggtags',      array(&$this, 'render_based_on_tags'));
            C_NextGen_Shortcode_Manager::add('nggslideshow', array(&$this, 'render_slideshow'));
            C_NextGen_Shortcode_Manager::add('nggrandom',    array(&$this, 'render_random_images'));
            C_NextGen_Shortcode_Manager::add('nggrecent',    array(&$this, 'render_recent_images'));
            C_NextGen_Shortcode_Manager::add('nggthumb',     array(&$this, 'render_thumb_shortcode'));
        }

        add_action('ngg_routes', array(&$this, 'define_routes'));

        add_filter('ngg_atp_show_display_type', array($this, 'atp_show_basic_galleries'), 10, 2);

        add_filter('ngg_' . NGG_BASIC_THUMBNAILS . '_template_dirs', array($this, 'filter_thumbnail_view_dir'));

        add_filter('ngg_' . NGG_BASIC_SLIDESHOW . '_template_dirs', array($this, 'filter_slideshow_view_dir'));
	}

    function define_routes($router)
    {
        $slug = '/'.C_NextGen_Settings::get_instance()->router_param_slug;
        $router->rewrite("{*}{$slug}{*}/image/{*}",         "{1}{$slug}{2}/pid--{3}");
        $router->rewrite("{*}{$slug}{*}/slideshow/{*}",     "{1}{$slug}{2}/show--" . NGG_BASIC_SLIDESHOW  . "/{3}");
        $router->rewrite("{*}{$slug}{*}/thumbnails/{*}",    "{1}{$slug}{2}/show--".  NGG_BASIC_THUMBNAILS . "/{3}");
        $router->rewrite("{*}{$slug}{*}/show--slide/{*}",   "{1}{$slug}{2}/show--" . NGG_BASIC_SLIDESHOW  . "/{3}");
        $router->rewrite("{*}{$slug}{*}/show--gallery/{*}", "{1}{$slug}{2}/show--" . NGG_BASIC_THUMBNAILS . "/{3}");
        $router->rewrite("{*}{$slug}{*}/page/{\\d}{*}",     "{1}{$slug}{2}/nggpage--{3}{4}");
    }

    /**
     * ATP filters display types by not displaying those whose name attribute isn't an active POPE module. This
     * is a workaround/hack to compensate for basic slideshow & thumbnails sharing a module.
     *
     * @param bool $available
     * @param C_Display_Type $display_type
     * @return bool
     */
    function atp_show_basic_galleries($available, $display_type)
    {
        if (in_array($display_type->name, array(NGG_BASIC_THUMBNAILS, NGG_BASIC_SLIDESHOW)))
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
     * Short-cut for rendering an thumbnail gallery
     * @param array $params
     * @param null $inner_content
     * @return string
     */
	function render($params, $inner_content=NULL)
    {
        $params['gallery_ids']     = $this->_get_param('id', NULL, $params);
        $params['display_type']    = $this->_get_param('display_type', NGG_BASIC_THUMBNAILS, $params);
        if (isset($params['images']))
        {
            $params['images_per_page'] = $this->_get_param('images', NULL, $params);
        }
        unset($params['id']);
        unset($params['images']);

		$renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
    }

	function render_based_on_tags($params, $inner_content=NULL)
    {
        $params['tag_ids']      = $this->_get_param('gallery', $this->_get_param('album', array(), $params), $params);
        $params['source']       = $this->_get_param('source', 'tags', $params);
        $params['display_type'] = $this->_get_param('display_type', NGG_BASIC_THUMBNAILS, $params);
        unset($params['gallery']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
    }

	function render_random_images($params, $inner_content=NULL)
	{
		$params['source']             = $this->_get_param('source', 'random', $params);
        $params['images_per_page']    = $this->_get_param('max', NULL, $params);
        $params['disable_pagination'] = $this->_get_param('disable_pagination', TRUE, $params);
        $params['display_type']       = $this->_get_param('display_type', NGG_BASIC_THUMBNAILS, $params);

        // inside if because Mixin_Displayed_Gallery_Instance_Methods->get_entities() doesn't handle NULL container_ids
        // correctly
        if (isset($params['id']))
        {
            $params['container_ids'] = $this->_get_param('id', NULL, $params);
        }

        unset($params['max']);
        unset($params['id']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
	}

	function render_recent_images($params, $inner_content=NULL)
	{
		        $params['source']             = $this->_get_param('source', 'recent', $params);
        $params['images_per_page']    = $this->_get_param('max', NULL, $params);
        $params['disable_pagination'] = $this->_get_param('disable_pagination', TRUE, $params);
        $params['display_type']       = $this->_get_param('display_type', NGG_BASIC_THUMBNAILS, $params);

        if (isset($params['id']))
        {
            $params['container_ids'] = $this->_get_param('id', NULL, $params);
        }

        unset($params['max']);
        unset($params['id']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
	}

	function render_thumb_shortcode($params, $inner_content=NULL)
	{
		$params['entity_ids']   = $this->_get_param('id', NULL, $params);
        $params['source']       = $this->_get_param('source', 'galleries', $params);
        $params['display_type'] = $this->_get_param('display_type', NGG_BASIC_THUMBNAILS, $params);
        unset($params['id']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
	}
    
	function render_slideshow($params, $inner_content=NULL)
	{
		$params['gallery_ids']    = $this->_get_param('id', NULL, $params);
        $params['display_type']   = $this->_get_param('display_type', NGG_BASIC_SLIDESHOW, $params);
        $params['gallery_width']  = $this->_get_param('w', NULL, $params);
        $params['gallery_height'] = $this->_get_param('h', NULL, $params);
        unset($params['id'], $params['w'], $params['h']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
	}    

    function filter_thumbnail_view_dir($dirs) 
    {
        $dirs['default'] = C_Component_Registry::get_instance()->get_module_dir(NGG_BASIC_GALLERY) . DIRECTORY_SEPARATOR . 'templates/thumbnails';
        return $dirs;
    }

    function filter_slideshow_view_dir($dirs) 
    {
        $dirs['default'] = C_Component_Registry::get_instance()->get_module_dir(NGG_BASIC_GALLERY) . DIRECTORY_SEPARATOR . 'templates/slideshow';
        return $dirs;
    }

}

/**
 * Wrapper to I_Displayed_Gallery_Renderer->display_images(); this will display
 * a basic thumbnails gallery
 *
 * @param int $galleryID Gallery ID
 * @param string $template Path to template file
 * @param bool $images_per_page Basic thumbnails setting
 */
function nggShowGallery($galleryID, $template = '', $images_per_page = FALSE)
{
	$args = array(
		'source' => 'galleries',
		'container_ids' => $galleryID
	);

	if (apply_filters('ngg_show_imagebrowser_first', FALSE, $galleryID))
		$args['display_type'] = NGG_BASIC_IMAGEBROWSER;
	else
		$args['display_type'] = NGG_BASIC_THUMBNAILS;

	if (!empty($template))
		$args['template'] = $template;
	if (!empty($images_per_page))
		$args['images_per_page'] = $images_per_page;

	echo C_Displayed_Gallery_Renderer::get_instance()->display_images($args);
}


/**
 * Wrapper to I_Displayed_Gallery_Renderer->display_images(); this will display
 * a basic slideshow gallery
 *
 * @param int $galleryID Gallery ID
 * @param int $width Gallery width
 * @param int $height Gallery height
 */
function nggShowSlideshow($galleryID, $width, $height)
{
	$args = array(
		'source'         => 'galleries',
		'container_ids'  => $galleryID,
		'gallery_width'  => $width,
		'gallery_height' => $height,
		'display_type'   => NGG_BASIC_SLIDESHOW
	);

	echo C_Displayed_Gallery_Renderer::get_instance()->display_images($args);
}

class C_NextGen_Basic_Gallery_Installer extends C_Gallery_Display_Installer
{
	function install($reset = FALSE)
	{
		$this->install_display_type(NGG_BASIC_THUMBNAILS,
			array(
				'title'					=>	__('NextGEN Basic Thumbnails', 'nggallery'),
                'module_id'             =>  NGG_BASIC_GALLERY,      
				'entity_types'			=>	array('image'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_gallery#thumb_preview.jpg',
				'default_source'		=>	'galleries',
				'view_order'            =>  NGG_DISPLAY_PRIORITY_BASE,
                'aliases'               =>  array(
                    'basic_thumbnail',
                    'basic_thumbnails',
                    'nextgen_basic_thumbnails',
                )
			)
		);

		$this->install_display_type(NGG_BASIC_SLIDESHOW,
			array(
				'title'					=>	__('NextGEN Basic Slideshow', 'nggallery'),
                'module_id'             =>  NGG_BASIC_GALLERY,
				'entity_types'			=>	array('image'),
				'preview_image_relpath'	=>	'photocrati-nextgen_basic_gallery#slideshow_preview.jpg',
				'default_source'		=>	'galleries',
				'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 10,
                'aliases'               =>  array(
                    'basic_slideshow',
                    'nextgen_basic_slideshow'
                )
			)
		);
	}
}

new M_NextGen_Basic_Gallery;
