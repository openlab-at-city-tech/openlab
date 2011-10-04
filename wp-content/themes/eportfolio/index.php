<?php get_header() ?>


<?php if ( have_posts() ) : ?>

	<header class="page-header">
		<h2 class="page-title"><?php 
			if (is_search()) echo 'Search results for "'.$s.'"';
			else echo single_cat_title( '', false );
		?></h2>

		<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) )
				echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
		?>
	</header>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<a href="<?php the_permalink() ?>"><img src="<?php echo ahs_getimg($post->ID,array(150,150)) ?>" height="150" width="150" class="alignleft" /></a>
			<div class="alignright">
				<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
				<div class="entry-content">
					<?php the_excerpt(); ?>
				</div><!-- .entry-content -->
			</div>
			<div class="clr"></div>
		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>
	
	<?php ahstheme_content_nav('below') ?>

<?php endif; ?>
			
			
<?php get_footer() ?>