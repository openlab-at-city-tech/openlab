<?php
/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package WordPress
 * @subpackage cv-portfolio-blocks
 * @since cv-portfolio-blocks 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Register block styles.
	 *
	 * @since cv-portfolio-blocks 1.0
	 *
	 * @return void
	 */
	function cv_portfolio_blocks_register_block_styles() {
		
		// Image: Borders.
		register_block_style(
			'core/image',
			array(
				'name'  => 'cv-portfolio-blocks-border',
				'label' => esc_html__( 'Borders', 'cv-portfolio-blocks' ),
			)
		);

		
	}
	add_action( 'init', 'cv_portfolio_blocks_register_block_styles' );
}