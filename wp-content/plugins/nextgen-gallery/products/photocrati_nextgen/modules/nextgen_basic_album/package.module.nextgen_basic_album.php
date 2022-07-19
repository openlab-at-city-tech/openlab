<?php
/**
 * Class A_NextGen_Album_Breadcrumbs
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_NextGen_Album_Breadcrumbs extends Mixin
{
    public $breadcrumb_cache = array();
    function are_breadcrumbs_enabled($display_settings)
    {
        $retval = FALSE;
        if (isset($display_settings['enable_breadcrumbs']) && $display_settings['enable_breadcrumbs']) {
            $retval = TRUE;
        } elseif (isset($display_settings['original_settings']) && $this->are_breadcrumbs_enabled($display_settings['original_settings'])) {
            $retval = TRUE;
        }
        return $retval;
    }
    function get_original_album_entities($display_settings)
    {
        $retval = array();
        if (isset($display_settings['original_album_entities'])) {
            $retval = $display_settings['original_album_entities'];
        } elseif (isset($display_settings['original_settings']) && $this->get_original_album_entities($display_settings['original_settings'])) {
            $retval = $this->get_original_album_entities($display_settings['original_settings']);
        }
        return $retval;
    }
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if ($displayed_gallery = $this->object->get_param('displayed_gallery')) {
            $ds = $displayed_gallery->display_settings;
            if ($this->are_breadcrumbs_enabled($ds) && ($original_entities = $this->get_original_album_entities($ds))) {
                $original_entities = $this->get_original_album_entities($ds);
                if (!empty($ds['original_album_id'])) {
                    $ids = $ds['original_album_id'];
                } else {
                    $ids = $displayed_gallery->container_ids;
                }
                $breadcrumbs = $this->object->generate_breadcrumb($ids, $original_entities);
                foreach ($root_element->find('nextgen_gallery.gallery_container', TRUE) as $container) {
                    $container->insert($breadcrumbs);
                }
            }
        }
        return $root_element;
    }
    function render_legacy_template_breadcrumbs($displayed_gallery, $entities, $gallery_id = FALSE)
    {
        $ds = $displayed_gallery->display_settings;
        if (!empty($entities) && !empty($ds['template']) && $this->are_breadcrumbs_enabled($ds)) {
            if ($gallery_id) {
                if (is_array($gallery_id)) {
                    $ids = $gallery_id;
                } else {
                    $ids = array($gallery_id);
                }
            } elseif (!empty($ds['original_album_id'])) {
                $ids = $ds['original_album_id'];
            } else {
                $ids = $displayed_gallery->container_ids;
            }
            // Prevent galleries with the same ID as the parent album being displayed as the root
            // breadcrumb when viewing the album page
            if (is_array($ids) && count($ids) == 1 && strpos($ids[0], 'a') !== 0) {
                $ids = array();
            }
            if (!empty($ds['original_album_entities'])) {
                $breadcrumb_entities = $ds['original_album_entities'];
            } else {
                $breadcrumb_entities = $entities;
            }
            return $this->object->generate_breadcrumb($ids, $breadcrumb_entities);
        } else {
            return '';
        }
    }
    function find_gallery_parent($gallery_id, $sortorder)
    {
        $map = C_Album_Mapper::get_instance();
        $found = array();
        foreach ($sortorder as $order) {
            if (strpos($order, 'a') === 0) {
                $album_id = ltrim($order, 'a');
                if (empty($this->breadcrumb_cache[$order])) {
                    $album = $map->find($album_id);
                    $this->breadcrumb_cache[$order] = $album;
                    if (in_array($gallery_id, $album->sortorder)) {
                        $found[] = $album;
                        break;
                    } else {
                        $found = $this->find_gallery_parent($gallery_id, $album->sortorder);
                        if ($found) {
                            $found[] = $album;
                            break;
                        }
                    }
                }
            }
        }
        return $found;
    }
    function generate_breadcrumb($gallery_id, $entities)
    {
        $found = array();
        $router = C_Router::get_instance();
        $app = $router->get_routed_app();
        if (is_array($gallery_id)) {
            $gallery_id = array_shift($gallery_id);
        }
        if (is_array($gallery_id)) {
            $gallery_id = $gallery_id[0];
        }
        foreach ($entities as $ndx => $entity) {
            $tmpid = (isset($entity->albumdesc) ? 'a' : '') . $entity->{$entity->id_field};
            $this->breadcrumb_cache[$tmpid] = $entity;
            if (isset($entity->albumdesc) && in_array($gallery_id, $entity->sortorder)) {
                $found[] = $entity;
                break;
            }
        }
        if (empty($found)) {
            foreach ($entities as $entity) {
                if (!empty($entity->sortorder)) {
                    $found = $this->object->find_gallery_parent($gallery_id, $entity->sortorder);
                }
                if (!empty($found)) {
                    $found[] = $entity;
                    break;
                }
            }
        }
        $found = array_reverse($found);
        if (strpos($gallery_id, 'a') === 0) {
            $album_found = FALSE;
            foreach ($found as $found_item) {
                if ($found_item->{$found_item->id_field} == $gallery_id) {
                    $album_found = TRUE;
                }
            }
            if (!$album_found) {
                $album_id = ltrim($gallery_id, 'a');
                $album = C_Album_Mapper::get_instance()->find($album_id);
                $found[] = $album;
                $this->breadcrumb_cache[$gallery_id] = $album;
            }
        } else {
            $gallery_found = FALSE;
            foreach ($entities as $entity) {
                if (isset($entity->is_gallery) && $entity->is_gallery && $gallery_id == $entity->{$entity->id_field}) {
                    $gallery_found = TRUE;
                    $found[] = $entity;
                    break;
                }
            }
            if (!$gallery_found) {
                $gallery = C_Gallery_Mapper::get_instance()->find($gallery_id);
                if ($gallery != null) {
                    $found[] = $gallery;
                    $this->breadcrumb_cache[$gallery->{$gallery->id_field}] = $gallery;
                }
            }
        }
        $crumbs = array();
        if (!empty($found)) {
            $end = end($found);
            reset($found);
            foreach ($found as $found_item) {
                $type = isset($found_item->albumdesc) ? 'album' : 'gallery';
                $id = ($type == 'album' ? 'a' : '') . $found_item->{$found_item->id_field};
                $entity = $this->breadcrumb_cache[$id];
                $link = NULL;
                if ($type == 'album') {
                    $name = $entity->name;
                    if ($entity->pageid > 0) {
                        $link = @get_page_link($entity->pageid);
                    }
                    if (empty($link) && $found_item !== $end) {
                        $link = $app->get_routed_url();
                        $link = $app->strip_param_segments($link);
                        $link = $app->set_parameter_value('album', $entity->slug, NULL, FALSE, $link);
                    }
                } else {
                    $name = $entity->title;
                }
                $crumbs[] = array('type' => $type, 'name' => $name, 'url' => $link);
            }
        }
        // free this memory immediately
        $this->breadcrumb_cache = array();
        $view = new C_MVC_View('photocrati-nextgen_basic_album#breadcrumbs', array('breadcrumbs' => $crumbs, 'divisor' => apply_filters('ngg_breadcrumb_separator', ' &raquo; ')));
        return $view->render(TRUE);
    }
}
/**
 * Because enqueueing an albums child entities (for use in lightboxes) is slow to do inside of cache_action() and
 * we can't guarantee index_action() will run on every hit (thanks to page caching) we inline those entities into
 * our basic albums templates under a window.load listener.
 *
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_NextGen_Album_Child_Entities extends Mixin
{
    protected static $_runonce = FALSE;
    public static $_entities = array();
    /**
     * The album controller will invoke this filter when its _render_album() method is called
     */
    function __construct()
    {
        if (!self::$_runonce) {
            add_filter('ngg_album_prepared_child_entity', array($this, 'register_child_gallery'), 10, 2);
        } else {
            self::$_runonce = TRUE;
        }
    }
    /**
     * Register each gallery belonging to the album that has just been rendered, so that when the MVC controller
     * system 'catches up' and runs $this->render_object() that method knows what galleries to inline as JS.
     *
     * @param array $galleries
     * @param $displayed_gallery
     * @return array mixed
     */
    function register_child_gallery($galleries, $displayed_gallery)
    {
        if (!$this->is_basic_album($displayed_gallery)) {
            return $galleries;
        }
        $id = $displayed_gallery->ID();
        foreach ($galleries as $gallery) {
            if ($gallery->is_album) {
                continue;
            }
            self::$_entities[$id][] = $gallery;
        }
        return $galleries;
    }
    function is_basic_album($displayed_gallery)
    {
        return in_array($displayed_gallery->display_type, array(NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM));
    }
    /**
     * Determine if we need to append the JS to the current template. This method static for the basic album controller to access.
     *
     * @param $display_settings
     * @return bool
     */
    static function are_child_entities_enabled($display_settings)
    {
        $retval = FALSE;
        if (empty($display_settings['open_gallery_in_lightbox'])) {
            $display_settings['open_gallery_in_lightbox'] = 0;
        }
        if ($display_settings['open_gallery_in_lightbox'] == 1) {
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * Search inside the template for the inside of the container and append our inline JS
     */
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if ($displayed_gallery = $this->object->get_param('displayed_gallery')) {
            if (!$this->is_basic_album($displayed_gallery)) {
                return $root_element;
            }
            $ds = $displayed_gallery->display_settings;
            if (self::are_child_entities_enabled($ds)) {
                $id = $displayed_gallery->ID();
                foreach ($root_element->find('nextgen_gallery.gallery_container', TRUE) as $container) {
                    $container->append(self::generate_script(self::$_entities[$id]));
                }
            }
        }
        return $root_element;
    }
    /**
     * Generate the JS that will be inserted into the template. This method static for the basic album controller to access.
     *
     * @param array $galleries
     * @return string
     */
    static function generate_script($galleries)
    {
        $retval = '<script type="text/javascript">window.addEventListener("load", function() {';
        foreach ($galleries as $gallery) {
            $dg = $gallery->displayed_gallery;
            $id = $dg->id();
            $retval .= 'galleries.gallery_' . $id . ' = ' . json_encode($dg->get_entity()) . ';';
            $retval .= 'galleries.gallery_' . $id . '.wordpress_page_root = "' . get_permalink() . '";';
        }
        $retval .= '}, false);</script>';
        return $retval;
    }
}
/**
 * Class A_NextGen_Album_Descriptions
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_NextGen_Album_Descriptions extends Mixin
{
    // When viewing a child gallery the album controller's add_description_to_legacy_templates() method will be
    // called for the gallery and then again for the root album; we only want to run once
    public static $_description_added_once = FALSE;
    function are_descriptions_enabled($display_settings)
    {
        $retval = FALSE;
        if (isset($display_settings['enable_descriptions']) && $display_settings['enable_descriptions']) {
            $retval = TRUE;
        } elseif (isset($display_settings['original_settings']) && $this->are_descriptions_enabled($display_settings['original_settings'])) {
            $retval = TRUE;
        }
        return $retval;
    }
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if ($displayed_gallery = $this->object->get_param('displayed_gallery')) {
            $ds = $displayed_gallery->display_settings;
            if ($this->are_descriptions_enabled($ds)) {
                $description = $this->object->generate_description($displayed_gallery);
                foreach ($root_element->find('nextgen_gallery.gallery_container', TRUE) as $container) {
                    // Determine where (to be compatible with breadcrumbs) in the container to insert
                    $pos = 0;
                    foreach ($container->_list as $ndx => $item) {
                        if (is_string($item)) {
                            $pos = $ndx;
                        } else {
                            break;
                        }
                    }
                    $container->insert($description, $pos);
                }
            }
        }
        return $root_element;
    }
    function render_legacy_template_description($displayed_gallery)
    {
        if (!empty($displayed_gallery->display_settings['template']) && $this->are_descriptions_enabled($displayed_gallery->display_settings)) {
            return $this->object->generate_description($displayed_gallery);
        } else {
            return '';
        }
    }
    function generate_description($displayed_gallery)
    {
        if (self::$_description_added_once) {
            return '';
        }
        self::$_description_added_once = TRUE;
        $description = $this->get_description($displayed_gallery);
        $view = new C_MVC_View('photocrati-nextgen_basic_album#descriptions', array('description' => $description));
        return $view->render(TRUE);
    }
    function get_description($displayed_gallery)
    {
        $description = '';
        // Important: do not array_shift() $displayed_gallery->container_ids as it will affect breadcrumbs
        $container_ids = $displayed_gallery->container_ids;
        if ($displayed_gallery->source == 'galleries') {
            $gallery_id = array_shift($container_ids);
            $gallery = C_Gallery_Mapper::get_instance()->find($gallery_id);
            if ($gallery && !empty($gallery->galdesc)) {
                $description = $gallery->galdesc;
            }
        } else {
            if ($displayed_gallery->source == 'albums') {
                $album_id = array_shift($container_ids);
                $album = C_Album_Mapper::get_instance()->find($album_id);
                if ($album && !empty($album->albumdesc)) {
                    $description = $album->albumdesc;
                }
            }
        }
        return $description;
    }
}
/**
 * Provides validation for NextGen Basic Albums
 * @mixin C_Album_Mapper
 * @adapts I_Album_Mapper
 */
class A_NextGen_Basic_Album extends Mixin
{
    function validation()
    {
        $ngglegacy_albums = array(NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM);
        if (in_array($this->object->name, $ngglegacy_albums)) {
            $this->object->validates_presence_of('gallery_display_type');
            $this->object->validates_numericality_of('galleries_per_page');
        }
        return $this->call_parent('validation');
    }
    function get_order()
    {
        return NGG_DISPLAY_PRIORITY_BASE + NGG_DISPLAY_PRIORITY_STEP;
    }
}
/**
 * Class A_NextGen_Basic_Album_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller
 * @property C_Display_Type_Controller|A_NextGen_Basic_Album_Controller $object
 */
class A_NextGen_Basic_Album_Controller extends Mixin_NextGen_Basic_Pagination
{
    var $albums = array();
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
        // Without this line the param() method will always return NULL when in wp_enqueue_scripts
        $renderer = C_Displayed_Gallery_Renderer::get_instance('inner');
        $renderer->do_app_rewrites($displayed_gallery);
        $display_settings = $displayed_gallery->display_settings;
        $gallery = $gallery_slug = $this->param('gallery');
        if ($gallery && strpos($gallery, 'nggpage--') !== 0) {
            $result = C_Gallery_Mapper::get_instance()->get_by_slug($gallery);
            if ($result) {
                $gallery = $result->{$result->id_field};
            }
            $parent_albums = $displayed_gallery->get_albums();
            $gallery_params = array('source' => 'galleries', 'container_ids' => array($gallery), 'display_type' => $display_settings['gallery_display_type'], 'original_display_type' => $displayed_gallery->display_type, 'original_settings' => $display_settings, 'original_album_entities' => $parent_albums);
            if (!empty($display_settings['gallery_display_template'])) {
                $gallery_params['template'] = $display_settings['gallery_display_template'];
            }
            $displayed_gallery = $renderer->params_to_displayed_gallery($gallery_params);
            if (is_null($displayed_gallery->id())) {
                $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));
            }
            self::$alternate_displayed_galleries[$id] = $displayed_gallery;
        }
        return $displayed_gallery;
    }
    /**
     * Renders the front-end for the NextGen Basic Album display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        $display_settings = $displayed_gallery->display_settings;
        // We need to fetch the album containers selected in the Attach
        // to Post interface. We need to do this, because once we fetch the
        // included entities, we need to iterate over each entity and assign it
        // a parent_id, which is the album that it belongs to. We need to do this
        // because the link to the gallery, is not /nggallery/gallery--id, but
        // /nggallery/album--id/gallery--id
        // Are we to display a gallery? Ensure our 'gallery' isn't just a paginated album view
        $gallery = $gallery_slug = $this->param('gallery');
        if ($gallery && strpos($gallery, 'nggpage--') !== 0) {
            // basic albums only support one per post
            if (isset($GLOBALS['nggShowGallery'])) {
                return '';
            }
            $GLOBALS['nggShowGallery'] = TRUE;
            $alternate_displayed_gallery = $this->object->get_alternate_displayed_gallery($displayed_gallery);
            if ($alternate_displayed_gallery !== $displayed_gallery) {
                $renderer = C_Displayed_Gallery_Renderer::get_instance('inner');
                add_filter('ngg_displayed_gallery_rendering', array($this, 'add_description_to_legacy_templates'), 8, 2);
                add_filter('ngg_displayed_gallery_rendering', array($this, 'add_breadcrumbs_to_legacy_templates'), 9, 2);
                $output = $renderer->display_images($alternate_displayed_gallery, $return);
                remove_filter('ngg_displayed_gallery_rendering', array($this, 'add_breadcrumbs_to_legacy_templates'));
                remove_filter('ngg_displayed_gallery_rendering', array($this, 'add_description_to_legacy_templates'));
                return $output;
            }
        } else {
            if ($album = $this->param('album')) {
                // Are we to display a sub-album?
                $result = C_Album_Mapper::get_instance()->get_by_slug($album);
                $album_sub = $result ? $result->{$result->id_field} : null;
                if ($album_sub != null) {
                    $album = $album_sub;
                }
                $displayed_gallery->entity_ids = array();
                $displayed_gallery->sortorder = array();
                $displayed_gallery->container_ids = ($album === '0' or $album === 'all') ? array() : array($album);
                $displayed_gallery->display_settings['original_album_id'] = 'a' . $album_sub;
                $displayed_gallery->display_settings['original_album_entities'] = $displayed_gallery->get_albums();
            }
        }
        // Get the albums
        // TODO: This should probably be moved to the elseif block above
        $this->albums = $displayed_gallery->get_albums();
        // None of the above: Display the main album. Get the settings required for display
        $current_page = (int) $this->param('page', $displayed_gallery->id(), 1);
        $offset = $display_settings['galleries_per_page'] * ($current_page - 1);
        $entities = $displayed_gallery->get_included_entities($display_settings['galleries_per_page'], $offset);
        // If there are entities to be displayed
        if ($entities) {
            $pagination_result = $this->object->create_pagination($current_page, $displayed_gallery->get_entity_count(), $display_settings['galleries_per_page'], urldecode($this->object->param('ajax_pagination_referrer') ?: ''));
            $display_settings['entities'] = $entities;
            $display_settings['pagination'] = $pagination_result['output'];
            $display_settings['displayed_gallery'] = $displayed_gallery;
            $display_settings = $this->prepare_legacy_album_params($displayed_gallery->get_entity(), $display_settings);
            if (!empty($display_settings['template']) && $display_settings['template'] != 'default') {
                // Add additional parameters
                $this->object->remove_param('ajax_pagination_referrer');
                $display_settings['current_page'] = $current_page;
                $display_settings['pagination_prev'] = $pagination_result['prev'];
                $display_settings['pagination_next'] = $pagination_result['next'];
                // Legacy templates lack a good way of injecting content at the right time
                $this->object->add_mixin('Mixin_NextGen_Basic_Templates');
                $this->object->add_mixin('A_NextGen_Album_Breadcrumbs');
                $this->object->add_mixin('A_NextGen_Album_Descriptions');
                $breadcrumbs = $this->object->render_legacy_template_breadcrumbs($displayed_gallery, $entities);
                $description = $this->object->render_legacy_template_description($displayed_gallery);
                // If enabled enqueue the child entities as JSON for lightboxes to read
                if (A_NextGen_Album_Child_Entities::are_child_entities_enabled($display_settings)) {
                    $script = A_NextGen_Album_Child_Entities::generate_script($entities);
                }
                $retval = $this->object->legacy_render($display_settings['template'], $display_settings, $return, 'album');
                if (!empty($description)) {
                    $retval = $description . $retval;
                }
                if (!empty($breadcrumbs)) {
                    $retval = $breadcrumbs . $retval;
                }
                if (!empty($script)) {
                    $retval = $retval . $script;
                }
                return $retval;
            } else {
                $params = $display_settings;
                $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
                switch ($displayed_gallery->display_type) {
                    case NGG_BASIC_COMPACT_ALBUM:
                        $template = 'compact';
                        break;
                    case NGG_BASIC_EXTENDED_ALBUM:
                        $template = 'extended';
                        break;
                }
                return $this->object->render_partial("photocrati-nextgen_basic_album#{$template}", $params, $return);
            }
        } else {
            return $this->object->render_partial('photocrati-nextgen_gallery_display#no_images_found', array(), $return);
        }
    }
    /**
     * Creates a displayed gallery of a gallery belonging to an album. Shared by index_action() and cache_action() to
     * allow lightboxes to open album children directly.
     *
     * @param $gallery
     * @param $display_settings
     * @return C_Displayed_Gallery
     */
    function make_child_displayed_gallery($gallery, $display_settings)
    {
        $gallery->displayed_gallery = new C_Displayed_Gallery();
        $gallery->displayed_gallery->container_ids = array($gallery->{$gallery->id_field});
        $gallery->displayed_gallery->display_settings = $display_settings;
        $gallery->displayed_gallery->returns = 'included';
        $gallery->displayed_gallery->source = 'galleries';
        $gallery->displayed_gallery->images_list_count = $gallery->displayed_gallery->get_entity_count();
        $gallery->displayed_gallery->is_album_gallery = TRUE;
        $gallery->displayed_gallery->to_transient();
        return $gallery;
    }
    function add_breadcrumbs_to_legacy_templates($html, $displayed_gallery)
    {
        $this->object->add_mixin('A_NextGen_Album_Breadcrumbs');
        $original_album_entities = array();
        if (isset($displayed_gallery->display_settings['original_album_entities'])) {
            $original_album_entities = $displayed_gallery->display_settings['original_album_entities'];
        } elseif (isset($displayed_gallery->display_settings['original_settings']) && isset($displayed_gallery->display_settings['original_settings']['original_album_entities'])) {
            $original_album_entities = $displayed_gallery->display_settings['original_settings']['original_album_entities'];
        }
        $breadcrumbs = $this->object->render_legacy_template_breadcrumbs($displayed_gallery, $original_album_entities, $displayed_gallery->container_ids);
        if (!empty($breadcrumbs)) {
            $html = $breadcrumbs . $html;
        }
        return $html;
    }
    function add_description_to_legacy_templates($html, $displayed_gallery)
    {
        $this->object->add_mixin('A_NextGen_Album_Descriptions');
        $description = $this->object->render_legacy_template_description($displayed_gallery);
        if (!empty($description)) {
            $html = $description . $html;
        }
        return $html;
    }
    /**
     * Gets the parent album for the entity being displayed
     * @param int $entity_id
     * @return null|object Album object
     */
    function get_parent_album_for($entity_id)
    {
        $retval = NULL;
        foreach ($this->albums as $album) {
            if (in_array($entity_id, $album->sortorder)) {
                $retval = $album;
                break;
            }
        }
        return $retval;
    }
    function prepare_legacy_album_params($displayed_gallery, $params)
    {
        $image_mapper = C_Image_Mapper::get_instance();
        $storage = C_Gallery_Storage::get_instance();
        $image_gen = C_Dynamic_Thumbnails_Manager::get_instance();
        if (empty($displayed_gallery->display_settings['override_thumbnail_settings'])) {
            // legacy templates expect these dimensions
            $image_gen_params = array('width' => 91, 'height' => 68, 'crop' => TRUE);
        } else {
            // use settings requested by user
            $image_gen_params = array('width' => $displayed_gallery->display_settings['thumbnail_width'], 'height' => $displayed_gallery->display_settings['thumbnail_height'], 'quality' => isset($displayed_gallery->display_settings['thumbnail_quality']) ? $displayed_gallery->display_settings['thumbnail_quality'] : 100, 'crop' => isset($displayed_gallery->display_settings['thumbnail_crop']) ? $displayed_gallery->display_settings['thumbnail_crop'] : NULL, 'watermark' => isset($displayed_gallery->display_settings['thumbnail_watermark']) ? $displayed_gallery->display_settings['thumbnail_watermark'] : NULL);
        }
        // so user templates can know how big the images are expected to be
        $params['image_gen_params'] = $image_gen_params;
        // Transform entities
        $params['galleries'] = $params['entities'];
        unset($params['entities']);
        foreach ($params['galleries'] as &$gallery) {
            // Get the preview image url
            $gallery->previewurl = '';
            if ($gallery->previewpic && $gallery->previewpic > 0) {
                if ($image = $image_mapper->find(intval($gallery->previewpic))) {
                    $gallery->previewpic_image = $image;
                    $gallery->previewpic_fullsized_url = $storage->get_image_url($image);
                    $gallery->previewurl = $storage->get_image_url($image, $image_gen->get_size_name($image_gen_params), TRUE);
                    $gallery->previewname = $gallery->name;
                } else {
                    $gallery->no_previewpic = TRUE;
                }
            }
            // Get the page link. If the entity is an album, then the url will
            // look like /nggallery/album--slug.
            $id_field = $gallery->id_field;
            if ($gallery->is_album) {
                if ($gallery->pageid > 0) {
                    $gallery->pagelink = @get_page_link($gallery->pageid);
                } else {
                    $pagelink = $this->object->get_routed_url(TRUE);
                    $pagelink = $this->object->remove_param_for($pagelink, 'album');
                    $pagelink = $this->object->remove_param_for($pagelink, 'gallery');
                    $pagelink = $this->object->remove_param_for($pagelink, 'nggpage');
                    $pagelink = $this->object->set_param_for($pagelink, 'album', $gallery->slug);
                    $gallery->pagelink = $pagelink;
                }
            } else {
                if ($gallery->pageid > 0) {
                    $gallery->pagelink = @get_page_link($gallery->pageid);
                }
                if (empty($gallery->pagelink)) {
                    $pagelink = $this->object->get_routed_url(TRUE);
                    $parent_album = $this->object->get_parent_album_for($gallery->{$id_field});
                    if ($parent_album) {
                        $pagelink = $this->object->remove_param_for($pagelink, 'album');
                        $pagelink = $this->object->remove_param_for($pagelink, 'gallery');
                        $pagelink = $this->object->remove_param_for($pagelink, 'nggpage');
                        $pagelink = $this->object->set_param_for($pagelink, 'album', $parent_album->slug);
                    } else {
                        if ($displayed_gallery->container_ids === array('0') || $displayed_gallery->container_ids === array('')) {
                            $pagelink = $this->object->set_param_for($pagelink, 'album', 'all');
                        } else {
                            $pagelink = $this->object->remove_param_for($pagelink, 'nggpage');
                            $pagelink = $this->object->set_param_for($pagelink, 'album', 'album');
                        }
                    }
                    $gallery->pagelink = $this->object->set_param_for($pagelink, 'gallery', $gallery->slug);
                }
            }
            // Mark the child type
            $gallery->entity_type = isset($gallery->is_gallery) && intval($gallery->is_gallery) ? 'gallery' : 'album';
            // If this setting is on we need to inject an effect code
            if (!empty($displayed_gallery->display_settings['open_gallery_in_lightbox']) && $gallery->entity_type == 'gallery') {
                $gallery = $this->object->make_child_displayed_gallery($gallery, $displayed_gallery->display_settings);
                if ($this->does_lightbox_support_displayed_gallery($displayed_gallery)) {
                    $gallery->displayed_gallery->effect_code = $this->object->get_effect_code($gallery->displayed_gallery);
                }
            }
            // Let plugins modify the gallery
            $gallery = apply_filters('ngg_album_galleryobject', $gallery);
        }
        // In at least one rare and so far impossible to reproduce circumstance it's possible for this controller to run
        // before the following adapter is constructed and thus allowed to register its hook on the following filter below.
        // This breaks the 'open child galleries in pro lightbox' feature.
        new A_NextGen_Album_Child_Entities();
        $params['galleries'] = apply_filters('ngg_album_prepared_child_entity', $params['galleries'], $params['displayed_gallery']);
        $params['album'] = reset($this->albums);
        $params['albums'] = $this->albums;
        // Clean up
        unset($storage);
        unset($image_mapper);
        unset($image_gen);
        unset($image_gen_params);
        return $params;
    }
    function _get_js_lib_url()
    {
        return $this->object->get_static_url('photocrati-nextgen_basic_album#init.js');
    }
    /**
     * Enqueues all static resources required by this display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('nextgen_basic_album_style', $this->object->get_static_url('photocrati-nextgen_basic_album#nextgen_basic_album.css'), [], NGG_SCRIPT_VERSION);
        wp_enqueue_style('nextgen_pagination_style', $this->get_static_url('photocrati-nextgen_pagination#style.css'), [], NGG_SCRIPT_VERSION);
        wp_enqueue_script('shave.js');
        $ds = $displayed_gallery->display_settings;
        if (!empty($ds['enable_breadcrumbs']) && $ds['enable_breadcrumbs'] || !empty($ds['original_settings']['enable_breadcrumbs']) && $ds['original_settings']['enable_breadcrumbs']) {
            wp_enqueue_style('nextgen_basic_album_breadcrumbs_style', $this->object->get_static_url('photocrati-nextgen_basic_album#breadcrumbs.css'), array(), NGG_SCRIPT_VERSION);
        }
    }
}
/**
 * Class A_NextGen_Basic_Album_Mapper
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Basic_Album_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name) && in_array($entity->name, array(NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM))) {
            // Set defaults for both display (album) types
            $settings = C_NextGen_Settings::get_instance();
            $default_template = isset($entity->settings["template"]) ? 'default' : 'default-view.php';
            $this->object->_set_default_value($entity, 'settings', 'display_view', $default_template);
            $this->object->_set_default_value($entity, 'settings', 'galleries_per_page', $settings->galPagedGalleries);
            $this->object->_set_default_value($entity, 'settings', 'enable_breadcrumbs', 1);
            $this->object->_set_default_value($entity, 'settings', 'disable_pagination', 0);
            $this->object->_set_default_value($entity, 'settings', 'enable_descriptions', 0);
            $this->object->_set_default_value($entity, 'settings', 'template', '');
            $this->object->_set_default_value($entity, 'settings', 'open_gallery_in_lightbox', 0);
            $this->_set_default_value($entity, 'settings', 'override_thumbnail_settings', 1);
            $this->_set_default_value($entity, 'settings', 'thumbnail_quality', $settings->thumbquality);
            $this->_set_default_value($entity, 'settings', 'thumbnail_crop', 1);
            $this->_set_default_value($entity, 'settings', 'thumbnail_watermark', 0);
            // Thumbnail dimensions -- only used by extended albums
            if ($entity->name == NGG_BASIC_COMPACT_ALBUM) {
                $this->_set_default_value($entity, 'settings', 'thumbnail_width', 240);
                $this->_set_default_value($entity, 'settings', 'thumbnail_height', 160);
            }
            // Thumbnail dimensions -- only used by extended albums
            if ($entity->name == NGG_BASIC_EXTENDED_ALBUM) {
                $this->_set_default_value($entity, 'settings', 'thumbnail_width', 300);
                $this->_set_default_value($entity, 'settings', 'thumbnail_height', 200);
            }
            if (defined('NGG_BASIC_THUMBNAILS')) {
                $this->object->_set_default_value($entity, 'settings', 'gallery_display_type', NGG_BASIC_THUMBNAILS);
            }
            $this->object->_set_default_value($entity, 'settings', 'gallery_display_template', '');
            $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
        }
    }
}
/**
 * Class A_NextGen_Basic_Album_Routes
 * @mixin C_Displayed_Gallery_Renderer
 * @adapts I_Displayed_Gallery_Renderer
 * @property A_NextGen_Basic_Album_Routes|C_Displayed_Gallery_Renderer $object
 */
class A_NextGen_Basic_Album_Routes extends Mixin
{
    protected static $has_ran;
    function do_app_rewrites($displayed_gallery)
    {
        if (self::$has_ran) {
            return;
        }
        self::$has_ran = TRUE;
        $this->object->call_parent('do_app_rewrites', $displayed_gallery);
        $do_rewrites = FALSE;
        $app = NULL;
        // Get display types
        $original_display_type = isset($displayed_gallery->display_settings['original_display_type']) ? $displayed_gallery->display_settings['original_display_type'] : '';
        $display_type = $displayed_gallery->display_type;
        // If we're viewing an album, rewrite the urls
        $regex = "/photocrati-nextgen_basic_\\w+_album/";
        if (preg_match($regex, $display_type)) {
            $do_rewrites = TRUE;
            // Get router
            $router = C_Router::get_instance();
            $app = $router->get_routed_app();
            $slug = '/' . C_NextGen_Settings::get_instance()->get('router_param_slug', 'nggallery');
            $app->rewrite("{*}{$slug}/page/{\\d}{*}", "{1}{$slug}/nggpage--{2}{3}", FALSE, TRUE);
            $app->rewrite("{*}{$slug}/pid--{*}", "{1}{$slug}/pid--{2}", FALSE, TRUE);
            // avoid conflicts with imagebrowser
            $app->rewrite("{*}{$slug}/{\\w}/{\\w}/{\\w}{*}", "{1}{$slug}/album--{2}/gallery--{3}/{4}{5}", FALSE, TRUE);
            $app->rewrite("{*}{$slug}/{\\w}/{\\w}", "{1}{$slug}/album--{2}/gallery--{3}", FALSE, TRUE);
            // TODO: We're commenting this out as it was causing a problem with sub-album requests not
            // working when placed beside paginated galleries. But we still need to figure out why, and fix that
            // $app->rewrite("{*}{$slug}/{\\w}", "{1}{$slug}/album--{2}", FALSE, TRUE);
        } elseif (preg_match($regex, $original_display_type)) {
            $do_rewrites = TRUE;
            // Get router
            $router = C_Router::get_instance();
            $app = $router->get_routed_app();
            $slug = '/' . C_NextGen_Settings::get_instance()->get('router_param_slug', 'nggallery');
            $app->rewrite("{*}{$slug}/album--{\\w}", "{1}{$slug}/{2}");
            $app->rewrite("{*}{$slug}/album--{\\w}/gallery--{\\w}", "{1}{$slug}/{2}/{3}");
            $app->rewrite("{*}{$slug}/album--{\\w}/gallery--{\\w}/{*}", "{1}{$slug}/{2}/{3}/{4}");
        }
        // Perform rewrites
        if ($do_rewrites && $app) {
            $app->do_rewrites();
        }
    }
    function render($displayed_gallery, $return = FALSE, $mode = NULL)
    {
        $this->object->do_app_rewrites($displayed_gallery);
        return $this->call_parent('render', $displayed_gallery, $return, $mode);
    }
}
/**
 * Class A_NextGen_Basic_Album_Urls
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_NextGen_Basic_Album_Urls extends Mixin
{
    function create_parameter_segment($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        if ($key == 'nggpage') {
            return 'page/' . $value;
        } elseif ($key == 'album') {
            return $value;
        } elseif ($key == 'gallery') {
            return $value;
        } else {
            return $this->call_parent('create_parameter_segment', $key, $value, $id, $use_prefix);
        }
    }
    function remove_parameter($key, $id = NULL, $url = FALSE)
    {
        $retval = $this->call_parent('remove_parameter', $key, $id, $url);
        $settings = C_NextGen_Settings::get_instance();
        $param_slug = preg_quote($settings->get('router_param_slug', 'nggallery'), '#');
        if (preg_match("#(/{$param_slug}/.*)album--#", $retval, $matches)) {
            $retval = str_replace($matches[0], $matches[1], $retval);
        }
        if (preg_match("#(/{$param_slug}/.*)gallery--#", $retval, $matches)) {
            $retval = str_replace($matches[0], $matches[1], $retval);
        }
        return $retval;
    }
}
/**
 * Class Mixin_NextGen_Basic_Album_Form
 * @mixin C_Form
 */
class Mixin_NextGen_Basic_Album_Form extends Mixin_Display_Type_Form
{
    function _get_field_names()
    {
        return array('nextgen_basic_album_gallery_display_type', 'nextgen_basic_album_galleries_per_page', 'nextgen_basic_album_enable_breadcrumbs', 'display_view', 'nextgen_basic_templates_template', 'nextgen_basic_album_enable_descriptions');
    }
    /**
     * Renders the Gallery Display Type field
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_album_gallery_display_type_field($display_type)
    {
        $mapper = C_Display_Type_Mapper::get_instance();
        // Disallow hidden or inactive display types
        $types = $mapper->find_by_entity_type('image');
        foreach ($types as $ndx => $type) {
            if (!empty($type->hidden_from_ui) && $type->hidden_from_ui) {
                unset($types[$ndx]);
            }
        }
        return $this->render_partial('photocrati-nextgen_basic_album#nextgen_basic_album_gallery_display_type', array('display_type_name' => $display_type->name, 'gallery_display_type_label' => __('Display galleries as', 'nggallery'), 'gallery_display_type_help' => __('How would you like galleries to be displayed?', 'nggallery'), 'gallery_display_type' => $display_type->settings['gallery_display_type'], 'galleries_per_page_label' => __('Galleries per page', 'nggallery'), 'galleries_per_page' => $display_type->settings['galleries_per_page'], 'display_types' => $types), TRUE);
    }
    /**
     * Renders the Galleries Per Page field
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_nextgen_basic_album_galleries_per_page_field($display_type)
    {
        return $this->_render_number_field($display_type, 'galleries_per_page', __('Items per page', 'nggallery'), $display_type->settings['galleries_per_page'], __('Maximum number of galleries or sub-albums to appear on a single page', 'nggallery'), FALSE, '', 0);
    }
    function _render_nextgen_basic_album_enable_breadcrumbs_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'enable_breadcrumbs', __('Enable breadcrumbs', 'nggallery'), isset($display_type->settings['enable_breadcrumbs']) ? $display_type->settings['enable_breadcrumbs'] : FALSE);
    }
    function _render_nextgen_basic_album_enable_descriptions_field($display_type)
    {
        return $this->_render_radio_field($display_type, 'enable_descriptions', __('Display descriptions', 'nggallery'), $display_type->settings['enable_descriptions']);
    }
}
/**
 * Class A_NextGen_Basic_Extended_Album_Form
 * @mixin C_Form
 * @adapts I_Form for the "photocrati-nextgen_basic_extended_album" context
 */
class A_NextGen_Basic_Extended_Album_Form extends Mixin_NextGen_Basic_Album_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_EXTENDED_ALBUM;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        $fields = parent::_get_field_names();
        $fields[] = 'thumbnail_override_settings';
        return $fields;
    }
    /**
     * Enqueues static resources required by this form
     */
    function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_extended_albums_settings_script', $this->object->get_static_url('photocrati-nextgen_basic_album#extended_settings.js'), array('jquery.nextgen_radio_toggle'));
    }
}
/**
 * Class A_NextGen_Basic_Compact_Album_Form
 * @mixin C_Form
 * @adapts I_Form for the "photocrati-nextgen_basic_compact_album" context
 */
class A_NextGen_Basic_Compact_Album_Form extends Mixin_NextGen_Basic_Album_Form
{
    function get_display_type_name()
    {
        return NGG_BASIC_COMPACT_ALBUM;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        $fields = parent::_get_field_names();
        $fields[] = 'thumbnail_override_settings';
        return $fields;
    }
    /**
     * Enqueues static resources required by this form
     */
    function enqueue_static_resources()
    {
        $this->object->enqueue_script('nextgen_basic_compact_albums_settings_script', $this->object->get_static_url('photocrati-nextgen_basic_album#compact_settings.js'), array('jquery.nextgen_radio_toggle'));
    }
}