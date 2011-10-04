<?php

// Load admin options page
// require_once( get_template_directory() . '/functions/options-page.php' );

$themecolors = array(
	'bg' => '002728',
	'border' => '021013',
	'text' => 'ffffff',
	'link' => 'a8ef9d',
	'url' => 'a8ef9d'
);

$content_width = 640;

// Widgets
if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar(
		array(
			'name' => 'Sidebar',
			'id' => 'sidebar',
			'before_widget' => '<li id="%1$s" class="boxed widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'name' => 'Footer Left',
			'id' => 'footer_left',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'name' => 'Footer Middle',
			'id' => 'footer_middle',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'name' => 'Footer Right',
			'id' => 'footer_right',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'name' => 'Header',
			'id' => 'header',
			'before_widget' => '<div id="headerbanner" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>'
		)
	);
}

// Comments
function motiontheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-wrap">
		<?php echo get_avatar($comment,$size='50'); ?>
		<div class="commentbody">
			<div class="author"><?php comment_author_link(); ?></div>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em>(Your comment is awaiting moderation...)</em>
			<?php endif; ?>
			<div class="commentmetadata"><a href="#comment-<?php comment_ID(); ?>" title=""><?php comment_date('F jS, Y'); ?> at <?php comment_time(); ?></a> <?php edit_comment_link( 'edit', '&nbsp;&nbsp;' , '' ); ?></div>
			<?php comment_text(); ?>
		</div><!-- /commentbody -->

		<div class="reply">
		<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	</div><!-- /comment -->
<?php
}

function motiontheme_ping($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-wrap">
		<div class="commentbody">
			<div class="author"><?php comment_author_link(); ?></div>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em>(Your comment is awaiting moderation...)</em>
			<?php endif; ?>
			<?php comment_text(); ?>
		</div><!-- /commentbody -->
	</div>
<?php
}

// Custom header image
define( 'HEADER_TEXTCOLOR', '' );
define( 'HEADER_IMAGE', '%s/images/genericlogo.png' );
define( 'HEADER_IMAGE_WIDTH', 50 );
define( 'HEADER_IMAGE_HEIGHT', 50 );
define( 'NO_HEADER_TEXT', true );

function admin_header_style() {
?>

<style type="text/css">
#headimg {
	background-color: #005760;
	background-position: 50% 50%;
	background-repeat: no-repeat;
	height: <?php echo HEADER_IMAGE_HEIGHT;?>px;
	width: <?php echo HEADER_IMAGE_WIDTH;?>px;
	padding: 25px;
}
#headimg h1, #headimg #desc {
	display: none;
}
</style>

<?php }

add_custom_image_header( '', 'admin_header_style' );

// Theme options: hide categories, hide home link
function motion_hide_categories() {
	return get_option( 'motion_hide_categories' );
}

function motion_hide_homelink() {
	return get_option( 'motion_hide_homelink' );
}

register_nav_menus(array(
    'primary' => __('Primary', 'nix'),
));

register_nav_menus(array(
    'top' => __('Top', 'nix'),
));

add_theme_support('automatic-feed-links');
add_custom_background();