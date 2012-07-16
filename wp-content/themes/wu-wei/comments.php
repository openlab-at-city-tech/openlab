<?php

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>

		<div id="comment-wrapper">

			<h3 id="comments" id="comment-<?php comment_ID(); ?>"><?php _e( 'Enter the password to view comments.', 'wu-wei' ); ?></h3>

		</div>

	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<div id="comment-wrapper">
	
	<h3 id="comments">
		<span class="comment-number"><?php comments_number( __( 'No comments', 'wu-wei' ), __( '1 comment', 'wu-wei' ), __( '% comments', 'wu-wei' ) ); ?></span>
		<span class="comment-message">
			<?php if ( comments_open() && pings_open() ) {
				printf( __( '<a href="#respond">Post your own</a> or leave a trackback: <a href="%1$s">Trackback URL</a>', 'wu-wei' ), get_trackback_url() );
			} elseif ( comments_open() && ! pings_open() ) {
				_e( '<a href="#respond">Post your own</a>', 'wu-wei' );
			} elseif ( ! comments_open() && pings_open() ) {
				printf( __( 'Leave a trackback: <a href="%1$s">Trackback URL</a>', 'wu-wei' ), get_trackback_url() );
			} elseif ( ! comments_open() && ! pings_open() ) {
				_e( '<em>Comments are closed</em>.', 'wu-wei' );
			} ?>
		</span>
	</h3>

	<?php if ( have_comments() ) : ?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
			<div class="clearboth"> </div>
		</div>
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist snap_preview">
		<?php wp_list_comments( 'avatar_size=48' ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
			<div class="clearboth"> </div>
		</div>
		<?php endif; // check for comment navigation ?>

	<?php endif; ?>


	<?php if ('open' == $post->comment_status) : ?>
		
	<?php comment_form(); ?>

<?php endif; // if you delete this the sky will fall on your head ?>

</div> <!-- end of comment-wrapper -->