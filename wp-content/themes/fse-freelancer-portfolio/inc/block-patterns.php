<?php
/**
 * Block Patterns
 *
 * @package fse_freelancer_portfolio
 * @since 1.0
 */

function fse_freelancer_portfolio_register_block_patterns() {
	$block_pattern_categories = array(
		'fse-freelancer-portfolio' => array( 'label' => esc_html__( 'FSE Freelancer Portfolio', 'fse-freelancer-portfolio' ) ),
		'pages' => array( 'label' => esc_html__( 'Pages', 'fse-freelancer-portfolio' ) ),
	);

	$block_pattern_categories = apply_filters( 'fse_freelancer_portfolio_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}
}
add_action( 'init', 'fse_freelancer_portfolio_register_block_patterns', 9 );