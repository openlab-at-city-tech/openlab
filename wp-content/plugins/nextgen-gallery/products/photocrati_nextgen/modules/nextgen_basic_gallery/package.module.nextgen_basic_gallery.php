<?php
/**
 * Class A_NextGen_Basic_Gallery_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller for both "photocrati-nextgen_basic_slideshow" and "photocrati-nextgen_basic_thumbnails" contexts
 * @property C_Display_Type_Controller|A_NextGen_Basic_Gallery_Controller $object
 */
class A_NextGen_Basic_Gallery_Controller extends Mixin
{
    protected static $alternate_displayed_galleries = array();
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @return C_Displayed_Gallery
     */
    function get_alternate_displayed_gallery($displayed_gallery)
    {
        // Prevent recursive checks for further alternates causing additional modifications to the settings array
        $id = $displayed_gallery->id();
        if (!empty(self::$alternate_displayed_galleries[$id])) {
            return self::$alternate_displayed_galleries[$id];
        }
        $show = $this->object->param('show');
        $pid = $this->object->param('pid');
        if (!empty($pid) && isset($displayed_gallery->display_settings['use_imagebrowser_effect']) && intval($displayed_gallery->display_settings['use_imagebrowser_effect'])) {
            $show = NGG_BASIC_IMAGEBROWSER;
        }
        // Are we to display a different display type?
        if (!empty($show)) {
            $params = (array) $displayed_gallery->get_entity();
            $ds = $params['display_settings'];
            if ((!empty($ds['show_slideshow_link']) || !empty($ds['show_thumbnail_link']) || !empty($ds['use_imagebrowser_effect'])) && $show != $this->object->context) {
                // Render the new display type
                $renderer = C_Displayed_Gallery_Renderer::get_instance();
                $params['original_display_type'] = $displayed_gallery->display_type;
                $params['original_settings'] = $displayed_gallery->display_settings;
                $params['display_type'] = $show;
                $params['display_settings'] = array();
                $displayed_gallery = $renderer->params_to_displayed_gallery($params);
                if (is_null($displayed_gallery->id())) {
                    $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));
                }
                self::$alternate_displayed_galleries[$id] = $displayed_gallery;
            }
        }
        return $displayed_gallery;
    }
    function index_action($displayed_gallery, $return = FALSE)
    {
        $alternate_displayed_gallery = $this->object->get_alternate_displayed_gallery($displayed_gallery);
        if ($displayed_gallery !== $alternate_displayed_gallery) {
            $renderer = C_Displayed_Gallery_Renderer::get_instance();
            return $renderer->display_images($alternate_displayed_gallery, $return);
        }
        return $this->call_parent('index_action', $displayed_gallery, $return);
    }
    /**
     * Returns a url to view the displayed gallery using an alternate display
     * type
     * @param C_Displayed_Gallery $displayed_gallery
     * @param string $display_type
     * @return string
     */
    function get_url_for_alternate_display_type($displayed_gallery, $display_type, $origin_url = FALSE)
    {
        if (!$origin_url && !empty($displayed_gallery->display_settings['original_display_type']) && !empty($_SERVER['NGG_ORIG_REQUEST_URI'])) {
            $origin_url = $_SERVER['NGG_ORIG_REQUEST_URI'];
        }
        $url = $origin_url ? $origin_url : $this->object->get_routed_url(TRUE);
        $url = $this->object->remove_param_for($url, 'show', $displayed_gallery->id());
        $url = $this->object->set_param_for($url, 'show', $display_type, $displayed_gallery->id());
        return $url;
    }
}
/**
 * Sets default values for the NextGen Basic Slideshow display type
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Basic_Gallery_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name)) {
            if ($entity->name == NGG_BASIC_SLIDESHOW) {
                $this->set_slideshow_defaults($entity);
            } else {
                if ($entity->name == NGG_BASIC_THUMBNAILS) {
                    $this->set_thumbnail_defaults($entity);
                }
            }
        }
    }
    function set_slideshow_defaults($entity)
    {
        $settings = C_NextGen_Settings::get_instance();
        $this->object->_set_default_value($entity, 'settings', 'gallery_width', $settings->irWidth);
        $this->object->_set_default_value($entity, 'settings', 'gallery_height', $settings->irHeight);
        $this->object->_set_default_value($entity, 'settings', 'show_thumbnail_link', $settings->galShowSlide ? 1 : 0);
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_link_text', $settings->galTextGallery);
        $this->object->_set_default_value($entity, 'settings', 'template', '');
        $this->object->_set_default_value($entity, 'settings', 'display_view', 'default');
        $this->object->_set_default_value($entity, 'settings', 'autoplay', 1);
        $this->object->_set_default_value($entity, 'settings', 'pauseonhover', 1);
        $this->object->_set_default_value($entity, 'settings', 'arrows', 0);
        $this->object->_set_default_value($entity, 'settings', 'interval', 3000);
        $this->object->_set_default_value($entity, 'settings', 'transition_speed', 300);
        $this->object->_set_default_value($entity, 'settings', 'transition_style', 'fade');
        // Part of the pro-modules
        $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
    }
    function set_thumbnail_defaults($entity)
    {
        $settings = C_NextGen_Settings::get_instance();
        $default_template = isset($entity->settings["template"]) ? 'default' : 'default-view.php';
        $this->object->_set_default_value($entity, 'settings', 'display_view', $default_template);
        $this->object->_set_default_value($entity, 'settings', 'images_per_page', $settings->galImages);
        $this->object->_set_default_value($entity, 'settings', 'number_of_columns', $settings->galColumns);
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_width', $settings->thumbwidth);
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_height', $settings->thumbheight);
        $this->object->_set_default_value($entity, 'settings', 'show_all_in_lightbox', $settings->galHiddenImg);
        $this->object->_set_default_value($entity, 'settings', 'ajax_pagination', $settings->galAjaxNav);
        $this->object->_set_default_value($entity, 'settings', 'use_imagebrowser_effect', $settings->galImgBrowser);
        $this->object->_set_default_value($entity, 'settings', 'template', '');
        $this->object->_set_default_value($entity, 'settings', 'display_no_images_error', 1);
        // TODO: Should this be called enable pagination?
        $this->object->_set_default_value($entity, 'settings', 'disable_pagination', 0);
        // Alternative view support
        $this->object->_set_default_value($entity, 'settings', 'show_slideshow_link', $settings->galShowSlide ? 1 : 0);
        $this->object->_set_default_value($entity, 'settings', 'slideshow_link_text', $settings->galTextSlide);
        // override thumbnail settings
        $this->object->_set_default_value($entity, 'settings', 'override_thumbnail_settings', 0);
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_quality', '100');
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_crop', 1);
        $this->object->_set_default_value($entity, 'settings', 'thumbnail_watermark', 0);
        // Part of the pro-modules
        $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
    }
}
/**
 * Class A_NextGen_Basic_Gallery_Urls
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_NextGen_Basic_Gallery_Urls extends Mixin
{
    function create_parameter_segment($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        if ($key == 'show') {
            if ($value == NGG_BASIC_SLIDESHOW) {
                $value = 'slideshow';
            } elseif ($value == NGG_BASIC_THUMBNAILS) {
                $value = 'thumbnails';
            } elseif ($value == NGG_BASIC_IMAGEBROWSER) {
                $value = 'imagebrowser';
            }
            return $value;
        } elseif ($key == 'nggpage') {
            return 'page/' . $value;
        } else {
            return $this->call_parent('create_parameter_segment', $key, $value, $id, $use_prefix);
        }
    }
    function set_parameter_value($key, $value, $id = NULL, $use_prefix = FALSE, $url = FALSE)
    {
        $retval = $this->call_parent('set_parameter_value', $key, $value, $id, $use_prefix, $url);
        return $this->_set_ngglegacy_page_parameter($retval, $key, $value, $id, $use_prefix);
    }
    function remove_parameter($key, $id = NULL, $url = FALSE)
    {
        $retval = $this->call_parent('remove_parameter', $key, $id, $url);
        $retval = $this->_set_ngglegacy_page_parameter($retval, $key);
        // For some reason, we're not removing our parameters the way we should. Our routing system seems to be
        // a bit broken and so I'm adding an exception here.
        // TODO: Our parameter manipulations need to be flawless. Look into route cause
        if ($key == 'show') {
            $uri = explode('?', $retval);
            $uri = $uri[0];
            $settings = C_NextGen_Settings::get_instance();
            $regex = '#/' . $settings->get('router_param_slug', 'nggallery') . '.*(/?(slideshow|thumbnails|imagebrowser)/?)#';
            if (preg_match($regex, $retval, $matches)) {
                $retval = str_replace($matches[1], '', $retval);
            }
        }
        return $retval;
    }
    function _set_ngglegacy_page_parameter($retval, $key, $value = NULL, $id = NULL, $use_prefix = NULL)
    {
        // Get the settings manager
        $settings = C_NextGen_Settings::get_instance();
        // Create regex pattern
        $param_slug = preg_quote($settings->get('router_param_slug', 'nggallery'), '#');
        if ($key == 'nggpage') {
            $regex = "#(/{$param_slug}/.*)(/?page/\\d+/?)(.*)#";
            if (preg_match($regex, $retval, $matches)) {
                $new_segment = $value ? "/page/{$value}" : "";
                $retval = rtrim(str_replace($matches[0], rtrim($matches[1], "/") . $new_segment . ltrim($matches[3], "/"), $retval), "/");
            }
        }
        # Convert the nggpage parameter to a slug
        if (preg_match("#(/{$param_slug}/.*)nggpage--(.*)#", $retval, $matches)) {
            $retval = rtrim(str_replace($matches[0], rtrim($matches[1], "/") . "/page/" . ltrim($matches[2], "/"), $retval), "/");
        }
        # Convert the show parameter to a slug
        if (preg_match("#(/{$param_slug}/.*)show--(.*)#", $retval, $matches)) {
            $retval = rtrim(str_replace($matches[0], rtrim($matches[1], "/") . '/' . $matches[2], $retval), "/");
            $retval = str_replace(NGG_BASIC_SLIDESHOW, 'slideshow', $retval);
            $retval = str_replace(NGG_BASIC_THUMBNAILS, 'thumbnails', $retval);
            $retval = str_replace(NGG_BASIC_IMAGEBROWSER, 'imagebrowser', $retval);
        }
        return $retval;
    }
}
/**
 * Class A_NextGen_Basic_Gallery_Validation
 * @mixin C_Display_Type
 * @adapts I_Display_Type
 */
class A_NextGen_Basic_Gallery_Validation extends Mixin
{
    function validation()
    {
        if ($this->object->name == NGG_BASIC_THUMBNAILS) {
            $this->object->validates_presence_of('thumbnail_width');
            $this->object->validates_presence_of('thumbnail_height');
            $this->object->validates_numericality_of('thumbnail_width');
            $this->object->validates_numericality_of('thumbnail_height');
            $this->object->validates_numericality_of('images_per_page');
        } else {
            if ($this->object->name == NGG_BASIC_SLIDESHOW) {
                $this->object->validates_presence_of('gallery_width');
                $this->object->validates_presence_of('gallery_height');
                $this->object->validates_numericality_of('gallery_width');
                $this->object->validates_numericality_of('gallery_height');
            }
        }
        return $this->call_parent('validation');
    }
}
/**
 * Class A_NextGen_Basic_Slideshow_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller for "photocrati-nextgen_basic_slideshow" context
 */
class A_NextGen_Basic_Slideshow_Controller extends Mixin
{
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return (optional)
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        // We now hide option for triggers on this display type.
        // This ensures they do not show based on past settings.
        $displayed_gallery->display_settings['ngg_triggers_display'] = 'never';
        // Get the images to be displayed
        $current_page = (int) $this->param('nggpage', 1);
        if ($images = $displayed_gallery->get_included_entities()) {
            // Get the gallery storage component
            $storage = C_Gallery_Storage::get_instance();
            // Create parameter list for the view
            $params = $displayed_gallery->display_settings;
            $params['storage'] = $storage;
            $params['images'] = $images;
            $params['displayed_gallery_id'] = $displayed_gallery->id();
            $params['current_page'] = $current_page;
            $params['effect_code'] = $this->object->get_effect_code($displayed_gallery);
            $params['anchor'] = 'ngg-slideshow-' . $displayed_gallery->id() . '-' . rand(1, getrandmax()) . $current_page;
            $gallery_width = $displayed_gallery->display_settings['gallery_width'];
            $gallery_height = $displayed_gallery->display_settings['gallery_height'];
            $params['aspect_ratio'] = $gallery_width / $gallery_height;
            $params['placeholder'] = $this->object->get_static_url('photocrati-nextgen_basic_gallery#slideshow/placeholder.gif');
            // This was not set correctly in previous versions
            if (empty($params['cycle_effect'])) {
                $params['cycle_effect'] = 'fade';
            }
            // Are we to generate a thumbnail link?
            if ($displayed_gallery->display_settings['show_thumbnail_link']) {
                $params['thumbnail_link'] = $this->object->get_url_for_alternate_display_type($displayed_gallery, NGG_BASIC_THUMBNAILS);
            }
            $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
            $retval = $this->object->render_partial('photocrati-nextgen_basic_gallery#slideshow/index', $params, $return);
        } else {
            $retval = $this->object->render_partial('photocrati-nextgen_gallery_display#no_images_found', array(), $return);
        }
        return $retval;
    }
    /**
     * Enqueues all static resources required by this display type
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        wp_enqueue_style('ngg_basic_slideshow_style', $this->get_static_url('photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.css'), array(), NGG_SCRIPT_VERSION);
        // Add new scripts for slick based slideshow
        wp_enqueue_script('ngg_slick', $this->get_static_url("photocrati-nextgen_basic_gallery#slideshow/slick/slick-1.8.0-modded.js"), array('jquery'), NGG_SCRIPT_VERSION);
        wp_enqueue_style('ngg_slick_slideshow_style', $this->get_static_url('photocrati-nextgen_basic_gallery#slideshow/slick/slick.css'), array(), NGG_SCRIPT_VERSION);
        wp_enqueue_style('ngg_slick_slideshow_theme', $this->get_static_url('photocrati-nextgen_basic_gallery#slideshow/slick/slick-theme.css'), array(), NGG_SCRIPT_VERSION);
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
    }
    /**
     * Provides the url of the JavaScript library required for
     * NextGEN Basic Slideshow to display
     * @return string
     */
    function _get_js_lib_url()
    {
        return $this->get_static_url('photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.js');
    }
}
/**
 * Provides the display settings form for the NextGen Basic Slideshow
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_basic_slideshow" context
 */
class A_NextGen_Basic_Slideshow_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_SLIDESHOW;
    }
    function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_slideshow_settings-js', $this->get_static_url('photocrati-nextgen_basic_gallery#slideshow/nextgen_basic_slideshow_settings.js'), array('jquery.nextgen_radio_toggle'));
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        return array('nextgen_basic_slideshow_gallery_dimensions', 'nextgen_basic_slideshow_autoplay', 'nextgen_basic_slideshow_pauseonhover', 'nextgen_basic_slideshow_arrows', 'nextgen_basic_slideshow_transition_style', 'nextgen_basic_slideshow_interval', 'nextgen_basic_slideshow_transition_speed', 'nextgen_basic_slideshow_show_thumbnail_link', 'nextgen_basic_slideshow_thumbnail_link_text', 'display_view');
    }
    /**
     * Renders the autoplay field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_autoplay_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'autoplay', __('Autoplay?', 'nggallery'), $display_type->settings['autoplay']);
    }
    /**
     * Renders the Pause on Hover field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_pauseonhover_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'pauseonhover', __('Pause on Hover?', 'nggallery'), $display_type->settings['pauseonhover']);
    }
    /**
     * Renders the arrows field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_arrows_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'arrows', __('Show Arrows?', 'nggallery'), $display_type->settings['arrows']);
    }
    /**
     * Renders the effect field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_transition_style_field($display_type)
    {
        return $this->_render_select_field($display_type, 'transition_style', __('Transition Style', 'nggallery'), array('slide' => 'Slide', 'fade' => 'Fade'), $display_type->settings['transition_style'], '', FALSE);
    }
    /**
     * Renders the interval field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_interval_field($display_type)
    {
        return $this->_render_number_field($display_type, 'interval', __('Interval', 'nggallery'), $display_type->settings['interval'], '', FALSE, __('Milliseconds', 'nggallery'), 1);
    }
    /**
     * Renders the interval field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_transition_speed_field($display_type)
    {
        return $this->_render_number_field($display_type, 'transition_speed', __('Transition Speed', 'nggallery'), $display_type->settings['transition_speed'], '', FALSE, __('Milliseconds', 'nggallery'), 1);
    }
    function _render_nextgen_basic_slideshow_gallery_dimensions_field($display_type)
    {
        return $this->render_partial('photocrati-nextgen_basic_gallery#slideshow/nextgen_basic_slideshow_settings_gallery_dimensions', array('display_type_name' => $display_type->name, 'gallery_dimensions_label' => __('Maximum dimensions', 'nggallery'), 'gallery_dimensions_tooltip' => __('Certain themes may allow images to flow over their container if this setting is too large', 'nggallery'), 'gallery_width' => $display_type->settings['gallery_width'], 'gallery_height' => $display_type->settings['gallery_height']), True);
    }
    /**
     * Renders the show_thumbnail_link settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_show_thumbnail_link_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_thumbnail_link', __('Show thumbnail link', 'nggallery'), $display_type->settings['show_thumbnail_link']);
    }
    /**
     * Renders the thumbnail_link_text settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_slideshow_thumbnail_link_text_field($display_type)
    {
        return $this->_render_text_field($display_type, 'thumbnail_link_text', __('Thumbnail link text', 'nggallery'), $display_type->settings['thumbnail_link_text'], '', !empty($display_type->settings['show_thumbnail_link']) ? FALSE : TRUE);
    }
}
/**
 * Class A_NextGen_Basic_Thumbnail_Form
 * @mixin C_Form
 * @adapts I_Form for photocrati-nextgen_basic_thumbnails context
 */
class A_NextGen_Basic_Thumbnail_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_THUMBNAILS;
    }
    /**
     * Enqueues static resources required by this form
     */
    function enqueue_static_resources()
    {
        $this->object->enqueue_style('nextgen_basic_thumbnails_settings', $this->object->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails_settings.css'));
        $this->object->enqueue_script('nextgen_basic_thumbnails_settings', $this->object->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails_settings.js'), array('jquery.nextgen_radio_toggle'));
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        return array('thumbnail_override_settings', 'nextgen_basic_thumbnails_images_per_page', 'nextgen_basic_thumbnails_number_of_columns', 'ajax_pagination', 'nextgen_basic_thumbnails_hidden', 'nextgen_basic_thumbnails_imagebrowser_effect', 'nextgen_basic_thumbnails_show_slideshow_link', 'nextgen_basic_thumbnails_slideshow_link_text', 'display_view', 'nextgen_basic_templates_template');
    }
    /**
     * Renders the images_per_page settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_thumbnails_images_per_page_field($display_type)
    {
        return $this->_render_number_field($display_type, 'images_per_page', __('Images per page', 'nggallery'), $display_type->settings['images_per_page'], __('0 will display all images at once', 'nggallery'), FALSE, '# of images', 0);
    }
    /**
     * Renders the number_of_columns settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_thumbnails_number_of_columns_field($display_type)
    {
        return $this->_render_number_field($display_type, 'number_of_columns', __('Number of columns to display', 'nggallery'), $display_type->settings['number_of_columns'], '', FALSE, __('# of columns', 'nggallery'), 0);
    }
    /**
     * Renders the 'Add hidden images' settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_thumbnails_hidden_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_all_in_lightbox', __('Add Hidden Images', 'nggallery'), $display_type->settings['show_all_in_lightbox'], __('If pagination is used this option will show all images in the modal window (Thickbox, Lightbox etc.) This increases page load.', 'nggallery'));
    }
    function _render_nextgen_basic_thumbnails_imagebrowser_effect_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'use_imagebrowser_effect', __('Use imagebrowser effect', 'nggallery'), $display_type->settings['use_imagebrowser_effect'], __('When active each image in the gallery will link to an imagebrowser display and lightbox effects will not be applied.', 'nggallery'));
    }
    /**
     * Renders the show_slideshow_link settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_thumbnails_show_slideshow_link_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_slideshow_link', __('Show slideshow link', 'nggallery'), $display_type->settings['show_slideshow_link']);
    }
    /**
     * Renders the slideshow_link_text settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_thumbnails_slideshow_link_text_field($display_type)
    {
        return $this->_render_text_field($display_type, 'slideshow_link_text', __('Slideshow link text', 'nggallery'), $display_type->settings['slideshow_link_text'], '', !empty($display_type->settings['show_slideshow_link']) ? FALSE : TRUE);
    }
}
/**
 * Class A_NextGen_Basic_Thumbnails_Controller
 * @mixin Mixin_NextGen_Basic_Pagination
 */
class A_NextGen_Basic_Thumbnails_Controller extends Mixin
{
    /**
     * Adds framework support for thumbnails
     */
    function initialize()
    {
        $this->add_mixin('Mixin_NextGen_Basic_Pagination');
    }
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return (optional)
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        $display_settings = $displayed_gallery->display_settings;
        $gallery_id = $displayed_gallery->id();
        if (!$display_settings['disable_pagination']) {
            $current_page = (int) $this->param('nggpage', $gallery_id, 1);
        } else {
            $current_page = 1;
        }
        $offset = $display_settings['images_per_page'] * ($current_page - 1);
        $storage = C_Gallery_Storage::get_instance();
        $total = $displayed_gallery->get_entity_count();
        // Get the images to be displayed
        if ($display_settings['images_per_page'] > 0 && $display_settings['show_all_in_lightbox']) {
            // the "Add Hidden Images" feature works by loading ALL images and then marking the ones not on this page
            // as hidden (style="display: none")
            $images = $displayed_gallery->get_included_entities();
            $i = 0;
            foreach ($images as &$image) {
                if ($i < $display_settings['images_per_page'] * ($current_page - 1)) {
                    $image->hidden = TRUE;
                } elseif ($i >= $display_settings['images_per_page'] * $current_page) {
                    $image->hidden = TRUE;
                }
                $i++;
            }
        } else {
            // just display the images for this page, as normal
            $images = $displayed_gallery->get_included_entities($display_settings['images_per_page'], $offset);
        }
        // Are there images to display?
        if ($images) {
            // Create pagination
            if ($display_settings['images_per_page'] && !$display_settings['disable_pagination']) {
                $pagination_result = $this->object->create_pagination($current_page, $total, $display_settings['images_per_page'], urldecode($this->object->param('ajax_pagination_referrer') ?: ''));
                $this->object->remove_param('ajax_pagination_referrer');
                $pagination_prev = $pagination_result['prev'];
                $pagination_next = $pagination_result['next'];
                $pagination = $pagination_result['output'];
            } else {
                list($pagination_prev, $pagination_next, $pagination) = array(NULL, NULL, NULL);
            }
            $thumbnail_size_name = 'thumbnail';
            if ($display_settings['override_thumbnail_settings']) {
                $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                if ($dynthumbs != null) {
                    $dyn_params = array('width' => $display_settings['thumbnail_width'], 'height' => $display_settings['thumbnail_height']);
                    if ($display_settings['thumbnail_quality']) {
                        $dyn_params['quality'] = $display_settings['thumbnail_quality'];
                    }
                    if ($display_settings['thumbnail_crop']) {
                        $dyn_params['crop'] = true;
                    }
                    if ($display_settings['thumbnail_watermark']) {
                        $dyn_params['watermark'] = true;
                    }
                    $thumbnail_size_name = $dynthumbs->get_size_name($dyn_params);
                }
            }
            // Generate a slideshow link
            $slideshow_link = '';
            if ($display_settings['show_slideshow_link']) {
                // origin_url is necessary for ajax operations. slideshow_link_origin will NOT always exist.
                $origin_url = $this->object->param('ajax_pagination_referrer');
                $slideshow_link = $this->object->get_url_for_alternate_display_type($displayed_gallery, NGG_BASIC_SLIDESHOW, $origin_url);
            }
            // This setting 1) points all images to an imagebrowser display & 2) disables the lightbox effect
            if ($display_settings['use_imagebrowser_effect']) {
                if (!empty($displayed_gallery->display_settings['original_display_type']) && !empty($_SERVER['NGG_ORIG_REQUEST_URI'])) {
                    $origin_url = $_SERVER['NGG_ORIG_REQUEST_URI'];
                }
                $url = !empty($origin_url) ? $origin_url : $this->object->get_routed_url(TRUE);
                $url = $this->object->remove_param_for($url, 'image');
                $url = $this->object->set_param_for($url, 'image', '%STUB%', NULL, FALSE);
                $effect_code = "class='use_imagebrowser_effect' data-imagebrowser-url='{$url}'";
            } else {
                $effect_code = $this->object->get_effect_code($displayed_gallery);
            }
            // The render functions require different processing
            if (!empty($display_settings['template']) && $display_settings['template'] != 'default') {
                $this->object->add_mixin('A_NextGen_Basic_Template_Form');
                $this->object->add_mixin('Mixin_NextGen_Basic_Templates');
                $params = $this->object->prepare_legacy_parameters($images, $displayed_gallery, array('next' => empty($pagination_next) ? FALSE : $pagination_next, 'prev' => empty($pagination_prev) ? FALSE : $pagination_prev, 'pagination' => $pagination, 'slideshow_link' => $slideshow_link, 'effect_code' => $effect_code));
                $output = $this->object->legacy_render($display_settings['template'], $params, $return, 'gallery');
            } else {
                $params = $display_settings;
                // Additional values for the carousel display view
                if (!empty($this->param('pid'))) {
                    foreach ($images as $image) {
                        if ($image->image_slug === $this->param('pid')) {
                            $params['current_image'] = $image;
                        }
                    }
                    if ($pagination_result) {
                        $params['pagination_prev'] = $pagination_result['prev'];
                        $params['pagination_next'] = $pagination_result['next'];
                    }
                }
                if (empty($params['current_image'])) {
                    $params['current_image'] = reset($images);
                }
                $params['storage'] =& $storage;
                $params['images'] =& $images;
                $params['displayed_gallery_id'] = $gallery_id;
                $params['current_page'] = $current_page;
                $params['effect_code'] = $effect_code;
                $params['pagination'] = $pagination;
                $params['thumbnail_size_name'] = $thumbnail_size_name;
                $params['slideshow_link'] = $slideshow_link;
                $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
                $output = $this->object->render_partial('photocrati-nextgen_basic_gallery#thumbnails/index', $params, $return);
            }
            return $output;
        } else {
            if ($display_settings['display_no_images_error']) {
                return $this->object->render_partial("photocrati-nextgen_gallery_display#no_images_found", array(), $return);
            }
        }
        return '';
    }
    /**
     * Enqueues all static resources required by this display type
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('nextgen_basic_thumbnails_style', $this->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.css'), array(), NGG_SCRIPT_VERSION);
        if ($displayed_gallery->display_settings['ajax_pagination']) {
            wp_enqueue_script('nextgen-basic-thumbnails-ajax-pagination', $this->object->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/ajax_pagination.js'), array(), NGG_SCRIPT_VERSION);
        }
        wp_enqueue_style('nextgen_pagination_style', $this->get_static_url('photocrati-nextgen_pagination#style.css'), array(), NGG_SCRIPT_VERSION);
    }
    /**
     * Provides the url of the JavaScript library required for
     * NextGEN Basic Thumbnails to display
     * @return string
     */
    function _get_js_lib_url()
    {
        return $this->object->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.js');
    }
    /**
     * Override to the MVC method, allows the above imagebrowser-url to return as image/23 instead of image--23
     *
     * @param $url
     * @param $key
     * @param $value
     * @param null $id
     * @param bool $use_prefix
     * @return string
     */
    function set_param_for($url, $key, $value, $id = NULL, $use_prefix = FALSE)
    {
        $retval = $this->call_parent('set_param_for', $url, $key, $value, $id, $use_prefix);
        while (preg_match("#(image)--([^/]+)#", $retval, $matches)) {
            $retval = str_replace($matches[0], $matches[1] . '/' . $matches[2], $retval);
        }
        return $retval;
    }
}