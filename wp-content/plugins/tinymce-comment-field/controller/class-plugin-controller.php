<?php

class TMCECF_PluginController {

    private function __construct() {
        register_activation_hook(TMCECF_PLUGIN_FILE, array(&$this, 'activate'));
        register_deactivation_hook(TMCECF_PLUGIN_FILE, array(&$this, 'deactivate'));

        add_action('init', array(&$this, 'load_textdomain'));
        add_action('admin_init', array(&$this, 'upgrade'));
        add_filter('plugin_action_links_' . TMCECF_PLUGIN, array(&$this, 'action_links'));
        add_action('activated_plugin', array(&$this, 'activated'), 10, 1);
        add_action('admin_notices', array(&$this, 'check_compatibility'));
        add_action('admin_init', array(&$this, 'handle_ignore_compatibility_issue'));
        add_action('tgmpa_register', array(&$this, 'handle_required_plugins'));

        TMCECF_TitanController::init();
        TMCECF_MetaBoxController::init();
        TMCECF_EditorController::init();
        TMCECF_CommentController::init();
        TMCECF_DummyController::init();
    }


    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }


    public function upgrade() {

        if (!TMCECF_PluginManager::isTitanEnabled()) {
            return;
        }

        $plugin_data = get_plugin_data(TMCECF_PLUGIN_FILE, false);
        $version = (float)$plugin_data['Version'];
        $current_version = (float)get_option('tinymce-comment-field_version');

        if ($version > $current_version) {

            switch ($current_version) {
                case 0:
                case 0.9:
                    $titan = TitanFramework::getInstance('tinymce-comment-field');
                    $buttons = $titan->getOption('buttons');
                    $shortcodes = $titan->getOption('allowed-shortcodes');
                    $install_timestamp = get_option('tinymce-comment-field_install-timestamp');

                    if (!$install_timestamp) {
                        update_option('tinymce-comment-field_install-timestamp', time());
                    }

                    $shortcodes_to_remove = array('wp_caption', 'caption', 'gallery');
                    $shortcodes = array_diff($shortcodes, $shortcodes_to_remove);

                    if (in_array('image', $buttons, true)) {
                        $image = array('image');
                        $buttons = array_diff($buttons, $image);
                        $titan->setOption('buttons', $buttons);

                        //set new options
                        $titan->setOption('allow_images_as_tag', true);
                        $shortcodes = array_merge($shortcodes, $shortcodes_to_remove);
                    }

                    $titan->setOption('allowed-shortcodes', $shortcodes);
                    break;
                case 1.0:
                    break;
                default:
                    break;
            }

            update_option('tinymce-comment-field_version', $version);
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain('tinymce-comment-field', true, TMCECF_PLUGIN_RELATIVE_DIR . '/languages/');
    }

    public function action_links($links) {
        $plugin_links = array('<a href="' . admin_url('admin.php?page=tinymce-comment-field') . '">' . __('Settings', 'tinymce-comment-field') . '</a>',
            '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B2WSC5FR2L8MU">' . __('Donate', 'tinymce-comment-field') . '</a>',);
        return array_merge($plugin_links, $links);
    }

    public function check_compatibility() {
        $ignore_compatibility_issue_option = get_option('tinymce-comment-field_ignore_compatibility_issue');
        $ignore_compatibility_issue = !empty($ignore_compatibility_issue_option) && $ignore_compatibility_issue_option === 'true';

        if (!$ignore_compatibility_issue && class_exists('Jetpack') && Jetpack::is_module_active('comments') && current_user_can('manage_options')) :
            echo '<div class="error"><p>';
            /** @noinspection HtmlUnknownTarget */
            printf(__('Jetpack Comments are activated. TinyMCE Comment Field won\'t work with Jetpack Comments activated. Please <a href="%2$s">deactivate</a> Jetpack Comments in order to work with TinyMCE Comment Field | <a href="%1$s">Dismiss</a>', 'tinymce-comment-field'), '?tmcecf_ignore_compatibility_issue=1', admin_url('admin.php?page=jetpack_modules'));
            echo '</p></div>';
        endif;
    }

    public function handle_ignore_compatibility_issue() {
        $tmcecf_ignore_compatibility_issue = filter_input(INPUT_GET, 'tmcecf_ignore_compatibility_issue', FILTER_SANITIZE_NUMBER_INT);

        if (!empty($tmcecf_ignore_compatibility_issue) && (int)$tmcecf_ignore_compatibility_issue === 1 && current_user_can('manage_options')) {
            add_option('tinymce-comment-field_ignore_compatibility_issue', 'true');
        }
    }

    public function handle_required_plugins() {
        $plugins = array(array('name' => 'Titan Framework', 'slug' => 'titan-framework', 'required' => true,
            'version' => '1.7.4',
            'force_activation' => true,
            'force_deactivation' => false, 'external_url' => '',
        ),);
        $config = array('default_path' => '', 'menu' => 'tmcecf-install-plugins', 'has_notices' => true,
            'dismissable' => false, 'is_automatic' => true, 'message' => '',
            'strings' => array('page_title' => __('Install Required Plugins', 'tinymce-comment-field'),
                'menu_title' => __('Install Plugins', 'tinymce-comment-field'),
                'installing' => __('Installing Plugin: %s', 'tinymce-comment-field'),
                // %s = plugin name.
                'oops' => __('Something went wrong with the plugin API.', 'tinymce-comment-field'),
                'notice_can_install_required' => _n_noop('TinyMCE Comment Field requires the following plugin: %1$s.', 'TinyMCE Comment Field requires the following plugins: %1$s.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'notice_can_install_recommended' => _n_noop('TinyMCE Comment Field recommends the following plugin: %1$s.', 'TinyMCE Comment Field recommends the following plugins: %1$s.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.'),
                // %1$s = plugin name(s).
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.'),
                // %1$s = plugin name(s).
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this TinyMCE Comment Field: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'tinymce-comment-field'),
                // %1$s = plugin name(s).
                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', 'tinymce-comment-field'),
                'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', 'tinymce-comment-field'),
                'return' => __('Return to Required Plugins Installer', 'tinymce-comment-field'),
                'plugin_activated' => __('Plugin activated successfully.', 'tinymce-comment-field'),
                'complete' => __('All plugins installed and activated successfully. %s', 'tinymce-comment-field'),
                // %s = dashboard link.
                'nag_type' => 'updated'
                // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            ));

        tgmpa($plugins, $config);
    }

    public function activated($plugin) {
        if ($plugin === TMCECF_PLUGIN || $plugin === 'titan-framework/titan-framework.php'):
            TMCECF_TitanController::save_editor_content_css();
        endif;
    }

    public function activate() {
        update_option('tinymce-comment-field_version', 1.0);
        update_option('tinymce-comment-field_install-timestamp', time());
    }

    public function deactivate() {
        delete_option('tinymce-comment-field_ignore_compatibility_issue');
    }
}