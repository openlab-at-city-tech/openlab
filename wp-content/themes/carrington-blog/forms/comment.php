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

global $post, $user_ID, $user_identity;

$commenter = wp_get_current_commenter();
extract($commenter);

$req = get_option('require_name_email');

// if post is open to new comments
if ('open' == $post->comment_status) {
	// if you need to be regestered to post comments..
	if ( get_option('comment_registration') && !$user_ID ) { ?>

<p id="you-must-be-logged-in-to-comment"><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'carrington-blog'), get_bloginfo('wpurl').'/wp-login.php?redirect_to='.urlencode(get_permalink())); ?></p>

<?php
	}
	else { 
?>

<div id="respond-p<?php echo $post->ID; ?>">
	<form action="<?php echo trailingslashit(get_bloginfo('wpurl')); ?>wp-comments-post.php" method="post" class="comment-form">
		<p class="comment-form-comment tight">
		<label class="h3" for="comment-p<?php echo $post->ID; ?>"><?php 
		if (function_exists('comment_form_title')) {
			comment_form_title();
			echo ' <em id="cancel-comment-reply">', cancel_comment_reply_link(), '</em>';
		}
		else {
			_e('Post a comment', 'carrington-blog');
		}
?></label>
			<br class="lofi" />
			<span class="comment-form-comment-area">
				<textarea id="comment-p<?php echo $post->ID; ?>" name="comment" rows="8" cols="40"></textarea><br />
				<em class="some-html-is-ok"><abbr title="<?php printf(__('You can use: %s', 'carrington-blog'), allowed_tags()); ?>"><?php _e('Some HTML is OK', 'carrington-blog'); ?></abbr></em>
			</span>
		</p>
<?php // if you're not logged in...
		if (!$user_ID) {
?>
		<p class="comment-form-user-info tight">
			<input type="text" id="author-p<?php echo $post->ID; ?>" name="author" value="<?php echo $comment_author; ?>" size="22" />
			<label for="author-p<?php echo $post->ID; ?>"><?php _e('Name', 'carrington-blog'); if ($req) { echo ' <em>' , _e('(required)', 'carrington-blog'), '</em>'; } ?></label>
		</p><!--/name-->
		<p class="comment-form-user-info tight">
			<input type="text" id="email-p<?php echo $post->ID; ?>" name="email" value="<?php echo $comment_author_email; ?>" size="22" />
			<label for="email-p<?php echo $post->ID; ?>"><?php
			_e('Email ', 'carrington-blog');
			$req ? $email_note = __('(required, but never shared)', 'carrington-jam') : $email_note = __('(never shared)', 'carrington-jam');
			echo ' <em>'.$email_note.'</em>';
?></label>
		</p><!--/email-->
		<p class="comment-form-user-info tight">
			<input type="text" id="url-p<?php echo $post->ID; ?>" name="url" value="<?php echo $comment_author_url; ?>" size="22" />
			<label title="<?php _e('Your website address', 'carrington-blog'); ?>" for="url-p<?php echo $post->ID; ?>"><?php _e('Web', 'carrington-blog'); ?></label>
		</p><!--/url-->
<?php 
		} 
?>
		<p class="tight">
			<input name="submit" type="submit" value="<?php _e('Post comment', 'carrington-blog'); ?>" />
			<span class="comment-form-trackback"><?php printf(__('or, reply to this post via <a rel="trackback" href="%s">trackback</a>.', 'carrington-blog'), get_trackback_url()); ?></span>
		</p>
<?php // if you're logged in...
		if ($user_ID) {
?>
		<p class="logged-in tight"><?php printf(__('Logged in as <a href="%s">%s</a>. ', 'carrington-blog'), get_bloginfo('wpurl').'/wp-admin/profile.php', $user_identity); wp_loginout(); ?>.</p>
<?php
		}
		cfct_comment_id_fields();
		do_action('comment_form', $post->ID);
?>
	</form>
</div>
<?php 
	} // If registration required and not logged in 
} // If you delete this the sky will fall on your head
?>