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

?>
<div id="post-search-<?php the_ID() ?>" <?php post_class('search'); ?>>
	<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="Permanent link to <?php the_title_attribute() ?>" rel="bookmark"><?php the_title() ?></a></h2>
	<div class="entry-content">
		<?php the_excerpt() ?>
		<div class="clear"></div>
	</div><!--.entry-content-->
	<p class="by-line">
		<span class="author vcard">
			<?php printf(__('<span class="by alt-font">By</span> %s', 'carrington-blog'), '<a class="url fn" href="'.get_author_link(false, get_the_author_ID(), $authordata->user_nicename).'" title="View all posts by ' . attribute_escape($authordata->display_name) . '">'.get_the_author().'</a>'); ?>
		</span>
		<span class="date"><span class="ndash alt-font">&ndash;</span> <abbr class="published" title="<?php the_time('Y-m-d\TH:i'); ?>"><?php the_time('F j, Y'); ?></abbr></span>
	</p>
</div><!-- .post -->