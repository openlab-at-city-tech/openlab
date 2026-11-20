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
$settings        = [
	'header_builder'                 => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_builder',
		'settings'     => false,
		'description'  => $builder_tabs,
		'context'      => [
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_desktop_items'           => [
		'control_type' => 'kadence_builder_control',
		'section'      => 'header_builder',
		'default'      => kadence()->default( 'header_desktop_items' ),
		'context'      => [
			[
				'setting' => '__device',
				'value'   => 'desktop',
			],
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
		'partial'      => [
			'selector'            => '#masthead',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_markup',
		],
		'choices'      => [
			'logo'         => [
				'name'    => esc_html__( 'Logo', 'kadence' ),
				'section' => 'title_tagline',
			],
			'navigation'   => [
				'name'    => esc_html__( 'Primary Navigation', 'kadence' ),
				'section' => 'kadence_customizer_primary_navigation',
			],
			'navigation-2' => [
				'name'    => esc_html__( 'Secondary Navigation', 'kadence' ),
				'section' => 'kadence_customizer_secondary_navigation',
			],
			'search'       => [
				'name'    => esc_html__( 'Search', 'kadence' ),
				'section' => 'kadence_customizer_header_search',
			],
			'button'       => [
				'name'    => esc_html__( 'Button', 'kadence' ),
				'section' => 'kadence_customizer_header_button',
			],
			'social'       => [
				'name'    => esc_html__( 'Social', 'kadence' ),
				'section' => 'kadence_customizer_header_social',
			],
			'html'         => [
				'name'    => esc_html__( 'HTML', 'kadence' ),
				'section' => 'kadence_customizer_header_html',
			],
		],
		'input_attrs'  => [
			'group' => 'header_desktop_items',
			'rows'  => [ 'top', 'main', 'bottom' ],
			'zones' => [
				'top'    => [
					'top_left'         => is_rtl() ? esc_html__( 'Top - Right', 'kadence' ) : esc_html__( 'Top - Left', 'kadence' ),
					'top_left_center'  => is_rtl() ? esc_html__( 'Top - Right Center', 'kadence' ) : esc_html__( 'Top - Left Center', 'kadence' ),
					'top_center'       => esc_html__( 'Top - Center', 'kadence' ),
					'top_right_center' => is_rtl() ? esc_html__( 'Top - Left Center', 'kadence' ) : esc_html__( 'Top - Right Center', 'kadence' ),
					'top_right'        => is_rtl() ? esc_html__( 'Top - Left', 'kadence' ) : esc_html__( 'Top - Right', 'kadence' ),
				],
				'main'   => [
					'main_left'         => is_rtl() ? esc_html__( 'Main - Right', 'kadence' ) : esc_html__( 'Main - Left', 'kadence' ),
					'main_left_center'  => is_rtl() ? esc_html__( 'Main - Right Center', 'kadence' ) : esc_html__( 'Main - Left Center', 'kadence' ),
					'main_center'       => esc_html__( 'Main - Center', 'kadence' ),
					'main_right_center' => is_rtl() ? esc_html__( 'Main - Left Center', 'kadence' ) : esc_html__( 'Main - Right Center', 'kadence' ),
					'main_right'        => is_rtl() ? esc_html__( 'Main - Left', 'kadence' ) : esc_html__( 'Main - Right', 'kadence' ),
				],
				'bottom' => [
					'bottom_left'         => is_rtl() ? esc_html__( 'Bottom - Right', 'kadence' ) : esc_html__( 'Bottom - Left', 'kadence' ),
					'bottom_left_center'  => is_rtl() ? esc_html__( 'Bottom - Right Center', 'kadence' ) : esc_html__( 'Bottom - Left Center', 'kadence' ),
					'bottom_center'       => esc_html__( 'Bottom - Center', 'kadence' ),
					'bottom_right_center' => is_rtl() ? esc_html__( 'Bottom - Left Center', 'kadence' ) : esc_html__( 'Bottom - Right Center', 'kadence' ),
					'bottom_right'        => is_rtl() ? esc_html__( 'Bottom - Left', 'kadence' ) : esc_html__( 'Bottom - Right', 'kadence' ),
				],
			],
		],
	],
	'header_tab_settings'            => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
		'context'      => [
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_desktop_available_items' => [
		'control_type' => 'kadence_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => [
			'group' => 'header_desktop_items',
			'zones' => [ 'top', 'main', 'bottom' ],
		],
		'context'      => [
			[
				'setting' => '__device',
				'value'   => 'desktop',
			],
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_mobile_items'            => [
		'control_type' => 'kadence_builder_control',
		'section'      => 'header_builder',
		'transport'    => 'refresh',
		'default'      => kadence()->default( 'header_mobile_items' ),
		'context'      => [
			[
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => [ 'tablet', 'mobile' ],
			],
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
		'partial'      => [
			'selector'            => '#mobile-header',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_header',
		],
		'choices'      => [
			'mobile-logo'       => [
				'name'    => esc_html__( 'Logo', 'kadence' ),
				'section' => 'title_tagline',
			],
			'mobile-navigation' => [
				'name'    => esc_html__( 'Mobile Navigation', 'kadence' ),
				'section' => 'kadence_customizer_mobile_navigation',
			],
			// 'mobile-navigation2'          => array(
			// 'name'    => esc_html__( 'Horizontal Navigation', 'kadence' ),
			// 'section' => 'mobile_horizontal_navigation',
			// ),
			'search'            => [
				'name'    => esc_html__( 'Search Toggle', 'kadence' ),
				'section' => 'kadence_customizer_header_search',
			],
			'mobile-button'     => [
				'name'    => esc_html__( 'Button', 'kadence' ),
				'section' => 'kadence_customizer_mobile_button',
			],
			'mobile-social'     => [
				'name'    => esc_html__( 'Social', 'kadence' ),
				'section' => 'kadence_customizer_mobile_social',
			],
			'mobile-html'       => [
				'name'    => esc_html__( 'HTML', 'kadence' ),
				'section' => 'kadence_customizer_mobile_html',
			],
			'popup-toggle'      => [
				'name'    => esc_html__( 'Trigger', 'kadence' ),
				'section' => 'kadence_customizer_mobile_trigger',
			],
		],
		'input_attrs'  => [
			'group' => 'header_mobile_items',
			'rows'  => [ 'popup', 'top', 'main', 'bottom' ],
			'zones' => [
				'popup'  => [
					'popup_content' => esc_html__( 'Popup Content', 'kadence' ),
				],
				'top'    => [
					'top_left'   => is_rtl() ? esc_html__( 'Top - Right', 'kadence' ) : esc_html__( 'Top - Left', 'kadence' ),
					'top_center' => esc_html__( 'Top - Center', 'kadence' ),
					'top_right'  => is_rtl() ? esc_html__( 'Top - Left', 'kadence' ) : esc_html__( 'Top - Right', 'kadence' ),
				],
				'main'   => [
					'main_left'   => is_rtl() ? esc_html__( 'Main - Right', 'kadence' ) : esc_html__( 'Main - Left', 'kadence' ),
					'main_center' => esc_html__( 'Main - Center', 'kadence' ),
					'main_right'  => is_rtl() ? esc_html__( 'Main - Left', 'kadence' ) : esc_html__( 'Main - Right', 'kadence' ),
				],
				'bottom' => [
					'bottom_left'   => is_rtl() ? esc_html__( 'Bottom - Right', 'kadence' ) : esc_html__( 'Bottom - Left', 'kadence' ),
					'bottom_center' => esc_html__( 'Bottom - Center', 'kadence' ),
					'bottom_right'  => is_rtl() ? esc_html__( 'Bottom - Left', 'kadence' ) : esc_html__( 'Bottom - Right', 'kadence' ),
				],
			],
		],
	],
	'header_mobile_available_items'  => [
		'control_type' => 'kadence_available_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'input_attrs'  => [
			'group' => 'header_mobile_items',
			'zones' => [ 'popup', 'top', 'main', 'bottom' ],
		],
		'context'      => [
			[
				'setting'  => '__device',
				'operator' => 'in',
				'value'    => [ 'tablet', 'mobile' ],
			],
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_transparent_link'        => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Transparent Header', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_transparent_header',
		],
		'context'      => [
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_sticky_link'             => [
		'control_type' => 'kadence_focus_button_control',
		'section'      => 'header_layout',
		'settings'     => false,
		'priority'     => 20,
		'label'        => esc_html__( 'Sticky Header', 'kadence' ),
		'input_attrs'  => [
			'section' => 'kadence_customizer_header_sticky',
		],
		'context'      => [
			[
				'setting'  => 'blocks_header',
				'operator' => '!=',
				'value'    => true,
			],
		],
	],
	'header_wrap_background'         => [
		'control_type' => 'kadence_background_control',
		'section'      => 'header_layout',
		'label'        => esc_html__( 'Header Background', 'kadence' ),
		'default'      => kadence()->default( 'header_wrap_background' ),
		'live_method'  => [
			[
				'type'     => 'css_background',
				'selector' => '#masthead',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			],
		],
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'tooltip' => __( 'Header Background', 'kadence' ),
		],
	],
	'header_mobile_switch'           => [
		'control_type' => 'kadence_range_control',
		'section'      => 'header_layout',
		'transport'    => 'refresh',
		'label'        => esc_html__( 'Screen size to switch to mobile header', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'default'      => kadence()->default( 'header_mobile_switch' ),
		'input_attrs'  => [
			'min'        => [
				'px' => 0,
			],
			'max'        => [
				'px' => 4000,
			],
			'step'       => [
				'px' => 1,
			],
			'units'      => [ 'px' ],
			'responsive' => false,
		],
	],
];
if ( defined( 'KADENCE_BLOCKS_VERSION' ) && version_compare( KADENCE_BLOCKS_VERSION, '3.3.0', '>=' ) ) {
	$settings['blocks_header']    = [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'header_layout',
		'priority'     => 30,
		'description'  => esc_html__( 'Enable Block Header', 'kadence' ),
		'default'      => kadence()->default( 'blocks_header' ),
		'label'        => esc_html__( 'Enable Block Header', 'kadence' ),
		'transport'    => 'refresh',
	];
	$settings['blocks_header_id'] = [
		'control_type' => 'kadence_select_control',
		'section'      => 'header_layout',
		'label'        => esc_html__( 'Header Block', 'kadence' ),
		'transport'    => 'refresh',
		'priority'     => 30,
		'default'      => kadence()->default( 'blocks_header_id' ),
		'input_attrs'  => [
			'options' => kadence()->block_header_options(),
		],
		'context'      => [
			[
				'setting'  => 'blocks_header',
				'operator' => '=',
				'value'    => true,
			],
		],
	];
} else {
	$settings['blocks_header'] = [];
}
Theme_Customizer::add_settings( $settings );

