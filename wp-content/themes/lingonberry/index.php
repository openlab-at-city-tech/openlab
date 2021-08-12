<?php get_header(); ?>

<main id="site-content" class="content section-inner">
	
	<div class="posts">

		<?php
	
		$archive_title 			= get_the_archive_title();
		$archive_description 	= get_the_archive_description();

		if ( $archive_title || $archive_description ) : ?>

			<header class="archive-header contain-margins">

				<?php if ( $archive_title ) : ?>
					<h1 class="archive-title"><?php echo wp_kses_post( $archive_title ); ?></h1>
				<?php endif; ?>

				<?php if ( $archive_description ) : ?>
					<div class="archive-description contain-margins"><?php echo wpautop( wp_kses_post( $archive_description ) ); ?></div>
				<?php endif; ?>
				
			</header><!-- .archive-header -->

			<?php 
		endif;

		if ( have_posts() ) :
			
			while ( have_posts() ) {
				the_post(); 
				get_template_part( 'content', get_post_format() ); 
			}

			get_template_part( 'pagination' ); 

		elseif ( is_search() ) : 
			?>

			<div class="post-bubbles">
				<div class="format-bubble"></div>
			</div>
		
			<div class="content-inner">
				<div class="post-content">
					<p><?php _e( 'No results. Try again, would you kindly?', 'lingonberry' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .post-content -->
			</div><!-- .content-inner -->

			<?php
		endif;
		?>

	</div><!-- .posts -->
		
</main><!-- #site-content -->

<?php get_footer(); ?>