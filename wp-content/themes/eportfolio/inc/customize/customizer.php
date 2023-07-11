<?php 
/**
 * ePortfolio Theme Customizer.
 *
 * @package ePortfolio
 */

//customizer core option
require get_template_directory() . '/inc/customize/core/customizer-core.php';

//customizer 
require get_template_directory() . '/inc/customize/core/default.php';
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eportfolio_customize_register( $wp_customize ) {

	// Load custom controls.
	require get_template_directory() . '/inc/customize/core/control.php';

	// Load customize sanitize.
	require get_template_directory() . '/inc/customize/core/sanitize.php';

	// Load customize callback.
	require get_template_directory() . '/inc/customize/core/callback.php';

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	
	/*option panel for page-template details*/
	require get_template_directory() . '/inc/customize/page-template.php';

	/*theme option panel details*/
	require get_template_directory() . '/inc/customize/theme-option.php';	
	// Register custom section types.
	$wp_customize->register_section_type( 'eportfolio_Customize_Section_Upsell' );

	// Register sections.
	$wp_customize->add_section(
		new eportfolio_Customize_Section_Upsell(
			$wp_customize,
			'theme_upsell',
			array(
				'title'    => esc_html__( 'ePortfolio Pro', 'eportfolio' ),
				'pro_text' => esc_html__( 'Upgrade To Pro', 'eportfolio' ),
				'pro_url'  => 'https://www.themeinwp.com/theme/eportfolio-pro/',
				'priority'  => 1,
			)
		)
	);
}
add_action( 'customize_register', 'eportfolio_customize_register' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */
function eportfolio_customize_preview_js() {

	wp_enqueue_script( 'eportfolio_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );

}
add_action( 'customize_preview_init', 'eportfolio_customize_preview_js' );


function eportfolio_customizer_css() {
	wp_enqueue_script('eportfolio_customize_admin_js', get_template_directory_uri().'/assets/twp/js/customizer-admin.js', array('customize-controls'));

	wp_enqueue_style( 'eportfolio_customize_controls', get_template_directory_uri() . '/assets/twp/css/customizer-control.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'eportfolio_customizer_css',0 );
