<?php
/**
 * @package Coraline
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
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Coraline 1.0
 */
function coraline_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Post Format support. Legacy category chooser will display in Theme Options for sites that set a category before post formats were added.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'gallery' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	$attachment_size = apply_filters( 'theme_attachment_size',  800 );
	add_image_size( 'coraline-image-template', $attachment_size, $attachment_size, false );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'coraline', get_template_directory() . '/languages' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'coraline' ),
	) );

	/**
	 * Add support for Eventbrite.
	 * See: https://wordpress.org/plugins/eventbrite-api/
	 */
	add_theme_support( 'eventbrite' );
}
endif;

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * Hooks into the after_setup_theme action.
 */
function coraline_register_custom_background() {
	add_theme_support( 'custom-background', apply_filters( 'coraline_custom_background_args', array(
		'default-color' => '',
		'default-image' => '',
	) ) );
}
add_action( 'after_setup_theme', 'coraline_register_custom_background' );

/**
 * Enqueue scripts and styles
 */
function coraline_scripts() {
	wp_enqueue_style( 'coraline', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'coraline_scripts' );

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
		<p><?php _e( 'Pingback:', 'coraline' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'coraline' ), ' ' ); ?></p>
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
		'name'          => __( 'Primary Widget Area', 'coraline' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'The primary widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Secondary Widget Area', 'coraline' ),
		'id'            => 'secondary-widget-area',
		'description'   => __( 'The secondary widget area appears in 3-column layouts', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 3, located above the primary and secondary sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Feature Widget Area', 'coraline' ),
		'id'            => 'feature-widget-area',
		'description'   => __( 'The feature widget above the sidebars in Content-Sidebar-Sidebar and Sidebar-Sidebar-Content layouts', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'First Footer Widget Area', 'coraline' ),
		'id'            => 'first-footer-widget-area',
		'description'   => __( 'The first footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Second Footer Widget Area', 'coraline' ),
		'id'            => 'second-footer-widget-area',
		'description'   => __( 'The second footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Third Footer Widget Area', 'coraline' ),
		'id'            => 'third-footer-widget-area',
		'description'   => __( 'The third footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Area 7, located in the footer. Empty by default.
	register_sidebar( array(
		'name'          => __( 'Fourth Footer Widget Area', 'coraline' ),
		'id'            => 'fourth-footer-widget-area',
		'description'   => __( 'The fourth footer widget area', 'coraline' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
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
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_html( get_the_date() )
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
	$options      = coraline_get_theme_options();
	$color_scheme = $options['color_scheme'];

	if ( ! empty( $color_scheme ) && 'light' != $color_scheme ) {
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
	$options        = coraline_get_theme_options();
	$current_layout = $options['theme_layout'];
	$two_columns    = array( 'content-sidebar', 'sidebar-content' );
	$three_columns  = array( 'content-sidebar-sidebar', 'sidebar-content-sidebar', 'sidebar-sidebar-content' );

	if ( in_array( $current_layout, $two_columns ) )
		return 'two-column ' . $current_layout;
	elseif ( in_array( $current_layout, $three_columns ) )
		return 'three-column ' . $current_layout;
	else
		return 'no-sidebars';
}

/**
 *  Adds coraline_current_layout() and $color_scheme to the array of body classes
 *
 * @since Coraline 1.0
 */
function coraline_body_class( $classes ) {
	$classes[] = coraline_current_layout();
	$options   = coraline_get_theme_options();

	if ( ! empty( $options['color_scheme'] ) && 'light' != $options['color_scheme'] )
		$classes[] = 'color-' . $options['color_scheme'];

	return $classes;
}
add_filter( 'body_class', 'coraline_body_class' );

/**
 * Adjust the content_width value based on layout option and current template.
 *
 * @since Coraline 1.0.2
 */
function coraline_set_full_content_width() {
	global $content_width;

	if ( is_attachment() || is_page_template( 'full-width-page.php' ) ) {
		$content_width = 770;

		// Override for 3-column layouts
		$layout = coraline_current_layout();
		if ( strstr( $layout, 'three-column' ) )
			$content_width = 990;
	}
}
add_action( 'template_redirect', 'coraline_set_full_content_width' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Coraline 1.0.2
 */
function coraline_wp_title( $title, $sep ) {
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
		$title .= " $sep " . sprintf( __( 'Page %s', 'coraline' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'coraline_wp_title', 10, 2 );

/**
 * Change Eventbrite meta separators to pipes.
 */
function coraline_event_meta_separators() {
	return ' | ';
}
add_filter( 'eventbrite_meta_separator', 'coraline_event_meta_separators' );

/**
 * Adds support for a custom header image.
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * This theme has some pretty cool theme options.
 */
require_once ( get_template_directory() . '/inc/theme-options.php' );

/**
 * Load Jetpack compatibility file.
 */
if ( file_exists( get_template_directory() . '/inc/jetpack.php' ) )
	require get_template_directory() . '/inc/jetpack.php';
