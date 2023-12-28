<?php
/**
 * Related Posts Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2021, Astra
 * @link        https://wpastra.com/
 * @since       Astra 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if Customizer config base class does not exist.
if ( ! class_exists( 'Astra_Customizer_Config_Base' ) ) {
	return;
}

/**
 * Register Related Posts Configurations.
 */
class Astra_Related_Posts_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register Related Posts Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 3.5.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$_configs = array(

			/**
			 * Option: Related Posts Query
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[related-posts-section-heading]',
				'section'  => 'section-blog-single',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'title'    => __( 'Related Posts', 'astra' ),
				'priority' => 10,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Related Posts setting.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
				'default'  => astra_get_option( 'enable-related-posts' ),
				'type'     => 'control',
				'control'  => 'ast-toggle-control',
				'title'    => __( 'Enable Related Posts', 'astra' ),
				'section'  => 'section-blog-single',
				'priority' => 10,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Related Posts Title
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-title]',
				'default'   => astra_get_option( 'related-posts-title' ),
				'type'      => 'control',
				'section'   => 'section-blog-single',
				'priority'  => 11,
				'title'     => __( 'Title', 'astra' ),
				'control'   => 'ast-text-input',
				'divider'   => array( 'ast_class' => 'ast-top-dotted-divider' ),
				'transport' => 'postMessage',
				'partial'   => array(
					'selector'            => '.ast-related-posts-title-section .ast-related-posts-title',
					'container_inclusive' => false,
					'render_callback'     => array( 'Astra_Related_Posts_Loader', 'render_related_posts_title' ),
				),
				'context'   => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Related Posts Title Alignment
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[releted-posts-title-alignment]',
				'default'    => astra_get_option( 'releted-posts-title-alignment' ),
				'section'    => 'section-blog-single',
				'transport'  => 'postMessage',
				'title'      => __( 'Title Alignment', 'astra' ),
				'type'       => 'control',
				'control'    => 'ast-selector',
				'priority'   => 11,
				'responsive' => false,
				'divider'    => array( 'ast_class' => 'ast-top-dotted-divider' ),
				'context'    => array(
					Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-title]',
						'operator' => '!=',
						'value'    => '',
					),
				),
				'choices'    => array(
					'left'   => 'align-left',
					'center' => 'align-center',
					'right'  => 'align-right',
				),
			),

			/**
			 * Option: Related Posts Structure
			 */
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[related-posts-structure]',
				'type'              => 'control',
				'control'           => 'ast-sortable',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
				'section'           => 'section-blog-single',
				'default'           => astra_get_option( 'related-posts-structure' ),
				'priority'          => 12,
				'context'           => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'title'             => __( 'Posts Structure', 'astra' ),
				'choices'           => array(
					'featured-image' => __( 'Featured Image', 'astra' ),
					'title-meta'     => __( 'Title & Post Meta', 'astra' ),
				),
				'divider'           => array( 'ast_class' => 'ast-top-dotted-divider' ),
			),

			array(
				'name'              => ASTRA_THEME_SETTINGS . '[related-posts-meta-structure]',
				'type'              => 'control',
				'control'           => 'ast-sortable',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
				'default'           => astra_get_option( 'related-posts-meta-structure' ),
				'context'           => array(
					Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-structure]',
						'operator' => 'contains',
						'value'    => 'title-meta',
					),
				),
				'section'           => 'section-blog-single',
				'priority'          => 12,
				'title'             => __( 'Meta', 'astra' ),
				'choices'           => array(
					'comments' => __( 'Comments', 'astra' ),
					'category' => __( 'Category', 'astra' ),
					'author'   => __( 'Author', 'astra' ),
					'date'     => __( 'Publish Date', 'astra' ),
					'tag'      => __( 'Tag', 'astra' ),
				),
				'divider'           => array( 'ast_class' => 'ast-top-dotted-divider' ),
			),

			/**
			 * Option: Enable excerpt for Related Posts.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[enable-related-posts-excerpt]',
				'default'  => astra_get_option( 'enable-related-posts-excerpt' ),
				'type'     => 'control',
				'control'  => 'ast-toggle-control',
				'title'    => __( 'Enable Post Excerpt', 'astra' ),
				'section'  => 'section-blog-single',
				'priority' => 12,
				'context'  => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'divider'  => array( 'ast_class' => 'ast-top-dotted-divider' ),
			),

			/**
			 * Option: Excerpt word count for Related Posts
			 */
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[related-posts-excerpt-count]',
				'default'     => astra_get_option( 'related-posts-excerpt-count' ),
				'type'        => 'control',
				'control'     => 'ast-slider',
				'context'     => array(
					Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts-excerpt]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section'     => 'section-blog-single',
				'title'       => __( 'Excerpt Word Count', 'astra' ),
				'priority'    => 12,
				'input_attrs' => array(
					'min'  => 0,
					'step' => 1,
					'max'  => 60,
				),
			),

			/**
			 * Option: No. of Related Posts
			 */
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[related-posts-total-count]',
				'default'     => astra_get_option( 'related-posts-total-count' ),
				'type'        => 'control',
				'control'     => 'ast-slider',
				'context'     => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'section'     => 'section-blog-single',
				'title'       => __( 'Total Number of Related Posts', 'astra' ),
				'priority'    => 11,
				'input_attrs' => array(
					'min'  => 1,
					'step' => 1,
					'max'  => 20,
				),
				'divider'     => array( 'ast_class' => 'ast-top-dotted-divider ast-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Columns
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[related-posts-grid-responsive]',
				'type'       => 'control',
				'control'    => 'ast-selector',
				'section'    => 'section-blog-single',
				'default'    => astra_get_option( 'related-posts-grid-responsive' ),
				'priority'   => 11,
				'context'    => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'title'      => __( 'Grid Column Layout', 'astra' ),
				'choices'    => array(
					'full'    => __( '1', 'astra' ),
					'2-equal' => __( '2', 'astra' ),
					'3-equal' => __( '3', 'astra' ),
					'4-equal' => __( '4', 'astra' ),
				),
				'responsive' => true,
				'renderAs'   => 'text',
				'divider'    => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Query group setting
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-query-group]',
				'default'   => astra_get_option( 'related-posts-query-group' ),
				'type'      => 'control',
				'transport' => 'postMessage',
				'control'   => 'ast-settings-group',
				'context'   => array(
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'title'     => __( 'Posts Query', 'astra' ),
				'section'   => 'section-blog-single',
				'priority'  => 11,
			),

			/**
			 * Option: Related Posts based on.
			 */
			array(
				'name'       => 'related-posts-based-on',
				'default'    => astra_get_option( 'related-posts-based-on' ),
				'type'       => 'sub-control',
				'transport'  => 'postMessage',
				'parent'     => ASTRA_THEME_SETTINGS . '[related-posts-query-group]',
				'section'    => 'section-blog-single',
				'priority'   => 1,
				'control'    => 'ast-selector',
				'divider'    => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				'title'      => __( 'Related Posts by', 'astra' ),
				'choices'    => array(
					'categories' => __( 'Categories', 'astra' ),
					'tags'       => __( 'Tags', 'astra' ),
				),
				'responsive' => false,
				'renderAs'   => 'text',
			),

			/**
			 * Option: Display Post Structure
			 */
			array(
				'name'      => 'related-posts-order-by',
				'default'   => astra_get_option( 'related-posts-order-by' ),
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-query-group]',
				'section'   => 'section-blog-single',
				'type'      => 'sub-control',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				'priority'  => 2,
				'transport' => 'postMessage',
				'title'     => __( 'Order by', 'astra' ),
				'control'   => 'ast-select',
				'choices'   => array(
					'date'          => __( 'Date', 'astra' ),
					'title'         => __( 'Title', 'astra' ),
					'menu_order'    => __( 'Post Order', 'astra' ),
					'rand'          => __( 'Random', 'astra' ),
					'comment_count' => __( 'Comment Counts', 'astra' ),
				),
			),

			/**
			 * Option: Display Post Structure
			 */
			array(
				'name'       => 'related-posts-order',
				'parent'     => ASTRA_THEME_SETTINGS . '[related-posts-query-group]',
				'section'    => 'section-blog-single',
				'type'       => 'sub-control',
				'transport'  => 'postMessage',
				'title'      => __( 'Order', 'astra' ),
				'default'    => astra_get_option( 'related-posts-order' ),
				'control'    => 'ast-selector',
				'priority'   => 3,
				'choices'    => array(
					'asc'  => __( 'Ascending', 'astra' ),
					'desc' => __( 'Descending', 'astra' ),
				),
				'responsive' => false,
				'renderAs'   => 'text',
			),

			/**
			 * Option: Related Posts colors setting group
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'default'   => astra_get_option( 'related-posts-colors-group' ),
				'type'      => 'control',
				'transport' => 'postMessage',
				'control'   => 'ast-settings-group',
				'context'   => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-structure]',
						'operator' => 'contains',
						'value'    => 'title-meta',
					),
				),
				'title'     => __( 'Content Colors', 'astra' ),
				'section'   => 'section-blog-single',
				'priority'  => 15,
			),

			/**
			 * Option: Related Posts title typography setting group
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-section-title-typography-group]',
				'type'      => 'control',
				'priority'  => 16,
				'control'   => 'ast-settings-group',
				'context'   => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-title]',
						'operator' => '!=',
						'value'    => '',
					),
				),
				'title'     => __( 'Section Title Font', 'astra' ),
				'section'   => 'section-blog-single',
				'transport' => 'postMessage',
			),

			/**
			 * Option: Related Posts title typography setting group
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-title-typography-group]',
				'type'      => 'control',
				'priority'  => 17,
				'control'   => 'ast-settings-group',
				'context'   => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-structure]',
						'operator' => 'contains',
						'value'    => 'title-meta',
					),
				),
				'title'     => __( 'Post Title Font', 'astra' ),
				'section'   => 'section-blog-single',
				'transport' => 'postMessage',
			),

			/**
			 * Option: Related Posts meta typography setting group
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-meta-typography-group]',
				'type'      => 'control',
				'priority'  => 18,
				'control'   => 'ast-settings-group',
				'context'   => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-structure]',
						'operator' => 'contains',
						'value'    => 'title-meta',
					),
				),
				'title'     => __( 'Meta Font', 'astra' ),
				'section'   => 'section-blog-single',
				'transport' => 'postMessage',
			),

			/**
			 * Option: Related Posts content typography setting group
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[related-posts-content-typography-group]',
				'type'      => 'control',
				'priority'  => 21,
				'control'   => 'ast-settings-group',
				'context'   => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts-excerpt]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'title'     => __( 'Content Font', 'astra' ),
				'section'   => 'section-blog-single',
				'transport' => 'postMessage',
			),

			/**
			 * Option: Related post block text color
			 */
			array(
				'name'      => 'related-posts-text-color',
				'tab'       => __( 'Normal', 'astra' ),
				'type'      => 'sub-control',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'section'   => 'section-blog-single',
				'default'   => astra_get_option( 'related-posts-text-color' ),
				'transport' => 'postMessage',
				'control'   => 'ast-color',
				'title'     => __( 'Text Color', 'astra' ),
			),

			/**
			 * Option: Related post block CTA link color
			 */
			array(
				'name'      => 'related-posts-link-color',
				'tab'       => __( 'Normal', 'astra' ),
				'type'      => 'sub-control',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'section'   => 'section-blog-single',
				'default'   => astra_get_option( 'related-posts-link-color' ),
				'transport' => 'postMessage',
				'control'   => 'ast-color',
				'title'     => __( 'Link Color', 'astra' ),
			),

			/**
			 * Option: Related Posts Query
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[related-posts-design-section-heading]',
				'section'  => 'section-blog-single',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'title'    => ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? __( 'Related Posts', 'astra' ) : __( 'Related Posts Design', 'astra' ),
				'context'  => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 14,
			),

			/**
			 * Option: Related post block BG color
			 */
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[related-posts-title-color]',
				'default'           => astra_get_option( 'related-posts-title-color' ),
				'type'              => 'control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'section'           => 'section-blog-single',
				'transport'         => 'postMessage',
				'priority'          => 14,
				'context'           => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					'relation' => 'AND',
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[related-posts-title]',
						'operator' => '!=',
						'value'    => '',
					),
				),
				'title'             => __( 'Section Title', 'astra' ),
			),

			/**
			 * Option: Related post block BG color
			 */
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[related-posts-background-color]',
				'default'           => astra_get_option( 'related-posts-background-color' ),
				'type'              => 'control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'section'           => 'section-blog-single',
				'transport'         => 'postMessage',
				'priority'          => 14,
				'context'           => array(
					true === Astra_Builder_Helper::$is_header_footer_builder_active ?
					Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[enable-related-posts]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'title'             => __( 'Section Background', 'astra' ),
				'divider'           => array( 'ast_class' => 'ast-bottom-section-divider' ),
			),

			/**
			 * Option: Related post meta color
			 */
			array(
				'name'      => 'related-posts-meta-color',
				'default'   => astra_get_option( 'related-posts-meta-color' ),
				'tab'       => __( 'Normal', 'astra' ),
				'type'      => 'sub-control',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'section'   => 'section-blog-single',
				'transport' => 'postMessage',
				'control'   => 'ast-color',
				'title'     => __( 'Meta Color', 'astra' ),
			),

			/**
			 * Option: Related hover CTA link color
			 */
			array(
				'name'      => 'related-posts-link-hover-color',
				'type'      => 'sub-control',
				'tab'       => __( 'Hover', 'astra' ),
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'section'   => 'section-blog-single',
				'control'   => 'ast-color',
				'default'   => astra_get_option( 'related-posts-link-hover-color' ),
				'transport' => 'postMessage',
				'title'     => __( 'Link Color', 'astra' ),
			),

			/**
			 * Option: Related hover meta link color
			 */
			array(
				'name'      => 'related-posts-meta-link-hover-color',
				'type'      => 'sub-control',
				'tab'       => __( 'Hover', 'astra' ),
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-colors-group]',
				'section'   => 'section-blog-single',
				'control'   => 'ast-color',
				'default'   => astra_get_option( 'related-posts-meta-link-hover-color' ),
				'transport' => 'postMessage',
				'title'     => __( 'Meta Link Color', 'astra' ),
			),

			/**
			 * Option: Related Posts Title Font Family
			 */
			array(
				'name'      => 'related-posts-title-font-family',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-title-typography-group]',
				'section'   => 'section-blog-single',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'related-posts-title-font-family' ),
				'title'     => __( 'Font Family', 'astra' ),
				'connect'   => ASTRA_THEME_SETTINGS . '[related-posts-title-font-weight]',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Title Font Weight
			 */
			array(
				'name'              => 'related-posts-title-font-weight',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-title-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'related-posts-title-font-weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'connect'           => 'related-posts-title-font-family',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Title Font Size
			 */

			array(
				'name'              => 'related-posts-title-font-size',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-title-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'default'           => astra_get_option( 'related-posts-title-font-size' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Font Size', 'astra' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'suffix'            => array( 'px', 'em', 'vw' ),
				'input_attrs'       => array(
					'px' => array(
						'min'  => 0,
						'step' => 1,
						'max'  => 200,
					),
					'em' => array(
						'min'  => 0,
						'step' => 0.01,
						'max'  => 20,
					),
					'vw' => array(
						'min'  => 0,
						'step' => 0.1,
						'max'  => 25,
					),
				),
			),

			/**
				 * Option: Related Posts Title Font Extras
				 */
				array(
					'name'    => 'related-posts-title-font-extras',
					'type'    => 'sub-control',
					'parent'  => ASTRA_THEME_SETTINGS . '[related-posts-title-typography-group]',
					'control' => 'ast-font-extras',
					'section' => 'section-blog-single',
					'default' => astra_get_option( 'related-posts-title-font-extras' ),
					'title'   => __( 'Font Extras', 'astra' ),
				),


			/**
			 * Option: Related Posts Title Font Family
			 */
			array(
				'name'      => 'related-posts-section-title-font-family',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-section-title-typography-group]',
				'section'   => 'section-blog-single',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'related-posts-section-title-font-family' ),
				'title'     => __( 'Font Family', 'astra' ),
				'connect'   => ASTRA_THEME_SETTINGS . '[related-posts-section-title-font-weight]',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Title Font Weight
			 */
			array(
				'name'              => 'related-posts-section-title-font-weight',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-section-title-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'related-posts-section-title-font-weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'connect'           => 'related-posts-section-title-font-family',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Title Font Size
			 */

			array(
				'name'              => 'related-posts-section-title-font-size',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-section-title-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'default'           => astra_get_option( 'related-posts-section-title-font-size' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Font Size', 'astra' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'suffix'            => array( 'px', 'em', 'vw' ),
				'input_attrs'       => array(
					'px' => array(
						'min'  => 0,
						'step' => 1,
						'max'  => 200,
					),
					'em' => array(
						'min'  => 0,
						'step' => 0.01,
						'max'  => 20,
					),
					'vw' => array(
						'min'  => 0,
						'step' => 0.1,
						'max'  => 25,
					),
				),
			),

			/**
				 * Option: Related Posts Title Font Extras
				 */
				array(
					'name'    => 'related-posts-section-title-font-extras',
					'type'    => 'sub-control',
					'parent'  => ASTRA_THEME_SETTINGS . '[related-posts-section-title-typography-group]',
					'control' => 'ast-font-extras',
					'section' => 'section-blog-single',
					'default' => astra_get_option( 'related-posts-section-title-font-extras' ),
					'title'   => __( 'Font Extras', 'astra' ),
				),

			/**
			 * Option: Related Posts Meta Font Family
			 */
			array(
				'name'      => 'related-posts-meta-font-family',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-meta-typography-group]',
				'section'   => 'section-blog-single',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'related-posts-meta-font-family' ),
				'title'     => __( 'Font Family', 'astra' ),
				'connect'   => ASTRA_THEME_SETTINGS . '[related-posts-meta-font-weight]',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Meta Font Weight
			 */
			array(
				'name'              => 'related-posts-meta-font-weight',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-meta-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'related-posts-meta-font-weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'connect'           => 'related-posts-meta-font-family',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Meta Font Size
			 */

			array(
				'name'              => 'related-posts-meta-font-size',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-meta-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'default'           => astra_get_option( 'related-posts-meta-font-size' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Font Size', 'astra' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'suffix'            => array( 'px', 'em', 'vw' ),
				'input_attrs'       => array(
					'px' => array(
						'min'  => 0,
						'step' => 1,
						'max'  => 200,
					),
					'em' => array(
						'min'  => 0,
						'step' => 0.01,
						'max'  => 20,
					),
					'vw' => array(
						'min'  => 0,
						'step' => 0.1,
						'max'  => 25,
					),
				),
			),

			/**
			 * Option: Related Posts Meta Font Extras
			 */
			array(
				'name'    => 'related-posts-meta-font-extras',
				'type'    => 'sub-control',
				'parent'  => ASTRA_THEME_SETTINGS . '[related-posts-meta-typography-group]',
				'control' => 'ast-font-extras',
				'section' => 'section-blog-single',
				'default' => astra_get_option( 'related-posts-meta-font-extras' ),
				'title'   => __( 'Font Extras', 'astra' ),
			),

			/**
			 * Option: Related Posts Content Font Family
			 */
			array(
				'name'      => 'related-posts-content-font-family',
				'parent'    => ASTRA_THEME_SETTINGS . '[related-posts-content-typography-group]',
				'section'   => 'section-blog-single',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'related-posts-content-font-family' ),
				'title'     => __( 'Font Family', 'astra' ),
				'connect'   => ASTRA_THEME_SETTINGS . '[related-posts-content-font-weight]',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Content Font Weight
			 */
			array(
				'name'              => 'related-posts-content-font-weight',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-content-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'related-posts-content-font-weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'connect'           => 'related-posts-content-font-family',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Related Posts Content Font Size
			 */
			array(
				'name'              => 'related-posts-content-font-size',
				'parent'            => ASTRA_THEME_SETTINGS . '[related-posts-content-typography-group]',
				'section'           => 'section-blog-single',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'default'           => astra_get_option( 'related-posts-content-font-size' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Font Size', 'astra' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'suffix'            => array( 'px', 'em', 'vw' ),
				'input_attrs'       => array(
					'px' => array(
						'min'  => 0,
						'step' => 1,
						'max'  => 200,
					),
					'em' => array(
						'min'  => 0,
						'step' => 0.01,
						'max'  => 20,
					),
					'vw' => array(
						'min'  => 0,
						'step' => 0.1,
						'max'  => 25,
					),
				),
			),

			/**
			 * Option: Related Posts Content Font Extras.
			 */
			/**
			 * Option: Related Posts Meta Font Extras
			 */
			array(
				'name'    => 'related-posts-content-font-extras',
				'type'    => 'sub-control',
				'parent'  => ASTRA_THEME_SETTINGS . '[related-posts-content-typography-group]',
				'control' => 'ast-font-extras',
				'section' => 'section-blog-single',
				'default' => astra_get_option( 'related-posts-content-font-extras' ),
				'title'   => __( 'Font Extras', 'astra' ),
			),
		);

		$configurations = array_merge( $configurations, $_configs );

		return $configurations;
	}
}

/**
 *  Kicking this off by creating NEW instance.
 */
new Astra_Related_Posts_Configs();
