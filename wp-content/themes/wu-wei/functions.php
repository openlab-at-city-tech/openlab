<?php
/**
 * @package WordPress
 * @subpackage Wu Wei
 */

$themecolors = array(
	'bg' => 'ffffff',
	'border' => 'd1d9dc',
	'text' => '516064',
	'link' => 'ff8a00',
	'url' => 'feb925',
);
$content_width = 460; // pixels

// Wu Wei has a small post title area so we need to remove the widont filter
function wuwei_wido() {
    remove_filter( 'the_title', 'widont' );
}
add_action( 'init', 'wuwei_wido' );

// Grab the theme options
require_once ( get_template_directory() . '/theme-options.php' );

// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );

// Make theme available for translation
// Translations can be filed in the /languages/ directory
load_theme_textdomain( 'wu-wei', get_template_directory() . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

// Register nav menu locations
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'wu-wei' ),
) );

// Add a home link to the default menu fallback wp_page_menu and change the menu class
function wuwei_page_menu_args( $args ) {
	$args['show_home'] = true;
	$args['menu_class'] = 'menu menu-main';
	return $args;
}
add_filter( 'wp_page_menu_args', 'wuwei_page_menu_args' );

// Register sidebar 1
register_sidebar( array (
		'name' => __( 'Footer Left', 'wu-wei' ),
		'id' => 'widget-area-1',
		'description' => __( 'Widgets in this area will be shown on the left side your blog footer.', 'wu-wei' ),
	)
);

// Register sidebar 2
register_sidebar( array (
		'name' => __( 'Footer Middle', 'wu-wei' ),
		'id' => 'widget-area-2',
		'description' => __( 'Widgets in this area will be shown in the middle of your blog footer.', 'wu-wei' ),
	)
);

// Register sidebar 3
register_sidebar( array (
		'name' => __( 'Footer Right', 'wu-wei' ),
		'id' => 'widget-area-3',
		'description' => __( 'Widgets in this area will be shown on the right side of your blog footer.', 'wu-wei' ),
	)
);

// Enable custom backgrounds
add_custom_background();

// Your changeable header business starts … NOW
// Set some default values
define('HEADER_TEXTCOLOR', 'D1D9DC'); // Default text color
define('HEADER_IMAGE_WIDTH', 700); // Default image width is actually the div's height
define('HEADER_IMAGE_HEIGHT', 144); // Same for height

function wuwei_header_style() {
// This function defines the style for the theme
// You can change these selectors to match your theme
?>
<style type="text/css">
#header img {
	margin: 2em 0 0 0;
}
<?php
// Has the text been hidden?
if ( 'blank' == get_header_textcolor() && get_header_image() != '' ) { ?>
	#header img {
		margin: -<?php echo HEADER_IMAGE_HEIGHT; ?>px 0 0 0;
	}	
	.blog-name a {
		display: block;
		text-indent: -9000px;
		width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
		height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	}
	.description {
		position: absolute;
		left: -9999px;
	}
<?php } elseif ( 'blank' == get_header_textcolor() && get_header_image() == '' ) {
// No text, no image
?>
.blog-name a, .description {
	position: absolute;
	left: -9999px;
}	
<?php } elseif ( get_header_textcolor() != HEADER_TEXTCOLOR ) {
// If the user has set a custom color for the text use that
?>
.blog-name a:link, .blog-name a:visited, .description {
	color: #<?php echo header_textcolor(); ?>
}
<?php } ?>
</style>
<?php
}

function wuwei_admin_header_style() {
?>
<style type="text/css">
#headimg {
	font-family: "Helvetica Neue",Arial,Helvetica,sans-serif;
	<?php if ( 'blank' != get_header_textcolor() ) : ?>
	<?php endif; ?>
	border: none;
}
.appearance_page_custom-header #headimg {
	border: none;
	min-height: 1px;
}
#headimg h1 {
	font-size: 60px;
	line-height: 1em;
	margin: 0;
	padding-bottom:0.25em;
}
#headimg, #headimg h1 a {
	text-decoration: none;
}
#headimg #desc {
	color: #6A797D;
	font-size: 18px;
	padding-bottom: 30px;
}
<?php if ( 'blank' == get_header_textcolor() ) { ?>
#header h1 a {
	display: none;
}
#header, #header h1 a {
	color: <?php echo HEADER_TEXTCOLOR ?>;
}
<?php } ?>
</style>
<?php
}

function wuwei_admin_header_image() { ?>
	<div id="headimg" style="max-width:<?php echo HEADER_IMAGE_WIDTH; ?>px;">
		<?php
		if ( 'blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<img src="<?php esc_url ( header_image() ); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
	</div>
<?php }

add_custom_image_header('wuwei_header_style', 'wuwei_admin_header_style', 'wuwei_admin_header_image');
// and thus ends the changeable header business

function wuwei_sidebars(){
	// Register sidebar 1
	register_sidebar( array (
			'name' => __( 'Footer Left', 'wu-wei' ),
			'id' => 'widget-area-1',
			'description' => __( 'Widgets in this area will be shown on the left side your blog footer.', 'wu-wei' ),
		)
	);

	// Register sidebar 2
	register_sidebar( array (
			'name' => __( 'Footer Middle', 'wu-wei' ),
			'id' => 'widget-area-2',
			'description' => __( 'Widgets in this area will be shown in the middle of your blog footer.', 'wu-wei' ),

		)
	);

	// Register sidebar 3
	register_sidebar( array (
			'name' => __( 'Footer Right', 'wu-wei' ),
			'id' => 'widget-area-3',
			'description' => __( 'Widgets in this area will be shown on the right side of your blog footer.', 'wu-wei' ),
		)
	);
}
add_action( 'widgets_init', 'wuwei_sidebars'  );