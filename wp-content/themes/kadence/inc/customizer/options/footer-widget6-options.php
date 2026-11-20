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
</div>
<?php
$compontent_tabs = ob_get_clean();
$settings        = [
	'footer_widget6_breaker'        => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'sidebar-widgets-footer6',
		'settings'     => false,
		'priority'     => 5,
		'description'  => $compontent_tabs,
	],
	'footer_widget6_align'          => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sidebar-widgets-footer6',
		'label'        => esc_html__( 'Content Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_widget6_align' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-widget6',
				'pattern'  => [
					'desktop' => 'content-align-$',
					'tablet'  => 'content-tablet-align-$',
					'mobile'  => 'content-mobile-align-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'left'   => [
					'tooltip'  => __( 'Left Align', 'kadence' ),
					'dashicon' => 'editor-alignleft',
				],
				'center' => [
					'tooltip'  => __( 'Center Align', 'kadence' ),
					'dashicon' => 'editor-aligncenter',
				],
				'right'  => [
					'tooltip'  => __( 'Right Align', 'kadence' ),
					'dashicon' => 'editor-alignright',
				],
			],
			'responsive' => true,
		],
	],
	'footer_widget6_vertical_align' => [
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'sidebar-widgets-footer6',
		'label'        => esc_html__( 'Content Vertical Align', 'kadence' ),
		'priority'     => 5,
		'default'      => kadence()->default( 'footer_widget6_vertical_align' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '.footer-widget6',
				'pattern'  => [
					'desktop' => 'content-valign-$',
					'tablet'  => 'content-tablet-valign-$',
					'mobile'  => 'content-mobile-valign-$',
				],
				'key'      => '',
			],
		],
		'input_attrs'  => [
			'layout'     => [
				'top'    => [
					'tooltip' => __( 'Top Align', 'kadence' ),
					'icon'    => 'aligntop',
				],
				'middle' => [
					'tooltip' => __( 'Middle Align', 'kadence' ),
					'icon'    => 'alignmiddle',
				],
				'bottom' => [
					'tooltip' => __( 'Bottom Align', 'kadence' ),
					'icon'    => 'alignbottom',
				],
			],
			'responsive' => true,
		],
	],
];

Theme_Customizer::add_settings( $settings );

