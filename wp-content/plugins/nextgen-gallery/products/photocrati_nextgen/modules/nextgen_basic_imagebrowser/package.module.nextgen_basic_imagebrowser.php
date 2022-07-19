<?php
/**
 * @mixin C_Display_Type
 * @adapts I_Display_Type
 */
class A_NextGen_Basic_ImageBrowser extends Mixin
{
    function validation()
    {
        return $this->call_parent('validation');
    }
}
/**
 * @property C_MVC_Controller|C_Display_Type_Controller|A_NextGen_Basic_ImageBrowser_Controller $object
 * @adapts I_Display_Type_Controller for "photocrati-nextgen_basic_imagebrowser" context
 */
class A_NextGen_Basic_ImageBrowser_Controller extends Mixin
{
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        // Force the trigger icon display off, regardless of past settings
        $displayed_gallery->display_settings['ngg_triggers_display'] = 'never';
        $picture_list = array();
        foreach ($displayed_gallery->get_included_entities() as $image) {
            $picture_list[$image->{$image->id_field}] = $image;
        }
        if ($picture_list) {
            $retval = $this->object->render_image_browser($displayed_gallery, $picture_list);
            if ($return) {
                return $retval;
            } else {
                print $retval;
            }
        } else {
            return $this->object->render_partial('photocrati-nextgen_gallery_display#no_images_found', array(), $return);
        }
        return '';
    }
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @param array $picture_list
     * @return string Rendered HTML
     */
    function render_image_browser($displayed_gallery, $picture_list)
    {
        $display_settings = $displayed_gallery->display_settings;
        $storage = C_Gallery_Storage::get_instance();
        $imap = C_Image_Mapper::get_instance();
        $application = C_Router::get_instance()->get_routed_app();
        // the pid may be a slug so we must track it & the slug target's database id
        $pid = $this->object->param('pid');
        $numeric_pid = NULL;
        // makes the upcoming which-image-am-I loop easier
        $picture_array = array();
        foreach ($picture_list as $picture) {
            $picture_array[] = $picture->{$imap->get_primary_key_column()};
        }
        // Determine which image in the list we need to display
        if (!empty($pid)) {
            if (is_numeric($pid) && !empty($picture_list[$pid])) {
                $numeric_pid = intval($pid);
            } else {
                // in the case it's a slug we need to search for the pid
                foreach ($picture_list as $key => $picture) {
                    if ($picture->image_slug == $pid || strtoupper($picture->image_slug) === strtoupper(urlencode($pid))) {
                        $numeric_pid = $key;
                        break;
                    }
                }
            }
        } else {
            reset($picture_array);
            $numeric_pid = current($picture_array);
        }
        // get ids to the next and previous images
        $total = count($picture_array);
        $key = array_search($numeric_pid, $picture_array);
        if (!$key) {
            $numeric_pid = reset($picture_array);
            $key = key($picture_array);
        }
        // for "viewing image #13 of $total"
        $picture_list_pos = $key + 1;
        // our image to display
        $picture = new C_Image_Wrapper($imap->find($numeric_pid), $displayed_gallery, TRUE);
        $picture = apply_filters('ngg_image_object', $picture, $numeric_pid);
        // determine URI to the next & previous images
        $back_pid = $key >= 1 ? $picture_array[$key - 1] : end($picture_array);
        // 'show' is set when using the imagebrowser as an alternate view to a thumbnail or slideshow
        // for which the basic-gallery module will rewrite the show parameter into existence as long as 'image'
        // is set. We remove 'show' here so navigation appears fluid.
        $current_url = $application->get_routed_url(TRUE);
        if ($this->object->param('ajax_pagination_referrer')) {
            $current_url = $this->object->param('ajax_pagination_referrer');
        }
        $prev_image_link = $this->object->set_param_for($current_url, 'pid', $picture_list[$back_pid]->image_slug);
        $prev_image_link = $this->object->remove_param_for($prev_image_link, 'show', $displayed_gallery->id());
        $next_pid = $key < $total - 1 ? $picture_array[$key + 1] : reset($picture_array);
        $next_image_link = $this->object->set_param_for($current_url, 'pid', $picture_list[$next_pid]->image_slug);
        $next_image_link = $this->object->remove_param_for($next_image_link, 'show', $displayed_gallery->id());
        // css class
        $anchor = 'ngg-imagebrowser-' . $displayed_gallery->id() . '-' . (get_the_ID() == false ? 0 : get_the_ID());
        // try to read EXIF data, but fallback to the db presets
        $meta = new C_NextGen_Metadata($picture);
        $meta->sanitize();
        $meta_results = array('exif' => $meta->get_EXIF(), 'iptc' => $meta->get_IPTC(), 'xmp' => $meta->get_XMP(), 'db' => $meta->get_saved_meta());
        $meta_results['exif'] = $meta_results['exif'] == false ? $meta_results['db'] : $meta_results['exif'];
        // disable triggers IF we're rendering inside of an ajax-pagination request; var set in common.js
        if (!empty($_POST['ajax_referrer'])) {
            $displayed_gallery->display_settings['ngg_triggers_display'] = 'never';
        }
        if (!empty($display_settings['template']) && $display_settings['template'] != 'default') {
            $this->object->add_mixin('Mixin_NextGen_Basic_Templates');
            $picture->href_link = $picture->get_href_link();
            $picture->previous_image_link = $prev_image_link;
            $picture->previous_pid = $back_pid;
            $picture->next_image_link = $next_image_link;
            $picture->next_pid = $next_pid;
            $picture->number = $picture_list_pos;
            $picture->total = $total;
            $picture->anchor = $anchor;
            return $this->object->legacy_render($display_settings['template'], array('image' => $picture, 'meta' => $meta, 'exif' => $meta_results['exif'], 'iptc' => $meta_results['iptc'], 'xmp' => $meta_results['xmp'], 'db' => $meta_results['db'], 'displayed_gallery' => $displayed_gallery), TRUE, 'imagebrowser');
        } else {
            $params = $display_settings;
            $params['anchor'] = $anchor;
            $params['image'] = $picture;
            $params['storage'] =& $storage;
            $params['previous_pid'] = $back_pid;
            $params['next_pid'] = $next_pid;
            $params['number'] = $picture_list_pos;
            $params['total'] = $total;
            $params['previous_image_link'] = $prev_image_link;
            $params['next_image_link'] = $next_image_link;
            $params['effect_code'] = $this->object->get_effect_code($displayed_gallery);
            $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
            return $this->object->render_partial('photocrati-nextgen_basic_imagebrowser#nextgen_basic_imagebrowser', $params, TRUE);
        }
    }
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('nextgen_basic_imagebrowser_style', $this->get_static_url('photocrati-nextgen_basic_imagebrowser#style.css'), array(), NGG_SCRIPT_VERSION);
        wp_enqueue_script('nextgen_basic_imagebrowser_script', $this->object->get_static_url(NGG_BASIC_IMAGEBROWSER . '#imagebrowser.js'), array('ngg_common'), NGG_SCRIPT_VERSION, TRUE);
    }
}
/**
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_basic_imagebrowser" context
 */
class A_NextGen_Basic_ImageBrowser_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_IMAGEBROWSER;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        return array('ajax_pagination', 'display_view', 'nextgen_basic_templates_template');
    }
}
/**
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Basic_ImageBrowser_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name) && $entity->name == NGG_BASIC_IMAGEBROWSER) {
            $default_template = isset($entity->settings["template"]) ? 'default' : 'default-view.php';
            $this->object->_set_default_value($entity, 'settings', 'display_view', $default_template);
            $this->object->_set_default_value($entity, 'settings', 'template', '');
            $this->object->_set_default_value($entity, 'settings', 'ajax_pagination', '1');
            // Part of the pro-modules
            $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
        }
    }
}
/**
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_NextGen_Basic_ImageBrowser_Urls extends Mixin
{
    function create_parameter_segment($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        if ($key == 'pid') {
            return "image/{$value}";
        } else {
            return $this->call_parent('create_parameter_segment', $key, $value, $id, $use_prefix);
        }
    }
}