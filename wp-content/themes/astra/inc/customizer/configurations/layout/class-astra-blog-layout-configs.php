<?php
/**
 * Bottom Footer Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Blog_Layout_Configs' ) ) {

	/**
	 * Register Blog Layout Customizer Configurations.
	 */
	class Astra_Blog_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Blog Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$blog_meta_choices = array(
				'comments' => __( 'Comments', 'astra' ),
				'category' => __( 'Category', 'astra' ),
				'author'   => __( 'Author', 'astra' ),
				'date'     => array(
					'clone'       => false,
					'is_parent'   => true,
					'main_index'  => 'date',
					'clone_limit' => 1,
					'title'       => __( 'Date', 'astra' ),
				),
				'tag'      => __( 'Tag', 'astra' ),
			);

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$blog_meta_choices['read-time'] = __( 'Read Time', 'astra' );
			}

			$_configs = array(

				/**
				 * Option: Blog Content Width
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[blog-width]',
					'default'    => astra_get_option( 'blog-width' ),
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-blog',
					'priority'   => 50,
					'transport'  => 'postMessage',
					'title'      => __( 'Content Width', 'astra' ),
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'custom'  => __( 'Custom', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Enter Width
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[blog-max-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'section'     => 'section-blog',
					'transport'   => 'postMessage',
					'default'     => astra_get_option( 'blog-max-width' ),
					'priority'    => 50,
					'context'     => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-width]',
							'operator' => '===',
							'value'    => 'custom',
						),
					),
					'title'       => __( 'Custom Width', 'astra' ),
					'suffix'      => 'px',
					'input_attrs' => array(
						'min'  => 768,
						'step' => 1,
						'max'  => 1920,
					),
					'divider'     => array( 'ast_class' => 'ast-top-dotted-divider' ),
				),

				/**
				 * Option: Blog Post Content
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[blog-post-content]',
					'section'    => 'section-blog',
					'title'      => __( 'Post Content', 'astra' ),
					'default'    => astra_get_option( 'blog-post-content' ),
					'type'       => 'control',
					'control'    => 'ast-selector',
					'priority'   => 75,
					'choices'    => array(
						'full-content' => __( 'Full Content', 'astra' ),
						'excerpt'      => __( 'Excerpt', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[archive-post-content-structure-divider]',
					'section'  => 'section-blog',
					'title'    => __( 'Posts Structure', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 50,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing' ),
				),

				/**
				 * Option: Display Post Structure
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'default'           => astra_get_option( 'blog-post-structure' ),
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-blog',
					'priority'          => 50,
					'title'             => __( 'Post Structure', 'astra' ),
					'divider'           => array( 'ast_class' => 'ast-top-spacing ast-bottom-section-divider' ),
					'choices'           => array(
						'image'      => __( 'Featured Image', 'astra' ),
						'title-meta' => __( 'Title & Blog Meta', 'astra' ),
					),
				),

				array(
					'name'              => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-blog',
					'default'           => astra_get_option( 'blog-meta' ),
					'priority'          => 50,
					'context'           => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
							'operator' => 'contains',
							'value'    => 'title-meta',
						),
					),
					'title'             => __( 'Meta', 'astra' ),
					'choices'           => $blog_meta_choices,
					'divider'           => array( 'ast_class' => 'ast-bottom-spacing ast-bottom-section-divider' ),
				),

				/**
				 * Option: Date Meta Type.
				 */
				array(
					'name'       => 'blog-meta-date-type',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'section'    => 'section-blog',
					'default'    => astra_get_option( 'blog-meta-date-type' ),
					'priority'   => 1,
					'linked'     => 'date',
					'transport'  => 'postMessage',
					'title'      => __( 'Type', 'astra' ),
					'choices'    => array(
						'published' => __( 'Published', 'astra' ),
						'updated'   => __( 'Last Updated', 'astra' ),
					),
					'divider'    => array( 'ast_class' => 'ast-bottom-spacing' ),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Date format support for meta field.
				 */
				array(
					'name'       => 'blog-meta-date-format',
					'default'    => astra_get_option( 'blog-meta-date-format' ),
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'linked'     => 'date',
					'type'       => 'sub-control',
					'control'    => 'ast-select',
					'transport'  => 'postMessage',
					'section'    => 'section-blog',
					'priority'   => 2,
					'responsive' => false,
					'renderAs'   => 'text',
					'title'      => __( 'Format', 'astra' ),
					'choices'    => array(
						''       => __( 'Default', 'astra' ),
						'F j, Y' => 'November 6, 2010',
						'Y-m-d'  => '2010-11-06',
						'm/d/Y'  => '11/06/2010',
						'd/m/Y'  => '06/11/2010',
					),
				),
			);

			if ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) {
				$_configs[] = array(
					'name'        => 'section-blog-ast-context-tabs',
					'section'     => 'section-blog',
					'type'        => 'control',
					'control'     => 'ast-builder-header-control',
					'priority'    => 0,
					'description' => '',
				);
			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Blog_Layout_Configs();
