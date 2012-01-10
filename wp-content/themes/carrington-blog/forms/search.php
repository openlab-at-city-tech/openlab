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

if (get_option('permalink_structure') != '') {
	$onsubmit = "location.href=this.action+'search/'+encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;";
}
else {
	$onsubmit = '';
}

?>

<form method="get" id="cfct-search" action="<?php echo trailingslashit(get_bloginfo('url')); ?>" onsubmit="<?php echo $onsubmit; ?>">
	<div>
		<input type="text" id="cfct-search-input" name="s" value="<?php echo wp_specialchars($s, 1); ?>" size="15" />
		<input type="submit" name="submit_button" value="<?php _e('Search', 'carrington-blog'); ?>" />
	</div>
</form>