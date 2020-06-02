<?php

define('NGG_BASIC_IMAGEBROWSER', 'photocrati-nextgen_basic_imagebrowser');

class M_NextGen_Basic_ImageBrowser extends C_Base_Module
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
            NGG_BASIC_IMAGEBROWSER,
            'NextGEN Basic ImageBrowser',
            'Provides the NextGEN Basic ImageBrowser Display Type',
            '3.3.2',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

        C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Basic_ImageBrowser_Installer');
    }

    function initialize()
    {
        parent::initialize();
        
        if (is_admin())
        {
            $forms = C_Form_Manager::get_instance();
            $forms->add_form(NGG_DISPLAY_SETTINGS_SLUG, NGG_BASIC_IMAGEBROWSER);
        }
    }

    /**
     * Register adapters required for the NextGen Basic ImageBrowser
     */
    function _register_adapters()
    {
        $registry = $this->get_registry();
        $registry->add_adapter('I_Display_Type_Mapper', 'A_NextGen_Basic_ImageBrowser_Mapper');
        $registry->add_adapter('I_Display_Type',        'A_NextGen_Basic_ImageBrowser');
        $registry->add_adapter('I_Routing_App',         'A_NextGen_Basic_ImageBrowser_Urls');

        if (M_Attach_To_Post::is_atp_url() || is_admin())
            $registry->add_adapter('I_Form', 'A_NextGen_Basic_ImageBrowser_Form', $this->module_id);

        if (!is_admin() && apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id))
            $registry->add_adapter('I_Display_Type_Controller', 'A_NextGen_Basic_ImageBrowser_Controller', $this->module_id);
    }

    function _register_hooks()
    {
        if (apply_filters('ngg_load_frontend_logic', TRUE, $this->module_id)
        && (!defined('NGG_DISABLE_LEGACY_SHORTCODES') || !NGG_DISABLE_LEGACY_SHORTCODES))
        {
            C_NextGen_Shortcode_Manager::add('imagebrowser',    array($this, 'render_shortcode'));
            C_NextGen_Shortcode_Manager::add('nggimagebrowser', array($this, 'render_shortcode'));
        }

        add_action('ngg_routes', array(&$this, 'define_routes'));
    }

    function define_routes($router)
    {
        $slug = '/'.C_NextGen_Settings::get_instance()->router_param_slug;
        $router->rewrite("{*}{$slug}{*}/image/{\\w}", "{1}{$slug}{2}/pid--{3}");
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

    function render_shortcode($params, $inner_content=NULL)
    {
        $params['gallery_ids']  = $this->_get_param('id', NULL, $params);
        $params['source']       = $this->_get_param('source', 'galleries', $params);
        $params['display_type'] = $this->_get_param('display_type', NGG_BASIC_IMAGEBROWSER, $params);

        unset($params['id']);

        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        return $renderer->display_images($params, $inner_content);
    }

    function get_type_list()
    {
        return array(
            'A_Nextgen_Basic_Imagebrowser'            => 'adapter.nextgen_basic_imagebrowser.php',
            'A_Nextgen_Basic_Imagebrowser_Controller' => 'adapter.nextgen_basic_imagebrowser_controller.php',
            'A_Nextgen_Basic_Imagebrowser_Form'       => 'adapter.nextgen_basic_imagebrowser_form.php',
            'A_Nextgen_Basic_Imagebrowser_Mapper'     => 'adapter.nextgen_basic_imagebrowser_mapper.php',
            'A_Nextgen_Basic_Imagebrowser_Urls'       => 'adapter.nextgen_basic_imagebrowser_urls.php',
            'C_Nextgen_Basic_Imagebrowser_Installer'  => 'class.nextgen_basic_imagebrowser_installer.php'
        );
    }
}

/**
 * Show an image browser. Ngglegacy function
 * @param int $galleryID
 * @param string $template
 * @return string
 */
function nggShowImageBrowser($galleryID, $template = '')
{
    $renderer = C_Displayed_Gallery_Renderer::get_instance();
    $retval = $renderer->display_images(array(
        'gallery_ids'   =>  array($galleryID),
        'display_type'  =>  NGG_BASIC_IMAGEBROWSER,
        'template'      =>  $template
    ));

    return apply_filters('ngg_show_imagebrowser_content', $retval, $galleryID);
}

/**
 * Create an image browser from a list of image objects. Ngglegacy function
 * @param array $picturelist
 * @param string $template (optional)
 * @return string
 */
function nggCreateImageBrowser($picturelist, $template = '')
{
    $renderer = C_Displayed_Gallery_Renderer::get_instance();
    $image_ids = array();
    foreach ($picturelist as $image) $image_ids[] = $image->pid;
    return $renderer->display_images(array(
        'image_ids'     =>  $image_ids,
        'display_type'  =>  NGG_BASIC_IMAGEBROWSER,
        'template'      =>  $template
    ));
}

/**
 * @mixin C_Display_Type_Installer
 */
class C_NextGen_Basic_ImageBrowser_Installer extends C_Gallery_Display_Installer
{
    function install($reset = FALSE)
    {
        $this->install_display_type(
            NGG_BASIC_IMAGEBROWSER, array(
                'title'                 => __('NextGEN Basic ImageBrowser', 'nggallery'),
                'entity_types'          => array('image'),
                'preview_image_relpath' => NGG_BASIC_IMAGEBROWSER . '#preview.jpg',
                'default_source'        => 'galleries',
                'view_order'            => NGG_DISPLAY_PRIORITY_BASE + 20,
                'aliases'               => array(
                    'basic_imagebrowser',
                    'imagebrowser',
                    'nextgen_basic_imagebrowser'
                )
            )
        );
    }
}

new M_NextGen_Basic_ImageBrowser();