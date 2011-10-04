<?php get_header(); ?>

<div id="main">
	<div id="content" class="full">

		<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
      
      <?php if (!(the_title() == "")){ ?>
        <div class="posttop">
  				<h2 class="posttitle"><?php the_title(); ?></h2>
  			</div>
      <?php } ?>

			<div class="postcontent">
				<?php the_content( 'Read more &raquo;' ); ?>
				<div class="linkpages"><?php wp_link_pages( 'link_before=<span>&link_after=</span>' ); ?></div>
			</div>
			<small><?php edit_post_link( 'Edit Page' , '' , ' : ' ); ?></small>
			<small class="permalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Permanent Link</a></small>
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

</div><!-- /main -->

<?php get_footer(); ?>