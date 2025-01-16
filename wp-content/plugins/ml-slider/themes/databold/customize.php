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
                'default' => '#016fb9',
                'css' => '%s .flexslider .flex-direction-nav li a { background-color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => '#016fb9',
                'css' => '%s .flexslider .flex-direction-nav li a:hover { background-color: %s }'
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
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-direction-nav li a:after { background-color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider .flex-direction-nav li a:hover:after { background-color: %s }'
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
                'default' => '#016fb9',
                'css' => array(
                    '%s .flexslider .flex-control-nav li a:not(.flex-active) { background: %s }',
                    '%s .flexslider .flex-control-nav li a.flex-active { border-color: %s }'
                )
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => '#016fb9',
                'css' => array(
                    '%s .flexslider .flex-control-nav li a:not(.flex-active):hover { background: %s }',
                    '%s .flexslider .flex-control-nav li a.flex-active { border-color: %s }'
                )
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
                'default' => '#ffffff',
                'css' => '%s .flexslider .caption-wrap .caption { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider'),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#333333',
                'css' => '%s .flexslider .caption-wrap .caption { color: %s }'
            ),
            array(
                'label' => esc_html__('Links', 'ml-slider'),
                'name' => 'caption_links_color',
                'type' => 'color',
                'default' => '#016fb9',
                'css' => '%s .flexslider .caption-wrap a { color: %s }'
            )
        )
    )
);