<?php
/**
 * Recommended plugins.
 */
	
require get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

function fse_freelancer_portfolio_register_recommended_plugins() {
	$plugins = array(
		array(
			'name'             => __( 'Creta Testimonial Showcase', 'fse-freelancer-portfolio' ),
			'slug'             => 'creta-testimonial-showcase',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
	);
	$config = array();
	fse_freelancer_portfolio_tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'fse_freelancer_portfolio_register_recommended_plugins' );