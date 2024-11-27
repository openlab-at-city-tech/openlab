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
                'default' => '#000000',
                'css' => '%1$s .flexslider .flex-direction-nav li a:before, %1$s .flexslider .flex-direction-nav li a:after { color: %2$s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'arrows_icon_hover',
                'type' => 'color',
                'default' => '#000000',
                'css' => '%1$s .flexslider .flex-direction-nav li a:hover:before, %1$s .flexslider .flex-direction-nav li a:hover:after { color: %2$s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation Color', 'ml-slider'),
        'dependencies' => 'navigation',
        'fields' => array(
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_color_hover',
                'type' => 'color',
                'default' => '#28303d',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover:after { color: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_color_active',
                'type' => 'color',
                'default' => '#28303d',
                'css' => '%s .flexslider .flex-control-nav li a.flex-active:after { color: %s }'
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
                'default' => '#28303d',
                'css' => '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:not(.flex-active) { color: %s }'
            ),
            array(
                'label' => esc_html__('Hover', 'ml-slider'),
                'name' => 'navigation_number_color_hover',
                'type' => 'color',
                'default' => '#28303d',
                'css' =>  '%s .flexslider ol.flex-control-nav:not(.flex-control-thumbs) li a:hover { color: %s }'
            ),
            array(
                'label' => esc_html__('Active', 'ml-slider'),
                'name' => 'navigation_number_color_active',
                'type' => 'color',
                'default' => '#28303d',
                'css' => '%s .flexslider .flex-control-nav li a.flex-active { color: %s }'
            )
        )
    ),
    array(
        'label' => esc_html__('Caption', 'ml-slider'),
        'fields' => array(
            array(
                'label' => esc_html__('Text', 'ml-slider' ),
                'name' => 'caption_text_color',
                'type' => 'color',
                'default' => '#28303d',
                'css' => '%s .flexslider .caption-wrap .caption { color: %s }'
            )
        )
    )
);