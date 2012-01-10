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

global $previousday, $authordata;
$previousday = -1;

?>
<div id="post-content-<?php the_ID() ?>" <?php post_class('full'); ?>>
	
	<h1 class="entry-title full-title"><a href="<?php the_permalink() ?>" title="Permanent link to <?php the_title_attribute() ?>" rel="bookmark" rev="post-<?php the_ID(); ?>"><?php the_title() ?></a></h1>
	
	<div class="entry-content full-content">
<?php 
		the_content('<span class="more-link">'.__('Continued...', 'carrington-blog').'</span>'); 
		$args = array(
			'before' => '<p class="pages-link">'. __('Pages: ', 'carrington-blog'),
			'after' => "</p>\n",
			'next_or_number' => 'number'
		);
		wp_link_pages($args);
?>
		<div class="clear"></div>
	</div><!-- .entry-content-->
	
	<p class="filed categories alt-font tight"><?php printf(__('Posted in %s.', 'carrington-blog'), get_the_category_list(', ')); ?></p>
	<?php the_tags(__('<p class="filed tags alt-font tight">Tagged with ', 'carrington-blog'), ', ', '.</p>'); ?>

	<p class="comments-link"><?php comments_popup_link(__('No comments', 'carrington-blog'), __('1 comment', 'carrington-blog'), __('% comments', 'carrington-blog')); ?></p>

	<p class="by-line">
		<span class="author vcard full-author">
			<?php printf(__('<span class="by alt-font">By</span> %s', 'carrington-blog'), '<a class="url fn" href="'.get_author_link(false, get_the_author_ID(), $authordata->user_nicename).'" title="View all posts by ' . attribute_escape($authordata->display_name) . '">'.get_the_author().'</a>') ?>
		</span>
		<span class="date full-date"><span class="ndash alt-font">&ndash;</span> <abbr class="published" title="<?php the_time('Y-m-d\TH:i'); ?>"><?php the_date(); ?></abbr></span>
	</p><!--/by-line-->

	<div id="post-comments-<?php the_ID(); ?>-target"></div>
	<div class="clear"></div>
	
	<?php edit_post_link(__('Edit', 'carrington-blog'), '<div class="edit-post edit">', '</div>'); ?>
</div><!-- .post -->