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

get_header();

$cat_title = '<a href="'.get_category_link(intval(get_query_var('cat'))).'">'.single_cat_title('', false).'</a>';

?>

<div id="content">
	<h1 class="page-title"><?php printf(__('Category Archives: %s', 'carrington-blog'), $cat_title); ?></h1>

	<div class="description"><?php echo category_description(); ?></div>

<?php
	cfct_loop();
	cfct_misc('nav-posts');
?>
</div>
<?php 
get_sidebar();

get_footer();

?>