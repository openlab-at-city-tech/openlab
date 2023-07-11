<?php

add_filter('trp_register_advanced_settings', 'trp_register_enable_numerals_translation', 1081);
function trp_register_enable_numerals_translation($settings_array)
{
    $settings_array[] = array(
        'name' => 'enable_numerals_translation',
        'type' => 'checkbox',
        'label' => esc_html__('Translate numbers and numerals', 'translatepress-multilingual'),
        'description' => esc_html__('Enable translation of numbers ( e.g. phone numbers)', 'translatepress-multilingual'),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}
