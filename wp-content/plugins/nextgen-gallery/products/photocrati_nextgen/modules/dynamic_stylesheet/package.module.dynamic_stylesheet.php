<?php
/**
 * Class C_Dynamic_Stylesheet_Controller
 * @mixin Mixin_Dynamic_Stylesheet_Instance_Methods
 * @mixin Mixin_Dynamic_Stylesheet_Actions
 * @implements I_Dynamic_Stylesheet
 */
class C_Dynamic_Stylesheet_Controller extends C_MVC_Controller
{
    static $_instances = array();
    var $_known_templates = array();
    var $_app = NULL;
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Dynamic_Stylesheet_Instance_Methods');
        $this->add_mixin('Mixin_Dynamic_Stylesheet_Actions');
        $this->implement('I_Dynamic_Stylesheet');
    }
    function initialize()
    {
        parent::initialize();
        $this->_app = C_NextGen_Settings::get_instance()->dynamic_stylesheet_slug;
    }
    static function &get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
}
/**
 * Provides instance methods for the dynamic stylesheet utility
 */
class Mixin_Dynamic_Stylesheet_Instance_Methods extends Mixin
{
    /**
     * Registers a template with the dynamic stylesheet utility. A template
     * must be registered before it can be loaded
     * @param string $name
     * @param string $template
     */
    function register($name, $template)
    {
        $this->object->_known_templates[$name] = $template;
    }
    /**
     * Finds a registered template by name
     * @param string $name
     * @return int
     */
    function get_css_template_index($name)
    {
        return array_search($name, array_keys($this->object->_known_templates));
    }
    function get_css_template($index)
    {
        $keys = array_keys($this->object->_known_templates);
        return $this->object->_known_templates[$keys[$index]];
    }
    /**
     * Loads a template, along with the dynamic variables to be interpolated
     * @param string $name
     * @param array $data (optional)
     */
    function enqueue($name, $data = array())
    {
        if (($index = $this->object->get_css_template_index($name)) !== FALSE) {
            if (is_subclass_of($data, 'C_DataMapper_Model')) {
                $data = $data->get_entity();
            }
            if (defined('NGG_INLINE_DYNAMIC_CSS') && NGG_INLINE_DYNAMIC_CSS) {
                $css = $this->render_view($this->object->get_css_template($index), $data, TRUE);
                wp_enqueue_style('ngg_dyncss', $this->get_static_url('photocrati-dynamic_stylesheet#blank.css'), array(), NGG_SCRIPT_VERSION);
                wp_add_inline_style('ngg_dyncss', $css);
            } else {
                $data = $this->object->encode($data);
                wp_enqueue_style('dyncss-' . $index . $data . '@dynamic', $this->object->get_router()->get_url("/{$this->object->_app}", array()) . "?index={$index}&data={$data}", FALSE, NGG_SCRIPT_VERSION);
            }
        }
    }
    /**
     * Encodes $data
     *
     * base64 encoding uses '==' to denote the end of the sequence, but keep it out of the url
     * @param $data
     * @return string
     */
    function encode($data)
    {
        $data = json_encode($data);
        $data = base64_encode($data);
        $data = str_replace('/', '\\', $data);
        $data = rtrim($data, '=');
        return $data;
    }
    /**
     * Decodes $data
     *
     * @param $data
     * @return array|mixed
     */
    function decode($data)
    {
        $data = str_replace('\\', '/', $data);
        $data = base64_decode($data . '==');
        $data = json_decode($data);
        return $data;
    }
}
/**
 * Provides controller actions for the dynamic stylesheet
 */
class Mixin_Dynamic_Stylesheet_Actions extends Mixin
{
    function index_action()
    {
        $this->set_content_type('css');
        if (($data = $this->param('data')) !== FALSE && ($index = $this->param('index')) !== FALSE) {
            $data = $this->object->decode($data);
            $this->render_view($this->object->get_css_template($index), $data);
        }
    }
}