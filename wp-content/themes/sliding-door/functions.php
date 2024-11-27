<?php
/**
 * slidingdoor functions and definitions
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, slidingdoor_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' ); 
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'slidingdoor_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Sliding_Door
 * @since Sliding Door 1.0
 */


/**
 * Color custom theme options
 */
require_once ( get_template_directory() . '/theme-options.php' );



	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	
	
		// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'sliding-door';



/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run slidingdoor_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'slidingdoor_setup' );

if ( ! function_exists( 'slidingdoor_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override slidingdoor_setup() in a child theme, add your own slidingdoor_setup to your child theme's
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
 * @since Sliding Door 1.0
 */
function slidingdoor_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	
    add_theme_support( 'custom-header' );
	
	add_theme_support( 'custom-background');

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 320, 200, true );
    
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'sliding-door', get_template_directory() . '/languages' );



	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'sliding-door' ),
		'custom-sliding-menu' => __( 'Sliding Navigation', 'sliding-door' ),
	) );


	// We want them to be 320 pixels wide by 200 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	//set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
	
	
		}
endif;




class My_Walker extends Walker_Nav_Menu
{
var $item_count = 0;

function end_el(&$output, $item, $depth=0, $args=array(), $id=0) {
		$output .= "";
	}
	
	/**
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
    function start_el(&$output, $item, $depth=0, $args=array(), $id=0) {
        global $wp_query,$item_count;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if($item_count <= 5){
			if(isset($item->object_id)) {
			$thumbnailid = (int)$item->object_id;
			$thumbnail = get_the_post_thumbnail_url( $thumbnailid );
			} else {
		$thumbnail =get_template_directory_uri()."/images/slidingdoor.jpg";
			}
		
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="bk'.(int)$item_count.'" ';

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
        $item_output = '<a' . $attributes .' style="background: url(\'' . $thumbnail. '\') repeat scroll 0%;">';
        $item_output .=  apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= '</a>';
 		$item_output .= "</li>\n";


        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth = 0 , $args=array(), $id = 0 );
        }
        if($item_count == 6){
			if(isset($item->object_id)) {
			$thumbnailid = (int)$item->object_id;
			$thumbnail = get_the_post_thumbnail_url( $thumbnailid );
			} else {
			$thumbnail =get_template_directory_uri()."/images/slidingdoor.jpg";
			}
		
         $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="bk'.(int)$item_count.'" ';

 $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
 
		$attributes = ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
        $item_output = '<a' . $attributes .' style="background: url(\'' . $thumbnail. '\') repeat scroll 0%;">';
        $item_output .=  apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= '</a>';
 		$item_output .= "</li>\n";


        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth = 0 , $args=array(), $id = 0 );
        }

    $item_count ++;
	}   
}

function no_sliding_menu(){
 $blog_url = site_url() ;
 $theme_url=get_template_directory_uri();
echo "<ul>
			<li class=\"bk0\"><a href=\"http://mac-host.com/support\" style=\"background: url('".$theme_url."/imagemenu/images/1.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk1\"><a href=\"".$blog_url."\" style=\"background: url('".$theme_url."/imagemenu/images/2.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk2\"><a href=\"".$blog_url."\" style=\"background: url('".$theme_url."/imagemenu/images/3.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk3\"><a href=\"http://mac-host.com/slidingdoor/slider.zip\" style=\"background: url('".$theme_url."/imagemenu/images/4.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk4\"><a href=\"http://mac-host.com/support\" style=\"background: url('".$theme_url."/imagemenu/images/5.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk5\"><a href=\"http://mac-host.com/support\" style=\"background: url('".$theme_url."/imagemenu/images/6.jpg') repeat scroll 0%;\">slidingdoor</a></li>
			<li class=\"bk6\"><a href=\"http://mac-host.com/support\" style=\"background: url('".$theme_url."/imagemenu/images/7.jpg') repeat scroll 0%;\">slidingdoor</a></li>
				</ul>";
}



/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Sliding Door 1.0
 */
function slidingdoor_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'slidingdoor_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Sliding Door 1.0
 * @return int
 */
function slidingdoor_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'slidingdoor_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Sliding Door 1.0
 * @return string "Continue Reading" link
 */
function slidingdoor_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'sliding-door' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and slidingdoor_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Sliding Door 1.0
 * @return string An ellipsis
 */
function slidingdoor_auto_excerpt_more( $more ) {
	return ' &hellip;' . slidingdoor_continue_reading_link();
}
add_filter( 'excerpt_more', 'slidingdoor_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Sliding Door 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function slidingdoor_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= slidingdoor_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'slidingdoor_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Sliding Door's style.css.
 *
 * @since Sliding Door 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function slidingdoor_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'slidingdoor_remove_gallery_css' );

if ( ! function_exists( 'slidingdoor_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own slidingdoor_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Sliding Door 1.0
 */
function slidingdoor_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'sliding-door' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'sliding-door' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'sliding-door' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'sliding-door' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'sliding-door' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'sliding-door'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override slidingdoor_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Sliding Door 1.0
 * @uses register_sidebar
 */
function slidingdoor_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'sliding-door' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'sliding-door' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'sliding-door' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'sliding-door' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


}
/** Register sidebars by running slidingdoor_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'slidingdoor_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Sliding Door 1.0
 */
function slidingdoor_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'slidingdoor_remove_recent_comments_style' );



/** Prints HTML with meta information for the current post‚date/time and author.
*/
if ( ! function_exists( 'slidingdoor_posted_on' ) ) :
function slidingdoor_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'sliding-door' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'sliding-door' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'slidingdoor_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Sliding Door 1.0
 */
function slidingdoor_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sliding-door' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sliding-door' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'sliding-door' );
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
