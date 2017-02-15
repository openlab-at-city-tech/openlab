<?php

class TMCECF_DummyController {

    private function __construct() {
        add_action('admin_menu', array(&$this, 'add_dummy_menu'));
    }
    
    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function add_dummy_menu() {
        if (!TMCECF_PluginManager::isTitanEnabled()):
            add_menu_page('TinyMCE Comment Field', 'TinyMCE Comment Field', 'manage_options', 'tinymce-comment-field', array(&$this, 'dummy'), 'dashicons-edit');
        endif;
    }

    public function dummy() {
        /** @noinspection PhpIncludeInspection */
        require_once(TMCECF_PLUGIN_DIR . 'views/dummy.php');
    }
}
