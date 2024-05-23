<?php
/**
 * Block Styles
 *
 * @package fse_freelancer_portfolio
 * @since 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	function fse_freelancer_portfolio_register_block_styles() {

		//Wp Block Padding Zero
		register_block_style(
			'core/group',
			array(
				'name'  => 'fse-freelancer-portfolio-padding-0',
				'label' => esc_html__( 'No Padding', 'fse-freelancer-portfolio' ),
			)
		);

		//Wp Block Post Author Style
		register_block_style(
			'core/post-author',
			array(
				'name'  => 'fse-freelancer-portfolio-post-author-card',
				'label' => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);

		//Wp Block Button Style
		register_block_style(
			'core/button',
			array(
				'name'         => 'fse-freelancer-portfolio-button',
				'label'        => esc_html__( 'Plain', 'fse-freelancer-portfolio' ),
			)
		);

		//Post Comments Style
		register_block_style(
			'core/post-comments',
			array(
				'name'         => 'fse-freelancer-portfolio-post-comments',
				'label'        => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);

		//Latest Comments Style
		register_block_style(
			'core/latest-comments',
			array(
				'name'         => 'fse-freelancer-portfolio-latest-comments',
				'label'        => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);


		//Wp Block Table Style
		register_block_style(
			'core/table',
			array(
				'name'         => 'fse-freelancer-portfolio-wp-table',
				'label'        => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);


		//Wp Block Pre Style
		register_block_style(
			'core/preformatted',
			array(
				'name'         => 'fse-freelancer-portfolio-wp-preformatted',
				'label'        => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);

		//Wp Block Verse Style
		register_block_style(
			'core/verse',
			array(
				'name'         => 'fse-freelancer-portfolio-wp-verse',
				'label'        => esc_html__( 'Theme Style', 'fse-freelancer-portfolio' ),
			)
		);
	}
	add_action( 'init', 'fse_freelancer_portfolio_register_block_styles' );
}
