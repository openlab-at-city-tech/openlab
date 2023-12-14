<?php
/**
 * Class A_NextGen_Album_Breadcrumbs
 *
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_NextGen_Album_Breadcrumbs extends Mixin
{
    public $breadcrumb_cache = array();
    public function are_breadcrumbs_enabled($display_settings)
    {
        $retval = false;
        if (isset($display_settings['enable_breadcrumbs']) && $display_settings['enable_breadcrumbs']) {
            $retval = true;
        } elseif (isset($display_settings['original_settings']) && $this->are_breadcrumbs_enabled($display_settings['original_settings'])) {
            $retval = true;
        }
        return $retval;
    }
    public function get_original_album_entities($display_settings)
    {
        $retval = [];
        if (isset($display_settings['original_album_entities'])) {
            $retval = $display_settings['original_album_entities'];
        } elseif (isset($display_settings['original_settings']) && $this->get_original_album_entities($display_settings['original_settings'])) {
            $retval = $this->get_original_album_entities($display_settings['original_settings']);
        }
        return $retval;
    }
    public function render_object()
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
                foreach ($root_element->find('nextgen_gallery.gallery_container', true) as $container) {
                    $container->insert($breadcrumbs);
                }
            }
        }
        return $root_element;
    }
    public function render_legacy_template_breadcrumbs($displayed_gallery, $entities, $gallery_id = false)
    {
        $ds = $displayed_gallery->display_settings;
        if (!empty($entities) && !empty($ds['template']) && $this->are_breadcrumbs_enabled($ds)) {
            if ($gallery_id) {
                if (is_array($gallery_id)) {
                    $ids = $gallery_id;
                } else {
                    $ids = [$gallery_id];
                }
            } elseif (!empty($ds['original_album_id'])) {
                $ids = $ds['original_album_id'];
            } else {
                $ids = $displayed_gallery->container_ids;
            }
            // Prevent galleries with the same ID as the parent album being displayed as the root
            // breadcrumb when viewing the album page.
            if (is_array($ids) && count($ids) == 1 && strpos($ids[0], 'a') !== 0) {
                $ids = [];
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
    public function find_gallery_parent($gallery_id, $sortorder)
    {
        $map = \Imagely\NGG\DataMappers\Album::get_instance();
        $found = [];
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
    public function generate_breadcrumb($gallery_id, $entities)
    {
        $found = [];
        $router = \Imagely\NGG\Util\Router::get_instance();
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
            $album_found = false;
            foreach ($found as $found_item) {
                if ($found_item->{$found_item->id_field} == $gallery_id) {
                    $album_found = true;
                }
            }
            if (!$album_found) {
                $album_id = ltrim($gallery_id, 'a');
                $album = \Imagely\NGG\DataMappers\Album::get_instance()->find($album_id);
                $found[] = $album;
                $this->breadcrumb_cache[$gallery_id] = $album;
            }
        } else {
            $gallery_found = false;
            foreach ($entities as $entity) {
                if (isset($entity->is_gallery) && $entity->is_gallery && $gallery_id == $entity->{$entity->id_field}) {
                    $gallery_found = true;
                    $found[] = $entity;
                    break;
                }
            }
            if (!$gallery_found) {
                $gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find($gallery_id);
                if ($gallery != null) {
                    $found[] = $gallery;
                    $this->breadcrumb_cache[$gallery->{$gallery->id_field}] = $gallery;
                }
            }
        }
        $crumbs = [];
        if (!empty($found)) {
            $end = end($found);
            reset($found);
            foreach ($found as $found_item) {
                $type = isset($found_item->albumdesc) ? 'album' : 'gallery';
                $id = ($type == 'album' ? 'a' : '') . $found_item->{$found_item->id_field};
                $entity = $this->breadcrumb_cache[$id];
                $link = null;
                if ($type == 'album') {
                    $name = $entity->name;
                    if ($entity->pageid > 0) {
                        $link = @get_page_link($entity->pageid);
                    }
                    if (empty($link) && $found_item !== $end) {
                        $link = $app->get_routed_url();
                        $link = $app->strip_param_segments($link);
                        $link = $app->set_parameter_value('album', $entity->slug, null, false, $link);
                    }
                } else {
                    $name = $entity->title;
                }
                $crumbs[] = ['type' => $type, 'name' => $name, 'url' => $link];
            }
        }
        // free this memory immediately.
        $this->breadcrumb_cache = [];
        $view = new C_MVC_View('photocrati-nextgen_basic_album#breadcrumbs', ['breadcrumbs' => $crumbs, 'divisor' => apply_filters('ngg_breadcrumb_separator', ' &raquo; ')]);
        return $view->render(true);
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
    protected static $_runonce = false;
    public static $_entities = array();
    /**
     * The album controller will invoke this filter when its _render_album() method is called
     */
    public function __construct()
    {
        if (!self::$_runonce) {
            add_filter('ngg_album_prepared_child_entity', [$this, 'register_child_gallery'], 10, 2);
        } else {
            self::$_runonce = true;
        }
    }
    /**
     * Register each gallery belonging to the album that has just been rendered, so that when the MVC controller
     * system 'catches up' and runs $this->render_object() that method knows what galleries to inline as JS.
     *
     * @param array             $galleries
     * @param $displayed_gallery
     * @return array mixed
     */
    public function register_child_gallery($galleries, $displayed_gallery)
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
    public function is_basic_album($displayed_gallery)
    {
        return in_array($displayed_gallery->display_type, [NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM]);
    }
    /**
     * Determine if we need to append the JS to the current template. This method static for the basic album controller to access.
     *
     * @param $display_settings
     * @return bool
     */
    static function are_child_entities_enabled($display_settings)
    {
        $retval = false;
        if (empty($display_settings['open_gallery_in_lightbox'])) {
            $display_settings['open_gallery_in_lightbox'] = 0;
        }
        if ($display_settings['open_gallery_in_lightbox'] == 1) {
            $retval = true;
        }
        return $retval;
    }
    /**
     * Search inside the template for the inside of the container and append our inline JS
     */
    public function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if ($displayed_gallery = $this->object->get_param('displayed_gallery')) {
            if (!$this->is_basic_album($displayed_gallery)) {
                return $root_element;
            }
            $ds = $displayed_gallery->display_settings;
            if (self::are_child_entities_enabled($ds)) {
                $id = $displayed_gallery->ID();
                foreach ($root_element->find('nextgen_gallery.gallery_container', true) as $container) {
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
 *
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_NextGen_Album_Descriptions extends Mixin
{
    // When viewing a child gallery the album controller's add_description_to_legacy_templates() method will be
    // called for the gallery and then again for the root album; we only want to run once.
    public static $_description_added_once = false;
    public function are_descriptions_enabled($display_settings)
    {
        $retval = false;
        if (isset($display_settings['enable_descriptions']) && $display_settings['enable_descriptions']) {
            $retval = true;
        } elseif (isset($display_settings['original_settings']) && $this->are_descriptions_enabled($display_settings['original_settings'])) {
            $retval = true;
        }
        return $retval;
    }
    public function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if ($displayed_gallery = $this->object->get_param('displayed_gallery')) {
            $ds = $displayed_gallery->display_settings;
            if ($this->are_descriptions_enabled($ds)) {
                $description = $this->object->generate_description($displayed_gallery);
                foreach ($root_element->find('nextgen_gallery.gallery_container', true) as $container) {
                    // Determine where (to be compatible with breadcrumbs) in the container to insert.
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
    public function render_legacy_template_description($displayed_gallery)
    {
        if (!empty($displayed_gallery->display_settings['template']) && $this->are_descriptions_enabled($displayed_gallery->display_settings)) {
            return $this->object->generate_description($displayed_gallery);
        } else {
            return '';
        }
    }
    public function generate_description($displayed_gallery)
    {
        if (self::$_description_added_once) {
            return '';
        }
        self::$_description_added_once = true;
        $description = $this->get_description($displayed_gallery);
        $view = new C_MVC_View('photocrati-nextgen_basic_album#descriptions', ['description' => $description]);
        return $view->render(true);
    }
    public function get_description($displayed_gallery)
    {
        $description = '';
        // Important: do not array_shift() $displayed_gallery->container_ids as it will affect breadcrumbs.
        $container_ids = $displayed_gallery->container_ids;
        if ($displayed_gallery->source == 'galleries') {
            $gallery_id = array_shift($container_ids);
            $gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find($gallery_id);
            if ($gallery && !empty($gallery->galdesc)) {
                $description = $gallery->galdesc;
            }
        } elseif ($displayed_gallery->source == 'albums') {
            $album_id = array_shift($container_ids);
            $album = \Imagely\NGG\DataMappers\Album::get_instance()->find($album_id);
            if ($album && !empty($album->albumdesc)) {
                $description = $album->albumdesc;
            }
        }
        return $description;
    }
}