<?php get_header(); ?>

<main id="site-content" role="main">

	<header class="single-container bg-color-white">

		<div class="post-inner section-inner">

			<h1><?php _e( 'Error 404', 'koji' ); ?></h1>

			<p class="sans-excerpt"><?php _e( "The page you're looking for could not be found. It may have been removed, renamed, or maybe it didn't exist in the first place. You can return to the home page through the link.", 'koji' ); ?></p>

			<a class="go-home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'To the home page', 'koji' ); ?> &rarr;</a>

		</div><!-- .post-inner -->

	</header><!-- .page-header -->

</main>

<?php get_footer(); ?>
