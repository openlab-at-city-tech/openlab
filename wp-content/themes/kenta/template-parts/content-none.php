<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Kenta
 */
?>
<section class="kenta-max-w-content has-global-padding mx-auto mb-60">
    <header class="text-center mt-10 mb-half-gutter">
        <h1 class="text-3xl font-bold text-accent-active">
			<?php
			if ( is_404() ) {
				esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kenta' );
			} else {
				esc_html_e( 'Nothing Found', 'kenta' );
			}
			?>
        </h1>
    </header>

    <div class="text-center form-controls form-default form-primary">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
            <!-- For Home Page -->
            <p class="mb-gutter">
				<?php
				printf( wp_kses(
				/*translators: %1$s: the url of 'post-new.php'*/
					__( 'Ready to publish your first post? <a class="link" href="%1$s">Get started here</a>.', 'kenta' ),
					array( 'a' => array( 'href' => array(), 'class' => array() ) ) ),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
            </p>
		<?php elseif ( is_search() ) : ?>
            <!-- For Search Result -->
            <p class="mb-gutter text-accent leading-normal">
				<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'kenta' ); ?>
            </p>
            <div class="kenta-max-w-content mx-auto kenta-no-result-search-form kenta-form">
				<?php get_search_form(); ?>
            </div>
		<?php else : ?>
            <!-- For Archive Page -->
            <p class="mb-gutter text-accent">
				<?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'kenta' ); ?>
            </p>
            <div class="kenta-max-w-content mx-auto kenta-no-result-search-form kenta-form">
				<?php get_search_form(); ?>
            </div>
		<?php endif; ?>
    </div>
</section>
