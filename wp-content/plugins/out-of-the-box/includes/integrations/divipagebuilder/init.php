<?php

namespace TheLion\Integrations\Divi;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists(__NAMESPACE__.'\initialize_extension')) {
    function initialize_extension()
    {
        if (class_exists(__NAMESPACE__.'\WPCP_DiviExtension')) {
            return;
        }

        require_once plugin_dir_path(__FILE__).'includes/DiviExtension.php';
    }
    add_action('divi_extensions_init', '\TheLion\Integrations\Divi\initialize_extension');
}
