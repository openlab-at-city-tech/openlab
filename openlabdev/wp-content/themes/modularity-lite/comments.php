<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments"><?php _e( 'This post is password protected. Enter the password to view comments.', 'modularity' ); ?></p>

			<?php
			return;
		}
	}

?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
	<h3 id="comments"><?php comments_number( __( 'Leave a comment', 'modularity' ), __( 'One Response', 'modularity' ), __( '% Responses', 'modularity' ) );?></h3>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<div id="comment-nav-above" class="nav-interior comments-nav">
		<div class="prev"><?php previous_comments_link( __( '&larr; Older Comments', 'modularity' ) ); ?></div>
		<div class="next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'modularity' ) ); ?></div>
	</div>
	<div class="clear"></div>	
	<?php endif; // check for comment navigation ?>

	<ol class="commentlist snap_preview">
		<?php wp_list_comments( array( 'callback' => 'modularity_comment' ) ); ?>
	</ol>
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<div id="comment-nav-below" class="nav-interior comments-nav">
		<div class="prev"><?php previous_comments_link( __( '&larr; Older Comments', 'modularity' ) ); ?></div>
		<div class="next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'modularity' ) ); ?></div>
	</div>
	<div class="clear"></div>	
	<?php endif; // check for comment navigation ?>	

 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e( 'Comments are closed.', 'modularity' ); ?></p>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<?php comment_form(); ?>

<?php endif; // if you delete this the sky will fall on your head ?>
