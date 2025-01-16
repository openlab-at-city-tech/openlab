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
                'default' => 'rgba(255,255,255,0.75)',
                'css' => '%s .flexslider ul.flex-direction-nav li a { background: %s }'
            ),
            array(
                'label' => esc_html__( 'Hover', 'ml-slider' ),
                'name' => 'arrows_color_hover',
                'type' => 'color',
                'default' => 'rgba(255,255,255,0.75)',
                'css' => '%s .flexslider ul.flex-direction-nav li a:hover { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider'),
                'name' => 'arrows_text_color',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider ul.flex-direction-nav li a { color: %s }'
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
                'css' => '%s .flexslider .flex-direction-nav li a:before { background: %s !important }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider .flex-direction-nav li a:hover:before { background: %s !important }'
            )
        )
    ),
    array(
        'label' => esc_html__('Numbers', 'ml-slider'),
        'fields' => array(
            array(
                'label' => esc_html__('Color', 'ml-slider'),
                'name' => 'navigation_color',
                'type' => 'color',
                'default' => '#ffffff',
                'css' => '%1$s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a, %1$s .flexslider .flex-slide-count { color: %2$s !important }'
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
                'default' => 'rgba(255,255,255,0.75)',
                'css' =>  '%s .flexslider ul.slides .caption-wrap { background: %s }'
            ),
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%s .flexslider ul.slides .caption-wrap { color: %s }'
            )
        )
    )
);