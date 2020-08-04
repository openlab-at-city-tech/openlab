<?php

/*	-----------------------------------------------------------------------------------------------
	THEME SETUP
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_setup' ) ) :
	function hamilton_setup() {
		
		// Automatic feed
		add_theme_support( 'automatic-feed-links' );
		
		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) $content_width = 560;
		
		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		
		// Custom Image Sizes
		add_image_size( 'hamilton_preview-image', 1200, 9999 );
		set_post_thumbnail_size( 1860, 9999 );
		
		// Background color
		add_theme_support( 'custom-background', array(
			'default-color' => 'ffffff',
		) );
		
		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 400,
			'width'       => 600,
			'flex-height' => true,
			'flex-width'  => true,
		) );
		
		// Title tag
		add_theme_support( 'title-tag' );
		
		// Add nav menu
		register_nav_menu( 'primary-menu', __( 'Primary Menu', 'hamilton' ) );
		register_nav_menu( 'secondary-menu', __( 'Secondary Menu', 'hamilton' ) );
		
		// Add excerpts to pages
		add_post_type_support( 'page', array( 'excerpt' ) );
		
		// HTML5 semantic markup
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		
		// Add Jetpack Infinite Scroll support
		add_theme_support( 'infinite-scroll', array(
			'type'           => 'click',
			'footer'		 => false,
			'footer_widgets' => false,
			'container'      => 'posts',
		) );
		
		// Make the theme translation ready
		load_theme_textdomain( 'hamilton', get_template_directory() . '/languages' );
		
	}
	add_action( 'after_setup_theme', 'hamilton_setup' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REQUIRED FILES
	Include required files
--------------------------------------------------------------------------------------------------- */

// Include the Hamilton Customizer class.
require get_template_directory() . '/inc/classes/class-hamilton-customize.php';


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_load_style' ) ) :
	function hamilton_load_style() {

		$dependencies = array();
		$theme_version = wp_get_theme( 'hamilton' )->get( 'Version' );

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'hamilton' );

		if ( 'off' !== $google_fonts ) {
			// Register Google Fonts
			wp_register_style( 'hamilton-fonts', '//fonts.googleapis.com/css?family=Libre+Franklin:300,400,400i,500,700,700i&amp;subset=latin-ext', false, $theme_version, 'all' );
			$dependencies[] = 'hamilton-fonts';
		}

		wp_enqueue_style( 'hamilton-style', get_stylesheet_uri(), $dependencies, $theme_version );
		
	}
	add_action( 'wp_enqueue_scripts', 'hamilton_load_style' );
endif;


/*	-----------------------------------------------------------------------------------------------
	ADD EDITOR STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_add_editor_styles' ) ) :
	function hamilton_add_editor_styles() {

		$editor_styles = array( 'assets/css/hamilton-classic-editor-styles.css' );

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'hamilton' );

		if ( 'off' !== $google_fonts ) {
			$editor_styles[] = '//fonts.googleapis.com/css?family=Libre+Franklin:300,400,400i,500,700,700i&amp;subset=latin-ext';
		}

		add_editor_style( $editor_styles );

	}
	add_action( 'init', 'hamilton_add_editor_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	DEACTIVATE DEFAULT WORDPRESS GALLERY STYLES
	Only applies to the shortcode gallery.
--------------------------------------------------------------------------------------------------- */

add_filter( 'use_default_gallery_style', '__return_false' );


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE SCRIPTS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_enqueue_scripts' ) ) :
	function hamilton_enqueue_scripts() {

		wp_enqueue_script( 'hamilton_global', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery', 'imagesloaded', 'masonry' ), '', true );

		if ( ( ! is_admin() ) && is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
	add_action( 'wp_enqueue_scripts', 'hamilton_enqueue_scripts' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER POST CLASSES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_post_classes' ) ) :
	function hamilton_post_classes( $classes ) {

		// Class indicating presence/lack of post thumbnail
		$classes[] = ( has_post_thumbnail() ? 'has-thumbnail' : 'missing-thumbnail' );
		
		return $classes;
	}
	add_action( 'post_class', 'hamilton_post_classes' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER BODY CLASSES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_body_classes' ) ) :
	function hamilton_body_classes( $classes ) {

		// Check whether we're in the customizer preview
		if ( is_customize_preview() ) {
			$classes[] = 'customizer-preview';
		}

		// Check whether we want it darker
		if ( get_theme_mod( 'hamilton_dark_mode' ) ) {
			$classes[] = 'dark-mode';
		}
		
		// Check whether we want the alt nav
		if ( get_theme_mod( 'hamilton_alt_nav' ) ) {
			$classes[] = 'show-alt-nav';
		}
		
		// Check whether we're doing three preview columns
		if ( get_theme_mod( 'hamilton_max_columns' ) ) {
			$classes[] = 'three-columns-grid';
		}
		
		// Check whether we're doing three preview columns
		if ( get_theme_mod( 'hamilton_show_titles' ) ) {
			$classes[] = 'show-preview-titles';
		}
		
		// Add short class to body if resumé page template
		if ( is_page_template( 'resume-page-template.php' ) ) {
			$classes[] = 'resume-template';
		}
		
		return $classes;

	}
	add_action( 'body_class', 'hamilton_body_classes' );
endif;


/*	-----------------------------------------------------------------------------------------------
	ADD NO-JS CLASS TO THE HTML ELEMENT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_has_js' ) ) :
	function hamilton_has_js() { 

		?>
		<script>jQuery( 'html' ).removeClass( 'no-js' ).addClass( 'js' );</script>
		<?php

	}
	add_action( 'wp_head', 'hamilton_has_js' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE TITLE
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_filter_archive_title' ) ) :
	function hamilton_filter_archive_title( $title ) {

		global $paged;

		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '#', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = get_the_date( 'Y' );
		} elseif ( is_month() ) {
			$title = get_the_date( 'F Y' );
		} elseif ( is_day() ) {
			$title = get_the_date( get_option( 'date_format' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title', 'hamilton' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title', 'hamilton' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( is_search() ) {
			$title = sprintf( __( 'Search: %s', 'hamilton' ), '&ldquo;' . get_search_query() . '&rdquo;' );
		} elseif ( is_home() ) {
			if ( $paged == 0 && get_theme_mod( 'hamilton_home_title' ) ) {
				$title = get_theme_mod( 'hamilton_home_title' );
			} else {
				$title = '';
			}
		}

		return $title;

	}
	add_filter( 'get_the_archive_title', 'hamilton_filter_archive_title' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE DESCRIPTION
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_filter_archive_description' ) ) :
	function hamilton_filter_archive_description( $description ) {

		if ( is_search() && have_posts() ) {
			global $wp_query;
			$description = sprintf( __( 'We found %s results matching your search.', 'hamilton' ), $wp_query->found_posts );
		} elseif ( is_search() && ! have_posts() ) {
			$description = sprintf( __( 'We could not find any results for your search.', 'hamilton' ), get_search_query() );
		}

		return $description;

	}
	add_filter( 'get_the_archive_description', 'hamilton_filter_archive_description' );
endif;


/* ---------------------------------------------------------------------------------------------
   DECLARE BLOCK EDITOR SUPPORT
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'hamilton_add_block_editor_features' ) ) :
	function hamilton_add_block_editor_features() {

		/* Block Editor Features ------------- */

		add_theme_support( 'align-wide' );

		/* Block Editor Palette -------------- */

		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Black', 'Name of the black color in the Gutenberg palette', 'hamilton' ),
				'slug' 	=> 'black',
				'color' => '#000',
			),
			array(
				'name' 	=> _x( 'Dark Gray', 'Name of the dark gray color in the Gutenberg palette', 'hamilton' ),
				'slug' 	=> 'dark-gray',
				'color' => '#333',
			),
			array(
				'name' 	=> _x( 'Medium Gray', 'Name of the medium gray color in the Gutenberg palette', 'hamilton' ),
				'slug' 	=> 'medium-gray',
				'color' => '#555',
			),
			array(
				'name' 	=> _x( 'Light Gray', 'Name of the light gray color in the Gutenberg palette', 'hamilton' ),
				'slug' 	=> 'light-gray',
				'color' => '#777',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Gutenberg palette', 'hamilton' ),
				'slug' 	=> 'white',
				'color' => '#fff',
			),
		) );

		/* Block Editor Font Sizes ----------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'hamilton' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'hamilton' ),
				'size' 		=> 17,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'hamilton' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'hamilton' ),
				'size' 		=> 20,
				'slug' 		=> 'regular',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'hamilton' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'hamilton' ),
				'size' 		=> 24,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'hamilton' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'hamilton' ),
				'size' 		=> 28,
				'slug' 		=> 'larger',
			),
		) );

	}
	add_action( 'after_setup_theme', 'hamilton_add_block_editor_features' );
endif;


/* ---------------------------------------------------------------------------------------------
   GUTENBERG EDITOR STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hamilton_block_editor_styles' ) ) :

	function hamilton_block_editor_styles() {

		$dependencies = array();
		$theme_version = wp_get_theme( 'hamilton' )->get( 'Version' );

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'hamilton' );

		if ( 'off' !== $google_fonts ) {

			// Register Google Fonts
			wp_register_style( 'hamilton-block-editor-styles-font', '//fonts.googleapis.com/css?family=Libre+Franklin:300,400,400i,500,700,700i&amp;subset=latin-ext', false, 1.0, 'all' );
			$dependencies[] = 'hamilton-block-editor-styles-font';

		}

		// Enqueue the editor styles
		wp_enqueue_style( 'hamilton-block-editor-styles', get_theme_file_uri( '/assets/css/hamilton-block-editor-styles.css' ), $dependencies, $theme_version, 'all' );

	}
	add_action( 'enqueue_block_editor_assets', 'hamilton_block_editor_styles', 1 );
endif;
