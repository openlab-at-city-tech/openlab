<?php
/**
 * Customizer
 * 
 * @package WordPress
 * @subpackage cv-portfolio-blocks
 * @since cv-portfolio-blocks 1.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function cv_portfolio_blocks_customize_register( $wp_customize ) {
	$wp_customize->add_section( new CV_Portfolio_Blocks_Upsell_Section($wp_customize,'upsell_section',array(
		'title'            => __( 'CV Portfolio Blocks', 'cv-portfolio-blocks' ),
		'button_text'      => __( 'Upgrade Pro', 'cv-portfolio-blocks' ),
		'url'              => 'https://www.wpradiant.net/products/cv-wordpress-theme/',
		'priority'         => 0,
	)));
}
add_action( 'customize_register', 'cv_portfolio_blocks_customize_register' );

/**
 * Enqueue script for custom customize control.
 */
function cv_portfolio_blocks_custom_control_scripts() {
	wp_enqueue_script( 'cv-portfolio-blocks-custom-controls-js', get_template_directory_uri() . '/assets/js/custom-controls.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'cv_portfolio_blocks_custom_control_scripts' );