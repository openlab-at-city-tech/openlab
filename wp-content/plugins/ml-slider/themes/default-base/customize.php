<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Keep this file as is. 
 * You can optionally add array() values to allow to customize theme design
 * See themes/customize.php as reference
 * 
 * Only use 'scope' key as immediate child of type 'section'
 * to limit the scope of customize settings to specific slideshow settings.
 * Example below means the setting only applies 
 * when dots navigation is set as dots or dots on hover
 * 'scope' => array(
 *    'navigation' => array( 
 *        'true',
 *        'dots_onhover'
 *    ),
 * )
 */

return array(
    array(
        'label' => esc_html__('Arrows', 'ml-slider'),
        'name' => 'arrows',
        'type' => 'section',
        'default' => 'on',
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'info' => esc_html__('The background color of the arrow buttons.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'arrows_color',
                        'type' => 'color',
                        'default' => '#333333',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a { background: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'arrows_color_hover',
                        'type' => 'color',
                        'default' => '#333333',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a:hover { background: [ms_value] }'
                    )
                ),
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Icon Colors', 'ml-slider'),
                'info' => esc_html__('The color of the arrow icons.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'arrows_icon',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a:after { background-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'arrows_icon_hover',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a:hover:after { background-color: [ms_value] }'
                    )
                ),
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Icon Size', 'ml-slider'),
                'info' => esc_html__('The size of the arrow icons.', 'ml-slider'),
                'name' => 'arrows_icon_size',
                'type' => 'range',
                'default' => 20,
                'metric' => 'px',
                'min' => 10,
                'max' => 40,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a:after { mask-size: [ms_value]px auto }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Color', 'ml-slider'),
                'info' => esc_html__('The border color of the arrow buttons.', 'ml-slider'),
                'type' => 'fields',
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'arrows_border',
                        'type' => 'color',
                        'default' => 'rgba(0,0,0,0)',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a { border-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'arrows_border_hover',
                        'type' => 'color',
                        'default' => 'rgba(0,0,0,0)',
                        'css' => '[ms_id] .flexslider .flex-direction-nav li a:hover { border-color: [ms_value] }'
                    )
                ),
                'slideshow_edit' => false,
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Width', 'ml-slider'),
                'info' => esc_html__('The thickness of the border around the arrow buttons.', 'ml-slider'),
                'name' => 'arrows_border_width',
                'type' => 'range',
                'default' => 0,
                'metric' => 'px',
                'min' => 0,
                'max' => 6,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { border-width: [ms_value]px }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Style', 'ml-slider'),
                'info' => esc_html__('The line style of the border around the arrow buttons.', 'ml-slider'),
                'name' => 'arrows_border_style',
                'type' => 'select',
                'default' => 'solid',
                'options' => array(
                    array(
                        'label' => esc_html__('Solid', 'ml-slider'),
                        'value' => 'solid'
                    ),
                    array(
                        'label' => esc_html__('Dotted', 'ml-slider'),
                        'value' => 'dotted'
                    ),
                    array(
                        'label' => esc_html__('Dashed', 'ml-slider'),
                        'value' => 'dashed'
                    )
                ),
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { border-style: [ms_value] }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Distance from Edge', 'ml-slider'),
                'info' => esc_html__('The distance between the arrows and the left/right edges of the slideshow.', 'ml-slider'),
                'name' => 'arrows_distance_edge',
                'type' => 'range',
                'default' => 10,
                'metric' => 'px',
                'min' => -100,
                'max' => 50,
                'css' => array(
                    '[ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-prev { left: [ms_value]px }',
                    '[ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-next { right: [ms_value]px }'
                ),
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Vertical Position', 'ml-slider'),
                'info' => esc_html__('Whether the arrows are positioned nearer the top or bottom of the slideshow.', 'ml-slider'),
                'name' => 'arrows_vertical_position',
                'type' => 'select',
                'default' => 'top',
                'options' => array(
                    /*array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'value' => 'default'
                    ),*/
                    array(
                        'label' => esc_html__('Top', 'ml-slider'),
                        'value' => 'top'
                    ),
                    array(
                        'label' => esc_html__('Bottom', 'ml-slider'),
                        'value' => 'bottom'
                    )
                ),
                'css' => 'css_rules',
                'css_rules' => array(
                    //'default' => '',
                    'top' => '[ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-prev, [ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-next { bottom: unset; top: calc([ms_field_value]% + 20px); transform: translateY(-[ms_field_value]%); }', // Take [ms_field_value] from arrows_vertical_position_offset
                    'bottom' => '[ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-prev, [ms_id] .flexslider:not(.filmstrip) .flex-direction-nav li a.flex-next { top: unset; bottom: [ms_field_value]%; transform: translateY([ms_field_value]%); }' // Take [ms_field_value] from arrows_vertical_position_offset
                ),
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Vertical Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the arrows sit from the top or bottom of the slideshow, depending on the "Vertical Position" setting.', 'ml-slider'),
                'name' => 'arrows_vertical_position_offset',
                'type' => 'range',
                'default' => 50,
                'metric' => '%',
                'min' => 0,
                'max' => 100,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'arrows_vertical_position',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Width', 'ml-slider'),
                'info' => esc_html__('The width of the arrow buttons.', 'ml-slider'),
                'name' => 'arrows_width',
                'type' => 'range',
                'default' => 26,
                'metric' => 'px',
                'min' => 20,
                'max' => 60,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { width: [ms_value]px }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Height', 'ml-slider'),
                'info' => esc_html__('The height of the arrow buttons.', 'ml-slider'),
                'name' => 'arrows_height',
                'type' => 'range',
                'default' => 26,
                'metric' => 'px',
                'min' => 20,
                'max' => 60,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { height: [ms_value]px }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Radius', 'ml-slider'),
                'info' => esc_html__("The roundness of the arrow buttons' corners. 50% makes them circular.", 'ml-slider'),
                'name' => 'arrows_border_radius',
                'type' => 'range',
                'default' => 50,
                'metric' => '%',
                'min' => 0,
                'max' => 50,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { border-radius: [ms_value]% }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Opacity (default)', 'ml-slider'),
                'info' => esc_html__('The transparency of the arrow buttons when not hovered.', 'ml-slider'),
                'name' => 'arrows_opacity',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a { opacity: [ms_value] }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Opacity (hover)', 'ml-slider'),
                'info' => esc_html__('The transparency of the arrow buttons when hovered.', 'ml-slider'),
                'name' => 'arrows_opacity_hover',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a:hover { opacity: [ms_value] }',
                'scope' => array(
                    'links' => array( // Only applies when arrows is: visible or visible on hover
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Focus Outline Color', 'ml-slider'),
                'info' => esc_html__('Use a high-contrast color. The outline color shows as a ring around the arrows when navigated to via keyboard. This improves accessibility for the arrows.', 'ml-slider'),
                'name' => 'arrows_focus_outline_color',
                'type' => 'color',
                'default' => '#fff',
                'css' => '[ms_id] .flexslider .flex-direction-nav li a:focus { outline-color: [ms_value] }',
                'scope' => array(
                    'links' => array(
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Focus Outline Width', 'ml-slider'),
                'info' => esc_html__('The width of the outline shown around an arrow when it receives focus, for accessibility.', 'ml-slider'),
                'name' => 'arrows_focus_outline_width',
                'type' => 'range',
                'default' => 2,
                'metric' => 'px',
                'min' => 0,
                'max' => 10,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a:focus { outline-width: [ms_value]px }',
                'scope' => array(
                    'links' => array(
                        'true',
                        'onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Focus Outline Offset', 'ml-slider'),
                'info' => esc_html__('The distance between the outline and the arrow when it receives focus, for accessibility.', 'ml-slider'),
                'name' => 'arrows_focus_outline_offset',
                'type' => 'range',
                'default' => 2,
                'metric' => 'px',
                'min' => -10,
                'max' => 20,
                'css' => '[ms_id] .flexslider .flex-direction-nav li a:focus { outline-offset: [ms_value]px }',
                'scope' => array(
                    'links' => array(
                        'true',
                        'onhover'
                    )
                )
            )
        )
    ),
    array(
        'label' => esc_html__('Navigation', 'ml-slider'),
        'name' => 'navigation',
        'type' => 'section',
        'default' => 'on',
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'info' => esc_html__('The background color of the navigation dots.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'navigation_color',
                        'type' => 'color',
                        'default' => 'rgba(0,0,0,0.5)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a:not(.flex-active) { background: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'navigation_color_hover',
                        'type' => 'color',
                        'default' => 'rgba(0,0,0,0.7)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a:hover { background: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Active', 'ml-slider'),
                        'name' => 'navigation_color_active',
                        'type' => 'color',
                        'default' => 'rgba(0,0,0,0.9)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a.flex-active { background: [ms_value] }'
                    )
                ),
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Color', 'ml-slider'),
                'info' => esc_html__('The border color of the navigation dots.', 'ml-slider'),
                'type' => 'fields',
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'navigation_border_color',
                        'type' => 'color',
                        'default' => 'rgba(255,255,255,0)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a { border-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'navigation_border_color_hover',
                        'type' => 'color',
                        'default' => 'rgba(255,255,255,0)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a:not(.flex-active):hover { border-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Active', 'ml-slider'),
                        'name' => 'navigation_border_color_active',
                        'type' => 'color',
                        'default' => 'rgba(255,255,255,0)',
                        'css' => '[ms_id] .flexslider .flex-control-nav li a.flex-active { border-color: [ms_value] }'
                    )
                ),
                'slideshow_edit' => false,
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Width', 'ml-slider'),
                'info' => esc_html__('The thickness of the border around the navigation dots.', 'ml-slider'),
                'name' => 'navigation_border_width',
                'type' => 'range',
                'default' => 0,
                'metric' => 'px',
                'min' => 0,
                'max' => 6,
                'css' => '[ms_id] .flexslider .flex-control-nav li a { border-width: [ms_value]px }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Border Style', 'ml-slider'),
                'info' => esc_html__('The line style of the border around the navigation dots.', 'ml-slider'),
                'name' => 'navigation_border_style',
                'type' => 'select',
                'default' => 'solid',
                'options' => array(
                    array(
                        'label' => esc_html__('Solid', 'ml-slider'),
                        'value' => 'solid'
                    ),
                    array(
                        'label' => esc_html__('Dotted', 'ml-slider'),
                        'value' => 'dotted'
                    ),
                    array(
                        'label' => esc_html__('Dashed', 'ml-slider'),
                        'value' => 'dashed'
                    )
                ),
                'css' => '[ms_id] .flexslider .flex-control-nav li a { border-style: [ms_value] }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Vertical Position', 'ml-slider'),
                'info' => esc_html__('Whether the navigation dots are positioned nearer the top or bottom of the slideshow.', 'ml-slider'),
                'name' => 'navigation_vertical_position',
                'type' => 'select',
                'default' => 'bottom',
                'options' => array(
                    /*array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'value' => 'default'
                    ),*/
                    array(
                        'label' => esc_html__('Top', 'ml-slider'),
                        'value' => 'top'
                    ),
                    array(
                        'label' => esc_html__('Bottom', 'ml-slider'),
                        'value' => 'bottom'
                    )
                ),
                'css' => 'css_rules',
                'css_rules' => array(
                    //'default' => '',
                    'top' => '[ms_id] .flexslider .flex-control-nav { bottom: unset; top: [ms_field_value]px }', // Take [ms_field_value] from navigation_vertical_position_offset
                    'bottom' => '[ms_id] .flexslider .flex-control-nav { top: unset; bottom: [ms_field_value]px }' // Take [ms_field_value] from navigation_vertical_position_offset
                ),
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Vertical Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the navigation dots sit from the top or bottom of the slideshow, depending on the "Vertical Position" setting.', 'ml-slider'),
                'name' => 'navigation_vertical_position_offset',
                'type' => 'range',
                'default' => -27,
                'metric' => 'px',
                'min' => -100,
                'max' => 300,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'navigation_vertical_position',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Alignment', 'ml-slider'),
                'info' => esc_html__('The horizontal alignment of the navigation dots.', 'ml-slider'),
                'name' => 'navigation_align',
                'type' => 'select',
                'default' => 'center',
                'options' => array(
                    array(
                        'label' => esc_html__('Left', 'ml-slider'),
                        'value' => 'left'
                    ),
                    array(
                        'label' => esc_html__('Right', 'ml-slider'),
                        'value' => 'right'
                    ),
                    array(
                        'label' => esc_html__('Center', 'ml-slider'),
                        'value' => 'center'
                    )
                ),
                'css' => '[ms_id] .flexslider .flex-control-nav { text-align: [ms_value] }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Dots Border Radius', 'ml-slider'),
                'info' => esc_html__("The roundness of the navigation dots' corners.", 'ml-slider'),
                'name' => 'navigation_border_radius',
                'type' => 'range',
                'default' => 20,
                'metric' => 'px',
                'min' => 0,
                'max' => 30,
                'css' => '[ms_id] .flexslider .flex-control-nav li a { border-radius: [ms_value]px }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Dots Width', 'ml-slider'),
                'info' => esc_html__('The width of each navigation dot.', 'ml-slider'),
                'name' => 'navigation_width',
                'type' => 'range',
                'default' => 11,
                'metric' => 'px',
                'min' => 5,
                'max' => 30,
                'css' => '[ms_id] .flexslider .flex-control-paging li a { width: [ms_value]px }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Dots Height', 'ml-slider'),
                'info' => esc_html__('The height of each navigation dot.', 'ml-slider'),
                'name' => 'navigation_height',
                'type' => 'range',
                'default' => 11,
                'metric' => 'px',
                'min' => 5,
                'max' => 30,
                'css' => '[ms_id] .flexslider .flex-control-paging li a { height: [ms_value]px }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
            array(
                'label' => esc_html__('Distance Between Dots', 'ml-slider'),
                'info' => esc_html__('The spacing between each navigation dot.', 'ml-slider'),
                'name' => 'navigation_between',
                'type' => 'range',
                'default' => 6,
                'metric' => 'px',
                'min' => 0,
                'max' => 20,
                'css' => '[ms_id] .flexslider .flex-control-nav li a { margin: 0 [ms_value]px }',
                'scope' => array(
                    'navigation' => array( // Only applies when navigation is: dots or dots on hover
                        'true',
                        'dots_onhover'
                    )
                )
            ),
        )
    ),
    array(
        'label' => esc_html__('Caption', 'ml-slider'),
        'name' => 'caption',
        'type' => 'section',
        'default' => 'on',
        'settings' => array(
            array(
                'label' => esc_html__('Colors', 'ml-slider'),
                'info' => esc_html__('The background, text, and link colors of the caption.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Background', 'ml-slider'),
                        'name' => 'caption_background',
                        'type' => 'color',
                        'default' => '#000000',
                        'css' =>  '[ms_id] .flexslider .caption-wrap { background: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Text', 'ml-slider'),
                        'name' => 'caption_text_color',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .caption-wrap { color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Links', 'ml-slider'),
                        'name' => 'caption_links_color',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .caption-wrap a { color: [ms_value] }'
                    )
                )
            ),
            array(
                'label' => esc_html__('Vertical Position', 'ml-slider'),
                'info' => esc_html__('Whether the caption is positioned nearer the top or bottom of the slide.', 'ml-slider'),
                'name' => 'caption_vertical_position',
                'type' => 'select',
                'default' => 'bottom',
                'options' => array(
                    /*array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'value' => 'default'
                    ),*/
                    array(
                        'label' => esc_html__('Top', 'ml-slider'),
                        'value' => 'top'
                    ),
                    array(
                        'label' => esc_html__('Bottom', 'ml-slider'),
                        'value' => 'bottom'
                    )
                ),
                'css' => 'css_rules', // refer to css_rules where 'value' => '.lorem {}' is based on 'options' value
                'css_rules' => array(
                    //'default' => '',
                    'top' => '[ms_id] .flexslider .caption-wrap { bottom: unset; top: [ms_field_value]%; transform: translateY(-[ms_field_value]%); }', // Take [ms_field_value] from caption_vertical_position_offset
                    'bottom' => '[ms_id] .flexslider .caption-wrap { top: unset; bottom: [ms_field_value]%; transform: translateY([ms_field_value]%); }', // Take [ms_field_value] from caption_vertical_position_offset 
                ),
                /*'dependencies' => array(
                    array(
                        'show' => 'caption_vertical_position_offset', // Show caption_vertical_position_offset
                        'when' => array(
                            'top',
                            'bottom'
                        ) // When caption_vertical_position is 'top' or 'bottom'
                    )
                )*/
            ),
            array(
                'label' => esc_html__('Vertical Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the caption sits from the top or bottom of the slide, depending on the "Vertical Position" setting.', 'ml-slider'),
                'name' => 'caption_vertical_position_offset',
                'type' => 'range',
                'default' => 0,
                'metric' => '%',
                'min' => 0,
                'max' => 100,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'caption_vertical_position'
            ),
            array(
                'label' => esc_html__('Horizontal Position', 'ml-slider'),
                'info' => esc_html__('Whether the caption is positioned nearer the left or right of the slide.', 'ml-slider'),
                'name' => 'caption_horizontal_position',
                'type' => 'select',
                'default' => 'left',
                'options' => array(
                    array(
                        'label' => esc_html__('Right', 'ml-slider'),
                        'value' => 'right'
                    ),
                    array(
                        'label' => esc_html__('Left', 'ml-slider'),
                        'value' => 'left'
                    )
                ),
                'css' => 'css_rules', // refer to css_rules where 'value' => '.lorem {}' is based on 'options' value
                'css_rules' => array(
                    'right' => '[ms_id] .flexslider .caption-wrap { left: unset; right: [ms_field_value]% }', // Take [ms_field_value] from caption_horizontal_position_offset
                    'left' => '[ms_id] .flexslider .caption-wrap { right: unset; left: [ms_field_value]% }', // Take [ms_field_value] from caption_horizontal_position_offset 
                ),
            ),
            array(
                'label' => esc_html__('Horizontal Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the caption sits from the left or right of the slide, depending on the "Horizontal Position" setting.', 'ml-slider'),
                'name' => 'caption_horizontal_position_offset',
                'type' => 'range',
                'default' => 0,
                'metric' => '%',
                'min' => 0,
                'max' => 100,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'caption_horizontal_position'
            ),
            array(
                'label' => esc_html__('Width', 'ml-slider'),
                'info' => esc_html__('The width of the caption box, as a percentage of the slide width.', 'ml-slider'),
                'name' => 'caption_width',
                'type' => 'range',
                'default' => 100,
                'metric' => '%',
                'min' => 0,
                'max' => 100,
                'css' => '[ms_id] .flexslider .caption-wrap { width: [ms_value]% }'
            ),
            array(
                'label' => esc_html__('Font Size', 'ml-slider'),
                'info' => esc_html__("The Font Size uses em units. The display is relative to your theme's CSS so the preview be different from the frontend display.", 'ml-slider'),
                'name' => 'caption_font_size',
                'type' => 'range',
                'default' => 1,
                'metric' => 'em',
                'min' => 0.5,
                'max' => 3,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .caption-wrap .caption { font-size: [ms_value]em }'
            ),
            array(
                'label' => esc_html__('Line Height', 'ml-slider'),
                'info' => esc_html__("The Line Height uses em units. The display is relative to your theme's CSS so the preview be different from the frontend display.", 'ml-slider'),
                'name' => 'caption_line_height',
                'type' => 'range',
                'default' => 1.4,
                'metric' => 'em',
                'min' => 0.5,
                'max' => 3,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .caption-wrap .caption { line-height: [ms_value]em }'
            ),
            array(
                'label' => esc_html__('Text Align', 'ml-slider'),
                'info' => esc_html__('The horizontal alignment of the text inside the caption.', 'ml-slider'),
                'name' => 'caption_text_align',
                'type' => 'select',
                'default' => 'left',
                'options' => array(
                    array(
                        'label' => esc_html__('Left', 'ml-slider'),
                        'value' => 'left'
                    ),
                    array(
                        'label' => esc_html__('Right', 'ml-slider'),
                        'value' => 'right'
                    ),
                    array(
                        'label' => esc_html__('Center', 'ml-slider'),
                        'value' => 'center'
                    )
                ),
                'css' => '[ms_id] .flexslider .caption-wrap .caption { text-align: [ms_value] }'
            ),
            array(
                'label' => esc_html__('Opacity', 'ml-slider'),
                'info' => esc_html__('The transparency of the caption background.', 'ml-slider'),
                'name' => 'caption_opacity',
                'type' => 'range',
                'default' => 0.7,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .caption-wrap { opacity: [ms_value] }'
            )
        )
    ),
    array(
        'label' => esc_html__('Play / Pause Button', 'ml-slider'),
        'name' => 'play_pause',
        'type' => 'section',
        'default' => 'on', // Accepted values: 'on' and 'off'
        'settings' => array(
            array(
                'label' => esc_html__('Background', 'ml-slider'),
                'info' => esc_html__('The background color of the play/pause button.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'play_button',
                        'type' => 'color',
                        'default' => '#000000',
                        'css' => '[ms_id] .flexslider .flex-pauseplay .flex-pause, [ms_id] .flexslider .flex-pauseplay .flex-play { background-color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'play_button_hover',
                        'type' => 'color',
                        'default' => '#000000',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:hover { background-color: [ms_value] }'
                    )
                ),
            ),
            array(
                'label' => esc_html__('Icon Colors', 'ml-slider'),
                'info' => esc_html__('The color of the play/pause icon.', 'ml-slider'),
                'type' => 'fields', // Fields added through 'fields' array
                'fields' => array(
                    array(
                        'label' => esc_html__('Default', 'ml-slider'),
                        'name' => 'play_button_icon',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:before { color: [ms_value] }'
                    ),
                    array(
                        'label' => esc_html__('Hover', 'ml-slider'),
                        'name' => 'play_button_icon_hover',
                        'type' => 'color',
                        'default' => '#ffffff',
                        'css' => '[ms_id] .flexslider .flex-pauseplay a:hover:before { color: [ms_value] }'
                    )
                ),
            ),
            array(
                'label' => esc_html__('Border Radius', 'ml-slider'),
                'info' => esc_html__("The roundness of the play/pause button's corners. 50% makes it circular.", 'ml-slider'),
                'name' => 'play_button_border_radius',
                'type' => 'range',
                'default' => 50,
                'metric' => 'px',
                'min' => 0,
                'max' => 50,
                'css' => '[ms_id] .flexslider .flex-pauseplay a { border-radius: [ms_value]px }'
            ),
            array(
                'label' => esc_html__('Opacity (default)', 'ml-slider'),
                'info' => esc_html__('The transparency of the play/pause button when not hovered.', 'ml-slider'),
                'name' => 'play_button_opacity',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-pauseplay a { opacity: [ms_value] }'
            ),
            array(
                'label' => esc_html__('Opacity (hover)', 'ml-slider'),
                'info' => esc_html__('The transparency of the play/pause button when hovered.', 'ml-slider'),
                'name' => 'play_button_opacity_hover',
                'type' => 'range',
                'default' => 1,
                'min' => 0.1,
                'max' => 1,
                'step' => 0.1,
                'css' => '[ms_id] .flexslider .flex-pauseplay a:hover { opacity: [ms_value] }'
            ),
            array(
                'label' => esc_html__('Vertical Position', 'ml-slider'),
                'info' => esc_html__('Whether the play/pause button is positioned nearer the top or bottom of the slideshow.', 'ml-slider'),
                'name' => 'play_button_vertical_position',
                'type' => 'select',
                'default' => 'top',
                'options' => array(
                    array(
                        'label' => esc_html__('Top', 'ml-slider'),
                        'value' => 'top'
                    ),
                    array(
                        'label' => esc_html__('Bottom', 'ml-slider'),
                        'value' => 'bottom'
                    )
                ),
                'css' => 'css_rules',
                'css_rules' => array(
                    'top' => '[ms_id] .flexslider .flex-pauseplay a { bottom: unset; top: [ms_field_value]px }', // Take [ms_field_value] from play_button_vertical_position_offset
                    'bottom' => '[ms_id] .flexslider .flex-pauseplay a { top: unset; bottom: [ms_field_value]px }' // Take [ms_field_value] from play_button_vertical_position_offset
                )
            ),
            array(
                'label' => esc_html__('Vertical Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the play/pause button sits from the top or bottom of the slideshow, depending on the "Vertical Position" setting.', 'ml-slider'),
                'name' => 'play_button_vertical_position_offset',
                'type' => 'range',
                'default' => 10,
                'metric' => 'px',
                'min' => -100,
                'max' => 500,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'play_button_vertical_position'
            ),
            array(
                'label' => esc_html__('Horizontal Position', 'ml-slider'),
                'info' => esc_html__('Whether the play/pause button is positioned nearer the left or right of the slideshow.', 'ml-slider'),
                'name' => 'play_button_horizontal_position',
                'type' => 'select',
                'default' => 'left',
                'options' => array(
                    array(
                        'label' => esc_html__('Right', 'ml-slider'),
                        'value' => 'right'
                    ),
                    array(
                        'label' => esc_html__('Left', 'ml-slider'),
                        'value' => 'left'
                    )
                ),
                'css' => 'css_rules', // refer to css_rules where 'value' => '.lorem {}' is based on 'options' value
                'css_rules' => array(
                    'right' => '[ms_id] .flexslider .flex-pauseplay a { left: unset; right: [ms_field_value]px }', // Take [ms_field_value] from play_button_horizontal_position_offset
                    'left' => '[ms_id] .flexslider .flex-pauseplay a { right: unset; left: [ms_field_value]px }', // Take [ms_field_value] from play_button_horizontal_position_offset
                ),
            ),
            array(
                'label' => esc_html__('Horizontal Position Offset', 'ml-slider'),
                'info' => esc_html__('How far the play/pause button sits from the left or right of the slideshow, depending on the "Horizontal Position" setting.', 'ml-slider'),
                'name' => 'play_button_horizontal_position_offset',
                'type' => 'range',
                'default' => 10,
                'metric' => 'px',
                'min' => -100,
                'max' => 500,
                'css' => 'css_field', // Use the CSS from another field defined at 'css_field'
                'css_field' => 'play_button_horizontal_position'
            ),
        )
    )
);
