<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

get_header(); ?>

<div id="content-container">
	<div id="content" role="main">

		<div id="post-0" class="post error404 not-found">
			<h1 class="entry-title"><?php _e( 'Not Found', 'pilcrow' ); ?></h1>
			<div class="entry entry-content">
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'pilcrow' ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .entry-content -->
		</div><!-- #post-0 -->

	</div><!-- #content -->
</div><!-- #container -->

<?php get_footer();
