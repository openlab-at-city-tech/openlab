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
                'default' => 'rgba(91,92,97,0.8)',
                'css' => '%s .flexslider .flex-direction-nav li a { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider .flex-direction-nav li a:hover { background: %s }'
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
                'default' => '#efeff0',
                'css' => '%s .flexslider .flex-direction-nav li a:before { background: %s !important }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#efeff0',
                'css' => '%s .flexslider .flex-direction-nav li a:hover:before { background: %s !important }'
            )
        )
    ),
    array(
        'label' => esc_html__('Arrows Border', 'ml-slider'),
        'dependencies' => 'links',
        'fields' => array(
            array(
                'label' => esc_html__('Border', 'ml-slider'),
                'name' => 'arrows_border',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-direction-nav li a { border-color: %s }'
            ),
        )
    ),
    array(
        'label' => esc_html__('Navigation Background', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'navigation_color',
                'type' => 'color',
                'default' => 'rgba(91,92,97,.8)',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:not(.flex-active) { background: %s }',
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => '#ffffff',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { background: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-control-nav li a.flex-active { background: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation Border', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Border', 'ml-slider'),
                'name' => 'navigation_border',
                'type' => 'color',
                'default' => 'rgba(91,92,97,.8)',
                'css' => '%s .flexslider .flex-control-nav li a { border-color: %s }'
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
                'default' => 'rgba(255,255,255,0.8)',
                'css' =>  '%s .flexslider .caption-wrap { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider .caption-wrap { color: %s }'
            )
        )
    )
);