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
<!-- <div class="kadence-build-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab preview-desktop kadence-build-tabs-button" data-device="desktop">
		<span class="dashicons dashicons-desktop"></span>
		<span><?php esc_html_e( 'Desktop', 'kadence' ); ?></span>
	</a>
	<a href="#" class="nav-tab preview-tablet preview-mobile kadence-build-tabs-button" data-device="tablet">
		<span class="dashicons dashicons-smartphone"></span>
		<span><?php esc_html_e( 'Tablet / Mobile', 'kadence' ); ?></span>
	</a>
</div> -->
<span class="button button-secondary kadence-builder-hide-button kadence-builder-tab-toggle"><span class="dashicons dashicons-no"></span><?php esc_html_e( 'Hide', 'kadence' ); ?></span>
<span class="button button-secondary kadence-builder-show-button kadence-builder-tab-toggle"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Footer Builder', 'kadence' ); ?></span>
<?php
$builder_tabs = ob_get_clean();
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
$settings = array(
	'footer_builder' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'footer_builder',
		'settings'     => false,
		'description'  => $builder_tabs,
	),
	'footer_items' => array(
		'control_type' => 'kadence_builder_control',
		'section'      => 'footer_builder',
		'default'      => kadence()->default( 'footer_items' ),
		'partial'      => array(
			'selector'            => '#colophon',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\footer_markup',
		),
		'choices'      => array(
			'footer-navigation'          => array(
				'name'    => esc_html__( 'Footer Navigation', 'kadence' ),
				'section' => 'kadence_customizer_footer_navigation',
			),
			'footer-social'        => array(
				'name'    => esc_html__( 'Social', 'kadence' ),
				'section' => 'kadence_customizer_footer_social',
			),
			'footer-html'          => array(
				'name'    => esc_html__( 'Copyright', 'kadence' ),
				'section' => 'kadence_customizer_footer_html',
			),
			'footer-widget1' => array(
				'name'    => esc_html__( 'Widget 1', 'kadence' ),
				'section' => 'sidebar-widgets-footer1',
			),
			'footer-widget2' => array(
				'name'    => esc_html__( 'Widget 2', 'kadence' ),
				'section' => 'sidebar-widgets-footer2',
			),
			'footer-widget3' => array(
				'name'    => esc_html__( 'Widget 3', 'kadence' ),
				'section' => 'sidebar-widgets-footer3',
			),
			'footer-widget4' => array(
				'name'    => esc_html__( 'Widget 4', 'kadence' ),
				'section' => 'sidebar-widgets-footer4',
			),
			'footer-widget5' => array(
				'name'    => esc_html__( 'Widget 5', 'kadence' ),
				'section' => 'sidebar-widgets-footer5',
			),
			'footer-widget6' => array(
				'name'    => esc_html__( 'Widget 6', 'kadence' ),
				'section' => 'sidebar-widgets-footer6',
			),
		),
		'input_attrs'  => array(
			'group' => 'footer_items',
			'rows'  => array( 'top', 'middle', 'bottom' ),
			'zones' => array(
				'top' => array(
					'top_1' => esc_html__( 'Top - 1', 'kadence' ),
					'top_2' => esc_html__( 'Top - 2', 'kadence' ),
					'top_3' => esc_html__( 'Top - 3', 'kadence' ),
					'top_4' => esc_html__( 'Top - 4', 'kadence' ),
					'top_5' => esc_html__( 'Top - 5', 'kadence' ),
				),
				'middle' => array(
					'middle_1' => esc_html__( 'Middle - 1', 'kadence' ),
					'middle_2' => esc_html__( 'Middle - 2', 'kadence' ),
					'middle_3' => esc_html__( 'Middle - 3', 'kadence' ),
					'middle_4' => esc_html__( 'Middle - 4', 'kadence' ),
					'middle_5' => esc_html__( 'Middle - 5', 'kadence' ),
				),
				'bottom' => array(
					'bottom_1' => esc_html__( 'Bottom - 1', 'kadence' ),
					'bottom_2' => esc_html__( 'Bottom - 2', 'kadence' ),
					'bottom_3' => esc_html__( 'Bottom - 3', 'kadence' ),
					'bottom_4' => esc_html__( 'Bottom - 4', 'kadence' ),
					'bottom_5' => esc_html__( 'Bottom - 5', 'kadence' ),
				),
			),
		),
	),
	'footer_tab_settings' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'footer_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'footer_available_items' => array(
		'control_type' => 'kadence_available_control',
		'section'      => 'footer_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'footer_items',
			'zones'  => array( 'top', 'middle', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'footer_wrap_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'footer_layout',
		'label'        => esc_html__( 'Footer Background', 'kadence' ),
		'default'      => kadence()->default( 'footer_wrap_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#colophon',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Footer Background', 'kadence' ),
		),
	),
	'enable_footer_on_bottom' => array(
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'footer_layout',
		'default'      => kadence()->default( 'enable_footer_on_bottom' ),
		'label'        => esc_html__( 'Keep footer on bottom of screen', 'kadence' ),
		'transport'    => 'refresh',
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
	),
);

Theme_Customizer::add_settings( $settings );

