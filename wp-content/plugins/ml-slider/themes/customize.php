<?php
/**
 * A list of settings to customize themes that applies to all themes.
 * 
 * 'css' can be a single string or an array of string
 * Each one requires 2 placeholders:
 *      - First one is for slideshow ID string ("#metaslider-id-{ID}")
 *      - Second one for theme setting (e.g. color)
 * Examples:
 *      'css' => array(
 *          '[ms_id] .lorem { color: [ms_value] }', 
 *          '[ms_id] .ipsum { color: [ms_value]; border-color: [ms_value] }', 
 *      )
 *      'css' => '[ms_id] .lorem { color: [ms_value] }'
 * 
 * 'slideshow_edit' => false // We don't show this setting / fields in the slideshow edit page
 */
return array(
    array(
        'label' => esc_html__('Play / Pause Button', 'ml-slider'),
        'name' => 'play_pause',
        'type' => 'section',
        'default' => 'on', // Accepted values: 'on' and 'off'
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'play_button',
                        'type' => 'color',
                        'default' => '#000000',
                        'css' => '[ms_id] .flexslider .flex-pauseplay .flex-pause, [ms_id] .flexslider .flex-pauseplay .flex-play { background-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'play_button_hover',
                        'type' => 'color',
                        'default' => '#000000',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:hover { background-color: [ms_value] }'
                    )
                ),
            ),
            array(
                'label' => esc_html__('Icon Colors', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'play_button_icon',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:before { color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'play_button_icon_hover',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:hover:before { color: [ms_value] }'
                    )
                ),
            ),
            array(
                'label' => esc_html__('Border Radius', 'ml-slider'),
                'name' => 'play_button_border_radius',
                'type' => 'range',
                'default' => 50,
                'metric' => 'px',
                'min' => 0,
                'max' => 50,
                'css' => '[ms_id] .flexslider .flex-pauseplay a { border-radius: [ms_value]px }'
            ),
            array(
                'label' => esc_html__('Opacity (default)', 'ml-slider'),
                'name' => 'play_button_opacity',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-pauseplay a { opacity: [ms_value] }'
            ),
            array(
                'label' => esc_html__('Opacity (hover)', 'ml-slider'),
                'name' => 'play_button_opacity_hover',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-pauseplay a:hover { opacity: [ms_value] }'
            ),
            /*array(
                'label' => esc_html__('Position', 'ml-slider'),
                'name' => 'play_button_position',
                'type' => 'select',
                'default' => 'bottom-left',
                'options' => array(
                    array(
                        'label' => esc_html__('Top Left', 'ml-slider'),
                        'value' => 'top-left'
                    ),
                    array(
                        'label' => esc_html__('Bottom Left', 'ml-slider'),
                        'value' => 'bottom-left'
                    ),
                    array(
                        'label' => esc_html__('Top Right', 'ml-slider'),
                        'value' => 'top-right'
                    ),
                    array(
                        'label' => esc_html__('Bottom Right', 'ml-slider'),
                        'value' => 'bottom-right'
                    ),
                ),
                'css' => 'css_rules',
                'css_rules' => array(
                    'bottom-left' => '[ms_id] .flexslider .flex-pauseplay a { left: 10px; bottom: 5px }',
                    'top-left' => '[ms_id] .flexslider .flex-pauseplay a { left: 10px; top: 5px; bottom: unset }',
                    'bottom-right' => '[ms_id] .flexslider .flex-pauseplay a { right: 10px; top: unset; bottom: 5px; left: unset }',
                    'top-right' => '[ms_id] .flexslider .flex-pauseplay a { right: 10px; top: 5px; bottom: unset; left: unset }',
                )
            )*/
        )
    ),
    array(
        'label' => esc_html__('Slideshow', 'ml-slider'),
        'name' => 'slideshow',
        'type' => 'section',
        'default' => 'on', // Accepted values: 'on' and 'off'
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'name' => 'slideshow_background',
                'type' => 'color',
                'default' => '#fff',
                'css' => '[ms_id] .slides li { background-color: [ms_value] }'
            ),
            array(
                'label' => esc_html__('Progress Bar', 'ml-slider'),
                'name' => 'slideshow_progress_bar_color',
                'type' => 'color',
                'default' => '#fff',
                'css' => '[ms_id] .flexslider .flex-progress-bar { background-color: [ms_value] }'
            )
        )
    )
);