<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: CommentsBrowser
*/

global $current_browser_section, $wp, $blog_id ;
?>

<?php get_header(); ?>

<div id="container">


	<div id="content">
		<div id="mainpage"  class="comment-browser">
			<?php 

			$commentbrowser_function = "commentbrowser_" . str_replace('-','_',$wp->query_vars['commentbrowser_function']);
			$commentbrowser_params =  $wp->query_vars['commentbrowser_params'];
		
			//var_dump($wp->query_vars);
		
		
			if(has_action('add_commentbrowser', $commentbrowser_function) && function_exists($commentbrowser_function)){
			
				$comment_list = call_user_func($commentbrowser_function, $commentbrowser_params);

			}
		
			?>		
		
	
		
	
			<div class="commentlist">			
			<?php if(count($comment_list)): ?>
			<?php foreach($comment_list as $comment): ?>

	
			<div <?php comment_class($classes); ?> id="comment-<?php echo (int)$blog_id ?>-<?php echo $comment->comment_ID; ?>">
				<div class="bubblearrow"></div>
				<div id="div-comment-<?php echo (int)$comment->blog_id; ?>-<?php echo $comment->comment_ID;; ?>" class="comment-body">
			
					<div class="comment-header">
				
						<div class="comment-author vcard">


							<?php echo get_avatar( $comment, 15 ); ?>


							<?php

							if($comment->user_id){
								$comment_user = get_userdata($comment->user_id); 
								$profile_url = get_bloginfo('url')."/comments-by-contributor/" . $comment_user->user_login;
								echo "<a href='$profile_url'>$comment_user->display_name</a>";
							}
							else{
								$profile_url = get_bloginfo('url')."/comments-by-contributor/" . $comment->comment_author;						
								echo "<a href='$profile_url'>$comment->comment_author</a>";						
							}
							?>
					

						</div>
				
						<div class="comment-meta">
					
							<span class="comment-id" title="<?php echo $comment->comment_ID; ?>"></span>
							<span class="comment-parent" title="<?php echo $comment->comment_parent; ?>"></span>
							<span class="comment-paragraph-number" title="<?php echo $comment->comment_text_signature; ?>"></span>


							<span class="comment-date"><?php comment_date('n/j/Y'); ?></span>
					

					
							<div class="comment-goto">
								<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo (int)$blog_id ?>-<?php echo $comment->comment_ID; ?>">Go to thread</a>
							</div>

					
							<?php do_action('digressit_custom_meta_data'); ?>

										
						</div>
					</div>
					<div class="comment-text">

						<?php 
						if ($comment->comment_approved == '0'): ?>
							<p><i>This comment is awaiting moderation.</i></p><?php
						else:
							echo $comment->comment_content; 
						endif;

						?>						

					</div>
				</div>
			</div>

		
			<?php endforeach; ?>
			<?php else: ?>
				<div class="comment">
					<div class="bubblearrow"></div>				
					<div class="no-comment-browser">
						<?php
					
						if(isset($commentbrowser_params)){
							?>No comments<?php
						}
						else{
							?>This page contains a running transcript of all conversations taking place in <?php bloginfo('name') ?>  
								organized by section. Click through the menu on the left to view the comments in each section of the document. 
								Click “Go to thread” to see the comment in context.<?php						
						}
					
						?>
					</div>
				</div>
			<?php endif; ?>
			</div>

		</div>
		<div class="clear"></div>
	</div>


</div>
<?php get_footer(); ?>

