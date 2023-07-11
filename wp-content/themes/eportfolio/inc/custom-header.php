<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package ePortfolio
 */
	register_default_headers( array(
	    'default-image' => array(
	        'url'           => get_stylesheet_directory_uri() . '/assets/img/header-bg.jpg',
	        'thumbnail_url' => get_stylesheet_directory_uri() . '/assets/img/header-bg.jpg',
	    ),
	) );
/**
 * Set up the WordPress core custom header feature.
 *
 * @uses eportfolio_header_style()
 */
function eportfolio_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'eportfolio_custom_header_args', array(
		'default-text-color'     => 'fff',
		'width'            => 1080,
		'height'           => 1920,
		'flex-height'      => true,
		'default-image' 		=> esc_url( get_stylesheet_directory_uri() . '/assets/img/header-bg.jpg'),
		'wp-head-callback'       => 'eportfolio_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'eportfolio_custom_header_setup' );

if ( ! function_exists( 'eportfolio_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see eportfolio_custom_header_setup().
	 */
	function eportfolio_header_style() {
		$header_text_color = get_header_textcolor();

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
			?>
			.site-title,
			.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
                display: none;
			}
		<?php
		// If the user has set a custom color for the text use that.
		else :
			?>
        .site-title a,
        #masthead ul.twp-social-icons.twp-social-icons-white a {
            color: #<?php echo esc_attr( $header_text_color ); ?>;
        }

        #masthead .twp-menu-icon.twp-white-menu-icon span:before,
        #masthead .twp-menu-icon.twp-white-menu-icon span:after {
            background-color: #<?php echo esc_attr( $header_text_color ); ?>;
        }
		<?php endif; ?>
		</style>
		<?php
	}
endif;
