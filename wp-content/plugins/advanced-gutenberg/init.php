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
require_once(plugin_dir_path(__FILE__) . '/incl/utilities-main.php');
require_once(plugin_dir_path(__FILE__) . '/incl/block-settings-main.php');
require_once(plugin_dir_path(__FILE__) . '/incl/block-controls-main.php');
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

if (! function_exists('advg_check_legacy_widget_block_init')) {
    /**
     * v2.11.0 - Check if core/legacy-widget exists in current user role through advgb_blocks_user_roles option,
     * either in inactive_blocks or active_blocks array.
     * https://github.com/publishpress/PublishPress-Blocks/issues/756#issuecomment-932358037
     *
     * This function can be used in future to add new blocks not available on widgets.php
     *
     * @return void
     */
    function advg_check_legacy_widget_block_init()
    {
        if(!current_user_can('edit_theme_options')) {
            return false;
        }

        global $wp_version;
        global $pagenow;
        if( ( $pagenow === 'widgets.php' || $pagenow === 'customize.php' ) && $wp_version >= 5.8 ) {

            $advgb_blocks_list          = get_option( 'advgb_blocks_list' ) && !empty( get_option( 'advgb_blocks_list' ) ) ? get_option( 'advgb_blocks_list' ) : [];
            $advgb_blocks_user_roles    = get_option( 'advgb_blocks_user_roles' ) && !empty( get_option( 'advgb_blocks_user_roles' ) ) ? get_option( 'advgb_blocks_user_roles' ) : [];
            $current_user               = wp_get_current_user();
            $current_user_role          = $current_user->roles[0];

            if( count( $advgb_blocks_list ) && count( $advgb_blocks_user_roles ) ) {

                if(
                    is_array($advgb_blocks_user_roles[$current_user_role]['active_blocks'])
                    && is_array($advgb_blocks_user_roles[$current_user_role]['inactive_blocks'])
                    && !in_array( 'core/legacy-widget', $advgb_blocks_user_roles[$current_user_role]['active_blocks'] )
                    && !in_array( 'core/legacy-widget', $advgb_blocks_user_roles[$current_user_role]['inactive_blocks'] )
                    && !empty( $current_user_role )
                ) {

                    array_push(
                        $advgb_blocks_user_roles[$current_user_role]['active_blocks'],
                        'core/legacy-widget'
                    );
                    update_option( 'advgb_blocks_user_roles', $advgb_blocks_user_roles, false );
                }
            }
        }
    }
}
add_action( 'admin_init', 'advg_check_legacy_widget_block_init' );
