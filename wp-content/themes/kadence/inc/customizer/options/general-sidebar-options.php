<?php
/**
 * Sidebar Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

ob_start(); ?>
<div class="kadence-compontent-description">
	<p style="margin:0"><?php echo esc_html__( 'Title and Content settings affect legacy widgets. For block editor widgets use settings in the editor.', 'kadence' ); ?></p>
</div>
<?php
$component_description = ob_get_clean();

Theme_Customizer::add_settings(
	[
		'sidebar_tabs'               => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'sidebar',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'sidebar',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'sidebar_design',
				],
				'active'  => 'general',
			],
		],
		'sidebar_tabs_design'        => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'sidebar_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'sidebar',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'sidebar_design',
				],
				'active'  => 'design',
			],
		],
		'sidebar_width'              => [
			'control_type' => 'kadence_range_control',
			'section'      => 'sidebar',
			'priority'     => 10,
			'label'        => esc_html__( 'Sidebar Width', 'kadence' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'general',
			// ),
			// ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.has-sidebar:not(.has-left-sidebar) .content-container',
					'property' => 'grid-template-columns',
					'pattern'  => '1fr $',
					'key'      => 'size',
				],
				[
					'type'     => 'css',
					'selector' => '.has-sidebar.has-left-sidebar .content-container',
					'property' => 'grid-template-columns',
					'pattern'  => '$ 1fr',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'sidebar_width' ),
			'input_attrs'  => [
				'min'        => [
					'px'  => 100,
					'em'  => 8,
					'rem' => 8,
					'%'   => 5,
				],
				'max'        => [
					'px'  => 600,
					'em'  => 30,
					'rem' => 30,
					'%'   => 60,
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
		'sidebar_widget_spacing'     => [
			'control_type' => 'kadence_range_control',
			'section'      => 'sidebar',
			'priority'     => 10,
			'label'        => esc_html__( 'Widget Spacing', 'kadence' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'general',
			// ),
			// ),
			'live_method'  => [
				[
					'type'     => 'css',
					'property' => 'margin-bottom',
					'selector' => '.primary-sidebar.widget-area .widget',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'sidebar_widget_spacing' ),
			'input_attrs'  => [
				'min'   => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
				],
				'max'   => [
					'px'  => 200,
					'em'  => 8,
					'rem' => 8,
				],
				'step'  => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
				],
				'units' => [ 'px', 'em', 'rem' ],
			],
		],
		'sidebar_widget_settings'    => [
			'control_type' => 'kadence_blank_control',
			'section'      => 'sidebar_design',
			'settings'     => false,
			'priority'     => 1,
			'description'  => $component_description,
		],
		'sidebar_widget_title'       => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Widget Titles', 'kadence' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
			'default'      => kadence()->default( 'sidebar_widget_title' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.primary-sidebar.widget-area .widget-title',
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
				'id' => 'sidebar_widget_title',
			],
		],
		'sidebar_widget_content'     => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Widget Content', 'kadence' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
			'default'      => kadence()->default( 'sidebar_widget_content' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.primary-sidebar.widget-area .widget',
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
				'id' => 'sidebar_widget_content',
			],
		],
		'sidebar_link_style'         => [
			'control_type' => 'kadence_select_control',
			'section'      => 'sidebar_design',
			'default'      => kadence()->default( 'sidebar_link_style' ),
			'label'        => esc_html__( 'Link Style', 'kadence' ),
			'input_attrs'  => [
				'options' => [
					'normal'    => [
						'name' => __( 'Underline on Hover', 'kadence' ),
					],
					'underline' => [
						'name' => __( 'Underline', 'kadence' ),
					],
					'plain'     => [
						'name' => __( 'No Underline', 'kadence' ),
					],
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.primary-sidebar',
					'pattern'  => 'sidebar-link-style-$',
					'key'      => '',
				],
			],
		],
		'sidebar_link_colors'        => [
			'control_type' => 'kadence_color_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Link Colors', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_link_colors' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area .sidebar-inner-wrap a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area .sidebar-inner-wrap a:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				],
			],
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
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
		'sidebar_background'         => [
			'control_type' => 'kadence_background_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Sidebar Background', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_background' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => '.primary-sidebar.widget-area',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Sidebar Background', 'kadence' ),
			],
		],
		'sidebar_divider_border'     => [
			'control_type' => 'kadence_border_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Sidebar Divider Border', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_divider_border' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '.has-sidebar.has-left-sidebar .primary-sidebar.widget-area',
					'pattern'  => '$',
					'property' => 'border-right',
					'pattern'  => '$',
					'key'      => 'border',
				],
				[
					'type'     => 'css_border',
					'selector' => '.has-sidebar:not(.has-left-sidebar) .primary-sidebar.widget-area',
					'pattern'  => '$',
					'property' => 'border-left',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
		'sidebar_padding'            => [
			'control_type' => 'kadence_measure_control',
			'section'      => 'sidebar_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'sidebar_padding' ),
			'label'        => esc_html__( 'Sidebar Padding', 'kadence' ),
			// 'context'      => array(
			// array(
			// 'setting' => '__current_tab',
			// 'value'   => 'design',
			// ),
			// ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				],
			],
			'input_attrs'  => [
				'responsive' => true,
			],
		],
		'sidebar_sticky'             => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'sidebar',
			'default'      => kadence()->default( 'sidebar_sticky' ),
			'label'        => esc_html__( 'Enable Sticky Sidebar', 'kadence' ),
			'transport'    => 'refresh',
		],
		'sidebar_sticky_last_widget' => [
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'sidebar',
			'default'      => kadence()->default( 'sidebar_sticky_last_widget' ),
			'label'        => esc_html__( 'Only Stick Last Widget', 'kadence' ),
			'transport'    => 'refresh',
			'context'      => [
				[
					'setting' => 'sidebar_sticky',
					'value'   => true,
				],
			],
		],
	]
);
