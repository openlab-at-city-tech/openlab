<?php

//* Education Theme Setting Defaults
add_filter( 'genesis_theme_settings_defaults', 'education_theme_defaults' );
function education_theme_defaults( $defaults ) {

	$defaults['blog_cat_num']              = 3;	
	$defaults['content_archive']           = 'excerpts';
	$defaults['content_archive_limit']     = 0;
	$defaults['content_archive_thumbnail'] = 0;
	$defaults['image_alignment']           = 'alignleft';
	$defaults['posts_nav']                 = 'numeric';
	$defaults['site_layout']               = 'content-sidebar';

	return $defaults;

}

//* Education Theme Setup
add_action( 'after_switch_theme', 'education_theme_setting_defaults' );
function education_theme_setting_defaults() {

	if( function_exists( 'genesis_update_settings' ) ) {

		genesis_update_settings( array(
			'blog_cat_num'              => 3,	
			'content_archive'           => 'excerpts',
			'content_archive_limit'     => 0,
			'content_archive_thumbnail' => 0,
			'image_alignment'           => 'alignleft',
			'posts_nav'                 => 'numeric',
			'site_layout'               => 'content-sidebar',
		) );

		if ( function_exists( 'GenesisResponsiveSliderInit' ) ) {

			genesis_update_settings( array(
				'location_horizontal'             => 'right',
				'location_vertical'               => 'top',
				'posts_num'                       => '3',
				'slideshow_arrows'                => 0,
				'slideshow_excerpt_content_limit' => '170',
				'slideshow_excerpt_content'       => 'full',
				'slideshow_excerpt_width'         => '35',
				'slideshow_height'                => '800',
				'slideshow_more_text'             => __( 'Continue Reading', 'education' ),
				'slideshow_pager'                 => 0,
				'slideshow_title_show'            => 1,
				'slideshow_width'                 => '1600',
			), GENESIS_RESPONSIVE_SLIDER_SETTINGS_FIELD );

		}
		
	}
	
	update_option( 'posts_per_page', 3 );

}

//* Set Genesis Responsive Slider defaults
add_filter( 'genesis_responsive_slider_settings_defaults', 'education_responsive_slider_defaults' );
function education_responsive_slider_defaults( $defaults ) {

	$args = array(
		'location_horizontal'             => 'right',
		'location_vertical'               => 'top',
		'posts_num'                       => '3',
		'slideshow_arrows'                => 0,
		'slideshow_excerpt_content_limit' => '170',
		'slideshow_excerpt_content'       => 'full',
		'slideshow_excerpt_width'         => '35',
		'slideshow_height'                => '800',
		'slideshow_more_text'             => __( 'Continue Reading', 'education' ),
		'slideshow_pager'                 => 0,
		'slideshow_title_show'            => 1,
		'slideshow_width'                 => '1600',
	);

	$args = wp_parse_args( $args, $defaults );
	
	return $args;
}
