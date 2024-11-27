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
 *          '%s .lorem { color: %s }', 
 *          '%1$s .ipsum { color: %2$s; border-color: %2$s }', 
 *      )
 *      'css' => '%s .lorem { color: %s }'
 */
return array(
    array(
        'label' => esc_html__('Play Button', 'ml-slider'),
        'dependencies' => 'pausePlay',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'play_button',
                'type' => 'color',
                'default' => '#000',
                'css' => '%1$s .flexslider .flex-pauseplay .flex-pause, %1$s .flexslider .flex-pauseplay .flex-play { background-color: %2$s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'play_button_hover',
                'type' => 'color',
                'default' => '#000',
                'css' => '%s .flexslider .flex-pauseplay a:hover { background-color: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Play Button Icon', 'ml-slider'),
        'dependencies' => 'pausePlay',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'play_button_icon',
                'type' => 'color',
                'default' => '#fff',
                'css' => '%s .flexslider .flex-pauseplay a:before { color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'play_button_icon_hover',
                'type' => 'color',
                'default' => '#fff',
                'css' => '%s .flexslider .flex-pauseplay a:hover:before { color: %s }'
            )
        )
    )
);