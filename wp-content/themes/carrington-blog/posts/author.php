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

global $authordata;

get_header();
?>
<div id="content">
<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();
?>

	<h1 class="page-title"><?php printf(__('Posts by: <a href="%s">%s</a>', 'carrington-blog'), get_author_posts_url($authordata->ID), get_author_name($authordata->ID)); ?></h1>

<?php
		if (!empty($bio)) {
?>

	<div class="description author-bio">

		<h2><?php printf(__('About %s', 'carrington-blog'), get_author_name($authordata->ID)); ?></h2>
	
<?php 
			echo cfct_basic_content_formatting(get_the_author_description()); 
?>
	
	</div>

<?php
		}
		break;
	}
}
rewind_posts();

cfct_loop();
cfct_misc('nav-posts');
?>

</div><!--#content-->
<?php

get_sidebar();

get_footer();

?>