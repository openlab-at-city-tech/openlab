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

		$wp_customize->add_section( 'background_image', array(
			'title'          => __( 'Background Image' ),
			'theme_supports' => 'custom-background',
			'priority'       => 130,
		) );

		$wp_customize->remove_section( 'custom_css' );

		// 'Theme Settings' subsections.
		$wp_customize->remove_section( 'genesis_adsense' );
		$wp_customize->remove_section( 'genesis_scripts' );
	}
);

// Add support for additional color style options.
remove_theme_support( 'genesis-style-selector' );
add_theme_support( 'genesis-style-selector', array(
	'education-pro-blue'   => 'Blue',
	'education-pro-green'  => 'Green',
	'education-pro-red'    => 'Red',
) );

// Convert "Header Right" widget area to a nav area.
unregister_sidebar( 'header-right' );
register_nav_menu( 'title-menu', 'Main Nav' );
add_action(
	'genesis_header_right',
	function() {
		add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
		add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
		echo genesis_get_nav_menu( [ 'theme_location' => 'title-menu' ] );
		remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
		remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
	}
);

/**
 * Modify Genesis default nav areas.
 *
 * - Rename 'primary'.
 * - Remove 'secondary' (footer menu).
 *
 * Must come after 'after_setup_theme' to follow Genesis nav registration.
 */
add_action(
	'after_setup_theme',
	function() {
		register_nav_menu( 'primary', 'Top Menu' );
		unregister_nav_menu( 'secondary' );
	},
	20
);

/**
 * Don't add dynamic nav items in the 'title-menu' location.
 */
add_filter(
	'openlab_add_dynamic_nav_items',
	function( $retval, $args ) {
		if ( 'title-menu' === $args->theme_location ) {
			$retval = false;
		}

		return $retval;
	},
	10,
	2
);

register_default_headers( [
	'circles' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/1circles.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/1circles.png' ),
		'description'   => 'Circles',
	],
] );

add_action(
	'wp_head',
	function() {
		$print_css_url = content_url( 'mu-plugins/theme-fixes/education-pro/print.css' );
		?>
<link rel="stylesheet" href="<?php echo esc_attr( $print_css_url ); ?>" type="text/css" media="print" />
		<?php
	}
);
