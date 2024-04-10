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
	'header_search_tabs' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'header_search',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_tabs,
	),
	'header_search_label' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'header_search',
		'sanitize'     => 'sanitize_text_field',
		'priority'     => 6,
		'default'      => kadence()->default( 'header_search_label' ),
		'label'        => esc_html__( 'Search Label', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'html',
				'selector' => '.search-toggle-label',
				'pattern'  => '$',
				'key'      => '',
			),
		),
	),
	'header_search_label_visiblity' => array(
		'control_type' => 'kadence_check_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_label_visiblity' ),
		'label'        => esc_html__( 'Search Label Visibility', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
			array(
				'setting'  => 'header_search_label',
				'operator' => '!empty',
				'value'    => '',
			),
		),
		'partial'      => array(
			'selector'            => '.search-toggle-open-container',
			'container_inclusive' => true,
			'render_callback'     => 'Kadence\header_search',
		),
		'input_attrs'  => array(
			'options' => array(
				'desktop' => array(
					'name' => __( 'Desktop', 'kadence' ),
					'icon' => 'desktop',
				),
				'tablet' => array(
					'name' => __( 'Tablet', 'kadence' ),
					'icon' => 'tablet',
				),
				'mobile' => array(
					'name' => __( 'Mobile', 'kadence' ),
					'icon' => 'smartphone',
				),
			),
		),
	),
	'header_search_icon' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_icon' ),
		'label'        => esc_html__( 'Search Icon', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		),
		'partial'      => array(
			'selector'            => '.search-toggle-icon',
			'container_inclusive' => false,
			'render_callback'     => 'Kadence\search_toggle',
		),
		'input_attrs'  => array(
			'layout' => array(
				'search' => array(
					'icon' => 'search',
				),
				'search2' => array(
					'icon' => 'search2',
				),
			),
			'responsive' => false,
			'class' => 'radio-icon-padding',
		),
	),
	'header_search_style' => array(
		'control_type' => 'kadence_radio_icon_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_style' ),
		'label'        => esc_html__( 'Search Style', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'class',
				'selector' => '.search-toggle-open',
				'pattern'  => 'search-toggle-style-$',
				'key'      => '',
			),
		),
		'input_attrs'  => array(
			'layout' => array(
				'default' => array(
					'name' => __( 'Default', 'kadence' ),
				),
				'bordered' => array(
					'name' => __( 'Bordered', 'kadence' ),
				),
			),
			'responsive' => false,
		),
	),
	'header_search_border' => array(
		'control_type' => 'kadence_border_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Border', 'kadence' ),
		'default'      => kadence()->default( 'header_search_border' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
			array(
				'setting'    => 'header_search_style',
				'operator'   => 'sub_object_contains',
				'sub_key'    => 'layout',
				'responsive' => false,
				'value'      => 'bordered',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_border',
				'selector' => '.search-toggle-open-container .search-toggle-open.search-toggle-style-bordered',
				'pattern'  => '$',
				'property' => 'border',
				'key'      => 'border',
			),
		),
		'input_attrs'  => array(
			'color'	     => false,
			'responsive' => false,
		),
	),
	'header_search_icon_size' => array(
		'control_type' => 'kadence_range_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Icon Size', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open .search-toggle-icon',
				'property' => 'font-size',
				'pattern'  => '$',
				'key'      => 'size',
			),
		),
		'default'      => kadence()->default( 'header_search_icon_size' ),
		'input_attrs'  => array(
			'min'        => array(
				'px'  => 0,
				'em'  => 0,
				'rem' => 0,
			),
			'max'        => array(
				'px'  => 100,
				'em'  => 12,
				'rem' => 12,
			),
			'step'       => array(
				'px'  => 1,
				'em'  => 0.01,
				'rem' => 0.01,
			),
			'units'      => array( 'px', 'em', 'rem' ),
			'responsive' => true,
		),
	),
	'header_search_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_search_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open:hover',
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
	'header_search_background' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Search Background', 'kadence' ),
		'default'      => kadence()->default( 'header_search_background' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open:hover',
				'property' => 'background',
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
					'tooltip' => __( 'Initial Background', 'kadence' ),
					'palette' => true,
				),
				'hover' => array(
					'tooltip' => __( 'Hover Background', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_search_typography' => array(
		'control_type' => 'kadence_typography_control',
		'section'      => 'header_search',
		'label'        => esc_html__( 'Label Font', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
			array(
				'setting'  => 'header_search_label',
				'operator' => '!empty',
				'value'    => '',
			),
		),
		'default'      => kadence()->default( 'header_search_typography' ),
		'live_method'     => array(
			array(
				'type'     => 'css_typography',
				'selector' => '.search-toggle-open-container .search-toggle-open',
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
			'id'      => 'header_search_typography',
			'options' => 'no-color',
		),
	),
	'header_search_padding' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_padding' ),
		'label'        => esc_html__( 'Search Padding', 'kadence' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'padding',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'header_search_margin' => array(
		'control_type' => 'kadence_measure_control',
		'section'      => 'header_search',
		'priority'     => 10,
		'default'      => kadence()->default( 'header_search_margin' ),
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
				'selector' => '.search-toggle-open-container .search-toggle-open',
				'property' => 'margin',
				'pattern'  => '$',
				'key'      => 'measure',
			),
		),
		'input_attrs'  => array(
			'responsive' => false,
		),
	),
	'info_search_modal' => array(
		'control_type' => 'kadence_title_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Modal Options', 'kadence' ),
		'settings'     => false,
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
	),
	'header_search_modal_color' => array(
		'control_type' => 'kadence_color_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Text Colors', 'kadence' ),
		'default'      => kadence()->default( 'header_search_modal_color' ),
		'live_method'     => array(
			array(
				'type'     => 'css',
				'selector' => '#search-drawer .drawer-inner .drawer-content form input.search-field, #search-drawer .drawer-inner .drawer-content form .kadence-search-icon-wrap, #search-drawer .drawer-header',
				'property' => 'color',
				'pattern'  => '$',
				'key'      => 'color',
			),
			array(
				'type'     => 'css',
				'selector' => '#search-drawer .drawer-inner .drawer-content form input.search-field:focus, #search-drawer .drawer-inner .drawer-content form input.search-submit:hover ~ .kadence-search-icon-wrap, #search-drawer .drawer-inner .drawer-content form button[type="submit"]:hover ~ .kadence-search-icon-wrap',
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
					'tooltip' => __( 'Focus/Hover Color', 'kadence' ),
					'palette' => true,
				),
			),
		),
	),
	'header_search_modal_background' => array(
		'control_type' => 'kadence_background_control',
		'section'      => 'header_search',
		'priority'     => 20,
		'label'        => esc_html__( 'Modal Background', 'kadence' ),
		'default'      => kadence()->default( 'header_search_modal_background' ),
		'context'      => array(
			array(
				'setting' => '__current_tab',
				'value'   => 'design',
			),
		),
		'live_method'     => array(
			array(
				'type'     => 'css_background',
				'selector' => '#search-drawer .drawer-inner',
				'property' => 'background',
				'pattern'  => '$',
				'key'      => 'base',
			),
		),
		'input_attrs'  => array(
			'tooltip'  => __( 'Modal Background', 'kadence' ),
		),
	),
);
if ( class_exists( 'woocommerce' ) ) {
	$settings = array_merge(
		$settings,
		array(
			'header_search_woo' => array(
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => 'header_search',
				'priority'     => 10,
				'default'      => kadence()->default( 'header_search_woo' ),
				'label'        => esc_html__( 'Search only Products?', 'kadence' ),
				'transport'    => 'refresh',
				'context'      => array(
					array(
						'setting' => '__current_tab',
						'value'   => 'general',
					),
				),
			),
		)
	);
}

Theme_Customizer::add_settings( $settings );

