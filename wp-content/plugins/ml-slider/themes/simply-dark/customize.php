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
                'default' => 'rgba(255,255,255,0.43)',
                'css' => '%s .flexslider ul.flex-direction-nav li a { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0.43)',
                'css' => '%s .flexslider ul.flex-direction-nav li a:hover { background: %s }'
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
                'default' => '#000000',
                'css' => '%s .flexslider ul.flex-direction-nav li a { color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider ul.flex-direction-nav li a:hover { color: %s }'
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
                'default' => 'rgba(0,0,0,0.5)',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => 'rgba(0,0,0,1)',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { background: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => 'rgba(0,0,0,1)',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a.flex-active { background: %s }'
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
                'default' => 'rgba(0,0,0,0.7)',
                'css' =>  '%s .flexslider ul.slides .caption-wrap { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider ul.slides .caption-wrap .caption > * { color: %s }'
            ),
            array(
                'label' => esc_html__('Links', 'ml-slider'),
                'name' => 'caption_links_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider ul.slides .caption-wrap .caption a { color: %s }'
            )
        )
    )
);