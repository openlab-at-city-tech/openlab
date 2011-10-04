<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title('&#124;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
<!--[if lt IE 7]>
<link href="<?php get_template_directory_uri(); ?>/ie6.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">var clear="<?php get_template_directory_uri(); ?>/images/clear.gif"; //path to clear.gif</script>
<script type="text/javascript" src="<?php get_template_directory_uri(); ?>/js/unitpngfix.js"></script>

<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
<![endif]-->

<?php wp_enqueue_script( 'sfhover', get_template_directory_uri() . '/js/sfhover.js' ); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="wrapper">

<div id="top">
	<?php
	  if (has_nav_menu('top')) {
        wp_nav_menu( array(
            'theme_location' => 'top',
            'depth' => '1',
            'menu_class' => 'top_menu') );
    }
    else {
        echo '<ul class="top_menu">';
        wp_list_pages( array('depth' => 1, 'title_li' => '' ));
        echo '</ul>';
    }
	?>

	<?php get_search_form(); ?>
</div><!-- /top -->

<div id="header">
	<div id="logo">
		<a href="<?php echo home_url(); ?>"><img src="<?php header_image() ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>
		<h1><a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"><?php bloginfo( 'description' ); ?></div>
	</div><!-- /logo -->

	<?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar( 'header' ) ) : ?>
	<div id="headerbanner">
		<p>Hey there! Thanks for dropping by <?php bloginfo( 'name' ); ?>! Take a look around and grab the <a href="<?php bloginfo( 'rss2_url' ); ?>">RSS feed</a> to stay updated. See you around!</p>
	</div>
	<?php endif; ?>
</div><!-- /header -->

<?php
  if (has_nav_menu('primary')) {
      wp_nav_menu( array(
          'theme_location' => 'primary',
          'depth' => '1',
          'menu_class' => 'primary_menu') );
  }
  else {
      echo '<ul class="primary_menu">';
      wp_list_categories( 'orderby=name&title_li=' );
      echo '</ul>';
  }
?>
