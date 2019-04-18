<?php if ( get_the_content() ) : ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="post-content"><?php the_content(); ?></div>
	</article>
<?php endif; ?>