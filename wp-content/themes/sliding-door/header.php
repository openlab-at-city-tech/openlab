<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Sliding_Door
 * @since Sliding Door 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'slidingdoor' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

<?php $url = get_template_directory_uri()?>


<?php
$options = get_option('slidingdoor_theme_options');
if ($options['option1']=="0")
  {
  $cssurl=$url."/dark.css";
  }
else
  {
  $cssurl=$url."/light.css";
  } 
?>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $cssurl; ?>">
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $url; ?>/imagemenu/imageMenu.css">
<script type="text/javascript" src="<?php echo $url; ?>/imagemenu/mootools.js"></script>
<script type="text/javascript" src="<?php echo $url; ?>/imagemenu/imageMenu.js"></script>

</head>

<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">
	<div id="header">
		<div id="masthead">
			<div id="branding" role="banner">
							
							
<?php
// Check if this has header image
if ( $img_src = get_header_image ()  ) :
  ?>
<img src="<?php header_image(); ?>" alt="<?php bloginfo('name'); ?>" />
<?php else: ?>

<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
				<<?php echo $heading_tag; ?> id="site-title">
					<span>
						<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</span>
				</<?php echo $heading_tag; ?>>
				<div id="site-description"><?php bloginfo( 'description' ); ?></div>
<?php endif; ?>

	
							
				
				
				


				<div id="imageMenu">
		
				<!-- THESE ARE THE LINKS YOU GO TO WHEN YOU CLICK ON A SLIDING DOOR IMAGE-->
				
				<?php 
				$walker = new My_Walker;
				wp_nav_menu( array( 
				'theme_location' => 'custom-sliding-menu',
				'fallback_cb' => 'no_sliding_menu',
				'container' => '',
				'container_class' =>'',
				'container_id' =>'', 
				'menu_class' =>'',
				'menu_id' =>'',
				'depth' => '1',  
				'walker' => $walker
				) 
				); ?>
				
			</div>
			
				<?php 
	if ($options['option2']=="1")
  { /*  stay open on page */
echo "<script type=\"text/javascript\">
			
			window.addEvent('domready', function(){
				var myMenu = new ImageMenu($$('#imageMenu a'),{openWidth:310, border:2,open:".$post->menu_order.", onOpen:function(e,i){location=(e);}});
			});
		</script>";
  }
else
  { /*  original version */
  echo "<script type=\"text/javascript\">
			
			window.addEvent('domready', function(){
				var myMenu = new ImageMenu($$('#imageMenu a'),{openWidth:310, border:2, onOpen:function(e,i){location=(e);}});
			});
		</script>";
  } 
  ?>
  
  
	
  			


				
			</div><!-- #branding -->

			<div id="access" role="navigation">
			  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
				<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'slidingdoor' ); ?>"><?php _e( 'Skip to content', 'slidingdoor' ); ?></a></div>
				<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
				<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary') ); ?>
			</div><!-- #access -->
		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main">
