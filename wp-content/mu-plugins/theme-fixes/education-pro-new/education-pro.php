<?php

/**
 * Disable auto-update support for the theme.
 *
 * We manage the theme independently. This also prevents 'Updates' section from appearing
 * on the theme's Settings panel.
 */
remove_theme_support( 'genesis-auto-updates' );

//* Add support for custom header
remove_theme_support( 'custom-header' );
add_theme_support( 'custom-header', array(
	'width'           => 2000,
	'height'          => 130,
	'header-selector' => 'a.title-area-link',
	'header-text'     => false,
) );

// There's literally no other way to do this.
$header_dimensions_callback = function() {
	global $_wp_theme_features;
	$_wp_theme_features['custom-header'][0]['width'] = 2000;
	$_wp_theme_features['custom-header'][0]['height'] = 130;
};
add_action( 'customize_controls_enqueue_scripts', $header_dimensions_callback, 0 );
add_action( 'wp_ajax_custom-header-crop', $header_dimensions_callback, 0 );

add_action(
	'customize_controls_print_styles',
	function() {
		?>
<style type="text/css">
.customize-control-header .uploaded button,
.customize-control-header .default button {
	height: 60px;
	overflow: hidden;
}
.customize-control-header .uploaded button.random,
.customize-control-header .default button.random {
	height: inherit;
	overflow: auto;
}
#customize-controls .customize-control-header .uploaded button img,
#customize-controls .customize-control-header .default button img {
	height: 60px;
	max-width: initial;
}
</style>
		<?php
	}
);

add_action(
	'customize_controls_enqueue_scripts',
	function() {
		wp_enqueue_script( 'openlab-education-pro-customize', content_url( 'mu-plugins/theme-fixes/education-pro/customize.js', array( 'jquery' ) ) );
	}
);

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

		if ( ! is_super_admin() ) {
			$wp_customize->remove_section( 'custom_css' );
		}

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

add_filter(
	'genesis_get_layouts',
	function( $layouts ) {
		$keys = [ 'content-sidebar', 'sidebar-content', 'full-width-content' ];
		return array_filter(
			$layouts,
			function( $k ) use ( $keys ) {
				return in_array( $k, $keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
);

$deregister_sidebars = [ 'home-featured', 'home-top', 'home-middle', /*'home-bottom',*/ 'sidebar-alt' ];
foreach ( $deregister_sidebars as $deregister_sidebar ) {
	unregister_sidebar( $deregister_sidebar );
}

add_action(
	'template_redirect',
	function() {
		// Handled separately.
		if ( is_front_page() ) {
			return;
		}

		add_action(
			'genesis_before_footer',
			function() {
				genesis_widget_area( 'home-bottom', array(
					'before' => '<div class="home-bottom widget-area"><div class="wrap">',
					'after'  => '</div></div>',
				) );
			}
		);
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
		register_nav_menu( 'primary', 'Main Nav' );
		unregister_nav_menu( 'secondary' );

		// Undo Education Pro's nav swap.
		remove_action( 'genesis_before_header', 'genesis_do_nav' );
		add_action( 'genesis_after_header', 'genesis_do_nav' );
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
	'foil' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/foil.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/foil.png' ),
		'description'   => 'Foil',
	],
	'leaves' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/leaves.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/leaves.png' ),
		'description'   => 'Leaves',
	],
	'numbers' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/numbers.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/numbers.png' ),
		'description'   => 'Numbers',
	],
	'candy' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/candy.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/candy.png' ),
		'description'   => 'Candy',
	],
	'firewood' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/firewood.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/firewood.png' ),
		'description'   => 'Firewood',
	],
	'circles' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/circles.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/circles.png' ),
		'description'   => 'Circles',
	],
	'fabric' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/fabric.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/fabric.png' ),
		'description'   => 'Fabric',
	],
	'water' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/water.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/water.png' ),
		'description'   => 'Water',
	],
	'stonefloor' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/stonefloor.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/stonefloor.png' ),
		'description'   => 'Stone Floor',
	],
	'riverrocks' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/riverrocks.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/riverrocks.png' ),
		'description'   => 'River Rocks',
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

remove_action( 'wp_head', 'genesis_custom_header_style' );
add_action( 'wp_head', 'openlab_custom_header_style' );
/**
 * Custom header callback.
 *
 * It outputs special CSS to the document head, modifying the look of the header based on user input.
 *
 * @since 1.6.0
 *
 * @return void Return early if `custom-header` not supported, user specified own callback, or no options set.
 */
function openlab_custom_header_style() {

	// Do nothing if custom header not supported.
	if ( ! current_theme_supports( 'custom-header' ) ) {
		return;
	}

	// Do nothing if user specifies their own callback.
	if ( get_theme_support( 'custom-header', 'wp-head-callback' ) ) {
		return;
	}

	$output = '';

	$header_image = get_header_image();
	$text_color   = get_header_textcolor();

	// If no options set, don't waste the output. Do nothing.
	if ( empty( $header_image ) && ! display_header_text() && get_theme_support( 'custom-header', 'default-text-color' ) === $text_color ) {
		return;
	}

	$header_selector = get_theme_support( 'custom-header', 'header-selector' );
	$title_selector  = genesis_html5() ? '.custom-header .site-title' : '.custom-header #title';
	$desc_selector   = genesis_html5() ? '.custom-header .site-description' : '.custom-header #description';

	// Header selector fallback.
	if ( ! $header_selector ) {
		$header_selector = genesis_html5() ? '.custom-header .site-header' : '.custom-header #header';
	}

	$gradient = 'rgba(170,36,24,0.7)';
	$scheme   = genesis_get_option( 'style_selection' );
	switch ( $scheme ) {
		case 'education-pro-blue' :
			$gradient = 'rgba(186,208,222,0.7)';
		break;

		case 'education-pro-red' :
			$gradient = 'rgba(219,47,31,0.7)';
		break;

		case 'education-pro-green' :
			$gradient = 'rgba(209,222,186,0.7)';
		break;
	}

	// Header image CSS, if exists.
	if ( $header_image ) {
		$output .= sprintf( '%s { background: linear-gradient( %s, %s ), url(%s) no-repeat !important; }', $header_selector, $gradient, $gradient, esc_url( $header_image ) );
	}

	// Header text color CSS, if showing text.
	if ( display_header_text() && get_theme_support( 'custom-header', 'default-text-color' ) !== $text_color ) {
		$output .= sprintf( '%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html( $text_color ), esc_html( $title_selector ), esc_html( $desc_selector ) );
	}

	if ( $output ) {
		printf( '<style type="text/css">%s</style>' . "\n", $output );
	}

}

/**
 * Remove Copyright text in footer.
 */
add_filter(
	'genesis_footer_creds_text',
	function( $text ) {
		$regex = '/\[footer_copyright[^\]]+\] &#x000B7;/';
		return preg_replace( $regex, '', $text );
	}
);


remove_action( 'genesis_header', 'genesis_do_header' );
add_action( 'genesis_header', 'openlab_genesis_do_header' );
function openlab_genesis_do_header() {
	global $wp_registered_sidebars;

	genesis_markup( array(
		'open'    => '<a class="title-area-link" href="' . home_url() . '"><div %s>',
		'context' => 'title-area',
	) );

		/**
		 * Fires inside the title area, before the site description hook.
		 *
		 * @since 2.6.0
		 */
		do_action( 'genesis_site_title' );

		/**
		 * Fires inside the title area, after the site title hook.
		 *
		 * @since 1.0.0
		 */
		do_action( 'genesis_site_description' );

	genesis_markup( array(
		'close'   => '</div></a>',
		'context' => 'title-area',
	) );

	if ( has_action( 'genesis_header_right' ) || ( isset( $wp_registered_sidebars['header-right'] ) && is_active_sidebar( 'header-right' ) ) ) {

		genesis_markup( array(
			'open'    => '<div %s>',
			'context' => 'header-widget-area',
		) );

			/**
			 * Fires inside the header widget area wrapping markup, before the Header Right widget area.
			 *
			 * @since 1.5.0
			 */
			do_action( 'genesis_header_right' );
			add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
			add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
			dynamic_sidebar( 'header-right' );
			remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
			remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );

		genesis_markup( array(
			'close'   => '</div>',
			'context' => 'header-widget-area',
		) );

	}

}

remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
add_action( 'genesis_site_title', 'openlab_genesis_seo_site_title' );
/**
 * Echo the site title into the header.
 *
 * Depending on the SEO option set by the user, this will either be wrapped in an `h1` or `p` element.
 *
 * Applies the `genesis_seo_title` filter before echoing.
 *
 * @since 1.1.0
 */
function openlab_genesis_seo_site_title() {

	// Set what goes inside the wrapping tags.
	$inside = sprintf( '%s', get_bloginfo( 'name' ) );

	// Determine which wrapping tags to use.
	$wrap = genesis_is_root_page() && 'title' === genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : 'p';

	// A little fallback, in case an SEO plugin is active.
	$wrap = genesis_is_root_page() && ! genesis_get_seo_option( 'home_h1_on' ) ? 'h1' : $wrap;

	// Wrap homepage site title in p tags if static front page.
	$wrap = is_front_page() && ! is_home() ? 'p' : $wrap;

	// And finally, $wrap in h1 if HTML5 & semantic headings enabled.
	$wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

	/**
	 * Site title wrapping element
	 *
	 * The wrapping element for the site title.
	 *
	 * @since 2.2.3
	 *
	 * @param string $wrap The wrapping element (h1, h2, p, etc.).
	 */
	$wrap = apply_filters( 'genesis_site_title_wrap', $wrap );

	// Build the title.
	$title = genesis_markup( array(
		'open'    => sprintf( "<{$wrap} %s>", genesis_attr( 'site-title' ) ),
		'close'   => "</{$wrap}>",
		'content' => $inside,
		'context' => 'site-title',
		'echo'    => false,
		'params'  => array(
			'wrap' => $wrap,
		),
	) );

	echo apply_filters( 'genesis_seo_title', $title, $inside, $wrap ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}

/**
 * Prevent resizing from resize-image-after-upload.
 */
add_filter(
	'wp_handle_upload',
	function( $image_data ) {
		remove_action( 'wp_handle_upload', 'jr_uploadresize_resize' );
		return $image_data;
	},
	0
);

/**
 * Add featured image to the beginning of post content.
 */
add_action(
	'genesis_entry_content',
	function() {
		if ( ! is_singular() ) {
			return;
		}

		$img = genesis_get_image( array(
			'format'  => 'html',
			'size'    => 'full',
			'context' => 'archive',
			'attr'    => 'post-image entry-image',
		) );

		if ( ! empty( $img ) ) {
			genesis_markup( array(
				'open'    => '<a %s>',
				'close'   => '</a>',
				'content' => wp_make_content_images_responsive( $img ),
				'context' => 'entry-image-link',
			) );
		}
	},
	8
);
