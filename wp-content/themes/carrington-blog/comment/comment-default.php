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

global $post, $comment;

extract($data); // for comment reply link
?>

<div id="comment-<?php comment_ID(); ?>" <?php comment_class('hentry'); ?>>
	<address class="vcard author entry-title comment-author">
<?php 
if (function_exists('get_avatar')) { 
?>
		<span class="photo avatar"><?php echo get_avatar($comment, 48) ?></span>
<?php
}

printf(__('%s <span class="says">says</span>', 'carrington-blog'), '<cite class="fn">'.get_comment_author_link().'</cite>');
?>
	</address><!--.vcard-->

	<div class="entry-content comment-content">
<?php
if ($comment->comment_approved == '0') {
?>
			<p class="notification"><strong><?php _e('(Your comment is awaiting moderation)', 'carrington-blog'); ?></strong></p>
<?php 
}
comment_text();
?>
	</div><!--.entry-content-->
	<div class="clear"></div>
	<div class="comment-meta commentmetadata small">
		<span class="date comment-date">
			<abbr class="published" title="<?php comment_time('Y-m-d\TH:i') ?>"><?php comment_date(); ?>, <a title="<?php _e('Permanent link to this comment','carrington-blog'); ?>" rel="bookmark" href="<?php the_permalink(); ?>#comment-<?php comment_ID(); ?>"><?php comment_time(); ?></a></abbr>
		</span><!--.date-->
<?php
if (function_exists('comment_reply_link')) {
	comment_reply_link(array_merge( $args, array('respond_id' => 'respond-p' . $post->ID, 'depth' => $depth, 'max_depth' => $args['max_depth'])), $comment, $post);
}
edit_comment_link(__('Edit', 'carrington-blog'), '<div class="edit-comment edit">', '</div>');
?>
	</div>
</div><!--.comment-->