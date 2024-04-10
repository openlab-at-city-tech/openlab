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
<div class="kadence-build-tabs nav-tab-wrapper wp-clearfix">
	<a href="#" class="nav-tab preview-desktop kadence-build-tabs-button" data-device="desktop">
		<span class="dashicons dashicons-desktop"></span>
		<span><?php esc_html_e( 'Desktop', 'kadence' ); ?></span>
	</a>
	<a href="#" class="nav-tab preview-tablet preview-mobile kadence-build-tabs-button" data-device="tablet">
		<span class="dashicons dashicons-smartphone"></span>
		<span><?php esc_html_e( 'Tablet / Mobile', 'kadence' ); ?></span>
	</a>
</div>
<span class="button button-secondary kadence-builder-hide-button kadence-builder-tab-toggle"><span class="dashicons dashicons-no"></span><?php esc_html_e( 'Hide', 'kadence' ); ?></span>
<span class="button button-secondary kadence-builder-show-button kadence-builder-tab-toggle"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Header Builder', 'kadence' ); ?></span>
<?php
$builder_tabs = ob_get_clean();
ob_start();
?>
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
	'header_builder' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_builder',
		'settings'     => false,
		'description'  => $builder_tabs,
	),
	'header_desktop_items' => array(
		'control_type' => 'kadence_builder_control',
		'section'      => 'header_builder',
		'default'      => kadence()->default( 'header_desktop_items' ),
		'context'      => array(
			array(
				'setting' => '__device',
				'value'   => 'desktop',
			),
		),
		'partial'      => array(
			'selector'            => '#masthead',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_markup',
		),
		'choices'      => array(
			'logo'          => array(
				'name'    => esc_html__( 'Logo', 'kadence' ),
				'section' => 'title_tagline',
			),
			'navigation'          => array(
				'name'    => esc_html__( 'Primary Navigation', 'kadence' ),
				'section' => 'kadence_customizer_primary_navigation',
			),
			'navigation-2'        => array(
				'name'    => esc_html__( 'Secondary Navigation', 'kadence' ),
				'section' => 'kadence_customizer_secondary_navigation',
			),
			'search' => array(
				'name'    => esc_html__( 'Search', 'kadence' ),
				'section' => 'kadence_customizer_header_search',
			),
			'button'        => array(
				'name'    => esc_html__( 'Button', 'kadence' ),
				'section' => 'kadence_customizer_header_button',
			),
			'social'        => array(
				'name'    => esc_html__( 'Social', 'kadence' ),
				'section' => 'kadence_customizer_header_social',
			),
			'html'          => array(
				'name'    => esc_html__( 'HTML', 'kadence' ),
				'section' => 'kadence_customizer_header_html',
			),
		),
		'input_attrs'  => array(
			'group' => 'header_desktop_items',
			'rows'  => array( 'top', 'main', 'bottom' ),
			'zones' => array(
				'top' => array(
					'top_left'         => is_rtl() ? esc_html__( 'Top - Right', 'kadence' ) : esc_html__( 'Top - Left', 'kadence' ),
					'top_left_center'  => is_rtl() ? esc_html__( 'Top - Right Center', 'kadence' ) : esc_html__( 'Top - Left Center', 'kadence' ),
					'top_center'       => esc_html__( 'Top - Center', 'kadence' ),
					'top_right_center' => is_rtl() ? esc_html__( 'Top - Left Center', 'kadence' ) : esc_html__( 'Top - Right Center', 'kadence' ),
					'top_right'        => is_rtl() ? esc_html__( 'Top - Left', 'kadence' ) : esc_html__( 'Top - Right', 'kadence' ),
				),
				'main' => array(
					'main_left'         => is_rtl() ? esc_html__( 'Main - Right', 'kadence' ) : esc_html__( 'Main - Left', 'kadence' ),
					'main_left_center'  => is_rtl() ? esc_html__( 'Main - Right Center', 'kadence' ) : esc_html__( 'Main - Left Center', 'kadence' ),
					'main_center'       => esc_html__( 'Main - Center', 'kadence' ),
					'main_right_center' => is_rtl() ? esc_html__( 'Main - Left Center', 'kadence' ) : esc_html__( 'Main - Right Center', 'kadence' ),
					'main_right'        => is_rtl() ? esc_html__( 'Main - Left', 'kadence' ) : esc_html__( 'Main - Right', 'kadence' ),
				),
				'bottom' => array(
					'bottom_left'         => is_rtl() ? esc_html__( 'Bottom - Right', 'kadence' ) : esc_html__( 'Bottom - Left', 'kadence' ),
					'bottom_left_center'  => is_rtl() ? esc_html__( 'Bottom - Right Center', 'kadence' ) : esc_html__( 'Bottom - Left Center', 'kadence' ),
					'bottom_center'       => esc_html__( 'Bottom - Center', 'kadence' ),
					'bottom_right_center' => is_rtl() ? esc_html__( 'Bottom - Left Center', 'kadence' ) : esc_html__( 'Bottom - Right Center', 'kadence' ),
					'bottom_right'        => is_rtl() ? esc_html__( 'Bottom - Left', 'kadence' ) : esc_html__( 'Bottom - Right', 'kadence' ),
				),
			),
		),
	),
	'header_tab_settings' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'header_desktop_available_items' => array(
		'control_type' => 'kadence_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'header_desktop_items',
			'zones'  => array( 'top', 'main', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting' => '__device',
				'value'   => 'desktop',
			),
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'header_mobile_items' => array(
		'control_type' => 'kadence_builder_control',
		'section'      => 'header_builder',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'header_mobile_items' ),
		'context'      => array(
			array(
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => array( 'tablet', 'mobile' ),
			),
		),
		'partial'      => array(
			'selector'            => '#mobile-header',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_header',
		),
		'choices'      => array(
			'mobile-logo'          => array(
				'name'    => esc_html__( 'Logo', 'kadence' ),
				'section' => 'title_tagline',
			),
			'mobile-navigation' => array(
				'name'    => esc_html__( 'Mobile Navigation', 'kadence' ),
				'section' => 'kadence_customizer_mobile_navigation',
			),
			// 'mobile-navigation2'          => array(
			// 	'name'    => esc_html__( 'Horizontal Navigation', 'kadence' ),
			// 	'section' => 'mobile_horizontal_navigation',
			// ),
			'search' => array(
				'name'    => esc_html__( 'Search Toggle', 'kadence' ),
				'section' => 'kadence_customizer_header_search',
			),
			'mobile-button'        => array(
				'name'    => esc_html__( 'Button', 'kadence' ),
				'section' => 'kadence_customizer_mobile_button',
			),
			'mobile-social'        => array(
				'name'    => esc_html__( 'Social', 'kadence' ),
				'section' => 'kadence_customizer_mobile_social',
			),
			'mobile-html'          => array(
				'name'    => esc_html__( 'HTML', 'kadence' ),
				'section' => 'kadence_customizer_mobile_html',
			),
			'popup-toggle'          => array(
				'name'    => esc_html__( 'Trigger', 'kadence' ),
				'section' => 'kadence_customizer_mobile_trigger',
			),
		),
		'input_attrs'  => array(
			'group' => 'header_mobile_items',
			'rows'  => array( 'popup', 'top', 'main', 'bottom' ),
			'zones' => array(
				'popup' => array(
					'popup_content' => esc_html__( 'Popup Content', 'kadence' ),
				),
				'top' => array(
					'top_left'   => is_rtl() ? esc_html__( 'Top - Right', 'kadence' ) : esc_html__( 'Top - Left', 'kadence' ),
					'top_center' => esc_html__( 'Top - Center', 'kadence' ),
					'top_right'  => is_rtl() ? esc_html__( 'Top - Left', 'kadence' ) : esc_html__( 'Top - Right', 'kadence' ),
				),
				'main' => array(
					'main_left'   => is_rtl() ? esc_html__( 'Main - Right', 'kadence' ) : esc_html__( 'Main - Left', 'kadence' ),
					'main_center' => esc_html__( 'Main - Center', 'kadence' ),
					'main_right'  => is_rtl() ? esc_html__( 'Main - Left', 'kadence' ) : esc_html__( 'Main - Right', 'kadence' ),
				),
				'bottom' => array(
					'bottom_left'   => is_rtl() ? esc_html__( 'Bottom - Right', 'kadence' ) : esc_html__( 'Bottom - Left', 'kadence' ),
					'bottom_center' => esc_html__( 'Bottom - Center', 'kadence' ),
					'bottom_right'  => is_rtl() ? esc_html__( 'Bottom - Left', 'kadence' ) : esc_html__( 'Bottom - Right', 'kadence' ),
				),
			),
		),
	),
	'header_mobile_available_items' => array(
		'control_type' => 'kadence_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => array(
			'group'  => 'header_mobile_items',
			'zones'  => array( 'popup', 'top', 'main', 'bottom' ),
		),
		'context'      => array(
			array(
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => array( 'tablet', 'mobile' ),
			),
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
	),
	'header_transparent_link' => array(
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Transparent Header', 'kadence' ),
		'input_attrs'  => array(
			'section' => 'kadence_customizer_transparent_header',
		),
	),
	'header_sticky_link' => array(
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Sticky Header', 'kadence' ),
		'input_attrs'  => array(
			'section' => 'kadence_customizer_header_sticky',
		),
	),
	'header_wrap_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'header_layout',
		'label'        => esc_html__( 'Header Background', 'kadence' ),
		'default'      => kadence()->default( 'header_wrap_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#masthead',
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
			'tooltip'  => __( 'Header Background', 'kadence' ),
		),
	),
	'header_mobile_switch' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_layout',
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Screen size to switch to mobile header', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'default'      => kadence()->default( 'header_mobile_switch' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
			),
			'max'        => array(
				'px'  => 4000,
			),
			'step'       => array(
				'px'  => 1,
			),
			'units'      => array( 'px' ),
			'responsive' => false,
		),
	),
);

Theme_Customizer::add_settings( $settings );

