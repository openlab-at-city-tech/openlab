<ul class="site-archive-posts">

	<?php 
	$i = 0; 
	while (have_posts()) : the_post(); 
	$i++;

	$classes = array('site-archive-post'); 
	
	if ( !has_post_thumbnail() ) {
		$classes[] = 'post-nothumbnail';
	} else {
		$classes[] = 'has-post-thumbnail';
	}

	?><li <?php post_class($classes); ?>>

		<div class="site-column-widget-wrapper">
			<?php if ( has_post_thumbnail() ) { ?>
			<div class="entry-thumbnail">
				<div class="entry-thumbnail-wrapper">
					<?php 
					echo '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
					the_post_thumbnail('post-thumbnail');
					echo '</a>';
					?>
				</div><!-- .entry-thumbnail-wrapper -->
			</div><!-- .entry-thumbnail --><?php } ?><!-- ws fix
			--><div class="entry-preview">
				<div class="entry-preview-wrapper">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'bradbury' ), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h2>
					<p class="entry-excerpt"><?php echo get_the_excerpt(); ?></p>
					<?php echo academiathemes_helper_display_datetime($post); ?>
				</div><!-- .entry-preview-wrapper -->
			</div><!-- .entry-preview -->
		</div><!-- .site-column-widget-wrapper -->

	</li><!-- .site-archive-post --><?php endwhile; ?>
	
</ul><!-- .site-archive-posts -->
<hr />

<?php
the_posts_pagination();
?>