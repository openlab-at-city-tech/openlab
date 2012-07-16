<?php
/**
 * @package WordPress
 * @subpackage Coraline
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 500;

/** Tell WordPress to run coraline_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'coraline_setup' );

if ( ! function_exists( 'coraline_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * To override coraline_setup() in a child theme, add your own coraline_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Coraline 1.0
 */
function coraline_setup() {

	// This theme has some pretty cool theme options
	require_once ( get_template_directory() . '/inc/theme-options.php' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Post Format support. Legacy category chooser will display in Theme Options for sites that set a category before post formats were added.
	add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'coraline', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'coraline' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '000' );
	// No CSS, just an IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/water-drops.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to coraline_header_image_width and coraline_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'coraline_header_image_width', 990 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'coraline_header_image_height', 180 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See coraline_admin_header_style(), below.
	add_custom_image_header( 'coraline_header_style', 'coraline_admin_header_style', 'coraline_admin_header_image' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'water-drops' => array(
			'url' => '%s/images/headers/water-drops.jpg',
			'thumbnail_url' => '%s/images/headers/water-drops-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Water drops', 'coraline' )
		),
		'limestone-cave' => array(
			'url' => '%s/images/headers/limestone-cave.jpg',
			'thumbnail_url' => '%s/images/headers/limestone-cave-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Limestone cave', 'coraline' )
		),
		'Cactii' => array(
			'url' => '%s/images/headers/cactii.jpg',
			'thumbnail_url' => '%s/images/headers/cactii-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Cactii', 'coraline' )
		)
	) );
}
endif;

if ( ! function_exists( 'coraline_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @since Coraline 1.0
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
endif;


if ( ! function_exists( 'coraline_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in coraline_setup().
 *
 * @since Coraline 1.0
 */
function coraline_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background: #<?php echo get_background_color(); ?>;
		border: none;
		text-align: center;
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
		max-width: 990px;
		width: 100%;
	}
	</style>
<?php
}
endif;

if ( ! function_exists( 'coraline_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in coraline_setup().
 *
 * @since Coraline 1.0
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
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since Coraline 1.0
 */
function coraline_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'coraline_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * @since Coraline 1.0
 * @return int
 */
function coraline_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'coraline_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Coraline 1.0
 * @return string "Continue Reading" link
 */
function coraline_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'coraline' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and coraline_continue_reading_link().
 *
 * @since Coraline 1.0
 * @return string An ellipsis
 */
function coraline_auto_excerpt_more( $more ) {
	return ' &hellip;' . coraline_continue_reading_link();
}
add_filter( 'excerpt_more', 'coraline_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * @since Coraline 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function coraline_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= coraline_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'coraline_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Coraline's style.css.
 *
 * @since Coraline 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function coraline_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'coraline_remove_gallery_css' );

if ( ! function_exists( 'coraline_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own coraline_comment(), and that function will be used instead.
 *
 * @since Coraline 1.0
 */
function coraline_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 48 ); ?>

			<cite class="fn"><?php comment_author_link(); ?></cite>

			<span class="comment-meta commentmetadata">
				|
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'coraline' ),
						get_comment_date(),
						get_comment_time()
					); ?></a>
					|
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					<?php edit_comment_link( __( 'Edit', 'coraline' ), ' | ' );
				?>
			</span><!-- .comment-meta .commentmetadata -->
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'coraline' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-body"><?php comment_text(); ?></div>

	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'coraline' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'coraline' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * @since Coraline 1.0
 * @uses register_sidebar
 */
function coraline_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'coraline' ),
		'id' => 'sidebar-1',
		'description' => __( 'The primary widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'coraline' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area appears in 3-column layouts', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located above the primary and secondary sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts. Empty by default.
	register_sidebar( array(
		'name' => __( 'Feature Widget Area', 'coraline' ),
		'id' => 'feature-widget-area',
		'description' => __( 'The feature widget above the sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'coraline' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'coraline' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'coraline' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 7, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'coraline' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running coraline_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'coraline_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * @since Coraline 1.0
 */
function coraline_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'coraline_remove_recent_comments_style' );

if ( ! function_exists( 'coraline_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time.
 *
 * @since Coraline 1.0
 */
function coraline_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s ', 'coraline' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'coraline_posted_by' ) ) :
/**
 * Prints HTML with meta information for the current author on multi-author blogs
 */
function coraline_posted_by() {
	if ( is_multi_author() && ! is_author() ) {
		printf( __( '<span class="by-author"><span class="sep">by</span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span> </span>', 'coraline' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'coraline' ), get_the_author_meta( 'display_name' ) ) ),
			esc_attr( get_the_author_meta( 'display_name' ) )
		);
	}
}
endif;

if ( ! function_exists( 'coraline_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Coraline 1.0
 */
function coraline_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'coraline' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'coraline' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'coraline' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

/**
 *  Returns the Coraline options with defaults as fallback
 *
 * @since Coraline 1.0.2
 */
function coraline_get_theme_options() {
	$defaults = array(
		'color_scheme' => 'light',
		'theme_layout' => 'content-sidebar',
	);
	$options = get_option( 'coraline_theme_options', $defaults );

	return $options;
}

/**
 * Register our color schemes and add them to the queue
 */
function coraline_color_registrar() {
	$options = coraline_get_theme_options();
	$color_scheme = $options['color_scheme'];

	if ( ! empty( $color_scheme ) && $color_scheme != 'light' ) {
		wp_register_style( $color_scheme, get_template_directory_uri() . '/colors/' . $color_scheme . '.css', null, null );
		wp_enqueue_style( $color_scheme );
	}
}
add_action( 'wp_enqueue_scripts', 'coraline_color_registrar' );

/**
 *  Returns the current Coraline layout as selected in the theme options
 *
 * @since Coraline 1.0
 */
function coraline_current_layout() {
	$options = coraline_get_theme_options();
	$current_layout = $options['theme_layout'];

	$two_columns = array( 'content-sidebar', 'sidebar-content' );
	$three_columns = array( 'content-sidebar-sidebar', 'sidebar-content-sidebar', 'sidebar-sidebar-content' );

	if ( in_array( $current_layout, $two_columns ) )
		return 'two-column ' . $current_layout;
	elseif ( in_array( $current_layout, $three_columns ) )
		return 'three-column ' . $current_layout;
	else
		return 'no-sidebars';
}

/**
 *  Adds coraline_current_layout() to the array of body classes
 *
 * @since Coraline 1.0
 */
function coraline_body_class($classes) {
	$classes[] = coraline_current_layout();

	return $classes;
}
add_filter( 'body_class', 'coraline_body_class' );

/**
 * WP.com: Check the current color scheme and set the correct themecolors array
 */
$options = coraline_get_theme_options();

$color_scheme = 'light';
if ( isset( $options['color_scheme'] ) )
	$color_scheme = $options['color_scheme'];

if ( 'light' == $color_scheme ) {
	$themecolors = array(
		'bg' => 'ffffff',
		'border' => 'cccccc',
		'text' => '333333',
		'link' => '0060ff',
		'url' => 'df0000',
	);
}
if ( 'dark' == $color_scheme ) {
	$themecolors = array(
		'bg' => '151515',
		'border' => '333333',
		'text' => 'bbbbbb',
		'link' => '80b0ff',
		'url' => 'e74040',
	);
}
if ( 'pink' == $color_scheme ) {
	$themecolors = array(
		'bg' => 'faccd6',
		'border' => 'c59aa4',
		'text' => '222222',
		'link' => 'd6284d',
		'url' => 'd6284d',
	);
}
if ( 'purple' == $color_scheme ) {
	$themecolors = array(
		'bg' => 'e1ccfa',
		'border' => 'c5b2de',
		'text' => '333333',
		'link' => '7728d6',
		'url' => '7728d6',
	);
}
if ( 'brown' == $color_scheme ) {
	$themecolors = array(
		'bg' => '9a7259',
		'border' => 'b38970',
		'text' => 'ffecd0',
		'link' => 'ffd2b7',
		'url' => 'ffd2b7',
	);
}
if ( 'red' == $color_scheme ) {
	$themecolors = array(
		'bg' => 'a20013',
		'border' => 'b92523',
		'text' => 'e68d77',
		'link' => 'ffd2b7',
		'url' => 'ffd2b7',
	);
}
if ( 'blue' == $color_scheme ) {
	$themecolors = array(
		'bg' => 'ccddfa',
		'border' => 'b2c3de',
		'text' => '333333',
		'link' => '2869d6',
		'url' => '2869d6',
	);
}

/**
 * Adjust the content_width value based on layout option and current template.
 *
 * @since Coraline 1.0.2
 * @param int content_width value
 */
function coraline_set_full_content_width() {
	global $content_width;
	$content_width = 770;

	// Override for 3-column layouts
	$layout = coraline_current_layout();
	if ( strstr( $layout, 'three-column' ) )
		$content_width = 990;
}