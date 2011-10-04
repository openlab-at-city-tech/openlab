<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('//', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<meta name="description" content="<?php bloginfo('description'); ?>" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/colours.css" type="text/css"/>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>

<body id="top" class="<?php echo setColourScheme(); ?>">

	<div class="full-column">

		<div class="center-column">

			<ul id="menu">
				<li><a href="<?php echo get_option('home'); ?>/" <?php if(is_home()) {echo 'class="selected"';} ?>><span>to the beginning</span><br />home</a></li>
				<li><a href="#"><span>description here</span><br />link 1</a></li>
				<li><a href="#"><span>description here</span><br />link 2</a></li>
				<li><a href="<?php bloginfo('rss2_url'); ?>"><span>rss syndication</span><br />entries</a></li>
				<li><a href="<?php bloginfo('comments_rss2_url'); ?>"><span>rss syndication</span><br />comments</a></li>
				<li class="last"><a href="#sidebar"><span>to the bottom</span><br />down</a></li>
			</ul>

			<div class="clearboth"><!-- --></div>

		</div>

	</div>

<div class="center-column">

	<div id="header">

		<div class="blog-name"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></div>
		<div class="description"><?php bloginfo('description'); ?> <a href="#">read more &#187;</a></div>

	</div>