<?php global $vigilance; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php if ( is_front_page() ) : ?>
		<title><?php bloginfo( 'name' ); ?></title>
	<?php elseif ( is_404() ) : ?>
		<title><?php _e( 'Page Not Found |', 'vigilance' ); ?> <?php bloginfo( 'name' ); ?></title>
	<?php elseif ( is_search() ) : ?>
		<title><?php printf( __("Search results for '%s'", "vigilance"), get_search_query()); ?> | <?php bloginfo( 'name' ); ?></title>
	<?php else : ?>
		<title><?php wp_title($sep = '' ); ?> | <?php bloginfo( 'name' );?></title>
	<?php endif; ?>

	<!-- Basic Meta Data -->
	<meta name="copyright" content="Design is copyright 2008 - <?php echo date('Y'); ?> The Theme Foundry" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<?php if ((is_single() || is_category() || is_page() || is_home()) && (!is_paged())) {} else { ?>
		<meta name="robots" content="noindex,follow" />
	<?php } ?>

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php bloginfo( 'stylesheet_directory' ); ?>/images/favicon.ico" />

	<!--Stylesheets-->
	<link href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" rel="stylesheet" />
	<!--[if lt IE 8]><link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_url' ); ?>/stylesheets/ie.css" /><![endif]-->
	<!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo( 'template_url' ); ?>/stylesheets/ie6.css" /><![endif]-->

	<!--WordPress-->
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php bloginfo( 'rss2_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<!--WP Hook-->
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div class="skip-content"><a href="#content"><?php _e( 'Skip to content', 'vigilance' ); ?></a></div>
	<div id="wrapper">
		<div id="header" class="clear">
			<?php if (is_home()) echo( '<h1 id="title">' ); else echo( '<div id="title">' );?><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a><?php if (is_home()) echo( '</h1>' ); else echo( '</div>' );?>
			<div id="description">
				<h2><?php bloginfo( 'description' ); ?></h2>
			</div><!--end description-->
			<div id="nav">
				<ul>
					<li class="page_item <?php if (is_front_page()) echo( 'current_page_item' );?>"><a href="<?php bloginfo( 'url' ); ?>"><?php _e( 'Home', 'vigilance' ); ?></a></li>
					<?php if ($vigilance->hidePages() !== 'true' ) : ?>
						<?php wp_list_pages( 'title_li=&depth=1' ); ?>
					<?php endif; ?>
					<?php if ($vigilance->hideCategories() != 'true' ) : ?>
						<?php wp_list_categories( 'title_li=&depth=1' ); ?>
					<?php endif; ?>
				</ul>
			</div><!--end nav-->
		</div><!--end header-->
		<div id="content" class="pad">
		<?php if (is_file(STYLESHEETPATH . '/header-banner.php' )) include(STYLESHEETPATH . '/header-banner.php' ); else include(TEMPLATEPATH . '/header-banner.php' ); ?>