<?php
/**
 * Class A_NextGen_Basic_Template_Form
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
    function _render_nextgen_basic_templates_template_field($display_type)
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
                $prefix = FALSE;
                break;
        }
        // ensure the current file is in the list
        $templates = $this->object->_get_available_templates($prefix);
        if (!isset($templates[$display_type->settings['template']])) {
            $templates[$display_type->settings['template']] = $display_type->settings['template'];
        }
        // add <default> template that acts the same way as having no template specified
        $templates['default'] = __('Default', 'nggallery');
        return $this->object->render_partial('photocrati-nextgen_basic_templates#nextgen_basic_templates_settings_template', array('display_type_name' => $display_type->name, 'template_label' => __('Legacy (Old) Templates', 'nggallery'), 'template_text' => __('Use a legacy template when rendering (not recommended).', 'nggallery'), 'chosen_file' => $display_type->settings['template'], 'templates' => $templates), True);
    }
    /**
     * Retrieves listing of available templates
     *
     * Override this function to modify or add to the available templates listing, array format
     * is array(file_abspath => label)
     * @return array
     */
    function _get_available_templates($prefix = FALSE)
    {
        $templates = array();
        foreach (C_Legacy_Template_Locator::get_instance()->find_all($prefix) as $label => $files) {
            foreach ($files as $file) {
                $tmp = explode(DIRECTORY_SEPARATOR, $file);
                $templates[$file] = "{$label}: " . end($tmp);
            }
        }
        asort($templates);
        return $templates;
    }
    /**
     * Returns the parameter objects necessary for legacy template rendering (legacy_render())
     *
     * @param $images
     * @param $displayed_gallery
     * @param array $params
     *
     * @return array
     */
    function prepare_legacy_parameters($images, $displayed_gallery, $params = array())
    {
        // setup
        $image_map = C_Image_Mapper::get_instance();
        $gallery_map = C_Gallery_Mapper::get_instance();
        $image_key = $image_map->get_primary_key_column();
        $gallery_key = $gallery_map->get_primary_key_column();
        $gallery_id = $displayed_gallery->id();
        $pid = $this->object->param('pid');
        // because picture_list implements ArrayAccess any array-specific actions must be taken on
        // $picture_list->container or they won't do anything
        $picture_list = new C_Image_Wrapper_Collection();
        $current_pid = NULL;
        // begin processing
        $current_page = @get_the_ID() == FALSE ? 0 : @get_the_ID();
        // determine what the "current image" is; used mostly for carousel
        if (!is_numeric($pid) && !empty($pid)) {
            $picture = $image_map->find_first(array('image_slug = %s', $pid));
            $pid = $picture->{$image_key};
        }
        // create our new wrappers
        foreach ($images as &$image) {
            if ($image && isset($params['effect_code'])) {
                if (is_object($image)) {
                    $image->thumbcode = $params['effect_code'];
                } elseif (is_array($image)) {
                    $image['thumbcode'] = $params['effect_code'];
                }
            }
            $new_image = new C_Image_Wrapper($image, $displayed_gallery);
            if ($pid == $new_image->{$image_key}) {
                $current_pid = $new_image;
            }
            $picture_list[] = $new_image;
        }
        reset($picture_list->container);
        // assign current_pid
        $current_pid = is_null($current_pid) ? current($picture_list->container) : $current_pid;
        foreach ($picture_list as &$image) {
            if (isset($image->hidden) && $image->hidden) {
                $tmp = $displayed_gallery->display_settings['number_of_columns'];
                $image->style = $tmp > 0 ? 'style="width:' . floor(100 / $tmp) . '%;display: none;"' : 'style="display: none;"';
            }
        }
        // find our gallery to build the new one on
        $orig_gallery = $gallery_map->find(current($picture_list->container)->galleryid);
        // create the 'gallery' object
        $gallery = new stdclass();
        $gallery->ID = $displayed_gallery->id();
        $gallery->name = stripslashes($orig_gallery->name);
        $gallery->title = stripslashes($orig_gallery->title);
        $gallery->description = html_entity_decode(stripslashes($orig_gallery->galdesc));
        $gallery->pageid = $orig_gallery->pageid;
        $gallery->anchor = 'ngg-gallery-' . $gallery_id . '-' . $current_page;
        $gallery->displayed_gallery =& $displayed_gallery;
        $gallery->columns = @intval($displayed_gallery->display_settings['number_of_columns']);
        $gallery->imagewidth = $gallery->columns > 0 ? 'style="width:' . floor(100 / $gallery->columns) . '%;"' : '';
        if (!empty($displayed_gallery->display_settings['show_slideshow_link'])) {
            $gallery->show_slideshow = TRUE;
            $gallery->slideshow_link = $params['slideshow_link'];
            $gallery->slideshow_link_text = $displayed_gallery->display_settings['slideshow_link_text'];
        } else {
            $gallery->show_slideshow = FALSE;
        }
        $gallery = apply_filters('ngg_gallery_object', $gallery, 4);
        // build our array of things to return
        $return = array('registry' => C_Component_Registry::get_instance(), 'gallery' => $gallery);
        // single_image is an internally added flag
        if (!empty($params['single_image'])) {
            $return['image'] = $picture_list[0];
        } else {
            $return['current'] = $current_pid;
            $return['images'] = $picture_list->container;
        }
        // this is expected to always exist
        if (!empty($params['pagination'])) {
            $return['pagination'] = $params['pagination'];
        } else {
            $return['pagination'] = NULL;
        }
        if (!empty($params['next'])) {
            $return['next'] = $params['next'];
        } else {
            $return['next'] = FALSE;
        }
        if (!empty($params['prev'])) {
            $return['prev'] = $params['prev'];
        } else {
            $return['prev'] = FALSE;
        }
        return $return;
    }
    function enqueue_static_resources()
    {
        wp_enqueue_style('ngg_template_settings', $this->get_static_url('photocrati-nextgen_basic_templates#ngg_template_settings.css'));
        wp_enqueue_script('ngg_template_settings', $this->get_static_url('photocrati-nextgen_basic_templates#ngg_template_settings.js'), array('ngg_select2'), TRUE);
        wp_localize_script('ngg_template_settings', 'ngg_template_settings', array('placeholder_text' => __('No template selected')));
    }
}
/**
 * Provides a utility to locate legacy templates
 * @mixin Mixin_Legacy_Template_Locator
 * @implements I_Legacy_Template_Locator
 */
class C_Legacy_Template_Locator extends C_Component
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Legacy_Template_Locator');
        $this->implement('I_Legacy_Template_Locator');
    }
    /**
     * @param bool|string $context
     * @return C_Legacy_Template_Locator
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * Provides instance methods for the legacy template locator
 */
class Mixin_Legacy_Template_Locator extends Mixin
{
    /**
     * Returns an array of template storing directories
     *
     * @return array Template storing directories
     */
    function get_template_directories()
    {
        return apply_filters('ngg_legacy_template_directories', array('Child Theme' => get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR, 'Parent Theme' => get_template_directory() . DIRECTORY_SEPARATOR . 'nggallery' . DIRECTORY_SEPARATOR, 'NextGEN Legacy' => NGGALLERY_ABSPATH . 'view' . DIRECTORY_SEPARATOR, 'NextGEN Overrides' => implode(DIRECTORY_SEPARATOR, array(WP_CONTENT_DIR, 'ngg', 'legacy', 'templates'))));
    }
    /**
     * Returns an array of all available template files
     *
     * @return array All available template files
     */
    function find_all($prefix = FALSE)
    {
        $files = array();
        foreach ($this->object->get_template_directories() as $label => $dir) {
            $tmp = $this->object->get_templates_from_dir($dir, $prefix);
            if (!$tmp) {
                continue;
            }
            $files[$label] = $tmp;
        }
        return $files;
    }
    /**
     * Recursively scans $dir for files ending in .php
     *
     * @param string $dir Directory
     * @return array All php files in $dir
     */
    function get_templates_from_dir($dir, $prefix = FALSE)
    {
        if (!is_dir($dir)) {
            return array();
        }
        $dir = new RecursiveDirectoryIterator($dir);
        $iterator = new RecursiveIteratorIterator($dir);
        // convert single-item arrays to string
        if (is_array($prefix) && count($prefix) <= 1) {
            $prefix = end($prefix);
        }
        // we can filter results by allowing a set of prefixes, one prefix, or by showing all available files
        if (is_array($prefix)) {
            $str = implode('|', $prefix);
            $regex_iterator = new RegexIterator($iterator, "/({$str})-.+\\.php\$/i", RecursiveRegexIterator::GET_MATCH);
        } elseif (is_string($prefix)) {
            $regex_iterator = new RegexIterator($iterator, "#(.*)[/\\\\]{$prefix}\\-?.*\\.php\$#i", RecursiveRegexIterator::GET_MATCH);
        } else {
            $regex_iterator = new RegexIterator($iterator, '/^.+\\.php$/i', RecursiveRegexIterator::GET_MATCH);
        }
        $files = array();
        foreach ($regex_iterator as $filename) {
            $files[] = reset($filename);
        }
        return $files;
    }
    /**
     * Find a particular template by name
     * @param string $template_name
     * @return string
     */
    function find($template_name)
    {
        $template_abspath = FALSE;
        // hook into the render feature to allow other plugins to include templates
        $custom_template = apply_filters('ngg_render_template', FALSE, $template_name);
        if ($custom_template === FALSE) {
            $custom_template = $template_name;
        }
        // Ensure we have a PHP extension
        if (strpos($custom_template, '.php') === FALSE) {
            $custom_template .= '.php';
        }
        // Find the abspath of the template to render
        if (!@file_exists($custom_template)) {
            foreach ($this->object->get_template_directories() as $dir) {
                if ($template_abspath) {
                    break;
                }
                $filename = implode(DIRECTORY_SEPARATOR, array(rtrim($dir, "/\\"), $custom_template));
                if (@file_exists($filename)) {
                    $template_abspath = $filename;
                } elseif (strpos($custom_template, '-template') === FALSE) {
                    $filename = implode(DIRECTORY_SEPARATOR, array(rtrim($dir, "/\\"), str_replace('.php', '', $custom_template) . '-template.php'));
                    if (@file_exists($filename)) {
                        $template_abspath = $filename;
                    }
                }
            }
        } else {
            if (!preg_match('#\\.\\.[/\\\\]#', $custom_template)) {
                // An absolute path was already given
                $template_abspath = $custom_template;
            }
        }
        return $template_abspath;
    }
}
/**
 * Class Mixin_NextGen_Basic_Templates
 * @mixin C_Display_Type_Controller
 *
 * Provides a mixin that other display type controllers can use to render the ngglegacy templates
 */
class Mixin_NextGen_Basic_Templates extends Mixin
{
    /**
     * Renders NextGen-Legacy style templates
     *
     * @param string $template_name File name
     * @param array $vars (optional) Specially formatted array of parameters
     * @param bool $return (optional)
     * @param string $prefix (optional)
     * @return string
     */
    function legacy_render($template_name, $vars = array(), $return = FALSE, $prefix = NULL)
    {
        $retval = "[Not a valid template]";
        $template_locator = C_Legacy_Template_Locator::get_instance();
        // search first for files with their prefix
        $template_abspath = $template_locator->find($prefix . '-' . $template_name);
        if (!$template_abspath) {
            $template_abspath = $template_locator->find($template_name);
        }
        if ($template_abspath) {
            // render the template
            extract($vars);
            if ($return) {
                ob_start();
            }
            include $template_abspath;
            if ($return) {
                $retval = ob_get_contents();
                ob_end_clean();
            }
        }
        return $retval;
    }
}