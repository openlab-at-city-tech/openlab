<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Miniva
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses miniva_header_style()
 */
function miniva_custom_header_setup() {
	$args = apply_filters(
		'miniva_custom_header_args',
		array(
			'default-image'      => '',
			'default-text-color' => '333333',
			'width'              => 1000,
			'height'             => 250,
			'flex-height'        => true,
			'wp-head-callback'   => 'miniva_header_style',
		)
	);
	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'miniva_custom_header_setup' );

if ( ! function_exists( 'miniva_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see miniva_custom_header_setup().
	 */
	function miniva_header_style() {
		$header_text_color = get_header_textcolor();
		$header_image      = get_header_image();

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color && ! $header_image ) {
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
				width: 1px;
				height: 1px;
			}
			<?php
			// If the user has set a custom color for the text use that.
		else :
			?>
			.site-title a,
			.site-description,
			.primary-menu > li > a {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
			.header-cart .icon,
			.header-search .search-form label .icon {
				fill: #<?php echo esc_attr( $header_text_color ); ?>;
			}

		<?php endif; ?>

			<?php if ( $header_image ) : ?>
			.site-header { background-image: url(<?php echo esc_url( $header_image ); ?>); }
			<?php endif; ?>
		</style>
		<?php
	}
endif;
