<?php

namespace TheLion\Integrations\Divi;

if (!defined('USEYOURDRIVE_VERSION')) {
    return;
}

class UseyourDrive extends \ET_Builder_Module
{
    public $slug = 'wpcp_useyourdrive';
    public $vb_support = 'on';
    public $use_raw_content = true;

    protected $module_credits = [
        'module_uri' => 'https://wpcloudplugins.com',
        'author' => 'WP Cloud Plugins',
        'author_uri' => 'https://wpcloudplugins.com',
    ];

    public function init()
    {
        $this->name = 'Google Drive Module';

        $this->settings_modal_toggles = [
            'general' => [
                'toggles' => [
                    'main_content' => 'Module Configuration',
                ],
            ],
        ];

        $this->advanced_fields = [
            'background' => false,
            'borders' => false,
            'box_shadow' => false,
            'button' => false,
            'filters' => false,
            'fonts' => false,
            'margin_padding' => false,
            'text' => false,
            'link_options' => false,
            'height' => false,
            'scroll_effects' => false,
            'animation' => false,
            'transform' => false,
        ];
    }

    public function get_fields()
    {
        return [
            'shortcode' => [
                'label' => esc_html__('Raw module shortcode', 'wpcloudplugins'),
                'type' => 'wpcp_shortcode_field',
                'option_category' => 'configuration',
                'description' => esc_html__('Edit this module via the Module Builder or manually via the raw code', 'wpcloudplugins'),
                'default' => '[useyourdrive mode="files"]',
                'ajax_url' => USEYOURDRIVE_ADMIN_URL,
                'plugin_slug' => 'useyourdrive',
                'toggle_slug' => 'main_content',
            ],
        ];
    }

    public function render($attrs, $content = null, $render_slug = '')
    {
        $shortcode = html_entity_decode(($this->props['shortcode']));
        if (empty($shortcode)) {
            return esc_html__('Please configure the module first', 'wpcloudplugins');
        }

        \ob_start();

        echo do_shortcode($shortcode);

        $content = \ob_get_clean();

        if (empty($content)) {
            return '';
        }

        return $content;
    }
}

new UseyourDrive();
