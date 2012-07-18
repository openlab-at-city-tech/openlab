<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lte IE 7]>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?> class="lteIE7">
<![endif]-->
<!--[if (gt IE 7) | (!IE)]><!--> 
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!--<![endif]-->

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php wp_title('//', true, 'right'); ?> <?php bloginfo('name'); ?></title>

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<?php $options = get_option('wuwei_theme_options'); if ( $options['colorscheme'] == 1 ) : ?>
	<link rel='stylesheet' type='text/css' href="<?php echo get_template_directory_uri(); ?>/colours.css" />
		<?php if ( is_rtl() ) : ?>
	<link rel='stylesheet' type='text/css' href="<?php echo get_template_directory_uri(); ?>/colours-rtl.css" />			
		<?php endif; ?>
	<?php endif; ?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' ); ?>
		
	<?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>

	<div class="full-column">

		<div class="center-column">
			
			<?php wp_nav_menu( 'container_class=menu menu-main&theme_location=primary' ); ?>

			<div class="clearboth"><!-- --></div>

		</div>

	</div>

<div class="center-column">

	<div id="header">

		<div class="blog-name"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo('name'); ?></a></div>
		<div class="description"><?php bloginfo('description'); ?></div>

		<?php if ( get_header_image() != '' ) : ?>
		<a href="<?php echo home_url( '/' ); ?>"><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" /></a>
		<?php endif; ?>

	</div>