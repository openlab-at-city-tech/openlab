<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Pilcrow
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses pilcrow_header_style()
 * @uses pilcrow_admin_header_style()
 * @uses pilcrow_admin_header_image()
 *
 * @package Pilcrow
 */
function pilcrow_custom_header_setup() {
	$args = array(
		'default-image'          => '%s/images/headers/books.jpg',
		'default-text-color'     => '000',
		'width'                  => apply_filters( 'pilcrow_header_image_width', 990 ),
		'height'                 => apply_filters( 'pilcrow_header_image_height', 257 ),
		'wp-head-callback'       => 'pilcrow_header_style',
		'admin-head-callback'    => 'pilcrow_admin_header_style',
		'admin-preview-callback' => 'pilcrow_admin_header_image',
	);

	$options = pilcrow_get_theme_options();
	if ( in_array( $options['theme_layout'], array( 'content-sidebar', 'sidebar-content' ) ) ) {
		$args['width']  = apply_filters( 'pilcrow_header_image_width', 770 );
		$args['height'] = apply_filters( 'pilcrow_header_image_height', 200 );
	}

	add_theme_support( 'custom-header', apply_filters( 'pilcrow_custom_header_args', $args ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( get_custom_header()->width, get_custom_header()->height, true );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'books'   => array(
			'url'           => '%s/images/headers/books.jpg',
			'thumbnail_url' => '%s/images/headers/books-thumbnail.jpg',
			'description'   => _x( 'Books', 'Header image description', 'pilcrow' )
		),
		'record'  => array(
			'url'           => '%s/images/headers/record.jpg',
			'thumbnail_url' => '%s/images/headers/record-thumbnail.jpg',
			'description'   => _x( 'Record', 'Header image description', 'pilcrow' )
		),
		'pattern' => array(
			'url'           => '%s/images/headers/pattern.jpg',
			'thumbnail_url' => '%s/images/headers/pattern-thumbnail.jpg',
			'description'   => _x( 'Pattern', 'Header image description', 'pilcrow' )
		),
	) );
}
add_action( 'after_setup_theme', 'pilcrow_custom_header_setup' );

if ( ! function_exists( 'pilcrow_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see pilcrow_custom_header_setup().
 */
function pilcrow_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color )
		return;

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		#site-title {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
		#nav {
			margin-top: 18px;
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a {
			color: #<?php echo $header_text_color; ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // pilcrow_header_style

if ( ! function_exists( 'pilcrow_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see pilcrow_custom_header_setup().
 */
function pilcrow_admin_header_style() {
?>
	<style type="text/css">
		/* Shows the same border as on front end */
		.appearance_page_custom-header #headimg {
			border: none;
			width: <?php echo get_custom_header()->width; ?>px;
			max-width: 800px;
		}
		#site-title {
			font-family: Georgia, serif;
			text-align: right;
			margin: 0;
		}
		#site-title a {
			color: #000;
			font-size: 40px;
			font-weight: bold;
			line-height: 72px;
			text-decoration: none;
		}
		#headimg img {
			height: auto;
			width: 100%;
		}
		/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
			#headimg #name { }
			#headimg #desc { }
		*/
	</style>
<?php
}
endif; // pilcrow_admin_header_style

if ( ! function_exists( 'pilcrow_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see pilcrow_custom_header_setup().
 */
function pilcrow_admin_header_image() {
	$style        = sprintf( ' style="color:#%s;"', get_header_textcolor() );
	$header_image = get_header_image();
?>
	<div id="headimg">
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<?php if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php
}
endif; // pilcrow_admin_header_image
