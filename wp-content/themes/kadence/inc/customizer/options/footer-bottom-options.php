<?php
/**
 * Header Main Row Options
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
Theme_Customizer::add_settings(
	array(
		'footer_bottom_tabs' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'footer_bottom',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'footer_bottom',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'footer_bottom_design',
				),
				'active' => 'general',
			),
		),
		'footer_bottom_tabs_design' => array(
			'control_type' => 'kadence_tab_control',
			'section'      => 'footer_bottom_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => array(
				'general' => array(
					'label'  => __( 'General', 'kadence' ),
					'target' => 'footer_bottom',
				),
				'design' => array(
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'footer_bottom_design',
				),
				'active' => 'design',
			),
		),
		'footer_bottom_contain' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 4,
			'default'      => kadence()->default( 'footer_bottom_contain' ),
			'label'        => esc_html__( 'Container Width', 'kadence' ),
			'live_method'  => array(
				array(
					'type'     => 'class',
					'selector' => '.site-bottom-footer-wrap',
					'pattern'  => array(
						'desktop' => 'site-footer-row-layout-$',
						'tablet'  => 'site-footer-row-tablet-layout-$',
						'mobile'  => 'site-footer-row-mobile-layout-$',
					),
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'standard' => array(
						'tooltip' => __( 'Background Fullwidth, Content Contained', 'kadence' ),
						'name'    => __( 'Standard', 'kadence' ),
						'icon'    => '',
					),
					'fullwidth' => array(
						'tooltip' => __( 'Background & Content Fullwidth', 'kadence' ),
						'name'    => __( 'Fullwidth', 'kadence' ),
						'icon'    => '',
					),
					'contained' => array(
						'tooltip' => __( 'Background & Content Contained', 'kadence' ),
						'name'    => __( 'Contained', 'kadence' ),
						'icon'    => '',
					),
				),
			),
		),
		'footer_bottom_columns' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'label'        => esc_html__( 'Columns', 'kadence' ),
			'priority'     => 5,
			//'transport'    => 'refresh',
			'default'      => kadence()->default( 'footer_bottom_columns' ),
			'partial'      => array(
				'selector'            => '#colophon',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\footer_markup',
			),
			'input_attrs'  => array(
				'layout' => array(
					'1' => array(
						'name' => __( '1', 'kadence' ),
					),
					'2' => array(
						'name' => __( '2', 'kadence' ),
					),
					'3' => array(
						'name' => __( '3', 'kadence' ),
					),
					'4' => array(
						'name' => __( '4', 'kadence' ),
					),
					'5' => array(
						'name' => __( '5', 'kadence' ),
					),
				),
				'responsive' => false,
				'footer'     => 'bottom',
			),
		),
		'footer_bottom_layout' => array(
			'control_type' => 'kadence_row_control',
			'section'      => 'footer_bottom',
			'label'        => esc_html__( 'Layout', 'kadence' ),
			'priority'     => 5,
			//'transport'    => 'refresh',
			'default'      => kadence()->default( 'footer_bottom_layout' ),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.site-top-footer-inner-wrap',
					'pattern'  => array(
						'desktop' => 'site-footer-row-column-layout-$',
						'tablet'  => 'site-footer-row-tablet-column-layout-$',
						'mobile'  => 'site-footer-row-mobile-column-layout-$',
					),
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'responsive' => true,
				'footer'     => 'bottom',
			),
		),
		'footer_bottom_collapse' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'default'      => kadence()->default( 'footer_bottom_collapse' ),
			'label'        => esc_html__( 'Row Collapse', 'kadence' ),
			'context'      => array(
				array(
					'setting'  => '__device',
					'operator' => 'in',
					'value'    => array( 'tablet', 'mobile' ),
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => 'ft-ro-collapse-$',
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'normal' => array(
						'name'    => __( 'Left to Right', 'kadence' ),
						'icon'    => '',
					),
					'rtl' => array(
						'name'    => __( 'Right to Left', 'kadence' ),
						'icon'    => '',
					),
				),
				'responsive' => false,
				'footer'     => 'bottom',
			),
		),
		'footer_bottom_direction' => array(
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'default'      => kadence()->default( 'footer_bottom_direction' ),
			'label'        => esc_html__( 'Column Direction', 'kadence' ),
			'context'      => array(
				array(
					'setting' => '__current_tab',
					'value'   => 'general',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => array(
						'desktop' => 'ft-ro-dir-$',
						'tablet'  => 'ft-ro-t-dir-$',
						'mobile'  => 'ft-ro-m-dir-$',
					),
					'key'      => '',
				),
			),
			'input_attrs'  => array(
				'layout' => array(
					'row' => array(
						'tooltip' => __( 'Left to Right', 'kadence' ),
						'name'    => __( 'Row', 'kadence' ),
						'icon'    => '',
					),
					'column' => array(
						'tooltip' => __( 'Top to Bottom', 'kadence' ),
						'name'    => __( 'Column', 'kadence' ),
						'icon'    => '',
					),
				),
				'responsive' => true,
				'footer'     => 'bottom',
			),
		),
		'footer_bottom_column_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Column Spacing', 'kadence' ),
			'context'      => array(
				array(
					'setting' => '__current_tab',
					'value'   => 'general',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'property' => 'grid-column-gap',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'key'      => 'size',
				),
				array(
					'type'     => 'css',
					'property' => 'grid-row-gap',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'footer_bottom_column_spacing' ),
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
		'footer_bottom_widget_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Widget Spacing', 'kadence' ),
			'context'      => array(
				array(
					'setting' => '__current_tab',
					'value'   => 'general',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'property' => 'margin-bottom',
					'selector' => '.site-bottom-footer-inner-wrap .widget',
					'pattern'  => '$',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'footer_bottom_widget_spacing' ),
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
		'footer_bottom_top_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Top Spacing', 'kadence' ),
			'context'      => array(
				array(
					'setting' => '__current_tab',
					'value'   => 'general',
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'padding-top',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'footer_bottom_top_spacing' ),
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
		'footer_bottom_bottom_spacing' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Bottom Spacing', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'padding-bottom',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'footer_bottom_bottom_spacing' ),
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
		'footer_bottom_height' => array(
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Min Height', 'kadence' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'min-height',
					'key'      => 'size',
				),
			),
			'default'      => kadence()->default( 'footer_bottom_height' ),
			'input_attrs'  => array(
				'min'     => array(
					'px'  => 10,
					'em'  => 1,
					'rem' => 1,
					'vh'  => 2,
				),
				'max'     => array(
					'px'  => 400,
					'em'  => 12,
					'rem' => 12,
					'vh'  => 40,
				),
				'step'    => array(
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh'  => 1,
				),
				'units'   => array( 'px', 'em', 'rem', 'vh' ),
			),
		),
		'footer_bottom_widget_title' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Widget Titles', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_widget_title' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner .widget-title',
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
				'id' => 'footer_bottom_widget_title',
			),
		),
		'footer_bottom_widget_content' => array(
			'control_type' => 'kadence_typography_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Widget Content', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_widget_content' ),
			'live_method'     => array(
				array(
					'type'     => 'css_typography',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
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
				'id' => 'footer_bottom_widget_content',
			),
		),
		'footer_bottom_link_colors' => array(
			'control_type' => 'kadence_color_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Link Colors', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_link_colors' ),
			'live_method'     => array(
				array(
					'type'     => 'css',
					'selector' => '.site-footer .site-bottom-footer-wrap .site-footer-row-container-inner a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				),
				array(
					'type'     => 'css',
					'selector' => '.site-footer .site-bottom-footer-wrap .site-footer-row-container-inner a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'hover',
				),
			),
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
		'footer_bottom_link_style' => array(
			'control_type' => 'kadence_select_control',
			'section'      => 'footer_bottom_design',
			'default'      => kadence()->default( 'footer_bottom_link_style' ),
			'label'        => esc_html__( 'Link Style', 'kadence' ),
			'input_attrs'  => array(
				'options' => array(
					'plain' => array(
						'name' => __( 'Underline on Hover', 'kadence' ),
					),
					'normal' => array(
						'name' => __( 'Underline', 'kadence' ),
					),
					'noline' => array(
						'name' => __( 'No Underline', 'kadence' ),
					),
				),
			),
			'live_method'     => array(
				array(
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => 'ft-ro-lstyle-$',
					'key'      => '',
				),
			),
		),
		'footer_bottom_background' => array(
			'control_type' => 'kadence_background_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Bottom Row Background', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_background' ),
			'live_method'     => array(
				array(
					'type'     => 'css_background',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				),
			),
			'input_attrs'  => array(
				'tooltip'  => __( 'Bottom Row Background', 'kadence' ),
			),
		),
		'footer_bottom_column_border' => array(
			'control_type' => 'kadence_border_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Column Border', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_column_border' ),
			'live_method'     => array(
				array(
					'type'     => 'css_border',
					'selector' => '.site-bottom-footer-inner-wrap .site-footer-section:not(:last-child):after',
					'pattern'  => '$',
					'property' => 'border-right',
					'pattern'  => '$',
					'key'      => 'border',
				),
			),
		),
		'footer_bottom_border' => array(
			'control_type' => 'kadence_borders_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_border' ),
			'settings'     => array(
				'border_top'    => 'footer_bottom_top_border',
				'border_bottom' => 'footer_bottom_bottom_border',
			),
			'live_method'     => array(
				'footer_bottom_top_border' => array(
					array(
						'type'     => 'css_border',
						'selector' => array(
							'desktop' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'tablet'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'mobile'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
						),
						'pattern'  => array(
							'desktop' => '$',
							'tablet'  => '$',
							'mobile'  => '$',
						),
						'property' => 'border-top',
						'pattern'  => '$',
						'key'      => 'border',
					),
				),
				'footer_bottom_bottom_border' => array( 
					array(
						'type'     => 'css_border',
						'selector' => array(
							'desktop' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'tablet'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'mobile'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
						),
						'pattern'  => array(
							'desktop' => '$',
							'tablet'  => '$',
							'mobile'  => '$',
						),
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					),
				),
			),
		),
	)
);
