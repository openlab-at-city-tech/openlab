<?php get_header(); ?>

<div id="main">

	<div id="content">

		<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( function_exists( 'wp_list_comments' ) ) : ?>
		<div <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
		<?php else : ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<?php endif; ?>

			<div class="posttop">
				<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetatop">
					<div class="categs">By <?php the_author() ?> &mdash; <?php comments_popup_link( __( 'Leave a comment' ), __( '1 Comment' ), __( '% Comments' ) ) ?></div>
					<div class="date"><span><?php the_time( get_option( 'date_format' ) ); ?></span></div>
				</div>
			</div>

			<div class="postcontent">
				<p class="attachment"><?php echo wp_get_attachment_image( $post->ID, 'auto' ); ?></p>
				<?php the_content( 'Read more &raquo;' ); ?>
				<div id="navigation" class="image">
					<div class="alignleft"><?php previous_image_link() ?></div>
					<div class="alignright"><?php next_image_link() ?></div>
				</div>
				<div class="linkpages"><?php wp_link_pages( 'before=<p><span>Pages:</span>&link_before=<span>&link_after=</span>' ); ?></div>
			</div>
			<small><?php edit_post_link( 'Admin: Edit this entry' , '' , '' ); ?></small>

			<div class="postmetabottom">
				<div class="readmore"><?php post_comments_feed_link(__( 'Comments <abbr title="Really Simple Syndication">RSS</abbr> feed' )); ?></div>
			</div>

		</div><!-- /post -->

		<div id="comments">
		<?php comments_template( '', true ); ?>
		</div><!-- /comments -->

		<?php endwhile; ?>

		<?php else : ?>
		<div class="post">
			<div class="posttop">
				<h2 class="posttitle"><a href="#">Oops!</a></h2>
			</div>
			<div class="postcontent">
				<p>What you are looking for doesn't seem to be on this page...</p>
			</div>
		</div><!-- /post -->
		<?php endif; ?>

	</div><!-- /content -->

	<?php get_sidebar(); ?>

</div><!-- /main -->

<?php get_footer(); ?>