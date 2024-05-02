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
$settings        = array(
	'footer_html_tabs' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'footer_html',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'footer_html_content' => array(
		'control_type' => 'kadence_editor_control',
		'sanitize'     => 'wp_kses_post',
		'section'      => 'footer_html',
		'description'  => esc_html__( 'Available Placeholders: {copyright} {year} {site-title} {theme-credit}', 'kadence' ),
		'priority'     => 4,
		'default'      => kadence()->default( 'footer_html_content' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'input_attrs'  => array(
			'id' => 'footer_html',
		),
		'partial'      => array(
			'selector'            => '.footer-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\footer_html',
		),
	),
	'footer_html_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_html',
		'label'        => esc_html__( 'Content Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_html_align' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.site-info',
				'pattern'  => array(
					'desktop' => 'content-align-$',
					'tablet'  => 'content-tablet-align-$',
					'mobile'  => 'content-mobile-align-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'left'   => array(
					'tooltip'  => __( 'Left Align', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				),
				'center' => array(
					'tooltip'  => __( 'Center Align', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				),
				'right'  => array(
					'tooltip'  => __( 'Right Align', 'kadence' ),
					'dashicon' => 'editor-alignright',
				),
			),
			'responsive' => true,
		),
	),
	'footer_html_vertical_align' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'footer_html',
		'label'        => esc_html__( 'Content Vertical Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_html_vertical_align' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'  => array(
			array(
				'type'     => 'class',
				'selector' => '.site-info',
				'pattern'  => array(
					'desktop' => 'content-valign-$',
					'tablet'  => 'content-tablet-valign-$',
					'mobile'  => 'content-mobile-valign-$',
				),
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'top' => array(
					'tooltip' => __( 'Top Align', 'kadence' ),
					'icon'    => 'aligntop',
				),
				'middle' => array(
					'tooltip' => __( 'Middle Align', 'kadence' ),
					'icon'    => 'alignmiddle',
				),
				'bottom' => array(
					'tooltip' => __( 'Bottom Align', 'kadence' ),
					'icon'    => 'alignbottom',
				),
			),
			'responsive' => true,
		),
	),
	'footer_html_typography' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'footer_html',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => kadence()->default( 'footer_html_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '#colophon .footer-html',
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
			'id' => 'footer_html_typography',
		),
	),
	'footer_html_link_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'footer_html',
		'label'        => esc_html__( 'Link Colors', 'kadence' ),
		'default'      => kadence()->default( 'footer_html_link_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-footer-row-container .site-footer-row .footer-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#colophon .site-footer-row-container .site-footer-row .footer-html a:hover',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'hover',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
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
	'footer_html_link_style' => array(
		'control_type' => 'kadence_select_control',
		'section'      => 'footer_html',
		'default'      => kadence()->default( 'footer_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'kadence' ),
		'input_attrs'  => array(
			'options' => array(
				'normal' => array(
					'name' => __( 'Underline', 'kadence' ),
				),
				'plain' => array(
					'name' => __( 'No Underline', 'kadence' ),
				),
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '#colophon .footer-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			),
		),
	),
	'footer_html_margin' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'footer_html',
		'priority'     => 10,
		'default'      => kadence()->default( 'footer_html_margin' ),
		'label'        => esc_html__( 'Margin', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#colophon .footer-html',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

