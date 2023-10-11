<?php
/**
 * ePortfolio functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ePortfolio
 */

if ( ! function_exists( 'eportfolio_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function eportfolio_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on ePortfolio, use a find and replace
		 * to change 'eportfolio' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'eportfolio', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary-nav' => esc_html__( 'Primary Menu', 'eportfolio' ),
			'social-nav' => esc_html__( 'Social Menu', 'eportfolio' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		 /*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
		    'image',
		    'video',
		    'quote',
		    'gallery',
		    'audio',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'eportfolio_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'eportfolio_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function eportfolio_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'eportfolio_content_width', 640 );
}
add_action( 'after_setup_theme', 'eportfolio_content_width', 0 );

/**
 * function for google fonts
 */
if (!function_exists('eportfolio_fonts_url')) :

    /**
     * Return fonts URL.
     *
     * @since 1.0.0
     * @return string Fonts URL.
     */
    function eportfolio_fonts_url(){

        $fonts_url = '';
        $fonts = array();

        $eportfolio_primary_font   = 'Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i';

        $eportfolio_fonts   = array();
        $eportfolio_fonts[] = $eportfolio_primary_font;

        for ($i = 0; $i < count($eportfolio_fonts); $i++) {

            if ('off' !== sprintf(_x('on', '%s font: on or off', 'eportfolio'), $eportfolio_fonts[$i])) {
                $fonts[] = $eportfolio_fonts[$i];
            }

        }

        if ($fonts) {
            $fonts_url = add_query_arg(array(
                'family' => urldecode(implode('|', $fonts)),
                'display' => 'swap',
            ), 'https://fonts.googleapis.com/css');
        }

        return $fonts_url;
    }
endif;

/**
 * Enqueue scripts and styles.
 */
function eportfolio_scripts() {
	wp_enqueue_style('font-awesome', get_template_directory_uri().'/assets/libraries/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('slick', get_template_directory_uri().'/assets/libraries/slick/css/slick.css');
	wp_enqueue_style('magnific', get_template_directory_uri().'/assets/libraries/magnific/css/magnific-popup.css');

	wp_enqueue_script( 'eportfolio-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script('jquery-slick', get_template_directory_uri() . '/assets/libraries/slick/js/slick.min.js', array('jquery'), '', true);
	wp_enqueue_script('jquery-magnific', get_template_directory_uri() . '/assets/libraries/magnific/js/jquery.magnific-popup.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'eportfolio-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_script('theiaStickySidebar', get_template_directory_uri() . '/assets/libraries/theiaStickySidebar/theia-sticky-sidebar.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'imagesloaded' );
    wp_enqueue_script('masonry');

    $fonts_url = eportfolio_fonts_url();
    if (!empty($fonts_url)) {
        wp_enqueue_style('eportfolio-google-fonts', $fonts_url, array(), null);
    }
    $args = eportfolio_get_localized_variables();

	wp_enqueue_script( 'eportfolio-script', get_template_directory_uri() . '/assets/twp/js/main.js',  array( 'jquery', 'wp-mediaelement' ), '', true );

    wp_localize_script( 'eportfolio-script', 'ePortfolioVal', $args );

	wp_enqueue_style( 'eportfolio-style', get_stylesheet_uri() );
    wp_style_add_data('eportfolio-style', 'rtl', 'replace');


    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'eportfolio_scripts' );


/*
* Tgmpa plugin activation.
*/
require get_template_directory().'/assets/libraries/TGM-Plugin/class-tgm-plugin-activation.php';

/**
 * Enqueue admin scripts and styles.
 */
function eportfolio_admin_scripts( $hook ) {
	$current_screen = get_current_screen();
	    if( $current_screen->id != "widgets" ) {

			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
		    
		    wp_enqueue_script('eportfolio-admin', get_template_directory_uri() . '/assets/twp/js/admin.js', array('jquery'), '', 1);

		    $ajax_nonce = wp_create_nonce('eportfolio_ajax_nonce');
					
			wp_localize_script( 
		        'eportfolio-admin',
		        'eportfolio_admin',
		        array(
		            'ajax_url'   => esc_url( admin_url( 'admin-ajax.php' ) ),
		            'ajax_nonce' => $ajax_nonce,
		            'active' => esc_html__('Active','eportfolio'),
			        'deactivate' => esc_html__('Deactivate','eportfolio'),
		         )
		    );

		}
	if ( 'widgets.php' === $hook ) {
	    wp_enqueue_media();
		wp_enqueue_script( 'eportfolio-custom-widgets', get_template_directory_uri() . '/assets/twp/js/widgets.js', array( 'jquery' ), '1.0.0', true );
	}
	wp_enqueue_style( 'eportfolio-custom-admin-style', get_template_directory_uri() . '/assets/twp/css/wp-admin.css', array(), '1.0.0' );

}
add_action( 'admin_enqueue_scripts', 'eportfolio_admin_scripts' );
/**
 * Implement the Custom Header feature.
 */

require get_template_directory() . '/inc/single-meta.php';
require get_template_directory() . '/inc/widgets/widgets-init.php';
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/hooks/blog-banner-slider.php';
require get_template_directory() . '/inc/localized-variables.php';

require get_template_directory() . '/classes/admin-notice.php';
require get_template_directory() . '/classes/plugin-classes.php';
require get_template_directory() . '/classes/about.php';


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customize/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Add featured image as background image to post navigation elements.
 *
 * @since ePortfolio 1.0
 *
 */
function eportfolio_post_nav_background() {
    if ( ! is_single() ) {
        return;
    }

    $previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
    $next     = get_adjacent_post( false, '', false );
    $css      = '';

    if ( is_attachment() && 'attachment' == $previous->post_type ) {
        return;
    }

    if ( $previous &&  has_post_thumbnail( $previous->ID ) ) {
        $prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'post-thumbnail' );
        $css .= '
			.post-navigation .nav-previous { background-image: url(' . esc_url( $prevthumb[0] ) . '); }
			.post-navigation .nav-previous .post-title, .post-navigation .nav-previous a:hover .post-title, .post-navigation .nav-previous a:focus .post-title, .post-navigation .nav-previous .meta-nav { color: #fff; }
			.post-navigation .nav-previous a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
    }

    if ( $next && has_post_thumbnail( $next->ID ) ) {
        $nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'post-thumbnail' );
        $css .= '
			.post-navigation .nav-next { background-image: url(' . esc_url( $nextthumb[0] ) . '); border-top: 0; }
			.post-navigation .nav-next .post-title, .post-navigation .nav-next a:hover .post-title, .post-navigation .nav-next a:focus .post-title, .post-navigation .nav-next .meta-nav { color: #fff; }
			.post-navigation .nav-next a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
    }

    wp_add_inline_style( 'eportfolio-style', $css );
}
add_action( 'wp_enqueue_scripts', 'eportfolio_post_nav_background' );


add_filter( 'walker_nav_menu_start_el', 'eportfolio_add_description', 10, 2 );
function eportfolio_add_description( $item_output, $item ) {
    $description = $item->post_content;
    if (('' !== $description) && (' ' !== $description) ) {
        return preg_replace( '/(<a.*)</', '$1' . '<span class="twp-menu-description">' . $description . '</span><', $item_output) ;
    }
    else {
        return $item_output;
    };
}

add_filter('themeinwp_enable_demo_import_compatiblity','eportfolio_demo_import_filter_apply');

if( !function_exists('eportfolio_demo_import_filter_apply') ):

	function eportfolio_demo_import_filter_apply(){

		return true;

	}

endif;