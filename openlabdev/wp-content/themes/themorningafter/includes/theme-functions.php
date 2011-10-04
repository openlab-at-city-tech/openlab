<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Page / Post navigation
- WooTabs - Popular Posts
- WooTabs - Latest Posts
- WooTabs - Latest Comments
- Custom Post Types - Updates
- WordPress Header Options
- Misc
- WordPress 3.0 New Features Support

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Page / Post navigation */
/*-----------------------------------------------------------------------------------*/
function woo_pagenav() { 

	if (function_exists( 'woo_pagination' ) ) { ?>
    
<?php woo_pagination(); ?>
    
	<?php } else { ?>    
    
		<?php if ( get_next_posts_link() || get_previous_posts_link() ) { ?>
        
            <div class="nav-entries">
                <div class="nav-prev fl"><?php previous_posts_link( __( '&laquo; Newer Entries ', 'woothemes' ) ); ?></div>
                <div class="nav-next fr"><?php next_posts_link( __( ' Older Entries &raquo;', 'woothemes' ) ); ?></div>
                <div class="fix"></div>
            </div>	
        
		<?php } ?>
    
	<?php }   
}                	

function woo_postnav() { 

	?>
        <div class="post-entries">
            <div class="post-prev fl"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ); ?></div>
            <div class="post-next fr"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ); ?></div>
            <div class="fix"></div>
        </div>	

	<?php 
}                	



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_tabs_popular' ) ) {
	function woo_tabs_popular( $posts = 5, $size = 35 ) {
		global $post;
		$popular = get_posts( 'orderby=comment_count&posts_per_page=' . $posts );
		foreach($popular as $post) {
			setup_postdata($post);
	?>
	<li>
		<?php if ($size > 0) { woo_image( 'height=' . $size . '&width=' . $size . '&class=thumbnail&single=true' ); } ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php }
	}
}



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_tabs_latest' ) ) {
	function woo_tabs_latest( $posts = 5, $size = 35 ) {
		global $post;
		$latest = get_posts( 'showposts='. $posts .'&orderby=post_date&order=desc' );
		foreach($latest as $post) {
			setup_postdata($post);
	?>
	<li>
		<?php if ($size > 0) { woo_image( 'height=' . $size . '&width=' . $size . '&class=thumbnail&single=true' ); } ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php } 
	}
}



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/

function woo_tabs_comments( $posts = 5, $size = 35 ) {
	global $wpdb;
	$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
	comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
	comment_type,comment_author_url,
	SUBSTRING(comment_content,1,50) AS com_excerpt
	FROM $wpdb->comments
	LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
	$wpdb->posts.ID)
	WHERE comment_approved = '1' AND comment_type = '' AND
	post_password = ''
	ORDER BY comment_date_gmt DESC LIMIT ".$posts;
	
	$comments = $wpdb->get_results( $sql );
	
	foreach ( $comments as $comment ) {
	?>
	<li>
		<?php echo get_avatar( $comment, $size ); ?>
	
		<a href="<?php echo get_permalink( $comment->ID ); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php _e( 'on ', 'woothemes'); ?> <?php echo $comment->post_title; ?>">
			<?php echo strip_tags( $comment->comment_author ); ?>: <?php echo strip_tags( $comment->com_excerpt ); ?>...
		</a>
		<div class="fix"></div>
	</li>
	<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Custom Post Types - Updates */
/*-----------------------------------------------------------------------------------*/

register_post_type(
	'updates', array(
	'label' => __( 'Updates', 'woothemes' ),
	'singular_label' => __( 'Update', 'woothemes' ),
	'public' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'rewrite' => false,
	'query_var' => false,
	'supports' => array( 'editor', 'author','trackbacks','comments' )
));

/*-----------------------------------------------------------------------------------*/
/* - WordPress Header Options */
/*-----------------------------------------------------------------------------------*/

/** Tell WordPress to run woo_header_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'woo_header_setup' );

if ( ! function_exists('woo_header_setup') ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails, navigation menus, and automatic feed links.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since 3.0.0
 */
function woo_header_setup() {

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/book.png' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'woo_header_image_width',  960 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'woo_header_image_height',	70 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall (larger images will be auto-cropped to fit).
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'woo_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array (
		'book' => array (
			'url' => '%s/images/headers/book.png',
			'thumbnail_url' => '%s/images/headers/book_thumb.png',
			'description' => __( 'Book', 'woothemes' )
		),
		'sky' => array (
			'url' => '%s/images/headers/sky.png',
			'thumbnail_url' => '%s/images/headers/sky_thumb.png',
			'description' => __( 'Sky', 'woothemes' )
		),
		'road' => array (
			'url' => '%s/images/headers/road.png',
			'thumbnail_url' => '%s/images/headers/road_thumb.png',
			'description' => __( 'Road', 'woothemes' )
		)
	) );
}
endif;

if ( ! function_exists( 'woo_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since 3.0.0
 */
function woo_admin_header_style() {
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
endif;


/*-----------------------------------------------------------------------------------*/
/* MISC */
/*-----------------------------------------------------------------------------------*/

//Make options panel title smaller
function tma_panel_tweak(){
?>
<style type="text/css">
	.toplevel_page_woothemes { font-size: 11px !important; }
</style>
<?php
}

add_action( 'admin_head','tma_panel_tweak' );

/*-----------------------------------------------------------------------------------*/
/* WordPress 3.0 New Features Support */
/*-----------------------------------------------------------------------------------*/

if ( function_exists('wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu' ) ) );
} 
    
?>