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

global $post;
$orig_post = $post;

?>
<hr class="lofi" />
<div id="sidebar">
	<div id="carrington-subscribe" class="widget">
		<h2 class="widget-title"><?php _e('Subscribe', 'carrington-blog'); ?></h2>
		<a class="feed alignright" title="RSS 2.0 feed for posts" rel="alternate" href="<?php bloginfo('rss2_url') ?>">
			<img src="<?php bloginfo('template_directory'); ?>/img/rss-button.gif" alt="<?php printf( __( '%s latest posts', 'carrington' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" title="<?php printf( __( '%s latest posts', 'carrington' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
		</a>
	</div><!--.widget-->
<?php
$about_text = cfct_about_text();
if (!empty($about_text)) {
?>
	<div id="carrington-about" class="widget">
		<div class="about">
			<h2 class="widget-title"><?php printf(__('About %s', 'carrington-blog'), get_bloginfo('name')); ?></h2>
<?php
	echo $about_text;
?>
		</div>
	</div><!--.widget-->
<?php
}
?>

	<div id="primary-sidebar">
<?php
$post = $orig_post;
if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Primary Sidebar') ) {
?>
		<div id="carrington-archives" class="widget">
			<h2 class="widget-title">Archives</h2>
			<ul>
				<?php wp_get_archives(); ?>
			</ul>
		</div><!--.widget-->
<?php
}
?>
	</div><!--#primary-sidebar-->
	<div id="secondary-sidebar">
<?php
$post = $orig_post;
if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Secondary Sidebar') ) { 
?>
		<div id="carrington-tags" class="widget">
			<h2 class="widget-title">Tags</h2>
			<?php wp_tag_cloud('smallest=10&largest=18&unit=px'); ?>
		</div><!--.widget-->
<?php
}
?>
	</div><!--#secondary-sidebar-->
	<div class="clear"></div>
</div><!--#sidebar-->