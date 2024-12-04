<?php

namespace SuperbThemesThemeInformationContent\Templates;

use SuperbThemesThemeInformationContent\ThemeEntryPoint;

defined('ABSPATH') || exit();

class TemplateInformationController
{
    private static $ThemeLink = false;

    public static function init($options)
    {
        self::$ThemeLink = isset($options['theme_url']) ? $options['theme_url'] : false;
        add_action('enqueue_block_editor_assets', array(__CLASS__, 'InformationContent'));

        if (!isset($options['templates']) || !is_array($options['templates'])) {
            return;
        }

        $templates = $options['templates'];

        add_filter('superbthemes_available_page_templates', function () use ($templates) {
            // Available page templates in this theme
            foreach ($templates as &$template) {
                $template['image'] = get_stylesheet_directory_uri() . '/inc/superbthemes-info-assets/' . $template['image'];
            }

            return $templates;
        }, PHP_INT_MAX);
    }


    public static function InformationContent()
    {
        if (!self::$ThemeLink) {
            return;
        }
        wp_enqueue_script(get_stylesheet() . '-info', get_template_directory_uri() . '/inc/superbthemes-info-content/template-information/information.js', array('jquery'), ThemeEntryPoint::Version, true);
        wp_enqueue_style(get_stylesheet() . '-info', get_template_directory_uri() . '/inc/superbthemes-info-content/template-information/information.css', array(), ThemeEntryPoint::Version);
        add_action('admin_footer', function () {
            $theme = wp_get_theme();
            $text = is_child_theme() ? sprintf(__("Unlock all features by upgrading to the premium edition of %s and its parent theme %s.", 'simple-nova'), $theme, wp_get_theme($theme->Template)) : sprintf(__("Unlock all features by upgrading to the premium edition of %s.", 'simple-nova'), $theme);
            ob_start();
?>
            <div class="superbthemes-js-information-wrapper">
                <div class="superbthemes-js-information-item">
                    <img width="25" height="25" src="<?php echo esc_url(get_template_directory_uri() . '/inc/superbthemes-info-content/icons/color-crown.svg'); ?>" />
                    <div class="superbthemes-js-information-item-header"><?php esc_html_e("Upgrade to premium", 'simple-nova'); ?></div>
                    <div class="superbthemes-js-information-item-content">
                        <p><?php echo esc_html($text); ?></p>
                        <a href="<?php echo esc_url(self::$ThemeLink); ?>" target="_blank" class="button button-primary"><?php esc_html_e("View Premium Version", 'simple-nova'); ?></a>
                    </div>
                </div>
            </div>
<?php
            $template = ob_get_clean();
            echo '<script type="text/template" id="tmpl-superbthemes-js-information-wrapper">' . $template . '</script>';
        });
    }
}
