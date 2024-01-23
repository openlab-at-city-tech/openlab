<?php
/**
 * Styling Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Header_Typo_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Header_Typo_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Header Typography Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'typography' ) ) {

				$_configs = array(

					/**
					 * Option: Site Title Font Size
					 */

					array(
						'name'              => 'font-size-site-title',
						'type'              => 'sub-control',
						'parent'            => ASTRA_THEME_SETTINGS . '[site-title-typography]',
						'section'           => 'title_tagline',
						'control'           => 'ast-responsive-slider',
						'default'           => astra_get_option( 'font-size-site-title' ),
						'transport'         => 'postMessage',
						'priority'          => 12,
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
					 * Option: Site Tagline Font Size
					 */

					array(
						'name'              => 'font-size-site-tagline',
						'type'              => 'sub-control',
						'parent'            => ASTRA_THEME_SETTINGS . '[site-tagline-typography]',
						'section'           => 'title_tagline',
						'control'           => 'ast-responsive-slider',
						'default'           => astra_get_option( 'font-size-site-tagline' ),
						'transport'         => 'postMessage',
						'priority'          => 16,
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
				);
			} else {

				$_configs = array(

					/**
					 * Option: Site Title Font Size
					 */

					array(
						'name'              => ASTRA_THEME_SETTINGS . '[font-size-site-title]',
						'type'              => 'control',
						'section'           => 'title_tagline',
						'default'           => astra_get_option( 'font-size-site-title' ),
						'transport'         => 'postMessage',
						'control'           => 'ast-responsive-slider',
						'priority'          => ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? 16 : 8,
						'title'             => __( 'Title Font Size', 'astra' ),
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
						'context'           => ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? array(
							Astra_Builder_Helper::$design_tab_config,
							array(
								'relation' => 'OR',
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'desktop',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'tablet',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'mobile',
									'operator'    => '==',
									'value'       => true,
								),
							),
						) : array(
							array(
								'relation' => 'OR',
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'desktop',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'tablet',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-title-responsive]',
									'setting-key' => 'mobile',
									'operator'    => '==',
									'value'       => true,
								),
							),
						),
					),

					/**
					 * Option: Site Tagline Font Size
					 */

					array(
						'name'              => ASTRA_THEME_SETTINGS . '[font-size-site-tagline]',
						'type'              => 'control',
						'section'           => 'title_tagline',
						'control'           => 'ast-responsive-slider',
						'default'           => astra_get_option( 'font-size-site-tagline' ),
						'transport'         => 'postMessage',
						'priority'          => ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? 20 : 12,
						'title'             => __( 'Tagline Font Size', 'astra' ),
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
						'context'           => ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? array(
							Astra_Builder_Helper::$design_tab_config,
							array(
								'relation' => 'OR',
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'desktop',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'tablet',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'mobile',
									'operator'    => '==',
									'value'       => true,
								),
							),
						) : array(
							array(
								'relation' => 'OR',
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'desktop',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'tablet',
									'operator'    => '==',
									'value'       => true,
								),
								array(
									'setting'     => ASTRA_THEME_SETTINGS . '[display-site-tagline-responsive]',
									'setting-key' => 'mobile',
									'operator'    => '==',
									'value'       => true,
								),
							),
						),
					),
				);
			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Header_Typo_Configs();


