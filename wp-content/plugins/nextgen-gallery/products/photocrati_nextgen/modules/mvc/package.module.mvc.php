<?php
/**
 * Class A_MVC_Factory
 *
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_MVC_Factory extends Mixin
{
    public function mvc_view($template, $params = array(), $engine = 'php', $context = false, $new_template_path = '')
    {
        return new C_MVC_View($template, $params, $engine, $context, $new_template_path);
    }
}
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Provides actions that are executed based on the requested url
 *
 * @mixin Mixin_MVC_Controller_Instance_Methods
 */
abstract class C_MVC_Controller extends C_Component
{
    var $_content_type = 'text/html';
    var $message = '';
    var $debug = false;
    public function define($context = false)
    {
        parent::define($context);
        $this->add_mixin('Mixin_MVC_Controller_Instance_Methods');
        $this->implement('I_MVC_Controller');
    }
    public function set_content_type($type)
    {
        switch ($type) {
            case 'html':
            case 'xhtml':
                $type = 'text/html';
                break;
            case 'xml':
                $type = 'text/xml';
                break;
            case 'rss':
            case 'rss2':
                $type = 'application/rss+xml';
                break;
            case 'css':
                $type = 'text/css';
                break;
            case 'javascript':
            case 'jscript':
            case 'emcascript':
                $type = 'text/javascript';
                break;
            case 'json':
                $type = 'application/json';
                break;
            case 'jpeg':
            case 'jpg':
            case 'jpe':
                $type = 'image/jpeg';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'png':
                $type = 'image/x-png';
                break;
            case 'tiff':
            case 'tif':
                $type = 'image/tiff';
                break;
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'webp':
                $type = 'image/webp';
                break;
        }
        $this->object->_content_type = $type;
        return $type;
    }
    public function expires($time)
    {
        $time = strtotime($time);
        if (!headers_sent()) {
            header('Expires: ' . strftime('%a, %d %b %Y %H:%M:%S %Z', $time));
        }
    }
    public function http_error($message, $code = 501)
    {
        $this->message = $message;
        $method = "http_{$code}_action";
        $this->{$method}();
    }
    public function is_valid_request($method)
    {
        return true;
    }
    public function is_post_request()
    {
        return 'POST' == $this->object->get_router()->get_request_method();
    }
    public function is_get_request()
    {
        return 'GET' == $this->object->get_router()->get_request_method();
    }
    public function is_delete_request()
    {
        return 'DELETE' == $this->object->get_router()->get_request_method();
    }
    public function is_put_request()
    {
        return 'PUT' == $this->object->get_router()->get_request_method();
    }
    public function do_not_cache()
    {
        if (!headers_sent()) {
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
        }
    }
    public function is_custom_request($type)
    {
        return strtolower($type) == strtolower($this->object->get_router()->get_request_method());
    }
    /**
     * @return \Imagely\NGG\Util\Router
     */
    public function get_router()
    {
        return \Imagely\NGG\Util\Router::get_instance();
    }
    /**
     * @return C_Routing_App
     */
    public function get_routed_app()
    {
        return $this->object->get_router()->get_routed_app();
    }
    public function remove_param_for($url, $key, $id = null)
    {
        $app = $this->object->get_routed_app();
        $retval = $app->remove_parameter($key, $id, $url);
        return $retval;
    }
    /**
     * Gets the absolute path of a static resource
     *
     * @param string       $path
     * @param string|false $module (optional).
     * @return string
     */
    public function get_static_abspath($path, $module = false)
    {
        return \Imagely\NGG\Display\StaticPopeAssets::get_abspath($path, $module);
    }
    /**
     * @param string       $path
     * @param string|false $module (optional).
     * @return string
     */
    public function get_static_url($path, $module = false)
    {
        return \Imagely\NGG\Display\StaticPopeAssets::get_url($path, $module);
    }
    /**
     * Renders a template and outputs the response headers
     *
     * @param string $name
     * @param array  $vars (optional).
     * @param bool   $return (optional).
     * @return string
     */
    public function render_view($name, $vars = array(), $return = false)
    {
        $this->object->render();
        return $this->object->render_partial($name, $vars, $return);
    }
    /**
     * @param string $template Path to the POPE module#filename.
     * @param array  $params Array of parameters to be extract()ed to the template file.
     * @param bool   $return When true results will be returned instead of printed.
     * @param null   $context Application context.
     * @param string $new_template_path Path to the new non-POPE file located under the plugin root's '/templates' directory.
     * @return mixed
     */
    public function render_partial($template, $params = array(), $return = false, $context = null, $new_template_path = '')
    {
        /** @var C_MVC_View $view */
        $view = $this->object->create_view($template, $params, $context, $new_template_path);
        return $view->render($return);
    }
}
/**
 * Adds methods for MVC Controller
 *
 * @property C_MVC_Controller $object
 */
class Mixin_MVC_Controller_Instance_Methods extends Mixin
{
    // Provide a default view.
    public function index_action($return = false)
    {
        return $this->render_view('photocrati-mvc#index', [], $return);
    }
    /**
     * Returns the value of a parameters
     *
     * @param string      $key
     * @param string|null $prefix (optional).
     * @return string
     */
    public function param($key, $prefix = null, $default = null)
    {
        return $this->object->get_routed_app()->get_parameter($key, $prefix, $default);
    }
    public function set_param($key, $value, $id = null, $use_prefix = false)
    {
        return $this->object->get_routed_app()->set_parameter($key, $value, $id, $use_prefix);
    }
    public function set_param_for($url, $key, $value, $id = null, $use_prefix = false)
    {
        return $this->object->get_routed_app()->set_parameter($key, $value, $id, $use_prefix, $url);
    }
    public function remove_param($key, $id = null)
    {
        return $this->object->get_routed_app()->remove_parameter($key, $id);
    }
    /**
     * Gets the routed url, generated by the Routing App
     *
     * @param bool $with_qs (optional) With QueryString.
     * @return string
     */
    public function get_routed_url($with_qs = false)
    {
        return $this->object->get_routed_app()->get_app_url(false, $with_qs);
    }
    /**
     * Outputs the response headers
     *
     * TODO: Determine if this can be moved into C_MVC_Controller
     */
    public function render()
    {
        if (!headers_sent() && !defined('DOING_AJAX') && !defined('REST_REQUEST')) {
            header('Content-Type: ' . $this->object->_content_type . '; charset=' . get_option('blog_charset'), true);
        }
    }
    /**
     * @param string $template Path to the POPE module#filename.
     * @param array  $params Array of parameters to be extract()ed to the template file.
     * @param null   $context Application context.
     * @param string $new_template_path Path to the new non-POPE file located under the plugin root's '/templates' directory.
     * @return mixed
     */
    public function create_view($template, $params = array(), $context = null, $new_template_path = '')
    {
        if (!$context) {
            $context = $this->object->context;
        }
        return C_Component_Factory::get_instance()->create('mvc_view', $template, $params, null, $context, $new_template_path);
    }
}
/**
 * Class C_MVC_View
 *
 * @mixin Mixin_Mvc_View_Instance_Methods
 * @property C_MVC_View $object
 */
class C_MVC_View extends C_Component
{
    public $_template = '';
    public $_engine = '';
    public $_params = array();
    public $_queue = array();
    public $_new_template = '';
    public function __construct($template, $params = array(), $engine = 'php', $context = false, $new_template_path = '')
    {
        $this->_template = $template;
        $this->_params = (array) $params;
        $this->_engine = $engine;
        $this->_new_template = $new_template_path;
        $context = $context ? array_unique([$context, $template], SORT_REGULAR) : $template;
        parent::__construct($context);
    }
    public function define($context = false)
    {
        parent::define($context);
        $this->implement('I_MVC_View');
        $this->add_mixin('Mixin_Mvc_View_Instance_Methods');
    }
    /**
     * Returns the variables to be used in the template
     *
     * @return array
     */
    public function get_template_vars()
    {
        $retval = [];
        foreach ($this->object->_params as $key => $value) {
            if (strpos($key, '_template') === 0) {
                $value = $this->object->get_template_abspath($value);
            }
            $retval[$key] = $value;
        }
        return $retval;
    }
    /**
     * @param string $value (optional).
     * @return string
     */
    public function get_template_abspath($value = null)
    {
        if (!$value) {
            $value = $this->object->_template;
        }
        $new_template_path = !empty($this->object->_new_template) ? $this->object->_new_template : '';
        if (strpos($value, DIRECTORY_SEPARATOR) !== false && @file_exists($value)) {
            // key is already abspath.
        } else {
            $value = $this->object->find_template_abspath($value, false, $new_template_path);
        }
        return $value;
    }
    public function rasterize_object($element)
    {
        return $element->rasterize();
    }
    public function start_element($id, $type = null, $context = null)
    {
        if ($type == null) {
            $type = 'element';
        }
        $count = count($this->object->_queue);
        $element = new C_MVC_View_Element($id, $type);
        if ($context != null) {
            if (!is_array($context)) {
                $context = ['object' => $context];
            }
            foreach ($context as $context_name => $context_value) {
                $element->set_context($context_name, $context_value);
            }
        }
        $this->object->_queue[] = $element;
        if ($count > 0) {
            $old_element = $this->object->_queue[$count - 1];
            $content = ob_get_contents();
            ob_clean();
            $old_element->append($content);
            $old_element->append($element);
        }
        ob_start();
        return $element;
    }
    public function end_element()
    {
        $content = ob_get_clean();
        $element = array_pop($this->object->_queue);
        if ($content != null) {
            $element->append($content);
        }
        return $element;
    }
    /**
     * Renders a sub-template for the view
     *
     * @param string $__template.
     * @param array  $__params.
     * @param bool   $__return Unused.
     * @return NULL
     */
    public function include_template($__template, $__params = null, $__return = false)
    {
        // We use underscores to prefix local variables to avoid conflicts wth
        // template vars.
        if ($__params == null) {
            $__params = [];
        }
        $__params['template_origin'] = $this->object->_template;
        $__target = $this->object->get_template_abspath($__template);
        $__origin_target = $this->object->get_template_abspath($this->object->_template);
        $__image_before_target = $this->object->get_template_abspath('photocrati-nextgen_gallery_display#image/before');
        $__image_after_target = $this->object->get_template_abspath('photocrati-nextgen_gallery_display#image/after');
        if ($__origin_target != $__target) {
            if ($__target == $__image_before_target) {
                $__image = isset($__params['image']) ? $__params['image'] : null;
                $this->start_element('nextgen_gallery.image_panel', 'item', $__image);
            }
            if ($__target == $__image_after_target) {
                $this->end_element();
            }
            extract($__params);
            include $__target;
            if ($__target == $__image_before_target) {
                $__image = isset($__params['image']) ? $__params['image'] : null;
                $this->start_element('nextgen_gallery.image', 'item', $__image);
            }
            if ($__target == $__image_after_target) {
                $this->end_element();
            }
        }
        return null;
    }
    /**
     * Gets the absolute path of an MVC template file
     *
     * @param string       $path
     * @param string|false $module (optional).
     * @param string       $new_template_path Non-POPE path coming from 'templates' in the plugin root.
     * @return string
     */
    public function find_template_abspath($path, $module = false, $new_template_path = '')
    {
        $fs = \Imagely\NGG\Util\Filesystem::get_instance();
        $settings = \Imagely\NGG\Settings\Settings::get_instance();
        // We also accept module_name#path, which needs parsing.
        if (!$module) {
            list($path, $module) = $fs->parse_formatted_path($path);
        }
        // Append the suffix.
        $path = $path . '.php';
        // First check if the template is in the override dir.
        $retval = $this->object->get_template_override_abspath($module, $path);
        if (!$retval && $new_template_path) {
            $retval = path_join(NGG_PLUGIN_DIR, 'templates' . DIRECTORY_SEPARATOR . $new_template_path . '.php');
        } else {
            $retval = $fs->join_paths($this->object->get_registry()->get_module_dir($module), $settings->mvc_template_dirname, $path);
        }
        if (!@file_exists($retval)) {
            throw new RuntimeException("{$retval} is not a valid MVC template");
        }
        return $retval;
    }
    /**
     * @param null|string $module_id
     * @return string
     */
    public function get_template_override_dir($module_id = null)
    {
        $root = trailingslashit(path_join(WP_CONTENT_DIR, 'ngg'));
        if (!@file_exists($root) && is_writable(trailingslashit(WP_CONTENT_DIR))) {
            wp_mkdir_p($root);
        }
        $modules = trailingslashit(path_join($root, 'modules'));
        if (!@file_exists($modules) && is_writable($root)) {
            wp_mkdir_p($modules);
        }
        if ($module_id) {
            $module_dir = trailingslashit(path_join($modules, $module_id));
            if (!@file_exists($module_dir) && is_writable($modules)) {
                wp_mkdir_p($module_dir);
            }
            $template_dir = trailingslashit(path_join($module_dir, 'templates'));
            if (!@file_exists($template_dir) && is_writable($module_dir)) {
                wp_mkdir_p($template_dir);
            }
            return $template_dir;
        }
        return $modules;
    }
    /**
     * @param $module
     * @param $filename
     * @return string|null
     */
    public function get_template_override_abspath($module, $filename)
    {
        $fs = \Imagely\NGG\Util\Filesystem::get_instance();
        $retval = null;
        $abspath = $fs->join_paths($this->object->get_template_override_dir($module), $filename);
        if (@file_exists($abspath)) {
            $retval = $abspath;
        }
        return $retval;
    }
}
class Mixin_Mvc_View_Instance_Methods extends Mixin
{
    /**
     * Renders the view (template)
     *
     * @param bool $return (optional).
     * @return string|NULL
     */
    public function render($return = false)
    {
        $element = $this->object->render_object();
        $content = $this->object->rasterize_object($element);
        if (!$return) {
            echo $content;
        }
        return $content;
    }
    public function render_object()
    {
        // We use underscores to prefix local variables to avoid conflicts wth
        // template vars.
        $__element = $this->start_element($this->object->_template, 'template', $this->object);
        $template_vars = $this->object->get_template_vars();
        extract($template_vars);
        include $this->object->get_template_abspath();
        $this->end_element();
        if (($displayed_gallery = $this->object->get_param('displayed_gallery')) && $this->object->get_param('display_type_rendering')) {
            $triggers = \Imagely\NGG\DisplayedGallery\TriggerManager::get_instance();
            $triggers->render($__element, $displayed_gallery);
        }
        return $__element;
    }
    /**
     * Adds a template parameter
     *
     * @param $key
     * @param $value
     */
    public function set_param($key, $value)
    {
        $this->object->_params[$key] = $value;
    }
    /**
     * Removes a template parameter
     *
     * @param $key
     */
    public function remove_param($key)
    {
        unset($this->object->_params[$key]);
    }
    /**
     * Gets the value of a template parameter
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get_param($key, $default = null)
    {
        if (isset($this->object->_params[$key])) {
            return $this->object->_params[$key];
        } else {
            return $default;
        }
    }
}
class C_MVC_View_Element
{
    var $_id;
    var $_type;
    var $_list;
    var $_context;
    public function __construct($id, $type = null)
    {
        $this->_id = $id;
        $this->_type = $type;
        $this->_list = [];
        $this->_context = [];
    }
    public function get_id()
    {
        return $this->_id;
    }
    public function append($child)
    {
        $this->_list[] = $child;
    }
    public function insert($child, $position = 0)
    {
        array_splice($this->_list, $position, 0, $child);
    }
    public function delete($child)
    {
        $index = array_search($child, $this->_list);
        if ($index !== false) {
            array_splice($this->_list, $index, 1);
        }
    }
    public function find($id, $recurse = false)
    {
        $list = [];
        $this->_find($list, $id, $recurse);
        return $list;
    }
    public function _find(array &$list, $id, $recurse = false)
    {
        foreach ($this->_list as $index => $element) {
            if ($element instanceof C_MVC_View_Element) {
                if ($element->get_id() == $id) {
                    $list[] = $element;
                }
                if ($recurse) {
                    $element->_find($list, $id, $recurse);
                }
            }
        }
    }
    public function get_context($name)
    {
        if (isset($this->_context[$name])) {
            return $this->_context[$name];
        }
        return null;
    }
    public function set_context($name, $value)
    {
        $this->_context[$name] = $value;
    }
    public function get_object()
    {
        return $this->get_context('object');
    }
    // XXX not implemented.
    public function parse()
    {
    }
    public function rasterize()
    {
        $ret = null;
        foreach ($this->_list as $index => $element) {
            if ($element instanceof C_MVC_View_Element) {
                $ret .= $element->rasterize();
            } else {
                $ret .= (string) $element;
            }
        }
        return $ret;
    }
}