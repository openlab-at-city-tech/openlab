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
        'label' => esc_html__('Slideshow', 'ml-slider'),
        'name' => 'slideshow',
        'type' => 'section',
        'default' => 'on', // Accepted values: 'on' and 'off'
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'info' => esc_html__("This background color is used when a slide does not fill the whole width and height of the slide area.", 'ml-slider'),
                'name' => 'slideshow_background',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0)',
                'css' => '[ms_id] .flex-viewport, [ms_id] .slides { background: [ms_value] }'
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