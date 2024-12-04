<?php

namespace SuperbThemesThemeInformationContent\ThemePage;

defined('ABSPATH') || exit();

class ThemePageController
{
    private static $meta_key = false;
    private static $theme_page_data = false;

    public static function init($options)
    {
        self::$theme_page_data = array(
            'theme_url' => isset($options['theme_url']) ? $options['theme_url'] : false,
            'demo_url' => isset($options['demo_url']) ? $options['demo_url'] : false,
            'features' => isset($options['features']) ? $options['features'] : false,
        );

        self::$meta_key = get_stylesheet() . '_themepage_seen';
        add_action('admin_menu', array(__CLASS__, 'ThemePageSubMenu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'EnqueueScripts'));
    }

    private static function GetPageSlug()
    {
        return get_stylesheet() . '-information';
    }

    private static function GetMetaKey()
    {
        return self::$meta_key;
    }

    public static function ThemePageSubMenu()
    {
        $awaiting = !get_user_meta(get_current_user_id(), self::GetMetaKey(), true) ? ' <span class="awaiting-mod">1</span>' : '';
        add_submenu_page('themes.php', __('Theme Settings', 'simple-nova'), __('Theme Settings', 'simple-nova') . $awaiting, 'manage_options', self::GetPageSlug(), array(__CLASS__, 'ThemePageContent'), 1);
    }

    public static function ThemePageContent()
    {
        update_user_meta(get_current_user_id(), self::GetMetaKey(), true);
        new ThemePageTemplate(self::$theme_page_data);
    }

    public static function EnqueueScripts($hook)
    {
        if ('appearance_page_' . self::GetPageSlug() != $hook) {
            return;
        }
        wp_enqueue_style(self::GetPageSlug(), get_template_directory_uri() . '/inc/superbthemes-info-content/theme-page/themepage.css');
    }

    public static function Cleanup()
    {
        delete_user_meta(get_current_user_id(), self::GetMetaKey());
    }
}
