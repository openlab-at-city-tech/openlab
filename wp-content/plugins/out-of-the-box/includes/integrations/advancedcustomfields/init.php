<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ACF
{
    // vars
    public $settings;

    public function __construct()
    {
        $this->settings = [
            'version' => '1.0.0',
            'url' => plugin_dir_url(__FILE__),
            'path' => plugin_dir_path(__FILE__),
        ];

        // include field
        add_action('acf/include_field_types', [$this, 'include_field']); // v5
    }

    public function include_field($version = false)
    {
        // include
        include_once 'fields/class-ACF_OutoftheBox_Field-v'.$version.'.php';
    }
}

new \TheLion\OutoftheBox\Integrations\ACF();
