<?php
defined('ABSPATH') || die;

//Check plugin requirements
if (version_compare(PHP_VERSION, '5.6.20', '<')) {
    if (! function_exists('advgb_disable_plugin')) {
        /**
         * Disable plugin
         *
         * @return void
         */
        function advgb_disable_plugin()
        {
            if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(__FILE__);
                unset($_GET['activate']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
            }
        }
    }

    if (! function_exists('advgb_show_error')) {
        /**
         * Show error
         *
         * @return void
         */
        function advgb_show_error()
        {
            echo '<div class="error"><p><strong>PublishPress Blocks</strong> needs at least PHP 5.6.20 version, please update php before installing the plugin.</p></div>';
        }
    }

    //Add actions
    add_action('admin_init', 'advgb_disable_plugin');
    add_action('admin_notices', 'advgb_show_error');

    //Do not load anything more
    return;
}

if (!defined('GUTENBERG_VERSION_REQUIRED')) {
    define('GUTENBERG_VERSION_REQUIRED', '5.7.0');
}

require_once(plugin_dir_path(__FILE__) . '/install.php');
require_once(plugin_dir_path(__FILE__) . '/incl/advanced-gutenberg-main.php');
new AdvancedGutenbergMain();

if (! function_exists('advg_language_domain_init')) {
    /**
     * Load language translations
     *
     * @return void
     */
    function advg_language_domain_init()
    {
        // First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
        unload_textdomain('advanced-gutenberg');

        // Load override language file first if available from version 2.3.11 and older
        if (file_exists(WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo')) {
            load_textdomain(
                'advanced-gutenberg',
                WP_LANG_DIR . '/plugins/' . 'advanced-gutenberg' . '-' . get_locale() . '.override.mo'
            );
        }

        // Call the core translations from plugins languages/ folder
        if (file_exists(plugin_dir_path(__FILE__) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo')) {
            load_textdomain(
                'advanced-gutenberg',
                plugin_dir_path(__FILE__) . 'languages/' . 'advanced-gutenberg' . '-' . get_locale() . '.mo'
            );
        }

        wp_set_script_translations(
            'editor',
            'advanced-gutenberg',
            plugin_dir_path( __FILE__ ) . 'languages'
        );
    }
}
add_action( 'init', 'advg_language_domain_init' );
