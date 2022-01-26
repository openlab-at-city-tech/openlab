<?php

namespace TheLion\OutoftheBox\Integrations\Elementor;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Widget extends \Elementor\Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        wp_register_script('OutoftheBox.Elementor.Widget', plugins_url('widget.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION);
    }

    public function is_editor()
    {
        return is_admin() && (isset($_GET['action']) && 'elementor' === $_GET['action']) || (isset($_REQUEST['elementor-preview']));
    }

    public function get_script_depends()
    {
        $a = $this->is_editor();
        if (false === $this->is_editor()) {
            return [];
        }

        global $OutoftheBox;

        $mediaplayer = $OutoftheBox->get_processor()->load_mediaplayer($OutoftheBox->settings['mediaplayer_skin']);

        if (!empty($mediaplayer)) {
            $mediaplayer->load_scripts();
            $mediaplayer->load_styles();
        }

        return ['OutoftheBox.UploadBox', 'OutoftheBox', 'OutoftheBox.Elementor.Widget'];
    }

    public function get_style_depends()
    {
        if (false === $this->is_editor()) {
            return [];
        }

        global $OutoftheBox;

        return ['Eva-Icons', 'OutoftheBox.ShortcodeBuilder', 'OutoftheBox'];
    }

    public function is_reload_preview_required()
    {
        return true;
    }

    public function get_name()
    {
        return 'wpcp-outofthebox';
    }

    public function get_title()
    {
        return 'Dropbox';
    }

    public function get_icon()
    {
        return 'eicon-cloud-check';
    }

    public function get_categories()
    {
        return ['wpcloudplugins'];
    }

    public function get_keywords()
    {
        return ['cloud', 'dropbox', 'documents', 'files', 'upload', 'video', 'audio', 'media', 'embed'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Shortcode', 'wpcloudplugins'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'shortcode',
            [
                'label' => esc_html__('Raw shortcode', 'wpcloudplugins'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'description' => esc_html__('Edit this shortcode via the Shortcode Builder or manually via the raw code', 'wpcloudplugins'),
                'default' => '[outofthebox mode="files"]',
                'rows' => 7,
            ]
        );

        $this->add_control(
            'edit_shortcode',
            [
                'type' => \Elementor\Controls_Manager::BUTTON,
                'show_label' => false,
                'text' => esc_html__('Edit via Shortcode Builder', 'wpcloudplugins'),
                'event' => 'wpcp:editor:edit_outofthebox_shortcode',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $html = $this->get_render_html($settings['shortcode']);

        echo ($html) ? $html : $settings['shortcode'];
    }

    protected function get_render_html($shortcode)
    {
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
