<?php get_header(); ?>

<div class="section-inner">

	<header class="page-header section-inner thin">

		<div>

			<h1 class="title"><?php _e( 'Error 404', 'hamilton' ); ?></h1>

			<p><?php _e( "The page you're looking for could not be found. It may have been removed, renamed, or maybe it didn't exist in the first place.", "hamilton" ); ?></p>

			<div class="meta">
			
				<a href="<?php echo esc_url( home_url() ); ?>"><?php _e( 'To the front page', 'hamilton' ); ?></a>
			
			</div>

		</div>

	</header><!-- .page-header -->

</div> <!-- .post -->

<?php get_footer(); ?>