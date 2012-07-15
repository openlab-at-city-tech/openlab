<?php
/**
 * Weaver functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, weaver_setup(), sets up the theme by registering support
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
 *     remove_filter( 'excerpt_length', 'weaver_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run weaver_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'weaver_setup' );

if ( ! function_exists( 'weaver_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override weaver_setup() in a child theme, add your own weaver_setup to your child theme's
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
 * @uses add_theme_support( 'custom-header', $weaverii_header ) for WP 3.4+ custom header
 */

function weaver_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Post Format support.
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'quote', 'status' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( WEAVER_TRANS, TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// Weaver supports two nav menus
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', WEAVER_TRANSADMIN ),
		'secondary' => __( 'Secondary Navigation', WEAVER_TRANSADMIN ),
	) );

	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 640;
	}

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.

	register_default_headers( array(
	'wheat' => array (
		'url' => "%s/images/headers/wheat.jpg",
		'thumbnail_url' => "%s/images/headers/wheat-thumbnail.jpg",
		'description' => __( 'Wheat 940x198 Header', WEAVER_TRANSADMIN )
	    ),
	'buds' => array(
		'url' => '%s/images/headers/buds.jpg',
		'thumbnail_url' => '%s/images/headers/buds-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Buds', WEAVER_TRANSADMIN )
	    ),
	'grand-teton' => array(
		'url' => '%s/images/headers/grand-teton.jpg',
		'thumbnail_url' => '%s/images/headers/grand-teton-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Grand Tetons', WEAVER_TRANSADMIN )
	    ),
	'moon' => array(
		'url' => '%s/images/headers/moon.jpg',
		'thumbnail_url' => '%s/images/headers/moon-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Moon', WEAVER_TRANSADMIN )
	    ),
	'moss' => array(
		'url' => '%s/images/headers/moss.jpg',
		'thumbnail_url' => '%s/images/headers/moss-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Moss', WEAVER_TRANSADMIN )
	    ),
	'mum' => array (
		'url' => "%s/images/headers/mum.jpg",
		'thumbnail_url' => "%s/images/headers/mum-thumbnail.jpg",
		'description' => __( 'Mum 940x198 Header', WEAVER_TRANSADMIN )
	    ),
	'ocean-birds' => array(
		'url' => '%s/images/headers/ocean-birds.jpg',
		'thumbnail_url' => '%s/images/headers/ocean-birds-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Ocean Birds', WEAVER_TRANSADMIN )
	    ),
	'painted-desert' => array(
		'url' => '%s/images/headers/painted-desert.jpg',
		'thumbnail_url' => '%s/images/headers/painted-desert-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Painted Desert', WEAVER_TRANSADMIN )
	    ),
	'path' => array(
		'url' => '%s/images/headers/path.jpg',
		'thumbnail_url' => '%s/images/headers/path-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Path', WEAVER_TRANSADMIN )
	    ),
	'sopris' => array (
		'url' => "%s/images/headers/sopris.png",
		'thumbnail_url' => "%s/images/headers/sopris-thumbnail.png",
		'description' => __( 'Sopris 940x198 Header', WEAVER_TRANSADMIN )
	    ),
	'sunset' => array(
		'url' => '%s/images/headers/sunset.jpg',
		'thumbnail_url' => '%s/images/headers/sunset-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Sunset', WEAVER_TRANSADMIN )
	    ),
	'wpweaver' => array (
		'url' => "%s/images/headers/wpweaver.jpg",
		'thumbnail_url' => "%s/images/headers/wpweaver-thumbnail.jpg",
		'description' => __( 'WPWeaver 940x140 Header', WEAVER_TRANSADMIN )
	    ),
	'yosemite' => array(
		'url' => '%s/images/headers/yosemite.jpg',
		'thumbnail_url' => '%s/images/headers/yosemite-thumbnail.jpg',
		/* translators: header image description */
		'description' => __( 'Yosemite', WEAVER_TRANSADMIN )
	    ),
	'indieave' => array (
		'url' => "%s/images/headers/indieave.png",
		'thumbnail_url' => "%s/images/headers/indieave-thumbnail.png",
		'description' => __( 'Indie Ave 940x180 Blank Header BG', WEAVER_TRANSADMIN )
	    ),
	'ivorydrive' => array (
		'url' => "%s/images/headers/ivorydrive.png",
		'thumbnail_url' => "%s/images/headers/ivorydrive-thumbnail.png",
		'description' => __( 'Ivory Drive 940x198 Blank Header BG', WEAVER_TRANSADMIN )
	    ),

	'transparent' => array(
		'url' => '%s/images/headers/transparent.png',
		'thumbnail_url' => '%s/images/headers/transparent-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Transparent header image', WEAVER_TRANSADMIN )
	    ),
	'black' => array(
		'url' => '%s/images/headers/black.png',
		'thumbnail_url' => '%s/images/headers/black-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Black', WEAVER_TRANSADMIN )
	    ),
	'gray' => array(
		'url' => '%s/images/headers/gray.png',
		'thumbnail_url' => '%s/images/headers/gray-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Gray', WEAVER_TRANSADMIN )
	    ),
	'silver' => array(
		'url' => '%s/images/headers/silver.png',
		'thumbnail_url' => '%s/images/headers/silver-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Silver', WEAVER_TRANSADMIN )
	    ),
	'white' => array(
		'url' => '%s/images/headers/white.png',
		'thumbnail_url' => '%s/images/headers/white-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'White', WEAVER_TRANSADMIN )
	    ),
	'maroon' => array(
		'url' => '%s/images/headers/maroon.png',
		'thumbnail_url' => '%s/images/headers/maroon-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Maroon', WEAVER_TRANSADMIN )
	    ),
	'red' => array(
		'url' => '%s/images/headers/red.png',
		'thumbnail_url' => '%s/images/headers/red-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Red', WEAVER_TRANSADMIN )
	    ),
	'olive' => array(
		'url' => '%s/images/headers/olive.png',
		'thumbnail_url' => '%s/images/headers/olive-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Olive', WEAVER_TRANSADMIN )
	    ),
	'yellow' => array(
		'url' => '%s/images/headers/yellow.png',
		'thumbnail_url' => '%s/images/headers/yellow-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Yellow', WEAVER_TRANSADMIN )
	    ),
	'green' => array(
		'url' => '%s/images/headers/green.png',
		'thumbnail_url' => '%s/images/headers/green-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Green', WEAVER_TRANSADMIN )
	    ),
	'lime' => array(
		'url' => '%s/images/headers/lime.png',
		'thumbnail_url' => '%s/images/headers/lime-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Lime', WEAVER_TRANSADMIN )
	    ),
	'teal' => array(
		'url' => '%s/images/headers/teal.png',
		'thumbnail_url' => '%s/images/headers/teal-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Teal', WEAVER_TRANSADMIN )
	    ),
	'aqua' => array(
		'url' => '%s/images/headers/aqua.png',
		'thumbnail_url' => '%s/images/headers/aqua-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Aqua', WEAVER_TRANSADMIN )
	    ),
	'navy' => array(
		'url' => '%s/images/headers/navy.png',
		'thumbnail_url' => '%s/images/headers/navy-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Navy', WEAVER_TRANSADMIN )
	    ),
	'blue' => array(
		'url' => '%s/images/headers/blue.png',
		'thumbnail_url' => '%s/images/headers/blue-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Blue', WEAVER_TRANSADMIN )
	    ),
	'purple' => array(
		'url' => '%s/images/headers/purple.png',
		'thumbnail_url' => '%s/images/headers/purple-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Purple', WEAVER_TRANSADMIN )
	    ),
	'fuchsia' => array(
		'url' => '%s/images/headers/fuchsia.png',
		'thumbnail_url' => '%s/images/headers/fuchsia-thumbnail.png',
		/* translators: header image description */
		'description' => __( 'fuchsia', WEAVER_TRANSADMIN )
	    )

	) );

	// now, need Weaver settings available for everything else

	$width = 0 ; $height = 0;

	if (weaver_load_cache()) { // settings will be there unless initial activation or theme preview
	    $width = weaver_getopt('ttw_header_image_width');
	    $height = weaver_getopt('ttw_header_image_height');
	}
	if ($width == 0 && $height == 0) {
	    $width = 940; $height = 198;
	}

	global $weaverii_header;
	$weaverii_header = array(
	    'default-image' => '%s/images/headers/wheat.jpg',
	    'random-default' => true,
	    'width' => $width,
	    'height' => $height,
	    'flex-height' => true,
	    'flex-width' => true,
	    'default-text-color' => '',
	    'header-text' => false,
	    'uploads' => true,
	    'wp-head-callback' => '',
	    'admin-head-callback' => 'weaver_admin_header_style',
	    'admin-preview-callback' => '',
	);

	if (function_exists('get_custom_header')) {
	    add_theme_support( 'custom-header', $weaverii_header );
	    add_theme_support( 'custom-background' );
	} else {
	    // WordPress 3.3 backward compatibility here
	    // Add support for custom backgrounds
	    add_custom_background();

	    // The default header text color
	    define('NO_HEADER_TEXT', !$weaverii_header['header-text']);	// don't include text info in the Headers admin
	    define( 'HEADER_TEXTCOLOR', $weaverii_header['default-text-color'] );

	    // By leaving empty, we allow for random image rotation.
	    // No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	    define( 'HEADER_IMAGE', $weaverii_header['default-image'] );
	    define( 'HEADER_IMAGE_WIDTH', $weaverii_header['width'] );
	    define( 'HEADER_IMAGE_HEIGHT', $weaverii_header['height'] );
	    // Turn on random header image rotation by default.
	    add_theme_support( 'custom-header');

	    // Add a way for the custom header to be styled in the admin panel that controls
	    // custom headers. See weaverii_admin_header_style(), below.
	    add_custom_image_header( 'weaver_admin_header_style', // WP 3.3 compatibility
		'weaver_admin_header_style' );

	}

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( $weaverii_header['width'], $weaverii_header['height'], true );

	// ... and thus ends the changeable header business.
}
endif;

function weaver_admin_init_cb() {
    /* this will initialize the SAPI stuff */

    weaver_sapi_options_init(); // This must come first as it hooks update_option used elsewhere

    // Now, init the Weaver database

    weaver_init_opts('weaver_admin_init_cb');		/* load opts */

    do_action('wvrx_extended_setup');	// load weaver extension options
    do_action('wvrx_plus_setup');	// set up Plus options
    do_action('wvrx_plus_admin_init');

    // weaver_sapi_options_init();
    return;
}


if ( ! function_exists( 'weaver_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in weaver_setup().
 *
 */
function weaver_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 */

if ( ! function_exists( 'weaver_page_menu_args' ) ) :	/* let child do this if it wants */
function weaver_page_menu_args( $args ) {
    if (weaver_getopt('ttw_menu_nohome'))
	$args['show_home'] = false;
    else
	$args['show_home'] = true;

    $hide_pages = get_pages(array('hierarchical' => 0, 'meta_key' => 'ttw-hide-on-menu'));	// get list of excluded pages
    if (!empty($hide_pages)) {
	$ex_list = '';
	foreach ($hide_pages as $page) {
	    $ex_list .= $page->ID . ',';	/* trailing , doesn't matter */
	}
	$args['exclude'] = $ex_list;
    }

    return $args;
}
add_filter( 'wp_page_menu_args', 'weaver_page_menu_args' );
endif;

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function weaver_excerpt_length( $length ) {
    $val = weaver_getopt('ttw_excerpt_length');
    if ($val > 0)
	return $val;
    return 40;
}
add_filter( 'excerpt_length', 'weaver_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
if (!function_exists('weaver_continue_reading_link')) :
function weaver_continue_reading_link($add_a = true) {
    $rep = weaver_getopt('ttw_excerpt_more_msg');
    if (!empty($rep))
	$msg = $rep;
    else
	$msg = __( 'Continue reading <span class="meta-nav">&rarr;</span>', WEAVER_TRANS );

    if ($add_a)
	return ' <a class="more-link" href="'. get_permalink() . '">' . $msg . '</a>';
    else
	return $msg;
}
endif;

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and weaver_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function weaver_auto_excerpt_more( $more ) {
    return ' &hellip;' . weaver_continue_reading_link();
}
add_filter( 'excerpt_more', 'weaver_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function weaver_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= weaver_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'weaver_custom_excerpt_more' );

/* route tinyMCE to our stylesheet */
function weaver_mce_css($default_style) {
    /* replace the default editor-style.css with a custom file generated for each multi-site site (or single site) */

    if (weaver_getopt('ttw_hide_editor_style'))
	return $default_style;

    $mce_css_dir = weaver_f_uploads_base_dir() . 'weaver-subthemes/weaver-editor-style.css';
    $mce_css_url = weaver_f_uploads_base_url() . 'weaver-subthemes/weaver-editor-style.css';

    if (!weaver_f_exists($mce_css_dir)) {	// see if it is there
	return $default_style;
    }

    /* do we need to do anything about rtl? */

    /* if we have a custom style file, return that instead of the default */

    return $mce_css_url;
}
add_filter('mce_css','weaver_mce_css');

if ( ! function_exists( 'weaver_comment_list' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own weaver_comment_list(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function weaver_comment_list( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', WEAVER_TRANS ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', WEAVER_TRANS ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', WEAVER_TRANS ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', WEAVER_TRANS ), ' ' );
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
		<p><?php _e( 'Pingback:', WEAVER_TRANS ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', WEAVER_TRANS), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

if (!function_exists('weaver_register_sidebar')) :
/**
 * Register widgetized areas: two default sidebars, two content areq sidebars,
 * a top area for specialized post pages, alternative sidebar for template pages,
 * and a header widget area.
 *
 * To override weaver_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @uses register_sidebar
 */
function weaver_register_sidebar($name, $id, $desc){
    register_sidebar( array(
	'name' => $name,
	'id' => $id,
	'description' => $desc,
	'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
	'after_widget' => '</li>',
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
	) );
}
endif;

if (! function_exists('weaver_widgets_init')) :
function weaver_widgets_init() {
	// Area 1, located at the top of the sidebar.
	weaver_register_sidebar(__( 'Primary Widget Area', WEAVER_TRANSADMIN ),
	    'primary-widget-area', __( 'The primary widget area', WEAVER_TRANSADMIN ));

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	weaver_register_sidebar(__( 'Secondary Widget Area', WEAVER_TRANSADMIN ),
	    'secondary-widget-area', __( 'The secondary widget area', WEAVER_TRANSADMIN ));

	## alternative widget area
	weaver_register_sidebar(__( 'Weaver Alternative Widget Area', WEAVER_TRANSADMIN ),
	    'alternative-widget-area', __( 'An alternative widget area used only by the Alternative Left and Right page templates.', WEAVER_TRANSADMIN ));

	## top widget area
	weaver_register_sidebar(__( 'Top Widget Area', WEAVER_TRANSADMIN ),
	    'top-widget-area',
	     __( 'The top widget area appears above the content area, including the main blog page. It is not displayed on special post pages (archives, etc.).', WEAVER_TRANSADMIN ));

	## bottom widget area
	weaver_register_sidebar(__( 'Bottom Widget Area', WEAVER_TRANSADMIN ),
	    'bottom-widget-area', __( 'The bottom widget area appears below the content area. It is not displayed on special post pages.', WEAVER_TRANSADMIN ));

	## Site-wide top area
	weaver_register_sidebar(__( 'Sitewide Top Widget Area', WEAVER_TRANSADMIN ),
	    'sitewide-top-widget-area',
	     __( 'This widget area appears at the top of all site static pages and post pages (including special post pages) EXCEPT pages using the blank or iframe page templates.', WEAVER_TRANSADMIN ));

	## Site-wide bottom area
	weaver_register_sidebar(__( 'Sitewide Bottom Widget Area', WEAVER_TRANSADMIN ),
	    'sitewide-bottom-widget-area',
	    __( 'This widget area appears at the bottom of all site static pages and post pages (including special post pages) EXCEPT pages using the blank or iframe page templates.', WEAVER_TRANSADMIN ));

	## Special Post Pages Top Widget area
	weaver_register_sidebar(__( 'Post Pages Top Widget Area', WEAVER_TRANSADMIN ),
	    'postpages-widget-area',
	    __( 'This widget area will appear at the top of special post pages (archives, attachment, author, category, single post) This is not used on the main blog page.', WEAVER_TRANSADMIN ));

	## header widget area
	weaver_register_sidebar( __( 'Header Widget Area', WEAVER_TRANSADMIN ),
	    'header-widget-area',
	     __( "The header widget area appears at the top of the page. It is intended
		for more advanced web pages, and is designed primarily to use Text Widgets to show social feeds
		or other custom items. Styling is via '#ttw-head-widget', '#ttw-head-widget .textwidget',
		and inline span style rules. Unless you add widgets, it doesn't show.", WEAVER_TRANSADMIN ));

	// Area 3, located in the footer. Empty by default.
	weaver_register_sidebar( __( 'First Footer Widget Area', WEAVER_TRANSADMIN ),
	    'first-footer-widget-area',
	     __( 'The first footer widget area', WEAVER_TRANSADMIN ));

	// Area 4, located in the footer. Empty by default.
	weaver_register_sidebar(__( 'Second Footer Widget Area', WEAVER_TRANSADMIN ),
	    'second-footer-widget-area',
	    __( 'The second footer widget area', WEAVER_TRANSADMIN ));

	// Area 5, located in the footer. Empty by default.
	weaver_register_sidebar(__( 'Third Footer Widget Area', WEAVER_TRANSADMIN ),
	    'third-footer-widget-area',
	    __( 'The third footer widget area', WEAVER_TRANSADMIN ));

	// Area 6, located in the footer. Empty by default.
	weaver_register_sidebar( __( 'Fourth Footer Widget Area', WEAVER_TRANSADMIN ),
	    'fourth-footer-widget-area',
	    __( 'The fourth footer widget area', WEAVER_TRANSADMIN ));

	$extra_areas = weaver_getopt('ttw_perpagewidgets');	// create extra areas?
	if (strlen($extra_areas) > 0) {
	    $extra_list = explode(',', $extra_areas);
	    foreach ($extra_list as $area) {
		weaver_register_sidebar( __('Per Page Area ', WEAVER_TRANSADMIN) . $area,
		    'per-page-'.$area,
		    __('This widget can serve as an additional Top, Primary, Secondary, Alternative, or Weaver Plus [weaver_widget_area] area replacement when you add its name "', WEAVER_TRANSADMIN) .
		       $area . __('" to individual pages from the "Weaver Options For This Page" box. You can style it using: ', WEAVER_TRANSADMIN) .
		       '".per-page-' . $area .'".'
		    );
	    }

	}
}
/** Register sidebars by running weaver_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'weaver_widgets_init' );
endif;

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 */
function weaver_remove_recent_comments_style() {
    if (false /* USE_WP_3_1 */) {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
    } else {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
    }
}

add_action( 'widgets_init', 'weaver_remove_recent_comments_style' );

if ( ! function_exists( 'weaver_per_post_style' ) ) {
function weaver_per_post_style() {
    // Emit a <style> for this post
    global $weaver_cur_post_id;

    $post_style = weaver_get_per_post_value('ttw_per_post_style');
    if (!empty($post_style)) {
	$rules = explode('}', trim($post_style));
	$post_id = '#post-' . $weaver_cur_post_id;
	echo ("\n<style type=\"text/css\">\n");
	foreach ($rules as $rule) {
	    $rule = trim($rule);
	    if (strlen($rule) > 1)  		// must have some content to the rule!
		echo("$post_id $rule}\n");	// add the post id to the front of each rule
	}
	echo("</style>\n");
    }
}
}

if ( ! function_exists( 'weaver_post_title' ) ) :
// display the post title
function weaver_post_title($single = '') {

	weaver_put_plus_postaddhtml($single,'wvp_post_pretitle');

	if ($single != 'single' && weaver_is_checked_post_opt('ttw-favorite-post')) {
		printf(sprintf("<img src=\"%s/images/icons/yellow-star.png\" />", get_template_directory_uri()));
	}
	if (weaver_getopt_plus('wvp_post_no_titlelink') || weaver_is_checked_post_opt('wvpp_post_no_titlelink') ) {
	    the_title();
	} else {
?>
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', WEAVER_TRANS ),
	   the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
<?php	}
	if ( (weaver_getopt('ttw_show_post_avatar') || weaver_is_checked_post_opt('ttw-show-post-avatar')) && !weaver_getopt('ttw_show_tiny_avatar')) { ?>
	    <div class="post-avatar" style="float: right;" >
	    <?php echo(get_avatar( get_the_author_meta('user_email') ,44,null,'avatar')); ?>
	    </div>
	<?php
	}
}
endif;

function weaver_put_plus_postaddhtml($single,$opt) {
    // add extra post html areas

    if (weaver_getopt_plus('wvp_post_blog_hidehtml') && $single != 'single')
        return;
    if (weaver_getopt_plus('wvp_post_single_hidehtml') && $single == 'single')
        return;

    $add = weaver_getopt_plus($opt);
    if ($opt) echo $add;

}

if ( ! function_exists( 'weaver_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function weaver_posted_on($single='blog',$from_bottom=false) {
    // Generate the Posted on xx by yy line.

	if ($from_bottom)	// always show if from bottom...
		weaver_posted_on_code();
	else if (!weaver_getopt_plus('wvp_post_info_move_top'))
		weaver_posted_on_code();

	if (!$from_bottom && weaver_getopt_plus('wvp_post_info_move_bottom')) {
	    weaver_posted_in($single,true);
	}

	if (!$from_bottom) {
	    weaver_put_plus_postaddhtml($single,'wvp_post_prebody');
	}
}

function weaver_posted_on_code() {
	if (weaver_getopt_plus('wvp_post_info_hide_top') || weaver_is_checked_page_opt('wvp_perpost_info_hide_top')) {
		return;
	}

	$leftm = '8';
	if (!weaver_getopt('ttw_hide_post_fill')) $leftm = '0';	// no left margin if not hiding fill in

	echo "        <div class=\"entry-meta\">\n";

	$by_img = "";
	if (weaver_getopt('ttw_post_icons')) {
	    if (!weaver_getopt('ttw_post_hide_date')) printf(sprintf('<img src="%s/images/icons/date-1.png" style="position:relative; top:4px; padding-right:4px;" />',
		get_template_directory_uri()));
	    if (!weaver_getopt('ttw_post_hide_author'))
	       $by_img = sprintf('<img src="%s/images/icons/author-1.png" style="position:relative; top:4px; padding-right:4px; margin-left:%spx;" />',
	       	    get_template_directory_uri(),$leftm);
	}

	if (!weaver_getopt('ttw_post_hide_date')) {
	    $msg = __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', WEAVER_TRANS );
	    if (weaver_getopt('ttw_post_hide_author')) $msg = __( '<span class="%1$s">Posted on</span> %2$s %3$s', WEAVER_TRANS );
	    printf( $msg,
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),$by_img);
	} else {
	    if (!empty($by_img)) echo $by_img;
	}
	if (!weaver_getopt('ttw_post_hide_author')) {
	    printf(
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', WEAVER_TRANS ), get_the_author() ),
			get_the_author()
	    ));

	    if ( (weaver_getopt('ttw_show_post_avatar') || weaver_is_checked_post_opt('ttw-show-post-avatar')) && weaver_getopt('ttw_show_tiny_avatar')) { ?>
	    <span class="post-avatar" style="padding-left:8px;position:relative; top:4px;">
	    <?php echo(get_avatar( get_the_author_meta('user_email') ,16,null,'avatar')); ?>
	    </span>
	<?php
	   }

	}
	echo "\n        </div><!-- .entry-meta -->\n";
}
endif;

if ( ! function_exists( 'weaver_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 */
function weaver_posted_in($single='blog',$from_top=false) {
	// display the standard cat/tag/comments line

	if (!$from_top) {
		weaver_put_plus_postaddhtml($single,'wvp_post_postbody');
	}

	if ($from_top)	// always show if from top...
		weaver_posted_in_code($single);
	else if (!weaver_getopt_plus('wvp_post_info_move_bottom'))
		weaver_posted_in_code($single);

	if (!$from_top && weaver_getopt_plus('wvp_post_info_move_top')) {
	    weaver_posted_on($single,true);
	}
}

function weaver_posted_in_code($single='blog') {
	global $weaver_cur_post_id;

	if (weaver_getopt_plus('wvp_post_info_hide_bottom') || weaver_is_checked_page_opt('wvp_perpost_info_hide_bottom')) {
		return;
	}
	if ($single == 'single') {
		weaver_posted_in_single();
		return;
	}
	$leftm = '8';
	if (!weaver_getopt('ttw_hide_post_fill')) $leftm = '0';	// no left margin if not hiding fill in

	echo("<div class=\"entry-utility\">\n");

	$need_sep = false;

	$cat_count = count( get_the_category() );
	if ( ($cat_count > 1 || ($cat_count < 2 && !weaver_getopt('ttw_hide_singleton_cat'))) && !weaver_getopt('ttw_post_hide_cats') ) {
	    if (weaver_getopt('ttw_post_icons')) {
		printf(sprintf('<img class="entry-cat-img" src="%s/images/icons/category-1.png" style="position:relative; top:4px;" />',
			get_template_directory_uri()));
	    }
	?>
		<span class="cat-links">
		<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', WEAVER_TRANS ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
		</span>
	<?php
	$need_sep = true;
	}

	if (!weaver_getopt('ttw_post_hide_cats')) {
	    $tags_list = get_the_tag_list( '', ', ' );
	    if ( $tags_list ) {
		if ($need_sep) { ?>
			<span class="meta-sep meta-sep-bar">|</span>
		<?php
		$need_sep = true;
		}
		if (weaver_getopt('ttw_post_icons')) {
		    printf(sprintf('<img class="entry-tag-img" src="%s/images/icons/tag-1.png" style="position:relative; top:5px; padding-left:5px;" />',
			get_template_directory_uri()));
		}
		?>
		<span class="tag-links">
		<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', WEAVER_TRANS ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
		</span>
	<?php }
	}

	global $post;

	if ( !weaver_getopt('ttw_hide_comments_closed') || get_comments_number($weaver_cur_post_id) > 0 || 'open' == $post->comment_status) {
	    if ($need_sep) { ?>
		<span class="meta-sep meta-sep-bar">|</span>
	    <?php
	    $need_sep = true;
	    }
	    if (weaver_getopt('ttw_post_icons')) {
		printf(sprintf('<img class="entry-comment-img" src="%s/images/icons/comment-1.png" style="position:relative; top:5px; padding-left:5px;" />',
			get_template_directory_uri()));
	    }
	?>
	    <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', WEAVER_TRANS ), __( '1 Comment', WEAVER_TRANS ), __( '% Comments', WEAVER_TRANS ) ); ?></span>
	<?php
	}

	edit_post_link( __( 'Edit', WEAVER_TRANS ), '<span class="meta-sep meta-sep-bar">|</span> <span class="edit-link">', '</span>' );

	echo("</div><!-- .entry-utility -->\n");
}
endif;

if ( ! function_exists( 'weaver_posted_in_single' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 */
function weaver_posted_in_single() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list && !weaver_getopt('ttw_post_hide_cats')) {
	    if (!weaver_getopt('ttw_post_icons')) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', WEAVER_TRANS );
	    } else {
		$posted_in = sprintf('<img class="entry-cat-img" src="%s/images/icons/category-1.png" style="position:relative; top:4px;" />',
			get_template_directory_uri()) . '%1$s' .
			sprintf('<img class="entry-tag-img" src="%s/images/icons/tag-1.png" style="position:relative; top:5px; padding-left:8px;" />',
			get_template_directory_uri()) . '%2$s' .
			sprintf('<img class="entry-permalink-img" src="%s/images/icons/permalink-1.png" style="position:relative; top:5px; padding-left:8px;" />',
			get_template_directory_uri()) . '<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>';
	    }
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) && !weaver_getopt('ttw_post_hide_cats')) {
	    if (!weaver_getopt('ttw_post_icons')) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', WEAVER_TRANS );
	    } else {
		$posted_in = sprintf('<img class="entry-cat-img" src="%s/images/icons/category-1.png" style="position:relative; top:4px;" />',
			get_template_directory_uri()) . '%1$s' .
			sprintf('<img class="entry-permalink-img" src="%s/images/icons/permalink-1.png" style="position:relative; top:5px; padding-left:8px;" />',
			get_template_directory_uri()) . '<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>';
	    }
	} else {
	    if (!weaver_getopt('ttw_post_icons')) {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', WEAVER_TRANS );
	    } else {
		$posted_in =
			sprintf('<img class="entry-permalink-img" style="position:relative; top:1px; float:left;" src="%s/images/icons/permalink-1.png" />',
			get_template_directory_uri()) . '<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>';
	    }
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
	edit_post_link( __( 'Edit', WEAVER_TRANS ), ' <span class="edit-link">', '</span>' );
}
endif;

// New stuff for Weaver

if (!function_exists('weaver_put_wvr_widgetarea')) :

function weaver_put_wvr_widgetarea($area,$style,$pagetype = false) {
    // emit ttw widget area depending on various settings (for page.php and index.php)

    if (weaver_is_checked_page_opt($area)) return;		// hide area option checked.

    $showwidg = !weaver_getopt($pagetype);
    if (is_front_page() && weaver_getopt('ttw_force_widg_frontpage')) $showwidg = true;

    if ($showwidg && is_active_sidebar($area)) { /* add top and bottom widget areas */
	ob_start(); /* let's use output buffering to allow use of Dynamic Widgets plugin and not have empty sidebar */
	$success = dynamic_sidebar($area);
	$content = ob_get_clean();
	if ($success) {
	?>
	    <div id=<?php echo ('"'.$style.'"'); ?> class="widget-area" role="complementary" ><ul class="xoxo">
	    <?php echo($content) ; ?>
	    </ul></div>
	<?php
	}
    }
}
endif;

if (! function_exists('weaver_the_content_featured')) :
function weaver_the_content_featured($force_featured=false) {
    if (weaver_getopt('ttw_show_featured_image_fullposts') || (weaver_getopt('ttw_always_excerpt') && weaver_getopt('ttw_show_featured_image_excerptedposts'))
	|| weaver_is_checked_post_opt('ttw-show-featured') || $force_featured) {
	?>
	<span class='featured-image'><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', WEAVER_TRANS ),
	    the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'thumbnail' ); ?></a></span>
	<?php
    }
    if ((weaver_getopt('ttw_always_excerpt') && !weaver_is_checked_post_opt('ttw-force-post-full'))
	|| weaver_is_checked_post_opt('ttw-force-post-excerpt')) {
	the_excerpt();
    } else {
	global $more;
	$more = false;		// need this to make it act like regular blog page
	$m = weaver_continue_reading_link(false);
	weaver_the_content($m);
	echo ("<div class=\"clear-cols\"></div>");
    }
}
endif;

if (!function_exists('weaver_the_content_featured_single')) :
function weaver_the_content_featured_single($force_featured=false) {
    if (weaver_getopt('ttw_show_featured_image_fullposts') || weaver_is_checked_post_opt('ttw-show-featured') || $force_featured) {
	?>
	<span class='featured-image'><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', WEAVER_TRANS ),
	    the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'thumbnail-single' ); ?></a></span>
	<?php
    }
    weaver_the_content();
    echo ("<div class=\"clear-cols\"></div>");
}
endif;

if (!function_exists('weaver_the_excerpt_featured')) :
function weaver_the_excerpt_featured($always_excerpt=false, $force_featured=false) {
    if (weaver_getopt('ttw_show_featured_image_excerptedposts') || weaver_is_checked_post_opt('ttw-show-featured') || $force_featured) {
	?>
	<span class='featured-image'><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', WEAVER_TRANS ),
	    the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'thumbnail' ); ?></a><span>
	<?php
    }
    if ((weaver_getopt('ttw_always_fullpost') || weaver_is_checked_post_opt('ttw-force-post-full')) && !$always_excerpt) {
	global $more;
	$more = false;		// need this to make it act like regular blog page
	$m = weaver_continue_reading_link(false);
	weaver_the_content($m);
	echo ("<div class=\"clear-cols\"></div>");
    }
    else
	the_excerpt();
}
endif;

if (!function_exists('weaver_the_content')) :
function weaver_the_content($m='') {
    if (weaver_is_checked_page_opt('wvr_raw_html') || weaver_is_checked_post_opt('wvr_raw_html')) {
	echo do_shortcode(get_the_content($m));
    } else {
	the_content($m);
    }
}
endif;

if (!function_exists('weaver_show_post_format')) :
function weaver_show_post_format($postID) {
    // use special post template to display a post in the blog if a special post format
    if (function_exists('get_post_format')) {	// for 3.1
	$post_format = get_post_format($postID);
	if ($post_format != '') {
	    get_template_part('content', $post_format);
	    return true;
	}
    }
    return false;
}
endif;

function weaver_page_menu() {
    /* handle sf-menu for wp_page_menu */
    $menu = wp_page_menu(array('echo' => false));
    if (weaver_getopt('ttw_use_superfish')) {
	$ulpos = stripos($menu, '<ul>');
	if ($ulpos !== false) {
	    echo substr_replace($menu, '<ul class="sf-menu">',$ulpos, 4);
	}
    } else {
	echo $menu;
    }
}

/* ============ define and setup admin pages ========== */

function weaver_wp_head() {
    require_once('wvr-includes/wvr-wphead.php');
    weaver_generate_wphead();
}

function weaver_unlink_page($link, $id) {
    $stay = get_post_meta($id, 'ttw-stay-on-page', true);
    if ($stay) {
	return "#";
    } else {
	return $link;
    }
}

function weaver_get_css_filename() {
       return weaver_f_uploads_base_dir() . 'weaver-subthemes/style-weaver.css';
}

function weaver_get_css_url() {
    return weaver_f_uploads_base_url() . 'weaver-subthemes/style-weaver.css';
}

function weaver_admin() {
    require_once('wvr-includes/wvr-admin.php'); // NOW - load the admin stuff

    weaver_do_admin();
}

function weaver_admin_scripts() {
    /* called only on the admin page, enqueue our special style sheet here (for tabbed pages) */

    wp_enqueue_style('weaverStylesheet', get_template_directory_uri().'/wvr-admin-style.css');
    wp_enqueue_script('weaverJscolor', get_template_directory_uri().'/js/jscolor/jscolor.js');
    wp_enqueue_script('weaverYetii', get_template_directory_uri().'/js/yetii/yetii-min.js');
    wp_enqueue_script('weaverHide', get_template_directory_uri().'/js/weaver/weaver-hide-css.js');

    do_action('wvrx_extended_admin_scripts');
    do_action('wvrx_plus_admin_scripts');
}

function weaver_add_admin() {
    /* adds our admin panel  (add_action: admin_menu) */
    // 'edit_theme_options' works for both single and multisite
    $page = add_theme_page(WEAVER_THEMENAME, WEAVER_THEMENAME . ' ' . __('Admin', WEAVER_TRANSADMIN), 'edit_theme_options', basename(__FILE__), 'weaver_admin');
    /* using registered $page handle to hook stylesheet loading for this admin page */
    add_action('admin_print_styles-'.$page, 'weaver_admin_scripts');
}

function weaver_admin_head() {
}

/* add the rest of our files */
require_once('wvr-settings.php');	// settings stay in theme root directory
require_once('wvr-includes/wvr-globals.php');
require_once('wvr-includes/wvr-utils.php');
require_once('wvr-includes/wvr-settings-lib.php');
require_once('wvr-includes/wvr-widgets.php');
require_once('wvr-includes/wvr-page-post-admin.php');	// add admin page/post admin panels
require_once('wvr-includes/wvr-shortcodes.php');

/* This is where the theme hooks into the rest of WordPress */
add_filter('page_link', 'weaver_unlink_page', 10, 2);		// for stay on page

add_action('admin_init', 'weaver_admin_init_cb');

add_action('wp_head', 'weaver_wp_head');
add_action('admin_menu', 'weaver_add_admin');
add_action('admin_head', 'weaver_admin_head');
?>
