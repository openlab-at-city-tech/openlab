<?php
defined('ABSPATH') || die;

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
     * Check if widget blocks exists in current user role through advgb_blocks_user_roles option,
     * either in inactive_blocks or active_blocks array.
     * https://github.com/publishpress/PublishPress-Blocks/issues/756#issuecomment-932358037
     *
     * @since 2.11.0
     * @since 3.1.4.2 - Added support for core/widget-group block
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

        $widget_blocks = [
            'core/legacy-widget',
            'core/widget-group'
        ];

        global $wp_version;
        global $pagenow;
        if( ( $pagenow === 'widgets.php' || $pagenow === 'customize.php' ) && $wp_version >= 5.8 ) {

            $advgb_blocks_list          = get_option( 'advgb_blocks_list' ) && !empty( get_option( 'advgb_blocks_list' ) ) ? get_option( 'advgb_blocks_list' ) : [];
            $advgb_blocks_user_roles    = get_option( 'advgb_blocks_user_roles' ) && !empty( get_option( 'advgb_blocks_user_roles' ) ) ? get_option( 'advgb_blocks_user_roles' ) : [];
            $current_user               = wp_get_current_user();
            $current_user_role          = $current_user->roles[0];

            if( count( $advgb_blocks_list ) && count( $advgb_blocks_user_roles ) ) {

                foreach( $widget_blocks as $item ) {
                    if( is_array( $advgb_blocks_user_roles[$current_user_role]['active_blocks'] )
                        && is_array($advgb_blocks_user_roles[$current_user_role]['inactive_blocks'] )
                        && ! in_array( $item, $advgb_blocks_user_roles[$current_user_role]['active_blocks'] )
                        && ! in_array( $item, $advgb_blocks_user_roles[$current_user_role]['inactive_blocks'] )
                        && ! empty( $current_user_role )
                    ) {
                        array_push(
                            $advgb_blocks_user_roles[$current_user_role]['active_blocks'],
                            $item
                        );
                        update_option( 'advgb_blocks_user_roles', $advgb_blocks_user_roles, false );
                    }
                }
            }
        }
    }
}
add_action( 'admin_init', 'advg_check_legacy_widget_block_init' );
