<?php
/**
 * Class A_Display_Settings_Controller
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page using "ngg_display_settings" context
 */
class A_Display_Settings_Controller extends Mixin
{
    /**
     * Static resources required for the Display Settings page
     */
    function enqueue_backend_resources()
    {
        $this->call_parent('enqueue_backend_resources');
        wp_enqueue_style('nextgen_gallery_display_settings');
        wp_enqueue_script('nextgen_gallery_display_settings');
    }
    function get_page_title()
    {
        return __('Gallery Settings', 'nggallery');
    }
    function get_required_permission()
    {
        return 'NextGEN Change options';
    }
}
/**
 * Class A_Display_Settings_Page
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 */
class A_Display_Settings_Page extends Mixin
{
    function setup()
    {
        $this->object->add(NGG_DISPLAY_SETTINGS_SLUG, array('adapter' => 'A_Display_Settings_Controller', 'parent' => NGGFOLDER, 'before' => 'ngg_other_options'));
        return $this->call_parent('setup');
    }
}
/**
 * Class A_Displayed_Gallery_Trigger_Element
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_Displayed_Gallery_Trigger_Element extends Mixin
{
    function render_object()
    {
        $root_element = $this->call_parent('render_object');
        if (($displayed_gallery = $this->object->get_param('displayed_gallery')) && $this->object->get_param('display_type_rendering')) {
            $triggers = C_Displayed_Gallery_Trigger_Manager::get_instance();
            $triggers->render($root_element, $displayed_gallery);
        }
        return $root_element;
    }
}
/**
 * Class A_Displayed_Gallery_Trigger_Resources
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller
 */
class A_Displayed_Gallery_Trigger_Resources extends Mixin
{
    protected $run_once = FALSE;
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        return $this->enqueue_displayed_gallery_trigger_buttons_resources($displayed_gallery);
    }
    function enqueue_displayed_gallery_trigger_buttons_resources($displayed_gallery = FALSE)
    {
        $retval = FALSE;
        M_Gallery_Display::enqueue_fontawesome();
        if (!$this->run_once && !empty($displayed_gallery) && !empty($displayed_gallery->display_settings['ngg_triggers_display']) && $displayed_gallery->display_settings['ngg_triggers_display'] !== 'never') {
            $pro_active = FALSE;
            if (defined('NGG_PRO_PLUGIN_VERSION')) {
                $pro_active = 'NGG_PRO_PLUGIN_VERSION';
            }
            if (defined('NEXTGEN_GALLERY_PRO_VERSION')) {
                $pro_active = 'NEXTGEN_GALLERY_PRO_VERSION';
            }
            if (!empty($pro_active)) {
                $pro_active = constant($pro_active);
            }
            if (!is_admin() && (empty($pro_active) || version_compare($pro_active, '1.0.11') >= 0)) {
                wp_enqueue_style('fontawesome');
                $retval = TRUE;
                $this->run_once = TRUE;
            }
        }
        return $retval;
    }
}
/**
 * Class A_Gallery_Display_Factory
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_Gallery_Display_Factory extends Mixin
{
    /**
     * Instantiates a Display Type
     * @param array|stdClass|C_DataMapper_Model $properties (optional)
     * @param C_Display_Type_Mapper $mapper (optional)
     * @param string|array|FALSE $context (optional)
     */
    function display_type($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Display_Type($properties, $mapper, $context);
    }
    /**
     * Instantiates a Displayed Gallery
     * @param array|stdClass|C_DataMapper_Model $properties (optional)
     * @param C_Displayed_Gallery_Mapper $mapper (optional)
     * @param string|array|FALSE $context (optional)
     */
    function displayed_gallery($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Displayed_Gallery($properties, $mapper, $context);
    }
}
/**
 * Class A_Gallery_Display_View
 * @mixin C_MVC_View
 * @adapts I_MVC_View
 */
class A_Gallery_Display_View extends Mixin
{
    /**
     * Check whether to render certain kinds of extra additions to the view for a displayed gallery
     * @param object $displayed_gallery
     * @param string $template_id
     * @param C_MVC_View_Element $root_element
     * @param string $addition_type what kind of addition is being made 'layout', 'decoration', 'style', 'logic' etc.
     * @return bool|string
     */
    function _check_addition_rendering($displayed_gallery, $template_id, $root_element, $addition_type)
    {
        $view = $root_element->get_object();
        $mode = $view->get_param('render_mode');
        $ret = true;
        switch ($addition_type) {
            case 'layout':
                $ret = !in_array($mode, array('bare', 'basic'));
                break;
            case 'decoration':
                break;
            case 'style':
                break;
            case 'logic':
                break;
        }
        return $ret;
    }
}
/**
 * A Display Type is a component which renders a collection of images
 * in a "gallery".
 *
 * Properties:
 * - entity_types (gallery, album)
 * - name		 (nextgen_basic-thumbnails)
 * - title		 (NextGEN Basic Thumbnails)
 * - aliases	[basic_thumbnail, basic_thumbnails]
 *
 * @mixin Mixin_Display_Type_Validation
 * @mixin Mixin_Display_Type_Instance_Methods
 * @implements I_Display_Type
 */
class C_Display_Type extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Display_Type_Mapper';
    var $__settings = array();
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_Display_Type_Validation');
        $this->add_mixin('Mixin_Display_Type_Instance_Methods');
        $this->implement('I_Display_Type');
    }
    /**
     * Initializes a display type with properties
     * @param array|stdClass|C_Display_Type $properties
     * @param FALSE|C_Display_Type_Mapper $mapper
     * @param FALSE|string|array $context
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        // If no mapper was specified, then get the mapper
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        // Construct the model
        parent::initialize($mapper, $properties);
    }
    /**
     * Allows a setting to be retrieved directly, rather than through the
     * settings property
     * @param string $property
     * @return mixed
     */
    function &__get($property)
    {
        if ($property == 'settings') {
            if (isset($this->_stdObject->settings)) {
                //$this->__settings = array_merge($this->_stdObject->settings, $this->__settings);
            }
            return $this->_stdObject->settings;
        }
        if (isset($this->_stdObject->settings[$property]) && $this->_stdObject->settings[$property] != NULL) {
            return $this->_stdObject->settings[$property];
        } else {
            return parent::__get($property);
        }
    }
    function &__set($property, $value)
    {
        if ($property == 'settings') {
            $retval = $this->_stdObject->settings = $value;
        } else {
            $retval = $this->_stdObject->settings[$property] = $value;
        }
        return $retval;
    }
    function __isset($property_name)
    {
        if ($property_name == 'settings') {
            return isset($this->_stdObject->settings);
        }
        return isset($this->_stdObject->settings[$property_name]) || parent::__isset($property_name);
    }
}
class Mixin_Display_Type_Validation extends Mixin
{
    function validation()
    {
        $this->object->validates_presence_of('entity_types');
        $this->object->validates_presence_of('name');
        $this->object->validates_presence_of('title');
        return $this->object->is_valid();
    }
}
/**
 * Provides methods available for class instances
 */
class Mixin_Display_Type_Instance_Methods extends Mixin
{
    /**
     * Determines if this display type is compatible with a displayed gallery source
     * @param stdClass $source
     * @return bool
     */
    function is_compatible_with_source($source)
    {
        return C_Displayed_Gallery_Source_Manager::get_instance()->is_compatible($source, $this);
    }
    function get_order()
    {
        return NGG_DISPLAY_PRIORITY_BASE;
    }
}
/**
 * A Controller which displays the settings form for the display type, as
 * well as the front-end display
 */
/**
 * Class C_Display_Type_Controller
 * @mixin Mixin_Display_Type_Controller
 * @implements I_Display_Type_Controller
 */
class C_Display_Type_Controller extends C_MVC_Controller
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Display_Type_Controller');
        $this->implement('I_Display_Type_Controller');
    }
    /**
     * Gets a singleton of the mapper
     * @param string|bool $context
     * @return C_Display_Type_Controller
     */
    public static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Display_Type_Controller($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * Provides instance methods for the C_Display_Type_Controller class
 */
class Mixin_Display_Type_Controller extends Mixin
{
    var $_render_mode;
    /**
     * Enqueues static resources required for lightbox effects
     * @param object $displayed_gallery
     */
    function enqueue_lightbox_resources($displayed_gallery)
    {
        C_Lightbox_Library_Manager::get_instance()->enqueue();
    }
    function is_cachable()
    {
        return TRUE;
    }
    /**
     * This method should be overwritten by other adapters/mixins, and call
     * wp_enqueue_script() / wp_enqueue_style()
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        // This script provides common JavaScript among all display types
        wp_enqueue_script('ngg_common');
        wp_add_inline_script('ngg_common', '
            var nggLastTimeoutVal = 1000;

			var nggRetryFailedImage = function(img) {
				setTimeout(function(){
					img.src = img.src;
				}, nggLastTimeoutVal);
			
				nggLastTimeoutVal += 500;
			}
        ');
        // Enqueue the display type library
        wp_enqueue_script($displayed_gallery->display_type, $this->object->_get_js_lib_url($displayed_gallery), array(), NGG_SCRIPT_VERSION);
        // Add "galleries = {};"
        $this->object->_add_script_data('ngg_common', 'galleries', new stdClass(), TRUE, FALSE);
        // Add "galleries.gallery_1 = {};"
        $this->object->_add_script_data('ngg_common', 'galleries.gallery_' . $displayed_gallery->id(), (array) $displayed_gallery->get_entity(), FALSE);
        $this->object->_add_script_data('ngg_common', 'galleries.gallery_' . $displayed_gallery->id() . '.wordpress_page_root', get_permalink(), FALSE);
        // Enqueue trigger button resources
        C_Displayed_Gallery_Trigger_Manager::get_instance()->enqueue_resources($displayed_gallery);
        // Enqueue lightbox library
        $this->object->enqueue_lightbox_resources($displayed_gallery);
    }
    function enqueue_ngg_styles()
    {
        $settings = C_NextGen_Settings::get_instance();
        if ((!is_multisite() || is_multisite() && $settings->wpmuStyle) && $settings->activateCSS) {
            wp_enqueue_style('nggallery', C_NextGen_Style_Manager::get_instance()->get_selected_stylesheet_url(), array(), NGG_SCRIPT_VERSION);
        }
    }
    function get_render_mode()
    {
        return $this->object->_render_mode;
    }
    function set_render_mode($mode)
    {
        $this->object->_render_mode = $mode;
    }
    /**
     * Ensures that the minimum configuration of parameters are sent to a view
     * @param $displayed_gallery
     * @param null $params
     * @return array|null
     */
    function prepare_display_parameters($displayed_gallery, $params = null)
    {
        if ($params == null) {
            $params = array();
        }
        $params['display_type_rendering'] = true;
        $params['displayed_gallery'] = $displayed_gallery;
        $params['render_mode'] = $this->object->get_render_mode();
        return $params;
    }
    /**
     * Renders the frontend display of the display type
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return (optional)
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        return $this->object->render_partial('photocrati-nextgen_gallery_display#index', array(), $return);
    }
    /**
     * Returns the url for the JavaScript library required
     * @return null|string
     */
    function _get_js_lib_url()
    {
        return NULL;
    }
    function does_lightbox_support_displayed_gallery($displayed_gallery, $lightbox = NULL)
    {
        if (!$lightbox) {
            $lightbox = C_Lightbox_Library_Manager::get_instance()->get_selected();
        }
        $retval = FALSE;
        if ($lightbox) {
            // HANDLE COMPATIBILITY BREAK
            // In NGG 2.1.48 and earlier, lightboxes were stdClass objects, and it was assumed
            // that they only supported galleries that contained images, not albums that contained galleries.
            // After NGG 2.1.48, lightboxes are now C_NGG_Lightbox instances which have a 'is_supported()' method
            // to test if the lightbox can work with the displayed gallery settings
            if (get_class($lightbox) == 'stdClass') {
                $retval = !in_array($displayed_gallery->source, array('album', 'albums'));
            } else {
                $retval = $lightbox->is_supported($displayed_gallery);
            }
        }
        return $retval;
    }
    /**
     * Returns the effect HTML code for the displayed gallery
     * @param object $displayed_gallery
     * @return string
     */
    function get_effect_code($displayed_gallery)
    {
        $retval = '';
        if ($lightbox = C_Lightbox_Library_Manager::get_instance()->get_selected()) {
            if ($this->does_lightbox_support_displayed_gallery($displayed_gallery, $lightbox)) {
                $retval = $lightbox->code;
                $retval = str_replace('%GALLERY_ID%', $displayed_gallery->id(), $retval);
                $retval = str_replace('%GALLERY_NAME%', $displayed_gallery->id(), $retval);
                global $post;
                if ($post && isset($post->ID) && $post->ID) {
                    $retval = str_replace('%PAGE_ID%', $post->ID, $retval);
                }
            }
        }
        // allow for customization
        $retval = apply_filters('ngg_effect_code', $retval, $displayed_gallery);
        return $retval;
    }
    /**
     * Adds data to the DOM which is then accessible by a script
     * @param string $handle
     * @param string $object_name
     * @param mixed $object_value
     * @param bool $define
     * @param bool $override
     * @return bool
     */
    function _add_script_data($handle, $object_name, $object_value, $define = TRUE, $override = FALSE)
    {
        $retval = FALSE;
        // wp_localize_script allows you to add data to the DOM, associated
        // with a particular script. You can even call wp_localize_script
        // multiple times to add multiple objects to the DOM. However, there
        // are a few problems with wp_localize_script:
        //
        // - If you call it with the same object_name more than once, you're
        //   overwritting the first call.
        // - You cannot namespace your objects due to the "var" keyword always
        // - being used.
        //
        // To circumvent the above issues, we're going to use the WP_Scripts
        // object to workaround the above issues
        global $wp_scripts;
        // Has the script been registered or enqueued yet?
        if (isset($wp_scripts->registered[$handle])) {
            // Get the associated data with this script
            $script =& $wp_scripts->registered[$handle];
            $data = isset($script->extra['data']) ? $script->extra['data'] : '';
            // Construct the addition
            $addition = $define ? "\nvar {$object_name} = " . json_encode($object_value) . ';' : "\n{$object_name} = " . json_encode($object_value) . ';';
            // Add the addition
            if ($override) {
                $data .= $addition;
                $retval = TRUE;
            } else {
                if (strpos($data, $object_name) === FALSE) {
                    $data .= $addition;
                    $retval = TRUE;
                }
            }
            $script->extra['data'] = $data;
            unset($script);
        }
        return $retval;
    }
    // Returns the longest and widest dimensions from a list of entities
    function get_entity_statistics($entities, $named_size, $style_images = FALSE)
    {
        $longest = $widest = 0;
        $storage = C_Gallery_Storage::get_instance();
        $image_mapper = FALSE;
        // we'll fetch this if needed
        // Calculate longest and
        foreach ($entities as $entity) {
            // Get the image
            $image = FALSE;
            if (isset($entity->pid)) {
                $image = $entity;
            } elseif (isset($entity->previewpic)) {
                if (!$image_mapper) {
                    $image_mapper = C_Image_Mapper::get_instance();
                }
                $image = $image_mapper->find($entity->previewpic);
            }
            // Once we have the image, get it's dimensions
            if ($image) {
                $dimensions = $storage->get_image_dimensions($image, $named_size);
                if ($dimensions['width'] > $widest) {
                    $widest = $dimensions['width'];
                }
                if ($dimensions['height'] > $longest) {
                    $longest = $dimensions['height'];
                }
            }
        }
        // Second loop to style images
        if ($style_images) {
            foreach ($entities as &$entity) {
                // Get the image
                $image = FALSE;
                if (isset($entity->pid)) {
                    $image = $entity;
                } elseif (isset($entity->previewpic)) {
                    if (!$image_mapper) {
                        $image_mapper = C_Image_Mapper::get_instance();
                    }
                    $image = $image_mapper->find($entity->previewpic);
                }
                // Once we have the image, get it's dimension and calculate margins
                if ($image) {
                    $dimensions = $storage->get_image_dimensions($image, $named_size);
                }
            }
        }
        return array('entities' => $entities, 'longest' => $longest, 'widest' => $widest);
    }
    /**
     * Renders a view after checking for templates
     */
    function create_view($template, $params = array(), $context = NULL)
    {
        if (isset($params['displayed_gallery'])) {
            if (isset($params['displayed_gallery']->display_settings)) {
                $template = $this->get_display_type_view_abspath($template, $params);
            }
        }
        return $this->call_parent('create_view', $template, $params, $context);
    }
    /**
     * Finds the abs path of template given file name and list of posssible directories
     * @param string $template
     * @param array $params
     * @return string $template 
     */
    function get_display_type_view_abspath($template, $params)
    {
        /* Identify display type and display_type_view */
        $displayed_gallery = $params['displayed_gallery'];
        $display_type_name = $params['displayed_gallery']->display_type;
        $display_settings = $displayed_gallery->display_settings;
        $display_type_view = NULL;
        if (isset($display_settings['display_type_view'])) {
            $display_type_view = $display_settings['display_type_view'];
        }
        if (isset($display_settings['display_view'])) {
            $display_type_view = $display_settings['display_view'];
        }
        if ($display_type_view && $display_type_view != 'default') {
            /*
             * A display type view or display template value looks like this:
             *
             * "default"
             * "imagebrowser-dark-template.php" ("default" category is implicit)
             * "custom/customized-template.php" ("custom" category is explicit)
             *
             * Templates can be found in multiple directories, and each directory is given
             * a key, which is used to distinguish it's "category".
             */
            $fs = C_Fs::get_instance();
            /* Fetch array of template directories */
            $dirs = M_Gallery_Display::get_display_type_view_dirs($display_type_name);
            // If the view starts with a slash, we assume that a filename has been given
            if (strpos($display_type_view, DIRECTORY_SEPARATOR) === 0) {
                if (@file_exists($display_type_view)) {
                    $template = $display_type_view;
                }
            } else {
                // Add the missing "default" category name prefix to the template to make it
                // more consistent to evaluate
                if (strpos($display_type_view, DIRECTORY_SEPARATOR) === FALSE) {
                    $display_type_view = join(DIRECTORY_SEPARATOR, array('default', $display_type_view));
                }
                foreach ($dirs as $category => $dir) {
                    $category = preg_quote($category . DIRECTORY_SEPARATOR);
                    if (preg_match("#^{$category}(.*)\$#", $display_type_view, $match)) {
                        $display_type_view = $match[1];
                        $template_abspath = $fs->join_paths($dir, $display_type_view);
                        if (@file_exists($template_abspath)) {
                            $template = $template_abspath;
                            break;
                        }
                    }
                }
            }
        }
        /* Return template. If no match is found, returns the original template */
        return $template;
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
    function define($context = FALSE, $not_used = FALSE)
    {
        $object_name = 'display_type';
        // Add the object name to the context of the object as well
        // This allows us to adapt the driver itself, if required
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, $object_name);
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_Display_Type_Mapper');
        $this->implement('I_Display_Type_Mapper');
        $this->set_model_factory_method($object_name);
        // Define columns
        $this->define_column('ID', 'BIGINT', 0);
        $this->define_column('name', 'VARCHAR(255)');
        $this->define_column('title', 'VARCHAR(255)');
        $this->define_column('preview_image_relpath', 'VARCHAR(255)');
        $this->define_column('default_source', 'VARCHAR(255)');
        $this->define_column('view_order', 'BIGINT', NGG_DISPLAY_PRIORITY_BASE);
        $this->add_serialized_column('settings');
        $this->add_serialized_column('entity_types');
    }
    function initialize($context = FALSE)
    {
        parent::initialize();
    }
    /**
     * Gets a singleton of the mapper
     * @param string|bool $context
     * @return C_Display_Type_Mapper
     */
    public static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Display_Type_Mapper($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * Provides instance methods for the display type mapper
 */
class Mixin_Display_Type_Mapper extends Mixin
{
    /**
     * Locates a Display Type by names
     * @param string $name
     * @param bool $model
     * @return null|object
     */
    function find_by_name($name, $model = FALSE)
    {
        $retval = NULL;
        $this->object->select();
        $this->object->where(array('name = %s', $name));
        $results = $this->object->run_query(FALSE, $model);
        if (!$results) {
            foreach ($this->object->find_all(FALSE, $model) as $entity) {
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
     * @param string|array $entity_type e.g. image, gallery, album
     * @param bool $model (optional)
     * @return array
     */
    function find_by_entity_type($entity_type, $model = FALSE)
    {
        $find_entity_types = is_array($entity_type) ? $entity_type : array($entity_type);
        $retval = NULL;
        foreach ($this->object->find_all(FALSE, $model) as $display_type) {
            foreach ($find_entity_types as $entity_type) {
                if (isset($display_type->entity_types) && in_array($entity_type, $display_type->entity_types)) {
                    $retval[] = $display_type;
                    break;
                }
            }
        }
        return $retval;
    }
    /**
     * Uses the title attribute as the post title
     * @param stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->title;
    }
    /**
     * Sets default values needed for display types
     * @param object $entity (optional)
     */
    function set_defaults($entity)
    {
        if (!isset($entity->settings)) {
            $entity->settings = array();
        }
        $this->_set_default_value($entity, 'preview_image_relpath', '');
        $this->_set_default_value($entity, 'default_source', '');
        $this->_set_default_value($entity, 'view_order', NGG_DISPLAY_PRIORITY_BASE);
        $this->_set_default_value($entity, 'settings', 'use_lightbox_effect', TRUE);
        $this->_set_default_value($entity, 'hidden_from_ui', FALSE);
        // todo remove later
        $this->_set_default_value($entity, 'hidden_from_igw', FALSE);
        $this->_set_default_value($entity, 'aliases', array());
        return $this->call_parent('set_defaults', $entity);
    }
}
/**
 * Associates a Display Type with a collection of images
 *
 * * Properties:
 * - source				(gallery, album, recent_images, random_images, etc)
 * - container_ids		(gallery ids, album ids, tag ids, etc)
 * - display_type		(name of the display type being used)
 * - display_settings	(settings for the display type)
 * - exclusions			(excluded entity ids)
 * - entity_ids			(specific images/galleries to include, sorted)
 * - order_by
 * - order_direction
 *
 * @mixin Mixin_Displayed_Gallery_Validation
 * @mixin Mixin_Displayed_Gallery_Instance_Methods
 * @mixin Mixin_Displayed_Gallery_Queries
 * @implements I_Displayed_Gallery
 */
class C_Displayed_Gallery extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Displayed_Gallery_Mapper';
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->add_mixin('Mixin_Displayed_Gallery_Validation');
        $this->add_mixin('Mixin_Displayed_Gallery_Instance_Methods');
        $this->add_mixin('Mixin_Displayed_Gallery_Queries');
        $this->implement('I_Displayed_Gallery');
    }
    /**
     * Initializes a display type with properties
     * @param array|stdClass|C_Displayed_Gallery $properties
     * @param FALSE|C_Displayed_Gallery_Mapper $mapper
     * @param FALSE|string|array $context
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        parent::initialize($mapper, $properties);
    }
}
/**
 * Provides validation
 */
class Mixin_Displayed_Gallery_Validation extends Mixin
{
    function validation()
    {
        // Valid sources
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
            // galleries and albums
            if ($source = $this->object->get_source()) {
                if (!$display_type->is_compatible_with_source($source)) {
                    $this->object->add_error(__('Source not compatible with selected display type', 'nggallery'), 'display_type');
                }
            }
            // Allow ONLY recent & random galleries to have their own maximum_entity_count
            if (!empty($this->object->display_settings['maximum_entity_count']) && in_array($this->object->source, array('random_images', 'recent_images', 'random', 'recent'))) {
                $this->object->maximum_entity_count = $this->object->display_settings['maximum_entity_count'];
            }
            // If no maximum_entity_count has been given, then set a maximum
            if (!isset($this->object->maximum_entity_count)) {
                $settings = C_NextGen_Settings::get_instance();
                $this->object->maximum_entity_count = $settings->get('maximum_entity_count', 500);
            }
        } else {
            $this->object->add_error('Invalid display type', 'display_type');
        }
        return $this->object->is_valid();
    }
}
class Mixin_Displayed_Gallery_Queries extends Mixin
{
    // The "alternative" approach to using "ORDER BY RAND()" works by finding X image PID in a kind of shotgun-blast
    // like scattering in a second query made via $wpdb that is then fed into the query built by _get_image_entities().
    // This variable is used to cache the results of that inner quasi-random PID retrieval so that multiple calls
    // to $displayed_gallery->get_entities() don't return different results for each invocation. This is important
    // for NextGen Pro's galleria module in order to 'localize' the results of get_entities() to JSON.
    protected static $_random_image_ids_cache = array();
    function get_entities($limit = FALSE, $offset = FALSE, $id_only = FALSE, $returns = 'included')
    {
        $retval = array();
        $source_obj = $this->object->get_source();
        $max = $this->object->get_maximum_entity_count();
        if (!$limit || is_numeric($limit) && $limit > $max) {
            $limit = $max;
        }
        // Ensure that all parameters have values that are expected
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
     * @param stdClass $source_obj
     * @param int $limit
     * @param int $offset
     * @param boolean $id_only
     * @param string $returns
     */
    function _get_image_entities($source_obj, $limit, $offset, $id_only, $returns)
    {
        // TODO: This method is very long, and therefore more difficult to read
        // Find a way to minimalize or segment
        $settings = C_NextGen_Settings::get_instance();
        $mapper = C_Image_Mapper::get_instance();
        $image_key = $mapper->get_primary_key_column();
        $select = $id_only ? $image_key : $mapper->get_table_name() . '.*';
        if (strtoupper($this->object->order_direction) == 'DSC') {
            $this->object->order_direction = 'DESC';
        }
        $sort_direction = in_array(strtoupper($this->object->order_direction), array('ASC', 'DESC')) ? $this->object->order_direction : $settings->galSortDir;
        $sort_by = in_array(strtolower($this->object->order_by), array_merge(C_Image_Mapper::get_instance()->get_column_names(), array('rand()'))) ? $this->object->order_by : $settings->galSort;
        // Quickly sanitize
        global $wpdb;
        $this->object->container_ids = $this->object->container_ids ? array_map(array($wpdb, '_escape'), $this->object->container_ids) : array();
        $this->object->entity_ids = $this->object->entity_ids ? array_map(array($wpdb, '_escape'), $this->object->entity_ids) : array();
        $this->object->exclusions = $this->object->exclusions ? array_map(array($wpdb, '_escape'), $this->object->exclusions) : array();
        // Here's what this method is doing:
        // 1) Determines what results need returned
        // 2) Determines from what container ids the results should come from
        // 3) Applies ORDER BY clause
        // 4) Applies LIMIT/OFFSET clause
        // 5) Executes the query and returns the result
        // We start with the most difficult query. When returns is "both", we
        // need to return a list of both included and excluded entity ids, and
        // mark specifically which entities are excluded
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
            // Add sortorder column
            if ($sortorder_set) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $sortorder_set, 'new_sortorder', TRUE);
                // A user might want to sort the results by the order of
                // images that they specified to be included. For that,
                // we need some trickery by reversing the order direction
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            // Add exclude column
            if ($excluded_set) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $excluded_set, 'exclude');
                $select .= ", IF (exclude = 0 AND @exclude = 0, {$if_true}, {$if_false}) AS 'exclude'";
            }
            // Select what we want
            $mapper->select($select);
        }
        // When returns is "included", the query is relatively simple. We
        // just provide a where clause to limit how many images we're returning
        // based on the entity_ids, exclusions, and container_ids parameters
        if ($returns == 'included') {
            // If the sortorder propery is available, then we need to override
            // the sortorder
            if ($this->object->sortorder) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $this->object->sortorder, 'new_sortorder', TRUE);
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            $mapper->select($select);
            // Filter based on entity_ids selection
            if ($this->object->entity_ids) {
                $mapper->where(array("{$image_key} IN %s", $this->object->entity_ids));
            }
            // Filter based on exclusions selection
            if ($this->object->exclusions) {
                $mapper->where(array("{$image_key} NOT IN %s", $this->object->exclusions));
            }
            // Ensure that no images marked as excluded at the gallery level are returned
            if (empty($this->object->skip_excluding_globally_excluded_images)) {
                $mapper->where(array("exclude = %d", 0));
            }
        } elseif ($returns == 'excluded') {
            // If the sortorder propery is available, then we need to override
            // the sortorder
            if ($this->object->sortorder) {
                $select = $this->object->_add_find_in_set_column($select, $image_key, $this->object->sortorder, 'new_sortorder', TRUE);
                $sort_direction = $this->object->order_direction == 'ASC' ? 'DESC' : 'ASC';
                $sort_by = 'new_sortorder';
            }
            // Mark each result as excluded
            $select .= ", 1 AS exclude";
            $mapper->select($select);
            // Is this case, entity_ids become the exclusions
            $exclusions = $this->object->entity_ids;
            // Remove the exclusions always takes precedence over entity_ids, so
            // we adjust the list of ids
            if ($this->object->exclusions) {
                foreach ($this->object->exclusions as $excluded_entity_id) {
                    if (($index = array_search($excluded_entity_id, $exclusions)) !== FALSE) {
                        unset($exclusions[$index]);
                    }
                }
            }
            // Filter based on exclusions selection
            if ($exclusions) {
                $mapper->where(array("{$image_key} NOT IN %s", $exclusions));
            } else {
                if ($this->object->exclusions) {
                    $mapper->where(array("{$image_key} IN %s", $this->object->exclusions));
                }
            }
            // Ensure that images marked as excluded are returned as well
            $mapper->where(array("exclude = 1"));
        }
        // Filter based on containers_ids. Container ids is a little more
        // complicated as it can contain gallery ids or tags
        if ($this->object->container_ids) {
            // Container ids are tags
            if ($source_obj->name == 'tags') {
                $term_ids = $this->object->get_term_ids_for_tags($this->object->container_ids);
                $mapper->where(array("{$image_key} IN %s", get_objects_in_term($term_ids, 'ngg_tag')));
            } else {
                $mapper->where(array("galleryid IN %s", $this->object->container_ids));
            }
        }
        // Filter based on excluded container ids
        if ($this->object->excluded_container_ids) {
            // Container ids are tags
            if ($source_obj->name == 'tags') {
                $term_ids = $this->object->get_term_ids_for_tags($this->object->excluded_container_ids);
                $mapper->where(array("{$image_key} NOT IN %s", get_objects_in_term($term_ids, 'ngg_tag')));
            } else {
                $mapper->where(array("galleryid NOT IN %s", $this->object->excluded_container_ids));
            }
        }
        // Adjust the query more based on what source was selected
        if (in_array($this->object->source, array('recent', 'recent_images'))) {
            $sort_direction = 'DESC';
            $sort_by = 'imagedate';
        } elseif ($this->object->source == 'random_images' && empty($this->object->entity_ids)) {
            // A gallery with source=random and a non-empty entity_ids is treated as being source=images & image_ids=(entity_ids)
            // In this case however source is random but no image ID are pre-filled.
            //
            // Here we must transform our query from "SELECT * FROM ngg_pictures WHERE gallery_id = X" into something
            // like "SELECT * FROM ngg_pictures WHERE pid IN (SELECT pid FROM ngg_pictures WHERE gallery_id = X ORDER BY RAND())"
            $table_name = $mapper->get_table_name();
            $where_clauses = array();
            $old_where_sql = '';
            // $this->get_entities_count() works by calling count(get_entities()) which means that for random galleries
            // there will be no limit passed to this method -- adjust the $limit now based on the maximum_entity_count
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
            if (C_NextGen_Settings::get_instance()->use_alternate_random_method || defined('NGG_DISABLE_ORDER_BY_RAND') && NGG_DISABLE_ORDER_BY_RAND) {
                // Check if the random image PID have been cached and use them (again) if already found
                $id = $this->object->ID();
                if (!empty(self::$_random_image_ids_cache[$id])) {
                    $image_ids = self::$_random_image_ids_cache[$id];
                } else {
                    global $wpdb;
                    // Prevent infinite loops: retrieve the image count and if needed just pull in every image available
                    $total = $wpdb->get_var("SELECT COUNT(`pid`) FROM {$wpdb->nggpictures} {$old_where_sql}");
                    $image_ids = array();
                    if ($total <= $limit) {
                        $image_ids = $wpdb->get_col("SELECT `pictures`.`pid` FROM {$wpdb->nggpictures} `pictures` {$old_where_sql} LIMIT {$total}");
                    } else {
                        // Start retrieving random ID from the DB and hope they exist; continue looping until our count is full
                        $segments = ceil($limit / 4);
                        while (count($image_ids) < $limit) {
                            $newID = $this->_query_random_ids_for_cache($segments, $old_where_sql);
                            $image_ids = array_merge(array_unique($image_ids), $newID);
                        }
                    }
                    // Prevent overflow
                    if (count($image_ids) > $limit) {
                        array_splice($image_ids, $limit);
                    }
                    // Give things an extra shake
                    shuffle($image_ids);
                    // Cache these ID in memory so that any attempts to call get_entities() more than once will result
                    // in the same images being retrieved for the duration of that page execution.
                    self::$_random_image_ids_cache[$id] = $image_ids;
                }
                $image_ids = implode(',', $image_ids);
                // Replace the existing WHERE clause with one where aready retrieved "random" PID are included
                $mapper->_where_clauses = array(" {$noExtras} `{$image_key}` IN ({$image_ids}) {$noExtras}");
            } else {
                // Replace the existing WHERE clause with one that selects from a sub-query that is randomly ordered
                $sub_where = "SELECT `{$image_key}` FROM `{$table_name}` i {$old_where_sql} ORDER BY RAND() LIMIT {$limit}";
                $mapper->_where_clauses = array(" {$noExtras} `{$image_key}` IN (SELECT `{$image_key}` FROM ({$sub_where}) o) {$noExtras}");
            }
        }
        // Apply a sorting order
        if ($sort_by) {
            $mapper->order_by($sort_by, $sort_direction);
        }
        // Apply a limit
        if ($limit) {
            if ($offset) {
                $mapper->limit($limit, $offset);
            } else {
                $mapper->limit($limit);
            }
        }
        $results = $mapper->run_query();
        return $results;
    }
    /**
     * @param int $limit
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
        return $wpdb->get_col("SELECT `pictures`.`pid` from {$wpdb->nggpictures} `pictures`\n                    JOIN (SELECT CEIL(MAX(`pid`) * RAND()) AS `pid` FROM {$wpdb->nggpictures}) AS `x` ON `pictures`.`pid` >= `x`.`pid`\n                    {$where_sql}\n                    AND `pictures`.`pid` MOD {$mod} = 0\n                    LIMIT {$limit}");
    }
    /**
     * Gets all gallery and album entities from albums specified, if any
     * @param stdClass $source_obj
     * @param int $limit
     * @param int $offset
     * @param boolean $id_only
     * @param array $returns
     */
    function _get_album_and_gallery_entities($source_obj, $limit = FALSE, $offset = FALSE, $id_only = FALSE, $returns = 'included')
    {
        // Albums queries and difficult and inefficient to perform due to the
        // database schema. To complicate things, we're returning two different
        // types of entities - galleries, and sub-albums.
        // The user prefixes entity_id's with an 'a' to distinguish album ids
        // from gallery ids. E.g. entity_ids=[1, "a2", 3]
        $album_mapper = C_Album_Mapper::get_instance();
        $album_key = $album_mapper->get_primary_key_column();
        $gallery_mapper = C_Gallery_Mapper::get_instance();
        $gallery_key = $gallery_mapper->get_primary_key_column();
        $select = $id_only ? $album_key . ", sortorder" : $album_mapper->get_table_name() . '.*';
        $retval = array();
        // If no exclusions are specified, are entity_ids are specified,
        // and we're to return is "included", then we have a relatively easy
        // query to perform - we just fetch each entity listed in
        // the entity_ids field
        if ($returns == 'included' && $this->object->entity_ids && empty($this->object->exclusions)) {
            $retval = $this->object->_entities_to_galleries_and_albums($this->object->entity_ids, $id_only, array(), $limit, $offset);
        } else {
            // Start the query
            $album_mapper->select($select);
            // Fetch the albums, and find the entity ids of the sub-albums and galleries
            $entity_ids = array();
            $excluded_ids = array();
            // Filter by container ids. If container_ids === '0' we retrieve all existing gallery_ids and use
            // them as the available entity_ids for comparability with 1.9x
            $container_ids = $this->object->container_ids;
            if ($container_ids) {
                if ($container_ids !== array('0') && $container_ids !== array('')) {
                    $album_mapper->where(array("{$album_key} IN %s", $container_ids));
                    foreach ($album_mapper->run_query() as $album) {
                        $entity_ids = array_merge($entity_ids, (array) $album->sortorder);
                    }
                } else {
                    if ($container_ids === array('0') || $container_ids === array('')) {
                        foreach ($gallery_mapper->select($gallery_key)->run_query() as $gallery) {
                            $entity_ids[] = $gallery->{$gallery_key};
                        }
                    }
                }
            }
            // Break the list of entities into two groups, included entities
            // and excluded entity ids
            // --
            // If a specific list of entity ids have been specified, then
            // we know what entity ids are meant to be included. We can compute
            // the intersect and also determine what entity ids are to be
            // excluded
            if ($this->object->entity_ids) {
                // Determine the real list of included entity ids. Exclusions
                // always take precedence
                $included_ids = $this->object->entity_ids;
                foreach ($this->object->exclusions as $excluded_id) {
                    if (($index = array_search($excluded_id, $included_ids)) !== FALSE) {
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
            // We're interested in only the included ids
            if ($returns == 'included') {
                $retval = $this->object->_entities_to_galleries_and_albums($included_ids, $id_only, array(), $limit, $offset);
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
     * @param bool $id_only
     * @param array $exclusions
     * @param int $limit
     * @param int $offset
     * @return array
     */
    function _entities_to_galleries_and_albums($entity_ids, $id_only = FALSE, $exclusions = array(), $limit = FALSE, $offset = FALSE)
    {
        $retval = array();
        $gallery_ids = array();
        $album_ids = array();
        $album_mapper = C_Album_Mapper::get_instance();
        $album_key = $album_mapper->get_primary_key_column();
        $gallery_mapper = C_Gallery_Mapper::get_instance();
        $image_mapper = C_Image_Mapper::get_instance();
        $gallery_key = $gallery_mapper->get_primary_key_column();
        $album_select = ($id_only ? $album_key : $album_mapper->get_table_name() . '.*') . ", 1 AS is_album, 0 AS is_gallery, name AS title, albumdesc AS galdesc";
        $gallery_select = ($id_only ? $gallery_key : $gallery_mapper->get_table_name() . '.*') . ", 1 AS is_gallery, 0 AS is_album";
        // Modify the sort order of the entities
        if ($this->object->sortorder) {
            $sortorder = array_intersect($this->object->sortorder, $entity_ids);
            $entity_ids = array_merge($sortorder, array_diff($entity_ids, $sortorder));
        }
        // Segment entity ids into two groups - galleries and albums
        foreach ($entity_ids as $entity_id) {
            if (substr($entity_id, 0, 1) == 'a') {
                $album_ids[] = intval(substr($entity_id, 1));
            } else {
                $gallery_ids[] = intval($entity_id);
            }
        }
        // Adjust query to include an exclude property
        if ($exclusions) {
            $album_select = $this->object->_add_find_in_set_column($album_select, $album_key, $this->object->exclusions, 'exclude');
            $album_select = $this->object->_add_if_column($album_select, 'exclude', 0, 1);
            $gallery_select = $this->object->_add_find_in_set_column($gallery_select, $gallery_key, $this->object->exclusions, 'exclude');
            $gallery_select = $this->object->_add_if_column($gallery_select, 'exclude', 0, 1);
        }
        // Add sorting parameter to the gallery and album queries
        if ($gallery_ids) {
            $gallery_select = $this->object->_add_find_in_set_column($gallery_select, $gallery_key, $gallery_ids, 'ordered_by', TRUE);
        } else {
            $gallery_select .= ", 0 AS ordered_by";
        }
        if ($album_ids) {
            $album_select = $this->object->_add_find_in_set_column($album_select, $album_key, $album_ids, 'ordered_by', TRUE);
        } else {
            $album_select .= ", 0 AS ordered_by";
        }
        // Fetch entities
        $galleries = $gallery_mapper->select($gallery_select)->where(array("{$gallery_key} IN %s", $gallery_ids))->order_by('ordered_by', 'DESC')->run_query();
        $counts = $image_mapper->select('galleryid, COUNT(*) as counter')->where(array(array("galleryid IN %s", $gallery_ids), array('exclude = %d', 0)))->group_by('galleryid')->run_query(FALSE, FALSE, TRUE);
        $albums = $album_mapper->select($album_select)->where(array("{$album_key} IN %s", $album_ids))->order_by('ordered_by', 'DESC')->run_query();
        // Reorder entities according to order specified in entity_ids
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
        // Sort the entities
        if ($this->object->order_by && $this->object->order_by != 'sortorder') {
            usort($retval, array(&$this, '_sort_album_result'));
        }
        if ($this->object->order_direction == 'DESC') {
            $retval = array_reverse($retval);
        }
        // Limit the entities
        if ($limit) {
            $retval = array_slice($retval, $offset, $limit);
        }
        return $retval;
    }
    /**
     * Returns the total number of entities in this displayed gallery
     * @param string $returns
     * @return int
     */
    function get_entity_count($returns = 'included')
    {
        $retval = 0;
        // Is this an image query?
        $source_obj = $this->object->get_source();
        if (in_array('image', $source_obj->returns)) {
            $retval = count($this->object->_get_image_entities($source_obj, FALSE, FALSE, TRUE, $returns));
        } elseif (in_array('gallery', $source_obj->returns)) {
            $retval = count($this->object->_get_album_and_gallery_entities($source_obj, FALSE, FALSE, TRUE, $returns));
        }
        $max = $this->get_maximum_entity_count();
        if ($retval > $max) {
            $retval = $max;
        }
        return $retval;
    }
    // Honor the gallery 'maximum_entity_count' setting ONLY when dealing with random & recent galleries. All
    // others will always obey the *global* 'maximum_entity_count' setting.
    function get_maximum_entity_count()
    {
        $max = intval(C_NextGen_Settings::get_instance()->get('maximum_entity_count', 500));
        $sources = C_Displayed_Gallery_Source_Manager::get_instance();
        $source_obj = $this->object->get_source();
        if (in_array($source_obj, array($sources->get('random'), $sources->get('random_images'), $sources->get('recent'), $sources->get('recent_images')))) {
            $max = intval($this->object->maximum_entity_count);
        }
        return $max;
    }
    /**
     * Returns all included entities for the displayed gallery
     * @param int $limit
     * @param int $offset
     * @param boolean $id_only
     * @return array
     */
    function get_included_entities($limit = FALSE, $offset = FALSE, $id_only = FALSE)
    {
        return $this->object->get_entities($limit, $offset, $id_only, 'included');
    }
    /**
     * Adds a FIND_IN_SET call to the select portion of the query, and
     * optionally defines a dynamic column
     * @param string $select
     * @param string $key
     * @param array $array
     * @param string $alias
     * @param boolean $add_column
     * @return string
     */
    function _add_find_in_set_column($select, $key, $array, $alias, $add_column = FALSE)
    {
        $array = array_map('intval', $array);
        $set = implode(",", array_reverse($array));
        if (!$select) {
            $select = "1";
        }
        $select .= ", @{$alias} := FIND_IN_SET({$key}, '{$set}')";
        if ($add_column) {
            $select .= " AS {$alias}";
        }
        return $select;
    }
    function _add_if_column($select, $alias, $true = 1, $false = 0)
    {
        if (!$select) {
            $select = "1";
        }
        $select .= ", IF(@{$alias} = 0, {$true}, {$false}) AS {$alias}";
        return $select;
    }
    /**
     * Parses the list of parameters provided in the displayed gallery, and
     * ensures everything meets expectations
     * @return boolean
     */
    function _parse_parameters()
    {
        $valid = FALSE;
        // Ensure that the source is valid
        if (C_Displayed_Gallery_Source_Manager::get_instance()->get($this->object->source)) {
            $valid = TRUE;
        }
        // Ensure that exclusions, entity_ids, and sortorder have valid elements.
        // IE likes to send empty array as an array with a single element that
        // has no value
        if ($this->object->exclusions && !$this->object->exclusions[0]) {
            $this->object->exclusions = array();
        }
        if ($this->object->entity_ids && !$this->object->entity_ids[0]) {
            $this->object->entity_ids = array();
        }
        if ($this->object->sortorder && !$this->object->sortorder[0]) {
            $this->object->sortorder = array();
        }
        return $valid;
    }
    /**
     * Returns a list of term ids for the list of tags
     * @global wpdb $wpdb
     * @param array $tags
     * @return array
     */
    function get_term_ids_for_tags($tags = FALSE)
    {
        global $wpdb;
        // If no tags were provided, get them from the container_ids
        if (!$tags || !is_array($tags)) {
            $tags = $this->object->container_ids;
        }
        // Convert container ids to a string suitable for WHERE IN
        $container_ids = array();
        if (is_array($tags) && !in_array('all', array_map('strtolower', $tags))) {
            foreach ($tags as $ndx => $container) {
                $container = esc_sql(str_replace('%', '%%', $container));
                $container_ids[] = "'{$container}'";
            }
            $container_ids = implode(',', $container_ids);
        }
        // Construct query
        $query = "SELECT {$wpdb->term_taxonomy}.term_id FROM {$wpdb->term_taxonomy}\n                  INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id\n                  WHERE {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id\n                  AND {$wpdb->term_taxonomy}.taxonomy = %s";
        if (!empty($container_ids)) {
            $query .= " AND ({$wpdb->terms}.slug IN ({$container_ids}) OR {$wpdb->terms}.name IN ({$container_ids}))";
        }
        $query .= " ORDER BY {$wpdb->terms}.term_id";
        $query = $wpdb->prepare($query, 'ngg_tag');
        // Get all term_ids for each image tag slug
        $term_ids = array();
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
     * @param stdClass $a
     * @param stdClass $b
     * @return int
     */
    function _sort_album_result($a, $b)
    {
        $key = $this->object->order_by;
        if (!isset($a->{$key}) || !isset($b->{$key})) {
            return 0;
        }
        return strcmp($a->{$key}, $b->{$key});
    }
}
/**
 * Provides instance methods useful for working with the C_Displayed_Gallery
 * model
 */
class Mixin_Displayed_Gallery_Instance_Methods extends Mixin
{
    function get_entity()
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
     * Gets the display type object used in this displayed gallery
     * @return C_Display_Type
     */
    function get_display_type()
    {
        return C_Display_Type_Mapper::get_instance()->find_by_name($this->object->display_type, TRUE);
    }
    /**
     * Gets the corresponding source instance
     * @return stdClass
     */
    function get_source()
    {
        return C_Displayed_Gallery_Source_Manager::get_instance()->get($this->object->source);
    }
    /**
     * Returns the galleries queries in this displayed gallery
     * @return array
     */
    function get_galleries()
    {
        $retval = array();
        if ($source = $this->object->get_source()) {
            if (in_array('image', $source->returns)) {
                $mapper = C_Gallery_Mapper::get_instance();
                $gallery_key = $mapper->get_primary_key_column();
                $mapper->select();
                if ($this->object->container_ids) {
                    $mapper->where(array("{$gallery_key} IN %s", $this->object->container_ids));
                }
                $retval = $mapper->run_query();
            }
        }
        return $retval;
    }
    /**
     * Gets albums queried in this displayed gallery
     * @return array
     */
    function get_albums()
    {
        $retval = array();
        if ($source = $this->object->get_source()) {
            if (in_array('album', $source->returns)) {
                $mapper = C_Album_Mapper::get_instance();
                $album_key = $mapper->get_primary_key_column();
                if ($this->object->container_ids) {
                    $mapper->select()->where(array("{$album_key} IN %s", $this->object->container_ids));
                }
                $retval = $mapper->run_query();
            }
        }
        return $retval;
    }
    /**
     * Returns a transient for the displayed gallery
     * @return string
     */
    function to_transient()
    {
        $params = $this->object->get_entity();
        unset($params->transient_id);
        $key = C_Photocrati_Transient_Manager::create_key('displayed_galleries', $params);
        if (is_null(C_Photocrati_Transient_Manager::fetch($key, NULL))) {
            C_Photocrati_Transient_Manager::update($key, $params, NGG_DISPLAYED_GALLERY_CACHE_TTL);
        }
        $this->object->transient_id = $key;
        if (!$this->object->id()) {
            $this->object->id($key);
        }
        return $key;
    }
    /**
     * Applies the values of a transient to this object
     * @param string $transient_id
     * @return bool
     */
    function apply_transient($transient_id = NULL)
    {
        $retval = FALSE;
        if (!$transient_id && isset($this->object->transient_id)) {
            $transient_id = $this->object->transient_id;
        }
        if ($transient_id && ($transient = C_Photocrati_Transient_Manager::fetch($transient_id, FALSE))) {
            // Ensure that the transient is an object, not array
            if (is_array($transient)) {
                $obj = new stdClass();
                foreach ($transient as $key => $value) {
                    $obj->{$key} = $value;
                }
                $transient = $obj;
            }
            $this->object->_stdObject = $transient;
            // Ensure that the display settings are an array
            $this->object->display_settings = $this->_object_to_array($this->object->display_settings);
            // Ensure that we have the most accurate transient id
            $this->object->transient_id = $transient_id;
            if (!$this->object->id()) {
                $this->object->id($transient_id);
            }
            $retval = TRUE;
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
 * Class C_Displayed_Gallery_Mapper
 * @mixin Mixin_Displayed_Gallery_Defaults
 * @implements I_Displayed_Gallery_Mapper
 */
class C_Displayed_Gallery_Mapper extends C_CustomPost_DataMapper_Driver
{
    static $_instances = array();
    function define($context = FALSE, $not_used = FALSE)
    {
        parent::define('displayed_gallery', array($context, 'displayed_gallery', 'display_gallery'));
        $this->add_mixin('Mixin_Displayed_Gallery_Defaults');
        $this->implement('I_Displayed_Gallery_Mapper');
        $this->set_model_factory_method('displayed_gallery');
        //		$this->add_post_hook(
        //			'save',
        //			'Propagate thumbnail dimensions',
        //			'Hook_Propagate_Thumbnail_Dimensions_To_Settings'
        //		);
    }
    /**
     * Initializes the mapper
     */
    function initialize()
    {
        parent::initialize();
    }
    /**
     * Gets a singleton of the mapper
     * @param string|bool $context
     * @return C_Displayed_Gallery_Mapper
     */
    public static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Displayed_Gallery_Mapper($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * Adds default values for the displayed gallery
 */
class Mixin_Displayed_Gallery_Defaults extends Mixin
{
    /**
     * Gets a display type object for a particular entity
     * @param stdClass|C_DataMapper_Model $entity
     * @return null|stdClass
     */
    function get_display_type($entity)
    {
        $mapper = C_Display_Type_Mapper::get_instance();
        return $mapper->find_by_name($entity->display_type);
    }
    /**
     * Sets defaults needed for the entity
     * @param object $entity
     */
    function set_defaults($entity)
    {
        // Ensure that we have a settings array
        if (!isset($entity->display_settings)) {
            $entity->display_settings = array();
        }
        // If the display type is set, then get it's settings and apply them as
        // defaults to the "display_settings" of the displayed gallery
        if (isset($entity->display_type)) {
            // Get display type mapper
            if ($display_type = $this->object->get_display_type($entity)) {
                $entity->display_settings = $this->array_merge_assoc($display_type->settings, $entity->display_settings, TRUE);
            }
        }
        // Default ordering
        $settings = C_NextGen_Settings::get_instance();
        $this->object->_set_default_value($entity, 'order_by', $settings->galSort);
        $this->object->_set_default_value($entity, 'order_direction', $settings->galSortDir);
        // Ensure we have an exclusions array
        $this->object->_set_default_value($entity, 'exclusions', array());
        // Ensure other properties exist
        $this->object->_set_default_value($entity, 'container_ids', array());
        $this->object->_set_default_value($entity, 'excluded_container_ids', array());
        $this->object->_set_default_value($entity, 'sortorder', array());
        $this->object->_set_default_value($entity, 'entity_ids', array());
        $this->object->_set_default_value($entity, 'returns', 'included');
        // Set maximum_entity_count
        $this->object->_set_default_value($entity, 'maximum_entity_count', $settings->maximum_entity_count);
    }
}
/**
 * Class C_Displayed_Gallery_Renderer
 * @mixin Mixin_Displayed_Gallery_Renderer
 * @implements I_Displayed_Gallery_Renderer
 */
class C_Displayed_Gallery_Renderer extends C_Component
{
    static $_instances = array();
    /**
     * Returns an instance of the class
     * @param bool|string $context
     * @return C_Displayed_Gallery_Renderer
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = __CLASS__;
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the object
     * @param bool $context
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Displayed_Gallery_Renderer');
        $this->implement('I_Displayed_Gallery_Renderer');
    }
}
/**
 * Provides the ability to render a display type
 */
class Mixin_Displayed_Gallery_Renderer extends Mixin
{
    function params_to_displayed_gallery($params)
    {
        $displayed_gallery = NULL;
        // Get the NextGEN settings to provide some defaults
        $settings = C_NextGen_Settings::get_instance();
        // Configure the arguments
        $defaults = array('id' => NULL, 'ids' => NULL, 'source' => '', 'src' => '', 'container_ids' => array(), 'gallery_ids' => array(), 'album_ids' => array(), 'tag_ids' => array(), 'display_type' => '', 'display' => '', 'exclusions' => array(), 'order_by' => $settings->galSort, 'order_direction' => $settings->galSortOrder, 'image_ids' => array(), 'entity_ids' => array(), 'tagcloud' => FALSE, 'returns' => 'included', 'slug' => NULL, 'sortorder' => array());
        $args = shortcode_atts($defaults, $params, 'ngg');
        // Are we loading a specific displayed gallery that's persisted?
        $mapper = C_Displayed_Gallery_Mapper::get_instance();
        if (!is_null($args['id'])) {
            $displayed_gallery = $mapper->find($args['id'], TRUE);
            unset($mapper);
            // no longer needed
        } else {
            // Perform some conversions...
            // Galleries?
            if ($args['gallery_ids']) {
                if ($args['source'] != 'albums' and $args['source'] != 'album') {
                    $args['source'] = 'galleries';
                    $args['container_ids'] = $args['gallery_ids'];
                    if ($args['image_ids']) {
                        $args['entity_ids'] = $args['image_ids'];
                    }
                } elseif ($args['source'] == 'albums') {
                    $args['entity_ids'] = $args['gallery_ids'];
                }
                unset($args['gallery_ids']);
            } elseif ($args['album_ids'] || $args['album_ids'] === '0') {
                $args['source'] = 'albums';
                $args['container_ids'] = $args['album_ids'];
                unset($args['albums_ids']);
            } elseif ($args['tag_ids']) {
                $args['source'] = 'tags';
                $args['container_ids'] = $args['tag_ids'];
                unset($args['tag_ids']);
            } elseif ($args['image_ids']) {
                $args['source'] = 'galleries';
                $args['entity_ids'] = $args['image_ids'];
                unset($args['image_ids']);
            } elseif ($args['tagcloud']) {
                $args['source'] = 'tags';
            }
            // Convert strings to arrays
            if (!empty($args['ids']) && !is_array($args['ids'])) {
                $args['container_ids'] = preg_split("/,|\\|/", $args['ids']);
                unset($args['ids']);
            }
            if (!is_array($args['container_ids'])) {
                $args['container_ids'] = preg_split("/,|\\|/", $args['container_ids']);
            }
            if (!is_array($args['exclusions'])) {
                $args['exclusions'] = preg_split("/,|\\|/", $args['exclusions']);
            }
            if (!is_array($args['entity_ids'])) {
                $args['entity_ids'] = preg_split("/,|\\|/", $args['entity_ids']);
            }
            if (!is_array($args['sortorder'])) {
                $args['sortorder'] = preg_split("/,|\\|/", $args['sortorder']);
            }
            // 'src' is used for legibility
            if (!empty($args['src']) && empty($args['source'])) {
                $args['source'] = $args['src'];
                unset($args['src']);
            }
            // 'display' is used for legibility
            if (!empty($args['display']) && empty($args['display_type'])) {
                $args['display_type'] = $args['display'];
                unset($args['display']);
            }
            // Get the display settings
            foreach (array_keys($defaults) as $key) {
                unset($params[$key]);
            }
            $args['display_settings'] = $params;
            // Create the displayed gallery
            $factory = C_Component_Factory::get_instance();
            $displayed_gallery = $factory->create('displayed_gallery', $args, $mapper);
            unset($factory);
        }
        // Validate the displayed gallery
        if ($displayed_gallery) {
            $displayed_gallery->validate();
        }
        return $displayed_gallery;
    }
    /**
     * Displays a "displayed gallery" instance
     *
     * Alias Properties:
     * gallery_ids/album_ids/tag_ids == container_ids
     * image_ids/gallery_ids		 == entity_ids
     *
     * Default Behavior:
     * - if order_by and order_direction are missing, the default settings
     *   are used from the "Other Options" page. The exception to this is
     *   when entity_ids are selected, in which the order is custom unless
     *   specified.
     *
     * How to use:
     *
     * To retrieve images from gallery 1 & 3, but exclude images 4 & 6:
     * [ngg gallery_ids="1,3" exclusions="4,6" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve images 1 & 2 from gallery 1:
     * [ngg gallery_ids="1" image_ids="1,2" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve images matching tags "landscapes" and "wedding shoots":
     * [ngg tag_ids="landscapes,wedding shoots" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve galleries from albums 1 & #, but exclude sub-album 1:
     * [ngg album_ids="1,2" exclusions="a1" display_type="photocrati-nextgen_basic_compact_album"]
     *
     * To retrieve galleries from albums 1 & 2, but exclude gallery 1:
     * [ngg album_ids="1,2" exclusions="1" display_type="photocrati-nextgen_basic_compact_album"]
     *
     * To retrieve image 2, 3, and 5 - independent of what container is used
     * [ngg image_ids="2,3,5" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve galleries 3 & 5, custom sorted, in album view
     * [ngg source="albums" gallery_ids="3,5" display_type="photocrati-nextgen_basic_compact_album"]
     *
     * To retrieve recent images, sorted by alt/title text
     * [ngg source="recent" order_by="alttext" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve random image
     * [ngg source="random" display_type="photocrati-nextgen_basic_thumbnails"]
     *
     * To retrieve a single image
     * [ngg image_ids='8' display_type='photocrati-nextgen_basic_singlepic']
     *
     * To retrieve a tag cloud
     * [ngg tagcloud=yes display_type='photocrati-nextgen_basic_tagcloud']
     *
     * @param array|C_Displayed_Gallery $params_or_dg
     * @param null|string $inner_content (optional)
     * @param bool|null $mode (optional)
     * @return string
     */
    function display_images($params_or_dg, $inner_content = NULL, $mode = NULL)
    {
        $retval = '';
        // Convert the array of parameters into a displayed gallery
        if (is_array($params_or_dg)) {
            $params = $params_or_dg;
            $displayed_gallery = $this->object->params_to_displayed_gallery($params);
        } elseif (is_object($params_or_dg) && get_class($params_or_dg) === 'C_Displayed_Gallery') {
            $displayed_gallery = $params_or_dg;
        } else {
            $displayed_gallery = NULL;
        }
        // Validate the displayed gallery
        if ($displayed_gallery && $displayed_gallery->validate()) {
            // Display!
            $retval = $this->object->render($displayed_gallery, TRUE, $mode);
        } else {
            if (C_NextGEN_Bootstrap::$debug) {
                $retval = __('We cannot display this gallery', 'nggallery') . $this->debug_msg($displayed_gallery->get_errors()) . $this->debug_msg($displayed_gallery->get_entity());
            } else {
                $retval = __('We cannot display this gallery', 'nggallery');
            }
        }
        return $retval;
    }
    function debug_msg($msg, $print_r = FALSE)
    {
        $retval = '';
        if (C_NextGEN_Bootstrap::$debug) {
            ob_start();
            if ($print_r) {
                echo '<pre>';
                print_r($msg);
                echo '</pre>';
            } else {
                var_dump($msg);
            }
            $retval = ob_get_clean();
        }
        return $retval;
    }
    /**
     * Gets the display type version associated with the displayed gallery
     * @param $display_type
     *
     * @return string
     */
    function get_display_type_version($display_type)
    {
        $retval = NGG_PLUGIN_VERSION;
        $registry = C_Component_Registry::get_instance();
        $module = $registry->get_module(isset($display_type->module_id) ? $display_type->module_id : $display_type->name);
        if ($module) {
            $retval = $module->module_version;
        }
        return $retval;
    }
    /**
     * Renders a displayed gallery on the frontend
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return
     * @param string|null $mode (optional)
     * @return string
     */
    function render($displayed_gallery, $return = FALSE, $mode = null)
    {
        $retval = '';
        $lookup = TRUE;
        // Simply throwing our rendered gallery into a feed will most likely not work correctly.
        // The MediaRSS option in NextGEN is available as an alternative.
        if (!C_NextGen_Settings::get_instance()->galleries_in_feeds && is_feed()) {
            return sprintf(__(' [<a href="%s">See image gallery at %s</a>] ', 'nggallery'), esc_url(apply_filters('the_permalink_rss', get_permalink())), $_SERVER['SERVER_NAME']);
        }
        if ($mode == null) {
            $mode = 'normal';
        }
        if (is_null($displayed_gallery->id())) {
            $displayed_gallery->id(md5(json_encode($displayed_gallery->get_entity())));
        }
        // Get the display type controller
        $controller = $this->get_registry()->get_utility('I_Display_Type_Controller', $displayed_gallery->display_type);
        // Get routing info
        $router = C_Router::get_instance();
        $url = $router->get_url($router->get_request_uri(), TRUE);
        // Should we lookup in cache?
        if (is_array($displayed_gallery->container_ids) && in_array('All', $displayed_gallery->container_ids)) {
            $lookup = FALSE;
        } elseif ($displayed_gallery->source == 'albums' && $controller->param('gallery') or $controller->param('album')) {
            $lookup = FALSE;
        } elseif (in_array($displayed_gallery->source, array('random', 'random_images'))) {
            $lookup = FALSE;
        } elseif ($controller->param('show')) {
            $lookup = FALSE;
        } elseif ($controller->is_cachable() === FALSE) {
            $lookup = FALSE;
        } elseif (!NGG_RENDERING_CACHE_ENABLED) {
            $lookup = FALSE;
        }
        // Enqueue any necessary static resources
        if ((!defined('NGG_SKIP_LOAD_SCRIPTS') || !NGG_SKIP_LOAD_SCRIPTS) && !$this->is_rest_request()) {
            $controller->enqueue_frontend_resources($displayed_gallery);
        }
        // Try cache lookup, if we're to do so
        $key = NULL;
        $html = FALSE;
        if ($lookup) {
            // The display type may need to output some things
            // even when serving from the cache
            if ($controller->has_method('cache_action')) {
                $retval = $controller->cache_action($displayed_gallery);
            }
            // Output debug message
            $retval .= $this->debug_msg("Lookup!");
            // Some settings affect display types
            $settings = C_NextGen_Settings::get_instance();
            $key_params = apply_filters('ngg_displayed_gallery_cache_params', array($displayed_gallery->get_entity(), $url, $mode, $settings->activateTags, $settings->appendType, $settings->maxImages, $settings->thumbEffect, $settings->thumbCode, $settings->galSort, $settings->galSortDir, $this->get_display_type_version($displayed_gallery->get_display_type())));
            // Any displayed gallery links on the home page will need to be regenerated if the permalink structure
            // changes
            if (is_home() or is_front_page()) {
                $key_params[] = get_option('permalink_structure');
            }
            // Try getting the rendered HTML from the cache
            $key = C_Photocrati_Transient_Manager::create_key('displayed_gallery_rendering', $key_params);
            $html = C_Photocrati_Transient_Manager::fetch($key, FALSE);
        } else {
            $retval .= $this->debug_msg("Not looking up in cache as per rules");
        }
        // TODO: This is hack. We need to figure out a more uniform way of detecting dynamic image urls
        if (strpos($html, C_Photocrati_Settings_Manager::get_instance()->dynamic_thumbnail_slug . '/') !== FALSE) {
            $html = FALSE;
            // forces the cache to be re-generated
        }
        // Output debug messages
        if ($html) {
            $retval .= $this->debug_msg("HIT!");
        } else {
            $retval .= $this->debug_msg("MISS!");
        }
        // If a cached version doesn't exist, then create the cache
        if (!$html) {
            $retval .= $this->debug_msg("Rendering displayed gallery");
            $current_mode = $controller->get_render_mode();
            $controller->set_render_mode($mode);
            $html = apply_filters('ngg_displayed_gallery_rendering', $controller->index_action($displayed_gallery, TRUE), $displayed_gallery);
            if ($key != null) {
                C_Photocrati_Transient_Manager::update($key, $html, NGG_RENDERING_CACHE_TTL);
            }
        }
        $retval .= $html;
        if (!$return) {
            echo $retval;
        }
        return $retval;
    }
    /**
     * @return bool
     */
    function is_rest_request()
    {
        return defined('REST_REQUEST') || strpos($_SERVER['REQUEST_URI'], 'wp-json') !== FALSE;
    }
}
class C_Displayed_Gallery_Source_Manager
{
    private $_sources = array();
    private $_entity_types = array();
    private $_registered_defaults = array();
    /* @var C_Displayed_Gallery_Source_Manager */
    static $_instance = NULL;
    /**
     * @return C_Displayed_Gallery_Source_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function register_defaults()
    {
        // Entity types must be registered first!!!
        // ----------------------------------------
        $this->register_entity_type('gallery', 'galleries');
        $this->register_entity_type('image', 'images');
        $this->register_entity_type('album', 'albums');
        // Galleries
        $galleries = new stdClass();
        $galleries->name = 'galleries';
        $galleries->title = __('Galleries', 'nggallery');
        $galleries->aliases = array('gallery', 'images', 'image');
        $galleries->returns = array('image');
        $this->register($galleries->name, $galleries);
        // Albums
        $albums = new stdClass();
        $albums->name = 'albums';
        $albums->title = __('Albums', 'nggallery');
        $albums->aliases = array('album');
        $albums->returns = array('album', 'gallery');
        $this->register($albums->name, $albums);
        // Tags
        $tags = new stdClass();
        $tags->name = 'tags';
        $tags->title = __('Tags', 'nggallery');
        $tags->aliases = array('tag', 'image_tags', 'image_tag');
        $tags->returns = array('image');
        $this->register($tags->name, $tags);
        // Random Images;
        $random = new stdClass();
        $random->name = 'random_images';
        $random->title = __('Random Images', 'nggallery');
        $random->aliases = array('random', 'random_image');
        $random->returns = array('image');
        $this->register($random->name, $random);
        // Recent Images
        $recent = new stdClass();
        $recent->name = 'recent_images';
        $recent->title = __('Recent Images', 'nggallery');
        $recent->aliases = array('recent', 'recent_image');
        $recent->returns = array('image');
        $this->register($recent->name, $recent);
        $this->_registered_defaults = TRUE;
    }
    function register($name, $properties)
    {
        // We'll use an object to represent the source
        $object = $properties;
        if (!is_object($properties)) {
            $object = new stdClass();
            foreach ($properties as $k => $v) {
                $object->{$k} = $v;
            }
        }
        // Set default properties
        $object->name = $name;
        if (!isset($object->title)) {
            $object->title = $name;
        }
        if (!isset($object->returns)) {
            $object->returns = array();
        }
        if (!isset($object->aliases)) {
            $object->aliases = array();
        }
        // Add internal reference
        $this->_sources[$name] = $object;
        foreach ($object->aliases as $name) {
            $this->_sources[$name] = $object;
        }
    }
    function register_entity_type()
    {
        $aliases = func_get_args();
        $name = array_shift($aliases);
        $this->_entity_types[] = $name;
        foreach ($aliases as $alias) {
            $this->_entity_types[$alias] = $name;
        }
    }
    function deregister($name)
    {
        if ($source = $this->get($name)) {
            unset($this->_sources[$name]);
            foreach ($source->aliases as $alias) {
                unset($this->_sources[$alias]);
            }
        }
    }
    function deregister_entity_type($name)
    {
        unset($this->_entity_types[$name]);
    }
    function get($name_or_alias)
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $retval = NULL;
        if (isset($this->_sources[$name_or_alias])) {
            $retval = $this->_sources[$name_or_alias];
        }
        return $retval;
    }
    function get_entity_type($name)
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $found = array_search($name, $this->_entity_types);
        if ($found) {
            return $this->_entity_types[$found];
        } else {
            return NULL;
        }
    }
    function get_all()
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        $retval = array();
        foreach (array_values($this->_sources) as $source_obj) {
            if (!in_array($source_obj, $retval)) {
                $retval[] = $source_obj;
            }
        }
        usort($retval, array(&$this, '__sort_by_name'));
        return $retval;
    }
    function __sort_by_name($a, $b)
    {
        return strcmp($a->name, $b->name);
    }
    function get_all_entity_types()
    {
        if (!$this->_registered_defaults) {
            $this->register_defaults();
        }
        return array_unique(array_values($this->_entity_types));
    }
    function is_registered($name)
    {
        return !is_null($this->get($name));
    }
    function is_valid_entity_type($name)
    {
        return !is_null($this->get_entity_type($name));
    }
    function deregister_all()
    {
        $this->_sources = array();
        $this->_entity_types = array();
        $this->_registered_defaults = FALSE;
    }
    function is_compatible($source, $display_type)
    {
        $retval = FALSE;
        if ($source = $this->get($source->name)) {
            // Get the real entity type names for the display type
            $display_type_entity_types = array();
            foreach ($display_type->entity_types as $type) {
                $result = $this->get_entity_type($type);
                if ($result) {
                    $display_type_entity_types[] = $result;
                }
            }
            foreach ($source->returns as $entity_type) {
                if (in_array($entity_type, $display_type_entity_types, TRUE)) {
                    $retval = TRUE;
                    break;
                }
            }
        }
        return $retval;
    }
}
abstract class C_Displayed_Gallery_Trigger
{
    static function is_renderable($name, $displayed_gallery)
    {
        return TRUE;
    }
    function get_css_class()
    {
        return 'far fa-circle';
    }
    function get_attributes()
    {
        return array('class' => $this->get_css_class());
    }
    function render()
    {
        $attributes = array();
        foreach ($this->get_attributes() as $k => $v) {
            $k = esc_attr($k);
            $v = esc_attr($v);
            $attributes[] = "{$k}='{$v}'";
        }
        $attributes = implode(" ", $attributes);
        return "<i {$attributes}></i>";
    }
}
/**
 * The Trigger Manager displays "trigger buttons" for a displayed gallery.
 *
 * Each display type can register a "handler", which is a class with a render method, which is used
 * to render the display of the trigger buttons.
 *
 * Each trigger button is registered with a handler, which is also a class with a render() method.
 * Class C_Displayed_Gallery_Trigger_Manager
 */
class C_Displayed_Gallery_Trigger_Manager
{
    static $_instance = NULL;
    private $_triggers = array();
    private $_trigger_order = array();
    private $_display_type_handlers = array();
    private $_default_display_type_handler = NULL;
    private $css_class = 'ngg-trigger-buttons';
    private $_default_image_types = array('photocrati-nextgen_basic_thumbnails', 'photocrati-nextgen_basic_singlepic', 'photocrati-nextgen_pro_thumbnail_grid', 'photocrati-nextgen_pro_blog_gallery', 'photocrati-nextgen_pro_film');
    /**
     * @return C_Displayed_Gallery_Trigger_Manager
     */
    static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function __construct()
    {
        $this->_default_display_type_handler = 'C_Displayed_Gallery_Trigger_Handler';
        foreach ($this->_default_image_types as $display_type) {
            $this->register_display_type_handler($display_type, 'C_Displayed_Gallery_Image_Trigger_Handler');
        }
    }
    function register_display_type_handler($display_type, $klass)
    {
        $this->_display_type_handlers[$display_type] = $klass;
    }
    function deregister_display_type_handler($display_type)
    {
        unset($this->_display_type_handlers[$display_type]);
    }
    function add($name, $handler)
    {
        $this->_triggers[$name] = $handler;
        $this->_trigger_order[] = $name;
        return $this;
    }
    function remove($name)
    {
        $order = array();
        unset($this->_triggers[$name]);
        foreach ($this->_trigger_order as $trigger) {
            if ($trigger != $name) {
                $order[] = $trigger;
            }
        }
        $this->_trigger_order = $order;
        return $this;
    }
    function _rebuild_index()
    {
        $order = array();
        foreach ($this->_trigger_order as $name) {
            $order[] = $name;
        }
        $this->_trigger_order = $order;
        return $this;
    }
    function increment_position($name)
    {
        if (($current_index = array_search($name, $this->_trigger_order)) !== FALSE) {
            $next_index = $current_index += 1;
            // 1,2,3,4,5 => 1,2,4,3,5
            if (isset($this->_trigger_order[$next_index])) {
                $next = $this->_trigger_order[$next_index];
                $this->_trigger_order[$next_index] = $name;
                $this->_trigger_order[$current_index] = $next;
            }
        }
        return $this->position_of($name);
    }
    function decrement_position($name)
    {
        if (($current_index = array_search($name, $this->_trigger_order)) !== FALSE) {
            $previous_index = $current_index -= 1;
            if (isset($this->_trigger_order[$previous_index])) {
                $previous = $this->_trigger_order[$previous_index];
                $this->_trigger_order[$previous_index] = $name;
                $this->_trigger_order[$current_index] = $previous;
            }
        }
        return $this->position_of($name);
    }
    function position_of($name)
    {
        return array_search($name, $this->_trigger_order);
    }
    function move_to_position($name, $position_index)
    {
        if (($current_index = $this->position_of($name)) !== FALSE) {
            $func = 'increment_position';
            if ($current_index < $position_index) {
                $func = 'decrement_position';
            }
            while ($this->position_of($name) != $position_index) {
                $this->{$func}($name);
            }
        }
        return $this->position_of($name);
    }
    function move_to_start($name)
    {
        if ($index = $this->position_of($name)) {
            unset($this->_trigger_order[$index]);
            array_unshift($this->_trigger_order, $name);
            $this->_rebuild_index();
        }
        return $this->position_of($name);
    }
    function count()
    {
        return count($this->_trigger_order);
    }
    function move_to_end($name)
    {
        $index = $this->position_of($name);
        if ($index !== FALSE or $index != $this->count() - 1) {
            unset($this->_trigger_order[$index]);
            $this->_trigger_order[] = $name;
            $this->_rebuild_index();
        }
        return $this->position_of($name);
    }
    function get_handler_for_displayed_gallery($displayed_gallery)
    {
        // Find the trigger handler for the current display type.
        // First, check the display settings for the displayed gallery. Some third-party
        // display types might specify their own handler
        $klass = NULL;
        if (isset($displayed_gallery->display_settings['trigger_handler'])) {
            $klass = $displayed_gallery->display_settings['trigger_handler'];
        } else {
            $klass = $this->_default_display_type_handler;
            if (isset($this->_display_type_handlers[$displayed_gallery->display_type])) {
                $klass = $this->_display_type_handlers[$displayed_gallery->display_type];
            }
        }
        return $klass;
    }
    function render($view, $displayed_gallery)
    {
        if ($klass = $this->get_handler_for_displayed_gallery($displayed_gallery)) {
            $handler = new $klass();
            $handler->view = $view;
            $handler->displayed_gallery = $displayed_gallery;
            $handler->manager = $this;
            if (method_exists($handler, 'render')) {
                $handler->render();
            }
        }
        return $view;
    }
    function render_trigger($name, $view, $displayed_gallery)
    {
        $retval = '';
        if (isset($this->_triggers[$name])) {
            $klass = $this->_triggers[$name];
            if (call_user_func(array($klass, 'is_renderable'), $name, $displayed_gallery)) {
                $handler = new $klass();
                $handler->name = $name;
                $handler->view = $this->view = $view;
                $handler->displayed_gallery = $displayed_gallery;
                $retval = $handler->render();
            }
        }
        return $retval;
    }
    function render_triggers($view, $displayed_gallery)
    {
        $output = FALSE;
        $css_class = esc_attr($this->css_class);
        $retval = array("<div class='{$css_class}'>");
        foreach ($this->_trigger_order as $name) {
            if ($markup = $this->render_trigger($name, $view, $displayed_gallery)) {
                $output = TRUE;
                $retval[] = $markup;
            }
        }
        if ($output) {
            $retval[] = "</div>";
            $retval = implode("\n", $retval);
        } else {
            $retval = '';
        }
        return $retval;
    }
    function enqueue_resources($displayed_gallery)
    {
        if ($handler = $this->get_handler_for_displayed_gallery($displayed_gallery)) {
            wp_enqueue_style('fontawesome');
            wp_enqueue_style('ngg_trigger_buttons');
            if (method_exists($handler, 'enqueue_resources')) {
                call_user_func(array($handler, 'enqueue_resources'), $displayed_gallery);
                foreach ($this->_trigger_order as $name) {
                    $handler = $this->_triggers[$name];
                    $renderable = TRUE;
                    if (method_exists($handler, 'is_renderable')) {
                        $renderable = call_user_func($handler, 'is_renderable', $name, $displayed_gallery);
                    }
                    if ($renderable && method_exists($handler, 'enqueue_resources')) {
                        call_user_func(array($handler, 'enqueue_resources', $name, $displayed_gallery));
                    }
                }
            }
        }
    }
}
class C_Displayed_Gallery_Image_Trigger_Handler
{
    function render()
    {
        foreach ($this->view->find('nextgen_gallery.image', true) as $image_element) {
            $image_element->append($this->manager->render_triggers($image_element, $this->displayed_gallery));
        }
    }
}
class C_Displayed_Gallery_Trigger_Handler
{
    function render()
    {
        $this->view->append($this->manager->render_triggers($this->view, $this->displayed_gallery));
    }
}
/**
 * Class Mixin_Display_Type_Form
 * @mixin C_Form
 */
class Mixin_Display_Type_Form extends Mixin
{
    var $_model = null;
    function initialize()
    {
        $this->object->implement('I_Display_Type_Form');
    }
    /**
     * A wrapper to wp_enqueue_script() and ATP's mark_script()
     *
     * Unlike wp_enqueue_script() the version parameter is last as NGG should always use NGG_SCRIPT_VERSION
     * @param string $handle
     * @param string $source
     * @param array $dependencies
     * @param bool $in_footer
     * @param string $version
     */
    public function enqueue_script($handle, $source = '', $dependencies = array(), $in_footer = FALSE, $version = NGG_SCRIPT_VERSION)
    {
        wp_enqueue_script($handle, $source, $dependencies, $version, $in_footer);
        $atp = C_Attach_Controller::get_instance();
        if ($atp !== NULL) {
            $atp->mark_script($handle);
        }
    }
    /**
     * A wrapper to wp_enqueue_style()
     *
     * Unlike wp_enqueue_style() the version parameter is last as NGG should always use NGG_SCRIPT_VERSION
     * @param string $handle
     * @param string $source
     * @param array $dependencies
     * @param string $media
     * @param string $version
     */
    public function enqueue_style($handle, $source = '', $dependencies = array(), $media = 'all', $version = NGG_SCRIPT_VERSION)
    {
        wp_enqueue_style($handle, $source, $dependencies, $version, $media);
    }
    /**
     * Returns the name of the display type. Sub-class should override
     * @throws Exception
     * @return string
     */
    function get_display_type_name()
    {
        throw new Exception(__METHOD__ . " not implemented");
    }
    /**
     * Returns the model (display type) used in the form
     * @return stdClass
     */
    function get_model()
    {
        if ($this->_model == null) {
            $mapper = C_Display_Type_Mapper::get_instance();
            $this->_model = $mapper->find_by_name($this->object->get_display_type_name(), TRUE);
        }
        return $this->_model;
    }
    /**
     * Returns the title of the form, which is the title of the display type
     * @return string
     */
    function get_title()
    {
        return __($this->object->get_model()->title, 'nggallery');
    }
    /**
     * Saves the settings for the display type
     * @param array $attributes
     * @return boolean
     */
    function save_action($attributes = array())
    {
        return $this->object->get_model()->save(array('settings' => $attributes));
    }
    /**
     * Renders the AJAX pagination settings field
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_ajax_pagination_field($display_type)
    {
        return $this->object->_render_radio_field($display_type, 'ajax_pagination', __('Enable AJAX pagination', 'nggallery'), isset($display_type->settings['ajax_pagination']) ? $display_type->settings['ajax_pagination'] : FALSE, __('Browse images without reloading the page.', 'nggallery'));
    }
    function _render_thumbnail_override_settings_field($display_type)
    {
        $enabled = isset($display_type->settings['override_thumbnail_settings']) ? $display_type->settings['override_thumbnail_settings'] : FALSE;
        $hidden = !$enabled;
        $width = $enabled && isset($display_type->settings['thumbnail_width']) ? intval($display_type->settings['thumbnail_width']) : 0;
        $height = $enabled && isset($display_type->settings['thumbnail_height']) ? intval($display_type->settings['thumbnail_height']) : 0;
        $crop = $enabled && isset($display_type->settings['thumbnail_crop']) ? $display_type->settings['thumbnail_crop'] : FALSE;
        $override_field = $this->_render_radio_field($display_type, 'override_thumbnail_settings', __('Override thumbnail settings', 'nggallery'), $enabled, __("This does not affect existing thumbnails; overriding the thumbnail settings will create an additional set of thumbnails. To change the size of existing thumbnails please visit 'Manage Galleries' and choose 'Create new thumbnails' for all images in the gallery.", 'nggallery'));
        $dimensions_field = $this->object->render_partial('photocrati-nextgen_gallery_display#thumbnail_settings', array('display_type_name' => $display_type->name, 'name' => 'thumbnail_dimensions', 'label' => __('Thumbnail dimensions', 'nggallery'), 'thumbnail_width' => $width, 'thumbnail_height' => $height, 'hidden' => $hidden ? 'hidden' : '', 'text' => ''), TRUE);
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
    function _render_image_override_settings_field($display_type)
    {
        $hidden = !(isset($display_type->settings['override_image_settings']) ? $display_type->settings['override_image_settings'] : FALSE);
        $override_field = $this->_render_radio_field($display_type, 'override_image_settings', __('Override image settings', 'nggallery'), isset($display_type->settings['override_image_settings']) ? $display_type->settings['override_image_settings'] : 0, __('Overriding the image settings will create an additional set of images', 'nggallery'));
        $qualities = array();
        for ($i = 100; $i > 40; $i -= 5) {
            $qualities[$i] = "{$i}%";
        }
        $quality_field = $this->_render_select_field($display_type, 'image_quality', __('Image quality', 'nggallery'), $qualities, $display_type->settings['image_quality'], '', $hidden);
        $crop_field = $this->_render_radio_field($display_type, 'image_crop', __('Image crop', 'nggallery'), $display_type->settings['image_crop'], '', $hidden);
        $watermark_field = $this->_render_radio_field($display_type, 'image_watermark', __('Image watermark', 'nggallery'), $display_type->settings['image_watermark'], '', $hidden);
        $everything = $override_field . $quality_field . $crop_field . $watermark_field;
        return $everything;
    }
    function _render_display_view_field($display_type)
    {
        $display_type_views = $this->get_available_display_type_views($display_type);
        $current_value = isset($display_type->settings['display_type_view']) ? $display_type->settings['display_type_view'] : '';
        if (isset($display_type->settings['display_view'])) {
            $current_value = $display_type->settings['display_view'];
        }
        return $this->object->_render_select_field($display_type, 'display_view', __('Select View', 'nggallery'), $display_type_views, $current_value, '', FALSE);
    }
    /**
     * Renders a field for selecting a template
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_display_type_view_field($display_type)
    {
        $display_type_views = $this->get_available_display_type_views($display_type);
        return $this->object->_render_select_field($display_type, 'display_type_view', __('Select View', 'nggallery'), $display_type_views, $display_type->settings['display_type_view'], '', FALSE);
    }
    /**
     * Gets available templates
     *
     * @param C_Display_Type $display_type
     * @return array
     */
    function get_available_display_type_views($display_type)
    {
        /* Set up templates array */
        if (strpos($display_type->name, 'basic') !== false) {
            $views = array('default' => __('Legacy', 'nggallery'));
        } else {
            $views = array('default' => __('Default', 'nggallery'));
        }
        /* Fetch array of directories to scan */
        $dirs = M_Gallery_Display::get_display_type_view_dirs($display_type);
        /* Populate the views array by scanning each directory for relevant templates */
        foreach ($dirs as $dir_name => $dir) {
            /* Confirm directory exists */
            if (!file_exists($dir) || !is_dir($dir)) {
                continue;
            }
            /* Scan for template files and create array */
            $files = scandir($dir);
            $template_files = preg_grep('/^.+\\-(template|view).php$/i', $files);
            $template_files = $template_files ? array_combine($template_files, $template_files) : array();
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