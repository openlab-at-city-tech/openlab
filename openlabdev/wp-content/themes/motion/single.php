<?php

get_header(); ?>

<div id="main">

	<div id="content">

		<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		

			<div class="posttop">
			  <?php if (!(the_title() == "")){ ?>
				  <h2 class="posttitle"><?php the_title(); ?></h2>
				<?php } ?>
				<div class="postmetatop">
					<div class="categs">Filed Under: <?php the_category( ', ' ); ?> by <?php the_author() ?> &mdash; <?php comments_popup_link( __( 'Leave a comment' ), __( '1 Comment' ), __( '% Comments' ) ) ?></div>
					<div class="date"><span><?php the_time( get_option( 'date_format' ) ); ?></span></div>
				</div>
			</div>

			<div class="postcontent">
				<?php the_content( 'Read more &raquo;' ); ?>
				<div class="linkpages"><?php wp_link_pages( 'before=<p><span>Pages:</span>&link_before=<span>&link_after=</span>' ); ?></div>
				<p>
				<span class="alignleft"><?php previous_post_link(); ?></span>
				<span class="alignright"><?php next_post_link(); ?></span>
				</p>
				<div class="clear"></div>
			</div>
			<small><?php edit_post_link( 'Edit Page' , '' , ' : ' ); ?></small>
			<small class="permalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Permanent Link</a></small>

			<div class="postmetabottom">
				<div class="tags"><?php the_tags( 'Tags: ', ', ', '' ); ?></div>
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

		<?php /* Uncomment this to enable single post pagination
		<div id="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div><!-- /navigation -->
		*/ ?>

	</div><!-- /content -->

	<?php get_sidebar(); ?>

</div><!-- /main -->

<?php get_footer(); ?>