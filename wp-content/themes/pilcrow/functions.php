<?php
/**
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 500;


if ( ! function_exists( 'pilcrow_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override pilcrow_setup() in a child theme, add your own pilcrow_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails, custom background, custom header, and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Pilcrow 1.0
 */
function pilcrow_setup() {

	add_editor_style( 'style-editor.css' );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add support for Post Formats
	// http://codex.wordpress.org/Post_Formats
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'pilcrow', get_template_directory() . '/languages' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'pilcrow' ),
	) );

	// This theme allows users to set a custom background
	add_theme_support( 'custom-background' );
}
endif;
add_action( 'after_setup_theme', 'pilcrow_setup' );

if ( ! function_exists( 'pilcrow_background_markup' ) ) :
/**
 * Adds a containing div around everything if the custom background feature is in use
 *
* @since Pilcrow 1.0
 */
function pilcrow_background_markup() {
	// check if we're using a custom background image or color
	if ( '' != get_background_color() || '' != get_background_image() ) {
		add_action( 'pilcrow_before', 'pilcrow_wrap_before' );
		add_action( 'pilcrow_after',  'pilcrow_wrap_after'  );
	}
}
endif;
add_action( 'init', 'pilcrow_background_markup' );

// If we are, let's hook into the pilcrow_before action
function pilcrow_wrap_before() {
	echo '<div id="wrapper">';
}

// And, let's hook into the pilcrow_after action
function pilcrow_wrap_after() {
	echo '</div><!-- #wrapper -->';
}

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link and only show 1 level of menu items (to match a previous theme)
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Pilcrow 1.0
 */
function pilcrow_page_menu_args( $args ) {
	$args['show_home'] = true;
	$args['depth']     = 1;
	return $args;
}
add_filter( 'wp_page_menu_args', 'pilcrow_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Pilcrow 1.0
 * @return int
 */
function pilcrow_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'pilcrow_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Pilcrow 1.0
 * @return string "Continue Reading" link
 */
function pilcrow_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pilcrow' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and pilcrow_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Pilcrow 1.0
 * @return string An ellipsis
 */
function pilcrow_auto_excerpt_more( $more ) {
	return ' &hellip;' . pilcrow_continue_reading_link();
}
add_filter( 'excerpt_more', 'pilcrow_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Pilcrow 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function pilcrow_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= pilcrow_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'pilcrow_custom_excerpt_more' );

if ( ! function_exists( 'pilcrow_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own pilcrow_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Pilcrow 1.0
 */
function pilcrow_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'pilcrow' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'pilcrow' ), ' ' ); ?></p>

	<?php else : ?>

		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-container">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 48 ); ?>
					<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'pilcrow' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'pilcrow' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'pilcrow' ), ' ' );
					?>
				</div><!-- .comment-meta .commentmetadata -->

				<div class="comment-body"><?php comment_text(); ?></div>

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->
			</div><!-- #comment-##  -->

	<?php
	endif;
}
endif;

/**
 * Register widgetized areas, including two sidebars and two widget-ready columns in the footer.
 *
 * To override pilcrow_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Pilcrow 1.0
 * @uses register_sidebar
 */
function pilcrow_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'pilcrow' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'The main sidebar', 'pilcrow' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Secondary Sidebar', 'pilcrow' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'The secondary sidebar in three-column layouts', 'pilcrow' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 3, located above the primary and secondary sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Feature Area', 'pilcrow' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'The feature widget area above the sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts', 'pilcrow' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'First Footer Area', 'pilcrow' ),
		'id'            => 'sidebar-4',
		'description'   => __( 'The first footer widget area', 'pilcrow' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Second Footer Area', 'pilcrow' ),
		'id'            => 'sidebar-5',
		'description'   => __( 'The second footer widget area', 'pilcrow' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
/** Register sidebars by running pilcrow_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'pilcrow_widgets_init' );

/**
 *  Returns the current pilcrow color scheme as selected in the theme options
 *
 * @since pilcrow 1.0
 */
function pilcrow_current_color_scheme() {
	$options = pilcrow_get_theme_options();
	return $options['color_scheme'];
}

/**
 *  Returns the current pilcrow theme options, with default values as fallback
 *
 * @since pilcrow 1.0
 */
function pilcrow_get_theme_options() {
	$defaults = array(
		'color_scheme' => 'light',
		'theme_layout' => 'content-sidebar',
	);
	$options = get_option( 'pilcrow_theme_options', $defaults );

	return $options;
}

/**
 * Register our color schemes and add them to the queue
 */
function pilcrow_color_registrar() {
	_deprecated_function( __FUNCTION__, '1.5', 'pilcrow_scripts()' );

	$color_scheme = pilcrow_current_color_scheme();

	switch ( $color_scheme ) {
		case 'dark':
			wp_enqueue_style( 'dark',  get_template_directory_uri() . '/colors/dark.css',  null, null );
			break;
		case 'brown':
			wp_enqueue_style( 'brown', get_template_directory_uri() . '/colors/brown.css', null, null );
			break;
		case 'red':
			wp_enqueue_style( 'red',   get_template_directory_uri() . '/colors/red.css',   null, null );
			break;
	}
}

/**
 * Enqueue scripts and styles
 */
function pilcrow_scripts() {
	$color_scheme = pilcrow_current_color_scheme();

	wp_enqueue_style( 'pilcrow', get_stylesheet_uri() );

	if ( 'light' != $color_scheme )
		wp_enqueue_style( $color_scheme, get_template_directory_uri() . "/colors/$color_scheme.css", array( 'pilcrow' ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'pilcrow_scripts' );

/**
 *  Returns the current pilcrow layout as selected in the theme options
 *
 * @since pilcrow 1.0
 */
function pilcrow_current_layout() {
	$options        = pilcrow_get_theme_options();
	$current_layout = $options['theme_layout'];

	$two_columns    = array( 'content-sidebar', 'sidebar-content' );
	$three_columns  = array( 'content-sidebar-sidebar', 'sidebar-sidebar-content', 'sidebar-content-sidebar' );

	if ( in_array( $current_layout, $two_columns ) )
		return 'two-column ' . $current_layout;
	elseif ( in_array( $current_layout, $three_columns ) )
		return 'three-column ' . $current_layout;
	else
		return $current_layout;
}

/**
 *  Adds pilcrow_current_layout() and the current color scheme to the array of body classes
 *
 * @since pilcrow 1.0
 */
function pilcrow_body_class( $classes ) {
	$color_scheme = pilcrow_current_color_scheme();
	$classes[]    = pilcrow_current_layout();

	switch ( $color_scheme ) {
		case 'dark':
			$classes[] = 'color-dark';
			break;
		case 'brown':
			$classes[] = 'color-brown';
			break;
		case 'red':
			$classes[] = 'color-red';
			break;
		default:
			$classes[] = 'color-light';
			break;
	}

	return $classes;
}
add_filter( 'body_class', 'pilcrow_body_class' );

if ( ! function_exists( 'pilcrow_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function pilcrow_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'pilcrow_attachment_size', 900 );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) )
	);
}
endif;

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Pilcrow 1.4
 */
function pilcrow_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'pilcrow' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'pilcrow_wp_title', 10, 2 );

/**
 * Adjusts content_width value for image attachments and the full width
 * page template.
 */
function pilcrow_content_width() {
	if ( ( is_attachment() && wp_attachment_is_image() ) || is_page_template( 'onecolumn-page.php' ) || 'no-sidebar' == pilcrow_current_layout() )
		$GLOBALS['content_width'] = 990;
}
add_action( 'template_redirect', 'pilcrow_content_width' );

/**
 * This theme has some pretty cool theme options.
 */
require get_template_directory() . '/inc/theme-options.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.compat.php';

/**
 * Load WP.com compatibility file.
 */
if ( file_exists( get_template_directory() . '/inc/wpcom.php' ) )
	require get_template_directory() . '/inc/wpcom.php';
