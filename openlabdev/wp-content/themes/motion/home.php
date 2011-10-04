<?php get_header(); ?>

<div id="main">

	<div id="content">

		<h2 id="contentdesc">Latest <span>Entries</span> &raquo;</h2>

		<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( function_exists( 'wp_list_comments' ) ) : ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<?php else : ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<?php endif; ?>

			<div class="posttop">
				<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetatop">
					<div class="categs">Filed Under: <?php the_category( ', ' ); ?> by <?php the_author() ?> &mdash; <?php comments_popup_link( __( 'Leave a comment' ), __( '1 Comment' ), __( '% Comments' ) ) ?></div>
					<div class="date"><span><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span></div>
				</div>
			</div>

			<div class="postcontent">
				<?php the_content( 'View full article &raquo;' ); ?>
			</div>

			<div class="postmetabottom">
				<div class="tags"><?php the_tags( 'Tags: ', ', ', '' ); ?></div>
				<div class="readmore">
					<span>
						<?php

						$moretag = strpos($post->post_content, '<!--more');
						$postpaged = strpos($post->post_content, '<!--nextpage');
						$next= '';

						if (!$moretag && !$postpaged)
							$full = true;
						else {
							$full = false;						
							if (!$moretag)
								$next = '2/';
							else
								$next = '#more-'.$id;
						}

						if( $full == true && $post->comment_status == 'open' ) { ?>
							<a href="<?php the_permalink() ?>#comments" title="<?php printf(__('Comment on %s'), the_title_attribute()); ?>"><?php _e('Comment', 'motion_theme'); ?> </a>
						<?php } elseif(!$full && $post->comment_status == 'open') { ?>
							<a href="<?php the_permalink(); echo $next; ?>" title="<?php printf(__('Continue reading %s and comment'), the_title_attribute()); ?>"><?php _e('Read&nbsp;More&nbsp;&amp;&nbsp;Comment', 'motion_theme'); ?></a>
						<?php } elseif(!$full && $post->comment_status == 'closed') { ?>
							<a href="<?php the_permalink(); echo $next; ?>" title="<?php _e('Continue reading', 'motion_theme'); the_title_attribute(); ?>"><?php _e('Read&nbsp;More', 'motion_theme'); ?></a>
						<?php } else { ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'motion_theme'), the_title_attribute()); ?>"><?php _e('Permalink', 'motion_theme'); ?> </a>
						<?php } ?>
					</span>
				</div>			
			</div>

		</div><!-- /post -->

		<?php endwhile; ?>

		<?php else : ?>

		<div class="post">
			<div class="posttop">
				<h2 class="posttitle"><a href="#">Oops!</a></h2>
			</div>
			<div class="postcontent">
				<p>What you are looking for doesn't seem to be on this page...</p>
			</div>
		</div>
		<?php endif; ?>

		<div id="navigation">
			<?php if ( function_exists( 'wp_pagenavi' ) ) : ?>
			<?php wp_pagenavi(); ?>
			<?php else : ?>
				<div class="alignleft"><?php next_posts_link( '&laquo; Older Entries' ); ?></div>
				<div class="alignright"><?php previous_posts_link( 'Newer Entries &raquo;' ); ?></div>
			<?php endif; ?>
		</div><!-- /navigation -->

	</div><!-- /content -->

	<?php get_sidebar(); ?>

</div><!-- /main -->

<?php get_footer(); ?>