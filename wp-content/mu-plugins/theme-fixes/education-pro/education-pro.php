<?php

/**
 * Disable auto-update support for the theme.
 *
 * We manage the theme independently. This also prevents 'Updates' section from appearing
 * on the theme's Settings panel.
 */
remove_theme_support( 'genesis-auto-updates' );

/**
 * Remove unused Settings metaboxes.
 */
add_action(
	'load-toplevel_page_genesis',
	function() {
		remove_meta_box( 'genesis-theme-settings-adsense', get_current_screen(), 'main'  );
		remove_meta_box( 'genesis-theme-settings-scripts', get_current_screen(), 'main'  );
	},
	50
);

/**
 * Move Genesis 'Theme Settings' Customizer panel higher in the order.
 */
add_filter(
	'genesis_customizer_theme_settings_config',
	function( $config ) {
		$config['genesis']['priority'] = 25;
		return $config;
	}
);

/**
 * More Customizer mods.
 */
add_action(
	'customize_register',
	function( $wp_customize ) {
		// Reordering.
		$wp_customize->add_section( 'static_front_page', array(
			'title'          => __( 'Homepage Settings' ),
			'priority'       => 65,
			'description'    => __( 'You can choose what&#8217;s displayed on the homepage of your site. It can be posts in reverse chronological order (classic blog), or a fixed/static page. To set a static homepage, you first need to create two Pages. One will become the homepage, and the other will be where your posts are displayed.' ),
			'active_callback' => array( $wp_customize, 'has_published_pages' ),
		) );

		$wp_customize->add_section( 'colors', array(
			'title'    => __( 'Background Color' ),
			'priority' => 120,
		) );

//		$wp_customize->remove_section( 'background_image' );
		$wp_customize->add_section( 'background_image', array(
			'title'          => __( 'Background Image' ),
			'theme_supports' => 'custom-background',
			'priority'       => 130,
		) );

		$wp_customize->remove_section( 'custom_css' );
	}
);
