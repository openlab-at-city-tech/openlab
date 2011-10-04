<?php

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>

		<div id="comment-wrapper">

			<h3 id="comments" id="comment-<?php comment_ID(); ?>">Enter the password to view comments.</h3>

		</div>

	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<div id="comment-wrapper">

	<h3 id="comments"><?php comments_number('no comments', '1 comment', '% comments' );?> <span><a href="#respond">leave your own</a>, follow the <?php comments_rss_link('feed'); ?>, or <a href="<?php trackback_url(FALSE); ?>">trackback</a></span></h3>

	<?php if ( have_comments() ) : ?>

		<!-- Paged comments currently not supported. I may add this at another date
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
			<div class="clearboth"> </div>
		</div>
		-->

		<ol class="commentlist">
		<?php wp_list_comments('avatar_size=48'); ?>
		</ol>

		<!-- Paged comments currently not supported. I may add this at another date
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
			<div class="clearboth"> </div>
		</div>
		-->

	 <?php else : // this is displayed if there are no comments so far ?>

		<?php if ('open' == $post->comment_status) : ?>
			<!-- If comments are open, but there are no comments. -->

		 <?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<p class="nocomments">Comments are closed.</p>

		<?php endif; ?>

	<?php endif; ?>


	<?php if ('open' == $post->comment_status) : ?>

	<div id="respond">

		<h3><?php comment_form_title( 'Leave a reply', 'Reply to %s' ); ?> <span></span></h3>

		<div class="cancel-comment-reply">
			<?php cancel_comment_reply_link('Cancel reply'); ?>
		</div>

		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>

			<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>

		<?php else : ?>

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

				<?php if ( $user_ID ) : ?>

					<p class="form-field">Logged in <span><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">As <?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></span></p>

				<?php else : ?>

					<p class="form-field"><label for="author">Name</label>
						<span><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
						<small><?php if ($req) echo "required"; ?></small></span>
					</p>

					<p class="form-field"><label for="email">Mail</label>
						<span><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
						<small><?php if ($req) echo "required, will not be published"; ?></small></span>
					</p>

					<p class="form-field"><label for="url">Website</label>
					<span><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></span></p>

				<?php endif; ?>

				<p class="form-field comment-box">Your comment
				<span><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></span></p>

				 <!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p></-->

				<p class="form-field submit-button">Submit<span><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" /><?php comment_id_fields(); ?></span></p>

				<?php do_action('comment_form', $post->ID); ?>

			</form>

		<?php endif; // If registration required and not logged in ?>

	</div> <!-- end of respond wrapper -->

<?php endif; // if you delete this the sky will fall on your head ?>

</div> <!-- end of comment-wrapper -->