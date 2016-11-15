<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

	<?php $header_image = get_header_image();
	if ( ! empty( $header_image ) ) { ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
	<?php } // if ( ! empty( $header_image ) ) ?>

 *
 * @package Coraline
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Rework this function to remove WordPress 3.4 support when WordPress 3.6 is released.
 *
 * @uses coraline_header_style()
 * @uses coraline_admin_header_style()
 * @uses coraline_admin_header_image()
 *
 * @package Coraline
 */
function coraline_custom_header_setup() {
	$args = array(
		'default-image'          => '%s/images/headers/water-drops.jpg',
		'default-text-color'     => '000',
		'width'                  => apply_filters( 'coraline_header_image_width', 990 ),
		'height'                 => apply_filters( 'coraline_header_image_height', 180 ),
		'flex-height'            => true,
		'wp-head-callback'       => 'coraline_header_style',
		'admin-head-callback'    => 'coraline_admin_header_style',
		'admin-preview-callback' => 'coraline_admin_header_image',
	);

	$args = apply_filters( 'coraline_custom_header_args', $args );

	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'water-drops' => array(
			'url' => '%s/images/headers/water-drops.jpg',
			'thumbnail_url' => '%s/images/headers/water-drops-thumbnail.jpg',
			'description' => __( 'Water drops', 'coraline' )
		),
		'limestone-cave' => array(
			'url' => '%s/images/headers/limestone-cave.jpg',
			'thumbnail_url' => '%s/images/headers/limestone-cave-thumbnail.jpg',
			'description' => __( 'Limestone cave', 'coraline' )
		),
		'Cactii' => array(
			'url' => '%s/images/headers/cactii.jpg',
			'thumbnail_url' => '%s/images/headers/cactii-thumbnail.jpg',
			'description' => __( 'Cactii', 'coraline' )
		),
	) );

	add_theme_support( 'custom-header', $args );

	/*
	 * We'll be using post thumbnails for custom header images on posts and pages.
	 * We want them to be 990 pixels wide by 180 pixels tall.
	 * Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	 */
	set_post_thumbnail_size( $args['width'], $args['height'], true );
}
add_action( 'after_setup_theme', 'coraline_custom_header_setup' );

if ( ! function_exists( 'coraline_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see coraline_custom_header_setup().
 */
function coraline_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		#site-title,
		#site-description {
			position: absolute;
			left: -9000px;
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // coraline_header_style

if ( ! function_exists( 'coraline_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see coraline_custom_header_setup().
 */
function coraline_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background: #<?php echo get_background_color(); ?>;
		border: none;
		text-align: center;
		width: 990px;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 36px;
		letter-spacing: -0.03em;
		line-height: 42px;
		text-decoration: none;
	}
	#desc {
		font-size: 18px;
		line-height: 31px;
		padding: 0 0 9px 0;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		width: 100%;
		max-width: 100%;
		height: auto;
	}
	</style>
<?php
}
endif; // coraline_admin_header_style

if ( ! function_exists( 'coraline_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see coraline_custom_header_setup().
 */
function coraline_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<img src="<?php esc_url ( header_image() ); ?>" alt="" />
	</div>
<?php }
endif; // coraline_admin_header_image