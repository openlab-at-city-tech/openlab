<?php
class A_Gallery_Display_Factory extends Mixin
{
    function displayed_gallery($properties = array(), $mapper = false, $context = false)
    {
        return new C_Displayed_Gallery($properties, $mapper, $context);
    }
    function display_type($properties = array(), $mapper = false, $context = false)
    {
        return new \C_Display_Type($properties, $mapper, $context);
    }
}
/**
 * A Display Type is a component which renders a collection of images
 * in a "gallery".
 *
 * Properties:
 * - entity_types (gallery, album)
 * - name        (nextgen_basic-thumbnails)
 * - title       (NextGEN Basic Thumbnails)
 * - aliases    [basic_thumbnail, basic_thumbnails]
 *
 * @mixin Mixin_Display_Type_Instance_Methods
 * @implements I_Display_Type
 */
class C_Display_Type extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Display_Type_Mapper';
    var $__settings = array();
    public function define($properties = array(), $mapper = false, $context = false)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_Display_Type_Instance_Methods');
        $this->implement('I_Display_Type');
    }
    /**
     * Initializes a display type with properties
     *
     * @param array|stdClass|C_Display_Type $properties
     * @param FALSE|C_Display_Type_Mapper   $mapper
     * @param FALSE|string|array            $context
     */
    public function initialize($properties = array(), $mapper = false, $context = false)
    {
        // If no mapper was specified, then get the mapper.
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        // Construct the model.
        parent::initialize($mapper, $properties);
    }
    /**
     * Allows a setting to be retrieved directly, rather than through the
     * settings property
     *
     * @param string $property
     * @return mixed
     */
    public function &__get($property)
    {
        if ($property == 'settings') {
            if (isset($this->_stdObject->settings)) {
                // $this->__settings = array_merge($this->_stdObject->settings, $this->__settings);
            }
            return $this->_stdObject->settings;
        }
        if (isset($this->_stdObject->settings[$property]) && $this->_stdObject->settings[$property] != null) {
            return $this->_stdObject->settings[$property];
        } else {
            return parent::__get($property);
        }
    }
    public function &__set($property, $value)
    {
        if ($property == 'settings') {
            $retval = $this->_stdObject->settings = $value;
        } else {
            $retval = $this->_stdObject->settings[$property] = $value;
        }
        return $retval;
    }
    public function __isset($property_name)
    {
        if ($property_name == 'settings') {
            return isset($this->_stdObject->settings);
        }
        return isset($this->_stdObject->settings[$property_name]) || parent::__isset($property_name);
    }
}
/**
 * Provides methods available for class instances
 */
class Mixin_Display_Type_Instance_Methods extends Mixin
{
    /**
     * Determines if this display type is compatible with a displayed gallery source
     *
     * @param stdClass $source
     * @return bool
     */
    public function is_compatible_with_source($source)
    {
        return C_Displayed_Gallery_Source_Manager::get_instance()->is_compatible($source, $this);
    }
    public function get_order()
    {
        return NGG_DISPLAY_PRIORITY_BASE;
    }
    public function validation()
    {
        $this->object->validates_presence_of('entity_types');
        $this->object->validates_presence_of('name');
        $this->object->validates_presence_of('title');
        return $this->object->is_valid();
    }
}
/**
 * @mixin Mixin_Display_Type_Controller
 * @property \Imagely\NGG\DisplayType\Controller $parent_controller_wrapper
 */
class C_Display_Type_Controller extends C_MVC_Controller
{
    protected static $_instances = array();
    public $parent_controller_wrapper;
    function define($context = false)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Display_Type_Controller');
        $this->implement('I_Display_Type_Controller');
        $this->object->parent_controller_wrapper = new \Imagely\NGG\DisplayType\Controller();
    }
    /**
     * @param string|bool $context
     * @return C_Display_Type_Controller
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Display_Type_Controller($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * @property C_Display_Type_Controller $object
 */
class Mixin_Display_Type_Controller extends Mixin
{
    function enqueue_displayed_gallery_trigger_buttons_resources($displayed_gallery = false)
    {
        return $this->object->parent_controller_wrapper->enqueue_displayed_gallery_trigger_buttons_resources($displayed_gallery);
    }
    function enqueue_lightbox_resources(C_Displayed_Gallery $displayed_gallery)
    {
        $this->object->parent_controller_wrapper->enqueue_lightbox_resources($displayed_gallery);
    }
    function is_cachable()
    {
        return $this->object->parent_controller_wrapper->is_cachable();
    }
    function enqueue_frontend_resources($displayed_gallery, $use_parent_controller = true)
    {
        if ($use_parent_controller) {
            $this->object->parent_controller_wrapper->enqueue_frontend_resources($displayed_gallery);
        }
    }
    /**
     * This function does nothing but remains for compatibility with NextGEN Pro which may invoke it.
     *
     * @TODO: Remove this method once Pro's minimum NGG version has been updated.
     */
    public function enqueue_ngg_styles()
    {
    }
    /**
     * @param null|array $params
     * @return array|null
     */
    function prepare_display_parameters($displayed_gallery, $params = null)
    {
        return $this->object->parent_controller_wrapper->prepare_display_parameters($displayed_gallery, $params);
    }
    /**
     * @return string
     */
    function index_action($displayed_gallery, $return = false)
    {
        return '';
    }
    /**
     * @return string
     */
    function get_effect_code($displayed_gallery)
    {
        return $this->object->parent_controller_wrapper->get_effect_code($displayed_gallery, false);
    }
    /**
     * @TODO Remove this when Pro no longer requires it
     * @deprecated
     * @param string $handle
     * @param string $object_name
     * @param mixed  $object_value
     * @param bool   $define
     * @param bool   $override
     * @return bool
     */
    public function _add_script_data($handle, $object_name, $object_value, $define = true, $override = false)
    {
        return \Imagely\NGG\Display\DisplayManager::add_script_data($handle, $object_name, $object_value, $define, $override);
    }
    function get_entity_statistics($entities, $named_size, $style_images = false)
    {
        return $this->object->parent_controller_wrapper->get_entity_statistics($entities, $named_size, $style_images);
    }
    function create_view($template, $params = array(), $context = null)
    {
        if (isset($params['displayed_gallery'])) {
            if (isset($params['displayed_gallery']->display_settings)) {
                $template = $this->get_display_type_view_abspath($template, $params);
            }
        }
        return $this->call_parent('create_view', $template, $params, $context);
    }
    /**
     * @param string $template
     * @param array  $params
     * @return string $template
     */
    function get_display_type_view_abspath($template, $params)
    {
        return $this->object->parent_controller_wrapper->get_display_type_view_abspath($template, $params);
    }
    /**
     * @param C_Displayed_Gallery $displayed_gallery
     * @return C_Displayed_Gallery mixed
     */
    function get_alternate_displayed_gallery($displayed_gallery)
    {
        return $this->object->parent_controller_wrapper->get_alternative_displayed_gallery($displayed_gallery);
    }
    function prepare_legacy_parameters($images, $displayed_gallery, $params = array())
    {
        return $this->object->parent_controller_wrapper->prepare_legacy_parameters($images, $displayed_gallery, $params);
    }
}
/**
 * Provides a datamapper to perform CRUD operations for Display Types
 *
 * @mixin Mixin_Display_Type_Mapper
 * @implements I_Display_Type_Mapper
 */
class C_Display_Type_Mapper extends C_CustomPost_DataMapper_Driver
{
    public static $_instances = array();
    public function define($context = false, $not_used = false)
    {
        $object_name = 'display_type';
        // Add the object name to the context of the object as well
        // This allows us to adapt the driver itself, if required.
        if (!is_array($context)) {
            $context = [$context];
        }
        array_push($context, $object_name);
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_Display_Type_Mapper');
        $this->implement('I_Display_Type_Mapper');
        $this->set_model_factory_method($object_name);
        // Define columns.
        $this->define_column('ID', 'BIGINT', 0);
        $this->define_column('name', 'VARCHAR(255)');
        $this->define_column('title', 'VARCHAR(255)');
        $this->define_column('preview_image_relpath', 'VARCHAR(255)');
        $this->define_column('default_source', 'VARCHAR(255)');
        $this->define_column('view_order', 'BIGINT', NGG_DISPLAY_PRIORITY_BASE);
        $this->add_serialized_column('settings');
        $this->add_serialized_column('entity_types');
    }
    public function initialize($context = false)
    {
        parent::initialize();
    }
    /**
     * Gets a singleton of the mapper
     *
     * @param string|bool $context
     * @return C_Display_Type_Mapper
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Display_Type_Mapper($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Locates a Display Type by names
     *
     * @param string $name
     * @param bool   $model
     * @return null|object
     */
    public function find_by_name($name, $model = false)
    {
        $retval = null;
        $this->object->select();
        $this->object->where(['name = %s', $name]);
        $results = $this->object->run_query(false, $model);
        if (!$results) {
            foreach ($this->object->find_all(false, $model) as $entity) {
                if ($entity->name == $name || isset($entity->aliases) && is_array($entity->aliases) && in_array($name, $entity->aliases)) {
                    $retval = $entity;
                    break;
                }
            }
        } else {
            $retval = $results[0];
        }
        return $retval;
    }
    /**
     * Finds display types used to display specific types of entities
     *
     * @param string|array $entity_type e.g. image, gallery, album
     * @param bool         $model (optional)
     * @return array
     */
    public function find_by_entity_type($entity_type, $model = false)
    {
        $find_entity_types = is_array($entity_type) ? $entity_type : [$entity_type];
        $retval = null;
        foreach ($this->object->find_all(false, $model) as $display_type) {
            foreach ($find_entity_types as $entity_type) {
                if (isset($display_type->entity_types) && in_array($entity_type, $display_type->entity_types)) {
                    $retval[] = $display_type;
                    break;
                }
            }
        }
        return $retval;
    }
}
/**
 * Provides instance methods for the display type mapper
 */
class Mixin_Display_Type_Mapper extends Mixin
{
    /**
     * Uses the title attribute as the post title
     *
     * @param stdClass $entity
     * @return string
     */
    public function get_post_title($entity)
    {
        return $entity->title;
    }
    /**
     * Sets default values needed for display types
     *
     * @param object $entity (optional)
     */
    public function set_defaults($entity)
    {
        if (!isset($entity->settings)) {
            $entity->settings = [];
        }
        $this->_set_default_value($entity, 'preview_image_relpath', '');
        $this->_set_default_value($entity, 'default_source', '');
        $this->_set_default_value($entity, 'view_order', NGG_DISPLAY_PRIORITY_BASE);
        $this->_set_default_value($entity, 'settings', 'use_lightbox_effect', true);
        $this->_set_default_value($entity, 'hidden_from_ui', false);
        // todo remove later.
        $this->_set_default_value($entity, 'hidden_from_igw', false);
        $this->_set_default_value($entity, 'aliases', []);
        return $this->call_parent('set_defaults', $entity);
    }
}
/**
 * Associates a Display Type with a collection of images
 *
 * * Properties:
 * - source             (gallery, album, recent_images, random_images, etc)
 * - container_ids      (gallery ids, album ids, tag ids, etc)
 * - display_type       (name of the display type being used)
 * - display_settings   (settings for the display type)
 * - exclusions         (excluded entity ids)
 * - entity_ids         (specific images/galleries to include, sorted)
 * - order_by
 * - order_direction
 *
 * @mixin Mixin_Displayed_Gallery_Instance_Methods
 * @implements I_Displayed_Gallery
 */
class C_Displayed_Gallery extends C_DataMapper_Model
{
    public $_mapper_interface = 'I_Displayed_Gallery_Mapper';
    // The "alternative" approach to using "ORDER BY RAND()" works by finding X image PID in a kind of shotgun-blast
    // like scattering in a second query made via $wpdb that is then fed into the query built by _get_image_entities().
    // This variable is used to cache the results of that inner quasi-random PID retrieval so that multiple calls
    // to $displayed_gallery->get_entities() don't return different results for each invocation. This is important
    // for NextGen Pro's galleria module in order to 'localize' the results of get_entities() to JSON.
    protected static $_random_image_ids_cache = array();
    public function define($properties = array(), $mapper = false, $context = false)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_Displayed_Gallery_Instance_Methods');
        $this->implement('I_Displayed_Gallery');
    }
    /**
     * Initializes a display type with properties
     *
     * @param array|stdClass|C_Displayed_Gallery $properties
     * @param FALSE|C_Displayed_Gallery_Mapper   $mapper
     * @param FALSE|string|array                 $context
     */
    public function initialize($properties = array(), $mapper = false, $context = false)
    {
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        parent::initialize($mapper, $properties);
    }
    public function get_entities($limit = false, $offset = false, $id_only = false, $returns = 'included')
    {
        $retval = [];
        $source_obj = $this->object->get_source();
        $max = $this->object->get_maximum_entity_count();
        if (!$limit || is_numeric($limit) && $limit > $max) {
            $limit = $max;
        }
        // Ensure that all parameters have values that are expected.
        if ($this->object->_parse_parameters()) {
            // Is this an image query?
            if (in_array('image', $source_obj->returns)) {
                $retval = $this->object->_get_image_entities($source_obj, $limit, $offset, $id_only, $returns);
            } elseif (in_array('gallery', $source_obj->returns)) {
                $retval = $this->object->_get_album_and_gallery_entities($source_obj, $limit, $offset, $id_only, $returns);
            }
        }
        return $retval;
    }
    /**
     * Gets all images in the displayed gallery
     *
     * @param stdClass $source_obj
     * @param int      $limit
     * @param int      $offset
     * @param boolean  $id_only
     * @param string   $returns
     */
    public function _get_image_entities($source_obj, $limit, $offset, $id_only, $returns)
    {
        // TODO: This method is very long, and therefore more difficult to read. Find a way to reduce in length or segment.
        $settings = \Imagely\NGG\Settings\Settings::get_instance();
        $mapper = \Imagely\NGG\DataMappers\Image::get_instance();
        $image_key = $mapper->get_primary_key_column();
        $select = $id_only ? $image_key : $mapper->get_table_name() . '.*';
        if (strtoupper($this->object->order_direction) == 'DSC') {
            $this->object->order_direction = 'DESC';
        }
        $sort_direction = in_array(strtoupper($this->object->order_direction), ['ASC', 'DESC']) ? $this->object->order_direction : $settings->galSortDir;
        $sort_by = in_array(strtolower($this->object->order_by), array_merge($mapper->get_column_names(), ['rand()'])) ? $this->object->order_by : $settings->get('galSort');
        // Quickly sanitize.
        global $wpdb;
        $this->object->container_ids = $this->object->container_ids ? array_map([$wpdb, '_escape'], $this->object->container_ids) : [];
        $this->object->entity_ids = $this->object->entity_ids ? array_map([$wpdb, '_escape'], $this->object->entity_ids) : [];
        $this->object->exclusions = $this->object->exclusions ? array_map([$wpdb, '_escape'], $this->object->exclusions) : [];
        // Here's what this method is doing:
        // 1) Determines what results need returned
        // 2) Determines from what container ids the results should come from
        // 3) Applies ORDER BY clause
        // 4) Applies LIMIT/OFFSET clause
        // 5) Executes the query and returns the result.
        // We start with the most difficult query. When returns is "both", we
        // need to return a list of both included and excluded entity ids, and
        // mark specifically which entities are excluded.
        if ($returns == 'both') {
            // We need to add two dynamic columns, one called "sortorder" and
            // the other called "exclude".
            $if_true = 1;
            $if_false = 0;
            $excluded_set = $this->object->entity_ids;
            if (!$excluded_set) {
                $if_true = 0;
                $if_false = 1;
                $excluded_set = $this->object->exclusions;
            }
            $sortorder_set = $this->object->sortorder ? $this->object->sortorder : $excluded_set;
            // Add sortorder column.
            if ($sortorder_set) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $sortorder_set, 'new_sortorder', true);
                // A user might want to sort the results by the order of
                // images that they specified to be included. For that,
                // we need some trickery by reversing the order direction.
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            // Add exclude column.
            if ($excluded_set) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $excluded_set, 'exclude');
                $select .= ", IF (exclude = 0 AND @exclude = 0, {$if_true}, {$if_false}) AS 'exclude'";
            }
            // Select what we want.
            $mapper->select($select);
        }
        // When returns is "included", the query is relatively simple. We
        // just provide a where clause to limit how many images we're returning
        // based on the entity_ids, exclusions, and container_ids parameters.
        if ($returns == 'included') {
            // If the sortorder propery is available, then we need to override
            // the sortorder.
            if ($this->object->sortorder) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $this->object->sortorder, 'new_sortorder', true);
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            $mapper->select($select);
            // Filter based on entity_ids selection.
            if ($this->object->entity_ids) {
                $mapper->where(["{$image_key} IN %s", $this->object->entity_ids]);
            }
            // Filter based on exclusions selection.
            if ($this->object->exclusions) {
                $mapper->where(["{$image_key} NOT IN %s", $this->object->exclusions]);
            }
            // Ensure that no images marked as excluded at the gallery level are returned.
            if (empty($this->object->skip_excluding_globally_excluded_images)) {
                $mapper->where(['exclude = %d', 0]);
            }
        } elseif ($returns == 'excluded') {
            // If the sortorder propery is available, then we need to override
            // the sortorder.
            if ($this->object->sortorder) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $this->object->sortorder, 'new_sortorder', true);
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            // Mark each result as excluded.
            $select .= ', 1 AS exclude';
            $mapper->select($select);
            // Is this case, entity_ids become the exclusions.
            $exclusions = $this->object->entity_ids;
            // Remove the exclusions always takes precedence over entity_ids, so
            // we adjust the list of ids.
            if ($this->object->exclusions) {
                foreach ($this->object->exclusions as $excluded_entity_id) {
                    if (($index = array_search($excluded_entity_id, $exclusions)) !== false) {
                        unset($exclusions[$index]);
                    }
                }
            }
            // Filter based on exclusions selection.
            if ($exclusions) {
                $mapper->where(["{$image_key} NOT IN %s", $exclusions]);
            } elseif ($this->object->exclusions) {
                $mapper->where(["{$image_key} IN %s", $this->object->exclusions]);
            }
            // Ensure that images marked as excluded are returned as well.
            $mapper->where(['exclude = 1']);
        }
        // Filter based on containers_ids. Container ids is a little more
        // complicated as it can contain gallery ids or tags.
        if ($this->object->container_ids) {
            // Container ids are tags.
            if ($source_obj->name == 'tags') {
                $term_ids = $this->object->get_term_ids_for_tags($this->object->container_ids);
                $mapper->where(["{$image_key} IN %s", get_objects_in_term($term_ids, 'ngg_tag')]);
            } else {
                $mapper->where(['galleryid IN %s', $this->object->container_ids]);
            }
        }
        // Filter based on excluded container ids.
        if ($this->object->excluded_container_ids) {
            // Container ids are tags.
            if ($source_obj->name == 'tags') {
                $term_ids = $this->object->get_term_ids_for_tags($this->object->excluded_container_ids);
                $mapper->where(["{$image_key} NOT IN %s", get_objects_in_term($term_ids, 'ngg_tag')]);
            } else {
                // Container ids are gallery ids.
                $mapper->where(['galleryid NOT IN %s', $this->object->excluded_container_ids]);
            }
        }
        // Adjust the query more based on what source was selected.
        if (in_array($this->object->source, ['recent', 'recent_images'])) {
            $sort_direction = 'DESC';
            $sort_by = apply_filters('ngg_recent_images_sort_by_column', 'imagedate');
        } elseif ($this->object->source == 'random_images' && empty($this->object->entity_ids)) {
            // A gallery with source=random and a non-empty entity_ids is treated as being source=images & image_ids=(entity_ids)
            // In this case however source is random but no image ID are pre-filled.
            //
            // Here we must transform our query from "SELECT * FROM ngg_pictures WHERE gallery_id = X" into something
            // like "SELECT * FROM ngg_pictures WHERE pid IN (SELECT pid FROM ngg_pictures WHERE gallery_id = X ORDER BY RAND())".
            $table_name = $mapper->get_table_name();
            $where_clauses = [];
            $old_where_sql = '';
            // $this->get_entities_count() works by calling count(get_entities()) which means that for random galleries
            // there will be no limit passed to this method -- adjust the $limit now based on the maximum_entity_count.
            $max = $this->object->get_maximum_entity_count();
            if (!$limit || is_numeric($limit) && $limit > $max) {
                $limit = $max;
            }
            foreach ($mapper->_where_clauses as $where) {
                $where_clauses[] = '(' . $where . ')';
            }
            if ($where_clauses) {
                $old_where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
            }
            $noExtras = '/*NGG_NO_EXTRAS_TABLE*/';
            // TODO: remove this constant. It was only introduced for a short period of time before the setting was
            // TODO: added to Other Options > Misc to allow users easier configuration.
            if (\Imagely\NGG\Settings\Settings::get_instance()->get('use_alternate_random_method') || defined('NGG_DISABLE_ORDER_BY_RAND') && NGG_DISABLE_ORDER_BY_RAND) {
                // Check if the random image PID have been cached and use them (again) if already found.
                $id = $this->object->ID();
                if (!empty(self::$_random_image_ids_cache[$id])) {
                    $image_ids = self::$_random_image_ids_cache[$id];
                } else {
                    global $wpdb;
                    // Prevent infinite loops: retrieve the image count and if needed just pull in every image available.
                    // PHP-CS flags this but it is a false positive, the $old_where_sql is an already prepared SQL string.
                    //
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $total = $wpdb->get_var("SELECT COUNT(`pid`) FROM {$wpdb->nggpictures} {$old_where_sql}");
                    $image_ids = [];
                    if ($total <= $limit) {
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        $image_ids = $wpdb->get_col("SELECT `pictures`.`pid` FROM {$wpdb->nggpictures} `pictures` {$old_where_sql} LIMIT {$total}");
                    } else {
                        // Start retrieving random ID from the DB and hope they exist; continue looping until our count is full.
                        $segments = ceil($limit / 4);
                        while (count($image_ids) < $limit) {
                            $newID = $this->_query_random_ids_for_cache($segments, $old_where_sql);
                            $image_ids = array_merge(array_unique($image_ids), $newID);
                        }
                    }
                    // Prevent overflow.
                    if (count($image_ids) > $limit) {
                        array_splice($image_ids, $limit);
                    }
                    // Give things an extra shake.
                    shuffle($image_ids);
                    // Cache these ID in memory so that any attempts to call get_entities() more than once will result
                    // in the same images being retrieved for the duration of that page execution.
                    self::$_random_image_ids_cache[$id] = $image_ids;
                }
                $image_ids = implode(',', $image_ids);
                // Replace the existing WHERE clause with one where aready retrieved "random" PID are included.
                $mapper->_where_clauses = [" {$noExtras} `{$image_key}` IN ({$image_ids}) {$noExtras}"];
            } else {
                // Replace the existing WHERE clause with one that selects from a sub-query that is randomly ordered.
                $sub_where = "SELECT `{$image_key}` FROM `{$table_name}` i {$old_where_sql} ORDER BY RAND() LIMIT {$limit}";
                $mapper->_where_clauses = [" {$noExtras} `{$image_key}` IN (SELECT `{$image_key}` FROM ({$sub_where}) o) {$noExtras}"];
            }
        }
        // Apply a sorting order.
        if ($sort_by) {
            $mapper->order_by($sort_by, $sort_direction);
        }
        // Apply a limit.
        if ($limit) {
            if ($offset) {
                $mapper->limit($limit, $offset);
            } else {
                $mapper->limit($limit);
            }
        }
        $results = $mapper->run_query();
        if (!is_admin() && in_array('image', $source_obj->returns)) {
            foreach ($results as $entity) {
                if (!empty($entity->description)) {
                    $entity->description = \Imagely\NGG\Display\I18N::translate($entity->description, 'pic_' . $entity->pid . '_description');
                }
                if (!empty($entity->alttext)) {
                    $entity->alttext = \Imagely\NGG\Display\I18N::translate($entity->alttext, 'pic_' . $entity->pid . '_alttext');
                }
            }
        }
        return $results;
    }
    /**
     * @param int    $limit
     * @param string $where_sql Must be the full "WHERE x=y" string
     * @return int[]
     */
    public function _query_random_ids_for_cache($limit = 10, $where_sql = '')
    {
        global $wpdb;
        $mod = rand(3, 9);
        if (empty($where_sql)) {
            $where_sql = 'WHERE 1=1';
        }
        // The following query uses $where_sql which is an already prepared clause generated by the DataMapper
        //
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->get_col("SELECT `pictures`.`pid` from {$wpdb->nggpictures} `pictures`\n                    JOIN (SELECT CEIL(MAX(`pid`) * RAND()) AS `pid` FROM {$wpdb->nggpictures}) AS `x` ON `pictures`.`pid` >= `x`.`pid`\n                    {$where_sql}\n                    AND `pictures`.`pid` MOD {$mod} = 0\n                    LIMIT {$limit}");
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }
    /**
     * Gets all gallery and album entities from albums specified, if any
     *
     * @param stdClass $source_obj
     * @param int      $limit
     * @param int      $offset
     * @param boolean  $id_only
     * @param array    $returns
     */
    public function _get_album_and_gallery_entities($source_obj, $limit = false, $offset = false, $id_only = false, $returns = 'included')
    {
        // Albums queries and difficult and inefficient to perform due to the
        // database schema. To complicate things, we're returning two different
        // types of entities - galleries, and sub-albums.
        // The user prefixes entity_id's with an 'a' to distinguish album ids
        // from gallery ids. E.g. entity_ids=[1, "a2", 3].
        $album_mapper = \Imagely\NGG\DataMappers\Album::get_instance();
        $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
        $album_key = $album_mapper->get_primary_key_column();
        $gallery_key = $gallery_mapper->get_primary_key_column();
        $select = $id_only ? $album_key . ', sortorder' : $album_mapper->get_table_name() . '.*';
        $retval = [];
        // If no exclusions are specified, are entity_ids are specified,
        // and we're to return is "included", then we have a relatively easy
        // query to perform - we just fetch each entity listed in
        // the entity_ids field.
        if ($returns == 'included' && $this->object->entity_ids && empty($this->object->exclusions)) {
            $retval = $this->object->_entities_to_galleries_and_albums($this->object->entity_ids, $id_only, [], $limit, $offset);
        } else {
            // Start the query.
            $album_mapper->select($select);
            // Fetch the albums, and find the entity ids of the sub-albums and galleries.
            $entity_ids = [];
            $excluded_ids = [];
            // Filter by container ids. If container_ids === '0' we retrieve all existing gallery_ids and use
            // them as the available entity_ids for comparability with 1.9x.
            $container_ids = $this->object->container_ids;
            if ($container_ids) {
                if ($container_ids !== ['0'] && $container_ids !== ['']) {
                    $container_ids = array_map('intval', $container_ids);
                    $album_mapper->where(["{$album_key} IN %s", $container_ids]);
                    // This order_by is necessary for albums to be ordered correctly given the WHERE .. IN() above.
                    $order_string = implode(',', $container_ids);
                    $album_mapper->order_by("FIELD('id', {$order_string})");
                    foreach ($album_mapper->run_query() as $album) {
                        $entity_ids = array_merge($entity_ids, (array) $album->sortorder);
                    }
                } elseif ($container_ids === ['0'] || $container_ids === ['']) {
                    foreach ($gallery_mapper->select($gallery_key)->run_query() as $gallery) {
                        $entity_ids[] = $gallery->{$gallery_key};
                    }
                }
            }
            // Break the list of entities into two groups, included entities
            // and excluded entity ids
            // --
            // If a specific list of entity ids have been specified, then
            // we know what entity ids are meant to be included. We can compute
            // the intersect and also determine what entity ids are to be
            // excluded.
            if ($this->object->entity_ids) {
                // Determine the real list of included entity ids. Exclusions
                // always take precedence.
                $included_ids = $this->object->entity_ids;
                foreach ($this->object->exclusions as $excluded_id) {
                    if (($index = array_search($excluded_id, $included_ids)) !== false) {
                        unset($included_ids[$index]);
                    }
                }
                $excluded_ids = array_diff($entity_ids, $included_ids);
            } elseif ($this->object->exclusions) {
                $included_ids = array_diff($entity_ids, $this->object->exclusions);
                $excluded_ids = array_diff($entity_ids, $included_ids);
            } else {
                $included_ids = $entity_ids;
            }
            // We've built our two groups. Let's determine how we'll focus on them
            // --
            // We're interested in only the included ids.
            if ($returns == 'included') {
                $retval = $this->object->_entities_to_galleries_and_albums($included_ids, $id_only, [], $limit, $offset);
            } elseif ($returns == 'excluded') {
                $retval = $this->object->_entities_to_galleries_and_albums($excluded_ids, $id_only, $excluded_ids, $limit, $offset);
            } else {
                $retval = $this->object->_entities_to_galleries_and_albums($entity_ids, $id_only, $excluded_ids, $limit, $offset);
            }
        }
        return $retval;
    }
    /**
     * Takes a list of entities, and returns the mapped galleries and sub-albums
     *
     * @param array $entity_ids
     * @param bool  $id_only
     * @param array $exclusions
     * @param int   $limit
     * @param int   $offset
     * @return array
     */
    public function _entities_to_galleries_and_albums($entity_ids, $id_only = false, $exclusions = array(), $limit = false, $offset = false)
    {
        $retval = [];
        $gallery_ids = [];
        $album_ids = [];
        $album_mapper = \Imagely\NGG\DataMappers\Album::get_instance();
        $gallery_mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
        $image_mapper = \Imagely\NGG\DataMappers\Image::get_instance();
        $album_key = $album_mapper->get_primary_key_column();
        $gallery_key = $gallery_mapper->get_primary_key_column();
        $album_select = ($id_only ? $album_key : $album_mapper->get_table_name() . '.*') . ', 1 AS is_album, 0 AS is_gallery, name AS title, albumdesc AS galdesc';
        $gallery_select = ($id_only ? $gallery_key : $gallery_mapper->get_table_name() . '.*') . ', 1 AS is_gallery, 0 AS is_album';
        // Modify the sort order of the entities.
        if ($this->object->sortorder) {
            $sortorder = array_intersect($this->object->sortorder, $entity_ids);
            $entity_ids = array_merge($sortorder, array_diff($entity_ids, $sortorder));
        }
        // Segment entity ids into two groups - galleries and albums.
        foreach ($entity_ids as $entity_id) {
            if (substr($entity_id, 0, 1) == 'a') {
                $album_ids[] = intval(substr($entity_id, 1));
            } else {
                $gallery_ids[] = intval($entity_id);
            }
        }
        // Adjust query to include an exclude property.
        if ($exclusions) {
            $album_select = $this->object->_add_find_in_set_column($album_select, $album_key, $this->object->exclusions, 'exclude');
            $album_select = $this->object->_add_if_column($album_select, 'exclude', 0, 1);
            $gallery_select = $this->object->_add_find_in_set_column($gallery_select, $gallery_key, $this->object->exclusions, 'exclude');
            $gallery_select = $this->object->_add_if_column($gallery_select, 'exclude', 0, 1);
        }
        // Add sorting parameter to the gallery and album queries.
        if ($gallery_ids) {
            $gallery_select = $this->object->_add_find_in_set_column($gallery_select, $gallery_key, $gallery_ids, 'ordered_by', true);
        } else {
            $gallery_select .= ', 0 AS ordered_by';
        }
        if ($album_ids) {
            $album_select = $this->object->_add_find_in_set_column($album_select, $album_key, $album_ids, 'ordered_by', true);
        } else {
            $album_select .= ', 0 AS ordered_by';
        }
        // Fetch entities.
        $galleries = $gallery_mapper->select($gallery_select)->where(["{$gallery_key} IN %s", $gallery_ids])->order_by('ordered_by', 'DESC')->run_query();
        $counts = $image_mapper->select('galleryid, COUNT(*) as counter')->where([['galleryid IN %s', $gallery_ids], ['exclude = %d', 0]])->group_by('galleryid')->run_query(false, false, true);
        $albums = $album_mapper->select($album_select)->where(["{$album_key} IN %s", $album_ids])->order_by('ordered_by', 'DESC')->run_query();
        // Reorder entities according to order specified in entity_ids.
        foreach ($entity_ids as $entity_id) {
            if (substr($entity_id, 0, 1) == 'a') {
                $album = array_shift($albums);
                if ($album) {
                    $retval[] = $album;
                }
            } else {
                $gallery = array_shift($galleries);
                if ($gallery) {
                    foreach ($counts as $id => $gal_count) {
                        if ($gal_count->galleryid == $gallery->gid) {
                            $gallery->counter = intval($gal_count->counter);
                            unset($counts[$id]);
                        }
                    }
                    $retval[] = $gallery;
                }
            }
        }
        // Sort the entities.
        if ($this->object->order_by && $this->object->order_by != 'sortorder') {
            usort($retval, [&$this, '_sort_album_result']);
        }
        if ($this->object->order_direction == 'DESC') {
            $retval = array_reverse($retval);
        }
        // Limit the entities.
        if ($limit) {
            $retval = array_slice($retval, $offset, $limit);
        }
        return $retval;
    }
    /**
     * Returns the total number of entities in this displayed gallery
     *
     * @param string $returns
     * @return int
     */
    public function get_entity_count($returns = 'included')
    {
        $retval = 0;
        // !!!TODO: don't use count() here on the PHP end.
        // Is this an image query?
        $source_obj = $this->object->get_source();
        if (in_array('image', $source_obj->returns)) {
            $retval = count($this->object->_get_image_entities($source_obj, false, false, true, $returns));
        } elseif (in_array('gallery', $source_obj->returns)) {
            $retval = count($this->object->_get_album_and_gallery_entities($source_obj, false, false, true, $returns));
        }
        $max = $this->get_maximum_entity_count();
        if ($retval > $max) {
            $retval = $max;
        }
        return $retval;
    }
    // Honor the gallery 'maximum_entity_count' setting ONLY when dealing with random & recent galleries. All
    // others will always obey the *global* 'maximum_entity_count' setting.
    public function get_maximum_entity_count()
    {
        $max = intval(\Imagely\NGG\Settings\Settings::get_instance()->get('maximum_entity_count', 500));
        $sources = C_Displayed_Gallery_Source_Manager::get_instance();
        $source_obj = $this->object->get_source();
        if (in_array($source_obj, [$sources->get('random'), $sources->get('random_images'), $sources->get('recent'), $sources->get('recent_images')])) {
            $max = intval($this->object->maximum_entity_count);
        }
        return $max;
    }
    /**
     * Returns all included entities for the displayed gallery
     *
     * @param int     $limit
     * @param int     $offset
     * @param boolean $id_only
     * @return array
     */
    public function get_included_entities($limit = false, $offset = false, $id_only = false)
    {
        return $this->object->get_entities($limit, $offset, $id_only, 'included');
    }
    /**
     * Adds a FIND_IN_SET call to the select portion of the query, and
     * optionally defines a dynamic column
     *
     * @param string  $select
     * @param string  $key
     * @param array   $array
     * @param string  $alias
     * @param boolean $add_column
     * @return string
     */
    public function _add_find_in_set_column($select, $key, $array, $alias, $add_column = false)
    {
        $array = array_map('intval', $array);
        $set = implode(',', array_reverse($array));
        if (!$select) {
            $select = '1';
        }
        $select .= ", @{$alias} := FIND_IN_SET({$key}, '{$set}')";
        if ($add_column) {
            $select .= " AS {$alias}";
        }
        return $select;
    }
    public function _add_if_column($select, $alias, $true = 1, $false = 0)
    {
        if (!$select) {
            $select = '1';
        }
        $select .= ", IF(@{$alias} = 0, {$true}, {$false}) AS {$alias}";
        return $select;
    }
    /**
     * Parses the list of parameters provided in the displayed gallery, and
     * ensures everything meets expectations
     *
     * @return boolean
     */
    public function _parse_parameters()
    {
        $valid = false;
        // Ensure that the source is valid.
        if (C_Displayed_Gallery_Source_Manager::get_instance()->get($this->object->source)) {
            $valid = true;
        }
        // Ensure that exclusions, entity_ids, and sortorder have valid elements.
        // IE likes to send empty array as an array with a single element that
        // has no value.
        if ($this->object->exclusions && !$this->object->exclusions[0]) {
            $this->object->exclusions = [];
        }
        if ($this->object->entity_ids && !$this->object->entity_ids[0]) {
            $this->object->entity_ids = [];
        }
        if ($this->object->sortorder && !$this->object->sortorder[0]) {
            $this->object->sortorder = [];
        }
        return $valid;
    }
    /**
     * Returns a list of term ids for the list of tags
     *
     * @global wpdb $wpdb
     * @param array $tags
     * @return array
     */
    public function get_term_ids_for_tags($tags = false)
    {
        global $wpdb;
        // If no tags were provided, get them from the container_ids.
        if (!$tags || !is_array($tags)) {
            $tags = $this->object->container_ids;
        }
        // Convert container ids to a string suitable for WHERE IN.
        $container_ids = [];
        if (is_array($tags) && !in_array('all', array_map('strtolower', $tags))) {
            foreach ($tags as $ndx => $container) {
                $container = esc_sql(str_replace('%', '%%', $container));
                $container_ids[] = "'{$container}'";
            }
            $container_ids = implode(',', $container_ids);
        }
        // Construct query.
        $query = "SELECT {$wpdb->term_taxonomy}.term_id FROM {$wpdb->term_taxonomy}\n                  INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id\n                  WHERE {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id\n                  AND {$wpdb->term_taxonomy}.taxonomy = %s";
        if (!empty($container_ids)) {
            $query .= " AND ({$wpdb->terms}.slug IN ({$container_ids}) OR {$wpdb->terms}.name IN ({$container_ids}))";
        }
        $query .= " ORDER BY {$wpdb->terms}.term_id";
        // This is a false positive
        //
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $query = $wpdb->prepare($query, 'ngg_tag');
        // Get all term_ids for each image tag slug.
        $term_ids = [];
        // This is a false positive
        //
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $results = $wpdb->get_results($query);
        if (is_array($results) && !empty($results)) {
            foreach ($results as $row) {
                $term_ids[] = $row->term_id;
            }
        }
        return $term_ids;
    }
    /**
     * Sorts the results of an album query
     *
     * @param stdClass $a
     * @param stdClass $b
     * @return int
     */
    public function _sort_album_result($a, $b)
    {
        $key = $this->object->order_by;
        if (!isset($a->{$key}) || !isset($b->{$key})) {
            return 0;
        }
        return strcmp($a->{$key}, $b->{$key});
    }
    /**
     * Gets the display type object used in this displayed gallery
     *
     * @return \Imagely\NGG\DataTypes\DisplayType
     */
    public function get_display_type()
    {
        return \Imagely\NGG\DataMappers\DisplayType::get_instance()->find_by_name($this->object->display_type);
    }
    /**
     * Gets albums queried in this displayed gallery
     *
     * @return array
     */
    public function get_albums()
    {
        $retval = [];
        if ($source = $this->object->get_source()) {
            if (in_array('album', $source->returns)) {
                $mapper = \Imagely\NGG\DataMappers\Album::get_instance();
                $album_key = $mapper->get_primary_key_column();
                if ($this->object->container_ids) {
                    $mapper->select()->where(["{$album_key} IN %s", $this->object->container_ids]);
                }
                $retval = $mapper->run_query();
            }
        }
        return $retval;
    }
    /**
     * Returns a transient for the displayed gallery
     *
     * @return string
     */
    public function to_transient()
    {
        $params = $this->object->get_entity();
        unset($params->transient_id);
        $key = \Imagely\NGG\Util\Transient::create_key('displayed_galleries', $params);
        if (is_null(\Imagely\NGG\Util\Transient::fetch($key, null))) {
            \Imagely\NGG\Util\Transient::update($key, $params, NGG_DISPLAYED_GALLERY_CACHE_TTL);
        }
        $this->object->transient_id = $key;
        if (!$this->object->id()) {
            $this->object->id($key);
        }
        return $key;
    }
    /**
     * Applies the values of a transient to this object
     *
     * @param string $transient_id
     * @return bool
     */
    public function apply_transient($transient_id = null)
    {
        $retval = false;
        if (!$transient_id && isset($this->object->transient_id)) {
            $transient_id = $this->object->transient_id;
        }
        if ($transient_id && ($transient = \Imagely\NGG\Util\Transient::fetch($transient_id, false))) {
            // Ensure that the transient is an object, not array.
            if (is_array($transient)) {
                $obj = new stdClass();
                foreach ($transient as $key => $value) {
                    $obj->{$key} = $value;
                }
                $transient = $obj;
            }
            $this->object->_stdObject = $transient;
            // Ensure that the display settings are an array.
            $this->object->display_settings = $this->_object_to_array($this->object->display_settings);
            // Ensure that we have the most accurate transient id.
            $this->object->transient_id = $transient_id;
            if (!$this->object->id()) {
                $this->object->id($transient_id);
            }
            $retval = true;
        } else {
            unset($this->object->transient_id);
            unset($this->object->_stdObject->transient_id);
            $this->object->to_transient();
        }
        return $retval;
    }
    public function _object_to_array($object)
    {
        $retval = $object;
        if (is_object($retval)) {
            $retval = get_object_vars($object);
        }
        if (is_array($retval)) {
            foreach ($retval as $key => $val) {
                if (is_object($val)) {
                    $retval[$key] = $this->_object_to_array($val);
                }
            }
        }
        return $retval;
    }
}
/**
 * Provides instance methods useful for working with the C_Displayed_Gallery model
 */
class Mixin_Displayed_Gallery_Instance_Methods extends Mixin
{
    public function validation()
    {
        // Valid sources.
        $this->object->validates_presence_of('source');
        // Valid display type?
        $this->object->validates_presence_of('display_type');
        if ($display_type = $this->object->get_display_type()) {
            foreach ($this->object->display_settings as $key => $val) {
                $display_type->settings[$key] = $val;
            }
            $this->object->display_settings = $display_type->settings;
            if (!$display_type->validate()) {
                foreach ($display_type->get_errors() as $property => $errors) {
                    foreach ($errors as $error) {
                        $this->object->add_error($error, $property);
                    }
                }
            }
            $this->object->display_type = $display_type->name;
            // Is the display type compatible with the source? E.g., if we're
            // using a display type that expects images, we can't be feeding it
            // galleries and albums.
            if ($source = $this->object->get_source()) {
                if (!$display_type->is_compatible_with_source($source)) {
                    $this->object->add_error(__('Source not compatible with selected display type', 'nggallery'), 'display_type');
                }
            }
            // Only some sources should have their own maximum_entity_count.
            if (!empty($this->object->display_settings['maximum_entity_count']) && in_array($this->object->source, ['tag', 'tags', 'random_images', 'recent_images', 'random', 'recent'])) {
                $this->object->maximum_entity_count = $this->object->display_settings['maximum_entity_count'];
            }
            // If no maximum_entity_count has been given, then set a maximum.
            if (!isset($this->object->maximum_entity_count)) {
                $settings = \Imagely\NGG\Settings\Settings::get_instance();
                $this->object->maximum_entity_count = $settings->get('maximum_entity_count', 500);
            }
        } else {
            $this->object->add_error('Invalid display type', 'display_type');
        }
        return $this->object->is_valid();
    }
    public function get_entity()
    {
        $entity = $this->call_parent('get_entity');
        unset($entity->post_author);
        unset($entity->post_date);
        unset($entity->post_date_gmt);
        unset($entity->post_title);
        unset($entity->post_excerpt);
        unset($entity->post_status);
        unset($entity->comment_status);
        unset($entity->ping_status);
        unset($entity->post_name);
        unset($entity->to_ping);
        unset($entity->pinged);
        unset($entity->post_modified);
        unset($entity->post_modified_gmt);
        unset($entity->post_parent);
        unset($entity->guid);
        unset($entity->post_type);
        unset($entity->post_mime_type);
        unset($entity->comment_count);
        unset($entity->filter);
        unset($entity->post_content_filtered);
        return $entity;
    }
    /**
     * Gets the corresponding source instance
     *
     * @return stdClass
     */
    public function get_source()
    {
        return C_Displayed_Gallery_Source_Manager::get_instance()->get($this->object->source);
    }
    /**
     * Returns the galleries queries in this displayed gallery
     *
     * @return array
     */
    public function get_galleries()
    {
        $retval = [];
        if ($source = $this->object->get_source()) {
            if (in_array('image', $source->returns)) {
                $mapper = \Imagely\NGG\DataMappers\Gallery::get_instance();
                $gallery_key = $mapper->get_primary_key_column();
                $mapper->select();
                if ($this->object->container_ids) {
                    $mapper->where(["{$gallery_key} IN %s", $this->object->container_ids]);
                }
                $retval = $mapper->run_query();
            }
        }
        return $retval;
    }
}
/**
 * Class C_Displayed_Gallery_Mapper
 *
 * @mixin Mixin_Displayed_Gallery_Defaults
 */
class C_Displayed_Gallery_Mapper extends C_CustomPost_DataMapper_Driver
{
    static $_instances = array();
    public function define($context = false, $not_used = false)
    {
        parent::define('displayed_gallery', [$context, 'displayed_gallery', 'display_gallery']);
        $this->add_mixin('Mixin_Displayed_Gallery_Defaults');
        $this->implement('I_Displayed_Gallery_Mapper');
        $this->set_model_factory_method('displayed_gallery');
        // $this->add_post_hook(
        // 'save',
        // 'Propagate thumbnail dimensions',
        // 'Hook_Propagate_Thumbnail_Dimensions_To_Settings'
        // );
    }
    /**
     * Initializes the mapper
     */
    public function initialize()
    {
        parent::initialize();
    }
    /**
     * Gets a singleton of the mapper
     *
     * @param string|bool $context
     * @return C_Displayed_Gallery_Mapper
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Displayed_Gallery_Mapper($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Gets a display type object for a particular entity
     *
     * @param stdClass|C_DataMapper_Model $entity
     * @return null|stdClass
     */
    public function get_display_type($entity)
    {
        $mapper = C_Display_Type_Mapper::get_instance();
        return $mapper->find_by_name($entity->display_type);
    }
}
/**
 * Adds default values for the displayed gallery
 */
class Mixin_Displayed_Gallery_Defaults extends Mixin
{
    /**
     * Sets defaults needed for the entity
     *
     * @param object $entity
     */
    public function set_defaults($entity)
    {
        // Ensure that we have a settings array.
        if (!isset($entity->display_settings)) {
            $entity->display_settings = [];
        }
        // If the display type is set, then get it's settings and apply them as
        // defaults to the "display_settings" of the displayed gallery.
        if (isset($entity->display_type)) {
            // Get display type mapper.
            if ($display_type = $this->object->get_display_type($entity)) {
                $entity->display_settings = $this->array_merge_assoc($display_type->settings, $entity->display_settings, true);
            }
        }
        // Default ordering.
        $settings = C_NextGen_Settings::get_instance();
        $this->object->_set_default_value($entity, 'order_by', $settings->galSort);
        $this->object->_set_default_value($entity, 'order_direction', $settings->galSortDir);
        // Ensure we have an exclusions array.
        $this->object->_set_default_value($entity, 'exclusions', []);
        // Ensure other properties exist.
        $this->object->_set_default_value($entity, 'container_ids', []);
        $this->object->_set_default_value($entity, 'excluded_container_ids', []);
        $this->object->_set_default_value($entity, 'sortorder', []);
        $this->object->_set_default_value($entity, 'entity_ids', []);
        $this->object->_set_default_value($entity, 'returns', 'included');
        // Set maximum_entity_count.
        $this->object->_set_default_value($entity, 'maximum_entity_count', $settings->maximum_entity_count);
    }
}
class C_Displayed_Gallery_Source_Manager
{
    private $_sources = array();
    private $_entity_types = array();
    private $_registered_defaults = array();
    /* @var C_Displayed_Gallery_Source_Manager */
    static $_instance = null;
    /**
     * @return C_Displayed_Gallery_Source_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_Displayed_Gallery_Source_Manager();
        }
        return self::$_instance;
    }
    public function register_defaults()
    {
        // Entity types must be registered first!!!
        $this->register_entity_type('gallery', 'galleries');
        $this->register_entity_type('image', 'images');
        $this->register_entity_type('album', 'albums');
        // Galleries.
        $galleries = new stdClass();
        $galleries->name = 'galleries';
        $galleries->title = __('Galleries', 'nggallery');
        $galleries->aliases = ['gallery', 'images', 'image'];
        $galleries->returns = ['image'];
        $this->register($galleries->name, $galleries);
        // Albums.
        $albums = new stdClass();
        $albums->name = 'albums';
        $albums->title = __('Albums', 'nggallery');
        $albums->aliases = ['album'];
        $albums->returns = ['album', 'gallery'];
        $this->register($albums->name, $albums);
        // Tags.
        $tags = new stdClass();
        $tags->name = 'tags';
        $tags->title = __('Tags', 'nggallery');
        $tags->aliases = ['tag', 'image_tags', 'image_tag'];
        $tags->returns = ['image'];
        $this->register($tags->name, $tags);
        // Random Images.
        $random = new stdClass();
        $random->name = 'random_images';
        $random->title = __('Random Images', 'nggallery');
        $random->aliases = ['random', 'random_image'];
        $random->returns = ['image'];
        $this->register($random->name, $random);
        // Recent Images.
        $recent = new stdClass();
        $recent->name = 'recent_images';
        $recent->title = __('Recent Images', 'nggallery');
        $recent->aliases = ['recent', 'recent_image'];
        $recent->returns = ['image'];
        $this->register($recent->name, $recent);
        $this->_registered_defaults = true;
    }
    public function register($name, $properties)
    {
        // We'll use an object to represent the source.
        $object = $properties;
        if (!is_object($properties)) {
            $object = new stdClass();
            foreach ($properties as $k => $v) {
                $object->{$k} = $v;
            }
        }
        // Set default properties.
        $object->name = $name;
        if (!isset($object->title)) {
            $object->title = $name;
        }
        if (!isset($object->returns)) {
            $object->returns = [];
        }
        if (!isset($object->aliases)) {
            $object->aliases = [];
        }
        // Add internal reference.
        $this->_sources[$name] = $object;
        foreach ($object->aliases as $name) {
            $this->_sources[$name] = $object;
        }
    }
    public function register_entity_type()
    {
        $aliases = func_get_args();
        $name = array_shift($aliases);
        $this->_entity_types[] = $name;
        foreach ($aliases as $alias) {
            $this->_entity_types[$alias] = $name;
        }
    }
    public function deregister($name)
    {
        if ($source = $this->get($name)) {
            unset($this->_sources[$name]);
            foreach ($source->aliases as $alias) {
                unset($this->_sources[$alias]);
            }
        }
    }
    public function deregister_entity_type($name)
    {
        unset($this->_entity_types[$name]);
    }
    public function get($name_or_alias)
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $retval = null;
        if (isset($this->_sources[$name_or_alias])) {
            $retval = $this->_sources[$name_or_alias];
        }
        return $retval;
    }
    public function get_entity_type($name)
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $found = array_search($name, $this->_entity_types);
        if ($found) {
            return $this->_entity_types[$found];
        } else {
            return null;
        }
    }
    public function get_all()
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $retval = [];
        foreach (array_values($this->_sources) as $source_obj) {
            if (!in_array($source_obj, $retval)) {
                $retval[] = $source_obj;
            }
        }
        usort($retval, [$this, '_sort_by_name']);
        return $retval;
    }
    public function _sort_by_name($a, $b)
    {
        return strcmp($a->name, $b->name);
    }
    public function get_all_entity_types()
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        return array_unique(array_values($this->_entity_types));
    }
    public function is_registered($name)
    {
        return !is_null($this->get($name));
    }
    public function is_valid_entity_type($name)
    {
        return !is_null($this->get_entity_type($name));
    }
    public function deregister_all()
    {
        $this->_sources = [];
        $this->_entity_types = [];
        $this->_registered_defaults = false;
    }
    public function is_compatible($source, $display_type)
    {
        $retval = false;
        if ($source = $this->get($source->name)) {
            // Get the real entity type names for the display type.
            $display_type_entity_types = [];
            foreach ($display_type->entity_types as $type) {
                $result = $this->get_entity_type($type);
                if ($result) {
                    $display_type_entity_types[] = $result;
                }
            }
            foreach ($source->returns as $entity_type) {
                if (in_array($entity_type, $display_type_entity_types, true)) {
                    $retval = true;
                    break;
                }
            }
        }
        return $retval;
    }
}
// This class is used exclusively by class.nextgen_pro_lightbox_trigger.php and should be removed when the minimum
// supported Pro version is updated.
/**
 * @deprecated
 */
abstract class C_Displayed_Gallery_Trigger
{
    public static function is_renderable($name, $displayed_gallery)
    {
        return true;
    }
    public function get_css_class()
    {
        return 'far fa-circle';
    }
    public function get_attributes()
    {
        return ['class' => $this->get_css_class()];
    }
    public function render()
    {
        $attributes = [];
        foreach ($this->get_attributes() as $k => $v) {
            $k = esc_attr($k);
            $v = esc_attr($v);
            $attributes[] = "{$k}='{$v}'";
        }
        $attributes = implode(' ', $attributes);
        return "<i {$attributes}></i>";
    }
}
/**
 * @deprecated
 * @mixin Mixin_GalleryStorage_Base_Dynamic
 * @mixin Mixin_GalleryStorage_Base_Getters
 * @mixin Mixin_GalleryStorage_Base_Management
 * @mixin Mixin_GalleryStorage_Base_Upload
 */
class C_Gallery_Storage extends C_Component
{
    public static $_instances = array();
    static $gallery_abspath_cache = array();
    /** @deprecated */
    public $_image_mapper;
    /** @deprecated */
    public $_gallery_mapper;
    function define($context = false)
    {
        parent::define($context);
        $this->add_mixin('Mixin_GalleryStorage_Base_Dynamic');
        $this->add_mixin('Mixin_GalleryStorage_Base_Getters');
        $this->add_mixin('Mixin_GalleryStorage_Base_Management');
        $this->add_mixin('Mixin_GalleryStorage_Base_Upload');
        $this->implement('I_Gallery_Storage');
        // TODO: remove this once Pro's API level is 4 or higher
        $this->implement('I_GalleryStorage_Driver');
        // backwards compatibility
    }
    /**
     * Provides some aliases to defined methods; thanks to this a call to C_Gallery_Storage->get_thumb_url() is
     * translated to C_Gallery_Storage->get_image_url('thumb').
     * TODO: Remove this 'magic' method so that our code is always understandable without needing deep context
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     * @throws Exception
     */
    function __call($method, $args)
    {
        if (preg_match('/^get_(\\w+)_(abspath|url|dimensions|html|size_params)$/', $method, $match)) {
            if (isset($match[1]) && isset($match[2]) && !$this->has_method($method)) {
                $method = 'get_image_' . $match[2];
                $args[] = $match[1];
                return parent::__call($method, $args);
            }
        }
        return parent::__call($method, $args);
    }
    /**
     * For compatibility reasons, we include this method. This used to be used to get the underlying storage driver.
     * Necessary for Imagify integration
     */
    function &get_wrapped_instance()
    {
        return $this;
    }
    function initialize()
    {
        parent::initialize();
        $this->_gallery_mapper = C_Gallery_Mapper::get_instance();
        $this->_image_mapper = C_Image_Mapper::get_instance();
    }
    /**
     * @param bool|string $context
     * @return C_Gallery_Storage
     */
    static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Gallery_Storage($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Gets the id of a gallery, regardless of whether an integer
     * or object was passed as an argument
     *
     * @param mixed $gallery_obj_or_id
     * @return null|int
     */
    function _get_gallery_id($gallery_obj_or_id)
    {
        $retval = null;
        $gallery_key = $this->object->_gallery_mapper->get_primary_key_column();
        if (is_object($gallery_obj_or_id)) {
            if (isset($gallery_obj_or_id->{$gallery_key})) {
                $retval = $gallery_obj_or_id->{$gallery_key};
            }
        } elseif (is_numeric($gallery_obj_or_id)) {
            $retval = $gallery_obj_or_id;
        }
        return $retval;
    }
    /**
     * Outputs/renders an image
     *
     * @param int|stdClass|C_Image $image
     * @return bool
     */
    function render_image($image, $size = false)
    {
        $format_list = $this->object->get_image_format_list();
        $abspath = $this->object->get_image_abspath($image, $size, true);
        if ($abspath == null) {
            $thumbnail = $this->object->generate_image_size($image, $size);
            if ($thumbnail != null) {
                $abspath = $thumbnail->fileName;
                $thumbnail->destruct();
            }
        }
        if ($abspath != null) {
            $data = @getimagesize($abspath);
            $format = 'jpg';
            if ($data != null && is_array($data) && isset($format_list[$data[2]])) {
                $format = $format_list[$data[2]];
            }
            // Clear output
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            $format = strtolower($format);
            // output image and headers
            header('Content-type: image/' . $format);
            readfile($abspath);
            return true;
        }
        return false;
    }
    /**
     * Sets a NGG image as a post thumbnail for the given post
     *
     * @param int                  $postId
     * @param int|C_Image|stdClass $image
     * @param bool                 $only_create_attachment
     * @return int
     */
    function set_post_thumbnail($postId, $image, $only_create_attachment = false)
    {
        $retval = false;
        // Get the post ID
        if (is_object($postId)) {
            $post = $postId;
            $postId = isset($post->ID) ? $post->ID : $post->post_id;
        }
        // Get the image
        if (is_int($image)) {
            $imageId = $image;
            $mapper = C_Image_Mapper::get_instance();
            $image = $mapper->find($imageId);
        }
        if ($image && $postId) {
            $attachment_id = $this->object->is_in_media_library($image->pid);
            if ($attachment_id === false) {
                $attachment_id = $this->object->copy_to_media_library($image);
            }
            if ($attachment_id) {
                if (!$only_create_attachment) {
                    set_post_thumbnail($postId, $attachment_id);
                }
                $retval = $attachment_id;
            }
        }
        return $retval;
    }
    function convert_slashes($path)
    {
        $search = ['/', '\\'];
        $replace = [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR];
        return str_replace($search, $replace, $path);
    }
    /**
     * Empties the gallery cache directory of content
     *
     * @param object $gallery
     */
    function flush_cache($gallery)
    {
        $cache = C_Cache::get_instance();
        $cache->flush_directory($this->object->get_cache_abspath($gallery));
    }
    /**
     * Sanitizes a directory path, replacing whitespace with dashes.
     *
     * Taken from WP' sanitize_file_name() and modified to not act on file extensions.
     *
     * Removes special characters that are illegal in filenames on certain
     * operating systems and special characters requiring special escaping
     * to manipulate at the command line. Replaces spaces and consecutive
     * dashes with a single dash. Trims period, dash and underscore from beginning
     * and end of filename. It is not guaranteed that this function will return a
     * filename that is allowed to be uploaded.
     *
     * @param string $dirname The directory name to be sanitized
     * @return string The sanitized directory name
     */
    public function sanitize_directory_name($dirname)
    {
        $dirname_raw = $dirname;
        $special_chars = ['?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', '%', '+', chr(0)];
        $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $dirname_raw);
        $dirname = preg_replace("#\\x{00a0}#siu", ' ', $dirname);
        $dirname = str_replace($special_chars, '', $dirname);
        $dirname = str_replace(['%20', '+'], '-', $dirname);
        $dirname = preg_replace('/[\\r\\n\\t -]+/', '-', $dirname);
        $dirname = trim($dirname, '.-_');
        return $dirname;
    }
    /**
     * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
     *
     * @param object|int $image Image ID or an image object
     * @param string     $size
     * @param array      $params
     * @param bool       $skip_defaults
     * @return bool|array
     */
    function calculate_image_size_dimensions($image, $size, $params = null, $skip_defaults = false)
    {
        $retval = false;
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $params = $this->object->get_image_size_params($image, $size, $params, $skip_defaults);
            // Get the image filename
            $image_path = $this->object->get_original_abspath($image, 'original');
            $clone_path = $this->object->get_image_abspath($image, $size);
            $retval = $this->object->calculate_image_clone_dimensions($image_path, $clone_path, $params);
        }
        return $retval;
    }
    /**
     * Generates a "clone" for an existing image, the clone can be altered using the $params array
     *
     * @param string $image_path
     * @param string $clone_path
     * @param array  $params
     * @return null|object
     */
    function generate_image_clone($image_path, $clone_path, $params)
    {
        $crop = isset($params['crop']) ? $params['crop'] : null;
        $watermark = isset($params['watermark']) ? $params['watermark'] : null;
        $reflection = isset($params['reflection']) ? $params['reflection'] : null;
        $rotation = isset($params['rotation']) ? $params['rotation'] : null;
        $flip = isset($params['flip']) ? $params['flip'] : '';
        $destpath = null;
        $thumbnail = null;
        $result = $this->object->calculate_image_clone_result($image_path, $clone_path, $params);
        // XXX this should maybe be removed and extra settings go into $params?
        $settings = apply_filters('ngg_settings_during_image_generation', C_NextGen_Settings::get_instance()->to_array());
        // Ensure we have a valid image
        if ($image_path && @file_exists($image_path) && $result != null && !isset($result['error'])) {
            $image_dir = dirname($image_path);
            $clone_path = $result['clone_path'];
            $clone_dir = $result['clone_directory'];
            $clone_format = $result['clone_format'];
            $format_list = $this->object->get_image_format_list();
            // Ensure target directory exists, but only create 1 subdirectory
            if (!@file_exists($clone_dir)) {
                if (strtolower(realpath($image_dir)) != strtolower(realpath($clone_dir))) {
                    if (strtolower(realpath($image_dir)) == strtolower(realpath(dirname($clone_dir)))) {
                        wp_mkdir_p($clone_dir);
                    }
                }
            }
            $method = $result['method'];
            $width = $result['width'];
            $height = $result['height'];
            $quality = $result['quality'];
            if ($quality == null) {
                $quality = 100;
            }
            if ($method == 'wordpress') {
                $original = wp_get_image_editor($image_path);
                $destpath = $clone_path;
                if (!is_wp_error($original)) {
                    $original->resize($width, $height, $crop);
                    $original->set_quality($quality);
                    $original->save($clone_path);
                }
            } elseif ($method == 'nextgen') {
                $destpath = $clone_path;
                $thumbnail = new C_NggLegacy_Thumbnail($image_path, true);
                if (!$thumbnail->error) {
                    if ($crop) {
                        $crop_area = $result['crop_area'];
                        $crop_x = $crop_area['x'];
                        $crop_y = $crop_area['y'];
                        $crop_width = $crop_area['width'];
                        $crop_height = $crop_area['height'];
                        $thumbnail->crop($crop_x, $crop_y, $crop_width, $crop_height);
                    }
                    $thumbnail->resize($width, $height);
                } else {
                    $thumbnail = null;
                }
            }
            // We successfully generated the thumbnail
            if (is_string($destpath) && (@file_exists($destpath) || $thumbnail != null)) {
                if ($clone_format != null) {
                    if (isset($format_list[$clone_format])) {
                        $clone_format_extension = $format_list[$clone_format];
                        $clone_format_extension_str = null;
                        if ($clone_format_extension != null) {
                            $clone_format_extension_str = '.' . $clone_format_extension;
                        }
                        $destpath_info = M_I18n::mb_pathinfo($destpath);
                        $destpath_extension = $destpath_info['extension'];
                        if (strtolower($destpath_extension) != strtolower($clone_format_extension)) {
                            $destpath_dir = $destpath_info['dirname'];
                            $destpath_basename = $destpath_info['filename'];
                            $destpath_new = $destpath_dir . DIRECTORY_SEPARATOR . $destpath_basename . $clone_format_extension_str;
                            if (@file_exists($destpath) && rename($destpath, $destpath_new) || $thumbnail != null) {
                                $destpath = $destpath_new;
                            }
                        }
                    }
                }
                if (is_null($thumbnail)) {
                    $thumbnail = new C_NggLegacy_Thumbnail($destpath, true);
                    if ($thumbnail->error) {
                        $thumbnail = null;
                        return null;
                    }
                } else {
                    $thumbnail->fileName = $destpath;
                }
                // This is quite odd, when watermark equals int(0) it seems all statements below ($watermark == 'image') and ($watermark == 'text') both evaluate as true
                // so we set it at null if it evaluates to any null-like value
                if ($watermark == null) {
                    $watermark = null;
                }
                if ($watermark == 1 || $watermark === true) {
                    $watermark_setting_keys = ['wmFont', 'wmType', 'wmPos', 'wmXpos', 'wmYpos', 'wmPath', 'wmText', 'wmOpaque', 'wmFont', 'wmSize', 'wmColor'];
                    foreach ($watermark_setting_keys as $watermark_key) {
                        if (!isset($params[$watermark_key])) {
                            $params[$watermark_key] = $settings[$watermark_key];
                        }
                    }
                    if (in_array(strval($params['wmType']), ['image', 'text'])) {
                        $watermark = $params['wmType'];
                    } else {
                        $watermark = 'text';
                    }
                }
                $watermark = strval($watermark);
                if ($watermark == 'image') {
                    $thumbnail->watermarkImgPath = $params['wmPath'];
                    $thumbnail->watermarkImage($params['wmPos'], $params['wmXpos'], $params['wmYpos']);
                } elseif ($watermark == 'text') {
                    $thumbnail->watermarkText = $params['wmText'];
                    $thumbnail->watermarkCreateText($params['wmColor'], $params['wmFont'], $params['wmSize'], $params['wmOpaque']);
                    $thumbnail->watermarkImage($params['wmPos'], $params['wmXpos'], $params['wmYpos']);
                }
                if ($rotation && in_array(abs($rotation), [90, 180, 270])) {
                    $thumbnail->rotateImageAngle($rotation);
                }
                $flip = strtolower($flip);
                if ($flip && in_array($flip, ['h', 'v', 'hv'])) {
                    $flip_h = in_array($flip, ['h', 'hv']);
                    $flip_v = in_array($flip, ['v', 'hv']);
                    $thumbnail->flipImage($flip_h, $flip_v);
                }
                if ($reflection) {
                    $thumbnail->createReflection(40, 40, 50, false, '#a4a4a4');
                }
                // Force format
                if ($clone_format != null && isset($format_list[$clone_format])) {
                    $thumbnail->format = strtoupper($format_list[$clone_format]);
                }
                $thumbnail = apply_filters('ngg_before_save_thumbnail', $thumbnail);
                // Always retrieve metadata from the backup when possible
                $backup_path = $image_path . '_backup';
                $exif_abspath = @file_exists($backup_path) ? $backup_path : $image_path;
                $exif_iptc = @\Imagely\NGG\DataStorage\EXIFWriter::read_metadata($exif_abspath);
                $thumbnail->save($destpath, $quality);
                @\Imagely\NGG\DataStorage\EXIFWriter::write_metadata($destpath, $exif_iptc);
            }
        }
        return $thumbnail;
    }
    /**
     * Returns an array of dimensional properties (width, height, real_width, real_height) of a resulting clone image if and when generated
     *
     * @param string $image_path
     * @param string $clone_path
     * @param array  $params
     * @return null|array
     */
    function calculate_image_clone_dimensions($image_path, $clone_path, $params)
    {
        $retval = null;
        $result = $this->object->calculate_image_clone_result($image_path, $clone_path, $params);
        if ($result != null) {
            $retval = ['width' => $result['width'], 'height' => $result['height'], 'real_width' => $result['real_width'], 'real_height' => $result['real_height']];
        }
        return $retval;
    }
    /**
     * Returns an array of properties of a resulting clone image if and when generated
     *
     * @param string $image_path
     * @param string $clone_path
     * @param array  $params
     * @return null|array
     */
    function calculate_image_clone_result($image_path, $clone_path, $params)
    {
        $width = isset($params['width']) ? $params['width'] : null;
        $height = isset($params['height']) ? $params['height'] : null;
        $quality = isset($params['quality']) ? $params['quality'] : null;
        $type = isset($params['type']) ? $params['type'] : null;
        $crop = isset($params['crop']) ? $params['crop'] : null;
        $watermark = isset($params['watermark']) ? $params['watermark'] : null;
        $rotation = isset($params['rotation']) ? $params['rotation'] : null;
        $reflection = isset($params['reflection']) ? $params['reflection'] : null;
        $crop_frame = isset($params['crop_frame']) ? $params['crop_frame'] : null;
        $result = null;
        // Ensure we have a valid image
        if ($image_path && @file_exists($image_path)) {
            // Ensure target directory exists, but only create 1 subdirectory
            $image_dir = dirname($image_path);
            $clone_dir = dirname($clone_path);
            $image_extension = M_I18n::mb_pathinfo($image_path, PATHINFO_EXTENSION);
            $image_extension_str = null;
            $clone_extension = M_I18n::mb_pathinfo($clone_path, PATHINFO_EXTENSION);
            $clone_extension_str = null;
            if ($image_extension != null) {
                $image_extension_str = '.' . $image_extension;
            }
            if ($clone_extension != null) {
                $clone_extension_str = '.' . $clone_extension;
            }
            $image_basename = M_I18n::mb_basename($image_path);
            $clone_basename = M_I18n::mb_basename($clone_path);
            // We use a default suffix as passing in null as the suffix will make WordPress use a default
            $clone_suffix = null;
            $format_list = $this->object->get_image_format_list();
            $clone_format = null;
            // format is determined below and based on $type otherwise left to null
            // suffix is only used to reconstruct paths for image_resize function
            if (strpos($clone_basename, $image_basename) === 0) {
                $clone_suffix = substr($clone_basename, strlen($image_basename));
            }
            if ($clone_suffix != null && $clone_suffix[0] == '-') {
                // WordPress adds '-' on its own
                $clone_suffix = substr($clone_suffix, 1);
            }
            // Get original image dimensions
            $dimensions = getimagesize($image_path);
            if ($width == null && $height == null) {
                if ($dimensions != null) {
                    if ($width == null) {
                        $width = $dimensions[0];
                    }
                    if ($height == null) {
                        $height = $dimensions[1];
                    }
                } else {
                    // XXX Don't think there's any other option here but to fail miserably...use some hard-coded defaults maybe?
                    return null;
                }
            }
            if ($dimensions != null) {
                $dimensions_ratio = $dimensions[0] / $dimensions[1];
                if ($width == null) {
                    $width = (int) round($height * $dimensions_ratio);
                    if ($width == $dimensions[0] - 1) {
                        $width = $dimensions[0];
                    }
                } elseif ($height == null) {
                    $height = (int) round($width / $dimensions_ratio);
                    if ($height == $dimensions[1] - 1) {
                        $height = $dimensions[1];
                    }
                }
                if ($width > $dimensions[0]) {
                    $width = $dimensions[0];
                }
                if ($height > $dimensions[1]) {
                    $height = $dimensions[1];
                }
                $image_format = $dimensions[2];
                if ($type != null) {
                    if (is_string($type)) {
                        $type = strtolower($type);
                        // Indexes in the $format_list array correspond to IMAGETYPE_XXX values appropriately
                        if (($index = array_search($type, $format_list)) !== false) {
                            $type = $index;
                            if ($type != $image_format) {
                                // Note: this only changes the FORMAT of the image but not the extension
                                $clone_format = $type;
                            }
                        }
                    }
                }
            }
            if ($width == null || $height == null) {
                // Something went wrong...
                return null;
            }
            // We now need to estimate the 'quality' or level of compression applied to the original JPEG: *IF* the
            // original image has a quality lower than the $quality parameter we will end up generating a new image
            // that is MUCH larger than the original. 'Quality' as an EXIF or IPTC property is quite unreliable
            // and not all software honors or treats it the same way. This calculation is simple: just compare the size
            // that our image could become to what it currently is. '3' is important here as JPEG uses 3 bytes per pixel.
            //
            // First we attempt to use ImageMagick if we can; it has a more robust method of calculation.
            if (!empty($dimensions['mime']) && $dimensions['mime'] == 'image/jpeg') {
                $possible_quality = null;
                $try_image_magick = true;
                if (defined('NGG_DISABLE_IMAGICK') && NGG_DISABLE_IMAGICK || function_exists('is_wpe') && ($dimensions[0] >= 8000 || $dimensions[1] >= 8000)) {
                    $try_image_magick = false;
                }
                if ($try_image_magick && extension_loaded('imagick') && class_exists('Imagick')) {
                    $img = new Imagick($image_path);
                    if (method_exists($img, 'getImageCompressionQuality')) {
                        $possible_quality = $img->getImageCompressionQuality();
                    }
                }
                // ImageMagick wasn't available so we guess it from the dimensions and filesize
                if ($possible_quality === null) {
                    $filesize = filesize($image_path);
                    $possible_quality = 101 - $width * $height * 3 / $filesize;
                }
                if ($possible_quality !== null && $possible_quality < $quality) {
                    $quality = $possible_quality;
                }
            }
            $result['clone_path'] = $clone_path;
            $result['clone_directory'] = $clone_dir;
            $result['clone_suffix'] = $clone_suffix;
            $result['clone_format'] = $clone_format;
            $result['base_width'] = $dimensions[0];
            $result['base_height'] = $dimensions[1];
            // image_resize() has limitations:
            // - no easy crop frame support
            // - fails if the dimensions are unchanged
            // - doesn't support filename prefix, only suffix so names like thumbs_original_name.jpg for $clone_path are not supported
            // also suffix cannot be null as that will make WordPress use a default suffix...we could use an object that returns empty string from __toString() but for now just fallback to ngg generator
            if (false) {
                // disabling the WordPress method for Iteration #6
                // if (($crop_frame == null || !$crop) && ($dimensions[0] != $width && $dimensions[1] != $height) && $clone_suffix != null)
                $result['method'] = 'wordpress';
                $new_dims = image_resize_dimensions($dimensions[0], $dimensions[1], $width, $height, $crop);
                if ($new_dims) {
                    list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $new_dims;
                    $width = $dst_w;
                    $height = $dst_h;
                } else {
                    $result['error'] = new WP_Error('error_getting_dimensions', __('Could not calculate resized image dimensions'));
                }
            } else {
                $result['method'] = 'nextgen';
                $original_width = $dimensions[0];
                $original_height = $dimensions[1];
                $aspect_ratio = $width / $height;
                $orig_ratio_x = $original_width / $width;
                $orig_ratio_y = $original_height / $height;
                if ($crop) {
                    $algo = 'shrink';
                    // either 'adapt' or 'shrink'
                    if ($crop_frame != null) {
                        $crop_x = (int) round($crop_frame['x']);
                        $crop_y = (int) round($crop_frame['y']);
                        $crop_width = (int) round($crop_frame['width']);
                        $crop_height = (int) round($crop_frame['height']);
                        $crop_final_width = (int) round($crop_frame['final_width']);
                        $crop_final_height = (int) round($crop_frame['final_height']);
                        $crop_width_orig = $crop_width;
                        $crop_height_orig = $crop_height;
                        $crop_factor_x = $crop_width / $crop_final_width;
                        $crop_factor_y = $crop_height / $crop_final_height;
                        $crop_ratio_x = $crop_width / $width;
                        $crop_ratio_y = $crop_height / $height;
                        if ($algo == 'adapt') {
                            // XXX not sure about this...don't use for now
                            // $crop_width = (int) round($width * $crop_factor_x);
                            // $crop_height = (int) round($height * $crop_factor_y);
                        } elseif ($algo == 'shrink') {
                            if ($crop_ratio_x < $crop_ratio_y) {
                                $crop_width = max($crop_width, $width);
                                $crop_height = (int) round($crop_width / $aspect_ratio);
                            } else {
                                $crop_height = max($crop_height, $height);
                                $crop_width = (int) round($crop_height * $aspect_ratio);
                            }
                            if ($crop_width == $crop_width_orig - 1) {
                                $crop_width = $crop_width_orig;
                            }
                            if ($crop_height == $crop_height_orig - 1) {
                                $crop_height = $crop_height_orig;
                            }
                        }
                        $crop_diff_x = (int) round(($crop_width_orig - $crop_width) / 2);
                        $crop_diff_y = (int) round(($crop_height_orig - $crop_height) / 2);
                        $crop_x += $crop_diff_x;
                        $crop_y += $crop_diff_y;
                        $crop_max_x = $crop_x + $crop_width;
                        $crop_max_y = $crop_y + $crop_height;
                        // Check if we're overflowing borders
                        //
                        if ($crop_x < 0) {
                            $crop_x = 0;
                        } elseif ($crop_max_x > $original_width) {
                            $crop_x -= $crop_max_x - $original_width;
                        }
                        if ($crop_y < 0) {
                            $crop_y = 0;
                        } elseif ($crop_max_y > $original_height) {
                            $crop_y -= $crop_max_y - $original_height;
                        }
                    } else {
                        if ($orig_ratio_x < $orig_ratio_y) {
                            $crop_width = $original_width;
                            $crop_height = (int) round($height * $orig_ratio_x);
                        } else {
                            $crop_height = $original_height;
                            $crop_width = (int) round($width * $orig_ratio_y);
                        }
                        if ($crop_width == $width - 1) {
                            $crop_width = $width;
                        }
                        if ($crop_height == $height - 1) {
                            $crop_height = $height;
                        }
                        $crop_x = (int) round(($original_width - $crop_width) / 2);
                        $crop_y = (int) round(($original_height - $crop_height) / 2);
                    }
                    $result['crop_area'] = ['x' => $crop_x, 'y' => $crop_y, 'width' => $crop_width, 'height' => $crop_height];
                } else {
                    // Just constraint dimensions to ensure there's no stretching or deformations
                    list($width, $height) = wp_constrain_dimensions($original_width, $original_height, $width, $height);
                }
            }
            $result['width'] = $width;
            $result['height'] = $height;
            $result['quality'] = $quality;
            $real_width = $width;
            $real_height = $height;
            if ($rotation && in_array(abs($rotation), [90, 270])) {
                $real_width = $height;
                $real_height = $width;
            }
            if ($reflection) {
                // default for nextgen was 40%, this is used in generate_image_clone as well
                $reflection_amount = 40;
                // Note, round() would probably be best here but using the same code that C_NggLegacy_Thumbnail uses for compatibility
                $reflection_height = intval($real_height * ($reflection_amount / 100));
                $real_height = $real_height + $reflection_height;
            }
            $result['real_width'] = $real_width;
            $result['real_height'] = $real_height;
        }
        return $result;
    }
    function generate_resized_image($image, $save = true)
    {
        $image_abspath = $this->object->get_image_abspath($image, 'full');
        $generated = $this->object->generate_image_clone($image_abspath, $image_abspath, $this->object->get_image_size_params($image, 'full'));
        if ($generated && $save) {
            $this->object->update_image_dimension_metadata($image, $image_abspath);
        }
        if ($generated) {
            $generated->destruct();
        }
    }
    public function update_image_dimension_metadata($image, $image_abspath)
    {
        // Ensure that fullsize dimensions are added to metadata array
        $dimensions = getimagesize($image_abspath);
        $full_meta = ['width' => $dimensions[0], 'height' => $dimensions[1], 'md5' => $this->object->get_image_checksum($image, 'full')];
        if (!isset($image->meta_data) or is_string($image->meta_data) && strlen($image->meta_data) == 0 or is_bool($image->meta_data)) {
            $image->meta_data = [];
        }
        $image->meta_data = array_merge($image->meta_data, $full_meta);
        $image->meta_data['full'] = $full_meta;
        // Don't forget to append the 'full' entry in meta_data in the db
        $this->object->_image_mapper->save($image);
    }
    /**
     * Most major browsers do not honor the Orientation meta found in EXIF. To prevent display issues we inspect
     * the EXIF data and rotate the image so that the EXIF field is not necessary to display the image correctly.
     * Note: generate_image_clone() will handle the removal of the Orientation tag inside the image EXIF.
     * Note: This only handles single-dimension rotation; at the time this method was written there are no known
     * camera manufacturers that both rotate and flip images.
     *
     * @param $image
     * @param bool  $save
     */
    public function correct_exif_rotation($image, $save = true)
    {
        $image_abspath = $this->object->get_image_abspath($image, 'full');
        // This method is necessary
        if (!function_exists('exif_read_data')) {
            return;
        }
        // We only need to continue if the Orientation tag is set
        $exif = @exif_read_data($image_abspath, 'exif');
        if (empty($exif['Orientation']) || $exif['Orientation'] == 1) {
            return;
        }
        $degree = 0;
        if ($exif['Orientation'] == 3) {
            $degree = 180;
        }
        if ($exif['Orientation'] == 6) {
            $degree = 90;
        }
        if ($exif['Orientation'] == 8) {
            $degree = 270;
        }
        $parameters = ['rotation' => $degree];
        $generated = $this->object->generate_image_clone($image_abspath, $image_abspath, $this->object->get_image_size_params($image, 'full', $parameters), $parameters);
        if ($generated && $save) {
            $this->object->update_image_dimension_metadata($image, $image_abspath);
        }
        if ($generated) {
            $generated->destruct();
        }
    }
    /**
     * Flushes the cache we use for path/url calculation for galleries
     */
    function flush_gallery_path_cache($gallery)
    {
        $gallery = is_numeric($gallery) ? $gallery : $gallery->gid;
        unset(self::$gallery_abspath_cache[$gallery]);
    }
    /**
     * Gets the id of an image, regardless of whether an integer
     * or object was passed as an argument
     *
     * @param object|int $image_obj_or_id
     * @return null|int
     */
    function _get_image_id($image_obj_or_id)
    {
        $retval = null;
        $image_key = $this->object->_image_mapper->get_primary_key_column();
        if (is_object($image_obj_or_id)) {
            if (isset($image_obj_or_id->{$image_key})) {
                $retval = $image_obj_or_id->{$image_key};
            }
        } elseif (is_numeric($image_obj_or_id)) {
            $retval = $image_obj_or_id;
        }
        return $retval;
    }
    /**
     * Returns the absolute path to the cache directory of a gallery.
     *
     * Without the gallery parameter the legacy (pre 2.0) shared directory is returned.
     *
     * @param int|object|false|C_Gallery $gallery (optional)
     * @return string Absolute path to cache directory
     */
    function get_cache_abspath($gallery = false)
    {
        return path_join($this->object->get_gallery_abspath($gallery), 'cache');
    }
    /**
     * Gets the absolute path where the full-sized image is stored
     *
     * @param int|object $image
     * @return null|string
     */
    function get_full_abspath($image)
    {
        return $this->object->get_image_abspath($image, 'full');
    }
    /**
     * Alias to get_image_dimensions()
     *
     * @param int|object $image
     * @return array
     */
    function get_full_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'full');
    }
    /**
     * Alias to get_image_html()
     *
     * @param int|object $image
     * @return string
     */
    function get_full_html($image)
    {
        return $this->object->get_image_html($image, 'full');
    }
    /**
     * Alias for get_original_url()
     *
     * @param int|stdClass|C_Image $image
     * @return string
     */
    function get_full_url($image)
    {
        return $this->object->get_image_url($image, 'full');
    }
    function get_gallery_root()
    {
        return wp_normalize_path(C_Fs::get_instance()->get_document_root('galleries'));
    }
    function _get_computed_gallery_abspath($gallery)
    {
        $retval = null;
        $gallery_root = $this->get_gallery_root();
        // Get the gallery entity from the database
        if ($gallery) {
            if (is_numeric($gallery)) {
                $gallery = $this->object->_gallery_mapper->find($gallery);
            }
        }
        // It just doesn't exist
        if (!$gallery) {
            return $retval;
        }
        // We we have a gallery, determine it's path
        if ($gallery) {
            if (isset($gallery->path)) {
                $retval = $gallery->path;
            } elseif (isset($gallery->slug)) {
                $basepath = wp_normalize_path(C_NextGen_Settings::get_instance()->gallerypath);
                $retval = path_join($basepath, $this->object->sanitize_directory_name(sanitize_title($gallery->slug)));
            }
            // Normalize the gallery path. If the gallery path starts with /wp-content, and
            // NGG_GALLERY_ROOT_TYPE is set to 'content', then we need to strip out the /wp-content
            // from the start of the gallery path
            if (NGG_GALLERY_ROOT_TYPE === 'content') {
                $retval = preg_replace('#^/?wp-content#', '', $retval);
            }
            // Ensure that the path is absolute
            if (strpos($retval, $gallery_root) !== 0) {
                // path_join() behaves funny - if the second argument starts with a slash,
                // it won't join the two paths together
                $retval = preg_replace('#^/#', '', $retval);
                $retval = path_join($gallery_root, $retval);
            }
            $retval = wp_normalize_path($retval);
        }
        return $retval;
    }
    /**
     * Get the abspath to the gallery folder for the given gallery
     * The gallery may or may not already be persisted
     *
     * @param int|object|C_Gallery $gallery
     * @return string
     */
    function get_gallery_abspath($gallery)
    {
        $gallery_id = is_numeric($gallery) ? $gallery : (is_object($gallery) && isset($gallery->gid) ? $gallery->gid : null);
        if (!$gallery_id || !isset(self::$gallery_abspath_cache[$gallery_id])) {
            self::$gallery_abspath_cache[$gallery_id] = $this->object->_get_computed_gallery_abspath($gallery);
        }
        return self::$gallery_abspath_cache[$gallery_id];
    }
    function get_gallery_relpath($gallery)
    {
        // Special hack for home.pl: their document root is just '/'
        $root = $this->object->get_gallery_root();
        if ($root === '/') {
            return $this->get_gallery_abspath($gallery);
        }
        return str_replace($this->object->get_gallery_root(), '', $this->get_gallery_abspath($gallery));
    }
    /**
     * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
     *
     * @param int|object $image
     * @param string     $size (optional) Default = full
     * @return string
     */
    function _get_computed_image_abspath($image, $size = 'full', $check_existance = false)
    {
        $retval = null;
        // If we have the id, get the actual image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have the image entity - user could have passed in an incorrect id
        if (is_object($image)) {
            if ($gallery_path = $this->object->get_gallery_abspath($image->galleryid)) {
                $folder = $prefix = $size;
                switch ($size) {
                    // Images are stored in the associated gallery folder
                    case 'full':
                        $retval = path_join($gallery_path, $image->filename);
                        break;
                    case 'backup':
                        $retval = path_join($gallery_path, $image->filename . '_backup');
                        if (!@file_exists($retval)) {
                            $retval = path_join($gallery_path, $image->filename);
                        }
                        break;
                    case 'thumbnail':
                        $size = 'thumbnail';
                        $folder = 'thumbs';
                        $prefix = 'thumbs';
                    // deliberately no break here
                    default:
                        // NGG 2.0 stores relative filenames in the meta data of
                        // an image. It does this because it uses filenames
                        // that follow conventional WordPress naming scheme.
                        $image_path = null;
                        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                        if (isset($image->meta_data) && isset($image->meta_data[$size]) && isset($image->meta_data[$size]['filename'])) {
                            if ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                                $image_path = path_join($this->object->get_cache_abspath($image->galleryid), $image->meta_data[$size]['filename']);
                            } else {
                                $image_path = path_join($gallery_path, $folder);
                                $image_path = path_join($image_path, $image->meta_data[$size]['filename']);
                            }
                        } elseif ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                            $params = $dynthumbs->get_params_from_name($size, true);
                            $image_path = path_join($this->object->get_cache_abspath($image->galleryid), $dynthumbs->get_image_name($image, $params));
                            // Filename is not found in meta, nor dynamic
                        } else {
                            $settings = C_NextGen_Settings::get_instance();
                            // This next bit is annoying but necessary for legacy reasons. NextGEN until 3.19 stored thumbnails
                            // with a filename of "thumbs_(whatever.jpg)" which Google indexes as "thumbswhatever.jpg" which is
                            // not good for SEO. From 3.19 on the default setting is "thumbs-" but we must account for legacy
                            // sites.
                            $image_path = path_join($gallery_path, $folder);
                            $new_thumb_path = path_join($image_path, "{$prefix}-{$image->filename}");
                            $old_thumb_path = path_join($image_path, "{$prefix}_{$image->filename}");
                            if ($settings->get('dynamic_image_filename_separator_use_dash', false)) {
                                // Check for thumbs- first
                                if (file_exists($new_thumb_path)) {
                                    $image_path = $new_thumb_path;
                                } elseif (file_exists($old_thumb_path)) {
                                    // Check for thumbs_ as a fallback
                                    $image_path = $old_thumb_path;
                                } else {
                                    // The thumbnail file does not exist, default to thumbs-
                                    $image_path = $new_thumb_path;
                                }
                            } else {
                                // Reversed: the option is disabled so check for thumbs_
                                if (file_exists($old_thumb_path)) {
                                    $image_path = $old_thumb_path;
                                } elseif (file_exists($new_thumb_path)) {
                                    // In case the user has switched back and forth, check for thumbs-
                                    $image_path = $new_thumb_path;
                                } else {
                                    // Default to thumbs_ per the site setting
                                    $image_path = $old_thumb_path;
                                }
                            }
                        }
                        $retval = $image_path;
                        break;
                }
            }
        }
        if ($retval && $check_existance && !@file_exists($retval)) {
            $retval = null;
        }
        return $retval;
    }
    function get_image_checksum($image, $size = 'full')
    {
        $retval = null;
        if ($image_abspath = $this->get_image_abspath($image, $size, true)) {
            $retval = md5_file($image_abspath);
        }
        return $retval;
    }
    /**
     * Gets the dimensions for a particular-sized image
     *
     * @param int|object $image
     * @param string     $size
     * @return null|array
     */
    function get_image_dimensions($image, $size = 'full')
    {
        $retval = null;
        // If an image id was provided, get the entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $size = $this->normalize_image_size_name($size);
            if (!$size) {
                $size = 'full';
            }
            // Image dimensions are stored in the $image->meta_data
            // property for all implementations
            if (isset($image->meta_data) && isset($image->meta_data[$size])) {
                $retval = $image->meta_data[$size];
            } else {
                $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                $abspath = $this->object->get_image_abspath($image, $size, true);
                if ($abspath) {
                    $dims = @getimagesize($abspath);
                    if ($dims) {
                        $retval['width'] = $dims[0];
                        $retval['height'] = $dims[1];
                    }
                } elseif ($size == 'backup') {
                    $retval = $this->object->get_image_dimensions($image, 'full');
                }
                if (!$retval && $dynthumbs && $dynthumbs->is_size_dynamic($size)) {
                    $new_dims = $this->object->calculate_image_size_dimensions($image, $size);
                    $retval = ['width' => $new_dims['real_width'], 'height' => $new_dims['real_height']];
                }
            }
        }
        return $retval;
    }
    function get_image_format_list()
    {
        $format_list = [IMAGETYPE_GIF => 'gif', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
        return $format_list;
    }
    /**
     * Gets the HTML for an image
     *
     * @param int|object $image
     * @param string     $size
     * @param array      $attributes (optional)
     * @return string
     */
    function get_image_html($image, $size = 'full', $attributes = array())
    {
        $retval = '';
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            // Set alt text if not already specified
            if (!isset($attributes['alttext'])) {
                $attributes['alt'] = esc_attr($image->alttext);
            }
            // Set the title if not already set
            if (!isset($attributes['title'])) {
                $attributes['title'] = esc_attr($image->alttext);
            }
            // Set the dimensions if not set already
            if (!isset($attributes['width']) or !isset($attributes['height'])) {
                $dimensions = $this->object->get_image_dimensions($image, $size);
                if (!isset($attributes['width'])) {
                    $attributes['width'] = $dimensions['width'];
                }
                if (!isset($attributes['height'])) {
                    $attributes['height'] = $dimensions['height'];
                }
            }
            // Set the url if not already specified
            if (!isset($attributes['src'])) {
                $attributes['src'] = $this->object->get_image_url($image, $size);
            }
            // Format attributes
            $attribs = [];
            foreach ($attributes as $attrib => $value) {
                $attribs[] = "{$attrib}=\"{$value}\"";
            }
            $attribs = implode(' ', $attribs);
            // Return HTML string
            $retval = "<img {$attribs} />";
        }
        return $retval;
    }
    function _get_computed_image_url($image, $size = 'full')
    {
        $retval = null;
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        // Get the image abspath
        $image_abspath = $this->object->get_image_abspath($image, $size);
        if ($dynthumbs->is_size_dynamic($size) && !file_exists($image_abspath)) {
            if (defined('NGG_DISABLE_DYNAMIC_IMG_URLS') && constant('NGG_DISABLE_DYNAMIC_IMG_URLS')) {
                $params = ['watermark' => false, 'reflection' => false, 'crop' => true];
                $result = $this->generate_image_size($image, $size, $params);
                if ($result) {
                    $image_abspath = $this->object->get_image_abspath($image, $size);
                }
            } else {
                return null;
            }
        }
        // Assuming we have an abspath, we can translate that to a url
        if ($image_abspath) {
            // Replace the gallery root with the proper url segment
            $gallery_root = preg_quote($this->get_gallery_root(), '#');
            $image_uri = preg_replace("#^{$gallery_root}#", '', $image_abspath);
            // Url encode each uri segment
            $segments = explode('/', $image_uri);
            $segments = array_map('rawurlencode', $segments);
            $image_uri = preg_replace('#^/#', '', implode('/', $segments));
            // Join gallery root and image uri
            $gallery_root = trailingslashit(NGG_GALLERY_ROOT_TYPE == 'site' ? site_url() : WP_CONTENT_URL);
            $gallery_root = is_ssl() ? str_replace('http:', 'https:', $gallery_root) : $gallery_root;
            $retval = $gallery_root . $image_uri;
        }
        return $retval;
    }
    function normalize_image_size_name($size = 'full')
    {
        switch ($size) {
            case 'full':
            case 'original':
            case 'image':
            case 'orig':
            case 'resized':
                $size = 'full';
                break;
            case 'thumbnails':
            case 'thumbnail':
            case 'thumb':
            case 'thumbs':
                $size = 'thumbnail';
                break;
        }
        return $size;
    }
    /**
     * Returns the named sizes available for images
     *
     * @return array
     */
    function get_image_sizes($image = false)
    {
        $retval = ['full', 'thumbnail'];
        if (is_numeric($image)) {
            $image = C_Image_Mapper::get_instance()->find($image);
        }
        if ($image) {
            if ($image->meta_data) {
                $meta_data = is_object($image->meta_data) ? get_object_vars($image->meta_data) : $image->meta_data;
                foreach ($meta_data as $key => $value) {
                    if (is_array($value) && isset($value['width']) && !in_array($key, $retval)) {
                        $retval[] = $key;
                    }
                }
            }
        }
        return $retval;
    }
    function get_image_size_params($image, $size, $params = array(), $skip_defaults = false)
    {
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
        if ($dynthumbs && $dynthumbs->is_size_dynamic($size)) {
            $named_params = $dynthumbs->get_params_from_name($size, true);
            if (!$params) {
                $params = [];
            }
            $params = array_merge($params, $named_params);
        }
        $params = apply_filters('ngg_get_image_size_params', $params, $size, $image);
        // Ensure we have a valid image
        if ($image) {
            $settings = C_NextGen_Settings::get_instance();
            if (!$skip_defaults) {
                // Get default settings
                if ($size == 'full') {
                    if (!isset($params['quality'])) {
                        $params['quality'] = $settings->imgQuality;
                    }
                } else {
                    if (!isset($params['crop'])) {
                        $params['crop'] = $settings->thumbfix;
                    }
                    if (!isset($params['quality'])) {
                        $params['quality'] = $settings->thumbquality;
                    }
                }
            }
            // width and height when omitted make generate_image_clone create a clone with original size, so try find defaults regardless of $skip_defaults
            if (!isset($params['width']) || !isset($params['height'])) {
                // First test if this is a "known" image size, i.e. if we store these sizes somewhere when users re-generate these sizes from the UI...this is required to be compatible with legacy
                // try the 2 default built-in sizes, first thumbnail...
                if ($size == 'thumbnail') {
                    if (!isset($params['width'])) {
                        $params['width'] = $settings->thumbwidth;
                    }
                    if (!isset($params['height'])) {
                        $params['height'] = $settings->thumbheight;
                    }
                } elseif ($size == 'full') {
                    if (!isset($params['width'])) {
                        if ($settings->imgAutoResize) {
                            $params['width'] = $settings->imgWidth;
                        }
                    }
                    if (!isset($params['height'])) {
                        if ($settings->imgAutoResize) {
                            $params['height'] = $settings->imgHeight;
                        }
                    }
                } elseif (isset($image->meta_data) && isset($image->meta_data[$size])) {
                    $dimensions = $image->meta_data[$size];
                    if (!isset($params['width'])) {
                        $params['width'] = $dimensions['width'];
                    }
                    if (!isset($params['height'])) {
                        $params['height'] = $dimensions['height'];
                    }
                }
            }
            if (!isset($params['crop_frame'])) {
                $crop_frame_size_name = 'thumbnail';
                if (isset($image->meta_data[$size]['crop_frame'])) {
                    $crop_frame_size_name = $size;
                }
                if (isset($image->meta_data[$crop_frame_size_name]['crop_frame'])) {
                    $params['crop_frame'] = $image->meta_data[$crop_frame_size_name]['crop_frame'];
                    if (!isset($params['crop_frame']['final_width'])) {
                        $params['crop_frame']['final_width'] = $image->meta_data[$crop_frame_size_name]['width'];
                    }
                    if (!isset($params['crop_frame']['final_height'])) {
                        $params['crop_frame']['final_height'] = $image->meta_data[$crop_frame_size_name]['height'];
                    }
                }
            } else {
                if (!isset($params['crop_frame']['final_width'])) {
                    $params['crop_frame']['final_width'] = $params['width'];
                }
                if (!isset($params['crop_frame']['final_height'])) {
                    $params['crop_frame']['final_height'] = $params['height'];
                }
            }
        }
        return $params;
    }
    /**
     * Alias to get_image_dimensions()
     *
     * @param int|object $image
     * @return array
     */
    function get_original_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'full');
    }
    /**
     * Alias to get_image_html()
     *
     * @param int|object $image
     * @return string
     */
    function get_original_html($image)
    {
        return $this->object->get_image_html($image, 'full');
    }
    /**
     * Gets the url to the original-sized image
     *
     * @param int|stdClass|C_Image $image
     * @param bool                 $check_existance (optional)
     * @return string
     */
    function get_original_url($image, $check_existance = false)
    {
        return $this->object->get_image_url($image, 'full', $check_existance);
    }
    /**
     * @param object|bool $gallery (optional)
     * @return string
     */
    function get_upload_abspath($gallery = false)
    {
        // Base upload path
        $retval = C_NextGen_Settings::get_instance()->gallerypath;
        $fs = C_Fs::get_instance();
        // If a gallery has been specified, then we'll
        // append the slug
        if ($gallery) {
            $retval = $this->get_gallery_abspath($gallery);
        }
        // We need to make this an absolute path
        if (strpos($retval, $fs->get_document_root('gallery')) !== 0) {
            $retval = rtrim($fs->join_paths($fs->get_document_root('gallery'), $retval), '/\\');
        }
        // Convert slashes
        return wp_normalize_path($retval);
    }
    /**
     * Gets the upload path, optionally for a particular gallery
     *
     * @param int|C_Gallery|object|false $gallery (optional)
     * @return string
     */
    function get_upload_relpath($gallery = false)
    {
        $fs = C_Fs::get_instance();
        $retval = str_replace($fs->get_document_root('gallery'), '', $this->object->get_upload_abspath($gallery));
        return '/' . wp_normalize_path(ltrim($retval, '/'));
    }
    /**
     * Set correct file permissions (taken from wp core). Should be called
     * after writing any file
     *
     * @class nggAdmin
     * @param string $filename
     * @return bool $result
     */
    function _chmod($filename = '')
    {
        $stat = @stat(dirname($filename));
        $perms = $stat['mode'] & 0666;
        // Remove execute bits for files
        if (@chmod($filename, $perms)) {
            return true;
        }
        return false;
    }
    function _delete_gallery_directory($abspath)
    {
        // Remove all image files and purge all empty directories left over
        $iterator = new DirectoryIterator($abspath);
        // Only delete image files! Other files may be stored incorrectly but it's not our place to delete them
        $removable_extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
        foreach ($removable_extensions as $extension) {
            $removable_extensions[] = $extension . '_backup';
        }
        foreach ($iterator as $file) {
            if (in_array($file->getBasename(), ['.', '..'])) {
                continue;
            } elseif ($file->isFile() || $file->isLink()) {
                $extension = strtolower(pathinfo($file->getPathname(), PATHINFO_EXTENSION));
                if (in_array($extension, $removable_extensions, true)) {
                    @unlink($file->getPathname());
                }
            } elseif ($file->isDir()) {
                $this->object->_delete_gallery_directory($file->getPathname());
            }
        }
        // DO NOT remove directories that still have files in them. Note: '.' and '..' are included with getSize()
        $empty = true;
        foreach ($iterator as $file) {
            if (in_array($file->getBasename(), ['.', '..'])) {
                continue;
            }
            $empty = false;
        }
        if ($empty) {
            @rmdir($iterator->getPath());
        }
    }
    /**
     * @param C_Image[]|int[] $images
     * @param C_Gallery|int   $dst_gallery
     * @return int[]
     */
    function copy_images($images, $dst_gallery)
    {
        $retval = [];
        // Ensure that the image ids we have are valid
        $image_mapper = C_Image_Mapper::get_instance();
        foreach ($images as $image) {
            if (is_numeric($image)) {
                $image = $image_mapper->find($image);
            }
            $image_abspath = $this->object->get_image_abspath($image, 'backup') ?: $this->object->get_image_abspath($image);
            if ($image_abspath) {
                // Import the image; this will copy the main file
                $new_image_id = $this->object->import_image_file($dst_gallery, $image_abspath, $image->filename);
                if ($new_image_id) {
                    // Copy the properties of the old image
                    $new_image = $image_mapper->find($new_image_id);
                    foreach (get_object_vars($image) as $key => $value) {
                        if (in_array($key, ['pid', 'galleryid', 'meta_data', 'filename', 'sortorder', 'extras_post_id'])) {
                            continue;
                        }
                        $new_image->{$key} = $value;
                    }
                    $image_mapper->save($new_image);
                    // Copy tags
                    $tags = wp_get_object_terms($image->pid, 'ngg_tag', 'fields=ids');
                    $tags = array_map('intval', $tags);
                    wp_set_object_terms($new_image_id, $tags, 'ngg_tag', true);
                    // Copy all of the generated versions (resized versions, watermarks, etc)
                    foreach ($this->object->get_image_sizes($image) as $named_size) {
                        if (in_array($named_size, ['full', 'thumbnail'])) {
                            continue;
                        }
                        $old_abspath = $this->object->get_image_abspath($image, $named_size);
                        $new_abspath = $this->object->get_image_abspath($new_image, $named_size);
                        if (is_array(@stat($old_abspath))) {
                            $new_dir = dirname($new_abspath);
                            // Ensure the target directory exists
                            if (@stat($new_dir) === false) {
                                wp_mkdir_p($new_dir);
                            }
                            @copy($old_abspath, $new_abspath);
                        }
                    }
                    // Mark as done
                    $retval[] = $new_image_id;
                }
            }
        }
        return $retval;
    }
    /**
     * Moves images from to another gallery
     *
     * @param array      $images
     * @param int|object $gallery
     * @return int[]
     */
    function move_images($images, $gallery)
    {
        $retval = $this->object->copy_images($images, $gallery);
        if ($images) {
            foreach ($images as $image_id) {
                $this->object->delete_image($image_id);
            }
        }
        return $retval;
    }
    /**
     * @param string $abspath
     * @return bool
     */
    function delete_directory($abspath)
    {
        $retval = false;
        if (@file_exists($abspath)) {
            $files = scandir($abspath);
            array_shift($files);
            array_shift($files);
            foreach ($files as $file) {
                $file_abspath = implode(DIRECTORY_SEPARATOR, [rtrim($abspath, '/\\'), $file]);
                if (is_dir($file_abspath)) {
                    $this->object->delete_directory($file_abspath);
                } else {
                    unlink($file_abspath);
                }
            }
            rmdir($abspath);
            $retval = @file_exists($abspath);
        }
        return $retval;
    }
    function delete_gallery($gallery)
    {
        $fs = C_Fs::get_instance();
        $safe_dirs = [DIRECTORY_SEPARATOR, $fs->get_document_root('plugins'), $fs->get_document_root('plugins_mu'), $fs->get_document_root('templates'), $fs->get_document_root('stylesheets'), $fs->get_document_root('content'), $fs->get_document_root('galleries'), $fs->get_document_root()];
        $abspath = $this->object->get_gallery_abspath($gallery);
        if ($abspath && file_exists($abspath) && !in_array(stripslashes($abspath), $safe_dirs)) {
            $this->object->_delete_gallery_directory($abspath);
        }
    }
    /**
     * @param int|C_Image  $image
     * @param string|FALSE $size
     * @return bool
     */
    function delete_image($image, $size = false)
    {
        $retval = false;
        // Ensure that we have the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            $image_id = $image->{$image->id_field};
            do_action('ngg_delete_image', $image_id, $size);
            // Delete only a particular image size
            if ($size) {
                $abspath = $this->object->get_image_abspath($image, $size);
                if ($abspath && @file_exists($abspath)) {
                    @unlink($abspath);
                }
                if (isset($image->meta_data) && isset($image->meta_data[$size])) {
                    unset($image->meta_data[$size]);
                    $this->object->_image_mapper->save($image);
                }
            } else {
                foreach ($this->object->get_image_sizes($image) as $named_size) {
                    $image_abspath = $this->object->get_image_abspath($image, $named_size);
                    @unlink($image_abspath);
                }
                // Delete the entity
                $this->object->_image_mapper->destroy($image);
            }
            $retval = true;
        }
        return $retval;
    }
    /**
     * Recover image from backup copy and reprocess it
     *
     * @param int|stdClass|C_Image $image
     * @return bool|string result code
     */
    function recover_image($image)
    {
        $retval = false;
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        if ($image) {
            $full_abspath = $this->object->get_image_abspath($image);
            $backup_abspath = $this->object->get_image_abspath($image, 'backup');
            if ($backup_abspath != $full_abspath && @file_exists($backup_abspath)) {
                if (is_writable($full_abspath) && is_writable(dirname($full_abspath))) {
                    // Copy the backup
                    if (@copy($backup_abspath, $full_abspath)) {
                        // Backup images are not altered at all; we must re-correct the EXIF/Orientation tag
                        $this->object->correct_exif_rotation($image, true);
                        // Re-create non-fullsize image sizes
                        foreach ($this->object->get_image_sizes($image) as $named_size) {
                            if (in_array($named_size, ['full', 'backup'])) {
                                continue;
                            }
                            // Reset thumbnail cropping set by 'Edit thumb' dialog
                            if ($named_size === 'thumbnail') {
                                unset($image->meta_data[$named_size]['crop_frame']);
                            }
                            $thumbnail = $this->object->generate_image_clone($full_abspath, $this->object->get_image_abspath($image, $named_size), $this->object->get_image_size_params($image, $named_size));
                            if ($thumbnail) {
                                $thumbnail->destruct();
                            }
                        }
                        do_action('ngg_recovered_image', $image);
                        // Reimport all metadata
                        $retval = $this->object->_image_mapper->reimport_metadata($image);
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Copies a NGG image to the media library and returns the attachment_id
     *
     * @param C_Image|int|stdClass $image
     * @return FALSE|int attachment_id
     */
    function copy_to_media_library($image)
    {
        $retval = false;
        // Get the image
        if (is_int($image)) {
            $imageId = $image;
            $mapper = C_Image_Mapper::get_instance();
            $image = $mapper->find($imageId);
        }
        if ($image) {
            $subdir = apply_filters('ngg_import_to_media_library_subdir', 'nggallery_import');
            $wordpress_upload_dir = wp_upload_dir();
            $path = $wordpress_upload_dir['path'] . DIRECTORY_SEPARATOR . $subdir;
            if (!file_exists($path)) {
                wp_mkdir_p($path);
            }
            $image_abspath = self::get_instance()->get_image_abspath($image, 'full');
            $new_file_path = $path . DIRECTORY_SEPARATOR . $image->filename;
            $image_data = getimagesize($image_abspath);
            $new_file_mime = $image_data['mime'];
            $i = 1;
            while (file_exists($new_file_path)) {
                ++$i;
                $new_file_path = $path . DIRECTORY_SEPARATOR . $i . '-' . $image->filename;
            }
            if (@copy($image_abspath, $new_file_path)) {
                $upload_id = wp_insert_attachment(['guid' => $new_file_path, 'post_mime_type' => $new_file_mime, 'post_title' => preg_replace('/\\.[^.]+$/', '', $image->alttext), 'post_content' => '', 'post_status' => 'inherit'], $new_file_path);
                update_post_meta($upload_id, '_ngg_image_id', intval($image->pid));
                // wp_generate_attachment_metadata() comes from this file
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $image_meta = wp_generate_attachment_metadata($upload_id, $new_file_path);
                // Generate and save the attachment metas into the database
                wp_update_attachment_metadata($upload_id, $image_meta);
                $retval = $upload_id;
            }
        }
        return $retval;
    }
    /**
     * Delete the given NGG image from the media library
     *
     * @var int|stdClass $imageId
     */
    function delete_from_media_library($imageId)
    {
        // Get the image
        if (!is_int($imageId)) {
            $image = $imageId;
            $imageId = $image->pid;
        }
        if ($postId = $this->object->is_in_media_library($imageId)) {
            wp_delete_post($postId);
        }
    }
    /**
     * Determines if the given NGG image id has been uploaded to the media library
     *
     * @param integer $imageId
     * @return FALSE|int attachment_id
     */
    function is_in_media_library($imageId)
    {
        $retval = false;
        // Get the image
        if (is_object($imageId)) {
            $image = $imageId;
            $imageId = $image->pid;
        }
        // Try to find an attachment for the given image_id
        if ($imageId) {
            $query = new WP_Query(['post_type' => 'attachment', 'meta_key' => '_ngg_image_id', 'meta_value_num' => $imageId]);
            foreach ($query->get_posts() as $post) {
                $retval = $post->ID;
            }
        }
        return $retval;
    }
    /**
     * @param string $filename
     * @return bool
     */
    public function is_allowed_image_extension($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        $allowed_extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
        foreach ($allowed_extensions as $extension) {
            $allowed_extensions[] = $extension . '_backup';
        }
        return in_array($extension, $allowed_extensions);
    }
    function is_current_user_over_quota()
    {
        $retval = false;
        $settings = C_NextGen_Settings::get_instance();
        if (is_multisite() && $settings->get('wpmuQuotaCheck')) {
            require_once ABSPATH . 'wp-admin/includes/ms.php';
            $retval = upload_is_user_over_quota(false);
        }
        return $retval;
    }
    /**
     * @param string? $filename
     * @return bool
     */
    function is_image_file($filename = null)
    {
        $retval = false;
        if (!$filename && isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $filename = $_FILES['file']['tmp_name'];
        }
        $allowed_mime = apply_filters('ngg_allowed_mime_types', NGG_DEFAULT_ALLOWED_MIME_TYPES);
        // If we can, we'll verify the mime type
        if (function_exists('exif_imagetype')) {
            if (($image_type = @exif_imagetype($filename)) !== false) {
                $retval = in_array(image_type_to_mime_type($image_type), $allowed_mime);
            }
        } else {
            $file_info = @getimagesize($filename);
            if (isset($file_info[2])) {
                $retval = in_array(image_type_to_mime_type($file_info[2]), $allowed_mime);
            }
        }
        return $retval;
    }
    function is_zip()
    {
        $retval = false;
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file_info = $_FILES['file'];
            if (isset($file_info['type'])) {
                $type = $file_info['type'];
                $type_parts = explode('/', $type);
                if (strtolower($type_parts[0]) == 'application') {
                    $spec = $type_parts[1];
                    $spec_parts = explode('-', $spec);
                    $spec_parts = array_map('strtolower', $spec_parts);
                    if (in_array($spec, ['zip', 'octet-stream']) || in_array('zip', $spec_parts)) {
                        $retval = true;
                    }
                }
            }
        }
        return $retval;
    }
    function maybe_base64_decode($data)
    {
        $decoded = base64_decode($data);
        if ($decoded === false) {
            return $data;
        } elseif (base64_encode($decoded) == $data) {
            return base64_decode($data);
        }
        return $data;
    }
    function get_unique_abspath($file_abspath)
    {
        $filename = basename($file_abspath);
        $dir_abspath = dirname($file_abspath);
        $num = 1;
        $pattern = path_join($dir_abspath, "*_{$filename}");
        if ($found = glob($pattern)) {
            natsort($found);
            $last = array_pop($found);
            $last = basename($last);
            if (preg_match('/^(\\d+)_/', $last, $match)) {
                $num = intval($match[1]) + 1;
            }
        }
        return path_join($dir_abspath, "{$num}_{$filename}");
    }
    function sanitize_filename_for_db($filename = null)
    {
        $filename = $filename ? $filename : uniqid('nextgen-gallery');
        $filename = preg_replace('#^/#', '', $filename);
        $filename = sanitize_file_name($filename);
        if (preg_match('/\\-(png|jpg|gif|jpeg|jpg_backup)$/i', $filename, $match)) {
            $filename = str_replace($match[0], '.' . $match[1], $filename);
        }
        return $filename;
    }
    /**
     * Determines whether a WebP image is animated which GD does not support.
     *
     * @see https://developers.google.com/speed/webp/docs/riff_container
     * @param string $filename
     * @return bool
     */
    public function is_animated_webp($filename)
    {
        $retval = false;
        $handle = fopen($filename, 'rb');
        fseek($handle, 12);
        if (fread($handle, 4) === 'VP8X') {
            fseek($handle, 20);
            $flag = fread($handle, 1);
            $retval = (bool) (ord($flag) >> 1 & 1);
        }
        fclose($handle);
        return $retval;
    }
    function import_image_file($dst_gallery, $image_abspath, $filename = null, $image = false, $override = false, $move = false)
    {
        $image_abspath = wp_normalize_path($image_abspath);
        if ($this->object->is_current_user_over_quota()) {
            $message = sprintf(__('Sorry, you have used your space allocation. Please delete some files to upload more files.', 'nggallery'));
            throw new E_NoSpaceAvailableException($message);
        }
        // Do we have a gallery to import to?
        if ($dst_gallery) {
            // Get the gallery abspath. This is where we will put the image files
            $gallery_abspath = $this->object->get_gallery_abspath($dst_gallery);
            // If we can't write to the directory, then there's no point in continuing
            if (!@file_exists($gallery_abspath)) {
                @wp_mkdir_p($gallery_abspath);
            }
            if (!is_writable($gallery_abspath)) {
                throw new E_InsufficientWriteAccessException(false, $gallery_abspath, false);
            }
            // Sanitize the filename for storing in the DB
            $filename = $this->sanitize_filename_for_db($filename);
            // Ensure that the filename is valid
            $extensions = apply_filters('ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES);
            $extensions[] = '_backup';
            $ext_list = implode('|', $extensions);
            if (!preg_match("/({$ext_list})\$/i", $filename)) {
                throw new E_UploadException(__('Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery'));
            }
            // GD does not support animated WebP and will generate a fatal error when we try to create thumbnails or resize
            if ($this->is_animated_webp($image_abspath)) {
                throw new E_UploadException(__('Animated WebP images are not supported.', 'nggallery'));
            }
            // Compute the destination folder
            $new_image_abspath = path_join($gallery_abspath, $filename);
            // Are the src and dst the same? If so, we don't have to copy or move files
            if ($image_abspath != $new_image_abspath) {
                // If we're not to override, ensure that the filename is unique
                if (!$override && @file_exists($new_image_abspath)) {
                    $new_image_abspath = $this->object->get_unique_abspath($new_image_abspath);
                    $filename = $this->sanitize_filename_for_db(basename($new_image_abspath));
                }
                // Try storing the file
                $copied = copy($image_abspath, $new_image_abspath);
                if ($copied && $move) {
                    unlink($image_abspath);
                }
                // Ensure that we're not vulerable to CVE-2017-2416 exploit
                if (($dimensions = getimagesize($new_image_abspath)) !== false) {
                    if (isset($dimensions[0]) && intval($dimensions[0]) > 30000 || isset($dimensions[1]) && intval($dimensions[1]) > 30000) {
                        unlink($new_image_abspath);
                        throw new E_UploadException(__('Image file too large. Maximum image dimensions supported are 30k x 30k.'));
                    }
                }
            }
            // Save the image in the DB
            $image_mapper = C_Image_Mapper::get_instance();
            $image_mapper->_use_cache = false;
            if ($image) {
                if (is_numeric($image)) {
                    $image = $image_mapper->find($image);
                }
            }
            if (!$image) {
                $image = $image_mapper->create();
            }
            $image->alttext = preg_replace('#\\.\\w{2,4}$#', '', $filename);
            $image->galleryid = is_numeric($dst_gallery) ? $dst_gallery : $dst_gallery->gid;
            $image->filename = $filename;
            $image->image_slug = nggdb::get_unique_slug(sanitize_title_with_dashes($image->alttext), 'image');
            $image_id = $image_mapper->save($image);
            if (!$image_id) {
                $exception = '';
                foreach ($image->get_errors() as $field => $errors) {
                    foreach ($errors as $error) {
                        if (!empty($exception)) {
                            $exception .= '<br/>';
                        }
                        $exception .= __(sprintf('Error while uploading %s: %s', $filename, $error), 'nextgen-gallery');
                    }
                }
                throw new E_UploadException($exception);
            }
            // Important: do not remove this line. The image mapper's save() routine imports metadata
            // meaning we must re-acquire a new $image object after saving it above; if we do not our
            // existing $image object will lose any metadata retrieved during said save() method.
            $image = $image_mapper->find($image_id);
            $image_mapper->_use_cache = true;
            $settings = C_NextGen_Settings::get_instance();
            // Backup the image
            if ($settings->get('imgBackup', false)) {
                $this->object->backup_image($image, true);
            }
            // Most browsers do not honor EXIF's Orientation header: rotate the image to prevent display issues
            $this->object->correct_exif_rotation($image, true);
            // Create resized version of image
            if ($settings->get('imgAutoResize', false)) {
                $this->object->generate_resized_image($image, true);
            }
            // Generate a thumbnail for the image
            $this->object->generate_thumbnail($image);
            // Set gallery preview image if missing
            C_Gallery_Mapper::get_instance()->set_preview_image($dst_gallery, $image_id, true);
            // Automatically watermark the main image if requested
            if ($settings->get('watermark_automatically_at_upload', 0)) {
                $image_abspath = $this->object->get_image_abspath($image, 'full');
                $this->object->generate_image_clone($image_abspath, $image_abspath, ['watermark' => true]);
            }
            // Notify other plugins that an image has been added
            do_action('ngg_added_new_image', $image);
            // delete dirsize after adding new images
            delete_transient('dirsize_cache');
            // Seems redundant to above hook. Maintaining for legacy purposes
            do_action('ngg_after_new_images_added', is_numeric($dst_gallery) ? $dst_gallery : $dst_gallery->gid, [$image_id]);
            return $image_id;
        } else {
            throw new E_EntityNotFoundException();
        }
        return null;
    }
    /**
     * Uploads base64 file to a gallery
     *
     * @param int|stdClass|C_Gallery  $gallery
     * @param string                  $data base64-encoded string of data representing the image
     * @param string|false (optional) $filename specifies the name of the file
     * @param int|false               $image_id (optional)
     * @param bool                    $override (optional)
     * @return bool|int
     */
    function upload_base64_image($gallery, $data, $filename = false, $image_id = false, $override = false, $move = false)
    {
        try {
            $temp_abspath = tempnam(sys_get_temp_dir(), '');
            // Try writing the image
            $fp = fopen($temp_abspath, 'wb');
            fwrite($fp, $this->maybe_base64_decode($data));
            fclose($fp);
        } catch (E_UploadException $ex) {
            throw $ex;
        }
        return $this->object->import_image_file($gallery, $temp_abspath, $filename, $image_id, $override, $move);
    }
    /**
     * Uploads an image for a particular gallery
     *
     * @param int|object|C_Gallery $gallery
     * @param string|bool          $filename (optional) Specifies the name of the file
     * @param string|bool          $data (optional) If specified, expects base64 encoded string of data
     * @return C_Image
     */
    function upload_image($gallery, $filename = false, $data = false)
    {
        $retval = null;
        // Ensure that we have the data present that we require
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            // $_FILES = Array(
            // [file] =>  Array (
            // [name] => Canada_landscape4.jpg
            // [type] => image/jpeg
            // [tmp_name] => /private/var/tmp/php6KO7Dc
            // [error] => 0
            // [size] => 64975
            // )
            //
            $file = $_FILES['file'];
            if ($this->object->is_zip()) {
                $retval = $this->object->upload_zip($gallery);
            } elseif ($this->is_image_file()) {
                $retval = $this->object->import_image_file($gallery, $file['tmp_name'], $filename ? $filename : (isset($file['name']) ? $file['name'] : false), false, false, true);
            } else {
                // Remove the non-valid (and potentially insecure) file from the PHP upload directory
                if (isset($_FILES['file']['tmp_name'])) {
                    $filename = $_FILES['file']['tmp_name'];
                    @unlink($filename);
                }
                throw new E_UploadException(__('Invalid image file. Acceptable formats: JPG, GIF, and PNG.', 'nggallery'));
            }
        } elseif ($data) {
            $retval = $this->object->upload_base64_image($gallery, $data, $filename);
        } else {
            throw new E_UploadException();
        }
        return $retval;
    }
    /**
     * @param int $gallery_id
     * @return array|bool
     */
    function upload_zip($gallery_id)
    {
        if (!$this->object->is_zip()) {
            return false;
        }
        $retval = false;
        $memory_limit = intval(ini_get('memory_limit'));
        if (!extension_loaded('suhosin') && $memory_limit < 256) {
            @ini_set('memory_limit', '256M');
        }
        $fs = C_Fs::get_instance();
        // Uses the WordPress ZIP abstraction API
        include_once $fs->join_paths(ABSPATH, 'wp-admin', 'includes', 'file.php');
        WP_Filesystem(false, get_temp_dir(), true);
        // Ensure that we truly have the gallery id
        $gallery_id = $this->object->_get_gallery_id($gallery_id);
        $zipfile = $_FILES['file']['tmp_name'];
        $dest_path = implode(DIRECTORY_SEPARATOR, [rtrim(get_temp_dir(), '/\\'), 'unpacked-' . M_I18n::mb_basename($zipfile)]);
        // Attempt to extract the zip file into the normal system directory
        $extracted = $this->object->extract_zip($zipfile, $dest_path);
        // Now verify it worked. get_temp_dir() will check each of the following directories to ensure they are
        // a directory and against wp_is_writable(). Should ALL of those options fail we will fallback to wp_upload_dir().
        //
        // WP_TEMP_DIR
        // sys_get_temp_dir()
        // ini/upload_tmp_dir
        // WP_CONTENT_DIR
        // /tmp
        $size = 0;
        $files = glob($dest_path . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            if (is_array(stat($file))) {
                $size += filesize($file);
            }
        }
        // Extraction failed; attempt again with wp_upload_dir()
        if ($size == 0) {
            // Remove the empty directory we may have possibly created but could not write to
            $this->object->delete_directory($dest_path);
            $destination = wp_upload_dir();
            $destination_path = $destination['basedir'];
            $dest_path = implode(DIRECTORY_SEPARATOR, [rtrim($destination_path, '/\\'), rand(), 'unpacked-' . M_I18n::mb_basename($zipfile)]);
            $extracted = $this->object->extract_zip($zipfile, $dest_path);
        }
        if ($extracted) {
            $retval = $this->object->import_gallery_from_fs($dest_path, $gallery_id);
        }
        $this->object->delete_directory($dest_path);
        if (!extension_loaded('suhosin')) {
            @ini_set('memory_limit', $memory_limit . 'M');
        }
        return $retval;
    }
    /**
     * @param string $zipfile
     * @param string $dest_path
     * @return bool FALSE on failure
     */
    public function extract_zip($zipfile, $dest_path)
    {
        wp_mkdir_p($dest_path);
        if (class_exists('ZipArchive', false) && apply_filters('unzip_file_use_ziparchive', true)) {
            $zipObj = new ZipArchive();
            if ($zipObj->open($zipfile) === false) {
                return false;
            }
            for ($i = 0; $i < $zipObj->numFiles; $i++) {
                $filename = $zipObj->getNameIndex($i);
                if (!$this->object->is_allowed_image_extension($filename)) {
                    continue;
                }
                $zipObj->extractTo($dest_path, [$zipObj->getNameIndex($i)]);
            }
        } else {
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
            $zipObj = new PclZip($zipfile);
            $zipContent = $zipObj->listContent();
            $indexesToExtract = [];
            foreach ($zipContent as $zipItem) {
                if ($zipItem['folder']) {
                    continue;
                }
                if (!$this->object->is_allowed_image_extension($zipItem['stored_filename'])) {
                    continue;
                }
                $indexesToExtract[] = $zipItem['index'];
            }
            if (!$zipObj->extractByIndex(implode(',', $indexesToExtract), $dest_path)) {
                return false;
            }
        }
        return true;
    }
}
/**
 * This is unused and can be removed when the minimum supported Pro version has an API level of 4.0 or higher.
 *
 * @package NextGEN Gallery
 */
/**
 * This class exists to prevent potential fatal errors being generated when parsing the ecommerce module class.
 *
 * @todo This can be removed once Pro's API level is 4.0 or higher.
 */
class C_NextGEN_Wizard_Manager extends C_Component
{
    /**
     * Unused.
     *
     * @var array Unused.
     */
    public static $_instances = array();
    /**
     * Unused.
     *
     * @param string $context Unused.
     * @return mixed
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_NextGEN_Wizard_Manager($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * gd.thumbnail.inc.php
 *
 * @author      Ian Selby (ian@gen-x-design.com)
 * @copyright   Copyright 2006-2011
 * @version     1.3.0 (based on 1.1.3)
 * @modded      by Alex Rabe
 */
/**
 * PHP class for dynamically resizing, cropping, and rotating images for thumbnail purposes and either displaying them on-the-fly or saving them.
 */
class C_NggLegacy_Thumbnail
{
    /**
     * Error message to display, if any
     *
     * @var string
     */
    var $errmsg;
    /**
     * Whether or not there is an error
     *
     * @var boolean
     */
    var $error;
    /**
     * Format of the image file
     *
     * @var string
     */
    var $format;
    /**
     * File name and path of the image file
     *
     * @var string
     */
    var $fileName;
    /**
     * Current dimensions of working image
     *
     * @var array
     */
    var $currentDimensions;
    /**
     * New dimensions of working image
     *
     * @var array
     */
    var $newDimensions;
    /**
     * Image resource for newly manipulated image
     *
     * @var resource
     * @access private
     */
    var $newImage;
    /**
     * Image resource for image before previous manipulation
     *
     * @var resource
     * @access private
     */
    var $oldImage;
    /**
     * Image resource for image being currently manipulated
     *
     * @var resource
     * @access private
     */
    var $workingImage;
    /**
     * Percentage to resize image by
     *
     * @var int
     * @access private
     */
    var $percent;
    /**
     * Maximum width of image during resize
     *
     * @var int
     * @access private
     */
    var $maxWidth;
    /**
     * Maximum height of image during resize
     *
     * @var int
     * @access private
     */
    var $maxHeight;
    /**
     * Image for Watermark
     *
     * @var string
     */
    var $watermarkImgPath;
    /**
     * Text for Watermark
     *
     * @var string
     */
    var $watermarkText;
    /**
     * Image Resource ID for Watermark
     *
     * @var string
     */
    function __construct($fileName, $no_ErrorImage = false)
    {
        // make sure the GD library is installed
        if (!function_exists('gd_info')) {
            echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
            echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
            throw new E_No_Image_Library_Exception();
        }
        // initialize variables
        $this->errmsg = '';
        $this->error = false;
        $this->currentDimensions = [];
        $this->newDimensions = [];
        $this->fileName = $fileName;
        $this->percent = 100;
        $this->maxWidth = 0;
        $this->maxHeight = 0;
        $this->watermarkImgPath = '';
        $this->watermarkText = '';
        // check to see if file exists
        if (!@file_exists($this->fileName)) {
            $this->errmsg = 'File not found';
            $this->error = true;
        } elseif (!is_readable($this->fileName)) {
            $this->errmsg = 'File is not readable';
            $this->error = true;
        }
        $image_size = null;
        // if there are no errors, determine the file format
        if ($this->error == false) {
            // set_time_limit(30);
            @ini_set('memory_limit', -1);
            $image_size = @getimagesize($this->fileName);
            if (isset($image_size) && is_array($image_size)) {
                $extensions = [IMAGETYPE_GIF => 'GIF', IMAGETYPE_JPEG => 'JPG', IMAGETYPE_PNG => 'PNG', IMAGETYPE_WEBP => 'WEBP'];
                $extension = array_key_exists($image_size[2], $extensions) ? $extensions[$image_size[2]] : '';
                if ($extension) {
                    $this->format = $extension;
                } else {
                    $this->errmsg = 'Unknown file format';
                    $this->error = true;
                }
            } else {
                $this->errmsg = 'File is not an image';
                $this->error = true;
            }
        }
        // increase memory-limit if possible, GD needs this for large images
        if (!extension_loaded('suhosin')) {
            @ini_set('memory_limit', '512M');
        }
        if ($this->error == false) {
            // Check memory consumption if file exists
            $this->checkMemoryForImage($this->fileName);
        }
        // initialize resources if no errors
        if ($this->error == false) {
            $img_err = null;
            switch ($this->format) {
                case 'GIF':
                    if (function_exists('ImageCreateFromGif')) {
                        $this->oldImage = @ImageCreateFromGif($this->fileName);
                    } else {
                        $img_err = __('Support for GIF format is missing.', 'nggallery');
                    }
                    break;
                case 'JPG':
                    if (function_exists('ImageCreateFromJpeg')) {
                        $this->oldImage = @ImageCreateFromJpeg($this->fileName);
                    } else {
                        $img_err = __('Support for JPEG format is missing.', 'nggallery');
                    }
                    break;
                case 'PNG':
                    if (function_exists('ImageCreateFromPng')) {
                        $this->oldImage = @ImageCreateFromPng($this->fileName);
                    } else {
                        $img_err = __('Support for PNG format is missing.', 'nggallery');
                    }
                    break;
                case 'WEBP':
                    if (function_exists('imagecreatefromwebp')) {
                        $this->oldImage = @imagecreatefromwebp($this->fileName);
                    } else {
                        $img_err = __('Support for WEBP format is missing.', 'nggallery');
                    }
                    break;
            }
            if (!$this->oldImage) {
                if ($img_err == null) {
                    $img_err = __('Check memory limit', 'nggallery');
                }
                $this->errmsg = sprintf(__('Create Image failed. %1$s', 'nggallery'), $img_err);
                $this->error = true;
            } else {
                $this->currentDimensions = ['width' => $image_size[0], 'height' => $image_size[1]];
                $this->newImage = $this->oldImage;
            }
        }
        if ($this->error == true) {
            if (!$no_ErrorImage) {
                $this->showErrorImage();
            }
            return;
        }
    }
    /**
     * Calculate the memory limit
     *
     * @param string $filename
     */
    function checkMemoryForImage($filename)
    {
        $imageInfo = getimagesize($filename);
        switch ($this->format) {
            case 'GIF':
                // measured factor 1 is better
                $CHANNEL = 1;
                break;
            case 'JPG':
                $CHANNEL = $imageInfo['channels'];
                break;
            case 'PNG':
                // didn't get the channel for png
                $CHANNEL = 3;
                break;
            case 'WEBP':
                $CHANNEL = $imageInfo['bits'];
                break;
        }
        $bits = !empty($imageInfo['bits']) ? $imageInfo['bits'] : 32;
        // imgInfo[bits] is not always available
        return $this->checkMemoryForData($imageInfo[0], $imageInfo[1], $CHANNEL, $bits);
    }
    function checkMemoryForData($width, $height, $channels = null, $bits = null)
    {
        $imageInfo = getimagesize($this->fileName);
        if ($channels == null) {
            switch ($this->format) {
                case 'GIF':
                    // measured factor 1 is better
                    $channels = 1;
                    break;
                case 'JPG':
                    $channels = $imageInfo['channels'];
                    break;
                case 'PNG':
                    // didn't get the channel for png
                    $channels = 3;
                    break;
                case 'WEBP':
                    $channels = $imageInfo['bits'];
                    break;
            }
        }
        if ($bits == null) {
            $bits = !empty($imageInfo['bits']) ? $imageInfo['bits'] : 32;
            // imgInfo[bits] is not always available
        }
        if (function_exists('memory_get_usage') && ini_get('memory_limit')) {
            $MB = 1048576;
            // number of bytes in 1M
            $K64 = 65536;
            // number of bytes in 64K
            $TWEAKFACTOR = 1.68;
            // Or whatever works for you
            $memoryNeeded = round((doubleval($width * $height * $bits * $channels) / 8 + $K64) * $TWEAKFACTOR);
            $memoryNeeded = memory_get_usage() + $memoryNeeded;
            // get memory limit
            $memory_limit = ini_get('memory_limit');
            // PHP docs : Note that to have no memory limit, set this directive to -1.
            if ($memory_limit == -1) {
                return true;
            }
            // Just check megabyte limits, not higher
            if (strtolower(substr($memory_limit, -1)) == 'm') {
                if ($memory_limit != '') {
                    $memory_limit = intval(substr($memory_limit, 0, -1)) * 1024 * 1024;
                }
                if ($memoryNeeded > $memory_limit) {
                    $memoryNeeded = round($memoryNeeded / 1024 / 1024, 2);
                    $this->errmsg = 'Exceed Memory limit. Require : ' . $memoryNeeded . ' MByte';
                    $this->error = true;
                    return false;
                }
            }
        }
        return true;
    }
    function __destruct()
    {
        $this->destruct();
    }
    /**
     * Must be called to free up allocated memory after all manipulations are done
     */
    function destruct()
    {
        if (is_resource($this->newImage) || $this->newImage instanceof GdImage) {
            @imagedestroy($this->newImage);
        }
        if (is_resource($this->oldImage) || $this->oldImage instanceof GdImage) {
            @imagedestroy($this->oldImage);
        }
        if (is_resource($this->workingImage) || $this->workingImage instanceof GdImage) {
            @imagedestroy($this->workingImage);
        }
    }
    /**
     * Returns the current width of the image
     *
     * @return int
     */
    function getCurrentWidth()
    {
        return $this->currentDimensions['width'];
    }
    /**
     * Returns the current height of the image
     *
     * @return int
     */
    function getCurrentHeight()
    {
        return $this->currentDimensions['height'];
    }
    /**
     * Calculates new image width
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcWidth($width, $height)
    {
        $newWp = 100 * $this->maxWidth / $width;
        $newHeight = $height * $newWp / 100;
        if (intval($newHeight) == $this->maxHeight - 1) {
            $newHeight = $this->maxHeight;
        }
        return ['newWidth' => intval($this->maxWidth), 'newHeight' => intval($newHeight)];
    }
    /**
     * Calculates new image height
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcHeight($width, $height)
    {
        $newHp = 100 * $this->maxHeight / $height;
        $newWidth = $width * $newHp / 100;
        if (intval($newWidth) == $this->maxWidth - 1) {
            $newWidth = $this->maxWidth;
        }
        return ['newWidth' => intval($newWidth), 'newHeight' => intval($this->maxHeight)];
    }
    /**
     * Calculates new image size based on percentage
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcPercent($width, $height, $percent = -1)
    {
        if ($percent == -1) {
            $percent = $this->percent;
        }
        $newWidth = $width * $percent / 100;
        $newHeight = $height * $percent / 100;
        return ['newWidth' => intval($newWidth), 'newHeight' => intval($newHeight)];
    }
    /**
     * Calculates new image size based on width and height, while constraining to maxWidth and maxHeight
     *
     * @param int $width
     * @param int $height
     */
    function calcImageSize($width, $height)
    {
        // $width and $height are the CURRENT image resolutions
        $ratio_w = $this->maxWidth / $width;
        $ratio_h = $this->maxHeight / $height;
        if ($ratio_w >= $ratio_h) {
            $width = $this->maxWidth;
            $height = (int) round($height * $ratio_h, 0);
        } else {
            $height = $this->maxHeight;
            $width = (int) round($width * $ratio_w, 0);
        }
        $this->newDimensions = ['newWidth' => $width, 'newHeight' => $height];
    }
    /**
     * Calculates new image size based percentage
     *
     * @param int $width
     * @param int $height
     */
    function calcImageSizePercent($width, $height)
    {
        if ($this->percent > 0) {
            $this->newDimensions = $this->calcPercent($width, $height);
        }
    }
    /**
     * Displays error image
     */
    function showErrorImage()
    {
        header('Content-type: image/png');
        $errImg = ImageCreate(220, 25);
        $bgColor = imagecolorallocate($errImg, 0, 0, 0);
        $fgColor1 = imagecolorallocate($errImg, 255, 255, 255);
        $fgColor2 = imagecolorallocate($errImg, 255, 0, 0);
        imagestring($errImg, 3, 6, 6, 'Error:', $fgColor2);
        imagestring($errImg, 3, 55, 6, $this->errmsg, $fgColor1);
        imagepng($errImg);
        imagedestroy($errImg);
    }
    /**
     * Resizes image to fixed Width x Height
     *
     * @param int $Width
     * @param int $Height
     * @param int $deprecated Unused
     */
    function resizeFix($Width = 0, $Height = 0, $deprecated = 3)
    {
        if (!$this->checkMemoryForData($Width, $Height)) {
            return;
        }
        $this->newWidth = $Width;
        $this->newHeight = $Height;
        if (function_exists('ImageCreateTrueColor')) {
            $this->workingImage = ImageCreateTrueColor($this->newWidth, $this->newHeight);
        } else {
            $this->workingImage = ImageCreate($this->newWidth, $this->newHeight);
        }
        // ImageCopyResampled(
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newWidth;
        $this->currentDimensions['height'] = $this->newHeight;
    }
    /**
     * Resizes image to maxWidth x maxHeight
     *
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $deprecated Unused
     */
    function resize($maxWidth = 0, $maxHeight = 0, $deprecated = 3)
    {
        if (!$this->checkMemoryForData($maxWidth, $maxHeight)) {
            return;
        }
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists('ImageCreateTrueColor')) {
            $this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        } else {
            $this->workingImage = ImageCreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        }
        // ImageCopyResampled(
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newDimensions['newWidth'], $this->newDimensions['newHeight'], $this->currentDimensions['width'], $this->currentDimensions['height']);
        ImageDestroy($this->oldImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newDimensions['newWidth'];
        $this->currentDimensions['height'] = $this->newDimensions['newHeight'];
    }
    /**
     * Resizes the image by $percent percent
     *
     * @param int $percent
     */
    function resizePercent($percent = 0)
    {
        $dims = $this->calcPercent($this->currentDimensions['width'], $this->currentDimensions['height'], $percent);
        if (!$this->checkMemoryForData($dims['newWidth'], $dims['newHeight'])) {
            return;
        }
        $this->percent = $percent;
        $this->calcImageSizePercent($this->currentDimensions['width'], $this->currentDimensions['height']);
        if (function_exists('ImageCreateTrueColor')) {
            $this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        } else {
            $this->workingImage = ImageCreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        }
        $this->ImageCopyResampled($this->workingImage, $this->oldImage, 0, 0, 0, 0, $this->newDimensions['newWidth'], $this->newDimensions['newHeight'], $this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $this->newDimensions['newWidth'];
        $this->currentDimensions['height'] = $this->newDimensions['newHeight'];
    }
    /**
     * Crops the image from calculated center in a square of $cropSize pixels
     *
     * @param int $cropSize
     */
    function cropFromCenter($cropSize)
    {
        if ($cropSize > $this->currentDimensions['width']) {
            $cropSize = $this->currentDimensions['width'];
        }
        if ($cropSize > $this->currentDimensions['height']) {
            $cropSize = $this->currentDimensions['height'];
        }
        $cropX = intval(($this->currentDimensions['width'] - $cropSize) / 2);
        $cropY = intval(($this->currentDimensions['height'] - $cropSize) / 2);
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists('ImageCreateTrueColor')) {
            $this->workingImage = ImageCreateTrueColor($cropSize, $cropSize);
        } else {
            $this->workingImage = ImageCreate($cropSize, $cropSize);
        }
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $cropX, $cropY, $cropSize, $cropSize, $cropSize, $cropSize);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $cropSize;
        $this->currentDimensions['height'] = $cropSize;
    }
    /**
     * Advanced cropping function that crops an image using $startX and $startY as the upper-left hand corner.
     *
     * @param int $startX
     * @param int $startY
     * @param int $width
     * @param int $height
     */
    function crop($startX, $startY, $width, $height)
    {
        if (!$this->checkMemoryForData($width, $height)) {
            return;
        }
        // make sure the cropped area is not greater than the size of the image
        if ($width > $this->currentDimensions['width']) {
            $width = $this->currentDimensions['width'];
        }
        if ($height > $this->currentDimensions['height']) {
            $height = $this->currentDimensions['height'];
        }
        // make sure not starting outside the image
        if ($startX + $width > $this->currentDimensions['width']) {
            $startX = $this->currentDimensions['width'] - $width;
        }
        if ($startY + $height > $this->currentDimensions['height']) {
            $startY = $this->currentDimensions['height'] - $height;
        }
        if ($startX < 0) {
            $startX = 0;
        }
        if ($startY < 0) {
            $startY = 0;
        }
        if ($this->workingImage != null && $this->workingImage != $this->oldImage) {
            ImageDestroy($this->workingImage);
            $this->workingImage = null;
        }
        if (function_exists('ImageCreateTrueColor')) {
            $this->workingImage = ImageCreateTrueColor($width, $height);
        } else {
            $this->workingImage = ImageCreate($width, $height);
        }
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $startX, $startY, $width, $height, $width, $height);
        ImageDestroy($this->oldImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $width;
        $this->currentDimensions['height'] = $height;
    }
    /**
     * Outputs the image to the screen, or saves to $name if supplied.  Quality of JPEG images can be controlled with the $quality variable
     *
     * @param int    $quality
     * @param string $name
     */
    function show($quality = 100, $name = '')
    {
        switch ($this->format) {
            case 'GIF':
                if ($name != '') {
                    @ImageGif($this->newImage, $name) or $this->error = true;
                } else {
                    header('Content-type: image/gif');
                    ImageGif($this->newImage);
                }
                break;
            case 'JPG':
                if ($name != '') {
                    @ImageJpeg($this->newImage, $name, $quality) or $this->error = true;
                } else {
                    header('Content-type: image/jpeg');
                    ImageJpeg($this->newImage, null, $quality);
                }
                break;
            case 'PNG':
                if ($name != '') {
                    @ImagePng($this->newImage, $name) or $this->error = true;
                } else {
                    header('Content-type: image/png');
                    ImagePng($this->newImage);
                }
                break;
            case 'WEBP':
                if ($name != '') {
                    $this->error = !@imagewebp($this->newImage, $name);
                } else {
                    header('Content-type: image/webp');
                    imagewebp($this->newImage);
                }
                break;
        }
    }
    /**
     * Saves image as $name (can include file path), with quality of # percent if file is a jpeg
     *
     * @param string $name
     * @param int    $quality
     * @return bool errorstate
     */
    function save($name, $quality = 100)
    {
        $this->show($quality, $name);
        if ($this->error == true) {
            $this->errmsg = 'Create Image failed. Check safe mode settings';
            return false;
        }
        if (function_exists('do_action')) {
            do_action('ngg_ajax_image_save', $name);
        }
        return true;
    }
    /**
     * Creates Apple-style reflection under image, optionally adding a border to main image
     *
     * @param int    $percent
     * @param int    $reflection
     * @param int    $white
     * @param bool   $border
     * @param string $borderColor
     */
    function createReflection($percent, $reflection, $white, $border = true, $borderColor = '#a4a4a4')
    {
        $width = $this->currentDimensions['width'];
        $height = $this->currentDimensions['height'];
        $reflectionHeight = intval($height * ($reflection / 100));
        $newHeight = $height + $reflectionHeight;
        $reflectedPart = $height * ($percent / 100);
        $this->workingImage = ImageCreateTrueColor($width, $newHeight);
        ImageAlphaBlending($this->workingImage, true);
        $colorToPaint = ImageColorAllocateAlpha($this->workingImage, 255, 255, 255, 0);
        ImageFilledRectangle($this->workingImage, 0, 0, $width, $newHeight, $colorToPaint);
        imagecopyresampled($this->workingImage, $this->newImage, 0, 0, 0, $reflectedPart, $width, $reflectionHeight, $width, $height - $reflectedPart);
        $this->imageFlipVertical();
        imagecopy($this->workingImage, $this->newImage, 0, 0, 0, 0, $width, $height);
        imagealphablending($this->workingImage, true);
        for ($i = 0; $i < $reflectionHeight; $i++) {
            $colorToPaint = imagecolorallocatealpha($this->workingImage, 255, 255, 255, ($i / $reflectionHeight * -1 + 1) * $white);
            imagefilledrectangle($this->workingImage, 0, $height + $i, $width, $height + $i, $colorToPaint);
        }
        if ($border == true) {
            $rgb = $this->hex2rgb($borderColor, false);
            $colorToPaint = imagecolorallocate($this->workingImage, $rgb[0], $rgb[1], $rgb[2]);
            imageline($this->workingImage, 0, 0, $width, 0, $colorToPaint);
            // top line
            imageline($this->workingImage, 0, $height, $width, $height, $colorToPaint);
            // bottom line
            imageline($this->workingImage, 0, 0, 0, $height, $colorToPaint);
            // left line
            imageline($this->workingImage, $width - 1, 0, $width - 1, $height, $colorToPaint);
            // right line
        }
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        $this->currentDimensions['width'] = $width;
        $this->currentDimensions['height'] = $newHeight;
    }
    /**
     * Flip an image.
     *
     * @param bool $horz flip the image in horizontal mode
     * @param bool $vert flip the image in vertical mode
     * @return true
     */
    function flipImage($horz = false, $vert = false)
    {
        $sx = $vert ? $this->currentDimensions['width'] - 1 : 0;
        $sy = $horz ? $this->currentDimensions['height'] - 1 : 0;
        $sw = $vert ? -$this->currentDimensions['width'] : $this->currentDimensions['width'];
        $sh = $horz ? -$this->currentDimensions['height'] : $this->currentDimensions['height'];
        $this->workingImage = imagecreatetruecolor($this->currentDimensions['width'], $this->currentDimensions['height']);
        $this->imagecopyresampled($this->workingImage, $this->oldImage, 0, 0, $sx, $sy, $this->currentDimensions['width'], $this->currentDimensions['height'], $sw, $sh);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        return true;
    }
    /**
     * Rotate an image clockwise or counter clockwise
     *
     * @param string $dir Either CW or CCW
     * @return bool
     */
    function rotateImage($dir = 'CW')
    {
        $angle = $dir == 'CW' ? 90 : -90;
        return $this->rotateImageAngle($angle);
    }
    /**
     * Rotate an image clockwise or counter clockwise
     *
     * @param int $angle Degrees to rotate the target image
     * @return bool
     */
    function rotateImageAngle($angle = 90)
    {
        if (function_exists('imagerotate')) {
            $this->currentDimensions['width'] = imagesx($this->workingImage);
            $this->currentDimensions['height'] = imagesy($this->workingImage);
            $this->oldImage = $this->workingImage;
            // imagerotate() rotates CCW ;
            // See for help: https://evertpot.com/115/
            $this->newImage = imagerotate($this->oldImage, 360 - $angle, 0);
            return true;
        }
        $this->workingImage = imagecreatetruecolor($this->currentDimensions['height'], $this->currentDimensions['width']);
        imagealphablending($this->workingImage, false);
        imagesavealpha($this->workingImage, true);
        switch ($angle) {
            case 90:
                for ($x = 0; $x < $this->currentDimensions['width']; $x++) {
                    for ($y = 0; $y < $this->currentDimensions['height']; $y++) {
                        if (!imagecopy($this->workingImage, $this->oldImage, $this->currentDimensions['height'] - $y - 1, $x, $x, $y, 1, 1)) {
                            return false;
                        }
                    }
                }
                break;
            case -90:
                for ($x = 0; $x < $this->currentDimensions['width']; $x++) {
                    for ($y = 0; $y < $this->currentDimensions['height']; $y++) {
                        if (!imagecopy($this->workingImage, $this->oldImage, $y, $this->currentDimensions['width'] - $x - 1, $x, $y, 1, 1)) {
                            return false;
                        }
                    }
                }
                break;
            default:
                return false;
        }
        $this->currentDimensions['width'] = imagesx($this->workingImage);
        $this->currentDimensions['height'] = imagesy($this->workingImage);
        $this->oldImage = $this->workingImage;
        $this->newImage = $this->workingImage;
        return true;
    }
    /**
     * Inverts working image, used by reflection function
     *
     * @access  private
     */
    function imageFlipVertical()
    {
        $x_i = imagesx($this->workingImage);
        $y_i = imagesy($this->workingImage);
        for ($x = 0; $x < $x_i; $x++) {
            for ($y = 0; $y < $y_i; $y++) {
                imagecopy($this->workingImage, $this->workingImage, $x, $y_i - $y - 1, $x, $y, 1, 1);
            }
        }
    }
    /**
     * Converts hexidecimal color value to rgb values and returns as array/string
     *
     * @param string $hex
     * @param bool   $asString
     * @return array|string
     */
    function hex2rgb($hex, $asString = false)
    {
        // strip off any leading #
        if (0 === strpos($hex, '#')) {
            $hex = substr($hex, 1);
        } elseif (0 === strpos($hex, '&H')) {
            $hex = substr($hex, 2);
        }
        // break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2) - 1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);
        // convert each tuple to decimal
        $rgb[0] = isset($rgb[0]) ? hexdec($rgb[0]) : 0;
        $rgb[1] = isset($rgb[1]) ? hexdec($rgb[1]) : 0;
        $rgb[2] = isset($rgb[2]) ? hexdec($rgb[2]) : 0;
        return $asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb;
    }
    /**
     * Based on the Watermark function by Marek Malcherek
     * http://www.malcherek.de
     *
     * @param string $color
     * @param string $wmFont
     * @param int    $wmSize
     * @param int    $wmOpaque
     */
    function watermarkCreateText($color, $wmFont, $wmSize = 10, $wmOpaque = 90)
    {
        if (empty($this->watermarkText)) {
            return;
        }
        if (!$color) {
            $color = '000000';
        }
        // set font path
        $wmFontPath = NGGALLERY_ABSPATH . 'fonts/' . $wmFont;
        if (!is_readable($wmFontPath)) {
            return;
        }
        // This function requires both the GD library and the FreeType library.
        if (!function_exists('ImageTTFBBox')) {
            return;
        }
        $words = preg_split('/ /', $this->watermarkText);
        $lines = [];
        $line = '';
        $watermark_image_width = 0;
        // attempt adding a new word until the width is too large; then start a new line and start again
        foreach ($words as $word) {
            // sanitize the text being input; imagettftext() can be sensitive
            $TextSize = $this->ImageTTFBBoxDimensions($wmSize, 0, $this->correct_gd_unc_path($wmFontPath), $line . preg_replace('~^(&([a-zA-Z0-9]);)~', htmlentities('${1}'), mb_convert_encoding($word, 'HTML-ENTITIES', 'UTF-8')));
            if ($watermark_image_width == 0) {
                $watermark_image_width = $TextSize['width'];
            }
            if ($TextSize['width'] > $this->newDimensions['newWidth']) {
                $lines[] = trim($line);
                $line = '';
            } elseif ($TextSize['width'] > $watermark_image_width) {
                $watermark_image_width = $TextSize['width'];
            }
            $line .= $word . ' ';
        }
        $lines[] = trim($line);
        // use this string to determine our largest possible line height
        $line_dimensions = $this->ImageTTFBBoxDimensions($wmSize, 0, $this->correct_gd_unc_path($wmFontPath), 'MXQJALYmxqjabdfghjklpqry019`@$^&*(,!132');
        $line_height = (float) $line_dimensions['height'] * 1.05;
        // Create an image to apply our text to
        $this->workingImage = ImageCreateTrueColor($watermark_image_width, (int) (count($lines) * $line_height));
        ImageSaveAlpha($this->workingImage, true);
        ImageAlphaBlending($this->workingImage, false);
        $bgText = imagecolorallocatealpha($this->workingImage, 255, 255, 255, 127);
        imagefill($this->workingImage, 0, 0, $bgText);
        $wmTransp = 127 - (int) $wmOpaque * 1.27;
        $rgb = $this->hex2rgb($color, false);
        $TextColor = imagecolorallocatealpha($this->workingImage, (int) $rgb[0], (int) $rgb[1], (int) $rgb[2], (int) $wmTransp);
        // Put text on the image, line-by-line
        $y_pos = $wmSize;
        foreach ($lines as $line) {
            imagettftext($this->workingImage, $wmSize, 0, 0, $y_pos, $TextColor, $this->correct_gd_unc_path($wmFontPath), $line);
            $y_pos += $line_height;
        }
        $this->watermarkImgPath = $this->workingImage;
        return;
    }
    /**
     * Returns a path that can be used with imagettftext() and ImageTTFBBox()
     *
     * imagettftext() and ImageTTFBBox() cannot load resources from Windows UNC paths
     * and require they be mangled to be like //server\filename instead of \\server\filename
     *
     * @param string $path Absolute file path
     * @return string $path Mangled absolute file path
     */
    public function correct_gd_unc_path($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && substr($path, 0, 2) === '\\\\') {
            $path = ltrim($path, '\\\\');
            $path = '//' . $path;
        }
        return $path;
    }
    /**
     * Calculates the width & height dimensions of ImageTTFBBox().
     *
     * Note: ImageTTFBBox() is unreliable with large font sizes
     *
     * @param $wmSize
     * @param $fontAngle
     * @param $wmFontPath
     * @param $text
     * @return array
     */
    function ImageTTFBBoxDimensions($wmSize, $fontAngle, $wmFontPath, $text)
    {
        $box = @ImageTTFBBox($wmSize, $fontAngle, $this->correct_gd_unc_path($wmFontPath), $text);
        $max_x = max([$box[0], $box[2], $box[4], $box[6]]);
        $max_y = max([$box[1], $box[3], $box[5], $box[7]]);
        $min_x = min([$box[0], $box[2], $box[4], $box[6]]);
        $min_y = min([$box[1], $box[3], $box[5], $box[7]]);
        return ['width' => $max_x - $min_x, 'height' => $max_y - $min_y];
    }
    function applyFilter($filterType)
    {
        $args = func_get_args();
        array_unshift($args, $this->newImage);
        return call_user_func_array('imagefilter', $args);
    }
    /**
     * Modfied Watermark function by Steve Peart
     * http://parasitehosting.com/
     *
     * @param string $relPOS
     * @param int    $xPOS
     * @param int    $yPOS
     */
    function watermarkImage($relPOS = 'botRight', $xPOS = 0, $yPOS = 0)
    {
        // if it's a resource ID take it as watermark text image
        if (is_resource($this->watermarkImgPath) || $this->watermarkImgPath instanceof GdImage) {
            $this->workingImage = $this->watermarkImgPath;
        } else {
            // (possibly) search for the file from the document root
            if (!is_file($this->watermarkImgPath)) {
                $fs = C_Fs::get_instance();
                if (is_file($fs->join_paths($fs->get_document_root('content'), $this->watermarkImgPath))) {
                    $this->watermarkImgPath = $fs->get_document_root('content') . $this->watermarkImgPath;
                }
            }
            // Would you really want to use anything other than a png?
            $this->workingImage = @imagecreatefrompng($this->watermarkImgPath);
            // if it's not a valid file die...
            if (empty($this->workingImage) or !$this->workingImage) {
                return;
            }
        }
        imagealphablending($this->workingImage, false);
        imagesavealpha($this->workingImage, true);
        $sourcefile_width = imageSX($this->oldImage);
        $sourcefile_height = imageSY($this->oldImage);
        $watermarkfile_width = imageSX($this->workingImage);
        $watermarkfile_height = imageSY($this->workingImage);
        switch (substr($relPOS, 0, 3)) {
            case 'top':
                $dest_y = 0 + $yPOS;
                break;
            case 'mid':
                $dest_y = $sourcefile_height / 2 - $watermarkfile_height / 2;
                break;
            case 'bot':
                $dest_y = $sourcefile_height - $watermarkfile_height - $yPOS;
                break;
            default:
                $dest_y = 0;
                break;
        }
        switch (substr($relPOS, 3)) {
            case 'Left':
                $dest_x = 0 + $xPOS;
                break;
            case 'Center':
                $dest_x = $sourcefile_width / 2 - $watermarkfile_width / 2;
                break;
            case 'Right':
                $dest_x = $sourcefile_width - $watermarkfile_width - $xPOS;
                break;
            default:
                $dest_x = 0;
                break;
        }
        // debug
        // $this->errmsg = 'X '.$dest_x.' Y '.$dest_y;
        // $this->showErrorImage();
        // if a gif, we have to upsample it to a truecolor image
        if ($this->format == 'GIF') {
            $tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);
            imagecopy($tempimage, $this->oldImage, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);
            $this->newImage = $tempimage;
        }
        $this->imagecopymerge_alpha($this->newImage, $this->workingImage, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, 100);
    }
    /**
     * Wrapper to imagecopymerge() that allows PNG transparency
     */
    function imagecopymerge_alpha($destination_image, $source_image, $destination_x, $destination_y, $source_x, $source_y, $source_w, $source_h, $pct)
    {
        $cut = imagecreatetruecolor($source_w, $source_h);
        imagecopy($cut, $destination_image, 0, 0, (int) $destination_x, (int) $destination_y, (int) $source_w, (int) $source_h);
        imagecopy($cut, $source_image, 0, 0, $source_x, $source_y, $source_w, $source_h);
        imagecopymerge($destination_image, $cut, (int) $destination_x, (int) $destination_y, 0, 0, (int) $source_w, (int) $source_h, (int) $pct);
    }
    /**
     * Modfied imagecopyresampled function to save transparent images
     * See : http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
     *
     * @since 1.9.0
     *
     * @param resource $dst_image
     * @param resource $src_image
     * @param int      $dst_x
     * @param int      $dst_y
     * @param int      $src_x
     * @param int      $src_y
     * @param int      $dst_w
     * @param int      $dst_h
     * @param int      $src_w
     * @param int      $src_h
     * @return bool
     */
    function imagecopyresampled(&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        // Check if this image is PNG or GIF, then set if Transparent
        if ($this->format == 'GIF' || $this->format == 'PNG') {
            imagealphablending($dst_image, false);
            imagesavealpha($dst_image, true);
            $transparent = imagecolorallocatealpha($dst_image, 255, 255, 255, 127);
            imagefilledrectangle($dst_image, 0, 0, $dst_w, $dst_h, $transparent);
        }
        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        return true;
    }
}
class C_Router_Wrapper extends Mixin
{
    protected static $instances = array();
    protected $context = false;
    public function __construct($context)
    {
        $this->context = $context;
    }
    public function __call($method, $args)
    {
        return \Imagely\NGG\Util\Router::get_instance($this->context)->{$method}(...$args);
    }
    static function get_instance($context = false)
    {
        if (!isset(self::$instances[$context])) {
            self::$instances[$context] = new C_Router_Wrapper($context);
        }
        return self::$instances[$context];
    }
}
/**
 * Provides methods to C_Gallery_Storage related to dynamic images, thumbnails, clones, etc
 *
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Dynamic extends Mixin
{
    /**
     * Generates a specific size for an image
     *
     * @param int|object|C_Image $image
     * @param string             $size
     * @param array|null         $params (optional)
     * @param bool               $skip_defaults (optional)
     * @return bool|object
     */
    function generate_image_size($image, $size, $params = null, $skip_defaults = false)
    {
        $retval = false;
        // Get the image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have a valid image
        if ($image) {
            $params = $this->object->get_image_size_params($image, $size, $params, $skip_defaults);
            $settings = C_NextGen_Settings::get_instance();
            // Get the image filename
            $filename = $this->object->get_image_abspath($image, 'original');
            $thumbnail = null;
            if ($size == 'full' && $settings->imgBackup == 1) {
                // XXX change this? 'full' should be the resized path and 'original' the _backup path
                $backup_path = $this->object->get_backup_abspath($image);
                if (!@file_exists($backup_path)) {
                    @copy($filename, $backup_path);
                }
            }
            // Generate the thumbnail using WordPress
            $existing_image_abpath = $this->object->get_image_abspath($image, $size);
            $existing_image_dir = dirname($existing_image_abpath);
            wp_mkdir_p($existing_image_dir);
            $clone_path = $existing_image_abpath;
            $thumbnail = $this->object->generate_image_clone($filename, $clone_path, $params);
            // We successfully generated the thumbnail
            if ($thumbnail != null) {
                $clone_path = $thumbnail->fileName;
                if (function_exists('getimagesize')) {
                    $dimensions = getimagesize($clone_path);
                } else {
                    $dimensions = [$params['width'], $params['height']];
                }
                if (!isset($image->meta_data)) {
                    $image->meta_data = [];
                }
                $size_meta = ['width' => $dimensions[0], 'height' => $dimensions[1], 'filename' => M_I18n::mb_basename($clone_path), 'generated' => microtime()];
                if (isset($params['crop_frame'])) {
                    $size_meta['crop_frame'] = $params['crop_frame'];
                }
                $image->meta_data[$size] = $size_meta;
                if ($size == 'full') {
                    $image->meta_data['width'] = $size_meta['width'];
                    $image->meta_data['height'] = $size_meta['height'];
                }
                $retval = $this->object->_image_mapper->save($image);
                do_action('ngg_generated_image', $image, $size, $params);
                if ($retval == 0) {
                    $retval = false;
                }
                if ($retval) {
                    $retval = $thumbnail;
                }
            } else {
                // Something went wrong. Thumbnail generation failed!
            }
        }
        return $retval;
    }
    /**
     * Generates a thumbnail for an image
     *
     * @param int|stdClass|C_Image $image
     * @return bool
     */
    function generate_thumbnail($image, $params = null, $skip_defaults = false)
    {
        $sized_image = $this->object->generate_image_size($image, 'thumbnail', $params, $skip_defaults);
        $retval = false;
        if ($sized_image != null) {
            $retval = true;
            $sized_image->destruct();
        }
        return $retval;
    }
}
/**
 * Provides getter methods to C_Gallery_Storage for determining absolute paths, URL, etc
 *
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Getters extends Mixin
{
    static $image_abspath_cache = array();
    static $image_url_cache = array();
    /**
     * Gets the absolute path of the backup of an original image
     *
     * @param string $image
     * @return null|string
     */
    function get_backup_abspath($image)
    {
        $retval = null;
        if ($image_path = $this->object->get_image_abspath($image)) {
            $retval = $image_path . '_backup';
        }
        return $retval;
    }
    function get_backup_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'backup');
    }
    function get_backup_url($image)
    {
        return $this->object->get_image_url($image, 'backup');
    }
    /**
     * Gets the absolute path where the image is stored. Can optionally return the path for a particular sized image.
     *
     * @param int|object $image
     * @param string     $size (optional) Default = full
     * @param bool       $check_existance (optional) Default = false
     * @return string
     */
    function get_image_abspath($image, $size = 'full', $check_existance = false)
    {
        $image_id = is_numeric($image) ? $image : $image->pid;
        $size = $this->object->normalize_image_size_name($size);
        $key = strval($image_id) . $size;
        if ($check_existance || !isset(self::$image_abspath_cache[$key])) {
            $retval = $this->object->_get_computed_image_abspath($image, $size, $check_existance);
            self::$image_abspath_cache[$key] = $retval;
        }
        $retval = self::$image_abspath_cache[$key];
        return $retval;
    }
    /**
     * Gets the url of a particular-sized image
     *
     * @param int|object $image
     * @param string     $size
     * @return string
     */
    function get_image_url($image, $size = 'full')
    {
        $retval = null;
        $image_id = is_numeric($image) ? $image : $image->pid;
        $key = strval($image_id) . $size;
        $success = true;
        if (!isset(self::$image_url_cache[$key])) {
            $url = $this->object->_get_computed_image_url($image, $size);
            if ($url) {
                self::$image_url_cache[$key] = $url;
                $success = true;
            } else {
                $success = false;
            }
        }
        if ($success) {
            $retval = self::$image_url_cache[$key];
        } else {
            $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
            if ($dynthumbs->is_size_dynamic($size)) {
                $params = $dynthumbs->get_params_from_name($size);
                $retval = \Imagely\NGG\Util\Router::get_instance()->get_url($dynthumbs->get_image_uri($image, $params), false, 'root');
            }
        }
        return apply_filters('ngg_get_image_url', $retval, $image, $size);
    }
    /**
     * An alias for get_full_abspath()
     *
     * @param int|object $image
     * @param bool       $check_existance
     * @return null|string
     */
    function get_original_abspath($image, $check_existance = false)
    {
        return $this->object->get_image_abspath($image, 'full', $check_existance);
    }
    /**
     * Flushes the cache we use for path/url calculation for images
     */
    function flush_image_path_cache($image, $size)
    {
        $image = is_numeric($image) ? $image : $image->pid;
        $key = strval($image) . $size;
        unset(self::$image_abspath_cache[$key]);
        unset(self::$image_url_cache[$key]);
    }
}
/**
 * Provides the basic methods of gallery management to C_Gallery_Storage
 *
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Management extends Mixin
{
    /**
     * Backs up an image file
     *
     * @param int|object $image
     * @param bool       $save
     * @return bool
     */
    function backup_image($image, $save = true)
    {
        $retval = false;
        $image_path = $this->object->get_image_abspath($image);
        if ($image_path && @file_exists($image_path)) {
            $retval = copy($image_path, $this->object->get_backup_abspath($image));
            // Store the dimensions of the image
            if (function_exists('getimagesize')) {
                $mapper = C_Image_Mapper::get_instance();
                if (!is_object($image)) {
                    $image = $mapper->find($image);
                }
                if ($image) {
                    if (empty($image->meta_data) || !is_array($image->meta_data)) {
                        $image->meta_data = [];
                    }
                    $dimensions = getimagesize($image_path);
                    $image->meta_data['backup'] = ['filename' => basename($image_path), 'width' => $dimensions[0], 'height' => $dimensions[1], 'generated' => microtime()];
                    if ($save) {
                        $mapper->save($image);
                    }
                }
            }
        }
        return $retval;
    }
}
/**
 * Provides upload-related methods used by C_Gallery_Storage
 *
 * @property C_Gallery_Storage $object
 */
class Mixin_GalleryStorage_Base_Upload extends Mixin
{
    /**
     * @param string        $abspath
     * @param int           $gallery_id
     * @param bool          $create_new_gallerypath
     * @param null|string   $gallery_title
     * @param array[string] $filenames
     * @return array|bool FALSE on failure
     */
    function import_gallery_from_fs($abspath, $gallery_id = null, $create_new_gallerypath = true, $gallery_title = null, $filenames = array())
    {
        if (@(!file_exists($abspath))) {
            return false;
        }
        $fs = C_Fs::get_instance();
        $retval = ['image_ids' => []];
        // Ensure that this folder has images
        $files = [];
        $directories = [];
        foreach (scandir($abspath) as $file) {
            if ($file == '.' || $file == '..' || strtoupper($file) == '__MACOSX') {
                continue;
            }
            $file_abspath = $fs->join_paths($abspath, $file);
            // Omit 'hidden' directories prefixed with a period
            if (is_dir($file_abspath) && strpos($file, '.') !== 0) {
                $directories[] = $file_abspath;
            } elseif ($this->is_image_file($file_abspath)) {
                if ($filenames && array_search($file_abspath, $filenames) !== false) {
                    $files[] = $file_abspath;
                } elseif (!$filenames) {
                    $files[] = $file_abspath;
                }
            }
        }
        if (empty($files) && empty($directories)) {
            return false;
        }
        // Get needed utilities
        $gallery_mapper = C_Gallery_Mapper::get_instance();
        // Recurse through the directory and pull in all of the valid images we find
        if (!empty($directories)) {
            foreach ($directories as $dir) {
                $subImport = $this->object->import_gallery_from_fs($dir, $gallery_id, $create_new_gallerypath, $gallery_title, $filenames);
                if ($subImport) {
                    $retval['image_ids'] = array_merge($retval['image_ids'], $subImport['image_ids']);
                }
            }
        }
        // If no gallery has been specified, then use the directory name as the gallery name
        if (!$gallery_id) {
            // Create the gallery
            $gallery = $gallery_mapper->create(['title' => $gallery_title ? $gallery_title : M_I18n::mb_basename($abspath)]);
            if (!$create_new_gallerypath) {
                $gallery_root = $fs->get_document_root('gallery');
                $gallery->path = str_ireplace($gallery_root, '', $abspath);
            }
            // Save the gallery
            if ($gallery->save()) {
                $gallery_id = $gallery->id();
            }
        }
        // Ensure that we have a gallery id
        if (!$gallery_id) {
            return false;
        } else {
            $retval['gallery_id'] = $gallery_id;
        }
        // Remove full sized image if backup is included
        $files_to_import = [];
        foreach ($files as $file_abspath) {
            if (preg_match('#_backup$#', $file_abspath)) {
                $files_to_import[] = $file_abspath;
                continue;
            } elseif (in_array([$file_abspath . '_backup', 'thumbs_' . $file_abspath, 'thumbs-' . $file_abspath], $files)) {
                continue;
            }
            $files_to_import[] = $file_abspath;
        }
        foreach ($files_to_import as $file_abspath) {
            $basename = preg_replace('#_backup$#', '', pathinfo($file_abspath, PATHINFO_BASENAME));
            if ($this->is_image_file($file_abspath)) {
                if ($image_id = $this->import_image_file($gallery_id, $file_abspath, $basename, false, false, false)) {
                    $retval['image_ids'][] = $image_id;
                }
            }
        }
        // Add the gallery name to the result
        if (!isset($gallery)) {
            $gallery = $gallery_mapper->find($gallery_id);
        }
        $retval['gallery_name'] = $gallery->title;
        return $retval;
    }
}
class Mixin_Validation extends Mixin
{
    public $_default_msgs = array('validates_presence_of' => '%s should be present', 'validates_presence_with' => '%s should be present with %s', 'validates_uniqueness_of' => '%s should be unique', 'validates_confirmation_of' => '%s should match confirmation', 'validates_exclusion_of' => '%s is reserved', 'validates_format_of' => '%s is invalid', 'validates_inclusion_of' => '%s is not included in the list', 'validates_numericality_of' => '%s is not numeric', 'validates_less_than' => '%s is too small', 'validates_greater_than' => '%s is too large', 'validates_equals' => '%s is invalid');
    public $_default_patterns = array('email_address' => '//');
    /**
     * Clears all errors for the object
     */
    public function clear_errors()
    {
        $this->object->_errors = [];
    }
    /**
     * Returns the errors for a particular property
     *
     * @param string $property
     * @return array|null
     */
    public function errors_for($property)
    {
        $errors = $this->object->_errors;
        if (isset($errors[$property])) {
            return $errors[$property];
        } else {
            return null;
        }
    }
    /**
     * Adds an error for a particular property of the object
     *
     * @param string $msg
     * @param string $property
     */
    public function add_error($msg, $property = '*')
    {
        if (!isset($this->object->_errors)) {
            $this->object->_errors = [];
        }
        $errors =& $this->object->_errors;
        if (!isset($errors[$property])) {
            $errors[$property] = [];
        }
        $errors[$property][] = $msg;
    }
    /**
     * Returns the default error message for a particular validator.
     * A hook could override this, or this class could be subclassed
     *
     * @param string $validator
     * @return string
     */
    public function _get_default_error_message_for($validator)
    {
        $retval = false;
        // The $validator variable is often set to __METHOD__, and many
        // forget that __METHOD__ looks like this:
        // Mixin_Active_Record_Validation::validates_presence_of
        // So, we fix that.
        if (strpos($validator, '::') !== false) {
            $parts = explode('::', $validator);
            $validator = $parts[1];
        }
        // Ensure that the validator has a default error message.
        if (isset($this->_default_msgs[$validator])) {
            $retval = $this->_default_msgs[$validator];
        }
        return $retval;
    }
    /**
     * Returns the default pattern for a formatter, such as an "e-mail address".
     *
     * @param string $formatter
     * @return string
     */
    public function get_default_pattern_for($formatter)
    {
        $retval = false;
        if (isset($this->_default_patterns[$formatter])) {
            $retval = $this->_default_patterns[$formatter];
        }
        return $retval;
    }
    /**
     * Gets all of the errors for the object
     *
     * @return array
     */
    public function get_errors($property = false)
    {
        $retval = $property ? $this->object->errors_for($property) : $this->object->_errors;
        if (!$retval || !is_array($retval)) {
            $retval = [];
        }
        return $retval;
    }
    /**
     * Determines if an object, or a particular field for that object, has errors
     *
     * @param string $property
     * @return bool
     */
    public function is_valid($property = false)
    {
        $valid = true;
        $errors = $this->object->get_errors();
        if ($property) {
            if (isset($errors[$property]) && !empty($errors[$property])) {
                $valid = false;
            }
        } elseif (!empty($errors)) {
            $valid = false;
        }
        return $valid;
    }
    /**
     * Determines if the object, or a particular field on the object, has errors
     *
     * @param string $property
     * @return bool
     */
    public function is_invalid($property = false)
    {
        return !$this->object->is_valid($property);
    }
    /**
     * Calls the validation method for a record, clearing the previous errors
     */
    public function validate()
    {
        $this->clear_errors();
        if ($this->object->has_method('validation')) {
            $this->object->validation();
        }
        return $this->object->is_valid();
    }
    /**
     * Converts the name of a property to a human readable property name
     * E.g. how_did_you_hear_about_us to "How did you hear about us"
     *
     * @param string $str
     * @return string
     */
    public function humanize_string($str)
    {
        $retval = [];
        if (is_array($str)) {
            foreach ($str as $s) {
                $retval[] = $this->humanize_string($s);
            }
        } else {
            $retval = ucfirst(str_replace('_', ' ', $str));
        }
        return $retval;
    }
    /**
     * Validates the length of a property's value
     *
     * @param string      $property
     * @param int         $length
     * @param string      $comparison_operator ===, !=, <, >, <=, or >=
     * @param bool|string $msg
     */
    public function validates_length_of($property, $length, $comparison_operator = '=', $msg = false)
    {
        $valid = true;
        $value = $this->object->{$property};
        $default_msg = $this->_get_default_error_message_for(__METHOD__);
        if (!$this->is_empty($value)) {
            switch ($comparison_operator) {
                case '=':
                case '==':
                    $valid = strlen($value) == $length;
                    $default_msg = $this->_get_default_error_message_for('validates_equals');
                    break;
                case '!=':
                case '!':
                    $valid = strlen($value) != $length;
                    $default_msg = $this->_get_default_error_message_for('validates_equals');
                    break;
                case '<':
                    $valid = strlen($value) < $length;
                    $default_msg = $this->_get_default_error_message_for('validates_less_than');
                    break;
                case '>':
                    $valid = strlen($value) > $length;
                    $default_msg = $this->_get_default_error_message_for('validates_greater_than');
                    break;
                case '<=':
                    $valid = strlen($value) <= $length;
                    $default_msg = $this->_get_default_error_message_for('validates_less_than');
                    break;
                case '>=':
                    $valid = strlen($value) >= $length;
                    $default_msg = $this->_get_default_error_message_for('validates_greater_than');
                    break;
            }
        } else {
            $valid = false;
        }
        if (!$valid) {
            if (!$msg) {
                $error_msg = sprintf($default_msg, $this->humanize_string($property));
            } else {
                $error_msg = $msg;
            }
            $this->add_error($error_msg, $property);
        }
    }
    /**
     * Validates that a property contains a numeric value. May optionally be tested against
     * other numbers.
     *
     * @param string    $property
     * @param int|float $comparison
     * @param string    $comparison_operator
     * @param string    $msg
     */
    public function validates_numericality_of($property, $comparison = false, $comparison_operator = false, $int_only = false, $msg = false)
    {
        $properties = is_array($property) ? $property : [$property];
        foreach ($properties as $property) {
            $value = $this->object->{$property};
            $default_msg = $this->_get_default_error_message_for(__METHOD__);
            if (!$this->is_empty($value)) {
                $invalid = false;
                if (is_numeric($value)) {
                    $value = $value += 0;
                    if ($int_only) {
                        $invalid = !is_int($value);
                    }
                    if (!$invalid) {
                        switch ($comparison_operator) {
                            case '=':
                            case '==':
                                $invalid = $value == $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_equals');
                                break;
                            case '!=':
                            case '!':
                                $invalid = $value != $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_equals');
                                break;
                            case '<':
                                $invalid = $value < $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_less_than');
                                break;
                            case '>':
                                $invalid = $value > $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_greater_than');
                                break;
                            case '<=':
                                $invalid = $value <= $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_less_than');
                                break;
                            case '>=':
                                $invalid = $value >= $comparison ? false : true;
                                $default_msg = $this->_get_default_error_message_for('validates_greater_than');
                                break;
                        }
                    }
                } else {
                    $invalid = true;
                }
                if ($invalid) {
                    if (!$msg) {
                        $error_msg = sprintf($default_msg, $this->humanize_string($property));
                    } else {
                        $error_msg = $msg;
                    }
                    $this->add_error($error_msg, $property);
                }
            }
        }
    }
    /**
     * Validates that a property includes a particular value
     *
     * @param string $property
     * @param array  $values
     * @param string $msg
     */
    public function validates_inclusion_of($property, $values = array(), $msg = false)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        if (!in_array($this->object->{$property}, $values)) {
            if (!$msg) {
                $msg = $this->_get_default_error_message_for(__METHOD__);
                $msg = sprintf($msg, $this->humanize_string($property));
            }
            $this->add_error($msg, $property);
        }
    }
    /**
     * Validates that a property's value matches a particular pattern
     *
     * @param string|array $property
     * @param string       $pattern
     * @param string       $msg
     */
    public function validates_format_of($property, $pattern, $msg = false)
    {
        if (!is_array($property)) {
            $property = [$property];
        }
        foreach ($property as $prop) {
            // We do not validate blank values - we rely on "validates_presense_of" for that.
            if (!$this->is_empty($this->object->{$prop})) {
                // If it doesn't match, then it's an error.
                if (!preg_match($pattern, $this->object->{$prop})) {
                    // Get default message.
                    if (!$msg) {
                        $msg = $this->_get_default_error_message_for(__METHOD__);
                        $msg = sprintf($msg, $this->humanize_string($property));
                    }
                    $this->add_error($msg, $prop);
                }
            }
        }
    }
    /**
     * Ensures that a property does NOT have a particular value
     *
     * @param string $property
     * @param array  $exclusions
     * @param string $msg
     */
    public function validates_exclusion_of($property, $exclusions = array(), $msg = false)
    {
        $invalid = false;
        if (!is_array($exclusions)) {
            $exclusions = [$exclusions];
        }
        foreach ($exclusions as $exclusion) {
            if ($exclusion == $this->object->{$property}) {
                $invalid = true;
                break;
            }
        }
        if ($invalid) {
            if (!$msg) {
                $msg = $this->_get_default_error_message_for(__METHOD__);
                $msg = sprintf($msg, $this->humanize_string($property));
            }
            $this->add_error($msg, $property);
        }
    }
    /**
     * Validates the confirmation of a property
     *
     * @param string $property
     * @param string $confirmation
     * @param string $msg
     */
    public function validates_confirmation_of($property, $confirmation, $msg = false)
    {
        if ($this->object->{$property} != $this->object->{$confirmation}) {
            if (!$msg) {
                $msg = $this->_get_default_error_message_for(__METHOD__);
                $msg = sprintf($msg, $this->humanize_string($property));
            }
            $this->add_error($msg, $property);
        }
    }
    /**
     * Validates the uniqueness of a property
     *
     * @param string $property
     * @param array  $scope
     * @param string $msg
     */
    public function validates_uniqueness_of($property, $scope = array(), $msg = false)
    {
        // Get any entities that have the same property.
        $mapper = $this->object->get_mapper();
        $key = $mapper->get_primary_key_column();
        $mapper->select($key);
        $mapper->limit(1);
        $mapper->where_and(["{$property} = %s", $this->object->{$property}]);
        if (!$this->object->is_new()) {
            $mapper->where_and(["{$key} != %s", $this->object->id()]);
        }
        foreach ($scope as $another_property) {
            $mapper->where_and(["{$another_property} = %s", $another_property]);
        }
        $result = $mapper->run_query();
        // If there's a result, it means that the entity is NOT unique.
        if ($result) {
            // Get default msg.
            if (!$msg) {
                $msg = $this->_get_default_error_message_for(__METHOD__);
                $msg = sprintf($msg, $this->humanize_string($property));
            }
            // Add error.
            $this->add_error($msg, $property);
        }
    }
    /**
     * Validates the presence of a value for a particular field
     *
     * @param string|array $properties
     * @param array        $with
     * @param string       $msg
     */
    public function validates_presence_of($properties, $with = array(), $msg = false)
    {
        $missing = [];
        if (!is_array($properties)) {
            $properties = [$properties];
        }
        // Iterate through each property that we're to check, and ensure
        // a value is present.
        foreach ($properties as $property) {
            $invalid = true;
            // Is a value present?
            if (!$this->is_empty($this->object->{$property})) {
                $invalid = false;
                // This property must be present with at least another property.
                if ($with) {
                    if (!is_array($with)) {
                        $with = [$with];
                    }
                    foreach ($with as $other) {
                        if ($this->is_empty($this->object->{$other})) {
                            $invalid = true;
                            $missing[] = $other;
                        }
                    }
                }
            }
            // Add error.
            if ($invalid) {
                if (!$msg) {
                    // If missing isn't empty, it means that we're to use the
                    // "with" error message.
                    if ($missing) {
                        $missing = implode(', ', $this->humanize_string($missing));
                        $msg = sprintf($this->_get_default_error_message_for('validates_presence_with'), $property, $missing);
                    } else {
                        $msg = sprintf($this->_get_default_error_message_for(__METHOD__), $property);
                    }
                }
                $this->add_error($msg, $property);
            }
        }
    }
}