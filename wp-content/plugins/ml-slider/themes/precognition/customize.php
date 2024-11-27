<?php
/**
 * Keep this file as is. 
 * You can optionally add array() values to allow to customize theme design
 * See themes/customize.php as reference
 */

return array(
    array(
        'label' => esc_html__('Arrows Icon', 'ml-slider'),
        'dependencies' => 'links',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'arrows_icon',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-direction-nav li a:before { background: %s !important }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-direction-nav li a:hover:before { background: %s !important }'
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
                'default' => '#ffffff',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:not(.flex-active):not(:hover) { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => '#343536',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { background: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => '#343536',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a.flex-active:not(:hover) { background: %s }'
            ),
            array(
                'label' => esc_html__('Border', 'ml-slider'),
                'name' => 'navigation_color_border',
                'type' => 'color',
                'default' => '#343536',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li { border-color: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation Text and Numbers', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'navigation_number_color',
                'type' => 'color',
                'default' => '#343536',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:not(.flex-active):not(:hover) { color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_number_color_hover',
                'type' => 'color',
                'default' => '#ffffff',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { color: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_number_color_active',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a.flex-active:not(:hover) { color: %s }'
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
                'default' => 'rgba(0,0,0,0.8)',
                'css' =>  '%s .flexslider ul.slides .caption-wrap { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider ul.slides .caption-wrap .caption { color: %s }'
            ),
            array(
                'label' => esc_html__('Links', 'ml-slider'),
                'name' => 'caption_links_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .caption-wrap .caption a { color: %s }'
            )
        )
    )
);