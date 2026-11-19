<?php
/**
 * Header Main Row Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

$all_post_types    = kadence()->get_post_types_objects();
$extras_post_types = [];
$add_extras        = false;
foreach ( $all_post_types as $post_type_item ) {
	$post_type_name  = $post_type_item->name;
	$post_type_label = $post_type_item->label;
	$ignore_type     = kadence()->get_post_types_to_ignore();
	if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
		$taxonomies = get_object_taxonomies( $post_type_name, 'objects' );
		$taxs       = [];
		foreach ( $taxonomies as $term_slug => $term ) {
			if ( ! $term->public || ! $term->show_ui ) {
				continue;
			}
			$taxs[ $term_slug ] = $term->label;
		}
		$settings   = [
			$post_type_name . '_layout_tabs'             => [
				'control_type' => 'kadence_tab_control',
				'section'      => $post_type_name . '_layout',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => [
					'general' => [
						'label'  => __( 'General', 'kadence' ),
						'target' => $post_type_name . '_layout',
					],
					'design'  => [
						'label'  => __( 'Design', 'kadence' ),
						'target' => $post_type_name . '_layout_design',
					],
					'active'  => 'general',
				],
			],
			$post_type_name . '_layout_tabs_design'      => [
				'control_type' => 'kadence_tab_control',
				'section'      => $post_type_name . '_layout_design',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => [
					'general' => [
						'label'  => __( 'General', 'kadence' ),
						'target' => $post_type_name . '_layout',
					],
					'design'  => [
						'label'  => __( 'Design', 'kadence' ),
						'target' => $post_type_name . '_layout_design',
					],
					'active'  => 'design',
				],
			],
			'info_' . $post_type_name . '_title'         => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 2,
				'label'        => $post_type_label . ' ' . esc_html__( 'Title', 'kadence' ),
				'settings'     => false,
			],
			'info_' . $post_type_name . '_title_design'  => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_layout_design',
				'priority'     => 2,
				'label'        => $post_type_label . ' ' . esc_html__( 'Title', 'kadence' ),
				'settings'     => false,
			],
			$post_type_name . '_title'                   => [
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 3,
				'default'      => kadence()->default( $post_type_name . '_title', true ),
				'label'        => esc_html__( 'Show Title?', 'kadence' ),
				'transport'    => 'refresh',
			],
			$post_type_name . '_title_layout'            => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Layout', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 4,
				'default'      => kadence()->default( $post_type_name . '_title_layout', 'normal' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'normal' => [
							'tooltip' => __( 'In Content', 'kadence' ),
							'icon'    => 'incontent',
						],
						'above'  => [
							'tooltip' => __( 'Above Content', 'kadence' ),
							'icon'    => 'abovecontent',
						],
					],
					'responsive' => false,
					'class'      => 'kadence-two-col',
				],
			],
			$post_type_name . '_title_inner_layout'      => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 4,
				'default'      => kadence()->default( $post_type_name . '_title_inner_layout', 'standard' ),
				'label'        => esc_html__( 'Container Width', 'kadence' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-hero-section',
						'pattern'  => 'entry-hero-layout-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
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
					'responsive' => false,
				],
			],
			$post_type_name . '_title_align'             => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Align', 'kadence' ),
				'priority'     => 4,
				'default'      => kadence()->default( $post_type_name . '_title_align' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-title',
						'pattern'  => [
							'desktop' => 'title-align-$',
							'tablet'  => 'title-tablet-align-$',
							'mobile'  => 'title-mobile-align-$',
						],
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'left'   => [
							'tooltip'  => __( 'Left Align Title', 'kadence' ),
							'dashicon' => 'editor-alignleft',
						],
						'center' => [
							'tooltip'  => __( 'Center Align Title', 'kadence' ),
							'dashicon' => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip'  => __( 'Right Align Title', 'kadence' ),
							'dashicon' => 'editor-alignright',
						],
					],
					'responsive' => true,
				],
			],
			$post_type_name . '_title_height'            => [
				'control_type' => 'kadence_range_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 5,
				'label'        => esc_html__( 'Container Min Height', 'kadence' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '#inner-wrap .' . $post_type_name . '-hero-section .entry-header',
						'property' => 'min-height',
						'pattern'  => '$',
						'key'      => 'size',
					],
				],
				'default'      => kadence()->default(
					$post_type_name . '_title_height',
					[
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					] 
				),
				'input_attrs'  => [
					'min'   => [
						'px'  => 10,
						'em'  => 1,
						'rem' => 1,
						'vh'  => 2,
					],
					'max'   => [
						'px'  => 800,
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
			$post_type_name . '_title_elements'          => [
				'control_type' => 'kadence_sorter_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 6,
				'default'      => [ 'title', 'breadcrumb', 'meta' ],
				'label'        => esc_html__( 'Title Elements', 'kadence' ),
				'transport'    => 'refresh',
				'settings'     => [
					'elements'   => $post_type_name . '_title_elements',
					'title'      => $post_type_name . '_title_element_title',
					'breadcrumb' => $post_type_name . '_title_element_breadcrumb',
					'categories' => $post_type_name . '_title_element_categories',
					'meta'       => $post_type_name . '_title_element_meta',
				],
				'input_attrs'  => [
					'defaults'   => [
						'categories' => [
							'enabled'  => true,
							'style'    => 'normal',
							'divider'  => 'vline',
							'taxonomy' => '',
						],
						'title'      => [
							'enabled' => true,
						],
						'meta'       => [
							'id'                     => 'meta',
							'enabled'                => false,
							'divider'                => 'dot',
							'author'                 => true,
							'authorImage'            => true,
							'authorEnableLabel'      => true,
							'authorLabel'            => '',
							'date'                   => true,
							'dateTime'               => false,
							'dateEnableLabel'        => false,
							'dateLabel'              => '',
							'dateUpdated'            => false,
							'dateUpdatedTime'        => false,
							'dateUpdatedDifferent'   => false,
							'dateUpdatedEnableLabel' => false,
							'dateUpdatedLabel'       => '',
							'comments'               => false,
							'commentsCondition'      => false,
						],
						'breadcrumb' => [
							'enabled'    => false,
							'show_title' => true,
						],
					],
					'group'      => $post_type_name . '_title_element',
					'taxonomies' => $taxs,
				],
			],
			$post_type_name . '_title_font'              => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.wp-site-blocks .' . $post_type_name . '-title h1',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'             => $post_type_name . '_title_font',
					'headingInherit' => true,
				],
			],
			$post_type_name . '_title_category_color'    => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Category Colors', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_category_color',
					[
						'color' => '',
						'hover' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies, .' . $post_type_name . '-title .entry-taxonomies a',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies a:hover',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'hover',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a:hover',
						'property' => 'background',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_title_category_font'     => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Category Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_category_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .entry-taxonomies, .' . $post_type_name . '-title .entry-taxonomies a',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'      => $post_type_name . '_title_category_font',
					'options' => 'no-color',
				],
			],
			$post_type_name . '_title_breadcrumb_color'  => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_breadcrumb_color',
					[
						'color' => '',
						'hover' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .kadence-breadcrumbs',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .kadence-breadcrumbs a:hover',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_title_breadcrumb_font'   => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Breadcrumb Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_breadcrumb_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .kadence-breadcrumbs',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'      => $post_type_name . '_title_breadcrumb_font',
					'options' => 'no-color',
				],
			],
			$post_type_name . '_title_meta_color'        => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Meta Colors', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_title_meta_color' ),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-meta',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-title .entry-meta a:hover',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_title_meta_font'         => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Meta Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_meta_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.' . $post_type_name . '-title .entry-meta',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'      => $post_type_name . '_title_meta_font',
					'options' => 'no-color',
				],
			],
			$post_type_name . '_title_background'        => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => $post_type_label . ' ' . esc_html__( 'Title Background', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_background',
					[
						'desktop' => [
							'color' => '',
						],
					] 
				),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => '#inner-wrap .page-hero-section .entry-hero-container-inner',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => $post_type_name . ' ' . __( 'Title Background', 'kadence' ),
				],
			],
			$post_type_name . '_title_featured_image'    => [
				'control_type' => 'kadence_switch_control',
				'section'      => $post_type_name . '_layout_design',
				'default'      => kadence()->default( $post_type_name . '_title_featured_image' ),
				'label'        => esc_html__( 'Use Featured Image for Background?', 'kadence' ),
				'transport'    => 'refresh',
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
			],
			$post_type_name . '_title_overlay_color'     => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_title_overlay_color',
					[
						'color' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-hero-section .hero-section-overlay',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					],
				],
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'input_attrs'  => [
					'colors'        => [
						'color' => [
							'tooltip' => __( 'Overlay Color', 'kadence' ),
							'palette' => true,
						],
					],
					'allowGradient' => true,
				],
			],
			$post_type_name . '_title_top_border'        => [
				'control_type' => 'kadence_border_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Top Border', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_title_top_border' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css_border',
						'selector' => '.' . $post_type_name . '-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-top',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
			],
			$post_type_name . '_title_bottom_border'     => [
				'control_type' => 'kadence_border_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Bottom Border', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_title_bottom_border' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css_border',
						'selector' => '.' . $post_type_name . '-hero-section .entry-hero-container-inner',
						'pattern'  => '$',
						'property' => 'border-bottom',
						'pattern'  => '$',
						'key'      => 'border',
					],
				],
			],
			'info_' . $post_type_name . '_layout'        => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_layout',
				'priority'     => 10,
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'kadence' ),
				'settings'     => false,
			],
			'info_' . $post_type_name . '_layout_design' => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_layout_design',
				'priority'     => 10,
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'kadence' ),
				'settings'     => false,
			],
			$post_type_name . '_layout'                  => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => $post_type_label . ' ' . esc_html__( 'Layout', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_layout', 'normal' ),
				'input_attrs'  => [
					'layout'     => [
						'normal'    => [
							'tooltip' => __( 'Normal', 'kadence' ),
							'icon'    => 'normal',
						],
						'narrow'    => [
							'tooltip' => __( 'Narrow', 'kadence' ),
							'icon'    => 'narrow',
						],
						'fullwidth' => [
							'tooltip' => __( 'Fullwidth', 'kadence' ),
							'icon'    => 'fullwidth',
						],
						'left'      => [
							'tooltip' => __( 'Left Sidebar', 'kadence' ),
							'icon'    => 'leftsidebar',
						],
						'right'     => [
							'tooltip' => __( 'Right Sidebar', 'kadence' ),
							'icon'    => 'rightsidebar',
						],
					],
					'responsive' => false,
					'class'      => 'kadence-three-col',
				],
			],
			$post_type_name . '_sidebar_id'              => [
				'control_type' => 'kadence_select_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Default Sidebar', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_sidebar_id' ),
				'input_attrs'  => [
					'options' => kadence()->sidebar_options(),
				],
			],
			$post_type_name . '_content_style'           => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Content Style', 'kadence' ),
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_content_style', 'boxed' ),
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ),
						'pattern'  => 'content-style-$',
						'key'      => '',
					],
					[
						'type'     => 'class',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'pattern'  => 'content-style-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'boxed'   => [
							'tooltip' => __( 'Boxed', 'kadence' ),
							'icon'    => 'boxed',
						],
						'unboxed' => [
							'tooltip' => __( 'Unboxed', 'kadence' ),
							'icon'    => 'narrow',
						],
					],
					'responsive' => false,
					'class'      => 'kadence-two-col',
				],
			],
			$post_type_name . '_vertical_padding'        => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Content Vertical Padding', 'kadence' ),
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_vertical_padding', 'show' ),
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ),
						'pattern'  => 'content-vertical-padding-$',
						'key'      => '',
					],
					[
						'type'     => 'class',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'pattern'  => 'content-vertical-padding-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'show'   => [
							'name' => __( 'Enable', 'kadence' ),
						],
						'hide'   => [
							'name' => __( 'Disable', 'kadence' ),
						],
						'top'    => [
							'name' => __( 'Top Only', 'kadence' ),
						],
						'bottom' => [
							'name' => __( 'Bottom Only', 'kadence' ),
						],
					],
					'responsive' => false,
					'class'      => 'kadence-two-grid',
				],
			],
			$post_type_name . '_background'              => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Site Background', 'kadence' ),
				'default'      => kadence()->default( 'site_background' ),
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => 'body.single-' . str_replace( '_', '-', $post_type_name ),
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
					[
						'type'     => 'css_background',
						'selector' => 'body.' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ),
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => $post_type_label . ' ' . __( 'Background', 'kadence' ),
				],
			],
			$post_type_name . '_content_background'      => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_layout_design',
				'label'        => esc_html__( 'Content Background', 'kadence' ),
				'default'      => kadence()->default( 'content_background' ),
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => 'body.single-' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ) . ' .content-bg, body.single' . ( 'give_forms' === $post_type_name ? $post_type_name : str_replace( '_', '-', $post_type_name ) ) . '.content-style-unboxed .site',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => $post_type_label . ' ' . __( 'Content Background', 'kadence' ),
				],
			],
		];
		$add_extras = false;
		$extras     = [];
		if ( post_type_supports( $post_type_name, 'thumbnail' ) ) {
			$add_extras                                      = true;
			$extras[ $post_type_name . '_feature' ]          = [
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 20,
				'default'      => kadence()->default( $post_type_name . '_feature' ),
				'label'        => esc_html__( 'Show Featured Image?', 'kadence' ),
				'transport'    => 'refresh',
			];
			$extras[ $post_type_name . '_feature_position' ] = [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Featured Image Position', 'kadence' ),
				'priority'     => 20,
				'transport'    => 'refresh',
				'default'      => kadence()->default( $post_type_name . '_feature_position', 'above' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_feature',
						'operator' => '=',
						'value'    => true,
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'above'  => [
							'name' => __( 'Above', 'kadence' ),
						],
						'behind' => [
							'name' => __( 'Behind', 'kadence' ),
						],
						'below'  => [
							'name' => __( 'Below', 'kadence' ),
						],
					],
					'responsive' => false,
				],
			];
			$extras[ $post_type_name . '_feature_ratio' ]    = [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_layout',
				'label'        => esc_html__( 'Featured Image Ratio', 'kadence' ),
				'priority'     => 20,
				'default'      => kadence()->default( $post_type_name . '_feature_ratio', '2-3' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_feature',
						'operator' => '=',
						'value'    => true,
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => 'body.' . str_replace( '_', '-', $post_type_name ) . ' .article-post-thumbnail',
						'pattern'  => 'kadence-thumbnail-ratio-$',
						'key'      => '',
					],
					[
						'type'     => 'class',
						'selector' => 'body.single-' . str_replace( '_', '-', $post_type_name ) . ' .article-post-thumbnail',
						'pattern'  => 'kadence-thumbnail-ratio-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'inherit' => [
							'name' => __( 'Inherit', 'kadence' ),
						],
						'1-1'     => [
							'name' => __( '1:1', 'kadence' ),
						],
						'3-4'     => [
							'name' => __( '4:3', 'kadence' ),
						],
						'2-3'     => [
							'name' => __( '3:2', 'kadence' ),
						],
						'9-16'    => [
							'name' => __( '16:9', 'kadence' ),
						],
						'1-2'     => [
							'name' => __( '1:2', 'kadence' ),
						],
					],
					'responsive' => false,
					'class'      => 'kadence-three-col-short',
				],
			];
		}
		if ( post_type_supports( $post_type_name, 'comments' ) ) {
			$add_extras                              = true;
			$extras[ $post_type_name . '_comments' ] = [
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => $post_type_name . '_layout',
				'priority'     => 20,
				'default'      => kadence()->default( $post_type_name . '_comments' ),
				'label'        => esc_html__( 'Show Comments?', 'kadence' ),
				'transport'    => 'refresh',
			];
		}
		if ( $add_extras ) {
			$settings = array_merge(
				$settings,
				$extras
			);
		}
		// Archives
		$kadence_custom_post_type_archive_settings = [
			$post_type_name . '_archive_tabs'             => [
				'control_type' => 'kadence_tab_control',
				'section'      => $post_type_name . '_archive',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => [
					'general' => [
						'label'  => __( 'General', 'kadence' ),
						'target' => $post_type_name . '_archive',
					],
					'design'  => [
						'label'  => __( 'Design', 'kadence' ),
						'target' => $post_type_name . '_archive_design',
					],
					'active'  => 'general',
				],
			],
			$post_type_name . '_archive_tabs_design'      => [
				'control_type' => 'kadence_tab_control',
				'section'      => $post_type_name . '_archive_design',
				'settings'     => false,
				'priority'     => 1,
				'input_attrs'  => [
					'general' => [
						'label'  => __( 'General', 'kadence' ),
						'target' => $post_type_name . '_archive',
					],
					'design'  => [
						'label'  => __( 'Design', 'kadence' ),
						'target' => $post_type_name . '_archive_design',
					],
					'active'  => 'design',
				],
			],
			'info_' . $post_type_name . '_archive_title'  => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 2,
				'label'        => esc_html__( 'Archive Title', 'kadence' ),
				'settings'     => false,
			],
			'info_' . $post_type_name . '_archive_title_design' => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_archive_design',
				'priority'     => 2,
				'label'        => esc_html__( 'Archive Title', 'kadence' ),
				'settings'     => false,
			],
			$post_type_name . '_archive_title'            => [
				'control_type' => 'kadence_switch_control',
				'sanitize'     => 'kadence_sanitize_toggle',
				'section'      => $post_type_name . '_archive',
				'priority'     => 3,
				'default'      => kadence()->default( $post_type_name . '_archive_title', true ),
				'label'        => esc_html__( 'Show Archive Title?', 'kadence' ),
				'transport'    => 'refresh',
			],
			$post_type_name . '_archive_title_layout'     => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Title Layout', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 4,
				'default'      => kadence()->default( $post_type_name . '_archive_title_layout', 'above' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'normal' => [
							'name' => __( 'In Content', 'kadence' ),
							'icon' => 'incontent',
						],
						'above'  => [
							'name' => __( 'Above Content', 'kadence' ),
							'icon' => 'abovecontent',
						],
					],
					'responsive' => false,
					'class'      => 'kadence-two-col',
				],
			],
			$post_type_name . '_archive_title_inner_layout' => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 4,
				'default'      => kadence()->default( $post_type_name . '_archive_title_inner_layout', 'standard' ),
				'label'        => esc_html__( 'Container Width', 'kadence' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_archive_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive-hero-section',
						'pattern'  => 'entry-hero-layout-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
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
					'responsive' => false,
				],
			],
			$post_type_name . '_archive_title_align'      => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Title Align', 'kadence' ),
				'priority'     => 4,
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_align',
					[
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					] 
				),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive-title',
						'pattern'  => [
							'desktop' => 'title-align-$',
							'tablet'  => 'title-tablet-align-$',
							'mobile'  => 'title-mobile-align-$',
						],
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'left'   => [
							'tooltip'  => __( 'Left Align Title', 'kadence' ),
							'dashicon' => 'editor-alignleft',
						],
						'center' => [
							'tooltip'  => __( 'Center Align Title', 'kadence' ),
							'dashicon' => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip'  => __( 'Right Align Title', 'kadence' ),
							'dashicon' => 'editor-alignright',
						],
					],
					'responsive' => true,
				],
			],
			$post_type_name . '_archive_title_height'     => [
				'control_type' => 'kadence_range_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 5,
				'label'        => esc_html__( 'Container Min Height', 'kadence' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_archive_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '#inner-wrap .' . $post_type_name . '-archive-hero-section .entry-header',
						'property' => 'min-height',
						'pattern'  => '$',
						'key'      => 'size',
					],
				],
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_height',
					[
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					] 
				),
				'input_attrs'  => [
					'min'   => [
						'px'  => 10,
						'em'  => 1,
						'rem' => 1,
						'vh'  => 2,
					],
					'max'   => [
						'px'  => 800,
						'em'  => 12,
						'rem' => 12,
						'vh'  => 100,
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
			$post_type_name . '_archive_title_elements'   => [
				'control_type' => 'kadence_sorter_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 6,
				'default'      => kadence()->default( $post_type_name . '_archive_title_elements', [ 'breadcrumb', 'title', 'description' ] ),
				'label'        => esc_html__( 'Title Elements', 'kadence' ),
				'transport'    => 'refresh',
				'settings'     => [
					'elements'    => $post_type_name . '_archive_title_elements',
					'title'       => $post_type_name . '_archive_title_element_title',
					'breadcrumb'  => $post_type_name . '_archive_title_element_breadcrumb',
					'description' => $post_type_name . '_archive_title_element_description',
				],
				'input_attrs'  => [
					'groupe'   => $post_type_name . '_archive_title_elements',
					'defaults' => [
						'title'       => kadence()->default(
							$post_type_name . '_archive_element_title',
							[
								'enabled' => true,
							] 
						),
						'description' => kadence()->default(
							$post_type_name . '_archive_title_element_description',
							[
								'enabled' => true,
							] 
						),
						'breadcrumb'  => kadence()->default(
							$post_type_name . '_archive_title_element_breadcrumb',
							[
								'enabled'    => false,
								'show_title' => true,
							] 
						),
					],
				],
			],
			$post_type_name . '_archive_title_color'      => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Title Color', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_color',
					[
						'color' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title h1',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
				],
				'input_attrs'  => [
					'colors' => [
						'color' => [
							'tooltip' => __( 'Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_archive_title_description_color' => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Description Colors', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_description_color',
					[
						'color' => '',
						'hover' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .archive-description',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .archive-description a:hover',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_archive_title_breadcrumb_color' => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Breadcrumb Colors', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_breadcrumb_color',
					[
						'color' => '',
						'hover' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .kadence-breadcrumbs',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-title .kadence-breadcrumbs a:hover',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_archive_title_background' => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Archive Title Background', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_background',
					[
						'desktop' => [
							'color' => '',
						],
					] 
				),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_archive_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => '#inner-wrap .' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => __( 'Title Background', 'kadence' ),
				],
			],
			$post_type_name . '_archive_title_overlay_color' => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Background Overlay Color', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_title_overlay_color',
					[
						'color' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.' . $post_type_name . '-archive-hero-section .hero-section-overlay',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'color',
					],
				],
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_archive_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'input_attrs'  => [
					'colors'        => [
						'color' => [
							'tooltip' => __( 'Overlay Color', 'kadence' ),
							'palette' => true,
						],
					],
					'allowGradient' => true,
				],
			],
			$post_type_name . '_archive_title_border'     => [
				'control_type' => 'kadence_borders_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Border', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_archive_title_border' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_title',
						'operator' => '=',
						'value'    => true,
					],
					[
						'setting'  => $post_type_name . '_archive_title_layout',
						'operator' => '=',
						'value'    => 'above',
					],
				],
				'settings'     => [
					'border_top'    => $post_type_name . '_archive_title_top_border',
					'border_bottom' => $post_type_name . '_archive_title_bottom_border',
				],
				'live_method'  => [
					$post_type_name . '_archive_title_top_border' => [
						[
							'type'     => 'css_border',
							'selector' => '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
							'pattern'  => '$',
							'property' => 'border-top',
							'key'      => 'border',
						],
					],
					$post_type_name . '_archive_title_bottom_border' => [ 
						[
							'type'     => 'css_border',
							'selector' => '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner',
							'property' => 'border-bottom',
							'pattern'  => '$',
							'key'      => 'border',
						],
					],
				],
			],
			'info_' . $post_type_name . '_archive_layout' => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 10,
				'label'        => esc_html__( 'Archive Layout', 'kadence' ),
				'settings'     => false,
			],
			// 'info_' . $post_type_name . '_archive_layout_design' => array(
			// 'control_type' => 'kadence_title_control',
			// 'section'      => $post_type_name . '_archive_design',
			// 'priority'     => 10,
			// 'label'        => esc_html__( 'Archive Layout', 'kadence' ),
			// 'settings'     => false,
			// ),
			$post_type_name . '_archive_layout'           => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Layout', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_archive_layout' ),
				'input_attrs'  => [
					'layout'     => [
						'normal'    => [
							'name' => __( 'Normal', 'kadence' ),
							'icon' => 'normal',
						],
						'narrow'    => [
							'name' => __( 'Narrow', 'kadence' ),
							'icon' => 'narrow',
						],
						'fullwidth' => [
							'name' => __( 'Fullwidth', 'kadence' ),
							'icon' => 'fullwidth',
						],
						'left'      => [
							'name' => __( 'Left Sidebar', 'kadence' ),
							'icon' => 'leftsidebar',
						],
						'right'     => [
							'name' => __( 'Right Sidebar', 'kadence' ),
							'icon' => 'rightsidebar',
						],
					],
					'class'      => 'kadence-three-col',
					'responsive' => false,
				],
			],
			$post_type_name . '_archive_sidebar_id'       => [
				'control_type' => 'kadence_select_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Archive Default Sidebar', 'kadence' ),
				'transport'    => 'refresh',
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_archive_sidebar_id', 'sidebar-primary' ),
				'input_attrs'  => [
					'options' => kadence()->sidebar_options(),
				],
			],
			$post_type_name . '_archive_content_style'    => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Content Style', 'kadence' ),
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_archive_content_style', 'boxed' ),
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => 'body.post-type-archive-' . $post_type_name,
						'pattern'  => 'content-style-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'boxed'   => [
							'tooltip' => __( 'Boxed', 'kadence' ),
							'icon'    => 'gridBoxed',
							'name'    => __( 'Boxed', 'kadence' ),
						],
						'unboxed' => [
							'tooltip' => __( 'Unboxed', 'kadence' ),
							'icon'    => 'gridUnboxed',
							'name'    => __( 'Unboxed', 'kadence' ),
						],
					],
					'responsive' => false,
					'class'      => 'kadence-two-col',
				],
			],
			$post_type_name . '_archive_columns'          => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 10,
				'label'        => esc_html__( 'Archive Columns', 'kadence' ),
				'transport'    => 'refresh',
				'default'      => kadence()->default( $post_type_name . '_archive_columns', 3 ),
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
					],
					'responsive' => false,
				],
			],
			'info_' . $post_type_name . '_archive_item_layout' => [
				'control_type' => 'kadence_title_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 12,
				'label'        => esc_html__( 'Post Item Layout', 'kadence' ),
				'settings'     => false,
			],
			$post_type_name . '_archive_elements'         => [
				'control_type' => 'kadence_sorter_control',
				'section'      => $post_type_name . '_archive',
				'priority'     => 12,
				'default'      => kadence()->default( $post_type_name . '_archive_elements', [ 'feature', 'title', 'meta', 'excerpt', 'readmore' ] ),
				'label'        => esc_html__( 'Item Elements', 'kadence' ),
				'transport'    => 'refresh',
				'settings'     => [
					'elements'   => $post_type_name . '_archive_elements',
					'feature'    => $post_type_name . '_archive_element_feature',
					'categories' => $post_type_name . '_archive_element_categories',
					'title'      => $post_type_name . '_archive_element_title',
					'meta'       => $post_type_name . '_archive_element_meta',
					'excerpt'    => $post_type_name . '_archive_element_excerpt',
					'readmore'   => $post_type_name . '_archive_element_readmore',
				],
				'input_attrs'  => [
					'groupe'     => $post_type_name . '_archive_elements',
					'sortable'   => false,
					'defaults'   => [
						'feature'    => kadence()->default(
							$post_type_name . '_archive_element_feature',
							[
								'enabled' => true,
								'ratio'   => '2-3',
								'size'    => 'medium_large',
							] 
						),
						'title'      => kadence()->default(
							$post_type_name . '_archive_element_title',
							[
								'enabled' => true,
							] 
						),
						'categories' => kadence()->default(
							$post_type_name . '_archive_element_categories',
							[
								'enabled'  => false,
								'style'    => 'normal',
								'divider'  => 'vline',
								'taxonomy' => '',
							] 
						),
						'meta'       => kadence()->default(
							$post_type_name . '_archive_element_meta',
							[
								'id'                     => 'meta',
								'enabled'                => false,
								'divider'                => 'dot',
								'author'                 => true,
								'authorLink'             => true,
								'authorImage'            => true,
								'authorImageSize'        => 25,
								'authorEnableLabel'      => true,
								'authorLabel'            => '',
								'date'                   => true,
								'dateTime'               => false,
								'dateEnableLabel'        => false,
								'dateLabel'              => '',
								'dateUpdated'            => false,
								'dateUpdatedTime'        => false,
								'dateUpdatedDifferent'   => false,
								'dateUpdatedEnableLabel' => false,
								'dateUpdatedLabel'       => '',
								'categories'             => false,
								'categoriesEnableLabel'  => false,
								'categoriesLabel'        => '',
								'comments'               => false,
								'commentsCondition'      => false,
							] 
						),
						'excerpt'    => kadence()->default(
							$post_type_name . '_archive_element_excerpt',
							[
								'enabled'     => true,
								'words'       => 55,
								'fullContent' => false,
							] 
						),
						'readmore'   => kadence()->default(
							$post_type_name . '_archive_element_readmore',
							[
								'enabled' => true,
							] 
						),
					],
					'dividers'   => [
						'dot'        => [
							'icon' => 'dot',
						],
						'slash'      => [
							'icon' => 'slash',
						],
						'dash'       => [
							'icon' => 'dash',
						],
						'vline'      => [
							'icon' => 'vline',
						],
						'customicon' => [
							'icon' => 'hours',
						],
					],
					'taxonomies' => $taxs,
				],
			],
			$post_type_name . '_archive_item_image_placement' => [
				'control_type' => 'kadence_radio_icon_control',
				'section'      => $post_type_name . '_archive',
				'label'        => esc_html__( 'Item Image Placement', 'kadence' ),
				'priority'     => 10,
				'default'      => kadence()->default( $post_type_name . '_archive_item_image_placement' ),
				'context'      => [
					[
						'setting'  => $post_type_name . '_archive_columns',
						'operator' => '=',
						'value'    => '1',
					],
				],
				'live_method'  => [
					[
						'type'     => 'class',
						'selector' => '.' . $post_type_name . '-archive.grid-cols',
						'pattern'  => 'item-image-style-$',
						'key'      => '',
					],
				],
				'input_attrs'  => [
					'layout'     => [
						'beside' => [
							'name' => __( 'Beside', 'kadence' ),
						],
						'above'  => [
							'name' => __( 'Above', 'kadence' ),
						],
					],
					'responsive' => false,
				],
			],
			$post_type_name . '_archive_item_title_font'  => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Post Item Title Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_item_title_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => '',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.loop-entry.type-' . $post_type_name . ' h2.entry-title',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'             => $post_type_name . '_archive_item_title_font',
					'headingInherit' => true,
				],
			],
			$post_type_name . '_archive_item_meta_color'  => [
				'control_type' => 'kadence_color_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Item Meta Colors', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_item_meta_color',
					[
						'color' => '',
						'hover' => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta',
						'property' => 'color',
						'pattern'  => '$',
						'key'      => 'color',
					],
					[
						'type'     => 'css',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta a:hover',
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
							'tooltip' => __( 'Link Hover Color', 'kadence' ),
							'palette' => true,
						],
					],
				],
			],
			$post_type_name . '_archive_item_meta_font'   => [
				'control_type' => 'kadence_typography_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Item Meta Font', 'kadence' ),
				'default'      => kadence()->default(
					$post_type_name . '_archive_item_meta_font',
					[
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					] 
				),
				'live_method'  => [
					[
						'type'     => 'css_typography',
						'selector' => '.loop-entry.type-' . $post_type_name . ' .entry-meta',
						'property' => 'font',
						'key'      => 'typography',
					],
				],
				'input_attrs'  => [
					'id'      => $post_type_name . '_archive_item_meta_font',
					'options' => 'no-color',
				],
			],
			$post_type_name . '_archive_background'       => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Site Background', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_archive_background' ),
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => 'body.post-type-archive-' . $post_type_name,
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => __( 'Archive Background', 'kadence' ),
				],
			],
			$post_type_name . '_archive_content_background' => [
				'control_type' => 'kadence_background_control',
				'section'      => $post_type_name . '_archive_design',
				'label'        => esc_html__( 'Content Background', 'kadence' ),
				'default'      => kadence()->default( $post_type_name . '_archive_content_background' ),
				'live_method'  => [
					[
						'type'     => 'css_background',
						'selector' => 'body.post-type-archive-' . $post_type_name . ' .content-bg, body.post-type-archive-' . $post_type_name . '.content-style-unboxed .site',
						'property' => 'background',
						'pattern'  => '$',
						'key'      => 'base',
					],
				],
				'input_attrs'  => [
					'tooltip' => __( 'Archive Content Background', 'kadence' ),
				],
			],
		];
		Theme_Customizer::add_settings( $kadence_custom_post_type_archive_settings );
		Theme_Customizer::add_settings( $settings );
	}
}
Theme_Customizer::add_settings(
	[ 
		'info_custom_post_types_placehoder' => [
			'control_type' => 'kadence_title_control',
			'section'      => 'custom_posts_placeholder',
			'priority'     => 10,
			'label'        => esc_html__( 'Custom Post Types', 'kadence' ),
			'settings'     => false,
		],
	]
);
