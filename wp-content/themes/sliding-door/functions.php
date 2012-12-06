<?php
/**
 * slidingdoor functions and definitions
 *
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



/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'slidingdoor_register', 'my_theme_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function my_theme_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin pre-packaged with a theme
		array(
			'name'     				=> 'Page Links To', // The plugin name
			'slug'     				=> 'page-links-to', // The plugin slug (typically the folder name)
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
		),

	);

	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'slidingdoor';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
			'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
			'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	slidingdoor( $plugins, $config );

}








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
	load_theme_textdomain( 'slidingdoor', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'slidingdoor' ),
		'custom-sliding-menu' => __( 'Sliding Navigation', 'slidingdoor' ),
	) );


	// We want them to be 320 pixels wide by 200 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	//set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
	
	
		}
endif;


// Get only the image url link by http://blogcastor.com
function get_the_post_thumbnail_url( $post_id = NULL ) {
    global $id;
    $post_id = ( NULL === $post_id ) ? $id : $post_id;
    $src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
    $src = $src[0];
    return $src;
}

class My_Walker extends Walker_Nav_Menu
{
var $item_count = 0;

function end_el(&$output, $item, $depth) {
		$output .= "";
	}
	
	/**
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
    function start_el(&$output, $item, $depth, $args) {
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
			$thumbnail = 'http://mac-host.com/slidingdoor/slidingdoor.jpg';
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


        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
        if($item_count == 6){
			if(isset($item->object_id)) {
			$thumbnailid = (int)$item->object_id;
			$thumbnail = get_the_post_thumbnail_url( $thumbnailid );
			} else {
			$thumbnail = 'http://mac-host.com/slidingdoor/slidingdoor.jpg';
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


        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

    $item_count ++;
	}   
}

function no_sliding_menu(){
 $blog_url = site_url() ;
 $theme_url=get_bloginfo('template_url');
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
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'slidingdoor' ) . '</a>';
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
			<?php printf( __( '%s <span class="says">says:</span>', 'slidingdoor' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'slidingdoor' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'slidingdoor' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'slidingdoor' ), ' ' );
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
		<p><?php _e( 'Pingback:', 'slidingdoor' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'slidingdoor'), ' ' ); ?></p>
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
		'name' => __( 'Primary Widget Area', 'slidingdoor' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'slidingdoor' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'slidingdoor' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'slidingdoor' ),
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
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'slidingdoor' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'slidingdoor' ), get_the_author() ),
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
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slidingdoor' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slidingdoor' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slidingdoor' );
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
