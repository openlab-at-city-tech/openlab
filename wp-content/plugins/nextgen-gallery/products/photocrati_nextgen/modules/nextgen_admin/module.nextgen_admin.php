<?php

/***
{
    Module: photocrati-nextgen_admin
}
***/

define('NGG_FS_ACCESS_SLUG', 'ngg_fs_access');

class M_NextGen_Admin extends C_Base_Module
{
    /**
     * Defines the module
     */
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-nextgen_admin',
            'NextGEN Administration',
            'Provides a framework for adding Administration pages',
            '3.3.11',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );

        C_Photocrati_Installer::add_handler($this->module_id, 'C_NextGen_Admin_Installer');

        C_NextGen_Settings::get_instance()->add_option_handler('C_NextGen_Admin_Option_Handler', array(
            'jquery_ui_theme',
            'jquery_ui_theme_version',
            'jquery_ui_theme_url'
        ));
        if (is_multisite()) C_NextGen_Global_Settings::get_instance()->add_option_handler('C_NextGen_Admin_Option_Handler', array(
            'jquery_ui_theme',
            'jquery_ui_theme_version',
            'jquery_ui_theme_url'
        ));
    }

    /**
     * Register utilities necessary for this module (and the plugin)
     */
    function _register_utilities()
    {
        // Provides a NextGEN Administation page
        $this->get_registry()->add_utility(
            'I_NextGen_Admin_Page',
            'C_NextGen_Admin_Page_Controller'
        );

        $this->get_registry()->add_utility(
            'I_Page_Manager',
            'C_NextGen_Admin_Page_Manager'
        );

        // Provides a form manager
        $this->get_registry()->add_utility(
            'I_Form_Manager',
            'C_Form_Manager'
        );

        // Provides a form
        $this->get_registry()->add_utility(
            'I_Form',
            'C_Form'
        );
    }

    /**
     * Registers adapters required by this module
     */
    function _register_adapters()
    {
        $this->get_registry()->add_adapter(
            'I_MVC_Controller',
            'A_MVC_Validation'
        );
    }


    function get_common_admin_css_handlers($array)
    {
        $array['nextgen_admin_css'] = $this->module_version;
        return $array;
    }

    function get_common_admin_js_handlers($array)
    {
        $array['nextgen_admin_js'] = $this->module_version;
        return $array;
    }

    // Enqueues static resources that should be enqueued in the HEADER on a NextGEN Admin Page
    static function enqueue_common_admin_static_resources($footer=FALSE)
    {
        $footer = ($footer === TRUE);
        $script_handles_filter = $footer ? 'ngg_admin_footer_script_handles' : 'ngg_admin_script_handles';
        $style_handles_filter  = 'ngg_admin_style_handles';
        $enqueue_action        = $footer ? 'ngg_admin_footer_enqueue_scripts' : 'ngg_admin_enqueue_scripts';

        if ($slug = C_NextGen_Admin_Page_Manager::is_requested()) {
            $script_handles = apply_filters($script_handles_filter, array(), $slug);
            $style_handles = apply_filters($style_handles_filter, array(), $slug);

            foreach ($script_handles as $handle => $version) {
                $hook = "ngg_enqueue_{$handle}_script";
                if (has_action($hook))
                    do_action($hook, $handle, $version);
                else
                    wp_enqueue_script($handle, '', array(), $version);

            }

            if (!$footer) {
                foreach ($style_handles as $handle => $version) {
                    $hook = "ngg_enqueue_{$handle}_style";
                    if (has_action($hook))
                        do_action($hook, $handle, $version);
                    else
                        wp_enqueue_style($handle, '', array(), $version);
                }
            }

            // Expose a means for other modules or third-party plugins to provide their own
            // enqueue calls
            do_action($enqueue_action, $slug);
        }

        // Have the toplevel "NextGEN Gallery" link to the Manage Galleries page 
        wp_add_inline_script('common', "jQuery(function($){
            var parent = $('.toplevel_page_nextgen-gallery');
            var manageGalleryUrl = parent.find('a[href*=\"manage-gallery\"]').attr('href');
            parent.attr('href', manageGalleryUrl);
        })");
    }

    // Enqueues static resources that should be enqueued in the FOOTER on a NextGEN Admin Page
    function enqueue_common_admin_footer_static_resources()
    {
        self::enqueue_common_admin_static_resources(TRUE);
    }

    /**
     * Hooks into the WordPress Framework
     */
    function _register_hooks()
    {
        // Register scripts
        add_action('init', array($this, 'register_scripts'), 9);
        add_action('init', array($this, 'handle_cache_flush_requests'));

        // Elementor's editor.php runs `new \WP_Scripts()` which requires we register scripts on both init and this
        // action if we want our resources to be used with the page builder.
        add_action('elementor/editor/before_enqueue_scripts', array($this, 'register_scripts'));

        // Wizard-related hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_wizard_components'));
        add_action('current_screen', array($this, 'init_wizards'), 20);
        add_action('current_screen', array($this, 'handle_wizards'), 100);

        // Enqueue common static resources for NGG admin pages
        add_filter('ngg_admin_style_handles', array($this, 'get_common_admin_css_handlers'));
        add_filter('ngg_admin_script_handles', array($this, 'get_common_admin_js_handlers'));
        add_action('admin_enqueue_scripts', array(get_class(), 'enqueue_common_admin_static_resources'));
        add_action('admin_footer_print_scripts', array(&$this, 'enqueue_common_admin_footer_static_resources'));

        // Provides menu options for managing NextGEN Settings
        add_action('admin_menu', array($this, 'add_menu_pages'), 999);

        // Add ngg-admin body class to all admin pages for styling
        add_filter( 'admin_body_class', array($this, 'add_ngg_body_class'));

        // Custom post types
        add_filter( 'admin_body_class', array($this, 'add_ngg_post_type_class'));
        add_filter( 'screen_options_show_screen', array($this, 'remove_post_type_screen_options'));
        add_action( 'all_admin_notices', array($this, 'custom_post_type_markup_top'), 11);
        add_action( 'admin_footer', array($this, 'custom_post_type_markup_bottom'));

        // Requirements need to be registered with the notification manager *before* it's serve_ajax_request()
        add_action('init', array(C_Admin_Requirements_Manager::get_instance(), 'create_notification'), -10);

        // Define routes
        add_action('ngg_routes', array($this, 'define_routes'));

        // Provides admin notices
        $notices = C_Admin_Notification_Manager::get_instance();
        add_action('init', array($notices, 'serve_ajax_request'));
        add_action('admin_footer', array($notices, 'enqueue_scripts'));
        add_action('do_ngg_notices', array($notices, 'render'));
        add_action('ngg_created_new_gallery', array($this, 'set_review_notice_flag'));
        add_action('ngg_created_new_gallery', get_class().'::update_gallery_count_setting');
        add_action('ngg_delete_gallery', get_class().'::update_gallery_count_setting');
        if (!self::is_ngg_legacy_page()) {
            add_action('all_admin_notices', get_class().'::emit_do_notices_action');
        }

        if (defined('PHP_VERSION_ID')) {
            $php_id = PHP_VERSION_ID;
        }
        else {
            $version = explode('.', PHP_VERSION);
            $php_id = ($version[0] * 10000 + $version[1] * 100 + $version[2]);
        }

        if ($php_id < 50300) {
            $notices->add(
                "ngg_php52_deprecation",
                array("message" => __('PHP 5.2 will be deprecated in a future version of NextGEN. Please upgrade your PHP installation to 5.3 or above.', 'nggallery'))
            );
        }

        // Add review notices
        $review_notice_1 = new C_Review_Notice(array(
                'name'    => 'review_level_1',
                'range'   => array('min' => 3, 'max' => 8),
                'follows' => '')
        );
        $review_notice_2 = new C_Review_Notice(array(
                'name'    => 'review_level_2',
                'range'   => array('min' => 10, 'max' => 18),
                'follows' => &$review_notice_1)
        );
        $review_notice_3 = new C_Review_Notice(array(
                'name'    => 'review_level_3',
                'range'   => array('min' => 20, 'max' => PHP_INT_MAX),
                'follows' => &$review_notice_2)
        );
        $notices->add($review_notice_1->get_name(), $review_notice_1);
        $notices->add($review_notice_2->get_name(), $review_notice_2);
        $notices->add($review_notice_3->get_name(), $review_notice_3);

        $notices->add('nextgen.beginner.gallery_creation_igw', 'C_NextGen_First_Run_Notification_Wizard');

        $notices->add('mailchimp_opt_in', 'C_Mailchimp_OptIn_Notice');
    }

    /**
     * Used to determine if the current request is for a NGG legacy page
     * @return bool
     */
    static function is_ngg_legacy_page()
    {
        return (
            is_admin() &&
            isset($_REQUEST['page']) &&
            in_array($_REQUEST['page'], array(
                'nggallery-manage-gallery',
                'nggallery-manage-album',
                'nggallery-tags',
                'manage-galleries'
            ))
        );
    }

    /**
     * Emits the 'do_ngg_notices' action
     * Used by the notification manager to render all notices
     */
    static function emit_do_notices_action()
    {
        if (!did_action('do_ngg_notices')) {
            do_action('do_ngg_notices');
        }
    }

    /**
     * We do not want to suddenly ask users for a review when they have upgraded. Instead we will wait for a new
     * gallery to be created and then will we also consider displaying reviews if the gallery count is within range.
     */
    function set_review_notice_flag()
    {
        $settings = C_NextGen_Settings::get_instance();
        if (!$settings->get('gallery_created_after_reviews_introduced'))
            $settings->set('gallery_created_after_reviews_introduced', TRUE);
        $settings->save();
    }

    /**
     * Review notifications are pegged to run only when the current gallery count is within a certain range. This
     * updates the 'gallery_count' setting when galleries have been created or deleted.
     */
    static function update_gallery_count_setting()
    {
        $settings = C_NextGen_Settings::get_instance();
        $mapper = C_Gallery_Mapper::get_instance();
        $original_cache_setting = $mapper->__use_cache;
        $mapper->_use_cache = FALSE;
        $gallery_count = C_Gallery_Mapper::get_instance()->count();
        $mapper->_use_cache = $original_cache_setting;
        $settings->set('gallery_count', $gallery_count);
        $settings->save();
        return $gallery_count;
    }

    function define_routes($router)
    {
        // TODO: Why is this in the nextgen-admin module? Shouldn't it be in the other options module?
        $router->create_app('/nextgen-settings')
            ->route('/update_watermark_preview', 'I_Settings_Manager_Controller#watermark_update');
    }

    function register_scripts()
    {
        $router = C_Router::get_instance();
        wp_register_script(
            'gritter',
            $router->get_static_url('photocrati-nextgen_admin#gritter/gritter.min.js'),
            array('jquery'),
            NGG_SCRIPT_VERSION
        );
        wp_register_style(
            'gritter',
            $router->get_static_url('photocrati-nextgen_admin#gritter/css/gritter.css'),
            array(),
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'ngg_progressbar',
            $router->get_static_url('photocrati-nextgen_admin#ngg_progressbar.js'),
            array('gritter'),
            NGG_SCRIPT_VERSION
        );
        wp_register_style(
            'ngg_progressbar',
            $router->get_static_url('photocrati-nextgen_admin#ngg_progressbar.css'),
            array('gritter'),
            NGG_SCRIPT_VERSION
        );
        wp_register_style(
            'ngg_select2',
            $router->get_static_url('photocrati-nextgen_admin#select2/select2.css'),
            array(),
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'ngg_select2',
            $router->get_static_url('photocrati-nextgen_admin#select2/select2.js'),
            array(),
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'jquery.nextgen_radio_toggle',
            $router->get_static_url('photocrati-nextgen_admin#jquery.nextgen_radio_toggle.js'),
            array('jquery'),
            NGG_SCRIPT_VERSION
        );
        wp_register_style(
            'ngg-jquery-ui',
            $router->get_static_url('photocrati-nextgen_admin#jquery-ui/jquery-ui-1.10.4.custom.css'),
            array(),
            '1.10.4'
        );
        wp_register_script(
            'iris',
            $router->get_url('/wp-admin/js/iris.min.js', FALSE, TRUE),
            array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch')
        );
        wp_register_script(
            'wp-color-picker',
            $router->get_url('/wp-admin/js/color-picker.js', FALSE, TRUE),
            array('iris')
        );

        wp_register_style(
            'nextgen_admin_css', 
            $router->get_static_url('photocrati-nextgen_admin#nextgen_admin_page.css'), 
            array('wp-color-picker'), 
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'nextgen_admin_js',
            $router->get_static_url('photocrati-nextgen_admin#nextgen_admin_page.js'),
            array('wp-color-picker'),
            NGG_SCRIPT_VERSION
        );

        // Style the parent menu icons for NextGEN-related pages
        wp_add_inline_style('wp-admin', '
            #adminmenu li.toplevel_page_nextgen-gallery img,
            #adminmenu li[class*=toplevel_page_nextgen-gallery] img,
            #adminmenu li[class*=toplevel_page_ngg] img {
                opacity: 1;
                max-width: 18px;
                padding-top: 7px;
            }
        ');
    }

    /**
     * Adds a common body class to all admin pages
     *
     * @param $classes
     *
     * @return string
     */
    function add_ngg_body_class( $classes )
    {
        return C_NextGen_Admin_Page_Manager::is_requested() ? "$classes ngg-admin" : $classes;
    }

    /**
     * Adds a common body class to all NGG post types pages
     *
     * @param $classes
     *
     * @return string
     */
    function add_ngg_post_type_class( $classes )
    {
        return C_NextGen_Admin_Page_Manager::is_requested_post_type() ? "$classes ngg-post-type" : $classes;
    }

    function remove_post_type_screen_options($show)
    {
        return C_NextGen_Admin_Page_Manager::is_requested_post_type() ? FALSE : $show;
    }

    /* Add common admin markup to top of custom post type pages */
	function custom_post_type_markup_top()
	{
        global $title;
        if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && !$this->is_ngg_post_type_with_template() ) {
            echo '<div id="ngg_page_content"><div class="ngg_page_content_header "><img src="' . C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png') . '"><h3>'. $title . '</h3></div><div class="ngg_page_content_main">';
        }
    }

    /* Add common admin markup to bottom of custom post type pages */
	function custom_post_type_markup_bottom()
	{
        if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && !$this->is_ngg_post_type_with_template() ) {
            echo '</div></div>';
        }
    }

    /* Conditional returns true if is post type and uses custom template */
    function is_ngg_post_type_with_template() 
    {
        $url = $_SERVER['REQUEST_URI'];
        if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && strpos($url, '&ngg_edit') ) {
            return TRUE;
        }
        return FALSE;
    }

    /* Add function to determine if block editor is in use */
    static function is_block_editor()
    {
        $is_block_editor = false;
        if (function_exists('get_current_screen')) {
            $current_screen = get_current_screen();
            if (is_object($current_screen)) {
                if (method_exists($current_screen, 'is_block_editor'))
                    $is_block_editor = $current_screen->is_block_editor();
                
                if (!$is_block_editor) {
                    if ($current_screen->action == 'add')
                        $is_block_editor = function_exists('use_block_editor_for_post_type') && use_block_editor_for_post_type($current_screen->post_type);
                    else
                        $is_block_editor = function_exists('use_block_editor_for_post') && use_block_editor_for_post(null);
                }
                
                if (!$is_block_editor) {
                    if ($current_screen->action == 'add')
                        $is_block_editor = !isset($_GET['classic-editor']) && function_exists('gutenberg_can_edit_post_type') && gutenberg_can_edit_post_type($current_screen->post_type);
                    else
                        $is_block_editor = !isset($_GET['classic-editor']) && function_exists('gutenberg_can_edit_post') && gutenberg_can_edit_post(null);
                }
            }
        }
        
        if (!$is_block_editor)
            $is_block_editor = function_exists('is_gutenberg_page') && is_gutenberg_page();

        return $is_block_editor;
    }

    function init_wizards()
    {
        $wizards = C_NextGEN_Wizard_Manager::get_instance();
        $wizards->set_starter(__('Do you need help with NextGEN?', 'nggallery'));
        $wizards->set_active(false);
        
        /* 
         * This is the new wizard sequence that helps users both crerate a gallery
         * and add it to a page.
         */
        $wizard = $wizards->add_wizard('nextgen.beginner.gallery_creation_igw');
        $wizard->add_step('start');
        $wizard->set_step_text('start', __('Hello, this wizard will guide you through creating a NextGEN gallery and inserting it into a page. Click "Next step" to proceed.', 'nggallery'));
        $wizard->add_step('pages_menu');
        $wizard->set_step_text('pages_menu', __('Click on "Pages" to access your WordPress pages.', 'nggallery'));
        $wizard->set_step_target('pages_menu', '#menu-pages a.menu-top', 'right center', 'left center');
        $wizard->set_step_view('pages_menu', '#menu-pages a.menu-top');
        $wizard->add_step('add_page_menu');
        $wizard->set_step_text('add_page_menu', __('Click "Add New" to create a new page.', 'nggallery'));
        $wizard->set_step_target('add_page_menu', '#menu-pages a[href*="post-new.php?post_type=page"]', 'right center', 'left center');
        $wizard->set_step_view('add_page_menu', '#menu-pages a[href*="post-new.php?post_type=page"]');
        
        if ($this->is_block_editor())
        {
            // Input page title
            $wizard->add_step('input_page_title');
            $wizard->set_step_text('input_page_title', __('Type in a title for your page.', 'nggallery'));
            $wizard->set_step_target('input_page_title', '.editor-post-title__input', 'bottom center', 'top center');
            $wizard->set_step_view('input_page_title', '.editor-post-title__input');

            // Add NextGen block
            $wizard->add_step('add_block');
            $wizard->set_step_text('add_block', __('Now click the button to insert a block.', 'nggallery'));
            $wizard->set_step_target('add_block', 'button.block-editor-inserter__toggle', 'right center', 'left center');
            $wizard->set_step_view('add_block', 'button.block-editor-inserter__toggle');
            $wizard->set_step_lazy('add_block', true);

            // Search for NextGen block
            $wizard->add_step('search_nextgen');
            $wizard->set_step_text('search_nextgen', __('Type "nextgen" to search for the NextGEN block.', 'nggallery'));
            $wizard->set_step_target( 'search_nextgen', 'input.block-editor-inserter__search', 'right center', 'left center');
            $wizard->set_step_view( 'search_nextgen', 'input.block-editor-inserter__search');
            $wizard->set_step_lazy('search_nextgen', true);

            // Select NextGen block
            $wizard->add_step('add_ngg_block');
            $wizard->set_step_text( 'add_ngg_block', __('Click on the NextGEN block to add it.', 'nggallery'));
            $wizard->set_step_target( 'add_ngg_block', 'button.editor-block-list-item-imagely-nextgen-gallery', 'right center', 'left center');
            $wizard->set_step_view( 'add_ngg_block', 'button.editor-block-list-item-imagely-nextgen-gallery');
            $wizard->set_step_lazy('add_ngg_block', true);
            $wizard->set_step_condition( 'add_ngg_block', 'wait', '1500');

            // Insert a NextGen Gallery
            $wizard->add_step('add-ngg-gallery');
            $wizard->set_step_text( 'add-ngg-gallery', __('Now click the "Add Gallery" button to open NextGEN\'s Insert Gallery Window.', 'nggallery'));
            $wizard->set_step_target( 'add-ngg-gallery', '.add-ngg-gallery', 'bottom center', 'top center');
            $wizard->set_step_view( 'add-ngg-gallery', '.add-ngg-gallery');
            $wizard->set_step_lazy('add-ngg-gallery', true);            
        }
        else {
            $wizard->add_step('input_page_title');
            $wizard->set_step_text('input_page_title', __('Type in a title for your page.', 'nggallery'));
            $wizard->set_step_target('input_page_title', 'input#title', 'bottom center', 'top center');
            $wizard->set_step_view('input_page_title', 'input#title');
            
            $wizard->add_step('add_gallery_button');
            $wizard->set_step_text('add_gallery_button', __('Now click the "Add Gallery" button to open NextGEN\'s Insert Gallery Window (IGW).', 'nggallery'));
            $wizard->set_step_target('add_gallery_button', 'a#ngg-media-button', 'right center', 'left center');
            $wizard->set_step_view('add_gallery_button', 'a#ngg-media-button');
        }
        
        $wizard->add_step('add_gallery_tab');
        $wizard->set_step_text('add_gallery_tab', __('Now click the "Upload Images" tab to add a new gallery.', 'nggallery'));
        $wizard->set_step_target('add_gallery_tab', '#attach_to_post_tabs a#ui-id-2', 'right center', 'left center');
        $wizard->set_step_view('add_gallery_tab', '#attach_to_post_tabs a#ui-id-2');
        $wizard->set_step_context('add_gallery_tab', 'iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]');
        $wizard->set_step_lazy('add_gallery_tab', true);
        $wizard->set_step_condition('add_gallery_tab', 'nextgen_event', 'plupload_init', null, 10000);
        $wizard->add_step('input_gallery_name');
        $wizard->set_step_text('input_gallery_name', __('Select a name for your gallery.', 'nggallery'));
        $wizard->set_step_target('input_gallery_name', 'input#gallery_name:visible', 'bottom center', 'top center');
        $wizard->set_step_view('input_gallery_name', 'input#gallery_name');
        $wizard->set_step_context('input_gallery_name', array('iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]', 'iframe#ngg-iframe-create_tab'));
        $wizard->set_step_lazy('input_gallery_name', true);
        // $wizard->set_step_condition('input_gallery_name', 'nextgen_event', 'plupload_init', null, 10000);
        $wizard->add_step('select_images');
        $wizard->set_step_text('select_images', __('Now click the "Add Files" button and select some images to add to the gallery.', 'nggallery'));
        $wizard->set_step_target('select_images', 'a#uploader_browse', 'bottom center', 'top center');
        $wizard->set_step_view('select_images', 'a#uploader_browse');
        $wizard->set_step_context('select_images', array('iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]', 'iframe#ngg-iframe-create_tab'));
        $wizard->set_step_lazy('select_images', true);
        $wizard->add_step('upload_images');
        $wizard->set_step_text('upload_images', __('Now click the "Start Upload" button to begin the upload process.', 'nggallery'));
        $wizard->set_step_target('upload_images', 'a#uploader_upload', 'bottom center', 'top center');
        $wizard->set_step_view('upload_images', 'a#uploader_upload');
        $wizard->set_step_context('upload_images', array('iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]', 'iframe#ngg-iframe-create_tab'));
        $wizard->set_step_lazy('upload_images', true);
        $wizard->set_step_condition('upload_images', 'plupload_bind', 'UploadComplete', array('iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]', 'iframe#ngg-iframe-create_tab', '#uploader'));
        $wizard->add_step('display_gallery_tab');
        $wizard->set_step_text('display_gallery_tab', __('Congratulations! You just created your first gallery. Now let\'s insert it into the page. Click the "Insert into Page" tab.', 'nggallery'));
        $wizard->set_step_target('display_gallery_tab', '#attach_to_post_tabs a#ui-id-1', 'right center', 'left center');
        $wizard->set_step_view('display_gallery_tab', '#attach_to_post_tabs a#ui-id-1');
        $wizard->set_step_context('display_gallery_tab', 'iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]');
        $wizard->set_step_lazy('display_gallery_tab', true);
        $wizard->set_step_condition('display_gallery_tab', 'wait', '500');
        $wizard->add_step('display_type_select');
        $wizard->set_step_text('display_type_select', __('Click on the "NextGEN Basic Slideshow" radio button to select the display type for the gallery.', 'nggallery'));
        $wizard->set_step_target('display_type_select', '.display_type_preview input[value="photocrati-nextgen_basic_slideshow"]', 'bottom center', 'top center');
        $wizard->set_step_view('display_type_select', '.display_type_preview input[type="radio"]');
        $wizard->set_step_context('display_type_select', 'iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]');
        $wizard->set_step_lazy('display_type_select', true);
        $wizard->add_step('source_select');
        $wizard->set_step_text('source_select', __('Now click inside the "Galleries" field and select your gallery.', 'nggallery'));
        $wizard->set_step_target('source_select', '#source_configuration .galleries_column .select2-container input', 'right center', 'left center');
        $wizard->set_step_view('source_select', '#source_configuration .galleries_column select');
        $wizard->set_step_context('source_select', 'iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]');
        $wizard->set_step_lazy('source_select', true);
        $wizard->add_step('insert_gallery');
        $wizard->set_step_text('insert_gallery', __('Now click on the "Insert Gallery" button to insert the gallery in your page.', 'nggallery'));
        $wizard->set_step_target('insert_gallery', '#displayed_tab #save_displayed_gallery', 'top center', 'bottom left');
        $wizard->set_step_view('insert_gallery', '#displayed_tab #save_displayed_gallery');
        $wizard->set_step_context('insert_gallery', 'iframe[src*="' . NGG_ATTACH_TO_POST_SLUG . '"]');
        $wizard->set_step_lazy('insert_gallery', true);
        $wizard->set_step_condition('insert_gallery', 'wait', '1000');
        $wizard->add_step('finish');
        $wizard->set_step_text('finish', __('Congratulations! You just created your first gallery. You can now click the "Publish" button on the right to publish your page.', 'nggallery'));
    }
    
    function handle_wizards()
    {
        $wizards = C_NextGEN_Wizard_Manager::get_instance();

        // adjust wizards state based on query/parameters
        $wizards->handle_wizard_query();
        
        global $ngg_fs;
        // make sure we don't trigger the wizards if NGG Fremius is running or this is an AJAX request
        if (isset($_REQUEST['ngg_dismiss_notice'])
        ||  (is_admin()
            //  && C_NextGen_Admin_Page_Manager::is_requested()
             && !M_Attach_To_Post::is_atp_url()
             && !isset($_REQUEST['attach_to_post'])
             && (!isset($ngg_fs) || !$ngg_fs->is_activation_mode())
             && (!defined('DOING_AJAX') || !DOING_AJAX)))
        {
            $wizards->set_active(true);
        }
        
        // before adding notices or activating individual wizards, ensure wizards are globally enabled and no wizard is currently running already
        if ($wizards->is_active() && $wizards->get_running_wizard() == null)
        {
            // add notice for gallery creation wizard
            $wizard = $wizards->get_wizard('nextgen.beginner.gallery_creation_igw');

            // enable gallery wizard / notification under specific conditions
            $is_ngg_overview_page = isset($_GET['page']) && $_GET['page'] == 'nextgen-gallery';
            $is_ecom_instructions_page = isset($_GET['page']) && $_GET['page'] == 'ngg-ecommerce-instructions-page';
            if (!$wizard->is_completed() && !$wizard->is_cancelled() && !$is_ecom_instructions_page) {
                $mapper = C_Gallery_Mapper::get_instance();
                if ($mapper->count() == 0)
                {
                    $wizard->set_active(true);
                    // This notification was registered earlier; set_wizard() here makes the is_renderable() return TRUE
                    C_NextGen_First_Run_Notification_Wizard::set_wizard($wizard);
                }
            }
            if ($is_ngg_overview_page)
                $wizard->set_active(true);
        }
    }
    
    function enqueue_wizard_components()
    {
        $router = C_Router::get_instance();
        
        // Wizards related scripts/styles
        wp_register_style(
            'bootstrap-tooltip',
            $router->get_static_url('photocrati-nextgen_admin#bootstrap/css/bootstrap-tooltip.css'),
            array(),
            NGG_SCRIPT_VERSION
        );
        
        wp_register_script(
            'tourist',
            $router->get_static_url('photocrati-nextgen_admin#tourist/tourist.js'),
            array('jquery', 'backbone'),
            NGG_SCRIPT_VERSION
        );
        wp_register_style(
            'tourist',
            $router->get_static_url('photocrati-nextgen_admin#tourist/tourist.css'),
            array('bootstrap-tooltip'),
            NGG_SCRIPT_VERSION
        );
        wp_register_script(
            'ngg-wizards',
            $router->get_static_url('photocrati-nextgen_admin#nextgen_wizards.js'),
            array('tourist'),
            NGG_SCRIPT_VERSION,
            true
        );
        wp_register_style(
            'ngg-wizards',
            $router->get_static_url('photocrati-nextgen_admin#nextgen_wizards.css'),
            array('tourist'),
            NGG_SCRIPT_VERSION
        );
        
        $wizards = C_NextGEN_Wizard_Manager::get_instance();
        $wizard = $wizards->get_next_wizard();

        if ($wizards->is_active() && $wizard != null) {
            $data = array();
            $data['starter'] = array('text' => $wizards->get_starter(), 'image' => $router->get_static_url('photocrati-nextgen_admin#imagely_icon.png'));
            $running_wizard = $wizards->get_running_wizard();
            $data['running_wizard'] = $running_wizard != null ? $running_wizard->get_id() : null;
            $data['wizard_list'] = array($wizard->toData());
            wp_localize_script('ngg-wizards', 'NextGEN_Wizard_Manager_State', $data);
            if (method_exists('M_Gallery_Display', 'enqueue_fontawesome'))
                M_Gallery_Display::enqueue_fontawesome();

            wp_enqueue_style('ngg-wizards');
            wp_enqueue_script('ngg-wizards');
        };
    }

    /**
     * Adds menu pages to manage NextGen Settings
     * @uses action: admin_menu
     */
    function add_menu_pages()
    {
        C_NextGen_Admin_Page_Manager::get_instance()->setup();
    }

    public function handle_cache_flush_requests()
    {
        if (isset($_REQUEST['ngg_flush']) && current_user_can('administrator'))
        {
            C_Photocrati_Transient_Manager::flush();
            die("Flushed all caches");
        }

        if (isset($_REQUEST['ngg_flush_expired']) && current_user_can('administrator'))
        {
            C_Photocrati_Transient_Manager::get_instance()->flush_expired();
            die("Flushed all expired caches");
        }
    }

    function get_type_list()
    {
        return array(
            'A_Fs_Access_Page'                        => 'adapter.fs_access_page.php',
            'A_MVC_Validation'                        => 'adapter.mvc_validation.php',
            'A_Nextgen_Admin_Default_Pages'           => 'adapter.nextgen_admin_default_pages.php',
            'A_Nextgen_Settings_Routes'               => 'adapter.nextgen_settings_routes.php',
            'C_Admin_Notification_Manager'            => 'class.admin_notification_manager.php',
            'C_Admin_Requirements_Manager'            => 'class.admin_requirements_manager.php',
            'C_Form'                                  => 'class.form.php',
            'C_Form_Manager'                          => 'class.form_manager.php',
            'C_Mailchimp_OptIn_Notice'                => 'class.mailchimp_optin_notice.php',
            'C_NextGEN_Wizard_Manager'                => 'class.nextgen_wizard_manager.php',
            'C_NextGen_Admin_Page_Manager'            => 'class.nextgen_admin_page_manager.php',
            'C_NextGen_First_Run_Notification_Wizard' => 'class.nextgen_first_run_notification_wizard.php',
            'C_Nextgen_Admin_Installer'               => 'class.nextgen_admin_installer.php',
            'C_Nextgen_Admin_Page_Controller'         => 'class.nextgen_admin_page_controller.php'
        );
    }
}

class C_NextGen_Admin_Installer
{
    function install()
    {
        $settings = C_NextGen_Settings::get_instance();

        // In version 0.2 of this module and earlier, the following values
        // were statically set rather than dynamically using a handler. Therefore, we need
        // to delete those static values
        $module_name = 'photocrati-nextgen_admin';
        $modules = get_option('pope_module_list', array());
        if (!$modules) {
            $modules = $settings->get('pope_module_list', array());
        }

        $cleanup = FALSE;
        foreach ($modules as $module) {
            if (strpos($module, $module_name) !== FALSE) {
                // Leave $module as-is: inside version_compare() will warn about passing items by reference
                $module = explode('|', $module);
                $val    = array_pop($module);
                if (version_compare($val, '0.3') == -1) {
                    $cleanup = TRUE;
                }
                break;
            }
        }

        if ($cleanup) {
            $keys = array(
                'jquery_ui_theme',
                'jquery_ui_theme_version',
                'jquery_ui_theme_url'
            );
            foreach ($keys as $key) $settings->delete($key);
        }
    }
}

class C_NextGen_Admin_Option_Handler
{
    function get_router()
    {
        return C_Router::get_instance();
    }

    function get($key, $default=NULL)
    {
        $retval = $default;

        switch ($key) {
            case 'jquery_ui_theme':
                $retval = 'jquery-ui-nextgen';
                break;
            case 'jquery_ui_theme_version':
                $retval = '1.8';
                break;
            case 'jquery_ui_theme_url':
                $retval = $this->get_router()->get_static_url('photocrati-nextgen_admin#jquery-ui/jquery-ui-1.10.4.custom.css');
                break;
        }

        return $retval;
    }
}

new M_NextGen_Admin();