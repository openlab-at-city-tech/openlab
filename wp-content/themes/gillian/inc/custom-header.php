<?php
/**
 * Custom Header feature.
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package gillian
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses gillian_header_style()
 */
function gillian_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'gillian_custom_header_args', array(
		'default-text-color'     => 'ffffff',
		'default-image'          => get_template_directory_uri() . '/images/default.png',
		'width'                  => 1920,
		'height'                 => 265,
		'wp-head-callback'       => 'gillian_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'gillian_custom_header_setup' );

if ( ! function_exists( 'gillian_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @see gillian_custom_header_setup().
 */
function gillian_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail.
	// get_header_textcolor() options: add_theme_support( 'custom-header' ) is default, hide text (returns 'blank') or any hex value.
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
		}
	<?php
		// If the user has set a custom color for the text use that.
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif;
