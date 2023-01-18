<?php
/**
 * Provides AJAX actions for the Attach To Post interface
 * TODO: Need to add authorization checks to each action
 *
 * @mixin C_Ajax_Controller
 * @adapts I_Ajax_Controller
 */
class A_Attach_To_Post_Ajax extends Mixin
{
    var $attach_to_post = NULL;
    /**
     * Retrieves the attach to post controller
     */
    function get_attach_to_post()
    {
        if (is_null($this->attach_to_post)) {
            $this->attach_to_post = C_Attach_Controller::get_instance();
        }
        return $this->attach_to_post;
    }
    /**
     * Returns a list of image sources for the Attach to Post interface
     * @return array
     */
    function get_attach_to_post_sources_action()
    {
        $response = array();
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery')) {
            $response['sources'] = $this->get_attach_to_post()->get_sources();
        }
        return $response;
    }
    /**
     * Gets existing galleries
     * @return array
     */
    function get_existing_galleries_action()
    {
        $this->debug = TRUE;
        $response = array();
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery')) {
            $limit = $this->object->param('limit');
            $offset = $this->object->param('offset');
            // We return the total # of galleries, so that the client can make
            // pagination requests
            $mapper = C_Gallery_Mapper::get_instance();
            $response['total'] = $mapper->count();
            $response['limit'] = $limit = $limit ? $limit : 0;
            $response['offset'] = $offset = $offset ? $offset : 0;
            // Get the galleries
            $mapper->select();
            if ($limit) {
                $mapper->limit($limit, $offset);
            }
            $response['items'] = $mapper->run_query();
        } else {
            $response['error'] = 'insufficient access';
        }
        $this->debug = FALSE;
        return $response;
    }
    /**
     * Gets existing albums
     * @return array
     */
    function get_existing_albums_action()
    {
        $response = array();
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery')) {
            $limit = $this->object->param('limit');
            $offset = $this->object->param('offset');
            // We return the total # of albums, so that the client can make pagination requests
            $mapper = C_Album_Mapper::get_instance();
            $response['total'] = $mapper->count();
            $response['limit'] = $limit = $limit ? $limit : 0;
            $response['offset'] = $offset = $offset ? $offset : 0;
            // Get the albums
            $mapper->select();
            if ($limit) {
                $mapper->limit($limit, $offset);
            }
            $response['items'] = $mapper->run_query();
        }
        return $response;
    }
    /**
     * Gets existing image tags
     * @return array
     */
    function get_existing_image_tags_action()
    {
        $response = array();
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery')) {
            $limit = $this->object->param('limit');
            $offset = $this->object->param('offset');
            $response['limit'] = $limit = $limit ? $limit : 0;
            $response['offset'] = $offset = $offset ? $offset : 0;
            $response['items'] = array();
            $params = array('number' => $limit, 'offset' => $offset, 'fields' => 'names');
            foreach (get_terms('ngg_tag', $params) as $term) {
                $response['items'][] = array('id' => $term, 'title' => $term, 'name' => $term);
            }
            $response['total'] = count(get_terms('ngg_tag', array('fields' => 'ids')));
        }
        return $response;
    }
    /**
     * Gets entities (such as images) for a displayed gallery (attached gallery)
     */
    function get_displayed_gallery_entities_action()
    {
        $response = array();
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery') && ($params = $this->object->param('displayed_gallery'))) {
            global $wpdb;
            $limit = $this->object->param('limit');
            $offset = $this->object->param('offset');
            $factory = C_Component_Factory::get_instance();
            $displayed_gallery = $factory->create('displayed_gallery');
            foreach ($params as $key => $value) {
                $key = $wpdb->_escape($key);
                if (!in_array($key, array('container_ids', 'entity_ids', 'sortorder'))) {
                    $value = esc_sql($value);
                }
                $displayed_gallery->{$key} = $value;
            }
            $response['limit'] = $limit = $limit ? esc_sql($limit) : 0;
            $response['offset'] = $offset = $offset ? esc_sql($offset) : 0;
            $response['total'] = $displayed_gallery->get_entity_count('both');
            $response['items'] = $displayed_gallery->get_entities($limit, $offset, FALSE, 'both');
            $controller = C_Display_Type_Controller::get_instance();
            $storage = C_Gallery_Storage::get_instance();
            $image_mapper = C_Image_Mapper::get_instance();
            $settings = C_NextGen_Settings::get_instance();
            foreach ($response['items'] as &$entity) {
                $image = $entity;
                if (in_array($displayed_gallery->source, array('album', 'albums'))) {
                    // Set the alttext of the preview image to the
                    // name of the gallery or album
                    if ($image = $image_mapper->find($entity->previewpic)) {
                        if ($entity->is_album) {
                            $image->alttext = sprintf(__('Album: %s', 'nggallery'), $entity->name);
                        } else {
                            $image->alttext = sprintf(__('Gallery: %s', 'nggallery'), $entity->title);
                        }
                    }
                    // Prefix the id of an album with 'a'
                    if ($entity->is_album) {
                        $id = $entity->{$entity->id_field};
                        $entity->{$entity->id_field} = 'a' . $id;
                    }
                }
                // Get the thumbnail
                $entity->thumb_url = $storage->get_image_url($image, 'thumb', TRUE);
                $entity->thumb_html = $storage->get_image_html($image, 'thumb');
            }
        } else {
            $response['error'] = __('Missing parameters', 'nggallery');
        }
        return $response;
    }
    /**
     * Saves the displayed gallery
     */
    function save_displayed_gallery_action()
    {
        $response = array();
        $mapper = C_Displayed_Gallery_Mapper::get_instance();
        // Do we have fields to work with?
        if ($this->object->validate_ajax_request('nextgen_edit_displayed_gallery', true) && ($params = json_decode($this->object->param('displayed_gallery')))) {
            // Existing displayed gallery ?
            if ($id = $this->object->param('id')) {
                $displayed_gallery = $mapper->find($id, TRUE);
                if ($displayed_gallery) {
                    foreach ($params as $key => $value) {
                        $displayed_gallery->{$key} = $value;
                    }
                }
            } else {
                $factory = C_Component_Factory::get_instance();
                $displayed_gallery = $factory->create('displayed_gallery', $params, $mapper);
            }
            // Save the changes
            if ($displayed_gallery) {
                if ($displayed_gallery->save()) {
                    $response['displayed_gallery'] = $displayed_gallery->get_entity();
                } else {
                    $response['validation_errors'] = $this->get_attach_to_post()->show_errors_for($displayed_gallery, TRUE);
                }
            } else {
                $response['error'] = __('Displayed gallery does not exist', 'nggallery');
            }
        } else {
            $response['error'] = __('Invalid request', 'nggallery');
        }
        return $response;
    }
}
/**
 * Class A_Gallery_Storage_Frame_Event
 * @mixin C_Gallery_Storage
 * @adapts I_Gallery_Storage
 */
class A_Gallery_Storage_Frame_Event extends Mixin
{
    function generate_thumbnail($image, $params = null, $skip_defaults = false)
    {
        $retval = $this->call_parent('generate_thumbnail', $image, $params, $skip_defaults);
        if (is_admin() && ($image = C_Image_Mapper::get_instance()->find($image))) {
            $controller = C_Display_Type_Controller::get_instance();
            $storage = C_Gallery_Storage::get_instance();
            $app = C_Router::get_instance()->get_routed_app();
            $image->thumb_url = $controller->set_param_for($app->get_routed_url(TRUE), 'timestamp', time(), NULL, $storage->get_thumb_url($image));
            $event = new stdClass();
            $event->pid = $image->{$image->id_field};
            $event->id_field = $image->id_field;
            $event->thumb_url = $image->thumb_url;
            C_Frame_Event_Publisher::get_instance('attach_to_post')->add_event(array('event' => 'thumbnail_modified', 'image' => $event));
        }
        return $retval;
    }
}
/**
 * Class C_Attach_Controller
 * @mixin Mixin_Attach_To_Post
 * @mixin Mixin_Attach_To_Post_Display_Tab
 * @implements I_Attach_To_Post_Controller
 */
class C_Attach_Controller extends C_NextGen_Admin_Page_Controller
{
    static $_instances = array();
    var $_displayed_gallery;
    var $_marked_scripts;
    var $_is_rendering;
    static function &get_instance($context = 'all')
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE)
    {
        if (!is_array($context)) {
            $context = array($context);
        }
        array_unshift($context, 'ngg_attach_to_post');
        parent::define($context);
        $this->add_mixin('Mixin_Attach_To_Post');
        $this->add_mixin('Mixin_Attach_To_Post_Display_Tab');
        $this->implement('I_Attach_To_Post_Controller');
    }
    function initialize()
    {
        parent::initialize();
        $this->_load_displayed_gallery();
        if (!has_action('wp_print_scripts', array($this, 'filter_scripts'))) {
            add_action('wp_print_scripts', array($this, 'filter_scripts'));
        }
        if (!has_action('wp_print_scripts', array($this, 'filter_styles'))) {
            add_action('wp_print_scripts', array($this, 'filter_styles'));
        }
    }
}
class Mixin_Attach_To_Post extends Mixin
{
    function _load_displayed_gallery()
    {
        $mapper = C_Displayed_Gallery_Mapper::get_instance();
        // Fetch the displayed gallery by ID
        if ($id = $this->object->param('id')) {
            $this->object->_displayed_gallery = $mapper->find($id, TRUE);
        } else {
            if (isset($_REQUEST['shortcode'])) {
                // Fetch the displayed gallery by shortcode
                $shortcode = base64_decode($_REQUEST['shortcode']);
                // $shortcode lacks the opening and closing brackets but still begins with 'ngg ' or 'ngg_images ' which are not parameters
                $params = preg_replace('/^(ngg|ngg_images) /i', '', $shortcode, 1);
                $params = stripslashes($params);
                $params = str_replace(array('[', ']'), array('&#91;', '&#93;'), $params);
                $params = shortcode_parse_atts($params);
                $this->object->_displayed_gallery = C_Displayed_Gallery_Renderer::get_instance()->params_to_displayed_gallery($params);
            }
        }
        // If all else fails, then create fresh with a new displayed gallery
        if (empty($this->object->_displayed_gallery)) {
            $this->object->_displayed_gallery = $mapper->create();
        }
    }
    /**
     * Gets all dependencies for a particular resource that has been registered using wp_register_style/wp_register_script
     * @param $handle
     * @param $type
     *
     * @return array
     */
    function get_resource_dependencies($handle, $type)
    {
        $retval = array();
        $wp_resources = $GLOBALS[$type];
        if (($index = array_search($handle, $wp_resources->registered)) !== FALSE) {
            $registered_script = $wp_resources->registered[$index];
            if ($registered_script->deps) {
                foreach ($registered_script->deps as $dep) {
                    $retval[] = $dep;
                    $retval = array_merge($retval, $this->get_script_dependencies($handle));
                }
            }
        }
        return $retval;
    }
    function get_script_dependencies($handle)
    {
        return $this->get_resource_dependencies($handle, 'wp_scripts');
    }
    function get_style_dependencies($handle)
    {
        return $this->get_resource_dependencies($handle, 'wp_styles');
    }
    function get_ngg_provided_resources($type)
    {
        $wp_resources = $GLOBALS[$type];
        $retval = array();
        foreach ($wp_resources->queue as $handle) {
            $script = $wp_resources->registered[$handle];
            if (strpos($script->src, plugin_dir_url(NGG_PLUGIN_BASENAME)) !== FALSE) {
                $retval[] = $handle;
            }
            if (defined('NGG_PRO_PLUGIN_BASENAME') && strpos($script->src, plugin_dir_url(NGG_PRO_PLUGIN_BASENAME)) !== FALSE) {
                $retval[] = $handle;
            }
            if (defined('NGG_PLUS_PLUGIN_BASENAME') && strpos($script->src, plugin_dir_url(NGG_PLUS_PLUGIN_BASENAME)) !== FALSE) {
                $retval[] = $handle;
            }
        }
        return array_unique($retval);
    }
    function get_ngg_provided_scripts()
    {
        return $this->get_ngg_provided_resources('wp_scripts');
    }
    function get_ngg_provided_styles()
    {
        return $this->get_ngg_provided_resources('wp_styles');
    }
    function get_igw_allowed_scripts()
    {
        $retval = array();
        foreach ($this->get_ngg_provided_scripts() as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_script_dependencies($handle));
        }
        foreach ($this->get_display_type_scripts() as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_script_dependencies($handle));
        }
        foreach ($this->attach_to_post_scripts as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_script_dependencies($handle));
        }
        return array_unique(apply_filters('ngg_igw_approved_scripts', $retval));
    }
    function get_display_type_scripts()
    {
        global $wp_scripts;
        $wp_scripts->old_queue = $wp_scripts->queue;
        $wp_scripts->queue = array();
        $mapper = C_Display_Type_Mapper::get_instance();
        foreach ($mapper->find_all() as $display_type) {
            $form = C_Form::get_instance($display_type->name);
            $form->enqueue_static_resources();
        }
        $retval = $wp_scripts->queue;
        $wp_scripts->queue = $wp_scripts->old_queue;
        unset($wp_scripts->old_queue);
        return $retval;
    }
    function get_display_type_styles()
    {
        global $wp_styles;
        $wp_styles->old_queue = $wp_styles->queue;
        $wp_styles->queue = array();
        $mapper = C_Display_Type_Mapper::get_instance();
        foreach ($mapper->find_all() as $display_type) {
            $form = C_Form::get_instance($display_type->name);
            $form->enqueue_static_resources();
        }
        $retval = $wp_styles->queue;
        $wp_styles->queue = $wp_styles->old_queue;
        unset($wp_styles->old_queue);
        return $retval;
    }
    function get_igw_allowed_styles()
    {
        $retval = array();
        foreach ($this->get_ngg_provided_styles() as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_style_dependencies($handle));
        }
        foreach ($this->get_display_type_styles() as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_style_dependencies($handle));
        }
        foreach ($this->attach_to_post_styles as $handle) {
            $retval[] = $handle;
            $retval = array_merge($retval, $this->get_style_dependencies($handle));
        }
        return array_unique(apply_filters('ngg_igw_approved_styles', $retval));
    }
    function filter_scripts()
    {
        global $wp_scripts;
        $new_queue = array();
        $current_queue = $wp_scripts->queue;
        $approved = $this->get_igw_allowed_scripts();
        foreach ($current_queue as $handle) {
            if (in_array($handle, $approved)) {
                $new_queue[] = $handle;
            }
        }
        $wp_scripts->queue = $new_queue;
    }
    function filter_styles()
    {
        global $wp_styles;
        $new_queue = array();
        $current_queue = $wp_styles->queue;
        $approved = $this->get_igw_allowed_styles();
        foreach ($current_queue as $handle) {
            if (in_array($handle, $approved)) {
                $new_queue[] = $handle;
            }
        }
        $wp_styles->queue = $new_queue;
    }
    function mark_script($handle)
    {
        return FALSE;
    }
    function enqueue_display_tab_js()
    {
        // Enqueue backbone.js library, required by the Attach to Post display tab
        wp_enqueue_script('backbone');
        // provided by WP
        $this->object->mark_script('backbone');
        // Enqueue the backbone app for the display tab
        // Get all entities used by the display tab
        $context = 'attach_to_post';
        $gallery_mapper = $this->get_registry()->get_utility('I_Gallery_Mapper', $context);
        $album_mapper = $this->get_registry()->get_utility('I_Album_Mapper', $context);
        $image_mapper = $this->get_registry()->get_utility('I_Image_Mapper', $context);
        $display_type_mapper = $this->get_registry()->get_utility('I_Display_Type_Mapper', $context);
        $sources = C_Displayed_Gallery_Source_Manager::get_instance();
        $settings = C_NextGen_Settings::get_instance();
        // Get the nextgen tags
        global $wpdb;
        $tags = $wpdb->get_results("SELECT DISTINCT name AS 'id', name FROM {$wpdb->terms}\n                        WHERE term_id IN (\n                                SELECT term_id FROM {$wpdb->term_taxonomy}\n                                WHERE taxonomy = 'ngg_tag'\n                        )");
        $all_tags = new stdClass();
        $all_tags->name = "All";
        $all_tags->id = "All";
        array_unshift($tags, $all_tags);
        $display_types = array();
        $registry = C_Component_Registry::get_instance();
        $display_type_mapper->flush_query_cache();
        foreach ($display_type_mapper->find_all() as $display_type) {
            if (isset($display_type->hidden_from_igw) && $display_type->hidden_from_igw || isset($display_type->hidden_from_ui) && $display_type->hidden_from_ui) {
                continue;
            }
            $available = $registry->is_module_loaded($display_type->name);
            if (!apply_filters('ngg_atp_show_display_type', $available, $display_type)) {
                continue;
            }
            // Some display types were saved with values like "nextgen-gallery-pro/modules/nextgen_pro_imagebrowser/static/preview.jpg"
            // as the preview_image_relpath property
            if (strpos($display_type->preview_image_relpath, '#') === FALSE) {
                $static_path = preg_replace("#^.*static/#", "", $display_type->preview_image_relpath);
                $module_id = isset($display_type->module_id) ? $display_type->module_id : $display_type->name;
                if ($module_id == 'photocrati-nextgen_basic_slideshow') {
                    $display_type->module_id = $module_id = 'photocrati-nextgen_basic_gallery';
                }
                $display_type->preview_image_relpath = "{$module_id}#{$static_path}";
                $display_type_mapper->save($display_type);
                $display_type_mapper->flush_query_cache();
            }
            $display_type->preview_image_url = M_Static_Assets::get_static_url($display_type->preview_image_relpath);
            $display_types[] = $display_type;
        }
        usort($display_types, array($this->object, '_display_type_list_sort'));
        wp_enqueue_script('ngg_display_tab', $this->get_static_url('photocrati-attach_to_post#display_tab.js'), array('jquery', 'backbone', 'photocrati_ajax'), NGG_ATTACH_TO_POST_VERSION);
        $this->object->mark_script('ngg_display_tab');
        wp_localize_script('ngg_display_tab', 'igw_data', array('displayed_gallery_preview_url' => $settings->gallery_preview_url, 'displayed_gallery' => $this->object->_displayed_gallery->get_entity(), 'sources' => $sources->get_all(), 'gallery_primary_key' => $gallery_mapper->get_primary_key_column(), 'galleries' => $gallery_mapper->find_all(), 'albums' => $album_mapper->find_all(), 'tags' => $tags, 'display_types' => $display_types, 'nonce' => M_Security::create_nonce('nextgen_edit_displayed_gallery'), 'image_primary_key' => $image_mapper->get_primary_key_column(), 'display_type_priority_base' => NGG_DISPLAY_PRIORITY_BASE, 'display_type_priority_step' => NGG_DISPLAY_PRIORITY_STEP, 'shortcode_ref' => isset($_REQUEST['ref']) ? floatval($_REQUEST['ref']) : null, 'shortcode_defaults' => array('order_by' => $settings->galSort, 'order_direction' => $settings->galSortDir, 'returns' => 'included', 'maximum_entity_count' => $settings->maximum_entity_count), 'shortcode_attr_replacements' => array('source' => 'src', 'container_ids' => 'ids', 'display_type' => 'display'), 'i18n' => array('sources' => __('Are you inserting a Gallery (default), an Album, or images based on Tags?', 'nggallery'), 'optional' => __('(optional)', 'nggallery'), 'slug_tooltip' => __('Sets an SEO-friendly name to this gallery for URLs. Currently only in use by the Pro Lightbox', 'nggallery'), 'slug_label' => __('Slug', 'nggallery'), 'no_entities' => __('No entities to display for this source', 'nggallery'), 'exclude_question' => __('Exclude?', 'nggallery'), 'select_gallery' => __('Select a Gallery', 'nggallery'), 'galleries' => __('Select one or more galleries (click in box to see available galleries).', 'nggallery'), 'albums' => __('Select one album (click in box to see available albums).', 'nggallery'))));
    }
    function start_resource_monitoring()
    {
        global $wp_scripts, $wp_styles;
        $this->attach_to_post_scripts = array();
        $this->attach_to_post_styles = array();
        $wp_styles->before_monitoring = $wp_styles->queue;
        $wp_scripts->before_monitoring = $wp_styles->queue;
    }
    function stop_resource_monitoring()
    {
        global $wp_scripts, $wp_styles;
        $this->attach_to_post_scripts = array_diff($wp_scripts->queue, $wp_scripts->before_monitoring);
        $this->attach_to_post_styles = array_diff($wp_styles->queue, $wp_styles->before_monitoring);
    }
    function enqueue_backend_resources()
    {
        $this->start_resource_monitoring();
        $this->call_parent('enqueue_backend_resources');
        // Enqueue frame event publishing
        wp_enqueue_script('frame_event_publisher');
        // Enqueue JQuery UI libraries
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('ngg_tabs', $this->get_static_url('photocrati-attach_to_post#ngg_tabs.js'), FALSE, NGG_SCRIPT_VERSION);
        wp_enqueue_style('buttons');
        // Ensure select2
        wp_enqueue_style('ngg_select2');
        wp_enqueue_script('ngg_select2');
        // Ensure that the Photocrati AJAX library is loaded
        wp_enqueue_script('photocrati_ajax');
        // Enqueue logic for the Attach to Post interface as a whole
        wp_enqueue_script('ngg_attach_to_post_js', $this->get_static_url('photocrati-attach_to_post#attach_to_post.js'), array(), NGG_SCRIPT_VERSION);
        wp_enqueue_style('ngg_attach_to_post', $this->get_static_url('photocrati-attach_to_post#attach_to_post.css'), array(), NGG_SCRIPT_VERSION);
        wp_dequeue_script('debug-bar-js');
        wp_dequeue_style('debug-bar-css');
        $this->object->enqueue_display_tab_js();
        do_action('ngg_igw_enqueue_scripts');
        do_action('ngg_igw_enqueue_styles');
        $this->stop_resource_monitoring();
    }
    /**
     * Renders the interface
     * @param bool $return
     * @return string
     */
    function index_action($return = FALSE)
    {
        $this->object->enqueue_backend_resources();
        $this->object->do_not_cache();
        // If Elementor is also active a fatal error is generated due to this method not existing
        if (!function_exists('wp_print_media_templates')) {
            require_once ABSPATH . WPINC . '/media-template.php';
        }
        // Enqueue resources
        return $this->object->render_view('photocrati-attach_to_post#attach_to_post', array('page_title' => $this->object->_get_page_title(), 'tabs' => $this->object->_get_main_tabs(), 'logo' => $this->get_static_url('photocrati-nextgen_admin#imagely_icon.png')), $return);
    }
    /**
     * Displays a preview image for the displayed gallery
     */
    function preview_action()
    {
        $found_preview_pic = FALSE;
        $dyn_thumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        $storage = C_Gallery_Storage::get_instance();
        $image_mapper = C_Image_Mapper::get_instance();
        // Get the first entity from the displayed gallery. We will use this
        // for a preview pic
        $results = $this->object->_displayed_gallery->get_included_entities(1);
        $entity = array_pop($results);
        $image = FALSE;
        if ($entity) {
            // This is an album or gallery
            if (isset($entity->previewpic)) {
                $image = (int) $entity->previewpic;
                if ($image = $image_mapper->find($image)) {
                    $found_preview_pic = TRUE;
                }
            } else {
                if (isset($entity->galleryid)) {
                    $image = $entity;
                    $found_preview_pic = TRUE;
                }
            }
        }
        // Were we able to find a preview pic? If so, then render it
        $image_size = $dyn_thumbs->get_size_name(array('width' => 300, 'height' => 200, 'quality' => 90, 'type' => 'jpg', 'watermark' => FALSE, 'crop' => TRUE));
        add_filter('ngg_before_save_thumbnail', array(&$this, 'set_igw_placeholder_text'));
        $found_preview_pic = $storage->render_image($image, $image_size, TRUE);
        remove_filter('ngg_before_save_thumbnail', array(&$this, 'set_igw_placeholder_text'));
        // Render invalid image if no preview pic is found
        if (!$found_preview_pic) {
            $filename = $this->object->get_static_abspath('photocrati-attach_to_post#invalid_image.png');
            $this->set_content_type('image/png');
            readfile($filename);
            $this->render();
        }
    }
    /**
     * Filter for ngg_before_save_thumbnail
     * @param stdClass $thumbnail
     * @return stdClass
     */
    function set_igw_placeholder_text($thumbnail)
    {
        $settings = C_NextGen_Settings::get_instance();
        $thumbnail->applyFilter(IMG_FILTER_BRIGHTNESS, -25);
        $watermark_settings = apply_filters('ngg_igw_placeholder_line_1_settings', array('text' => __("NextGEN Gallery", 'nggallery'), 'font_color' => 'ffffff', 'font' => 'YanoneKaffeesatz-Bold.ttf', 'font_size' => 32));
        if ($watermark_settings) {
            $thumbnail->watermarkText = $watermark_settings['text'];
            $thumbnail->watermarkCreateText($watermark_settings['font_color'], $watermark_settings['font'], $watermark_settings['font_size'], 100);
            $thumbnail->watermarkImage('topCenter', 0, 72);
        }
        $watermark_settings = apply_filters('ngg_igw_placeholder_line_2_settings', array('text' => __("Click to edit", 'nggallery'), 'font_color' => 'ffffff', 'font' => 'YanoneKaffeesatz-Bold.ttf', 'font_size' => 15));
        if ($watermark_settings) {
            $thumbnail->watermarkText = $watermark_settings['text'];
            $thumbnail->watermarkCreateText($watermark_settings['font_color'], $watermark_settings['font'], $watermark_settings['font_size'], 100);
            $thumbnail->watermarkImage('topCenter', 0, 108);
        }
        return $thumbnail;
    }
    /**
     * Returns the page title of the Attach to Post interface
     * @return string
     */
    function _get_page_title()
    {
        return __('NextGEN Gallery - Attach To Post', 'nggallery');
    }
    /**
     * Returns the main tabs displayed on the Attach to Post interface
     * @return array
     */
    function _get_main_tabs()
    {
        $retval = array();
        if (M_Security::is_allowed('NextGEN Manage gallery')) {
            $retval['displayed_tab'] = array('content' => $this->object->_render_display_tab(), 'title' => __('Insert Into Page', 'nggallery'));
        }
        if (M_Security::is_allowed('NextGEN Upload images')) {
            $retval['create_tab'] = array('content' => $this->object->_render_create_tab(), 'title' => __('Upload Images', 'nggallery'));
        }
        if (M_Security::is_allowed('NextGEN Manage others gallery') && M_Security::is_allowed('NextGEN Manage gallery')) {
            $retval['galleries_tab'] = array('content' => $this->object->_render_galleries_tab(), 'title' => __('Manage Galleries', 'nggallery'));
        }
        if (M_Security::is_allowed('NextGEN Edit album')) {
            $retval['albums_tab'] = array('content' => $this->object->_render_albums_tab(), 'title' => __('Manage Albums', 'nggallery'));
        }
        // if ($sec_actor->is_allowed('NextGEN Manage tags')) {
        // 	$retval['tags_tab']         = array(
        // 		'content'   =>  $this->object->_render_tags_tab(),
        // 		'title'     =>  __('Manage Tags', 'nggallery')
        // 	);
        // }
        return apply_filters('ngg_attach_to_post_main_tabs', $retval);
    }
    /**
     * Renders a NextGen Gallery page in an iframe, suited for the attach to post
     * interface
     * @param string $page
     * @param null|int $tab_id (optional)
     * @return string
     */
    function _render_ngg_page_in_frame($page, $tab_id = null)
    {
        $frame_url = admin_url("/admin.php?page={$page}&attach_to_post");
        $frame_url = nextgen_esc_url($frame_url);
        if ($tab_id) {
            $tab_id = " id='ngg-iframe-{$tab_id}'";
        }
        return "<iframe name='{$page}' frameBorder='0'{$tab_id} class='ngg-attach-to-post ngg-iframe-page-{$page}' scrolling='yes' src='{$frame_url}'></iframe>";
    }
    /**
     * Renders the display tab for adjusting how images/galleries will be displayed
     * @return string
     */
    function _render_display_tab()
    {
        return $this->object->render_partial('photocrati-attach_to_post#display_tab', array('messages' => array(), 'displayed_gallery' => $this->object->_displayed_gallery, 'tabs' => $this->object->_get_display_tabs()), TRUE);
    }
    /**
     * Renders the tab used primarily for Gallery and Image creation
     * @return string
     */
    function _render_create_tab()
    {
        return $this->object->_render_ngg_page_in_frame('ngg_addgallery', 'create_tab');
    }
    /**
     * Renders the tab used for Managing Galleries
     * @return string
     */
    function _render_galleries_tab()
    {
        return $this->object->_render_ngg_page_in_frame('nggallery-manage-gallery', 'galleries_tab');
    }
    /**
     * Renders the tab used for Managing Albums
     */
    function _render_albums_tab()
    {
        return $this->object->_render_ngg_page_in_frame('nggallery-manage-album', 'albums_tab');
    }
    /**
     * Renders the tab used for Managing Albums
     * @return string
     */
    function _render_tags_tab()
    {
        return $this->object->_render_ngg_page_in_frame('nggallery-tags', 'tags_tab');
    }
}
/**
 * Provides the "Display Tab" for the Attach To Post interface/controller
 * @see C_Attach_Controller adds this mixin
 */
class Mixin_Attach_To_Post_Display_Tab extends Mixin
{
    function _display_type_list_sort($type_1, $type_2)
    {
        $order_1 = $type_1->view_order;
        $order_2 = $type_2->view_order;
        if ($order_1 == null) {
            $order_1 = NGG_DISPLAY_PRIORITY_BASE;
        }
        if ($order_2 == null) {
            $order_2 = NGG_DISPLAY_PRIORITY_BASE;
        }
        if ($order_1 > $order_2) {
            return 1;
        }
        if ($order_1 < $order_2) {
            return -1;
        }
        return 0;
    }
    /**
     * Gets a list of tabs to render for the "Display" tab
     */
    function _get_display_tabs()
    {
        // The ATP requires more memmory than some applications, somewhere around 60MB.
        // Because it's such an important feature of NextGEN Gallery, we temporarily disable
        // any memory limits
        if (!extension_loaded('suhosin')) {
            @ini_set('memory_limit', -1);
        }
        return array('choose_display_tab' => $this->object->_render_choose_display_tab(), 'display_settings_tab' => $this->object->_render_display_settings_tab(), 'preview_tab' => $this->object->_render_preview_tab());
    }
    /**
     * Renders the accordion tab, "What would you like to display?"
     */
    function _render_choose_display_tab()
    {
        return array('id' => 'choose_display', 'title' => __('Choose Display', 'nggallery'), 'content' => $this->object->_render_display_source_tab_contents() . $this->object->_render_display_type_tab_contents());
    }
    /**
     * Renders the contents of the source tab
     * @return string
     */
    function _render_display_source_tab_contents()
    {
        return $this->object->render_partial('photocrati-attach_to_post#display_tab_source', array('i18n' => array()), TRUE);
    }
    /**
     * Renders the contents of the display type tab
     */
    function _render_display_type_tab_contents()
    {
        return $this->object->render_partial('photocrati-attach_to_post#display_tab_type', array(), TRUE);
    }
    /**
     * Renders the display settings tab for the Attach to Post interface
     * @return array
     */
    function _render_display_settings_tab()
    {
        return array('id' => 'display_settings_tab', 'title' => __('Customize Display Settings', 'nggallery'), 'content' => $this->object->_render_display_settings_contents());
    }
    /**
     * If editing an existing displayed gallery, retrieves the name
     * of the display type
     * @return string
     */
    function _get_selected_display_type_name()
    {
        $retval = '';
        if ($this->object->_displayed_gallery) {
            $retval = $this->object->_displayed_gallery->display_type;
        }
        return $retval;
    }
    /**
     * Is the displayed gallery that's being edited using the specified display
     * type?
     * @param string $name	name of the display type
     * @return bool
     */
    function is_displayed_gallery_using_display_type($name)
    {
        $retval = FALSE;
        if ($this->object->_displayed_gallery) {
            $retval = $this->object->_displayed_gallery->display_type == $name;
        }
        return $retval;
    }
    /**
     * Renders the contents of the display settings tab
     * @return string
     */
    function _render_display_settings_contents()
    {
        $retval = array();
        // Get all display setting forms
        $form_manager = C_Form_Manager::get_instance();
        $forms = $form_manager->get_forms(NGG_DISPLAY_SETTINGS_SLUG, TRUE);
        // Display each form
        foreach ($forms as $form) {
            // Enqueue the form's static resources
            $form->enqueue_static_resources();
            // Determine which classes to use for the form's "class" attribute
            $model = $form->get_model();
            $current = $this->object->is_displayed_gallery_using_display_type($model->name);
            $css_class = $current ? 'display_settings_form' : 'display_settings_form hidden';
            $defaults = $model->settings;
            // If this form is used to provide the display settings for the current
            // displayed gallery, then we need to override the forms settings
            // with the displayed gallery settings
            if ($current) {
                $settings = $this->array_merge_assoc($model->settings, $this->object->_displayed_gallery->display_settings, TRUE);
                $model->settings = $settings;
            }
            // Output the display settings form
            $retval[] = $this->object->render_partial('photocrati-attach_to_post#display_settings_form', array('settings' => $form->render(), 'display_type_name' => $model->name, 'css_class' => $css_class, 'defaults' => $defaults), TRUE);
        }
        // In addition, we'll render a form that will be displayed when no
        // display type has been selected in the Attach to Post interface
        // Render the default "no display type selected" view
        $css_class = $this->object->_get_selected_display_type_name() ? 'display_settings_form hidden' : 'display_settings_form';
        $retval[] = $this->object->render_partial('photocrati-attach_to_post#no_display_type_selected', array('no_display_type_selected' => __('No display type selected', 'nggallery'), 'css_class' => $css_class), TRUE);
        // Return all display setting forms
        return implode("\n", $retval);
    }
    /**
     * Renders the tab used to preview included images
     * @return array
     */
    function _render_preview_tab()
    {
        return array('id' => 'preview_tab', 'title' => __('Sort or Exclude Images', 'nggallery'), 'content' => $this->object->_render_preview_tab_contents());
    }
    /**
     * Renders the contents of the "Preview" tab.
     * @return string
     */
    function _render_preview_tab_contents()
    {
        return $this->object->render_partial('photocrati-attach_to_post#preview_tab', array(), TRUE);
    }
}