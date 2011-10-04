<?php

if (!is_admin()) {
	wp_enqueue_script('jquery');
	wp_enqueue_script('ahsjquery', get_bloginfo('stylesheet_directory') . '/js/jquery.ahs.js', array('jquery'));
}

$content_width = 620;

add_action( 'after_setup_theme', 'ahstheme_setup' );

if ( ! function_exists( 'ahstheme_setup' ) ):

function ahstheme_setup() {

	// Load up our theme options page and related code.
//	require( dirname( __FILE__ ) . '/inc/theme-options.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'ahstheme' ) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// The next four constants set how this theme supports custom headers.

	// The default header text color
	define( 'HEADER_TEXTCOLOR', '000' );

	// By leaving empty, we allow for random image rotation.
	define( 'HEADER_IMAGE', '' );

	// The height and width of your custom header.
	// Add a filter to ahstheme_header_image_width and ahstheme_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'ahstheme_header_image_width', 930 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'ahstheme_header_image_height', 215 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add Twenty Eleven's custom image sizes
	add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
	add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist

	// Turn on random header image rotation by default.
	add_theme_support( 'custom-header', array( 'random-default' => true ) );
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See ahstheme_admin_header_style(), below.
	add_custom_image_header( 'ahstheme_header_style', 'ahstheme_admin_header_style', 'ahstheme_admin_header_image' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	$imgs = array();
	foreach (range(1,9) as $i) {
		$imgs[$i] = array(
			'url' => '%s/images/headers/default-'.$i.'.jpg',
			'thumbnail_url' => '%s/images/headers/default-'.$i.'-thumb.jpg',
			'description' => 'Image '.$i,
		);
	}
	register_default_headers($imgs);
}
endif; // ahstheme_setup

if ( ! function_exists( 'ahstheme_header_style' ) ) :
function ahstheme_header_style() {

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
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
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
endif; // ahstheme_header_style

if ( ! function_exists( 'ahstheme_admin_header_style' ) ) :
function ahstheme_admin_header_style() {
?>
	<style type="text/css">
	</style>
<?php
}
endif; // ahstheme_admin_header_style

if ( ! function_exists( 'ahstheme_admin_header_image' ) ) :
function ahstheme_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // ahstheme_admin_header_image

function ahstheme_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'ahstheme_excerpt_length' );

function ahstheme_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '" class="readmore">' . __( 'Read More', 'ahstheme' ) . '</a>';
}

function ahstheme_auto_excerpt_more( $more ) {
	return ' &hellip;' . ahstheme_continue_reading_link();
}
add_filter( 'excerpt_more', 'ahstheme_auto_excerpt_more' );

function ahstheme_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= ahstheme_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'ahstheme_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function ahstheme_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'ahstheme_page_menu_args' );

function ahstheme_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="nav_<?php echo $nav_id; ?>">
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'twentyeleven' ) ); ?></div>
		</nav>
	<?php endif;
}

if ( ! function_exists( 'ahstheme_comment' ) ) :

function ahstheme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'ahstheme' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'ahstheme' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'ahstheme' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'ahstheme' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'ahstheme' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'ahstheme' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'ahstheme' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for ahstheme_comment()

if ( ! function_exists( 'ahstheme_posted_on' ) ) :
function ahstheme_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'ahstheme' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'ahstheme' ), get_the_author() ),
		esc_html( get_the_author() )
	);
}
endif;

function ahstheme_body_classes( $classes ) {

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'ahstheme_body_classes' );


add_filter('manage_posts_columns', 'ahs_custom_columns');
function ahs_custom_columns($defaults) {
    global $wp_query;
	if ('post' == $wp_query->query_vars['post_type']) {
	    unset($defaults['comments']);
	    unset($defaults['author']);
	    unset($defaults['categories']);
	    unset($defaults['date']);
	    unset($defaults['title']);
	    unset($defaults['tags']);
    	$defaults['thumbnail'] = 'Image';
    	$defaults['title'] = 'Title';
    	$defaults['categories'] = 'Categories';
    	$defaults['tags'] = 'Tags';
    	$defaults['comments'] = 'Comments';
    	$defaults['date'] = 'Date';
    }
    return $defaults;
}

add_action('manage_posts_custom_column',  'my_show_columns');
function my_show_columns($name) {
    global $post;
    switch ($name) {
        case 'thumbnail':
            if (has_post_thumbnail($post->ID)) echo get_the_post_thumbnail($post->ID, array('40','40'));
            else echo '<img width="40" height="40" src="'.get_template_directory_uri().'/images/no-image.jpg" class="attachment-50x50 wp-post-image" alt="No Image Set" title="No Image Set">';
            break;
    }
}

function my_wp_admin_css() { ?>
	<style type="text/css">
	  .wp-list-table .column-thumbnail { width: 70px; }
	  .wp-list-table .column-title { width: 330px; }
	</style> <?php
}

function featitem_add() {
	add_action('admin_head', 'my_wp_admin_css');
}

add_action('admin_init', 'featitem_add');


?>