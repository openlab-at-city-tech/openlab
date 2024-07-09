<?php
/**
 * Provides validation for datamapper entities within an MVC controller
 *
 * @mixin C_MVC_Controller
 * @adapts I_MVC_Controller
 */
class A_MVC_Validation extends Mixin
{
    public function show_errors_for($entity, $return = false)
    {
        $retval = '';
        if (!$entity->is_valid()) {
            $retval = $this->object->render_partial('photocrati-nextgen_admin#entity_errors', ['entity' => $entity], $return);
        }
        return $retval;
    }
    public function show_success_for($entity, $message, $return = false)
    {
        $retval = '';
        if ($entity->is_valid()) {
            $retval = $this->object->render_partial('photocrati-nextgen_admin#entity_saved', ['entity' => $entity, 'message' => $message]);
        }
        return $retval;
    }
}
/**
 * Class C_Form
 *
 * @mixin Mixin_Form_Instance_Methods
 * @mixin Mixin_Form_Field_Generators
 * @implements I_Form
 */
class C_Form extends C_MVC_Controller
{
    static $_instances = array();
    var $page = null;
    /**
     * Gets an instance of a form
     *
     * @param string $context
     * @return C_Form
     */
    public static function &get_instance($context)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Form($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the form
     *
     * @param string|bool $context (optional).
     */
    public function define($context = false)
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
    public function enqueue_static_resources()
    {
    }
    /**
     * Gets a list of fields to render
     *
     * @return array
     */
    public function _get_field_names()
    {
        return [];
    }
    public function get_id()
    {
        return $this->object->context;
    }
    public function get_title()
    {
        return $this->object->context;
    }
    /**
     * Saves the form/model
     *
     * @param array $attributes
     * @return bool
     */
    public function save_action($attributes = array())
    {
        if (!$attributes) {
            $attributes = [];
        }
        if ($this->object->has_method('get_model') && $this->object->get_model()) {
            return $this->object->get_model()->save($attributes);
        } else {
            return true;
        }
    }
    /**
     * Returns the rendered form
     *
     * @param bool $wrap (optional) Default = true.
     * @return string
     */
    public function render($wrap = true)
    {
        $fields = [];
        foreach ($this->object->_get_field_names() as $field) {
            $method = "_render_{$field}_field";
            if ($this->object->has_method($method)) {
                $fields[] = $this->object->{$method}($this->object->get_model());
            }
        }
        return $this->object->render_partial('photocrati-nextgen_admin#form', ['fields' => $fields, 'wrap' => $wrap], true);
    }
    public function get_model()
    {
        return $this->object->page->has_method('get_model') ? $this->object->page->get_model() : null;
    }
    public function get_i18n_strings()
    {
    }
}
/**
 * Provides some default field generators for forms to use
 */
class Mixin_Form_Field_Generators extends Mixin
{
    /**
     * @param stdClass|C_Display_Type $display_type
     * @param string                  $name
     * @param string                  $label
     * @param array                   $options
     * @param int|string              $value
     * @param string                  $text
     * @param bool                    $hidden
     * @return string
     */
    public function _render_select_field($display_type, $name, $label, $options, $value, $text = '', $hidden = false)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_select', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'options' => $options, 'value' => $value, 'text' => $text, 'hidden' => $hidden], true);
    }
    public function _render_radio_field($display_type, $name, $label, $value, $text = '', $hidden = false)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_radio', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden], true);
    }
    public function _render_number_field($display_type, $name, $label, $value, $text = '', $hidden = false, $placeholder = '', $min = null, $max = null)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_number', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder, 'min' => $min, 'max' => $max], true);
    }
    public function _render_text_field($display_type, $name, $label, $value, $text = '', $hidden = false, $placeholder = '')
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_text', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder], true);
    }
    public function _render_textarea_field($display_type, $name, $label, $value, $text = '', $hidden = false, $placeholder = '')
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_textarea', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden, 'placeholder' => $placeholder], true);
    }
    public function _render_color_field($display_type, $name, $label, $value, $text = '', $hidden = false)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_color', ['display_type_name' => $display_type->name, 'name' => $name, 'label' => $label, 'value' => $value, 'text' => $text, 'hidden' => $hidden], true);
    }
    /**
     * Renders a pair of fields for width and width-units (px, em, etc)
     *
     * @param C_Display_Type $display_type
     * @return string
     */
    public function _render_width_and_unit_field($display_type)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#field_generator/nextgen_settings_field_width_and_unit', ['display_type_name' => $display_type->name, 'name' => 'width', 'label' => __('Gallery width', 'nggallery'), 'value' => $display_type->settings['width'], 'text' => __('An empty or 0 setting will make the gallery full width', 'nggallery'), 'placeholder' => __('(optional)', 'nggallery'), 'unit_name' => 'width_unit', 'unit_value' => $display_type->settings['width_unit'], 'options' => ['px' => __('Pixels', 'nggallery'), '%' => __('Percent', 'nggallery')]], true);
    }
    public function _get_aspect_ratio_options()
    {
        return ['first_image' => __('First Image', 'nggallery'), 'image_average' => __('Average', 'nggallery'), '1.5' => '3:2 [1.5]', '1.333' => '4:3 [1.333]', '1.777' => '16:9 [1.777]', '1.6' => '16:10 [1.6]', '1.85' => '1.85:1 [1.85]', '2.39' => '2.39:1 [2.39]', '1.81' => '1.81:1 [1.81]', '1' => '1:1 (Square) [1]'];
    }
}
class C_Mailchimp_OptIn_Notice
{
    /** @var C_Mailchimp_OptIn_Notice $_instance */
    static $_instance = null;
    /**
     * @return C_Mailchimp_OptIn_Notice
     */
    public static function get_instance()
    {
        if (!self::$_instance) {
            self::$_instance = new C_Mailchimp_OptIn_Notice();
        }
        return self::$_instance;
    }
    /**
     * @return string
     */
    public function get_css_class()
    {
        return 'notice notice-success';
    }
    /**
     * @return bool
     */
    public function is_dismissable()
    {
        return true;
    }
    /**
     * @param $code
     * @return array
     */
    public function dismiss($code)
    {
        return ['handled' => true];
    }
    /**
     * @return bool
     */
    public function is_renderable()
    {
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'attach_to_post') !== false) {
            return false;
        }
        if (!C_NextGen_Admin_Page_Manager::is_requested()) {
            return false;
        }
        if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME') || defined('NGG_PRO_PLUGIN_BASENAME')) {
            return false;
        }
        $settings = \Imagely\NGG\Settings\Settings::get_instance();
        try {
            $time = time();
            $install = new DateTime('@' . $settings->get('installDate'));
            $now = new DateTime('@' . $time);
            $diff = (int) $install->diff($now)->format('%a days');
            if ($diff >= 14) {
                return true;
            }
        } catch (Exception $exception) {
        }
        return false;
    }
    /**
     * @return string
     */
    public function render()
    {
        $manager = \Imagely\NGG\Admin\Notifications\Manager::get_instance();
        $view = new C_MVC_View('photocrati-nextgen_admin#mailchimp_optin', ['dismiss_url' => $manager->_dismiss_url . '&name=mailchimp_opt_in&code=1', 'i18n' => ['headline' => __('Thank you for using NextGEN Gallery!', 'nggallery'), 'message' => __('Get NextGEN Gallery updates, photography tips, business tips, tutorials, and resources straight to your mailbox.', 'nggallery'), 'submit' => __('Yes, Please!', 'nggallery'), 'confirmation' => __('Thank you for subscribing!', 'nggallery'), 'email_placeholder' => __('Email Address', 'nggallery'), 'name_placeholder' => __('First Name', 'nggallery'), 'connect_error' => __('Cannot connect to the registration server right now. Please try again later.', 'nggallery')]]);
        return $view->render(true);
    }
}
if (!class_exists('C_NextGen_Admin_Installer')) {
}
/**
 * @mixin Mixin_NextGen_Admin_Page_Instance_Methods
 */
class C_NextGen_Admin_Page_Controller extends C_MVC_Controller
{
    static $_instances = array();
    public $name;
    /**
     * @param bool|string $context
     * @return C_NextGen_Admin_Page_Controller
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_NextGen_Admin_Page_Controller($context);
        }
        return self::$_instances[$context];
    }
    public function define($context = false)
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
    public function is_authorized_request($privilege = null)
    {
        if (!$privilege) {
            $privilege = $this->object->get_required_permission();
        }
        // Security::verify_nonce() is a wrapper to wp_verify_nonce().
        //
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if ($this->object->is_post_request() && (!isset($_REQUEST['nonce']) || !\Imagely\NGG\Util\Security::verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), $privilege))) {
            return false;
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        // Ensure that the user has permission to access this page.
        return \Imagely\NGG\Util\Security::is_allowed($privilege);
    }
    /**
     * Returns the permission required to access this page
     *
     * @return string
     */
    public function get_required_permission()
    {
        return str_replace([' ', "\n", "\t"], '_', $this->object->name);
    }
    // Sets an appropriate screen for NextGEN Admin Pages.
    public function set_screen()
    {
        $screen = get_current_screen();
        if ($screen) {
            $screen->ngg = true;
        } elseif (is_null($screen)) {
            $screen = WP_Screen::get($this->object->name);
            $screen->ngg = true;
            set_current_screen($this->object->name);
        }
    }
    /**
     * Enqueues resources required by a NextGEN Admin page
     */
    public function enqueue_backend_resources()
    {
        $this->set_screen();
        if (C_NextGen_Admin_Page_Manager::is_requested()) {
            M_NextGen_Admin::enqueue_common_admin_static_resources();
        }
        wp_enqueue_script('jquery');
        $this->object->enqueue_jquery_ui_theme();
        wp_enqueue_script('photocrati_ajax');
        wp_enqueue_script('jquery-ui-accordion');
        \Imagely\NGG\Display\DisplayManager::enqueue_fontawesome();
        // Ensure select2.
        wp_enqueue_style('ngg_select2');
        wp_enqueue_script('ngg_select2');
    }
    public function enqueue_jquery_ui_theme()
    {
        $settings = \Imagely\NGG\Settings\Settings::get_instance();
        wp_enqueue_style($settings->jquery_ui_theme, is_ssl() ? str_replace('http:', 'https:', $settings->jquery_ui_theme_url) : $settings->jquery_ui_theme_url, [], $settings->jquery_ui_theme_version);
    }
    /**
     * Returns the page title
     *
     * @return string
     */
    public function get_page_title()
    {
        return $this->object->name;
    }
    /**
     * Returns the page heading
     *
     * @return string
     */
    public function get_page_heading()
    {
        return $this->object->get_page_title();
    }
    /**
     * Returns a header message
     *
     * @return string
     */
    public function get_header_message()
    {
        $message = '';
        if (defined('NGG_PRO_PLUGIN_VERSION') || defined('NGG_PLUS_PLUGIN_VERSION')) {
            $message = '<p>' . __('Good work. Keep making the web beautiful.', 'nggallery') . '</p>';
        }
        return $message;
    }
    /**
     * Returns the type of forms to render on this page
     *
     * @return string
     */
    public function get_form_type()
    {
        return is_array($this->object->context) ? $this->object->context[0] : $this->object->context;
    }
    public function get_success_message()
    {
        return __('Saved successfully', 'nggallery');
    }
    /**
     * Returns an accordion tab, encapsulating the form
     *
     * @param C_Form $form
     * @return string
     */
    public function to_accordion_tab($form)
    {
        return $this->object->render_partial('photocrati-nextgen_admin#accordion_tab', ['id' => $form->get_id(), 'title' => $form->get_title(), 'content' => $form->render(true)], true);
    }
    /**
     * Returns the forms registered for the current get_form_type()
     *
     * @return array
     */
    public function get_forms()
    {
        $form_manager = \Imagely\NGG\Admin\FormManager::get_instance();
        return array_map(function ($form) {
            $form = $this->object->get_registry()->get_utility('I_Form', $form);
            $form->page = $this;
            return $form;
        }, $form_manager->get_forms($this->object->get_form_type()));
    }
    /**
     * Gets the action to be executed
     *
     * @return string
     */
    public function _get_action()
    {
        $action = $this->object->param('action') ?: '';
        $retval = preg_quote($action, '/');
        $retval = strtolower(preg_replace('/[^\\w]/', '_', $retval));
        return preg_replace('/_{2,}/', '_', $retval) . '_action';
    }
    /**
     * Returns the template to be rendered for the index action
     *
     * @return string
     */
    public function index_template()
    {
        return 'photocrati-nextgen_admin#nextgen_admin_page';
    }
    /**
     * Returns a list of parameters to include when rendering the view
     *
     * @return array
     */
    public function get_index_params()
    {
        return [];
    }
    public function show_save_button()
    {
        return true;
    }
    /**
     * Renders a NextGEN Admin Page using jQuery Accordions
     */
    public function index_action()
    {
        $this->object->enqueue_backend_resources();
        if ($token = $this->object->is_authorized_request()) {
            // Get each form. Validate it and save any changes if this is a post
            // request.
            $tabs = [];
            $errors = [];
            $action = $this->object->_get_action();
            $success = $this->object->param('message');
            if ($success) {
                $success = $this->object->get_success_message();
            } else {
                $success = $this->object->is_post_request() ? $this->object->get_success_message() : '';
            }
            // First, process the Post request.
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
                    if (!$form->get_model()->is_valid()) {
                        if ($form_errors = $this->object->show_errors_for($form->get_model(), true)) {
                            $errors[] = $form_errors;
                        }
                        $form->get_model()->clear_errors();
                    }
                }
            }
            // Render the view.
            $index_params = ['page_heading' => $this->object->get_page_heading(), 'tabs' => $tabs, 'forms' => $forms, 'errors' => $errors, 'success' => $success, 'form_header' => false, 'header_message' => $this->object->get_header_message(), 'nonce' => \Imagely\NGG\Util\Security::create_nonce($this->object->get_required_permission()), 'show_save_button' => $this->object->show_save_button(), 'model' => $this->object->has_method('get_model') ? $this->object->get_model() : null, 'logo' => $this->object->get_router()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')];
            $index_params = array_merge($index_params, $this->object->get_index_params());
            $this->object->render_partial($index_template, $index_params);
        } else {
            $this->object->render_view('photocrati-nextgen_admin#not_authorized', ['name' => $this->object->name, 'title' => $this->object->get_page_title()]);
        }
    }
}
/**
 * Class C_NextGen_Admin_Page_Manager
 *
 * @mixin Mixin_Page_Manager
 * @implements I_Page_Manager
 */
class C_NextGen_Admin_Page_Manager extends C_Component
{
    static $_instance = null;
    var $_pages = array();
    /**
     * Gets an instance of the Page Manager
     *
     * @param string|false $context (optional).
     * @return C_NextGen_Admin_Page_Manager
     */
    public static function &get_instance($context = false)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new C_NextGen_Admin_Page_Manager($context);
        }
        return self::$_instance;
    }
    /**
     * Defines the instance of the Page Manager
     *
     * @param string $context
     */
    public function define($context = false)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Page_Manager');
        $this->implement('I_Page_Manager');
    }
    /**
     * Determines if a NextGEN page or post type is being requested
     *
     * @return bool|string
     */
    public static function is_requested()
    {
        $retval = false;
        if (self::is_requested_page()) {
            $retval = self::is_requested_page();
        } elseif (self::is_requested_post_type()) {
            $retval = self::is_requested_post_type();
        }
        return apply_filters('is_ngg_admin_page', $retval);
    }
    /**
     * Determines if a NextGEN Admin page is being requested
     *
     * @return bool|string
     */
    static function is_requested_page()
    {
        $retval = false;
        // First, check the screen for the "ngg" property. This is how legacy pages register themselves.
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
     *
     * @return bool|string
     */
    public static function is_requested_post_type()
    {
        $retval = false;
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
    public function add($slug, $properties = array())
    {
        if (!isset($properties['adapter'])) {
            $properties['adapter'] = null;
        }
        if (!isset($properties['parent'])) {
            $properties['parent'] = null;
        }
        if (!isset($properties['add_menu'])) {
            $properties['add_menu'] = true;
        }
        if (!isset($properties['before'])) {
            $properties['before'] = null;
        }
        if (!isset($properties['url'])) {
            $properties['url'] = null;
        }
        $this->object->_pages[$slug] = $properties;
    }
    public function move_page($slug, $other_slug, $after = false)
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
            array_splice($slug_list, $other_idx, 0, [$slug]);
            array_splice($item_list, $other_idx, 0, [$item]);
            $this->object->_pages = array_combine($slug_list, $item_list);
        }
    }
    public function remove($slug)
    {
        unset($this->object->_pages[$slug]);
    }
    public function get_all()
    {
        return $this->object->_pages;
    }
    public function setup()
    {
        $registry = $this->get_registry();
        $controllers = [];
        foreach ($this->object->_pages as $slug => $properties) {
            $post_type = null;
            $page_title = 'Unnamed Page';
            $menu_title = 'Unnamed Page';
            $permission = null;
            $callback = null;
            // There's two type of pages we can have. Some are powered by our controllers, and others
            // are powered by WordPress, such as a custom post type page.
            // Is this powered by a controller? If so, we expect an adapter.
            if ($properties['adapter']) {
                $controllers[$slug] = $registry->get_utility('I_NextGen_Admin_Page', $slug);
                $menu_title = $controllers[$slug]->get_page_heading();
                $page_title = $controllers[$slug]->get_page_title();
                $permission = $controllers[$slug]->get_required_permission();
                $callback = [&$controllers[$slug], 'index_action'];
            } elseif ($properties['url']) {
                $url = $properties['url'];
                if (preg_match('/post_type=([^&]+)/', $url, $matches)) {
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
                            // $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title );.
                            if ($menu[2] == $slug) {
                                $item_index = $index;
                            } elseif ($menu[2] == $properties['before']) {
                                $before_index = $index;
                            }
                        }
                    }
                    if ($item_index > -1 && $before_index > -1) {
                        $item = $parent[$item_index];
                        unset($parent[$item_index]);
                        $parent = array_values($parent);
                        if ($item_index < $before_index) {
                            --$before_index;
                        }
                        array_splice($parent, $before_index, 0, [$item]);
                        $submenu[$properties['parent']] = $parent;
                    }
                }
            }
        }
        do_action('ngg_pages_setup');
    }
}
class C_NextGen_First_Run_Notification_Wizard
{
    protected static $_instance = null;
    /**
     * @return bool
     */
    public function is_renderable()
    {
        return true;
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
        return true;
    }
    public function dismiss($code)
    {
        return ['handled' => true];
    }
    public function enqueue_backend_resources()
    {
        wp_enqueue_script('nextgen_first_run_wizard', \Imagely\NGG\Util\Router::get_instance()->get_static_url('photocrati-nextgen_admin#first_run_wizard.js'), ['jquery', 'jquery-modal'], NGG_SCRIPT_VERSION, true);
        wp_enqueue_style('jquery-modal');
    }
    /**
     * @return C_NextGen_First_Run_Notification_Wizard
     */
    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_NextGen_First_Run_Notification_Wizard();
        }
        return self::$_instance;
    }
}