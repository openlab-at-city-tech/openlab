<?php
/**
 * Functions for block templates module
 *
 * @package Sydney
 */

if ( !Sydney_Modules::is_module_active( 'block-templates' ) ) {
	return;
}

/**
 * Load Customizer options
 */
function sydney_block_templates_customizer_options( $wp_customize ) {
	require get_template_directory() . '/inc/customizer/options/modules/block-templates.php';
}
add_action( 'customize_register', 'sydney_block_templates_customizer_options' );