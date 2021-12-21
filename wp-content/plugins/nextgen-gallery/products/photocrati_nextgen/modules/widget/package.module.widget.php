<?php
/**
 * Class C_Widget
 *
 * @implements I_Widget
 */
class C_Widget extends C_MVC_Controller
{
    public static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Widget');
    }
    /**
     * @param bool|string $context
     * @return C_Widget
     */
    public static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Widget($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Function for templates without widget support
     *
     * @return C_Widget_Gallery
     */
    function echo_widget_random($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail')
    {
        $options = array('title' => FALSE, 'items' => $number, 'show' => $show, 'type' => 'random', 'width' => $width, 'height' => $height, 'exclude' => $exclude, 'list' => $list, 'webslice' => FALSE);
        $widget = new C_Widget_Gallery();
        $widget->widget($args = array('widget_id' => 'sidebar_1'), $options);
        return $widget;
    }
    /**
     * Function for templates without widget support
     *
     * @return C_Widget_Gallery
     */
    function echo_widget_recent($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail')
    {
        $options = array('title' => FALSE, 'items' => $number, 'show' => $show, 'type' => 'recent', 'width' => $width, 'height' => $height, 'exclude' => $exclude, 'list' => $list, 'webslice' => FALSE);
        $widget = new C_Widget_Gallery();
        $widget->widget($args = array('widget_id' => 'sidebar_1'), $options);
        return $widget;
    }
    /**
     * Function for templates without widget support
     *
     * @param integer $galleryID
     * @param string $width
     * @param string $height
     * @return C_Widget_Slideshow
     */
    function echo_widget_slideshow($galleryID, $width = '', $height = '')
    {
        $widget = new C_Widget_Slideshow();
        $widget->render_slideshow($galleryID, $width, $height);
        return $widget;
    }
}
class C_Widget_Gallery extends WP_Widget
{
    protected static $displayed_gallery_ids = array();
    function __construct()
    {
        $widget_ops = ['classname' => 'ngg_images', 'description' => __('Add recent or random images from the galleries', 'nggallery')];
        parent::__construct('ngg-images', __('NextGEN Widget', 'nggallery'), $widget_ops);
        // Determine what widgets will exist in the future, create their displayed galleries, enqueue their resources,
        // and cache the resulting displayed gallery for later rendering to avoid the ID changing due to misc attributes
        // in $args being different now and at render time ($args is sidebar information that is not relevant)
        add_action('wp_enqueue_scripts', function () {
            global $wp_registered_sidebars;
            $sidebars = wp_get_sidebars_widgets();
            $options = $this->get_settings();
            foreach ($sidebars as $sidebar_name => $sidebar) {
                if ($sidebar_name === 'wp_inactive_widgets' || !$sidebar) {
                    continue;
                }
                foreach ($sidebar as $widget) {
                    if (strpos($widget, 'ngg-images-', 0) !== 0) {
                        continue;
                    }
                    $id = str_replace('ngg-images-', '', $widget);
                    if (isset($options[$id])) {
                        $sidebar_data = $wp_registered_sidebars[$sidebar_name];
                        $sidebar_data['widget_id'] = $widget;
                        // These are normally replaced at display time but we're building our cache before then
                        $sidebar_data['before_widget'] = str_replace('%1$s', $widget, $sidebar_data['before_widget']);
                        $sidebar_data['before_widget'] = str_replace('%2$s', 'ngg_images', $sidebar_data['before_widget']);
                        $sidebar_data['widget_name'] = __('NextGEN Widget', 'nggallery');
                        $displayed_gallery = $this->get_displayed_gallery($sidebar_data, $options[$id]);
                        self::$displayed_gallery_ids[$widget] = $displayed_gallery;
                        $controller = C_Display_Type_Controller::get_instance(NGG_BASIC_THUMBNAILS);
                        M_Gallery_Display::enqueue_frontend_resources_for_displayed_gallery($displayed_gallery, $controller);
                    }
                }
            }
            // Now enqueue the basic styling for the display itself
            $router = C_Router::get_instance();
            wp_enqueue_style('nextgen_widgets_style', $router->get_static_url('photocrati-widget#widgets.css'), [], NGG_SCRIPT_VERSION);
            wp_enqueue_style('nextgen_basic_thumbnails_style', $router->get_static_url('photocrati-nextgen_basic_gallery#thumbnails/nextgen_basic_thumbnails.css'), [], NGG_SCRIPT_VERSION);
        }, 11);
        // It is important that this run at priority 11 or higher so that M_Gallery_Display->enqueue_frontend_resources() has run first
    }
    function get_defaults()
    {
        return ['exclude' => 'all', 'height' => '75', 'items' => '4', 'list' => '', 'show' => 'thumbnail', 'title' => 'Gallery', 'type' => 'recent', 'webslice' => TRUE, 'width' => '100'];
    }
    function form($instance)
    {
        // used for rendering utilities
        $parent = C_Widget::get_instance();
        // defaults
        $instance = wp_parse_args((array) $instance, $this->get_defaults());
        return $parent->render_partial('photocrati-widget#form_gallery', array('self' => $this, 'instance' => $instance, 'title' => esc_attr($instance['title']), 'items' => intval($instance['items']), 'height' => esc_attr($instance['height']), 'width' => esc_attr($instance['width'])));
    }
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        // do not allow 0 or less
        if ((int) $new_instance['items'] <= 0) {
            $new_instance['items'] = 4;
        }
        // for clarity: empty the list if we're showing every gallery anyway
        if ($new_instance['exclude'] == 'all') {
            $new_instance['list'] = '';
        }
        // remove gallery ids that do not exist
        if (in_array($new_instance['exclude'], array('denied', 'allow'))) {
            // do search
            $mapper = C_Gallery_Mapper::get_instance();
            $ids = explode(',', $new_instance['list']);
            foreach ($ids as $ndx => $id) {
                if (!$mapper->find($id)) {
                    unset($ids[$ndx]);
                }
            }
            $new_instance['list'] = implode(',', $ids);
        }
        // reset to show all galleries IF there are no valid galleries in the list
        if ($new_instance['exclude'] !== 'all' && empty($new_instance['list'])) {
            $new_instance['exclude'] = 'all';
        }
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['items'] = (int) $new_instance['items'];
        $instance['type'] = $new_instance['type'];
        $instance['show'] = $new_instance['show'];
        $instance['width'] = (int) $new_instance['width'];
        $instance['height'] = (int) $new_instance['height'];
        $instance['exclude'] = $new_instance['exclude'];
        $instance['list'] = $new_instance['list'];
        $instance['webslice'] = (bool) $new_instance['webslice'];
        return $instance;
    }
    /**
     * @param array $args
     * @param array $instance
     * @return C_Displayed_Gallery
     */
    public function get_displayed_gallery($args, $instance)
    {
        $settings = C_NextGen_Settings::get_instance();
        // these are handled by extract() but I want to silence my IDE warnings that these vars don't exist
        $before_widget = NULL;
        $before_title = NULL;
        $after_widget = NULL;
        $after_title = NULL;
        $widget_id = NULL;
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);
        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        $factory = C_Component_Factory::get_instance();
        $view = $factory->create('mvc_view', '');
        // IE8 webslice support if needed
        if (!empty($instance['webslice'])) {
            $before_widget .= '<div class="hslice" id="ngg-webslice">';
            $before_title = str_replace('class="', 'class="entry-title ', $before_title);
            $after_widget = '</div>' . $after_widget;
        }
        $source = $instance['type'] == 'random' ? 'random_images' : 'recent';
        $template = !empty($instance['template']) ? $instance['template'] : $view->get_template_abspath('photocrati-widget#display_gallery');
        $params = array('slug' => 'widget-' . $args['widget_id'], 'source' => $source, 'display_type' => NGG_BASIC_THUMBNAILS, 'images_per_page' => $instance['items'], 'maximum_entity_count' => $instance['items'], 'template' => $template, 'image_type' => $instance['show'] == 'original' ? 'full' : 'thumb', 'show_all_in_lightbox' => FALSE, 'show_slideshow_link' => FALSE, 'show_thumbnail_link' => FALSE, 'use_imagebrowser_effect' => FALSE, 'disable_pagination' => TRUE, 'image_width' => $instance['width'], 'image_height' => $instance['height'], 'ngg_triggers_display' => 'never', 'widget_setting_title' => $title, 'widget_setting_before_widget' => $before_widget, 'widget_setting_before_title' => $before_title, 'widget_setting_after_widget' => $after_widget, 'widget_setting_after_title' => $after_title, 'widget_setting_width' => $instance['width'], 'widget_setting_height' => $instance['height'], 'widget_setting_show_setting' => $instance['show'], 'widget_setting_widget_id' => $widget_id);
        switch ($instance['exclude']) {
            case 'all':
                break;
            case 'denied':
                $mapper = C_Gallery_Mapper::get_instance();
                $gallery_ids = array();
                $list = explode(',', $instance['list']);
                foreach ($mapper->find_all() as $gallery) {
                    if (!in_array($gallery->{$gallery->id_field}, $list)) {
                        $gallery_ids[] = $gallery->{$gallery->id_field};
                    }
                }
                $params['container_ids'] = implode(',', $gallery_ids);
                break;
            case 'allow':
                $params['container_ids'] = $instance['list'];
                break;
        }
        // "Random" galleries are a bit resource intensive when querying the database and widgets are generally
        // going to be on every page a site may serve. Because the displayed gallery renderer does *NOT* cache the
        // HTML of random galleries the following is a bit of a workaround: for random widgets we create a displayed
        // gallery object and then cache the results of get_entities() so that, for at least as long as
        // NGG_RENDERING_CACHE_TTL seconds, widgets will be temporarily cached
        if (in_array($params['source'], array('random', 'random_images')) && (float) $settings->random_widget_cache_ttl > 0) {
            $displayed_gallery = $renderer->params_to_displayed_gallery($params);
            if (is_null($displayed_gallery->id())) {
                $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));
            }
            $cache_group = 'random_widget_gallery_ids';
            $cache_params = array($displayed_gallery->get_entity());
            $transientM = C_Photocrati_Transient_Manager::get_instance();
            $key = $transientM->generate_key($cache_group, $cache_params);
            $ids = $transientM->get($key, FALSE);
            if (!empty($ids)) {
                $params['image_ids'] = $ids;
            } else {
                $ids = array();
                foreach ($displayed_gallery->get_entities($instance['items'], FALSE, TRUE) as $item) {
                    $ids[] = $item->{$item->id_field};
                }
                $params['image_ids'] = implode(',', $ids);
                $transientM->set($key, $params['image_ids'], (float) $settings->random_widget_cache_ttl * 60);
            }
            $params['source'] = 'images';
            unset($params['container_ids']);
        }
        $final_displayed_gallery = $renderer->params_to_displayed_gallery($params);
        if (is_null($final_displayed_gallery->id())) {
            $final_displayed_gallery->id(md5(json_encode($final_displayed_gallery->get_entity())));
        }
        return $final_displayed_gallery;
    }
    function widget($args, $instance)
    {
        // This displayed gallery is created dynamically at runtime
        if (empty(self::$displayed_gallery_ids[$args['widget_id']])) {
            $displayed_gallery = $this->get_displayed_gallery($args, $instance);
            self::$displayed_gallery_ids[$displayed_gallery->id()] = $displayed_gallery;
        } else {
            // The displayed gallery was created during the action wp_enqueue_resources and was cached to avoid ID conflicts
            $displayed_gallery = self::$displayed_gallery_ids[$args['widget_id']];
        }
        print C_Displayed_Gallery_Renderer::get_instance()->display_images($displayed_gallery);
    }
}
class C_Widget_MediaRSS extends WP_Widget
{
    var $options;
    function __construct()
    {
        $widget_ops = array('classname' => 'ngg_mrssw', 'description' => __('Widget that displays Media RSS links for NextGEN Gallery.', 'nggallery'));
        parent::__construct('ngg-mrssw', __('NextGEN Media RSS', 'nggallery'), $widget_ops);
    }
    function form($instance)
    {
        // used for rendering utilities
        $parent = C_Widget::get_instance();
        // defaults
        $instance = wp_parse_args((array) $instance, array('mrss_text' => __('Media RSS', 'nggallery'), 'mrss_title' => __('Link to the main image feed', 'nggallery'), 'show_global_mrss' => TRUE, 'show_icon' => TRUE, 'title' => 'Media RSS'));
        return $parent->render_partial('photocrati-widget#form_mediarss', array('self' => $this, 'instance' => $instance, 'title' => esc_attr($instance['title']), 'mrss_text' => esc_attr($instance['mrss_text']), 'mrss_title' => esc_attr($instance['mrss_title'])));
    }
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['show_global_mrss'] = $new_instance['show_global_mrss'];
        $instance['show_icon'] = $new_instance['show_icon'];
        $instance['mrss_text'] = $new_instance['mrss_text'];
        $instance['mrss_title'] = $new_instance['mrss_title'];
        return $instance;
    }
    function widget($args, $instance)
    {
        // these are handled by extract() but I want to silence my IDE warnings that these vars don't exist
        $before_widget = NULL;
        $before_title = NULL;
        $after_widget = NULL;
        $after_title = NULL;
        $widget_id = NULL;
        extract($args);
        $settings = C_NextGen_Settings::get_instance();
        $parent = C_Component_Registry::get_instance()->get_utility('I_Widget');
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);
        $parent->render_partial('photocrati-widget#display_mediarss', array('self' => $this, 'instance' => $instance, 'title' => $title, 'settings' => $settings, 'before_widget' => $before_widget, 'before_title' => $before_title, 'after_widget' => $after_widget, 'after_title' => $after_title, 'widget_id' => $widget_id));
    }
    function get_mrss_link($mrss_url, $show_icon = TRUE, $title, $text)
    {
        $out = '';
        if ($show_icon) {
            $icon_url = NGGALLERY_URLPATH . 'images/mrss-icon.gif';
            $out .= "<a href='{$mrss_url}' title='{$title}' class='ngg-media-rss-link'>";
            $out .= "<img src='{$icon_url}' alt='MediaRSS Icon' title='" . $title . "' class='ngg-media-rss-icon' />";
            $out .= "</a> ";
        }
        if ($text != '') {
            $out .= "<a href='{$mrss_url}' title='{$title}' class='ngg-media-rss-link'>";
            $out .= $text;
            $out .= "</a>";
        }
        return $out;
    }
}
class C_Widget_Slideshow extends WP_Widget
{
    protected static $displayed_gallery_ids = array();
    function __construct()
    {
        $widget_ops = ['classname' => 'widget_slideshow', 'description' => __('Show a NextGEN Gallery Slideshow', 'nggallery')];
        parent::__construct('slideshow', __('NextGEN Slideshow', 'nggallery'), $widget_ops);
        // Determine what widgets will exist in the future, create their displayed galleries, enqueue their resources,
        // and cache the resulting displayed gallery for later rendering to avoid the ID changing due to misc attributes
        // in $args being different now and at render time ($args is sidebar information that is not relevant)
        add_action('wp_enqueue_scripts', function () {
            global $wp_registered_sidebars;
            $sidebars = wp_get_sidebars_widgets();
            $options = $this->get_settings();
            foreach ($sidebars as $sidebar_name => $sidebar) {
                if ($sidebar_name === 'wp_inactive_widgets' || !$sidebar) {
                    continue;
                }
                foreach ($sidebar as $widget) {
                    if (strpos($widget, 'slideshow-', 0) !== 0) {
                        continue;
                    }
                    $id = str_replace('slideshow-', '', $widget);
                    if (isset($options[$id])) {
                        $sidebar_data = $wp_registered_sidebars[$sidebar_name];
                        $sidebar_data['widget_id'] = $widget;
                        // These are normally replaced at display time but we're building our cache before then
                        $sidebar_data['before_widget'] = str_replace('%1$s', $widget, $sidebar_data['before_widget']);
                        $sidebar_data['before_widget'] = str_replace('%2$s', 'widget_slideshow', $sidebar_data['before_widget']);
                        $sidebar_data['widget_name'] = __('NextGEN Slideshow', 'nggallery');
                        $displayed_gallery = $this->get_displayed_gallery($sidebar_data, $options[$id]);
                        self::$displayed_gallery_ids[$widget] = $displayed_gallery;
                        $controller = C_Display_Type_Controller::get_instance(NGG_BASIC_SLIDESHOW);
                        M_Gallery_Display::enqueue_frontend_resources_for_displayed_gallery($displayed_gallery, $controller);
                    }
                }
            }
            $router = C_Router::get_instance();
            wp_enqueue_style('nextgen_widgets_style', $router->get_static_url('photocrati-widget#widgets.css'), array(), NGG_SCRIPT_VERSION);
            wp_enqueue_style('nextgen_basic_slideshow_style', $router->get_static_url('photocrati-nextgen_basic_gallery#slideshow/ngg_basic_slideshow.css'), array(), NGG_SCRIPT_VERSION);
        }, 11);
    }
    /**
     * @param array $args
     * @param array $instance
     * @return C_Displayed_Gallery
     */
    public function get_displayed_gallery($args, $instance)
    {
        if (empty($instance['limit'])) {
            $instance['limit'] = 10;
        }
        $params = array(
            'container_ids' => $instance['galleryid'],
            'display_type' => 'photocrati-nextgen_basic_slideshow',
            'gallery_width' => $instance['width'],
            'gallery_height' => $instance['height'],
            'source' => 'galleries',
            'slug' => 'widget-' . $args['widget_id'],
            'entity_types' => array('image'),
            'show_thumbnail_link' => FALSE,
            'show_slideshow_link' => FALSE,
            'use_imagebrowser_effect' => FALSE,
            // just to be safe
            'ngg_triggers_display' => 'never',
        );
        if (0 === $instance['galleryid']) {
            $params['source'] = 'random_images';
            $params['maximum_entity_count'] = $instance['limit'];
            unset($params['container_ids']);
        }
        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        $displayed_gallery = $renderer->params_to_displayed_gallery($params);
        if (is_null($displayed_gallery->id())) {
            $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));
        }
        return $displayed_gallery;
    }
    function form($instance)
    {
        global $wpdb;
        // used for rendering utilities
        $parent = C_Widget::get_instance();
        // defaults
        $instance = wp_parse_args((array) $instance, array('galleryid' => '0', 'height' => '120', 'title' => 'Slideshow', 'width' => '160', 'limit' => '10'));
        return $parent->render_partial('photocrati-widget#form_slideshow', array('self' => $this, 'instance' => $instance, 'title' => esc_attr($instance['title']), 'height' => esc_attr($instance['height']), 'width' => esc_attr($instance['width']), 'limit' => esc_attr($instance['limit']), 'tables' => $wpdb->get_results("SELECT * FROM {$wpdb->nggallery} ORDER BY 'name' ASC")));
    }
    function update($new_instance, $old_instance)
    {
        $nh = $new_instance['height'];
        $nw = $new_instance['width'];
        if (empty($nh) || (int) $nh === 0) {
            $new_instance['height'] = 120;
        }
        if (empty($nw) || (int) $nw === 0) {
            $new_instance['width'] = 160;
        }
        if (empty($new_instance['limit'])) {
            $new_instance['limit'] = 10;
        }
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['galleryid'] = (int) $new_instance['galleryid'];
        $instance['height'] = (int) $new_instance['height'];
        $instance['width'] = (int) $new_instance['width'];
        $instance['limit'] = (int) $new_instance['limit'];
        return $instance;
    }
    function widget($args, $instance)
    {
        // these are handled by extract() but I want to silence my IDE warnings that these vars don't exist
        $before_widget = NULL;
        $before_title = NULL;
        $after_widget = NULL;
        $after_title = NULL;
        $widget_id = NULL;
        extract($args);
        $parent = C_Component_Registry::get_instance()->get_utility('I_Widget');
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Slideshow', 'nggallery') : $instance['title'], $instance, $this->id_base);
        $out = $this->render_slideshow($args, $instance);
        $parent->render_partial('photocrati-widget#display_slideshow', array('self' => $this, 'instance' => $instance, 'title' => $title, 'out' => $out, 'before_widget' => $before_widget, 'before_title' => $before_title, 'after_widget' => $after_widget, 'after_title' => $after_title, 'widget_id' => $widget_id));
    }
    function render_slideshow($args, $instance)
    {
        // This displayed gallery is created dynamically at runtime
        if (empty(self::$displayed_gallery_ids[$args['widget_id']])) {
            $displayed_gallery = $this->get_displayed_gallery($args, $instance);
            self::$displayed_gallery_ids[$displayed_gallery->id()] = $displayed_gallery;
        } else {
            // The displayed gallery was created during the action wp_enqueue_resources and was cached to avoid ID conflicts
            $displayed_gallery = self::$displayed_gallery_ids[$args['widget_id']];
        }
        $renderer = C_Displayed_Gallery_Renderer::get_instance();
        $retval = $renderer->display_images($displayed_gallery);
        $retval = apply_filters('ngg_show_slideshow_widget_content', $retval, $instance['galleryid'], $instance['width'], $instance['height']);
        return $retval;
    }
}