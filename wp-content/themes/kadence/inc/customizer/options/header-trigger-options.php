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
	'mobile_trigger_tabs'        => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_trigger',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_trigger',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_trigger_design',
			],
			'active'  => 'general',
		],
	],
	'mobile_trigger_tabs_design' => [
		'control_type' => 'kadence_tab_control',
		'section'      => 'mobile_trigger_design',
		'settings'     => false,
		'priority'     => 1,
		'input_attrs'  => [
			'general' => [
				'label'  => __( 'General', 'kadence' ),
				'target' => 'mobile_trigger',
			],
			'design'  => [
				'label'  => __( 'Design', 'kadence' ),
				'target' => 'mobile_trigger_design',
			],
			'active'  => 'design',
		],
	],
	'mobile_trigger_label'       => [
		'control_type' => 'kadence_text_control',
		'section'      => 'mobile_trigger',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 6,
		'default'      => kadence()->default( 'mobile_trigger_label' ),
		'label'        => esc_html__( 'Menu Label', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'html',
				'selector' => '.menu-toggle-label',
				'pattern'  => '$',
				'key'      => '',
			],
		],
	],
	'mobile_trigger_icon'        => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_trigger',
		'priority'     => 10,
		'default'      => kadence()->default( 'mobile_trigger_icon' ),
		'label'        => esc_html__( 'Trigger Icon', 'kadence' ),
		'partial'      => [
			'selector'            => '.menu-toggle-icon',
			'container_inclusive' => false,
			'render_callback'     => 'Kadence\popup_toggle',
		],
		'input_attrs'  => [
			'layout'     => [
				'menu'  => [
					'icon' => 'menu',
				],
				'menu2' => [
					'icon' => 'menu2',
				],
				'menu3' => [
					'icon' => 'menu3',
				],
			],
			'responsive' => false,
		],
	],
	'mobile_trigger_style'       => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'mobile_trigger_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'mobile_trigger_style' ),
		'label'        => esc_html__( 'Trigger Style', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.menu-toggle-open',
				'pattern'  => 'menu-toggle-style-$',
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'default'  => [
					'name' => __( 'Default', 'kadence' ),
				],
				'bordered' => [
					'name' => __( 'Bordered', 'kadence' ),
				],
			],
			'responsive' => false,
		],
	],
	'mobile_trigger_border'      => [
		'control_type' => 'kadence_border_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Border', 'kadence' ),
		'default'      => kadence()->default( 'mobile_trigger_border' ),
		'context'      => [
			[
				'setting'    => 'mobile_trigger_style',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'bordered',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.mobile-toggle-open-container .menu-toggle-style-bordered',
				'pattern'  => '$',
				'property' => 'border',
				'key'      => 'border',
			],
		],
		'input_attrs'  => [
			'color'      => false,
			'responsive' => false,
		],
	],
	'mobile_trigger_icon_size'   => [
		'control_type' => 'kadence_range_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open .menu-toggle-icon',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'mobile_trigger_icon_size' ),
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
	'mobile_trigger_color'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Colors', 'kadence' ),
		'default'      => kadence()->default( 'mobile_trigger_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open:hover',
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
	'mobile_trigger_background'  => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Background', 'kadence' ),
		'default'      => kadence()->default( 'mobile_trigger_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Background', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Hover Background', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'mobile_trigger_typography'  => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'mobile_trigger_design',
		'label'        => esc_html__( 'Trigger Font', 'kadence' ),
		'context'      => [
			[
				'setting'  => 'mobile_trigger_label',
				'operator' => '!empty',
				'value'    => '',
			],
		],
		'default'      => kadence()->default( 'mobile_trigger_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
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
			'id'      => 'mobile_trigger_typography',
			'options' => 'no-color',
		],
	],
	'mobile_trigger_padding'     => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'mobile_trigger_design',
		'priority'     => 10,
		'default'      => kadence()->default( 'mobile_trigger_padding' ),
		'label'        => esc_html__( 'Trigger Padding', 'kadence' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-toggle-open-container .menu-toggle-open',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'info_link_drawer_container' => [
		'control_type' => 'kadence_title_control',
		'section'      => 'mobile_trigger',
		'priority'     => 20,
		'label'        => esc_html__( 'Drawer Container Options', 'kadence' ),
		'settings'     => false,
	],
	'mobile_trigger_drawer_link' => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'mobile_trigger',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Drawer Container Options', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_header_popup',
		],
	],
];

Theme_Customizer::add_settings( $settings );
