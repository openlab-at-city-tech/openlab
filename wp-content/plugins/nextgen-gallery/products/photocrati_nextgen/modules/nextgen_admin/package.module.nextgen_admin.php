<?php
/**
 * Class A_Fs_Access_Page
 * @todo Finish the implementation
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page for the "ngg_fs_access" context
 */
class A_Fs_Access_Page extends Mixin
{
    function index_action()
    {
        $router = C_Router::get_instance();
        $url = $this->param('uri') ? $router->get_url($this->param('uri')) : admin_url('/admin.php?' . $router->get_querystring());
        // Request filesystem credentials from user
        $creds = request_filesystem_credentials($url, '', FALSE, ABSPATH, array());
        if (WP_Filesystem($creds)) {
            global $wp_filesystem;
        }
    }
    /**
     * Determines whether the given paths are writable
     * @return boolean
     */
    function are_paths_writable()
    {
        $retval = TRUE;
        $path = $this->object->param('path');
        if (!is_array($path)) {
            $path = array($path);
        }
        foreach ($path as $p) {
            if (!is_writable($p)) {
                $retval = FALSE;
                break;
            }
        }
        return $retval;
    }
}
/**
 * Provides validation for datamapper entities within an MVC controller
 * @mixin C_MVC_Controller
 * @adapts I_MVC_Controller
 */
class A_MVC_Validation extends Mixin
{
    function show_errors_for($entity, $return = FALSE)
    {
        $retval = '';
        if ($entity->is_invalid()) {
            $retval = $this->object->render_partial('photocrati-nextgen_admin#entity_errors', array('entity' => $entity), $return);
        }
        return $retval;
    }
    function show_success_for($entity, $message, $return = FALSE)
    {
        $retval = '';
        if ($entity->is_valid()) {
            $retval = $this->object->render_partial('photocrati-nextgen_admin#entity_saved', array('entity' => $entity, 'message' => $message));
        }
        return $retval;
    }
}
/**
 * Class A_NextGen_Admin_Default_Pages
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 */
class A_NextGen_Admin_Default_Pages extends Mixin
{
    function setup()
    {
        $this->object->add(NGG_FS_ACCESS_SLUG, array('adapter' => 'A_Fs_Access_Page', 'parent' => NGGFOLDER, 'add_menu' => FALSE));
        return $this->call_parent('setup');
    }
}
class C_Review_Notice
{
    function __construct($params = array())
    {
        $this->_data['name'] = $params['name'];
        $this->_data['range'] = $params['range'];
        $this->_data['follows'] = $params['follows'];
    }
    function get_name()
    {
        return $this->_data['name'];
    }
    function get_gallery_count()
    {
        // Get the total # of galleries if we don't have them
        $settings = C_NextGen_Settings::get_instance();
        $count = $settings->get('gallery_count', FALSE);
        if (!$count) {
            $count = M_NextGen_Admin::update_gallery_count_setting();
        }
        return $count;
    }
    function get_range()
    {
        return $this->_data['range'];
    }
    function is_renderable()
    {
        return ($this->is_on_dashboard() || $this->is_on_ngg_admin_page()) && $this->is_at_gallery_count() && $this->is_previous_notice_dismissed() && $this->gallery_created_flag_check();
    }
    function gallery_created_flag_check()
    {
        $settings = C_NextGen_Settings::get_instance();
        return $settings->get('gallery_created_after_reviews_introduced');
    }
    function is_at_gallery_count()
    {
        $retval = FALSE;
        $range = $this->_data['range'];
        $count = $this->get_gallery_count();
        $manager = C_Admin_Notification_Manager::get_instance();
        // Determine if we match the current range
        if ($count >= $range['min'] && $count <= $range['max']) {
            $retval = TRUE;
        }
        // If the current number of galleries exceeds the parent notice's maximum we should dismiss the parent
        if (!empty($this->_data['follows'])) {
            $follows = $this->_data['follows'];
            $parent_range = $follows->get_range();
            if ($count > $parent_range['max'] && !$manager->is_dismissed($follows->get_name())) {
                $manager->dismiss($follows->get_name(), 2);
            }
        }
        return $retval;
    }
    function is_previous_notice_dismissed($level = FALSE)
    {
        $retval = FALSE;
        $manager = C_Admin_Notification_Manager::get_instance();
        if (empty($level)) {
            $level = $this;
        }
        if (!empty($level->_data['follows'])) {
            $parent = $level->_data['follows'];
            $retval = $manager->is_dismissed($parent->get_name());
            if (!$retval && !empty($parent->_data['follows'])) {
                $retval = $this->is_previous_notice_dismissed($parent);
            }
        } else {
            $retval = TRUE;
        }
        return $retval;
    }
    function is_on_dashboard()
    {
        return preg_match('#/wp-admin/?(index\\.php)?$#', $_SERVER['REQUEST_URI']) == TRUE;
    }
    function is_on_ngg_admin_page()
    {
        // Do not show this notification inside of the ATP popup
        return (preg_match("/wp-admin.*(ngg|nextgen).*/", $_SERVER['REQUEST_URI']) || isset($_REQUEST['page']) && preg_match("/ngg|nextgen/", $_REQUEST['page'])) && strpos(strtolower($_SERVER['REQUEST_URI']), '&attach_to_post') == false;
    }
    function render()
    {
        $view = new C_MVC_View('photocrati-nextgen_admin#review_notice', array('number' => $this->get_gallery_count()));
        return $view->render(TRUE);
    }
    function dismiss($code)
    {
        $retval = array('dismiss' => TRUE, 'persist' => TRUE, 'success' => TRUE, 'code' => $code, 'dismiss_code' => $code);
        $manager = C_Admin_Notification_Manager::get_instance();
        if ($code == 1 || $code == 3) {
            $retval['review_level_1'] = $manager->dismiss('review_level_1', 2);
            $retval['review_level_2'] = $manager->dismiss('review_level_2', 2);
            $retval['review_level_3'] = $manager->dismiss('review_level_3', 2);
        }
        return $retval;
    }
}
class C_Admin_Notification_Wrapper
{
    public $_name;
    public $_data;
    function __construct($name, $data)
    {
        $this->_name = $name;
        $this->_data = $data;
    }
    function is_renderable()
    {
        return true;
    }
    function is_dismissable()
    {
        return true;
    }
    function render()
    {
        return $this->_data["message"];
    }
}
class C_Admin_Notification_Manager
{
    public $_notifications = array();
    public $_displayed_notice = FALSE;
    public $_dismiss_url = NULL;
    /**
     * @var C_Admin_Notification_Manager
     */
    static $_instance = NULL;
    /**
     * @return C_Admin_Notification_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function __construct()
    {
        $this->_dismiss_url = site_url('/?ngg_dismiss_notice=1');
    }
    function has_displayed_notice()
    {
        return $this->_displayed_notice;
    }
    function add($name, $handler)
    {
        $this->_notifications[$name] = $handler;
    }
    function remove($name)
    {
        unset($this->_notifications[$name]);
    }
    function render()
    {
        $output = array();
        foreach (array_keys($this->_notifications) as $notice) {
            if ($html = $this->render_notice($notice)) {
                $output[] = $html;
            }
        }
        echo implode("\n", $output);
    }
    function is_dismissed($name)
    {
        $retval = FALSE;
        $settings = C_NextGen_Settings::get_instance();
        $dismissed = $settings->get('dismissed_notifications', array());
        if (isset($dismissed[$name])) {
            if ($id = get_current_user_id()) {
                if (in_array($id, $dismissed[$name])) {
                    $retval = TRUE;
                } else {
                    if (in_array('unknown', $dismissed[$name])) {
                        $retval = TRUE;
                    }
                }
            }
        }
        return $retval;
    }
    function dismiss($name, $dismiss_code = 1)
    {
        $response = array();
        if ($handler = $this->get_handler_instance($name)) {
            $has_method = method_exists($handler, 'is_dismissable');
            if ($has_method && $handler->is_dismissable() || !$has_method) {
                if (method_exists($handler, 'dismiss')) {
                    $response = $handler->dismiss($dismiss_code);
                    $response['handled'] = TRUE;
                }
                if (is_bool($response)) {
                    $response = array('dismiss' => $response);
                }
                // Set default key/values
                if (!isset($response['handled'])) {
                    $response['handled'] = FALSE;
                }
                if (!isset($response['dismiss'])) {
                    $response['dismiss'] = TRUE;
                }
                if (!isset($response['persist'])) {
                    $response['persist'] = $response['dismiss'];
                }
                if (!isset($response['success'])) {
                    $response['success'] = $response['dismiss'];
                }
                if (!isset($response['code'])) {
                    $response['code'] = $dismiss_code;
                }
                if ($response['dismiss']) {
                    $settings = C_NextGen_Settings::get_instance();
                    $dismissed = $settings->get('dismissed_notifications', array());
                    if (!isset($dismissed[$name])) {
                        $dismissed[$name] = array();
                    }
                    $user_id = get_current_user_id();
                    $dismissed[$name][] = $user_id ? $user_id : 'unknown';
                    $settings->set('dismissed_notifications', $dismissed);
                    if ($response['persist']) {
                        $settings->save();
                    }
                }
            } else {
                $response['error'] = __("Notice is not dismissible", 'nggallery');
            }
        } else {
            $response['error'] = __("No handler defined for this notice", 'nggallery');
        }
        return $response;
    }
    function get_handler_instance($name)
    {
        $retval = NULL;
        if (isset($this->_notifications[$name])) {
            $handler = $this->_notifications[$name];
            if (is_object($handler)) {
                $retval = $handler;
            } elseif (is_array($handler)) {
                $retval = new C_Admin_Notification_Wrapper($name, $handler);
            } elseif (class_exists($handler)) {
                $retval = call_user_func(array($handler, 'get_instance'), $name);
            }
        }
        return $retval;
    }
    function enqueue_scripts()
    {
        if ($this->has_displayed_notice()) {
            $router = C_Router::get_instance();
            wp_enqueue_script('ngg_admin_notices', $router->get_static_url('photocrati-nextgen_admin#admin_notices.js'), array(), NGG_SCRIPT_VERSION, TRUE);
            wp_localize_script('ngg_admin_notices', 'ngg_dismiss_url', [$this->_dismiss_url]);
        }
    }
    function serve_ajax_request()
    {
        $retval = array('failure' => TRUE);
        if (isset($_REQUEST['ngg_dismiss_notice'])) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            ob_start();
            if (!isset($_REQUEST['code'])) {
                $_REQUEST['code'] = 1;
            }
            if (isset($_REQUEST['name'])) {
                $retval = $this->dismiss($_REQUEST['name'], intval($_REQUEST['code']));
            } else {
                $retval['msg'] = __('Not a valid notice name', 'nggallery');
            }
            ob_end_clean();
            echo json_encode($retval);
            exit;
        }
    }
    function render_notice($name)
    {
        $retval = '';
        if (($handler = $this->get_handler_instance($name)) && !$this->is_dismissed($name)) {
            // Does the handler want to render?
            $has_method = method_exists($handler, 'is_renderable');
            if ($has_method && $handler->is_renderable() || !$has_method) {
                $show_dismiss_button = false;
                if (method_exists($handler, 'show_dismiss_button')) {
                    $show_dismiss_button = $handler->show_dismiss_button();
                } else {
                    if (method_exists($handler, 'is_dismissable')) {
                        $show_dismiss_button = $handler->is_dismissable();
                    }
                }
                $template = method_exists($handler, 'get_mvc_template') ? $handler->get_mvc_template() : 'photocrati-nextgen_admin#admin_notice';
                // The 'inline' class is necessary to prevent our notices from being moved in the DOM
                // see https://core.trac.wordpress.org/ticket/34570 for reference
                $css_class = 'inline ';
                $css_class .= method_exists($handler, 'get_css_class') ? $handler->get_css_class() : 'updated';
                $view = new C_MVC_View($template, array('css_class' => $css_class, 'is_dismissable' => method_exists($handler, 'is_dismissable') ? $handler->is_dismissable() : FALSE, 'html' => method_exists($handler, 'render') ? $handler->render() : '', 'show_dismiss_button' => $show_dismiss_button, 'notice_name' => $name));
                $retval = $view->render(TRUE);
                if (method_exists($handler, 'enqueue_backend_resources')) {
                    $handler->enqueue_backend_resources();
                }
                $this->_displayed_notice = TRUE;
            }
        }
        return $retval;
    }
}
class C_Admin_Requirements_Manager
{
    protected $_requirements = array();
    protected $_groups = array();
    public function __construct()
    {
        $this->set_initial_groups();
    }
    protected function set_initial_groups()
    {
        // Requirements can be added with any group key desired but only registered groups will be displayed
        $this->_groups = apply_filters('ngg_admin_requirements_manager_groups', array('phpext' => __('NextGen Gallery requires the following PHP extensions to function correctly. Please contact your hosting provider or systems admin and ask them for assistance:', 'nggallery'), 'phpver' => __('NextGen Gallery has degraded functionality because of your PHP version. Please contact your hosting provider or systems admin and ask them for assistance:', 'nggallery'), 'dirperms' => __('NextGen Gallery has found an issue trying to access the following files or directories. Please ensure the following locations have the correct permissions:', 'nggallery')));
    }
    /**
     * @return C_Admin_Requirements_Manager
     */
    private static $_instance = NULL;
    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_Admin_Requirements_Manager();
        }
        return self::$_instance;
    }
    /**
     * @param string $name Unique notification ID
     * @param string $group Choose one of phpext | phpver | dirperms
     * @param callable $callback Method that determines whether the notification should display
     * @param array $data Possible keys: className, message, dismissable
     */
    public function add($name, $group, $callback, $data)
    {
        $this->_requirements[$group][$name] = new C_Admin_Requirements_Notice($name, $callback, $data);
    }
    /**
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->_notifications[$name]);
    }
    public function create_notification()
    {
        foreach ($this->_groups as $groupID => $groupLabel) {
            if (empty($this->_requirements[$groupID])) {
                continue;
            }
            $dismissable = TRUE;
            $notices = array();
            foreach ($this->_requirements[$groupID] as $key => $requirement) {
                $passOrFail = $requirement->run_callback();
                if (!$passOrFail) {
                    // If any of the notices can't be dismissed then all notices in that group can't be dismissed
                    if (!$requirement->is_dismissable()) {
                        // Add important notices to the beginning of the list
                        $dismissable = FALSE;
                        array_unshift($notices, $requirement);
                    } else {
                        // Less important notices go to the end of the list
                        $notices[] = $requirement;
                    }
                }
            }
            // Don't display empty group notices
            if (empty($notices)) {
                continue;
            }
            // Generate the combined message for this group
            $message = '<p>' . $this->_groups[$groupID] . '</p><ul>';
            foreach ($notices as $requirement) {
                // Make non-dismissable notifications bold
                $string = $requirement->is_dismissable() ? $requirement->get_message() : '<strong>' . $requirement->get_message() . '</strong>';
                $message .= '<li>' . $string . '</li>';
            }
            $message .= '</ul>';
            // Generate the notice object
            $name = 'ngg_requirement_notice_' . $groupID . '_' . md5($message);
            $notice = new C_Admin_Requirements_Notice($name, '__return_true', array('dismissable' => $dismissable, 'message' => $message));
            C_Admin_Notification_Manager::get_instance()->add($name, $notice);
        }
    }
}
class C_Admin_Requirements_Notice
{
    protected $_name;
    protected $_data;
    protected $_callback;
    /**
     * C_Admin_Requirements_Notice constructor
     * @param string $name
     * @param callable $callback
     * @param array $data
     */
    public function __construct($name, $callback, $data)
    {
        $this->_name = $name;
        $this->_data = $data;
        $this->_callback = $callback;
    }
    /**
     * @return bool
     */
    public function is_renderable()
    {
        return true;
    }
    /**
     * @return bool
     */
    public function is_dismissable()
    {
        return isset($this->_data['dismissable']) ? $this->_data['dismissable'] : TRUE;
    }
    /**
     * @return string
     */
    public function render()
    {
        return $this->_data["message"];
    }
    /**
     * @return string
     */
    public function get_mvc_template()
    {
        return 'photocrati-nextgen_admin#requirement_notice';
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->_name;
    }
    /**
     * @return bool
     */
    public function run_callback()
    {
        if (is_callable($this->_callback)) {
            return call_user_func($this->_callback);
        } else {
            return false;
        }
    }
    /**
     * @return string
     */
    public function get_css_class()
    {
        $prefix = 'notice notice-';
        if ($this->is_dismissable()) {
            return $prefix . 'warning';
        } else {
            return $prefix . 'error';
        }
    }
    public function get_message()
    {
        return empty($this->_data['message']) ? "" : $this->_data['message'];
    }
}
/**
 * Class C_Form
 * @mixin Mixin_Form_Instance_Methods
 * @mixin Mixin_Form_Field_Generators
 * @implements I_Form
 */
class C_Form extends C_MVC_Controller
{
    static $_instances = array();
    var $page = NULL;
    /**
     * Gets an instance of a form
     * @param string $context
     * @return C_Form
     */
    static function &get_instance($context)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the form
     * @param string|bool $context (optional)
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Form_Instance_Methods');
        $this->add_mixin('Mixin_Form_Field_Generators');
        $this->implement('I_Form');
    }
}
class Mixin_Form_Instance_Methods extends Mixin
{
    /**
     * Enqueues any static resources required by the form
     */
    function enqueue_static_resources()
    {
    }
    /**
     * Gets a list of fields to render
     * @return array
     */
    function _get_field_names()
    {
        return array();
    }
    function get_id()
    {
        return $this->object->context;
    }
    function get_title()
    {
        return $this->object->context;
    }
    /**
     * Saves the form/model
     * @param array $attributes
     * @return bool
     */
    function save_action($attributes = array())
    {
        if (!$attributes) {
            $attributes = array();
        }
        if ($this->object->has_method('get_model') && $this->object->get_model()) {
            return $this->object->get_model()->save($attributes);
        } else {
            return TRUE;
        }
    }
    /**
     * Returns the rendered form
     * @param bool $wrap (optional) Default = true
     * @return string
     */
    function render($wrap = TRUE)
    {
        $fields = array();
        foreach ($this->object->_get_field_names() as $field) {
            $method = "_render_{$field}_field";
            if ($this->object->has_method($method)) {
                $fields[] = $this->object->{$method}($this->object->get_model());
            }
        }
        return $this->object->render_partial('photocrati-nextgen_admin#form', array('fields' => $fields, 'wrap' => $wrap), TRUE);
    }
    function get_model()
    {
        return $this->object->page->has_method('get_model') ? $this->object->page->get_model() : NULL;
    }
}
/**
 * Provides some default field generators for forms to use
 */
class Mixin_Form_Field_Generators extends Mixin
{
    /**
     * @param stdClass|C_Display_Type $display_type
     * @param string $name
     * @param string $label
     * @param array $options
     * @param int|string $value
     * @param string $text
     * @param bool $hidden
     * @return string
     */
    function _render_select_field($display_type, $name, $label, $options, $value, $text = '', $hidden = FALSE)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_select', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'options' => $options, 'value' => $value, 'text' => $text, 'hidden' => $hidden], TRUE);
    }
    function _render_radio_field($display_type, $name, $label, $value, $text = '', $hidden = FALSE)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_radio', array('display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden), True);
    }
    function _render_number_field($display_type, $name, $label, $value, $text = '', $hidden = FALSE, $placeholder = '', $min = NULL, $max = NULL)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_number', array('display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder, 'min' => $min, 'max' => $max), True);
    }
    function _render_text_field($display_type, $name, $label, $value, $text = '', $hidden = FALSE, $placeholder = '')
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_text', array('display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder), True);
    }
    function _render_textarea_field($display_type, $name, $label, $value, $text = '', $hidden = FALSE, $placeholder = '')
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_textarea', array('display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder), True);
    }
    function _render_color_field($display_type, $name, $label, $value, $text = '', $hidden = FALSE)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_color', array('display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden), True);
    }
    /**
     * Renders a pair of fields for width and width-units (px, em, etc)
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    function _render_width_and_unit_field($display_type)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_width_and_unit', array('display_type_name' => $display_type->name, 'name' => 'width', 'label' => __('Gallery width', 'nggallery'), 'value' => $display_type->settings['width'], 'text' => __('An empty or 0 setting will make the gallery full width', 'nggallery'), 'placeholder' => __('(optional)', 'nggallery'), 'unit_name' => 'width_unit', 'unit_value' => $display_type->settings['width_unit'], 'options' => array('px' => __('Pixels', 'nggallery'), '%' => __('Percent', 'nggallery'))), TRUE);
    }
    function _get_aspect_ratio_options()
    {
        return array('first_image' => __('First Image', 'nggallery'), 'image_average' => __('Average', 'nggallery'), '1.5' => '3:2 [1.5]', '1.333' => '4:3 [1.333]', '1.777' => '16:9 [1.777]', '1.6' => '16:10 [1.6]', '1.85' => '1.85:1 [1.85]', '2.39' => '2.39:1 [2.39]', '1.81' => '1.81:1 [1.81]', '1' => '1:1 (Square) [1]');
    }
}
/**
 * Class C_Form_Manager
 * @mixin Mixin_Form_Manager
 * @implements I_Form_Manager
 */
class C_Form_Manager extends C_Component
{
    static $_instances = array();
    var $_forms = array();
    /**
     * Returns an instance of the form manager
     * @param string|bool $context (optional)
     * @return C_Form_Manager
     */
    static function &get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the instance
     * @param mixed $context
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Form_Manager');
        $this->implement('I_Form_Manager');
    }
}
class Mixin_Form_Manager extends Mixin
{
    /**
     * Adds one or more
     * @param string $type
     * @param array|string $form_names
     * @return int Results of get_form_count($type)
     */
    function add_form($type, $form_names)
    {
        if (!isset($this->object->_forms[$type])) {
            $this->object->_forms[$type] = array();
        }
        if (!is_array($form_names)) {
            $form_names = array($form_names);
        }
        foreach ($form_names as $form) {
            $this->object->_forms[$type][] = $form;
        }
        return $this->object->get_form_count($type);
    }
    /**
     * Alias for add_form() method
     * @param string $type
     * @param string|array $form_names
     * @return int
     */
    function add_forms($type, $form_names)
    {
        return $this->object->add_form($type, $form_names);
    }
    /**
     * Removes one or more forms of a particular type
     * @param string $type
     * @param string|array $form_names
     * @return int	number of forms remaining for the type
     */
    function remove_form($type, $form_names)
    {
        $retval = 0;
        if (isset($this->object->_forms[$type])) {
            foreach ($form_names as $form) {
                if ($index = array_search($form, $this->object->_forms[$type])) {
                    unset($this->object->_forms[$type][$index]);
                }
            }
            $retval = $this->object->get_form_count($type);
        }
        return $retval;
    }
    /**
     * Alias for remove_form() method
     * @param string $type
     * @param string|array $form_names
     * @return int
     */
    function remove_forms($type, $form_names)
    {
        return $this->object->remove_form($type, $form_names);
    }
    /**
     * Gets known form types
     * @return array
     */
    function get_known_types()
    {
        return array_keys($this->object->_forms);
    }
    /**
     * Gets forms of a particular type
     * @param string $type
     * @param string|bool $instantiate (optional)
     * @return array
     */
    function get_forms($type, $instantiate = FALSE)
    {
        $retval = array();
        if (isset($this->object->_forms[$type])) {
            if (!$instantiate) {
                $retval = $this->object->_forms[$type];
            } else {
                foreach ($this->object->_forms[$type] as $context) {
                    $retval[] = $this->get_registry()->get_utility('I_Form', $context);
                }
            }
        }
        return $retval;
    }
    /**
     * Gets the number of forms registered for a particular type
     * @param string $type
     * @return int
     */
    function get_form_count($type)
    {
        $retval = 0;
        if (isset($this->object->_forms[$type])) {
            $retval = count($this->object->_forms[$type]);
        }
        return $retval;
    }
    /**
     * Gets the index of a particular form
     * @param string $type
     * @param string $name
     * @return FALSE|int
     */
    function get_form_index($type, $name)
    {
        $retval = FALSE;
        if ($this->object->get_form_count($type) > 0) {
            $retval = array_search($name, $this->object->_forms[$type]);
        }
        return $retval;
    }
    /**
     * Adds one or more forms before a form already registered
     * @param string $type
     * @param string $before
     * @param string|array $form_names
     * @param int $offset
     * @return int
     */
    function add_form_before($type, $before, $form_names, $offset = 0)
    {
        $retval = 0;
        $index = FALSE;
        $use_add = FALSE;
        // Append the forms
        if ($this->object->get_form_count($type) == 0) {
            $use_add = TRUE;
        } else {
            if (($index = $this->object->get_form_index($type, $name)) == FALSE) {
                $use_add = FALSE;
            }
        }
        if ($use_add) {
            $this->object->add_forms($type, $form_names);
        } else {
            $before = array_slice($this->object->get_forms($type), 0, $offset);
            $after = array_slice($this->object->get_forms($type), $offset);
            $this->object->_forms[$type] = array_merge($before, $form_names, $after);
            $retval = $this->object->get_form_count($type);
        }
        return $retval;
    }
    /**
     * Adds one or more forms after an existing form
     * @param string $type
     * @param string $after
     * @param string|array $form_names
     * @return int
     */
    function add_form_after($type, $after, $form_names)
    {
        return $this->object->add_form_before($type, $after, $form_names, 1);
    }
}
class C_Mailchimp_OptIn_Notice
{
    /** @var C_Mailchimp_OptIn_Notice $_instance */
    static $_instance = NULL;
    /**
     * @return C_Mailchimp_OptIn_Notice
     */
    static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    /**
     * @return string
     */
    function get_css_class()
    {
        return 'notice notice-success';
    }
    /**
     * @return bool
     */
    public function is_dismissable()
    {
        return TRUE;
    }
    /**
     * @param $code
     * @return array
     */
    public function dismiss($code)
    {
        return array('handled' => TRUE);
    }
    /**
     * @return bool
     */
    function is_renderable()
    {
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'attach_to_post') !== FALSE) {
            return FALSE;
        }
        if (!C_NextGen_Admin_Page_Manager::is_requested()) {
            return FALSE;
        }
        if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME') || defined('NGG_PRO_PLUGIN_BASENAME')) {
            return FALSE;
        }
        $settings = C_NextGen_Settings::get_instance();
        try {
            $time = time();
            $install = new DateTime("@" . $settings->get('installDate'));
            $now = new DateTime("@" . $time);
            $diff = (int) $install->diff($now)->format('%a days');
            if ($diff >= 14) {
                return TRUE;
            }
        } catch (Exception $exception) {
        }
        return FALSE;
    }
    /**
     * @return string
     */
    function render()
    {
        $manager = C_Admin_Notification_Manager::get_instance();
        $view = new C_MVC_View('photocrati-nextgen_admin#mailchimp_optin', ['dismiss_url' => $manager->_dismiss_url . '&name=mailchimp_opt_in&code=1', 'i18n' => ['headline' => __('Thank you for using NextGEN Gallery!', 'nggallery'), 'message' => __('Get NextGEN Gallery updates, photography tips, business tips, tutorials, and resources straight to your mailbox.', 'nggallery'), 'submit' => __('Yes, Please!', 'nggallery'), 'confirmation' => __('Thank you for subscribing!', 'nggallery'), 'email_placeholder' => __('Email Address', 'nggallery'), 'name_placeholder' => __('First Name', 'nggallery'), 'connect_error' => __('Cannot connect to the registration server right now. Please try again later.', 'nggallery')]]);
        return $view->render(TRUE);
    }
}
if (!class_exists('C_NextGen_Admin_Installer')) {
}
/**
 * @mixin Mixin_NextGen_Admin_Page_Instance_Methods
 * @implements I_NextGen_Admin_Page
 */
class C_NextGen_Admin_Page_Controller extends C_MVC_Controller
{
    static $_instances = array();
    /**
     * @param bool|string $context
     * @return C_NextGen_Admin_Page_Controller
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
        if (is_array($context)) {
            $this->name = $context[0];
        } else {
            $this->name = $context;
        }
        parent::define($context);
        $this->add_mixin('Mixin_NextGen_Admin_Page_Instance_Methods');
        $this->implement('I_NextGen_Admin_Page');
    }
}
/**
 * @property Mixin_NextGen_Admin_Page_Instance_Methods|C_MVC_Controller|A_MVC_Validation $object
 */
class Mixin_NextGen_Admin_Page_Instance_Methods extends Mixin
{
    /**
     * @param string $privilege
     * @return bool
     *
     * Authorizes the request
     */
    function is_authorized_request($privilege = NULL)
    {
        if (!$privilege) {
            $privilege = $this->object->get_required_permission();
        }
        if ($this->object->is_post_request() && (!isset($_REQUEST['nonce']) || !M_Security::verify_nonce($_REQUEST['nonce'], $privilege))) {
            return FALSE;
        }
        // Ensure that the user has permission to access this page
        return M_Security::is_allowed($privilege);
    }
    /**
     * Returns the permission required to access this page
     * @return string
     */
    function get_required_permission()
    {
        return str_replace(array(' ', "\n", "\t"), '_', $this->object->name);
    }
    // Sets an appropriate screen for NextGEN Admin Pages
    function set_screen()
    {
        $screen = get_current_screen();
        if ($screen) {
            $screen->ngg = TRUE;
        } else {
            if (is_null($screen)) {
                $screen = WP_Screen::get($this->object->name);
                $screen->ngg = TRUE;
                set_current_screen($this->object->name);
            }
        }
    }
    /**
     * Enqueues resources required by a NextGEN Admin page
     */
    function enqueue_backend_resources()
    {
        $this->set_screen();
        if (C_NextGen_Admin_Page_Manager::is_requested()) {
            M_NextGen_Admin::enqueue_common_admin_static_resources();
        }
        wp_enqueue_script('jquery');
        $this->object->enqueue_jquery_ui_theme();
        wp_enqueue_script('photocrati_ajax');
        wp_enqueue_script('jquery-ui-accordion');
        if (method_exists('M_Gallery_Display', 'enqueue_fontawesome')) {
            M_Gallery_Display::enqueue_fontawesome();
        }
        // Ensure select2
        wp_enqueue_style('ngg_select2');
        wp_enqueue_script('ngg_select2');
    }
    function enqueue_jquery_ui_theme()
    {
        $settings = C_NextGen_Settings::get_instance();
        wp_enqueue_style($settings->jquery_ui_theme, is_ssl() ? str_replace('http:', 'https:', $settings->jquery_ui_theme_url) : $settings->jquery_ui_theme_url, array(), $settings->jquery_ui_theme_version);
    }
    /**
     * Returns the page title
     * @return string
     */
    function get_page_title()
    {
        return $this->object->name;
    }
    /**
     * Returns the page heading
     * @return string
     */
    function get_page_heading()
    {
        return $this->object->get_page_title();
    }
    /**
     * Returns a header message
     * @return string
     */
    function get_header_message()
    {
        $message = '';
        if (defined('NGG_PRO_PLUGIN_VERSION') || defined('NGG_PLUS_PLUGIN_VERSION')) {
            $message = '<p>' . __("Good work. Keep making the web beautiful.", 'nggallery') . '</p>';
        }
        return $message;
    }
    /**
     * Returns the type of forms to render on this page
     * @return string
     */
    function get_form_type()
    {
        return is_array($this->object->context) ? $this->object->context[0] : $this->object->context;
    }
    function get_success_message()
    {
        return __("Saved successfully", 'nggallery');
    }
    /**
     * Returns an accordion tab, encapsulating the form
     * @param C_Form $form
     * @return string
     */
    function to_accordion_tab($form)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#accordion_tab', array('id' => $form->get_id(), 'title' => $form->get_title(), 'content' => $form->render(TRUE)), TRUE);
    }
    /**
     * Returns the forms registered for the current get_form_type()
     * @return array
     */
    function get_forms()
    {
        $form_manager = C_Form_Manager::get_instance();
        return array_map(function ($form) {
            $form = $this->object->get_registry()->get_utility('I_Form', $form);
            $form->page = $this;
            return $form;
        }, $form_manager->get_forms($this->object->get_form_type()));
    }
    /**
     * Gets the action to be executed
     * @return string
     */
    function _get_action()
    {
        $action = $this->object->param('action') ?: '';
        $retval = preg_quote($action, '/');
        $retval = strtolower(preg_replace("/[^\\w]/", '_', $retval));
        return preg_replace("/_{2,}/", "_", $retval) . '_action';
    }
    /**
     * Returns the template to be rendered for the index action
     * @return string
     */
    function index_template()
    {
        return 'photocrati-nextgen_admin#nextgen_admin_page';
    }
    /**
     * Returns a list of parameters to include when rendering the view
     * @return array
     */
    function get_index_params()
    {
        return array();
    }
    function show_save_button()
    {
        return TRUE;
    }
    /**
     * Renders a NextGEN Admin Page using jQuery Accordions
     */
    function index_action()
    {
        $this->object->enqueue_backend_resources();
        if ($token = $this->object->is_authorized_request()) {
            // Get each form. Validate it and save any changes if this is a post
            // request
            $tabs = array();
            $errors = array();
            $action = $this->object->_get_action();
            $success = $this->object->param('message');
            if ($success) {
                $success = $this->object->get_success_message();
            } else {
                $success = $this->object->is_post_request() ? $this->object->get_success_message() : '';
            }
            // First, process the Post request
            if ($this->object->is_post_request() && $this->object->has_method($action)) {
                $this->object->{$action}($this->object->param($this->context));
            }
            $index_template = $this->object->index_template();
            foreach ($this->object->get_forms() as $form) {
                $form->page = $this->object;
                $form->enqueue_static_resources();
                if ($this->object->is_post_request()) {
                    if ($form->has_method($action)) {
                        $form->{$action}($this->object->param($form->context));
                    }
                }
                // This is a strange but necessary hack: this seemingly extraneous use of to_accordion_tab() normally
                // just means that we're rendering the admin content twice but NextGen Pro's pricelist and coupons pages
                // actually depend on echo'ing the $tabs variable here, unlike the 'nextgen_admin_page' template which
                // doesn't make use of the $tabs parameter at all.
                // TLDR: The next two lines are necessary for the pricelist and coupons pages.
                if ($index_template !== 'photocrati-nextgen_admin#nextgen_admin_page') {
                    $tabs[] = $this->object->to_accordion_tab($form);
                }
                $forms[] = $form;
                if ($form->has_method('get_model') && $form->get_model()) {
                    if ($form->get_model()->is_invalid()) {
                        if ($form_errors = $this->object->show_errors_for($form->get_model(), TRUE)) {
                            $errors[] = $form_errors;
                        }
                        $form->get_model()->clear_errors();
                    }
                }
            }
            // Render the view
            $index_params = array('page_heading' => $this->object->get_page_heading(), 'tabs' => $tabs, 'forms' => $forms, 'errors' => $errors, 'success' => $success, 'form_header' => FALSE, 'header_message' => $this->object->get_header_message(), 'nonce' => M_Security::create_nonce($this->object->get_required_permission()), 'show_save_button' => $this->object->show_save_button(), 'model' => $this->object->has_method('get_model') ? $this->object->get_model() : NULL, 'logo' => $this->object->get_router()->get_static_url('photocrati-nextgen_admin#imagely_icon.png'));
            $index_params = array_merge($index_params, $this->object->get_index_params());
            $this->object->render_partial($index_template, $index_params);
        } else {
            $this->object->render_view('photocrati-nextgen_admin#not_authorized', array('name' => $this->object->name, 'title' => $this->object->get_page_title()));
        }
    }
}
/**
 * Class C_NextGen_Admin_Page_Manager
 * @mixin Mixin_Page_Manager
 * @implements I_Page_Manager
 */
class C_NextGen_Admin_Page_Manager extends C_Component
{
    static $_instance = NULL;
    var $_pages = array();
    /**
     * Gets an instance of the Page Manager
     * @param string|false $context (optional)
     * @return C_NextGen_Admin_Page_Manager
     */
    static function &get_instance($context = FALSE)
    {
        if (is_null(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass($context);
        }
        return self::$_instance;
    }
    /**
     * Defines the instance of the Page Manager
     * @param string $context
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Page_Manager');
        $this->implement('I_Page_Manager');
    }
    /**
     * Determines if a NextGEN page or post type is being requested
     * @return bool|string
     */
    static function is_requested()
    {
        $retval = FALSE;
        if (self::is_requested_page()) {
            $retval = self::is_requested_page();
        } elseif (self::is_requested_post_type()) {
            $retval = self::is_requested_post_type();
        }
        return apply_filters('is_ngg_admin_page', $retval);
    }
    /**
     * Determines if a NextGEN Admin page is being requested
     * @return bool|string
     */
    static function is_requested_page()
    {
        $retval = FALSE;
        // First, check the screen for the "ngg" property. This is how ngglegacy pages register themselves
        $screen = get_current_screen();
        if (property_exists($screen, 'ngg') && $screen->ngg) {
            $retval = $screen->id;
        } else {
            foreach (self::get_instance()->get_all() as $slug => $properties) {
                // Are we rendering a NGG added page?
                if (isset($properties['hook_suffix'])) {
                    $hook_suffix = $properties['hook_suffix'];
                    if (did_action("load-{$hook_suffix}")) {
                        $retval = $slug;
                        break;
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Determines if a NextGEN post type is being requested
     * @return bool|string
     */
    static function is_requested_post_type()
    {
        $retval = FALSE;
        $screen = get_current_screen();
        foreach (self::get_instance()->get_all() as $slug => $properties) {
            // Are we rendering a NGG post type?
            if (isset($properties['post_type']) && $screen->post_type == $properties['post_type']) {
                $retval = $slug;
                break;
            }
        }
        return $retval;
    }
}
class Mixin_Page_Manager extends Mixin
{
    function add($slug, $properties = array())
    {
        if (!isset($properties['adapter'])) {
            $properties['adapter'] = NULL;
        }
        if (!isset($properties['parent'])) {
            $properties['parent'] = NULL;
        }
        if (!isset($properties['add_menu'])) {
            $properties['add_menu'] = TRUE;
        }
        if (!isset($properties['before'])) {
            $properties['before'] = NULL;
        }
        if (!isset($properties['url'])) {
            $properties['url'] = NULL;
        }
        $this->object->_pages[$slug] = $properties;
    }
    function move_page($slug, $other_slug, $after = false)
    {
        $page_list = $this->object->_pages;
        if (isset($page_list[$slug]) && isset($page_list[$other_slug])) {
            $slug_list = array_keys($page_list);
            $item_list = array_values($page_list);
            $slug_idx = array_search($slug, $slug_list);
            $item = $page_list[$slug];
            unset($slug_list[$slug_idx]);
            unset($item_list[$slug_idx]);
            $slug_list = array_values($slug_list);
            $item_list = array_values($item_list);
            $other_idx = array_search($other_slug, $slug_list);
            array_splice($slug_list, $other_idx, 0, array($slug));
            array_splice($item_list, $other_idx, 0, array($item));
            $this->object->_pages = array_combine($slug_list, $item_list);
        }
    }
    function remove($slug)
    {
        unset($this->object->_pages[$slug]);
    }
    function get_all()
    {
        return $this->object->_pages;
    }
    function setup()
    {
        $registry = $this->get_registry();
        $controllers = array();
        foreach ($this->object->_pages as $slug => $properties) {
            $post_type = NULL;
            $page_title = "Unnamed Page";
            $menu_title = "Unnamed Page";
            $permission = NULL;
            $callback = NULL;
            // There's two type of pages we can have. Some are powered by our controllers, and others
            // are powered by WordPress, such as a custom post type page.
            // Is this powered by a controller? If so, we expect an adapter
            if ($properties['adapter']) {
                $controllers[$slug] = $registry->get_utility('I_NextGen_Admin_Page', $slug);
                $menu_title = $controllers[$slug]->get_page_heading();
                $page_title = $controllers[$slug]->get_page_title();
                $permission = $controllers[$slug]->get_required_permission();
                $callback = array(&$controllers[$slug], 'index_action');
            } elseif ($properties['url']) {
                $url = $properties['url'];
                if (preg_match("/post_type=([^&]+)/", $url, $matches)) {
                    $this->object->_pages[$slug]['post_type'] = $matches[1];
                }
                $slug = $url;
                if (isset($properties['menu_title'])) {
                    $menu_title = $properties['menu_title'];
                }
                if (isset($properties['permission'])) {
                    $permission = $properties['permission'];
                }
            }
            // Are we to add a menu?
            if ($properties['add_menu'] && current_user_can($permission)) {
                $this->object->_pages[$slug]['hook_suffix'] = add_submenu_page($properties['parent'], $page_title, $menu_title, $permission, $slug, $callback);
                if ($properties['before']) {
                    global $submenu;
                    if (empty($submenu[$properties['parent']])) {
                        $parent = null;
                    } else {
                        $parent = $submenu[$properties['parent']];
                    }
                    $item_index = -1;
                    $before_index = -1;
                    if ($parent != null) {
                        foreach ($parent as $index => $menu) {
                            // under add_submenu_page, $menu_slug is index 2
                            // $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title );
                            if ($menu[2] == $slug) {
                                $item_index = $index;
                            } else {
                                if ($menu[2] == $properties['before']) {
                                    $before_index = $index;
                                }
                            }
                        }
                    }
                    if ($item_index > -1 && $before_index > -1) {
                        $item = $parent[$item_index];
                        unset($parent[$item_index]);
                        $parent = array_values($parent);
                        if ($item_index < $before_index) {
                            $before_index--;
                        }
                        array_splice($parent, $before_index, 0, array($item));
                        $submenu[$properties['parent']] = $parent;
                    }
                }
            }
        }
        do_action('ngg_pages_setup');
    }
}
// For backwards compatibility
// TODO: Remove some time in 2018
class C_Page_Manager
{
    /**
     * @return C_NextGen_Admin_Page_Manager
     */
    static function get_instance()
    {
        return C_NextGen_Admin_Page_Manager::get_instance();
    }
    static function is_requested()
    {
        return C_NextGen_Admin_Page_Manager::is_requested();
    }
}
class C_NextGen_First_Run_Notification_Wizard
{
    protected static $_instance = NULL;
    /**
     * @return bool
     */
    public function is_renderable()
    {
        return TRUE;
    }
    /**
     * @return string
     */
    public function render()
    {
        $block = <<<EOT
        <style>
            div#ngg-wizard-video {
                width: 710px;
                max-width: 710px;
            }
        </style>
        <div class="hidden" id="ngg-wizard-video" style="border: none">
            <iframe width="640"
                    height="480"
                    src="https://www.youtube.com/embed/ZAYj6D5XXNk"
                    frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media;"
                    allowfullscreen></iframe>
        </div>
EOT;
        return __('Thanks for installing NextGEN Gallery! Want help creating your first gallery?', 'nggallery') . ' <a id="ngg-video-wizard-invoker" href="">' . __('Launch the Gallery Wizard', 'nggallery') . '</a>. ' . __('If you close this message, you can also launch the Gallery Wizard at any time from the', 'nggallery') . ' <a href="' . esc_url(admin_url('admin.php?page=nextgen-gallery')) . '">' . __('NextGEN Overview page', 'nggallery') . '</a>.' . $block;
    }
    public function get_css_class()
    {
        return 'updated';
    }
    public function is_dismissable()
    {
        return TRUE;
    }
    public function dismiss($code)
    {
        return ['handled' => TRUE];
    }
    public function enqueue_backend_resources()
    {
        wp_enqueue_script('nextgen_first_run_wizard', C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#first_run_wizard.js'), ['jquery', 'jquery-modal'], NGG_SCRIPT_VERSION, TRUE);
        wp_enqueue_style('jquery-modal');
    }
    /**
     * @return C_NextGen_First_Run_Notification_Wizard
     */
    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
}
/**
 * Class C_NextGEN_Wizard
 */
class C_NextGEN_Wizard
{
    var $_id = null;
    var $_active = false;
    var $_priority = 100;
    var $_data = array();
    var $_steps = array();
    var $_current_step = null;
    var $_view = null;
    function __construct($id)
    {
        $this->_id = $id;
    }
    function get_id()
    {
        return $this->_id;
    }
    function is_active()
    {
        return $this->_active;
    }
    function set_active($active)
    {
        $this->_active = $active;
    }
    function get_priority()
    {
        return $this->_priority;
    }
    function set_priority($priority)
    {
        $this->_priority = $priority;
    }
    function is_completed()
    {
        if (isset($this->_data['state'])) {
            return $this->_data['state'] == 'completed';
        }
        return false;
    }
    function set_completed()
    {
        $this->_data['state'] = 'completed';
    }
    function is_cancelled()
    {
        if (isset($this->_data['state'])) {
            return $this->_data['state'] == 'cancelled';
        }
        return false;
    }
    function set_cancelled()
    {
        $this->_data['state'] = 'cancelled';
    }
    function add_step($step_id, $label = null, $properties = null)
    {
        $step = array('label' => $label, 'target_anchor' => 'top center', 'popup_anchor' => 'bottom center', 'target_wait' => '0');
        if ($properties != null) {
            $step = array_merge($step, $properties);
        }
        $this->_steps[$step_id] = $step;
    }
    function get_step_list()
    {
        return array_keys($this->_steps);
    }
    function get_step_property($step_id, $prop_name)
    {
        if (isset($this->_steps[$step_id][$prop_name])) {
            return $this->_steps[$step_id][$prop_name];
        }
        return null;
    }
    function set_step_property($step_id, $prop_name, $prop_value)
    {
        if (!isset($this->_steps[$step_id])) {
            $this->add_step($step_id);
        }
        if (isset($this->_steps[$step_id])) {
            $this->_steps[$step_id][$prop_name] = $prop_value;
        }
    }
    function get_step_label($step_id)
    {
        return $this->get_step_property($step_id, 'label');
    }
    function set_step_label($step_id, $label)
    {
        $this->set_step_property($step_id, 'label', $label);
    }
    function get_step_text($step_id)
    {
        return $this->get_step_property($step_id, 'text');
    }
    function set_step_text($step_id, $text)
    {
        $this->set_step_property($step_id, 'text', $text);
    }
    function get_step_target_anchor($step_id)
    {
        return $this->get_step_property($step_id, 'target_anchor');
    }
    function set_step_target_anchor($step_id, $anchor)
    {
        $this->set_step_property($step_id, 'target_anchor', $anchor);
    }
    function get_step_target_wait($step_id)
    {
        return $this->get_step_property($step_id, 'target_wait');
    }
    function set_step_target_wait($step_id, $wait)
    {
        $this->set_step_property($step_id, 'target_wait', $wait);
    }
    function get_step_optional($step_id)
    {
        return $this->get_step_property($step_id, 'optional');
    }
    function set_step_optional($step_id, $optional)
    {
        $this->set_step_property($step_id, 'optional', $optional);
    }
    function get_step_lazy($step_id)
    {
        return $this->get_step_property($step_id, 'lazy');
    }
    function set_step_lazy($step_id, $lazy)
    {
        $this->set_step_property($step_id, 'lazy', $lazy);
    }
    function get_step_context($step_id)
    {
        return $this->get_step_property($step_id, 'context');
    }
    function set_step_context($step_id, $context)
    {
        $this->set_step_property($step_id, 'context', $context);
    }
    function get_step_popup_anchor($step_id)
    {
        return $this->get_step_property($step_id, 'popup_anchor');
    }
    function set_step_popup_anchor($step_id, $anchor)
    {
        $this->set_step_property($step_id, 'popup_anchor', $anchor);
    }
    function get_step_target($step_id)
    {
        return $this->get_step_property($step_id, 'target');
    }
    function set_step_target($step_id, $target, $target_anchor = null, $popup_anchor = null)
    {
        $this->set_step_property($step_id, 'target', $target);
        if ($target_anchor != null) {
            $this->set_step_target_anchor($step_id, $target_anchor);
        }
        if ($popup_anchor != null) {
            $this->set_step_popup_anchor($step_id, $popup_anchor);
        }
    }
    function get_step_view($step_id)
    {
        return $this->get_step_property($step_id, 'view');
    }
    function set_step_view($step_id, $view)
    {
        $this->set_step_property($step_id, 'view', $view);
    }
    function get_step_condition($step_id)
    {
        return $this->get_step_property($step_id, 'condition');
    }
    function set_step_condition($step_id, $condition_type, $condition_value, $condition_context = null, $condition_timeout = -1)
    {
        $condition = array('type' => $condition_type, 'value' => $condition_value, 'context' => $condition_context, 'timeout' => $condition_timeout);
        $this->set_step_property($step_id, 'condition', $condition);
    }
    function get_current_step()
    {
        return $this->_current_step;
    }
    function set_current_step($step_id)
    {
        $this->_current_step = $step_id;
    }
    function get_view()
    {
        return $this->_view;
    }
    function set_view($view)
    {
        $this->_view = $view;
    }
    function toData()
    {
        $steps = array();
        $view = $this->_view;
        $current_step = $this->_current_step;
        foreach ($this->_steps as $step_id => $step) {
            if ($current_step == null) {
                $current_step = $step_id;
            }
            if ($current_step == $step_id && isset($step['view'])) {
                $view = $step['view'];
            }
            $step['id'] = $step_id;
            $steps[] = $step;
        }
        $ret = new stdClass();
        $ret->id = $this->_id;
        $ret->view = $view;
        $ret->steps = $steps;
        $ret->current_step = $this->_current_step;
        return $ret;
    }
    function _set_data($data)
    {
        if ($data == null) {
            $data = array();
        }
        $this->_data = $data;
    }
}
/**
 * Class C_NextGEN_Wizard_Manager
 * @implements I_NextGEN_Wizard_Manager
 */
class C_NextGEN_Wizard_Manager extends C_Component
{
    static $_instances = array();
    var $_active = false;
    var $_wizards = array();
    var $_wizards_data = array();
    var $_starter = null;
    var $_handled_query = false;
    /**
     * Returns an instance of the wizard manager
     * @param bool|string $context
     * @return C_NextGEN_Wizard_Manager
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the instance
     * @param mixed $context
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_NextGEN_Wizard_Manager');
        $this->_wizards_data = get_option('ngg_wizards');
    }
    function add_wizard($id, $active = false, $priority = 100)
    {
        $wizard = new C_NextGEN_Wizard($id);
        $wizard->set_active($active);
        $wizard->set_priority($priority);
        if (isset($this->_wizards_data[$id])) {
            $wizard->_set_data($this->_wizards_data[$id]);
        }
        $this->_wizards[$id] = $wizard;
        return $wizard;
    }
    function remove_wizard($id)
    {
        if (isset($this->_wizards[$id])) {
            unset($this->_wizards[$id]);
        }
    }
    function get_wizard($id)
    {
        if (isset($this->_wizards[$id])) {
            return $this->_wizards[$id];
        }
        return null;
    }
    function _sort_wizards($wizard1, $wizard2)
    {
        $diff = $wizard1->get_priority() - $wizard2->get_priority();
        if ($diff == 0) {
            $wizard_ids = array_keys($this->_wizards);
            $index1 = array_search($wizard1->get_id(), $wizard_ids, true);
            $index2 = array_search($wizard2->get_id(), $wizard_ids, true);
            if ($index1 !== false && $index2 !== false) {
                $diff = $index1 - $index2;
            }
        }
        return $diff;
    }
    function get_next_wizard()
    {
        if (!$this->is_active()) {
            return null;
        }
        $wizards = $this->_wizards;
        if (count($wizards) > 0) {
            if (count($wizards) > 1) {
                uasort($wizards, array($this, '_sort_wizards'));
            }
            foreach ($wizards as $id => $wizard) {
                if ($wizard->is_active()) {
                    return $wizard;
                }
            }
        }
        return null;
    }
    function get_running_wizard()
    {
        if (!$this->is_active()) {
            return null;
        }
        $wizards = $this->_wizards;
        if (count($wizards) > 0) {
            if (count($wizards) > 1) {
                uasort($wizards, array($this, '_sort_wizards'));
            }
            foreach ($wizards as $id => $wizard) {
                if ($wizard->is_active() && $wizard->get_current_step() != null) {
                    return $wizard;
                }
            }
        }
        return null;
    }
    function get_starter()
    {
        return $this->_starter;
    }
    function set_starter($starter)
    {
        $this->_starter = $starter;
    }
    function is_active()
    {
        return $this->_active;
    }
    function set_active($active)
    {
        $this->_active = $active;
    }
    function generate_wizard_query($wizard, $action, $params = array())
    {
    }
    function handle_wizard_query($parameters = NULL, $force = false)
    {
        if ($this->_handled_query && !$force) {
            return;
        }
        if ($parameters == null) {
            $parameters = $_REQUEST;
        }
        // determine if we're currently in the middle of a wizard (i.e. wizard that involves multiple pages)
        // if so then determine the current step
        if (isset($parameters['ngg_wizard'])) {
            $wizard = $this->get_wizard($parameters['ngg_wizard']);
            if ($wizard != null) {
                $wizard->set_active(true);
                $steps = $wizard->get_step_list();
                $count = count($steps);
                $current_step = isset($parameters['ngg_wizard_step']) ? $parameters['ngg_wizard_step'] : null;
                if ($current_step != null) {
                    $idx = array_search($current_step, $steps);
                    if ($idx !== false) {
                        $idx++;
                        if ($idx < $count) {
                            $wizard->set_current_step($steps[$idx]);
                        }
                    }
                } else {
                    if ($count > 0) {
                        $wizard->set_current_step($steps[0]);
                    }
                }
            }
            $this->_handled_query = true;
        }
    }
}