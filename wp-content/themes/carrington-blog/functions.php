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

load_theme_textdomain('carrington-blog');

define('CFCT_DEBUG', false);
define('CFCT_PATH', trailingslashit(TEMPLATEPATH));
define('CFCT_HOME_LIST_LENGTH', 5);
define('CFCT_HOME_LATEST_LENGTH', 250);

$cfct_options = array(
	'cfct_home_column_1_cat',
	'cfct_home_column_1_content',
	'cfct_latest_limit_1',
	'cfct_list_limit_1',
	'cfct_home_column_2_cat',
	'cfct_home_column_2_content',
	'cfct_latest_limit_2',
	'cfct_list_limit_2',
	'cfct_home_column_3_cat',
	'cfct_home_column_3_content',
	'cfct_latest_limit_3',
	'cfct_list_limit_3',
	'cfct_ajax_load',
	'cfct_lightbox',
	'cfct_posts_per_archive_page',
	'cfct_custom_colors',
	'cfct_custom_header_image',
	'cfct_header_image_type',
	'cfct_footer_image_type',
	'cfct_header_image',
	'cfct_css_background_images',
);

$cfct_color_options = array(
	'cfct_header_background_color' => '51555c',
	'cfct_header_text_color' => 'cecfd1',
	'cfct_header_link_color' => 'ffffff',
	'cfct_header_nav_background_color' => 'e9eaea',
	'cfct_header_nav_link_color' => 'a00004',
	'cfct_header_nav_text_color' => '51555c',
	'cfct_page_title_color' => '51555c',
	'cfct_page_subtitle_color' => '51555c',
	'cfct_link_color' => 'a00004',
	'cfct_footer_background_color' => '51555c',
	'cfct_footer_text_color' => '999999',
	'cfct_footer_link_color' => 'CECFD1',
);

foreach ($cfct_color_options as $k => $default) {
	$cfct_options[] = $k;
}

function cfct_blog_option_defaults($options) {
	$options['cfct_list_limit_1'] = CFCT_HOME_LIST_LENGTH;
	$options['cfct_latest_limit_1'] = CFCT_HOME_LATEST_LENGTH;
	$options['cfct_list_limit_2'] = CFCT_HOME_LIST_LENGTH;
	$options['cfct_latest_limit_2'] = CFCT_HOME_LATEST_LENGTH;
	$options['cfct_list_limit_3'] = CFCT_HOME_LIST_LENGTH;
	$options['cfct_latest_limit_3'] = CFCT_HOME_LATEST_LENGTH;
	$options['cfct_ajax_load'] = 'yes';
	$options['cfct_custom_colors'] = 'no';
	$options['cfct_custom_header_image'] = 'no';
	$options['cfct_css_background_images'] = 'yes';
	return $options;
}
add_filter('cfct_option_defaults', 'cfct_blog_option_defaults');


function cfct_blog_init() {
	if (cfct_get_option('cfct_ajax_load') == 'yes') {
		cfct_ajax_load();
	}
	if (cfct_get_option('cfct_lightbox') != 'no' && !is_admin()) {
		wp_enqueue_script('cfct_thickbox', get_bloginfo('template_directory').'/carrington-core/lightbox/thickbox.js', array('jquery'), '1.0');
// in the future we'll use this, but for now we want 2.5 compatibility
//		wp_enqueue_style('jquery-lightbox', get_bloginfo('template_directory').'/carrington-core/lightbox/css/lightbox.css');
	}
}
add_action('init', 'cfct_blog_init');

wp_enqueue_script('jquery');
wp_enqueue_script('carrington', get_bloginfo('template_directory').'/js/carrington.js', array('jquery'), '1.0');

// Filter comment reply link to work with namespaced comment-reply javascript.
add_filter('cancel_comment_reply_link', 'cfct_get_cancel_comment_reply_link', 10, 3);

function cfct_blog_head() {
// see enqueued style in cfct_blog_init, we'll activate that in the future
	if (cfct_get_option('cfct_lightbox') != 'no') {
		echo '
<link rel="stylesheet" type="text/css" media="screen" href="'.get_bloginfo('template_directory').'/carrington-core/lightbox/css/thickbox.css" />
		';
	}
	cfct_get_option('cfct_ajax_load') == 'no' ? $ajax_load = 'false' : $ajax_load = 'true';
	echo '
<script type="text/javascript">
var CFCT_URL = "'.get_bloginfo('url').'";
var CFCT_AJAX_LOAD = '.$ajax_load.';
</script>
	';
	if (cfct_get_option('cfct_lightbox') != 'no') {
		echo '
<script type="text/javascript">
tb_pathToImage = "' . get_bloginfo('template_directory') . '/carrington-core/lightbox/img/loadingAnimation.gif";
jQuery(function($) {
	$("a.thickbox").each(function() {
		var url = $(this).attr("rel");
		var post_id = $(this).parents("div.post").attr("id");
		$(this).attr("href", url).attr("rel", post_id);
	});
});
</script>
		';
	}
// preview
	if (isset($_GET['cfct_action']) && $_GET['cfct_action'] == 'custom_color_preview' && current_user_can('manage_options')) {
		cfct_blog_custom_colors('preview');
	}
	else if (cfct_get_option('cfct_custom_colors') == 'yes') {
		cfct_blog_custom_colors();
	}
	if (cfct_get_option('cfct_custom_header_image') == 'yes') {
		$header_image = cfct_get_option('cfct_header_image');
		if ($header_image != 0 && $img = wp_get_attachment_image_src($header_image, 'large')) {
?>
<style type="text/css">
#header .wrapper {
	background-image: url(<?php echo $img[0]; ?>);
	background-repeat: no-repeat;
	height: <?php echo $img[2]; ?>px;
}
</style>
<?php
		}
		else {
?>
<style type="text/css">
#header .wrapper {
	background-image: url();
}
</style>
<?php
		}
	}
}
add_action('wp_head', 'cfct_blog_head');

function cfct_blog_custom_colors($type = 'option') {
	$colors = cfct_get_custom_colors($type);
	if (get_option('cfct_header_image_type') == 'light') {
		$header_img_type = 'light';
		$header_grad_type = 'dark';
	}
	else {
		$header_img_type = 'dark';
		$header_grad_type = 'light';
	}
	get_option('cfct_footer_image_type') == 'light' ? $footer_img_type = 'light' : $footer_img_type = 'dark';
?>
<style type="text/css">
#header {
	background-color: #<?php echo $colors['cfct_header_background_color']; ?>;
	color: #<?php echo $colors['cfct_header_text_color']; ?>;
}
#header a,
#header a:visited {
	color: #<?php echo $colors['cfct_header_link_color']; ?>;
}
#sub-header,
.nav ul{
	background-color: #<?php echo $colors['cfct_header_nav_background_color']; ?>;
	color: #<?php echo $colors['cfct_header_nav_text_color']; ?>;
}
#sub-header a,
#sub-header a:visited,
.nav li li a,
.nav li li a:visited {
	color: #<?php echo $colors['cfct_header_nav_link_color']; ?> !important;
}
h1,
h1 a,
h1 a:hover,
h1 a:visited {
	color: #<?php echo $colors['cfct_page_title_color']; ?>;
}
h2,
h2 a,
h2 a:hover,
h2 a:visited {
	color: #<?php echo $colors['cfct_page_subtitle_color']; ?>;
}
a,
a:hover,
a:visited {
	color: #<?php echo $colors['cfct_link_color']; ?>;
}
.hentry .edit,
.hentry .edit a,
.hentry .edit a:visited,
.hentry .edit a:hover,
.comment-reply-link,
.comment-reply-link:visited,
.comment-reply-link:hover {
	background-color: #<?php echo $colors['cfct_link_color']; ?>;
}
#footer {
	background-color: #<?php echo $colors['cfct_footer_background_color']; ?>;
	color: #<?php echo $colors['cfct_footer_text_color']; ?>;
}
#footer a,
#footer a:visited {
	color: #<?php echo $colors['cfct_footer_link_color']; ?>;
}
#footer p#developer-link a,
#footer p#developer-link a:visited {
	background-image: url(<?php bloginfo('template_directory'); ?>/img/footer/by-crowd-favorite-<?php echo $footer_img_type; ?>.png);
}
<?php
	if (cfct_get_option('cfct_css_background_images') != 'no') {
?>
#header {
	background-image: url(<?php bloginfo('template_directory'); ?>/img/header/gradient-<?php echo $header_grad_type; ?>.png);
}
#header .wrapper {
	background-image: url(<?php bloginfo('template_directory'); ?>/img/header/texture-<?php echo $header_img_type; ?>.png);
}
#footer {
	background-image: url(<?php bloginfo('template_directory'); ?>/img/footer/gradient-<?php echo $footer_img_type; ?>.png);
}
<?php
	}
?>
</style>
<?php

}

include_once(CFCT_PATH.'functions/admin.php');
include_once(CFCT_PATH.'functions/sidebars.php');

include_once(CFCT_PATH.'carrington-core/carrington.php');

?>