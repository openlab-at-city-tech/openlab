<?php
/**
 * Keep this file as is. 
 * You can optionally add array() values to allow to customize theme design
 * See themes/customize.php as reference
 */

return array(
    array(
        'label' => esc_html__('Arrows Background', 'ml-slider'),
        'dependencies' => 'links',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'arrows_color',
                'type' => 'color',
                'default' => '#fff',
                'css' => '%s .flexslider .flex-direction-nav li { background-color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => '#07383C',
                'css' => '%s .flexslider .flex-direction-nav li:hover { background-color: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Arrows Icon', 'ml-slider'),
        'dependencies' => 'links',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'arrows_icon',
                'type' => 'color',
                'default' => '#50585C',
                'css' => '%1$s .flexslider .flex-direction-nav li a.flex-prev, %1$s .flexslider .flex-direction-nav li a.flex-next { background-color: %2$s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#fff',
                'css' => '%1$s .flexslider .flex-direction-nav li a.flex-prev:hover, %1$s .flexslider .flex-direction-nav li a.flex-next:hover { background-color: %2$s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'navigation_color',
                'type' => 'color',
                'default' => '#07383C',
                'css' => '%s .flexslider .flex-control-nav li a:not(.flex-active) { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => '#07383C',
                'css' => '%s .flexslider .flex-control-nav li a:hover { background: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => '#07383C',
                'css' => '%s .flexslider .flex-control-nav li a.flex-active { background: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Caption', 'ml-slider'),
        'fields' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'name' => 'caption_background',
                'type' => 'color',
                'default' => '#fff',
                'css' => '%s .flexslider .caption-wrap { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider'),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#000',
                'css' => '%s .flexslider .caption-wrap { color: %s }'
            ),
            array(
                'label' => esc_html__('Links', 'ml-slider'),
                'name' => 'caption_links_color',
                'type' => 'color',
                'default' => '#F9F9F9',
                'css' => '%s .flexslider .caption-wrap a { color: %s }'
            )
        )
    )
);
