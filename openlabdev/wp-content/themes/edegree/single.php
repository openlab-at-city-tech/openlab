<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<p class="entry-date"><?php the_time('M') ?><br /><span class="date"><?php the_time('j')?></span></p>
			<div class="entry_header">
                <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a> <?php edit_post_link('Edit', '<span class="editpost">', '</span>'); ?></h1>
                <?php if ($post->comment_status != 'closed'):?>
                    <div class="comment-bubble"><?php comments_popup_link('<span class="nocomment">Leave a comment &#187;</span>', '1 Comment', '% Comments'); ?></div>
                <?php endif;?>
                <div class="recover"></div>
            </div>
			
			<div class="entry">
				<?php 
				the_content();
				wp_link_pages();
				?>

				<?php the_tags( '<p class="tags">Tags: ', ', ', '</p>'); ?>

			<p class="postmetacat"><span class="folder-icon"><?php _e('Posted in')?></span> <span class="categories"><?php the_category(' ') ?></span><br />
            <span class="comment-icon"><?php comments_popup_link('No Comments Yet', '1 Comment', '% Comments')?></span> <?php _e('Posted by')?> <span class="usr-meta"><?php the_author() ?></span> <?php if (isset($options['tags'])) : ?><span class="tags"><?php the_tags('', ', ', ''); ?></span><?php endif; ?></p>
			</div>
		</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.')?></p>

<?php endif; ?>

<?php get_footer(); ?>
