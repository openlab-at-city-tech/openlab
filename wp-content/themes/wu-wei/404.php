<?php get_header(); ?>

		<div class="post">

			<div class="post-info">

				<h1><?php _e( '404 Not Found', 'wu-wei' ); ?></h1>

			</div>

			<div class="post-content">
				<p><?php _e( 'Oops! This page does not exist. Maybe you can try searching for it again.', 'wu-wei' ); ?></p>

				<?php get_search_form(); ?>
			</div>

			<div class="clearboth"><!-- --></div>

		</div>

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
