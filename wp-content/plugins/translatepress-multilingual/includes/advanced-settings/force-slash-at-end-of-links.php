<?php

add_filter('trp_register_advanced_settings', 'trp_register_force_slash_in_home_url', 1071);
function trp_register_force_slash_in_home_url($settings_array)
{
    $settings_array[] = array(
        'name' => 'force_slash_at_end_of_links',
        'type' => 'checkbox',
        'label' => esc_html__('Force slash at end of home url:', 'translatepress-multilingual'),
        'description' => wp_kses(__('Ads a slash at the end of the home_url() function', 'translatepress-multilingual'), array('br' => array())),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}
