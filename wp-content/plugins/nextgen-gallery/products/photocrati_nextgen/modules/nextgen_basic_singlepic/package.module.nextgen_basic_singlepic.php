<?php
/**
 * Class A_NextGen_Basic_Singlepic
 * @mixin C_Display_Type
 * @adapts I_Display_Type
 */
class A_NextGen_Basic_Singlepic extends Mixin
{
    function validation()
    {
        if ($this->object->name == NGG_BASIC_SINGLEPIC) {
        }
        return $this->call_parent('validation');
    }
}
/**
 * Class A_NextGen_Basic_Singlepic_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller for "photocrati-nextgen_basic_singlepic" context
 */
class A_NextGen_Basic_Singlepic_Controller extends Mixin
{
    /**
     * Displays the 'singlepic' display type
     *
     * @param C_Displayed_Gallery
     * @param bool $return (optional)
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        $storage = C_Gallery_Storage::get_instance();
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        $display_settings = $displayed_gallery->display_settings;
        // use this over get_included_entities() so we can display images marked 'excluded'
        $displayed_gallery->skip_excluding_globally_excluded_images = TRUE;
        $entities = $displayed_gallery->get_entities(1, FALSE, FALSE, 'included');
        $image = array_shift($entities);
        if (!$image) {
            return $this->object->render_partial("photocrati-nextgen_gallery_display#no_images_found", array(), $return);
        }
        switch ($display_settings['float']) {
            case 'left':
                $display_settings['float'] = 'ngg-left';
                break;
            case 'right':
                $display_settings['float'] = 'ngg-right';
                break;
            case 'center':
                $display_settings['float'] = 'ngg-center';
                break;
            default:
                $display_settings['float'] = '';
                break;
        }
        $params = array();
        if (!empty($display_settings['link'])) {
            $target = $display_settings['link_target'];
            $effect_code = '';
        } else {
            $display_settings['link'] = $storage->get_image_url($image, 'full', TRUE);
            $target = '_self';
            $effect_code = $this->object->get_effect_code($displayed_gallery);
        }
        $params['target'] = $target;
        // mode is a legacy parameter
        if (!is_array($display_settings['mode'])) {
            $display_settings['mode'] = explode(',', $display_settings['mode']);
        }
        if (in_array('web20', $display_settings['mode'])) {
            $display_settings['display_reflection'] = TRUE;
        }
        if (in_array('watermark', $display_settings['mode'])) {
            $display_settings['display_watermark'] = TRUE;
        }
        if (isset($display_settings['w'])) {
            $display_settings['width'] = $display_settings['w'];
        } elseif (isset($display_settings['h'])) {
            unset($display_settings['width']);
        }
        if (isset($display_settings['h'])) {
            $display_settings['height'] = $display_settings['h'];
        } elseif (isset($display_settings['w'])) {
            unset($display_settings['height']);
        }
        // legacy assumed no width/height meant full size unlike generate_thumbnail: force a full resolution
        if (!isset($display_settings['width']) && !isset($display_settings['height'])) {
            $display_settings['width'] = $image->meta_data['width'];
        }
        if (isset($display_settings['width'])) {
            $params['width'] = $display_settings['width'];
        }
        if (isset($display_settings['height'])) {
            $params['height'] = $display_settings['height'];
        }
        $params['quality'] = $display_settings['quality'];
        $params['crop'] = $display_settings['crop'];
        $params['watermark'] = $display_settings['display_watermark'];
        $params['reflection'] = $display_settings['display_reflection'];
        // Fall back to full in case dynamic images aren't available
        $size = 'full';
        if ($dynthumbs != null) {
            $size = $dynthumbs->get_size_name($params);
        }
        $thumbnail_url = $storage->get_image_url($image, $size);
        if (!empty($display_settings['template']) && $display_settings['template'] != 'default') {
            $this->object->add_mixin('A_NextGen_Basic_Template_Form');
            $this->object->add_mixin('Mixin_NextGen_Basic_Templates');
            $params = $this->object->prepare_legacy_parameters(array($image), $displayed_gallery, array('single_image' => TRUE));
            // the wrapper is a lazy-loader that calculates variables when requested. We here override those to always
            // return the same precalculated settings provided
            $params['image']->container[0]->_cache_overrides['caption'] = $displayed_gallery->inner_content;
            $params['image']->container[0]->_cache_overrides['classname'] = 'ngg-singlepic ' . $display_settings['float'];
            $params['image']->container[0]->_cache_overrides['imageURL'] = $display_settings['link'];
            $params['image']->container[0]->_cache_overrides['thumbnailURL'] = $thumbnail_url;
            $params['target'] = $target;
            // if a link is present we temporarily must filter out the effect code
            if (empty($effect_code)) {
                add_filter('ngg_get_thumbcode', array(&$this, 'strip_thumbcode'), 10);
            }
            $retval = $this->object->legacy_render($display_settings['template'], $params, $return, 'singlepic');
            if (empty($effect_code)) {
                remove_filter('ngg_get_thumbcode', array(&$this, 'strip_thumbcode'), 10);
            }
            return $retval;
        } else {
            $params = $display_settings;
            $params['storage'] =& $storage;
            $params['image'] =& $image;
            $params['effect_code'] = $effect_code;
            $params['inner_content'] = $displayed_gallery->inner_content;
            $params['settings'] = $display_settings;
            $params['thumbnail_url'] = $thumbnail_url;
            $params['target'] = $target;
            $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
            return $this->object->render_partial('photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic', $params, $return);
        }
    }
    /**
     * Intentionally disable the application of the effect code
     * @param string $thumbcode Unused
     * @return string
     */
    function strip_thumbcode($thumbcode)
    {
        return '';
    }
    /**
     * Enqueues all static resources required by this display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('nextgen_basic_singlepic_style', $this->get_static_url('photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic.css'), array(), NGG_SCRIPT_VERSION);
    }
}
/**
 * Class A_NextGen_Basic_SinglePic_Form
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_basic_singlepic" context
 */
class A_NextGen_Basic_SinglePic_Form extends Mixin_Display_Type_Form
{
    /**
     * Returns the name of the display type
     * @return string
     */
    function get_display_type_name()
    {
        return NGG_BASIC_SINGLEPIC;
    }
    /**
     * Returns the name of the fields to render for the SinglePic
     */
    function _get_field_names()
    {
        return array('nextgen_basic_singlepic_dimensions', 'nextgen_basic_singlepic_link', 'nextgen_basic_singlepic_link_target', 'nextgen_basic_singlepic_float', 'nextgen_basic_singlepic_quality', 'nextgen_basic_singlepic_crop', 'nextgen_basic_singlepic_display_watermark', 'nextgen_basic_singlepic_display_reflection', 'nextgen_basic_templates_template');
    }
    function _render_nextgen_basic_singlepic_dimensions_field($display_type)
    {
        return $this->object->render_partial('photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic_settings_dimensions', array('display_type_name' => $display_type->name, 'dimensions_label' => __('Thumbnail dimensions', 'nggallery'), 'width_label' => __('Width'), 'width' => $display_type->settings['width'], 'height_label' => __('Height'), 'height' => $display_type->settings['height']), True);
    }
    function _render_nextgen_basic_singlepic_link_field($display_type)
    {
        return $this->object->render_partial('photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic_settings_link', array('display_type_name' => $display_type->name, 'link_label' => __('Link'), 'link' => $display_type->settings['link']), True);
    }
    function _render_nextgen_basic_singlepic_link_target_field($display_type)
    {
        return $this->_render_select_field($display_type, 'link_target', __('Link target', 'nggallery'), array('_self' => __('Self', 'nggallery'), '_blank' => __('Blank', 'nggallery'), '_parent' => __('Parent', 'nggallery'), '_top' => __('Top', 'nggallery')), $display_type->settings['link_target']);
    }
    function _render_nextgen_basic_singlepic_quality_field($display_type)
    {
        return $this->object->render_partial('photocrati-nextgen_basic_singlepic#nextgen_basic_singlepic_settings_quality', array('display_type_name' => $display_type->name, 'quality_label' => __('Image quality', 'nggallery'), 'quality' => $display_type->settings['quality']), True);
    }
    function _render_nextgen_basic_singlepic_display_watermark_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'display_watermark', __('Display watermark', 'nggallery'), $display_type->settings['display_watermark']);
    }
    function _render_nextgen_basic_singlepic_display_reflection_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'display_reflection', __('Display reflection', 'nggallery'), $display_type->settings['display_reflection']);
    }
    function _render_nextgen_basic_singlepic_crop_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'crop', __('Crop thumbnail', 'nggallery'), $display_type->settings['crop']);
    }
    function _render_nextgen_basic_singlepic_float_field($display_type)
    {
        return $this->_render_select_field($display_type, 'float', __('Float', 'nggallery'), array('' => __('None', 'nggallery'), 'left' => __('Left', 'nggallery'), 'right' => __('Right', 'nggallery')), $display_type->settings['float']);
    }
}
/**
 * Class A_NextGen_Basic_SinglePic_Mapper
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Basic_SinglePic_Mapper extends Mixin
{
    /**
     * Sets default values for SinglePic settings
     * @param stdClass|C_DataMapper_Model $entity
     */
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name) && $entity->name == NGG_BASIC_SINGLEPIC) {
            $this->object->_set_default_value($entity, 'settings', 'width', '');
            $this->object->_set_default_value($entity, 'settings', 'height', '');
            $this->object->_set_default_value($entity, 'settings', 'mode', '');
            $this->object->_set_default_value($entity, 'settings', 'display_watermark', 0);
            $this->object->_set_default_value($entity, 'settings', 'display_reflection', 0);
            $this->object->_set_default_value($entity, 'settings', 'float', '');
            $this->object->_set_default_value($entity, 'settings', 'link', '');
            $this->object->_set_default_value($entity, 'settings', 'link_target', '_blank');
            $this->object->_set_default_value($entity, 'settings', 'quality', 100);
            $this->object->_set_default_value($entity, 'settings', 'crop', 0);
            $this->object->_set_default_value($entity, 'settings', 'template', '');
            // Part of the pro-modules
            $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
        }
    }
}