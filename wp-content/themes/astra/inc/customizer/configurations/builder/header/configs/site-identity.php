<?php
/**
 * Site identity Header Configuration.
 *
 * @author      Astra
 * @package     Astra
 * @copyright   Copyright (c) 2023, Astra
 * @link        https://wpastra.com/
 * @since       4.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register site identity header builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_site_identity_configuration() {
	$_section = 'title_tagline';

	$_configs = array(

		/*
		 * Update the Site Identity section inside Layout -> Header
		 *
		 * @since 3.0.0
		 */
		array(
			'name'     => 'title_tagline',
			'type'     => 'section',
			'priority' => 100,
			'title'    => __( 'Logo', 'astra' ),
			'panel'    => 'panel-header-builder-group',
		),

		/**
		* Link to the astra logo and site title settings.
		*/
		array(
			'name'           => ASTRA_THEME_SETTINGS . '[logo-title-settings-link]',
			'type'           => 'control',
			'control'        => 'ast-customizer-link',
			'section'        => 'astra-site-identity',
			'priority'       => 100,
			'link_type'      => 'section',
			'is_button_link' => true,
			'linked'         => 'title_tagline',
			'link_text'      => __( 'Site Title & Logo Settings', 'astra' ),
		),

		/**
		 * Option: Header Builder Tabs
		 */
		array(
			'name'        => $_section . '-ast-context-tabs',
			'section'     => $_section,
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 0,
			'description' => '',
		),

		/**
		 * Option: Header logo color.
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-logo-color]',
			'default'  => astra_get_option( 'header-logo-color' ),
			'type'     => 'control',
			'control'  => 'ast-color',
			'section'  => 'title_tagline',
			'priority' => 5,
			'context'  => Astra_Builder_Helper::$design_tab,
			'title'    => __( 'Logo Color', 'astra' ),
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Option: Header logo color description.
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-logo-color-notice]',
			'type'     => 'control',
			'control'  => 'ast-description',
			'section'  => 'title_tagline',
			'priority' => 5,
			'label'    => '',
			'context'  => Astra_Builder_Helper::$design_tab,
			'help'     => __( 'Use it with transparent images for optimal results.', 'astra' ),
		),

		// Option: Site Title Color.
		array(
			'name'      => 'header-color-site-title',
			'parent'    => ASTRA_THEME_SETTINGS . '[site-identity-title-color-group]',
			'section'   => 'title_tagline',
			'type'      => 'sub-control',
			'control'   => 'ast-color',
			'priority'  => 5,
			'default'   => astra_get_option( 'header-color-site-title' ),
			'transport' => 'postMessage',
			'title'     => __( 'Normal', 'astra' ),
			'context'   => Astra_Builder_Helper::$design_tab,
		),

		// Option: Site Title Hover Color.
		array(
			'name'      => 'header-color-h-site-title',
			'parent'    => ASTRA_THEME_SETTINGS . '[site-identity-title-color-group]',
			'section'   => 'title_tagline',
			'type'      => 'sub-control',
			'control'   => 'ast-color',
			'priority'  => 10,
			'transport' => 'postMessage',
			'default'   => astra_get_option( 'header-color-h-site-title' ),
			'title'     => __( 'Hover', 'astra' ),
			'context'   => Astra_Builder_Helper::$design_tab,
		),


		/**
				 * Option: Divider
				 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[' . $_section . '-margin-divider]',
			'section'  => $_section,
			'title'    => __( 'Spacing', 'astra' ),
			'type'     => 'control',
			'control'  => 'ast-heading',
			'priority' => 220,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$design_tab,
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Option: Margin Space
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[' . $_section . '-margin]',
			'default'           => astra_get_option( $_section . '-margin' ),
			'type'              => 'control',
			'transport'         => 'postMessage',
			'control'           => 'ast-responsive-spacing',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
			'section'           => $_section,
			'priority'          => 220,
			'title'             => __( 'Margin', 'astra' ),
			'linked_choices'    => true,
			'unit_choices'      => array( 'px', 'em', '%' ),
			'choices'           => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
			'context'           => Astra_Builder_Helper::$design_tab,
			'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
		),

	);

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_site_identity_configuration();
}
