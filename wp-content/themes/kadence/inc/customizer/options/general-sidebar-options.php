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
	array(
		'sidebar_tabs' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'sidebar',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'sidebar',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'sidebar_design',
				),
				'active' => 'general',
			),
		),
		'sidebar_tabs_design' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'sidebar_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'sidebar',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'sidebar_design',
				),
				'active' => 'design',
			),
		),
		'sidebar_width' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'sidebar',
			'priority'     => 10,
			'label'        => esc_html__( 'Sidebar Width', 'kadence' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'general',
			// 	),
			// ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.has-sidebar:not(.has-left-sidebar) .content-container',
					'property' => 'grid-template-columns',
					'pattern'  => '1fr $',
					'key'      => 'size',
				),
				array(
					'type'     => 'css',
					'selector' => '.has-sidebar.has-left-sidebar .content-container',
					'property' => 'grid-template-columns',
					'pattern'  => '$ 1fr',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'sidebar_width' ),
			'input_attrs'  => array(
				'min'        => array(
					'px'  => 100,
					'em'  => 8,
					'rem' => 8,
					'%' => 5,
				),
				'max'        => array(
					'px'  => 600,
					'em'  => 30,
					'rem' => 30,
					'%'   => 60,
				),
				'step'       => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'%' => 1,
				),
				'units'      => array( 'px', 'em', 'rem', '%' ),
				'responsive' => false,
			),
		),
		'sidebar_widget_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'sidebar',
			'priority'     => 10,
			'label'        => esc_html__( 'Widget Spacing', 'kadence' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'general',
			// 	),
			// ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'property' => 'margin-bottom',
					'selector' => '.primary-sidebar.widget-area .widget',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'sidebar_widget_spacing' ),
			'input_attrs'  => array(
				'min'     => array(
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
				),
				'max'     => array(
					'px'  => 200,
					'em'  => 8,
					'rem' => 8,
				),
				'step'    => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
				),
				'units'   => array( 'px', 'em', 'rem' ),
			),
		),
		'sidebar_widget_settings' => array(
			'control_type' => 'kadence_blank_control',
			'section'      => 'sidebar_design',
			'settings'     => false,
			'priority'     => 1,
			'description'  => $component_description,
		),
		'sidebar_widget_title' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Widget Titles', 'kadence' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'default'      => kadence()->default( 'sidebar_widget_title' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.primary-sidebar.widget-area .widget-title',
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id' => 'sidebar_widget_title',
			),
		),
		'sidebar_widget_content' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Widget Content', 'kadence' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'default'      => kadence()->default( 'sidebar_widget_content' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.primary-sidebar.widget-area .widget',
					'pattern'  => array(
						'desktop' => '$',
						'tablet'  => '$',
						'mobile'  => '$',
					),
					'property' => 'font',
					'key'      => 'typography',
				),
			),
			'input_attrs'  => array(
				'id' => 'sidebar_widget_content',
			),
		),
		'sidebar_link_style' => array(
			'control_type' => 'kadence_select_control',
			'section'      => 'sidebar_design',
			'default'      => kadence()->default( 'sidebar_link_style' ),
			'label'        => esc_html__( 'Link Style', 'kadence' ),
			'input_attrs'  => array(
				'options' => array(
					'normal' => array(
						'name' => __( 'Underline on Hover', 'kadence' ),
					),
					'underline' => array(
						'name' => __( 'Underline', 'kadence' ),
					),
					'plain' => array(
						'name' => __( 'No Underline', 'kadence' ),
					),
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.primary-sidebar',
					'pattern'  => 'sidebar-link-style-$',
					'key'      => '',
				),
			),
		),
		'sidebar_link_colors' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Link Colors', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_link_colors' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area .sidebar-inner-wrap a',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area .sidebar-inner-wrap a:hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'input_attrs'  => array(
				'colors' => array(
					'color' => array(
						'tooltip' => __( 'Initial Color', 'kadence' ),
						'palette' => true,
					),
					'hover' => array(
						'tooltip' => __( 'Hover Color', 'kadence' ),
						'palette' => true,
					),
				),
			),
		),
		'sidebar_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Sidebar Background', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_background' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => '.primary-sidebar.widget-area',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Sidebar Background', 'kadence' ),
			),
		),
		'sidebar_divider_border' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'sidebar_design',
			'label'        => esc_html__( 'Sidebar Divider Border', 'kadence' ),
			'default'      => kadence()->default( 'sidebar_divider_border' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.has-sidebar.has-left-sidebar .primary-sidebar.widget-area',
					'pattern'  => '$',
					'property' => 'border-right',
					'pattern'  => '$',
					'key'      => 'border',
				),
				array(
					'type'     => 'css_border',
					'selector' => '.has-sidebar:not(.has-left-sidebar) .primary-sidebar.widget-area',
					'pattern'  => '$',
					'property' => 'border-left',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
		'sidebar_padding' => array(
			'control_type' => 'kadence_measure_control',
			'section'      => 'sidebar_design',
			'priority'     => 10,
			'default'      => kadence()->default( 'sidebar_padding' ),
			'label'        => esc_html__( 'Sidebar Padding', 'kadence' ),
			// 'context'      => array(
			// 	array(
			// 		'setting' => '__current_tab',
			// 		'value'   => 'design',
			// 	),
			// ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.primary-sidebar.widget-area',
					'property' => 'padding',
					'pattern'  => '$',
					'key'      => 'measure',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
			),
		),
		'sidebar_sticky' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'sidebar',
			'default'      => kadence()->default( 'sidebar_sticky' ),
			'label'        => esc_html__( 'Enable Sticky Sidebar', 'kadence' ),
			'transport'    => 'refresh',
		),
		'sidebar_sticky_last_widget' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'sidebar',
			'default'      => kadence()->default( 'sidebar_sticky_last_widget' ),
			'label'        => esc_html__( 'Only Stick Last Widget', 'kadence' ),
			'transport'    => 'refresh',
			'context'      => array(
				array(
					'setting' => 'sidebar_sticky',
					'value'   => true,
				),
			),
		),
	)
);
