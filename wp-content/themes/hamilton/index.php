<?php get_header(); ?>

<div class="section-inner">

	<?php 

	$archive_title_elem 	= is_front_page() || ( is_home() && get_option( 'show_on_front' ) == 'posts' ) ? 'h2' : 'h1';
	$archive_title 			= get_the_archive_title();
	$archive_description 	= get_the_archive_description();

	if ( $archive_title || $archive_description ) : ?>

		<header class="page-header fade-block">
			<div>

				<?php if ( $archive_title ) : ?>
					<<?php echo $archive_title_elem; ?> class="title"><?php echo wp_kses_post( $archive_title ); ?></<?php echo $archive_title_elem; ?>>
				<?php endif; ?>

				<?php if ( $archive_description ) : ?>
					<div class="archive-description"><?php echo wpautop( $archive_description ); ?></div>
				<?php endif; ?>

				<?php if ( is_search() && ! have_posts() ) get_search_form(); ?>

			</div>
		</header><!-- .page-header -->

	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<div class="posts" id="posts">

			<?php 
			while ( have_posts() ) : the_post();

				get_template_part( 'content' );

			endwhile; 
			?>

		</div><!-- .posts -->
	
	<?php endif; ?>

</div><!-- .section-inner -->

<?php 

get_template_part( 'pagination' ); 

get_footer(); ?>