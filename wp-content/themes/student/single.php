<?php get_header() ?>
<?php the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h2 class="entry-title"><?php the_title(); ?></h2>
	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->
	
	<div class="entry-meta">This entry was posted on <a href="<?php the_permalink() ?>"><?php the_time('F j, Y') ?></a><?php the_tags('; Tagged ', ', '); ?></div>
</article><!-- #post-<?php the_ID(); ?> -->
<?php get_footer() ?>