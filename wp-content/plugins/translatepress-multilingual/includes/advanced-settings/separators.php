<?php


if ( !defined('ABSPATH' ) )
    exit();

add_filter( 'trp_register_advanced_settings', 'trp_register_troubleshoot_separator', 5 );
function trp_register_troubleshoot_separator( $settings_array ){
    $settings_array[] = array(
        'name'          => 'troubleshoot_options',
        'type'          => 'separator',
        'label'         => esc_html__( 'Troubleshooting', 'translatepress-multilingual' ),
        'no-border'     => true,
        'id'            =>'troubleshooting',
    );
    return $settings_array;
}

add_filter( 'trp_register_advanced_settings', 'trp_register_container_titles' );
function trp_register_container_titles( $settings_array ){
    $container_titles = [
        [
            'name'          => 'exclude_gettext_strings_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Exclude Gettext strings', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_gettext_strings'
        ],
        [
            'name'          => 'exclude_at_strings_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Exclude strings from automatic translation', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_at_strings'
        ],
        [
            'name'          => 'exclude_dynamic_strings_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Exclude from dynamic translation', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_dynamic_strings'
        ],
        [
            'name'          => 'exclude_selectors_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Exclude selectors from translation', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_selectors'
        ],
        [
            'name'          => 'exclude_selectors_at_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Exclude selectors only from automatic translation', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_selectors_at'
        ],
        [
            'name'          => 'exclude_paths_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Do not translate certain paths', 'translatepress-multilingual' ),
            'id'            => 'exclude_strings',
            'container'     => 'exclude_paths'
        ],
        [
            'name'          => 'troubleshooting_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Troubleshooting', 'translatepress-multilingual' ),
            'id'            => 'troubleshooting',
            'container'     => 'troubleshooting'
        ],
        [
            'name'          => 'debug_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Debug', 'translatepress-multilingual' ),
            'id'            => 'debug',
            'container'     => 'debug'
        ],
        [
            'name'          => 'custom_language_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Custom languages', 'translatepress-multilingual' ),
            'id'            => 'custom_language',
            'container'     => 'custom_language'
        ],
        [
            'name'          => 'misc_options_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Miscellaneous options', 'translatepress-multilingual' ),
            'id'            => 'miscellaneous_options',
            'container'     => 'miscellaneous_options'
        ],
        [
            'name'          => 'language_switcher_title',
            'type'          => 'container_title',
            'label'         => esc_html__( 'Language Switcher', 'translatepress-multilingual' ),
            'id'            => 'miscellaneous_options',
            'container'     => 'language_switcher'
        ]
    ];

    return array_merge( $settings_array, $container_titles );
}

add_filter( 'trp_register_advanced_settings', 'trp_register_exclude_separator', 95 );
function trp_register_exclude_separator( $settings_array ){
    $settings_array[] = array(
        'name'          => 'exclude_strings',
        'type'          => 'separator',
        'label'         => esc_html__( 'Exclude strings & pages', 'translatepress-multilingual' ),
        'id'            =>'exclude_strings',
    );
    return $settings_array;
}

add_filter( 'trp_register_advanced_settings', 'trp_register_debug_separator', 500 );
function trp_register_debug_separator( $settings_array ){
	$settings_array[] = array(
	    'name'          => 'debug_options',
		'type'          => 'separator',
		'label'         => esc_html__( 'Debug', 'translatepress-multilingual' ),
        'id'            => 'debug',
	);
	return $settings_array;
}

add_filter( 'trp_register_advanced_settings', 'trp_register_miscellaneous_separator', 1000 );
function trp_register_miscellaneous_separator( $settings_array ){
    $settings_array[] = array(
        'name'          => 'miscellaneous_options',
        'type'          => 'separator',
        'label'         => esc_html__( 'Miscellaneous options', 'translatepress-multilingual' ),
        'id'            => 'miscellaneous_options',
    );
    return $settings_array;
}

add_filter( 'trp_register_advanced_settings', 'trp_register_custom_language_separator', 2000 );
function trp_register_custom_language_separator( $settings_array ){
	$settings_array[] = array(
		'name'          => 'custom_language',
		'type'          => 'separator',
		'label'         => esc_html__( 'Custom language', 'translatepress-multilingual' ),
        'id'            => 'custom_language',
	);
	return $settings_array;
}