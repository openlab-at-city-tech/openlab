<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$settings = [
	'footer_social_tabs'                 => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'footer_social',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'footer_social',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'footer_social_design',
			],
			'active'  => 'general',
		],
	],
	'footer_social_tabs_design'          => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'footer_social_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'footer_social',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'footer_social_design',
			],
			'active'  => 'design',
		],
	],
	'footer_social_title'                => [
		'control_type' => 'kadence_text_control',
		'section'      => 'footer_social',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 4,
		'label'        => esc_html__( 'Title', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_title' ),
		'partial'      => [
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\footer_social',
		],
	],
	'footer_social_items'                => [
		'control_type' => 'kadence_social_control',
		'section'      => 'footer_social',
		'priority'     => 6,
		'default'      => kadence()->default( 'footer_social_items' ),
		'label'        => esc_html__( 'Social Items', 'kadence' ),
		'partial'      => [
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\footer_social',
		],
	],
	'footer_social_show_label'           => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'footer_social',
		'priority'     => 8,
		'default'      => kadence()->default( 'footer_social_show_label' ),
		'label'        => esc_html__( 'Show Icon Label?', 'kadence' ),
		'partial'      => [
			'selector'            => '.footer-social-wrap',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\footer_social',
		],
	],
	'footer_social_item_spacing'         => [
		'control_type' => 'kadence_range_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Item Spacing', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_item_spacing' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .site-footer-wrap .footer-social-wrap .footer-social-inner-wrap',
				'property' => 'gap',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 50,
				'em'  => 3,
				'rem' => 3,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem' ],
			'responsive' => false,
		],
	],
	'footer_social_align'                => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Content Align', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'footer_social_align' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-social',
				'pattern'  => [
					'desktop' => 'content-align-$',
					'tablet'  => 'content-tablet-align-$',
					'mobile'  => 'content-mobile-align-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'left'   => [
					'tooltip'  => __( 'Left Align', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				],
				'center' => [
					'tooltip'  => __( 'Center Align', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				],
				'right'  => [
					'tooltip'  => __( 'Right Align', 'kadence' ),
					'dashicon' => 'editor-alignright',
				],
			],
			'responsive' => true,
		],
	],
	'footer_social_vertical_align'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_social',
		'label'        => esc_html__( 'Content Vertical Align', 'kadence' ),
		'priority'     => 10,
		'default'      => kadence()->default( 'footer_social_vertical_align' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-social',
				'pattern'  => [
					'desktop' => 'content-valign-$',
					'tablet'  => 'content-tablet-valign-$',
					'mobile'  => 'content-mobile-valign-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'top'    => [
					'tooltip' => __( 'Top Align', 'kadence' ),
					'icon'    => 'aligntop',
				],
				'middle' => [
					'tooltip' => __( 'Middle Align', 'kadence' ),
					'icon'    => 'alignmiddle',
				],
				'bottom' => [
					'tooltip' => __( 'Bottom Align', 'kadence' ),
					'icon'    => 'alignbottom',
				],
			],
			'responsive' => true,
		],
	],
	'footer_social_style'                => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_social_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'footer_social_style' ),
		'label'        => esc_html__( 'Social Style', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-social-inner-wrap',
				'pattern'  => 'social-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'filled'  => [
					'name' => __( 'Filled', 'kadence' ),
				],
				'outline' => [
					'name' => __( 'Outline', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'footer_social_icon_size'            => [
		'control_type' => 'kadence_range_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.footer-social-wrap .footer-social-inner-wrap',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'footer_social_icon_size' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			],
			'units'      => [ 'px', 'em', 'rem' ],
			'responsive' => false,
		],
	],
	'footer_social_brand'                => [
		'control_type' => 'kadence_select_control',
		'section'      => 'footer_social_design',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'footer_social_brand' ),
		'label'        => esc_html__( 'Use Brand Colors?', 'kadence' ),
		'input_attrs'  => [
			'options' => [
				''           => [
					'name' => __( 'No', 'kadence' ),
				],
				'always'     => [
					'name' => __( 'Yes', 'kadence' ),
				],
				'onhover'    => [
					'name' => __( 'On Hover', 'kadence' ),
				],
				'untilhover' => [
					'name' => __( 'Until Hover', 'kadence' ),
				],
			],
		],
	],
	'footer_social_color'                => [
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Colors', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'footer_social_background'           => [
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Background Colors', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting'  => 'footer_social_style',
				'operator' => '=',
				'value'    => 'filled',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'footer_social_border_colors'        => [
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border Colors', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_border' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover',
				'property' => 'border-color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'footer_social_border'               => [
		'control_type' => 'kadence_border_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border', 'kadence' ),
		'default'      => kadence()->default( 'footer_social_border' ),
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'border',
				'pattern'  => '$',
				'key'      => 'border',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
			'color'      => false,
		],
	],
	'footer_social_border_radius'        => [
		'control_type' => 'kadence_range_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Border Radius', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button',
				'property' => 'border-radius',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'footer_social_border_radius' ),
		'input_attrs'  => [
			'min'        => [
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
				'%'   => 0,
			],
			'max'        => [
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
				'%'   => 100,
			],
			'step'       => [
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
				'%'   => 1,
			],
			'units'      => [ 'px', 'em', 'rem', '%' ],
			'responsive' => false,
		],
	],
	'footer_social_typography'           => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'footer_social_design',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'footer_social_show_label',
				'operator' => '=',
				'value'    => true,
			],
		],
		'default'      => kadence()->default( 'footer_social_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.footer-social-wrap a.social-button .social-label',
				'pattern'  => [
					'desktop' => '$',
					'tablet'  => '$',
					'mobile'  => '$',
				],
				'property' => 'font',
				'key'      => 'typography',
			],
		],
		'input_attrs'  => [
			'id'      => 'footer_social_typography',
			'options' => 'no-color',
		],
	],
	'footer_social_margin'               => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'footer_social_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'footer_social_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#colophon .footer-social-wrap',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'footer_social_link_to_social_links' => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'footer_social',
		'settings'     => false,
		'priority'     => 25,
		'label'        => esc_html__( 'Set Social Links', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_general_social',
		],
	],
];

Theme_Customizer::add_settings( $settings );
