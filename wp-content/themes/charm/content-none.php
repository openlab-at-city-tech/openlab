<section id="post-0">
	<div class="post-content">

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p>Ready to publish your first post? <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>">Get started here</a>.</p>

		<?php elseif ( is_page_template( 'template-portfolio.php' ) && current_user_can( 'publish_posts' ) ) : ?>

			<p>Ready to publish your first project? <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=project' ) ); ?>">Get started here</a>.</p>

		<?php elseif ( is_search() ) : ?>

			<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'themerain' ); ?></p>
			<?php get_search_form(); ?>

		<?php elseif ( is_404() ) : ?>

			<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'themerain' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( 'It seems we can not find what you are looking for. Perhaps searching can help.', 'themerain' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	</div>
</section>