<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

ob_start(); ?>
<div class="kadence-compontent-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab kadence-general-tab kadence-compontent-tabs-button nav-tab-active" data-tab="general">
		<span><?php esc_html_e( 'General', 'kadence' ); ?></span>
	</a>
	<a href="#" class="nav-tab kadence-design-tab kadence-compontent-tabs-button" data-tab="design">
		<span><?php esc_html_e( 'Design', 'kadence' ); ?></span>
	</a>
</div>
<?php
$compontent_tabs = ob_get_clean();
$settings        = [
	'header_search_tabs'             => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_search',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'header_search_label'            => [
		'control_type' => 'kadence_text_control',
		'section'      => 'header_search',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 6,
		'default'      => kadence()->default( 'header_search_label' ),
		'label'        => esc_html__( 'Search Label', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'html',
				'selector' => '.search-toggle-label',
				'pattern'  => '$',
				'key'      => '',
			],
		],
	],
	'header_search_label_visiblity'  => [
		'control_type' => 'kadence_check_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_label_visiblity' ),
		'label'        => esc_html__( 'Search Label Visibility', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'header_search_label',
				'operator' => '!empty',
				'value'    => '',
			],
		],
		'partial'      => [
			'selector'            => '.search-toggle-open-container',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_search',
		],
		'input_attrs'  => [
			'options' => [
				'desktop' => [
					'name' => __( 'Desktop', 'kadence' ),
					'icon' => 'desktop',
				],
				'tablet'  => [
					'name' => __( 'Tablet', 'kadence' ),
					'icon' => 'tablet',
				],
				'mobile'  => [
					'name' => __( 'Mobile', 'kadence' ),
					'icon' => 'smartphone',
				],
			],
		],
	],
	'header_search_icon'             => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_icon' ),
		'label'        => esc_html__( 'Search Icon', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'partial'      => [
			'selector'            => '.search-toggle-icon',
			'container_inclusive' => false,
			'render_callback'     => 'Kadence\search_toggle',
		],
		'input_attrs'  => [
			'layout'     => [
				'search'  => [
					'icon' => 'search',
				],
				'search2' => [
					'icon' => 'search2',
				],
			],
			'responsive' => false,
			'class'      => 'radio-icon-padding',
		],
	],
	'header_search_style'            => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_style' ),
		'label'        => esc_html__( 'Search Style', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.search-toggle-open',
				'pattern'  => 'search-toggle-style-$',
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
	'header_search_border'           => [
		'control_type' => 'kadence_border_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Border', 'kadence' ),
		'default'      => kadence()->default( 'header_search_border' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'    => 'header_search_style',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'bordered',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_border',
				'selector' => '.search-toggle-open-container .search-toggle-open.search-toggle-style-bordered',
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
	'header_search_icon_size'        => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open .search-toggle-icon',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			],
		],
		'default'      => kadence()->default( 'header_search_icon_size' ),
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
			'responsive' => true,
		],
	],
	'header_search_color'            => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_search_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
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
	'header_search_background'       => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Background', 'kadence' ),
		'default'      => kadence()->default( 'header_search_background' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open:hover',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
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
	'header_search_typography'       => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Label Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
			[
				'setting'  => 'header_search_label',
				'operator' => '!empty',
				'value'    => '',
			],
		],
		'default'      => kadence()->default( 'header_search_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.search-toggle-open-container .search-toggle-open',
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
			'id'      => 'header_search_typography',
			'options' => 'no-color',
		],
	],
	'header_search_padding'          => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_padding' ),
		'label'        => esc_html__( 'Search Padding', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'header_search_margin'           => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
	'info_search_modal'              => [
		'control_type' => 'kadence_title_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Modal Options', 'kadence' ),
		'settings'     => false,
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
	],
	'header_search_modal_color'      => [
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Text Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_search_modal_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '#search-drawer .drawer-inner .drawer-content form input.search-field, #search-drawer .drawer-inner .drawer-content form .kadence-search-icon-wrap, #search-drawer .drawer-header',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '#search-drawer .drawer-inner .drawer-content form input.search-field:focus, #search-drawer .drawer-inner .drawer-content form input.search-submit:hover ~ .kadence-search-icon-wrap, #search-drawer .drawer-inner .drawer-content form button[type="submit"]:hover ~ .kadence-search-icon-wrap',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'colors' => [
				'color' => [
					'tooltip' => __( 'Initial Color', 'kadence' ),
					'palette' => true,
				],
				'hover' => [
					'tooltip' => __( 'Focus/Hover Color', 'kadence' ),
					'palette' => true,
				],
			],
		],
	],
	'header_search_modal_background' => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Modal Background', 'kadence' ),
		'default'      => kadence()->default( 'header_search_modal_background' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#search-drawer .drawer-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Modal Background', 'kadence' ),
		],
	],
];
if ( class_exists( 'woocommerce' ) ) {
	$settings = array_merge(
		$settings,
		[
			'header_search_woo' => [
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => 'header_search',
				'priority'     => 10,
				'default'      => kadence()->default( 'header_search_woo' ),
				'label'        => esc_html__( 'Search only Products?', 'kadence' ),
				'transport'    => 'refresh',
				'context'      => [
					[
						'setting' => '__current_tab',
						'value'   => 'general',
					],
				],
			],
		]
	);
}

Theme_Customizer::add_settings( $settings );

