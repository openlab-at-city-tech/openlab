<?php

// This file is part of the Carrington Blog Theme for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008-2009 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }

$use_background_img = cfct_get_option('cfct_css_background_images');
$use_background_img == 'no' ? $css_ext = '?type=attachment-noimg' : $css_ext = '?type=attachment';

global $post;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ); ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	
	<link href="<?php bloginfo('url') ?>" rel="home" />
	
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'carrington' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'carrington' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>

	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url') ?>/css/css.php<?php echo $css_ext; ?>" />
	
	<?php if ($use_background_img == 'yes'): ?>
	<!--[if lte IE 6]>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/DD_belatedPNG.js"></script>
		<script type="text/javascript">
			DD_belatedPNG.fix('img, #header, #header .wrapper, .figure-info, .previous-attachment, .next-attachment');
		</script>
	<![endif]-->
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<body id="attachment">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div id="header">
	<a class="wrapper" href="<?php echo get_permalink($post->post_parent); ?>" rev="up post">&larr; <?php printf(__('back to &#8220;%s&#8221;', 'carrington-blog'), get_the_title($post->post_parent)); ?></a>
</div>

<div id="attachment-content" class="figure">
	<div class="entry-attachment">
		<a title="<?php _e('Link to original file','carrington-blog'); ?>" href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'large' ); ?></a>
	</div>
 	<div class="figure-info">
		<div class="caption">
			<h1 class="title"><?php the_title(); ?></h1>
			<?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?>
		</div>
		<div class="description">
			<?php the_content() ?>
		</div>
	</div>

<?php
	if(cfct_get_adjacent_image_link(false) != '') {
		echo '<div class="next-attachment"><span>',next_image_link(),'</span></div>';
	}
	if(cfct_get_adjacent_image_link(true) != '') {
		echo '<div class="previous-attachment"><span>',previous_image_link(),'</span></div>';
	}
	
?>
</div>

<?php endwhile; else:
	cfct_template_file('misc','no-results.php');
endif; ?>

<?php wp_footer() ?>
</body>
</html>