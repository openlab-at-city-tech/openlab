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
$settings        = [
	'mobile_html_tabs'       => [
		'control_type' => 'kadence_blank_control',
		'section'      => 'mobile_html',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	],
	'mobile_html_content'    => [
		'control_type' => 'kadence_editor_control',
		'section'      => 'mobile_html',
		'sanitize'     => 'wp_kses_post',
		'priority'     => 4,
		'default'      => kadence()->default( 'mobile_html_content' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'input_attrs'  => [
			'id' => 'mobile_html',
		],
		'partial'      => [
			'selector'            => '.mobile-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_html',
		],
	],
	'mobile_html_wpautop'    => [
		'control_type' => 'kadence_switch_control',
		'sanitize'     => 'kadence_sanitize_toggle',
		'section'      => 'mobile_html',
		'default'      => kadence()->default( 'mobile_html_wpautop' ),
		'label'        => esc_html__( 'Automatically Add Paragraphs', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'general',
			],
		],
		'partial'      => [
			'selector'            => '.mobile-html',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\mobile_html',
		],
	],
	'mobile_html_typography' => [
		'control_type' => 'kadence_typography_control',
		'section'      => 'mobile_html',
		'label'        => esc_html__( 'Font', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'default'      => kadence()->default( 'mobile_html_typography' ),
		'live_method'  => [
			[
				'type'     => 'css_typography',
				'selector' => '.mobile-html',
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
			'id' => 'mobile_html_typography',
		],
	],
	'mobile_html_link_color' => [
		'control_type' => 'kadence_color_control',
		'section'      => 'mobile_html',
		'label'        => esc_html__( 'Link Colors', 'kadence' ),
		'default'      => kadence()->default( 'mobile_html_link_color' ),
		'live_method'  => [
			[
				'type'     => 'css',
				'selector' => '.mobile-html a',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			],
			[
				'type'     => 'css',
				'selector' => '.mobile-html a:hover',
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
	'mobile_html_link_style' => [
		'control_type' => 'kadence_select_control',
		'section'      => 'mobile_html',
		'default'      => kadence()->default( 'mobile_html_link_style' ),
		'label'        => esc_html__( 'Link Style', 'kadence' ),
		'context'      => [
			[
				'setting' => '__current_tab',
				'value'   => 'design',
			],
		],
		'input_attrs'  => [
			'options' => [
				'normal' => [
					'name' => __( 'Underline', 'kadence' ),
				],
				'plain'  => [
					'name' => __( 'No Underline', 'kadence' ),
				],
			],
		],
		'live_method'  => [
			[
				'type'     => 'class',
				'selector' => '#mobile-header .mobile-html',
				'pattern'  => 'inner-link-style-$',
				'key'      => '',
			],
		],
	],
	'mobile_html_margin'     => [
		'control_type' => 'kadence_measure_control',
		'section'      => 'mobile_html',
		'priority'     => 10,
		'default'      => kadence()->default( 'mobile_html_margin' ),
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
				'selector' => '.mobile-html',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			],
		],
		'input_attrs'  => [
			'responsive' => false,
		],
	],
];

Theme_Customizer::add_settings( $settings );

