<?php

get_header(); ?>

<div id="main">

	<div id="content">

		<?php if ( have_posts() ) : ?>

		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

		<?php /* If this is a category archive */ if ( is_category() ) { ?>
		<h2 id="contentdesc">Category: <span><?php single_cat_title(); ?></span></h2>
		<?php /* If this is a tag archive */ } elseif (  is_tag() ) { ?>
		<h2 id="contentdesc">Tag Archive: <span><?php single_tag_title(); ?></span></h2>
		<?php /* If this is a daily archive */ } elseif ( is_day() ) { ?>
		<h2 id="contentdesc">Archive for <span><?php the_time( 'F jS, Y' ); ?></span></h2>
		<?php /* If this is a monthly archive */ } elseif ( is_month() ) { ?>
		<h2 id="contentdesc">Archive for <span><?php the_time( 'F, Y' ); ?></span></h2>
		<?php /* If this is a yearly archive */ } elseif ( is_year() ) { ?>
		<h2 id="contentdesc">Archive for <span><?php the_time( 'Y' ); ?></span></h2>
		<?php /* If this is an author archive */ } elseif ( is_author() ) { ?>
		<h2 id="contentdesc">Author Archive</h2>
		<?php /* If this is a paged archive */ } elseif ( isset( $_GET['paged'] ) && !empty( $_GET['paged'] ) ) { ?>
		<h2 id="contentdesc">Blog Archives</h2>
		<?php } ?><br/>

		<?php while ( have_posts() ) : the_post(); ?>


		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

			<div class="posttop">
				<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetatop">
					<div class="categs">Filed Under: <?php the_category( ', ' ); ?> by <?php the_author() ?> &mdash; <?php comments_popup_link( __( 'Leave a comment', 'motion_theme' ), __( '1 Comment', 'motion_theme' ), __( '% Comments', 'motion_theme' ) ) ?></div>
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

						if( $full == true && comments_open($post->ID)) { ?>
							<a href="<?php the_permalink() ?>#comments" title="<?php printf(__('Comment on %s'), the_title_attribute()); ?>"><?php _e('Comment', 'motion_theme'); ?> </a>
						<?php } elseif(!$full && comments_open($post->ID)) { ?>
							<a href="<?php the_permalink(); echo $next; ?>" title="<?php printf(__('Continue reading %s and comment'), the_title_attribute()); ?>"><?php _e('Read&nbsp;More&nbsp;&amp;&nbsp;Comment' , 'motion_theme' ); ?></a>
						<?php } elseif(!$full && (!(comments_open($post->ID)))) { ?>
							<a href="<?php the_permalink(); echo $next; ?>" title="<?php _e('Continue reading' , 'motion_theme'); the_title_attribute(); ?>"><?php _e('Read&nbsp;More' , 'motion_theme'); ?></a>
						<?php } else { ?>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s'), the_title_attribute()); ?>"><?php _e('Permalink', 'motion_theme'); ?> </a>
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
		</div><!-- /post -->

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