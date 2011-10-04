<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */

$themecolors = array(
	'bg' => '111111',
	'border' => '111111',
	'text' => 'eeeeee',
	'link' => '428CE7',
	'url' => '428CE7'
);
$content_width = 950; // pixels

// Grab the theme options page
require_once ( get_template_directory() . '/theme-options.php' );

// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );

// Add post thumbnail theme support
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 150, 150, true );

// Add a new image size
add_image_size( 'modularity-slideshow', 950, 425, true );

// Register nav menu locations
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'text_domain' ),
) );

// Get wp_page_menu() lookin' more like wp_nav_menu()
function modularity_page_menu_args( $args ) {
	$args['show_home'] = true;
	$args['menu_class'] = 'main-nav';
	return $args;
}
add_filter( 'wp_page_menu_args', 'modularity_page_menu_args' );

// Give Modularity a custom background
add_custom_background();

// Allow custom colors to clear the background image
function modularity_custom_background_color() {
	if ( get_background_image() == '' && get_background_color() != '' ) { ?>
		<style type="text/css">
		body {
			background-image: none;
		}
		</style>			
	<?php }
}
add_action( 'wp_head', 'modularity_custom_background_color' );

// To use a sidebar, or not to use a sidebar, that is the question. This generates the appropriate class
function modularity_sidebar_class() {
	$options = get_option( 'modularity_theme_options' );

	if ( $options['sidebar'] == 1 ) {
		echo "15 colborder home";
	}
	else {
		echo "24 last";
	}	
}

// The header business begins here:

// No CSS, just IMG call
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', '');
define('HEADER_IMAGE_WIDTH', 950);
define('HEADER_IMAGE_HEIGHT', 200);
define( 'NO_HEADER_TEXT', true );

function modularity_admin_header_style() {
?>
<style type="text/css">
#headimg {
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
}

#headimg h1, #headimg #desc {
	display: none;
}

</style>
<?php
}

add_custom_image_header( '', 'modularity_admin_header_style' );
// and thus ends the header business

// Comments in the Modularity style
function modularity_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-wrapper">
			<div class="comment-meta">
				<?php echo get_avatar( $comment, 75 ); ?>
				<div class="comment-author vcard">
					<strong class="fn"><?php comment_author_link(); ?></strong>
				</div><!-- .comment-author .vcard -->
			</div>
			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'modularity' ); ?></em>
					<br />
				<?php endif; ?>
				<?php comment_text(); ?>
				<p class="post-time">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'modularity' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'modularity' ), ' ' );
					?>
					<br />
				</p>
				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->				
			</div>
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="pingback">
		<p><?php _e( 'Pingback:', 'modularity' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'modularity'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}

// The Sidebar business
$options = get_option( 'modularity_theme_options' );
if ( $options['sidebar'] == 0 ) {
	$optional_description = __( 'The optional Modularity Lite sidebar is currently deactivated but can be activated from Appearance > Theme Options', 'modularity' );	
} else {
	$optional_description = '';	
}

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Sidebar',
		'id' => 'sidebar',
		'description' => $optional_description,
		'before_widget' => '<div id="%1$s" class="item %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="sub">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => 'Footer 1',
		'id' => 'footer-1',
		'before_widget' => '<div id="%1$s" class="item %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="sub">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer 2',
		'id' => 'footer-2',
		'before_widget' => '<div id="%1$s" class="item %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="sub">',
		'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name' => 'Footer 3',
		'id' => 'footer-3',
		'before_widget' => '<div id="%1$s" class="item %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="sub">',
		'after_title' => '</h3>'
	));

	register_sidebar(array(
		'name' => 'Footer 4',
		'id' => 'footer-4',
		'before_widget' => '<div id="%1$s" class="item %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="sub">',
		'after_title' => '</h3>'
	));	
}

// Load Base Javascripts
if (!is_admin()) add_action( 'init', 'load_base_js' );
function load_base_js( ) {

	wp_enqueue_script('jquery');
	//wp_enqueue_script('jquerynav', get_bloginfo('template_directory').'/js/nav.js', array('jquery'));
	wp_enqueue_script('cycle', get_bloginfo('template_directory').'/js/jquery.cycle.js', array('jquery'));
	//wp_enqueue_script('search', get_bloginfo('template_directory').'/js/search.js', array( 'jquery' ) );

}

// Load Dom Ready Javascripts
function load_dom_ready_js() { ?>

	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery(document).ready(function(){
			jQuery(function() {
			    jQuery("#slideshow").cycle({
			        speed: '2500',
			        timeout: '500',
					pause: 1
			    });
			});
		});
	/* ]]> */
	</script>

<?php }
add_action('wp_head', 'load_dom_ready_js');