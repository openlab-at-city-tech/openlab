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
                'default' => 'rgba(255,255,255,0.9)',
                'css' => '%s .flexslider ul.flex-direction-nav li a { background: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0.9)',
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
                'default' => '#6c6c6c',
                'css' => '%s .flexslider .flex-direction-nav li a:before { background-color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#6c6c6c',
                'css' => '%s .flexslider .flex-direction-nav li a:hover:before { background-color: %s }'
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
                'default' => 'rgba(255,255,255,0.95)',
                'css' => '%1$s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:not(.flex-active):not(:hover) { background: %2$s; border-color: %2$s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0.95)',
                'css' =>  '%1$s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { border-color: %2$s; color: %2$s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0.95)',
                'css' => '%1$s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a.flex-active:not(:hover) { border-color: %2$s; color: %2$s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation numbers', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Default', 'ml-slider'),
                'name' => 'navigation_number_color',
                'type' => 'color',
                'default' => 'rgba(74,74,74,0.8)',
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
                'css' =>  '%s .flexslider ul.slides .caption-wrap { background: linear-gradient(rgba(0,0,0,0),%s) }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%s .flexslider ul.slides .caption-wrap .caption { color: %s }'
            )
        )
    )
);