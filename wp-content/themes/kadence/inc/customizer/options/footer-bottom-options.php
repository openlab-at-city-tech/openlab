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

ob_start();
?>
<div class="kadence-compontent-description">
	<p style="margin:0"><?php echo esc_html__( 'Title and Content settings affect legacy widgets. For block editor widgets use settings in the editor.', 'kadence' ); ?></p>
</div>
<?php
$component_description = ob_get_clean();

Theme_Customizer::add_settings(
	[
		'footer_bottom_tabs'            => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'footer_bottom',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'footer_bottom',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'footer_bottom_design',
				],
				'active'  => 'general',
			],
		],
		'footer_bottom_tabs_design'     => [
			'control_type' => 'kadence_tab_control',
			'section'      => 'footer_bottom_design',
			'settings'     => false,
			'priority'     => 1,
			'input_attrs'  => [
				'general' => [
					'label'  => __( 'General', 'kadence' ),
					'target' => 'footer_bottom',
				],
				'design'  => [
					'label'  => __( 'Design', 'kadence' ),
					'target' => 'footer_bottom_design',
				],
				'active'  => 'design',
			],
		],
		'footer_bottom_contain'         => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 4,
			'default'      => kadence()->default( 'footer_bottom_contain' ),
			'label'        => esc_html__( 'Container Width', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.site-bottom-footer-wrap',
					'pattern'  => [
						'desktop' => 'site-footer-row-layout-$',
						'tablet'  => 'site-footer-row-tablet-layout-$',
						'mobile'  => 'site-footer-row-mobile-layout-$',
					],
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout' => [
					'standard'  => [
						'tooltip' => __( 'Background Fullwidth, Content Contained', 'kadence' ),
						'name'    => __( 'Standard', 'kadence' ),
						'icon'    => '',
					],
					'fullwidth' => [
						'tooltip' => __( 'Background & Content Fullwidth', 'kadence' ),
						'name'    => __( 'Fullwidth', 'kadence' ),
						'icon'    => '',
					],
					'contained' => [
						'tooltip' => __( 'Background & Content Contained', 'kadence' ),
						'name'    => __( 'Contained', 'kadence' ),
						'icon'    => '',
					],
				],
			],
		],
		'footer_bottom_columns'         => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'label'        => esc_html__( 'Columns', 'kadence' ),
			'priority'     => 5,
			// 'transport'    => 'refresh',
			'default'      => kadence()->default( 'footer_bottom_columns' ),
			'partial'      => [
				'selector'            => '#colophon',
				'container_inclusive' => true,
				'render_callback'     => 'Kadence\footer_markup',
			],
			'input_attrs'  => [
				'layout'     => [
					'1' => [
						'name' => __( '1', 'kadence' ),
					],
					'2' => [
						'name' => __( '2', 'kadence' ),
					],
					'3' => [
						'name' => __( '3', 'kadence' ),
					],
					'4' => [
						'name' => __( '4', 'kadence' ),
					],
					'5' => [
						'name' => __( '5', 'kadence' ),
					],
				],
				'responsive' => false,
				'footer'     => 'bottom',
			],
		],
		'footer_bottom_layout'          => [
			'control_type' => 'kadence_row_control',
			'section'      => 'footer_bottom',
			'label'        => esc_html__( 'Layout', 'kadence' ),
			'priority'     => 5,
			// 'transport'    => 'refresh',
			'default'      => kadence()->default( 'footer_bottom_layout' ),
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.site-top-footer-inner-wrap',
					'pattern'  => [
						'desktop' => 'site-footer-row-column-layout-$',
						'tablet'  => 'site-footer-row-tablet-column-layout-$',
						'mobile'  => 'site-footer-row-mobile-column-layout-$',
					],
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'responsive' => true,
				'footer'     => 'bottom',
			],
		],
		'footer_bottom_collapse'        => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'default'      => kadence()->default( 'footer_bottom_collapse' ),
			'label'        => esc_html__( 'Row Collapse', 'kadence' ),
			'context'      => [
				[
					'setting'  => '__device',
					'operator' => 'in',
					'value'    => [ 'tablet', 'mobile' ],
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => 'ft-ro-collapse-$',
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'normal' => [
						'name' => __( 'Left to Right', 'kadence' ),
						'icon' => '',
					],
					'rtl'    => [
						'name' => __( 'Right to Left', 'kadence' ),
						'icon' => '',
					],
				],
				'responsive' => false,
				'footer'     => 'bottom',
			],
		],
		'footer_bottom_direction'       => [
			'control_type' => 'kadence_radio_icon_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'default'      => kadence()->default( 'footer_bottom_direction' ),
			'label'        => esc_html__( 'Column Direction', 'kadence' ),
			'context'      => [
				[
					'setting' => '__current_tab',
					'value'   => 'general',
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => [
						'desktop' => 'ft-ro-dir-$',
						'tablet'  => 'ft-ro-t-dir-$',
						'mobile'  => 'ft-ro-m-dir-$',
					],
					'key'      => '',
				],
			],
			'input_attrs'  => [
				'layout'     => [
					'row'    => [
						'tooltip' => __( 'Left to Right', 'kadence' ),
						'name'    => __( 'Row', 'kadence' ),
						'icon'    => '',
					],
					'column' => [
						'tooltip' => __( 'Top to Bottom', 'kadence' ),
						'name'    => __( 'Column', 'kadence' ),
						'icon'    => '',
					],
				],
				'responsive' => true,
				'footer'     => 'bottom',
			],
		],
		'footer_bottom_column_spacing'  => [
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Column Spacing', 'kadence' ),
			'context'      => [
				[
					'setting' => '__current_tab',
					'value'   => 'general',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'property' => 'grid-column-gap',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'key'      => 'size',
				],
				[
					'type'     => 'css',
					'property' => 'grid-row-gap',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'footer_bottom_column_spacing' ),
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
		'footer_bottom_widget_spacing'  => [
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Widget Spacing', 'kadence' ),
			'context'      => [
				[
					'setting' => '__current_tab',
					'value'   => 'general',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'property' => 'margin-bottom',
					'selector' => '.site-bottom-footer-inner-wrap .widget',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'footer_bottom_widget_spacing' ),
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
		'footer_bottom_top_spacing'     => [
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Top Spacing', 'kadence' ),
			'context'      => [
				[
					'setting' => '__current_tab',
					'value'   => 'general',
				],
			],
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'padding-top',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'footer_bottom_top_spacing' ),
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
		'footer_bottom_bottom_spacing'  => [
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Bottom Spacing', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'padding-bottom',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'footer_bottom_bottom_spacing' ),
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
		'footer_bottom_height'          => [
			'control_type' => 'kadence_range_control',
			'section'      => 'footer_bottom',
			'priority'     => 5,
			'label'        => esc_html__( 'Min Height', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '#colophon .site-bottom-footer-inner-wrap',
					'pattern'  => '$',
					'property' => 'min-height',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'footer_bottom_height' ),
			'input_attrs'  => [
				'min'   => [
					'px'  => 10,
					'em'  => 1,
					'rem' => 1,
					'vh'  => 2,
				],
				'max'   => [
					'px'  => 400,
					'em'  => 12,
					'rem' => 12,
					'vh'  => 40,
				],
				'step'  => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'vh'  => 1,
				],
				'units' => [ 'px', 'em', 'rem', 'vh' ],
			],
		],
		'footer_bottom_widget_settings' => [
			'control_type' => 'kadence_blank_control',
			'section'      => 'footer_bottom_design',
			'settings'     => false,
			'priority'     => 1,
			'description'  => $component_description,
		],
		'footer_bottom_widget_title'    => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Widget Titles', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_widget_title' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner .widget-title',
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
				'id' => 'footer_bottom_widget_title',
			],
		],
		'footer_bottom_widget_content'  => [
			'control_type' => 'kadence_typography_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Widget Content', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_widget_content' ),
			'live_method'  => [
				[
					'type'     => 'css_typography',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
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
				'id' => 'footer_bottom_widget_content',
			],
		],
		'footer_bottom_link_colors'     => [
			'control_type' => 'kadence_color_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Link Colors', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_link_colors' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.site-footer .site-bottom-footer-wrap .site-footer-row-container-inner a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))',
					'property' => 'color',
					'pattern'  => '$',
					'key'      => 'color',
				],
				[
					'type'     => 'css',
					'selector' => '.site-footer .site-bottom-footer-wrap .site-footer-row-container-inner a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover',
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
		'footer_bottom_link_style'      => [
			'control_type' => 'kadence_select_control',
			'section'      => 'footer_bottom_design',
			'default'      => kadence()->default( 'footer_bottom_link_style' ),
			'label'        => esc_html__( 'Link Style', 'kadence' ),
			'input_attrs'  => [
				'options' => [
					'plain'  => [
						'name' => __( 'Underline on Hover', 'kadence' ),
					],
					'normal' => [
						'name' => __( 'Underline', 'kadence' ),
					],
					'noline' => [
						'name' => __( 'No Underline', 'kadence' ),
					],
				],
			],
			'live_method'  => [
				[
					'type'     => 'class',
					'selector' => '.site-bottom-footer-inner-wrap',
					'pattern'  => 'ft-ro-lstyle-$',
					'key'      => '',
				],
			],
		],
		'footer_bottom_background'      => [
			'control_type' => 'kadence_background_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Bottom Row Background', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_background' ),
			'live_method'  => [
				[
					'type'     => 'css_background',
					'selector' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
					'property' => 'background',
					'pattern'  => '$',
					'key'      => 'base',
				],
			],
			'input_attrs'  => [
				'tooltip' => __( 'Bottom Row Background', 'kadence' ),
			],
		],
		'footer_bottom_column_border'   => [
			'control_type' => 'kadence_border_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Column Border', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_column_border' ),
			'live_method'  => [
				[
					'type'     => 'css_border',
					'selector' => '.site-bottom-footer-inner-wrap .site-footer-section:not(:last-child):after',
					'pattern'  => '$',
					'property' => 'border-right',
					'pattern'  => '$',
					'key'      => 'border',
				],
			],
		],
		'footer_bottom_border'          => [
			'control_type' => 'kadence_borders_control',
			'section'      => 'footer_bottom_design',
			'label'        => esc_html__( 'Border', 'kadence' ),
			'default'      => kadence()->default( 'footer_bottom_border' ),
			'settings'     => [
				'border_top'    => 'footer_bottom_top_border',
				'border_bottom' => 'footer_bottom_bottom_border',
			],
			'live_method'  => [
				'footer_bottom_top_border'    => [
					[
						'type'     => 'css_border',
						'selector' => [
							'desktop' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'tablet'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'mobile'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
						],
						'pattern'  => [
							'desktop' => '$',
							'tablet'  => '$',
							'mobile'  => '$',
						],
						'property' => 'border-top',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
				'footer_bottom_bottom_border' => [ 
					[
						'type'     => 'css_border',
						'selector' => [
							'desktop' => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'tablet'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
							'mobile'  => '.site-bottom-footer-wrap .site-footer-row-container-inner',
						],
						'pattern'  => [
							'desktop' => '$',
							'tablet'  => '$',
							'mobile'  => '$',
						],
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
			],
		],
	]
);
