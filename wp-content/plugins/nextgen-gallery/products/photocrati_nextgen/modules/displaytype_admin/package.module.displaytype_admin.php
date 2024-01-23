<?php
/**
 * Class Mixin_NextGen_Basic_Album_Form
 *
 * @mixin C_Form
 */
class Mixin_NextGen_Basic_Album_Form extends Mixin_Display_Type_Form
{
    public function _get_field_names()
    {
        return ['nextgen_basic_album_gallery_display_type', 'nextgen_basic_album_galleries_per_page', 'nextgen_basic_album_enable_breadcrumbs', 'display_view', 'nextgen_basic_templates_template', 'nextgen_basic_album_enable_descriptions'];
    }
    /**
     * Renders the Gallery Display Type field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_album_gallery_display_type_field($display_type)
    {
        $mapper = \Imagely\NGG\DataMappers\DisplayType::get_instance();
        // Disallow hidden or inactive display types.
        $types = $mapper->find_by_entity_type('image');
        foreach ($types as $ndx => $type) {
            if (!empty($type->hidden_from_ui) && $type->hidden_from_ui) {
                unset($types[$ndx]);
            }
        }
        return $this->render_partial('imagely-displaytype_admin#nextgen_basic_album_gallery_display_type', ['display_type_name' => $display_type->name, 'gallery_display_type_label' => __('Display galleries as', 'nggallery'), 'gallery_display_type_help' => __('How would you like galleries to be displayed?', 'nggallery'), 'gallery_display_type' => $display_type->settings['gallery_display_type'], 'galleries_per_page_label' => __('Galleries per page', 'nggallery'), 'galleries_per_page' => $display_type->settings['galleries_per_page'], 'display_types' => $types], true);
    }
    /**
     * Renders the Galleries Per Page field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_album_galleries_per_page_field($display_type)
    {
        return $this->_render_number_field($display_type, 'galleries_per_page', __('Items per page', 'nggallery'), $display_type->settings['galleries_per_page'], __('Maximum number of galleries or sub-albums to appear on a single page', 'nggallery'), false, '', 0);
    }
    public function _render_nextgen_basic_album_enable_breadcrumbs_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'enable_breadcrumbs', __('Enable breadcrumbs', 'nggallery'), isset($display_type->settings['enable_breadcrumbs']) ? $display_type->settings['enable_breadcrumbs'] : false);
    }
    public function _render_nextgen_basic_album_enable_descriptions_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'enable_descriptions', __('Display descriptions', 'nggallery'), $display_type->settings['enable_descriptions']);
    }
}
/**
 * Class A_Display_Settings_Controller
 *
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page using "ngg_display_settings" context
 */
class A_Display_Settings_Controller extends Mixin
{
    /**
     * Static resources required for the Display Settings page
     */
    public function enqueue_backend_resources()
    {
        $this->call_parent('enqueue_backend_resources');
        wp_enqueue_style('nextgen_gallery_display_settings');
        wp_enqueue_script('nextgen_gallery_display_settings');
    }
    public function get_page_title()
    {
        return esc_html__('Gallery Settings', 'nggallery');
    }
    public function get_required_permission()
    {
        return 'NextGEN Change options';
    }
}
/**
 * Class A_Display_Settings_Page
 *
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 */
class A_Display_Settings_Page extends Mixin
{
    public function setup()
    {
        $this->object->add(NGG_DISPLAY_SETTINGS_SLUG, ['adapter' => 'A_Display_Settings_Controller', 'parent' => NGGFOLDER, 'before' => 'ngg_other_options']);
        return $this->call_parent('setup');
    }
}
/**
 * Class A_NextGen_Basic_Extended_Album_Form
 *
 * @mixin C_Form
 * @adapts I_Form for the "photocrati-nextgen_basic_extended_album" context
 */
class A_NextGen_Basic_Extended_Album_Form extends Mixin_NextGen_Basic_Album_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_EXTENDED_ALBUM;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    public function _get_field_names()
    {
        $fields = parent::_get_field_names();
        $fields[] = 'thumbnail_override_settings';
        return $fields;
    }
    /**
     * Enqueues static resources required by this form
     */
    public function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_extended_albums_settings_script', $this->object->get_static_url('imagely-displaytype_admin#extended_settings.js'), ['jquery.nextgen_radio_toggle']);
    }
}
/**
 * @mixin C_Form
 */
class Mixin_Display_Type_Form extends Mixin
{
    public $_model = null;
    public function initialize()
    {
        $this->object->implement('I_Display_Type_Form');
    }
    /**
     * A wrapper to wp_enqueue_script() and ATP's mark_script()
     *
     * Unlike wp_enqueue_script() the version parameter is last as NGG should always use NGG_SCRIPT_VERSION
     *
     * @param string $handle
     * @param string $source
     * @param array  $dependencies
     * @param bool   $in_footer
     * @param string $version
     */
    public function enqueue_script($handle, $source = '', $dependencies = array(), $in_footer = false, $version = NGG_SCRIPT_VERSION)
    {
        wp_enqueue_script($handle, $source, $dependencies, $version, $in_footer);
    }
    /**
     * A wrapper to wp_enqueue_style()
     *
     * Unlike wp_enqueue_style() the version parameter is last as NGG should always use NGG_SCRIPT_VERSION
     *
     * @param string $handle
     * @param string $source
     * @param array  $dependencies
     * @param string $media
     * @param string $version
     */
    public function enqueue_style($handle, $source = '', $dependencies = array(), $media = 'all', $version = NGG_SCRIPT_VERSION)
    {
        wp_enqueue_style($handle, $source, $dependencies, $version, $media);
    }
    /**
     * Returns the name of the display type. Sub-class should override
     *
     * @throws Exception
     * @return string
     */
    public function get_display_type_name()
    {
        throw new Exception(__METHOD__ . ' not implemented');
    }
    /**
     * Returns the model (display type) used in the form
     *
     * @return stdClass
     */
    public function get_model()
    {
        if ($this->_model == null) {
            $mapper = \Imagely\NGG\DataMappers\DisplayType::get_instance();
            $this->_model = $mapper->find_by_name($this->object->get_display_type_name());
        }
        return $this->_model;
    }
    /**
     * Returns the title of the form, which is the title of the display type
     *
     * @return string
     */
    public function get_title()
    {
        return __($this->object->get_model()->title, 'nggallery');
    }
    /**
     * Saves the settings for the display type
     *
     * @param array $attributes
     * @return boolean
     */
    public function save_action($attributes = array())
    {
        $model = $this->object->get_model();
        if ($model) {
            return $model->save(['settings' => $attributes]);
        }
        return false;
    }
    /**
     * Renders the AJAX pagination settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_ajax_pagination_field($display_type)
    {
        return $this->object->_render_radio_field($display_type, 'ajax_pagination', __('Enable AJAX pagination', 'nggallery'), isset($display_type->settings['ajax_pagination']) ? $display_type->settings['ajax_pagination'] : false, __('Browse images without reloading the page.', 'nggallery'));
    }
    public function _render_thumbnail_override_settings_field($display_type)
    {
        $enabled = isset($display_type->settings['override_thumbnail_settings']) ? $display_type->settings['override_thumbnail_settings'] : false;
        $hidden = !$enabled;
        $width = $enabled && isset($display_type->settings['thumbnail_width']) ? intval($display_type->settings['thumbnail_width']) : 0;
        $height = $enabled && isset($display_type->settings['thumbnail_height']) ? intval($display_type->settings['thumbnail_height']) : 0;
        $crop = $enabled && isset($display_type->settings['thumbnail_crop']) ? $display_type->settings['thumbnail_crop'] : false;
        $override_field = $this->_render_radio_field($display_type, 'override_thumbnail_settings', __('Override thumbnail settings', 'nggallery'), $enabled, __("This does not affect existing thumbnails; overriding the thumbnail settings will create an additional set of thumbnails. To change the size of existing thumbnails please visit 'Manage Galleries' and choose 'Create new thumbnails' for all images in the gallery.", 'nggallery'));
        $dimensions_field = $this->object->render_partial('imagely-displaytype_admin#thumbnail_settings', ['display_type_name' => $display_type->name, 'name' => 'thumbnail_dimensions', 'label' => __('Thumbnail dimensions', 'nggallery'), 'thumbnail_width' => $width, 'thumbnail_height' => $height, 'hidden' => $hidden ? 'hidden' : '', 'text' => ''], true);
        $crop_field = $this->_render_radio_field($display_type, 'thumbnail_crop', __('Thumbnail crop', 'nggallery'), $crop, '', $hidden);
        $everything = $override_field . $dimensions_field . $crop_field;
        return $everything;
    }
    /**
     * Renders the thumbnail override settings field(s)
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_image_override_settings_field($display_type)
    {
        $hidden = !(isset($display_type->settings['override_image_settings']) ? $display_type->settings['override_image_settings'] : false);
        $override_field = $this->_render_radio_field($display_type, 'override_image_settings', __('Override image settings', 'nggallery'), isset($display_type->settings['override_image_settings']) ? $display_type->settings['override_image_settings'] : 0, __('Overriding the image settings will create an additional set of images', 'nggallery'));
        $qualities = [];
        for ($i = 100; $i > 40; $i -= 5) {
            $qualities[$i] = "{$i}%";
        }
        $quality_field = $this->_render_select_field($display_type, 'image_quality', __('Image quality', 'nggallery'), $qualities, $display_type->settings['image_quality'], '', $hidden);
        $crop_field = $this->_render_radio_field($display_type, 'image_crop', __('Image crop', 'nggallery'), $display_type->settings['image_crop'], '', $hidden);
        $watermark_field = $this->_render_radio_field($display_type, 'image_watermark', __('Image watermark', 'nggallery'), $display_type->settings['image_watermark'], '', $hidden);
        $everything = $override_field . $quality_field . $crop_field . $watermark_field;
        return $everything;
    }
    public function _render_display_view_field($display_type)
    {
        $display_type_views = $this->get_available_display_type_views($display_type);
        $current_value = isset($display_type->settings['display_type_view']) ? $display_type->settings['display_type_view'] : '';
        if (isset($display_type->settings['display_view'])) {
            $current_value = $display_type->settings['display_view'];
        }
        return $this->object->_render_select_field($display_type, 'display_view', __('Select View', 'nggallery'), $display_type_views, $current_value, '', false);
    }
    /**
     * Renders a field for selecting a template
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_display_type_view_field($display_type)
    {
        $display_type_views = $this->get_available_display_type_views($display_type);
        return $this->object->_render_select_field($display_type, 'display_type_view', __('Select View', 'nggallery'), $display_type_views, $display_type->settings['display_type_view'], '', false);
    }
    /**
     * Gets available templates
     *
     * @param C_Display_Type $display_type
     * @return array
     */
    public function get_available_display_type_views($display_type)
    {
        /* Set up templates array */
        if (strpos($display_type->name, 'basic') !== false) {
            $views = ['default' => __('Legacy', 'nggallery')];
        } else {
            $views = ['default' => __('Default', 'nggallery')];
        }
        /* Fetch array of directories to scan */
        $dirs = \Imagely\NGG\Display\DisplayManager::get_display_type_view_dirs($display_type);
        /* Populate the views array by scanning each directory for relevant templates */
        foreach ($dirs as $dir_name => $dir) {
            /* Confirm directory exists */
            if (!file_exists($dir) || !is_dir($dir)) {
                continue;
            }
            /* Scan for template files and create array */
            $files = scandir($dir);
            $template_files = preg_grep('/^.+\\-(template|view).php$/i', $files);
            $template_files = $template_files ? array_combine($template_files, $template_files) : [];
            /* For custom templates only, append directory name placeholder */
            foreach ($template_files as $key => $value) {
                if ($dir_name !== 'default') {
                    $template_files[$dir_name . DIRECTORY_SEPARATOR . $key] = $dir_name . DIRECTORY_SEPARATOR . $value;
                    unset($template_files[$key]);
                }
            }
            $views = array_merge($views, $template_files);
        }
        return $views;
    }
}
/**
 * @mixin C_Form
 */
class A_NextGen_Basic_SinglePic_Form extends Mixin_Display_Type_Form
{
    /**
     * Returns the name of the display type
     *
     * @return string
     */
    public function get_display_type_name()
    {
        return NGG_BASIC_SINGLEPIC;
    }
    /**
     * Returns the name of the fields to render for the SinglePic
     */
    public function _get_field_names()
    {
        return ['nextgen_basic_singlepic_dimensions', 'nextgen_basic_singlepic_link', 'nextgen_basic_singlepic_link_target', 'nextgen_basic_singlepic_float', 'nextgen_basic_singlepic_quality', 'nextgen_basic_singlepic_crop', 'nextgen_basic_singlepic_display_watermark', 'nextgen_basic_singlepic_display_reflection', 'nextgen_basic_templates_template'];
    }
    public function _render_nextgen_basic_singlepic_dimensions_field($display_type)
    {
        return $this->object->render_partial('imagely-displaytype_admin#nextgen_basic_singlepic_settings_dimensions', ['display_type_name' => $display_type->name, 'dimensions_label' => __('Thumbnail dimensions', 'nggallery'), 'width_label' => __('Width'), 'width' => $display_type->settings['width'], 'height_label' => __('Height'), 'height' => $display_type->settings['height']], true);
    }
    public function _render_nextgen_basic_singlepic_link_field($display_type)
    {
        return $this->object->render_partial('imagely-displaytype_admin#nextgen_basic_singlepic_settings_link', ['display_type_name' => $display_type->name, 'link_label' => __('Link'), 'link' => $display_type->settings['link']], true);
    }
    public function _render_nextgen_basic_singlepic_link_target_field($display_type)
    {
        return $this->_render_select_field($display_type, 'link_target', __('Link target', 'nggallery'), ['_self' => __('Self', 'nggallery'), '_blank' => __('Blank', 'nggallery'), '_parent' => __('Parent', 'nggallery'), '_top' => __('Top', 'nggallery')], $display_type->settings['link_target']);
    }
    public function _render_nextgen_basic_singlepic_quality_field($display_type)
    {
        return $this->object->render_partial('imagely-displaytype_admin#nextgen_basic_singlepic_settings_quality', ['display_type_name' => $display_type->name, 'quality_label' => __('Image quality', 'nggallery'), 'quality' => $display_type->settings['quality']], true);
    }
    public function _render_nextgen_basic_singlepic_display_watermark_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'display_watermark', __('Display watermark', 'nggallery'), $display_type->settings['display_watermark']);
    }
    public function _render_nextgen_basic_singlepic_display_reflection_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'display_reflection', __('Display reflection', 'nggallery'), $display_type->settings['display_reflection']);
    }
    public function _render_nextgen_basic_singlepic_crop_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'crop', __('Crop thumbnail', 'nggallery'), $display_type->settings['crop']);
    }
    public function _render_nextgen_basic_singlepic_float_field($display_type)
    {
        return $this->_render_select_field($display_type, 'float', __('Float', 'nggallery'), ['' => __('None', 'nggallery'), 'left' => __('Left', 'nggallery'), 'right' => __('Right', 'nggallery')], $display_type->settings['float']);
    }
}
/**
 * Provides the display settings form for the NextGen Basic Slideshow
 *
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_basic_slideshow" context
 */
class A_NextGen_Basic_Slideshow_Form extends Mixin_Display_Type_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_SLIDESHOW;
    }
    public function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_slideshow_settings-js', $this->get_static_url('imagely-displaytype_admin#nextgen_basic_slideshow_settings.js'), ['jquery.nextgen_radio_toggle']);
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    public function _get_field_names()
    {
        return ['nextgen_basic_slideshow_gallery_dimensions', 'nextgen_basic_slideshow_autoplay', 'nextgen_basic_slideshow_pauseonhover', 'nextgen_basic_slideshow_arrows', 'nextgen_basic_slideshow_transition_style', 'nextgen_basic_slideshow_interval', 'nextgen_basic_slideshow_transition_speed', 'nextgen_basic_slideshow_show_thumbnail_link', 'nextgen_basic_slideshow_thumbnail_link_text', 'display_view'];
    }
    /**
     * Renders the autoplay field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_autoplay_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'autoplay', __('Autoplay?', 'nggallery'), $display_type->settings['autoplay']);
    }
    /**
     * Renders the Pause on Hover field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_pauseonhover_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'pauseonhover', __('Pause on Hover?', 'nggallery'), $display_type->settings['pauseonhover']);
    }
    /**
     * Renders the arrows field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_arrows_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'arrows', __('Show Arrows?', 'nggallery'), $display_type->settings['arrows']);
    }
    /**
     * Renders the effect field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_transition_style_field($display_type)
    {
        return $this->_render_select_field($display_type, 'transition_style', __('Transition Style', 'nggallery'), ['slide' => 'Slide', 'fade' => 'Fade'], $display_type->settings['transition_style'], '', false);
    }
    /**
     * Renders the interval field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_interval_field($display_type)
    {
        return $this->_render_number_field($display_type, 'interval', __('Interval', 'nggallery'), $display_type->settings['interval'], '', false, __('Milliseconds', 'nggallery'), 1);
    }
    /**
     * Renders the interval field for new Slick.js slideshow
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_transition_speed_field($display_type)
    {
        return $this->_render_number_field($display_type, 'transition_speed', __('Transition Speed', 'nggallery'), $display_type->settings['transition_speed'], '', false, __('Milliseconds', 'nggallery'), 1);
    }
    public function _render_nextgen_basic_slideshow_gallery_dimensions_field($display_type)
    {
        return $this->render_partial('imagely-displaytype_admin#nextgen_basic_slideshow_settings_gallery_dimensions', ['display_type_name' => $display_type->name, 'gallery_dimensions_label' => __('Maximum dimensions', 'nggallery'), 'gallery_dimensions_tooltip' => __('Certain themes may allow images to flow over their container if this setting is too large', 'nggallery'), 'gallery_width' => $display_type->settings['gallery_width'], 'gallery_height' => $display_type->settings['gallery_height']], true);
    }
    /**
     * Renders the show_thumbnail_link settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_show_thumbnail_link_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_thumbnail_link', __('Show thumbnail link', 'nggallery'), $display_type->settings['show_thumbnail_link']);
    }
    /**
     * Renders the thumbnail_link_text settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_slideshow_thumbnail_link_text_field($display_type)
    {
        return $this->_render_text_field($display_type, 'thumbnail_link_text', __('Thumbnail link text', 'nggallery'), $display_type->settings['thumbnail_link_text'], '', !empty($display_type->settings['show_thumbnail_link']) ? false : true);
    }
}
/**
 * @mixin C_Form
 */
class A_NextGen_Basic_Tagcloud_Form extends Mixin_Display_Type_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_TAGCLOUD;
    }
    public function _get_field_names()
    {
        return ['nextgen_basic_tagcloud_number', 'nextgen_basic_tagcloud_display_type'];
    }
    public function enqueue_static_resources()
    {
        $this->object->enqueue_style('nextgen_basic_tagcloud_settings-css', $this->get_static_url('imagely-displaytype_admin#tagcloud_settings.css'));
    }
    public function _render_nextgen_basic_tagcloud_number_field($display_type)
    {
        return $this->_render_number_field($display_type, 'number', __('Maximum number of tags', 'nggallery'), $display_type->settings['number']);
    }
    public function _render_nextgen_basic_tagcloud_display_type_field($display_type)
    {
        $types = [];
        $skip_types = [NGG_BASIC_TAGCLOUD, NGG_BASIC_SINGLEPIC, NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM];
        if (empty($display_type->settings['gallery_display_type']) && !empty($display_type->settings['gallery_type'])) {
            $display_type->settings['gallery_display_type'] = $display_type->settings['display_type'];
        }
        $skip_types = apply_filters('ngg_basic_tagcloud_excluded_display_types', $skip_types);
        $mapper = \Imagely\NGG\DataMappers\DisplayType::get_instance();
        $display_types = $mapper->find_all();
        foreach ($display_types as $dt) {
            if (in_array($dt->name, $skip_types)) {
                continue;
            }
            if (!empty($dt->hidden_from_ui)) {
                continue;
            }
            $types[$dt->name] = $dt->title;
        }
        return $this->_render_select_field($display_type, 'gallery_display_type', __('Display type', 'nggallery'), $types, $display_type->settings['gallery_display_type'], __('The display type that the tagcloud will point its results to', 'nggallery'));
    }
}
/**
 * Class A_NextGen_Basic_Template_Form
 *
 * @mixin C_Form
 * @adapts I_Form
 */
class A_NextGen_Basic_Template_Form extends Mixin
{
    /**
     * Renders 'template' settings field
     *
     * @param $display_type
     * @return mixed
     */
    public function _render_nextgen_basic_templates_template_field($display_type)
    {
        switch ($display_type->name) {
            case 'photocrati-nextgen_basic_singlepic':
                $prefix = 'singlepic';
                break;
            case 'photocrati-nextgen_basic_thumbnails':
                $prefix = 'gallery';
                break;
            case 'photocrati-nextgen_basic_slideshow':
                $prefix = 'gallery';
                break;
            case 'photocrati-nextgen_basic_imagebrowser':
                $prefix = 'imagebrowser';
                break;
            case NGG_BASIC_COMPACT_ALBUM:
                $prefix = 'album';
                break;
            case NGG_BASIC_EXTENDED_ALBUM:
                $prefix = 'album';
                break;
            default:
                $prefix = false;
                break;
        }
        // ensure the current file is in the list.
        $templates = $this->_get_available_templates($prefix);
        if (!isset($templates[$display_type->settings['template']])) {
            $templates[$display_type->settings['template']] = $display_type->settings['template'];
        }
        // add <default> template that acts the same way as having no template specified.
        $templates['default'] = __('Default', 'nggallery');
        return $this->object->render_partial('imagely-displaytype_admin#nextgen_basic_templates_settings_template', ['display_type_name' => $display_type->name, 'template_label' => __('Legacy (Old) Templates', 'nggallery'), 'template_text' => __('Use a legacy template when rendering (not recommended).', 'nggallery'), 'chosen_file' => $display_type->settings['template'], 'templates' => $templates], true);
    }
    /**
     * Retrieves listing of available templates
     *
     * Override this function to modify or add to the available templates listing, array format
     * is array(file_abspath => label)
     *
     * @return array
     */
    public function _get_available_templates($prefix = false)
    {
        $templates = [];
        foreach (\Imagely\NGG\DisplayType\LegacyTemplateLocator::get_instance()->find_all($prefix) as $label => $files) {
            foreach ($files as $file) {
                $tmp = explode(DIRECTORY_SEPARATOR, $file);
                $templates[$file] = "{$label}: " . end($tmp);
            }
        }
        asort($templates);
        return $templates;
    }
    public function enqueue_static_resources()
    {
        wp_enqueue_style('ngg_template_settings', $this->get_static_url('imagely-displaytype_admin#ngg_template_settings.css'));
        wp_enqueue_script('ngg_template_settings', $this->get_static_url('imagely-displaytype_admin#ngg_template_settings.js'), ['ngg_select2'], true);
        wp_localize_script('ngg_template_settings', 'ngg_template_settings', ['placeholder_text' => __('No template selected')]);
    }
}
/**
 * Class A_NextGen_Basic_Thumbnail_Form
 *
 * @mixin C_Form
 * @adapts I_Form for photocrati-nextgen_basic_thumbnails context
 */
class A_NextGen_Basic_Thumbnail_Form extends Mixin_Display_Type_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_THUMBNAILS;
    }
    /**
     * Enqueues static resources required by this form
     */
    public function enqueue_static_resources()
    {
        $this->object->enqueue_style('nextgen_basic_thumbnails_settings', $this->object->get_static_url('imagely-displaytype_admin#nextgen_basic_thumbnails_settings.css'));
        $this->object->enqueue_script('nextgen_basic_thumbnails_settings', $this->object->get_static_url('imagely-displaytype_admin#nextgen_basic_thumbnails_settings.js'), ['jquery.nextgen_radio_toggle']);
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    public function _get_field_names()
    {
        return ['thumbnail_override_settings', 'nextgen_basic_thumbnails_images_per_page', 'nextgen_basic_thumbnails_number_of_columns', 'ajax_pagination', 'nextgen_basic_thumbnails_hidden', 'nextgen_basic_thumbnails_imagebrowser_effect', 'nextgen_basic_thumbnails_show_slideshow_link', 'nextgen_basic_thumbnails_slideshow_link_text', 'display_view', 'nextgen_basic_templates_template'];
    }
    /**
     * Renders the images_per_page settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_thumbnails_images_per_page_field($display_type)
    {
        return $this->_render_number_field($display_type, 'images_per_page', __('Images per page', 'nggallery'), $display_type->settings['images_per_page'], __('0 will display all images at once', 'nggallery'), false, '# of images', 0);
    }
    /**
     * Renders the number_of_columns settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_thumbnails_number_of_columns_field($display_type)
    {
        return $this->_render_number_field($display_type, 'number_of_columns', __('Number of columns to display', 'nggallery'), $display_type->settings['number_of_columns'], '', false, __('# of columns', 'nggallery'), 0);
    }
    /**
     * Renders the 'Add hidden images' settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_thumbnails_hidden_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_all_in_lightbox', __('Add Hidden Images', 'nggallery'), $display_type->settings['show_all_in_lightbox'], __('If pagination is used this option will show all images in the modal window (Thickbox, Lightbox etc.) This increases page load.', 'nggallery'));
    }
    public function _render_nextgen_basic_thumbnails_imagebrowser_effect_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'use_imagebrowser_effect', __('Use imagebrowser effect', 'nggallery'), $display_type->settings['use_imagebrowser_effect'], __('When active each image in the gallery will link to an imagebrowser display and lightbox effects will not be applied.', 'nggallery'));
    }
    /**
     * Renders the show_slideshow_link settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_thumbnails_show_slideshow_link_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'show_slideshow_link', __('Show slideshow link', 'nggallery'), $display_type->settings['show_slideshow_link']);
    }
    /**
     * Renders the slideshow_link_text settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_nextgen_basic_thumbnails_slideshow_link_text_field($display_type)
    {
        return $this->_render_text_field($display_type, 'slideshow_link_text', __('Slideshow link text', 'nggallery'), $display_type->settings['slideshow_link_text'], '', !empty($display_type->settings['show_slideshow_link']) ? false : true);
    }
}
/**
 * Class A_NextGen_Basic_Compact_Album_Form
 *
 * @mixin C_Form
 * @adapts I_Form for the "photocrati-nextgen_basic_compact_album" context
 */
class A_NextGen_Basic_Compact_Album_Form extends Mixin_NextGen_Basic_Album_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_COMPACT_ALBUM;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    public function _get_field_names()
    {
        $fields = parent::_get_field_names();
        $fields[] = 'thumbnail_override_settings';
        return $fields;
    }
    /**
     * Enqueues static resources required by this form
     */
    public function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_compact_albums_settings_script', $this->object->get_static_url('imagely-displaytype_admin#compact_settings.js'), ['jquery.nextgen_radio_toggle']);
    }
}
/**
 * @mixin C_Form
 */
class A_NextGen_Basic_ImageBrowser_Form extends Mixin_Display_Type_Form
{
    public function get_display_type_name()
    {
        return NGG_BASIC_IMAGEBROWSER;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    public function _get_field_names()
    {
        return ['ajax_pagination', 'display_view', 'nextgen_basic_templates_template'];
    }
}