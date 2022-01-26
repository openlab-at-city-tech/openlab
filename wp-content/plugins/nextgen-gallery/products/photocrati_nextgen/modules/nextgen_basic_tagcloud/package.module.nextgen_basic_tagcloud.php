<?php
/**
 * Class A_NextGen_Basic_Tagcloud
 * @mixin C_Display_Type
 * @adapts I_Display_Type
 */
class A_NextGen_Basic_Tagcloud extends Mixin
{
    function validation()
    {
        if ($this->object->name == NGG_BASIC_TAGCLOUD) {
            $this->object->validates_presence_of('gallery_display_type');
        }
        // If we have a "gallery_display_type", we don't need a "display_type" setting
        if (isset($this->object->settings['display_type']) && isset($this->object->settings['gallery_display_type'])) {
            unset($this->object->settings['display_type']);
        }
        return $this->call_parent('validation');
    }
}
/**
 * Class A_NextGen_Basic_Tagcloud_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller for "photocrati-nextgen_basic_tagcloud" context
 */
class A_NextGen_Basic_Tagcloud_Controller extends Mixin
{
    protected static $alternate_displayed_galleries = array();
    function get_alternate_displayed_gallery($displayed_gallery)
    {
        // Prevent recursive checks for further alternates causing additional modifications to the settings array
        $id = $displayed_gallery->id();
        if (!empty(self::$alternate_displayed_galleries[$id])) {
            return self::$alternate_displayed_galleries[$id];
        }
        $tag = urldecode($this->param('gallerytag'));
        // The display setting 'display_type' has been removed to 'gallery_display_type'
        if (isset($display_settings['display_type'])) {
            $display_settings['gallery_display_type'] = $display_settings['display_type'];
            unset($display_settings['display_type']);
        }
        // we're looking at a tag, so show images w/that tag as a thumbnail gallery
        if (!is_home() && !empty($tag)) {
            $params = ['source' => 'tags', 'container_ids' => array(esc_attr($tag)), 'display_type' => $displayed_gallery->display_settings['gallery_display_type'], 'original_display_type' => $displayed_gallery->display_type, 'original_settings' => $displayed_gallery->display_settings];
            $renderer = C_Displayed_Gallery_Renderer::get_instance();
            $alternate_displayed_gallery = $renderer->params_to_displayed_gallery($params);
            if (is_null($alternate_displayed_gallery->id())) {
                $alternate_displayed_gallery->id(md5(json_encode($alternate_displayed_gallery->get_entity())));
            }
            self::$alternate_displayed_galleries[$id] = $alternate_displayed_gallery;
            return $alternate_displayed_gallery;
        }
        return $displayed_gallery;
    }
    /**
     * Displays the 'tagcloud' display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return (optional)
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        // we're looking at a tag, so show images w/that tag as a thumbnail gallery
        if (!is_home() && !empty($this->param('gallerytag'))) {
            $displayed_gallery = $this->get_alternate_displayed_gallery($displayed_gallery);
            return C_Displayed_Gallery_Renderer::get_instance()->display_images($displayed_gallery);
        }
        $application = C_Router::get_instance()->get_routed_app();
        $display_settings = $displayed_gallery->display_settings;
        $defaults = array('exclude' => '', 'format' => 'list', 'include' => $displayed_gallery->get_term_ids_for_tags(), 'largest' => 22, 'link' => 'view', 'number' => $display_settings['number'], 'order' => 'ASC', 'orderby' => 'name', 'smallest' => 8, 'taxonomy' => 'ngg_tag', 'unit' => 'pt');
        $args = wp_parse_args('', $defaults);
        // Always query top tags
        $tags = get_terms($args['taxonomy'], array_merge($args, array('orderby' => 'count', 'order' => 'DESC')));
        foreach ($tags as $key => $tag) {
            $tags[$key]->link = $this->object->set_param_for($application->get_routed_url(TRUE), 'gallerytag', $tag->slug);
            $tags[$key]->id = $tag->term_id;
        }
        $params = $display_settings;
        $params['inner_content'] = $displayed_gallery->inner_content;
        $params['tagcloud'] = wp_generate_tag_cloud($tags, $args);
        $params['displayed_gallery_id'] = $displayed_gallery->id();
        $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
        return $this->object->render_partial('photocrati-nextgen_basic_tagcloud#nextgen_basic_tagcloud', $params, $return);
    }
    /**
     * Enqueues all static resources required by this display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('photocrati-nextgen_basic_tagcloud-style', $this->get_static_url('photocrati-nextgen_basic_tagcloud#nextgen_basic_tagcloud.css'), array(), NGG_SCRIPT_VERSION);
        $this->enqueue_ngg_styles();
    }
}
/**
 * Class A_NextGen_Basic_Tagcloud_Form
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_basic_tagcloud" context
 */
class A_NextGen_Basic_Tagcloud_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_TAGCLOUD;
    }
    function _get_field_names()
    {
        return array('nextgen_basic_tagcloud_number', 'nextgen_basic_tagcloud_display_type');
    }
    function enqueue_static_resources()
    {
        $this->object->enqueue_style('nextgen_basic_tagcloud_settings-css', $this->get_static_url('photocrati-nextgen_basic_tagcloud#settings.css'));
    }
    function _render_nextgen_basic_tagcloud_number_field($display_type)
    {
        return $this->_render_number_field($display_type, 'number', __('Maximum number of tags', 'nggallery'), $display_type->settings['number']);
    }
    function _render_nextgen_basic_tagcloud_display_type_field($display_type)
    {
        $types = array();
        $skip_types = array(NGG_BASIC_TAGCLOUD, NGG_BASIC_SINGLEPIC, NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM);
        if (empty($display_type->settings['gallery_display_type']) && !empty($display_type->settings['gallery_type'])) {
            $display_type->settings['gallery_display_type'] = $display_type->settings['display_type'];
        }
        $skip_types = apply_filters('ngg_basic_tagcloud_excluded_display_types', $skip_types);
        $mapper = C_Display_Type_Mapper::get_instance();
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
 * Class A_NextGen_Basic_TagCloud_Mapper
 *
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Basic_TagCloud_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name) && $entity->name == NGG_BASIC_TAGCLOUD) {
            if (isset($entity->display_settings) && is_array($entity->display_settings) && isset($entity->display_settings['display_type'])) {
                if (!isset($entity->display_settings['gallery_display_type'])) {
                    $entity->display_settings['gallery_display_type'] = $entity->display_settings['display_type'];
                }
                unset($entity->display_settings['display_type']);
            }
            $this->object->_set_default_value($entity, 'settings', 'gallery_display_type', NGG_BASIC_THUMBNAILS);
            $this->object->_set_default_value($entity, 'settings', 'number', 45);
            $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
        }
    }
}
/**
 * Class A_NextGen_Basic_TagCloud_Urls
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_NextGen_Basic_TagCloud_Urls extends Mixin
{
    function create_parameter_segment($key, $value, $id, $use_prefix)
    {
        if ($key == 'gallerytag') {
            return 'tags/' . $value;
        } else {
            return $this->call_parent('create_parameter_segment', $key, $value, $id, $use_prefix);
        }
    }
    function set_parameter_value($key, $value, $id = NULL, $use_prefix = FALSE, $url = FALSE)
    {
        $retval = $this->call_parent('set_parameter_value', $key, $value, $id, $use_prefix, $url);
        return $this->_set_tag_cloud_parameters($retval, $key, $id);
    }
    function remove_parameter($key, $id = NULL, $url = FALSE)
    {
        $retval = $this->call_parent('remove_parameter', $key, $id, $url);
        $retval = $this->_set_tag_cloud_parameters($retval, $key, $id);
        return $retval;
    }
    function _set_tag_cloud_parameters($retval, $key, $id = NULL)
    {
        // Get the settings manager
        $settings = C_NextGen_Settings::get_instance();
        // Create the regex pattern
        $sep = preg_quote($settings->get('router_param_separator', '--'), '#');
        if ($id) {
            $id = preg_quote($id, '#') . $sep;
        }
        $prefix = preg_quote($settings->get('router_param_prefix', ''), '#');
        $regex = implode('', array('#//?', $id ? "({$id})?" : "(\\w+{$sep})?", "({$prefix})?gallerytag{$sep}([\\w\\-_]+)/?#"));
        // Replace any page parameters with the ngglegacy equivalent
        if (preg_match($regex, $retval, $matches)) {
            $retval = rtrim(str_replace($matches[0], "/tags/{$matches[3]}/", $retval), "/");
        }
        return $retval;
    }
}
/**
 * Class C_Taxonomy_Controller
 * @implements I_Taxonomy_Controller
 */
class C_Taxonomy_Controller extends C_MVC_Controller
{
    static $_instances = array();
    protected $ngg_tag_detection_has_run = FALSE;
    /**
     * Returns an instance of this class
     *
     * @param string|bool $context
     * @return C_Taxonomy_Controller
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Taxonomy_Controller');
    }
    function render_tag($tag)
    {
        $mapper = C_Display_Type_Mapper::get_instance();
        // Respect the global display type setting
        $display_type = $mapper->find_by_name(NGG_BASIC_TAGCLOUD, TRUE);
        $display_type = !empty($display_type->settings['gallery_display_type']) ? $display_type->settings['gallery_display_type'] : NGG_BASIC_THUMBNAILS;
        return "[ngg source='tags' container_ids='{$tag}' slug='{$tag}' display_type='{$display_type}']";
    }
    /**
     * Determines if the current page is /ngg_tag/{*}
     *
     * @param $posts Wordpress post objects
     * @param WP_Query $wp_query_local
     * @return array Wordpress post objects
     */
    function detect_ngg_tag($posts, $wp_query_local)
    {
        global $wp;
        global $wp_query;
        $wp_query_orig = false;
        if ($wp_query_local != null && $wp_query_local != $wp_query) {
            $wp_query_orig = $wp_query;
            $wp_query = $wp_query_local;
        }
        // This appears to be necessary for multisite installations, but I can't imagine why. More hackery..
        $tag = urldecode(get_query_var('ngg_tag') ? get_query_var('ngg_tag') : get_query_var('name'));
        $tag = stripslashes(M_NextGen_Data::strip_html($tag));
        // Tags may not include HTML
        if (!$this->ngg_tag_detection_has_run && !is_admin() && !empty($tag) && (stripos($wp->request, 'ngg_tag') === 0 || isset($wp_query->query_vars['page_id']) && $wp_query->query_vars['page_id'] === 'ngg_tag')) {
            $this->ngg_tag_detection_has_run = TRUE;
            // Wordpress somewhat-correctly generates several notices, so silence them as they're really unnecessary
            if (!defined('WP_DEBUG') || !WP_DEBUG) {
                error_reporting(0);
            }
            // Without this all url generated from this page lacks the /ngg_tag/(slug) section of the URL
            add_filter('ngg_wprouting_add_post_permalink', '__return_false');
            // create in-code a fake post; we feed it back to Wordpress as the sole result of the "the_posts" filter
            $posts = NULL;
            $posts[] = $this->create_ngg_tag_post($tag);
            $wp_query->is_404 = FALSE;
            $wp_query->is_page = TRUE;
            $wp_query->is_singular = TRUE;
            $wp_query->is_home = FALSE;
            $wp_query->is_archive = FALSE;
            $wp_query->is_category = FALSE;
            unset($wp_query->query['error']);
            $wp_query->query_vars['error'] = '';
        }
        if ($wp_query_orig !== false) {
            $wp_query = $wp_query_orig;
            // Commenting this out as it was causing WSOD in 2.2.8
            //        	$wp_query->is_page = FALSE; // Prevents comments from displaying on our taxonomy 'page'
        }
        return $posts;
    }
    function create_ngg_tag_post($tag)
    {
        $title = sprintf(__('Images tagged &quot;%s&quot;', 'nggallery'), $tag);
        $title = apply_filters('ngg_basic_tagcloud_title', $title, $tag);
        $post = new stdClass();
        $post->post_author = FALSE;
        $post->post_name = 'ngg_tag';
        $post->guid = get_bloginfo('wpurl') . '/' . 'ngg_tag';
        $post->post_title = $title;
        $post->post_content = $this->render_tag($tag);
        $post->ID = FALSE;
        $post->post_type = 'page';
        $post->post_status = 'publish';
        $post->comment_status = 'closed';
        $post->ping_status = 'closed';
        $post->comment_count = 0;
        $post->post_date = current_time('mysql');
        $post->post_date_gmt = current_time('mysql', 1);
        return $post;
    }
}