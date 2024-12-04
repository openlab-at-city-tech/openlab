<?php
/**
 * Template part for displaying front page introduction.
 *
 * @package Flawless Blog
 */

// Breaking Articles Section.
$flash_articles_section = get_theme_mod( 'flawless_blog_flash_articles_section_enable', false );

if ( false === $flash_articles_section ) {
	return;
}

$content_ids = array();

$flash_articles_content_type = get_theme_mod( 'flawless_blog_flash_articles_content_type', 'post' );

if ( $flash_articles_content_type === 'post' ) {

	for ( $i = 1; $i <= 5; $i++ ) {
		$content_ids[] = get_theme_mod( 'flawless_blog_flash_articles_post_' . $i );
	}

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => absint( 5 ),
		'ignore_sticky_posts' => true,
	);
	if ( ! empty( array_filter( $content_ids ) ) ) {
		$args['post__in'] = array_filter( $content_ids );
		$args['orderby']  = 'post__in';
	} else {
		$args['orderby'] = 'date';
	}

} else {
	$cat_content_id = get_theme_mod( 'flawless_blog_flash_articles_category' );
	$args           = array(
		'cat'            => $cat_content_id,
		'posts_per_page' => absint( 5 ),
	);
}

$query = new WP_Query( $args );
if ( $query->have_posts() ) {

	$section_title = get_theme_mod( 'flawless_blog_flash_articles_title', __( 'New', 'flawless-blog' ) );
	?>

	<div id="flawless_blog_flash_articles_section" class="news-ticker-section">
		<div class="theme-wrapper">
			<div class="news-ticker-section-wrapper">
				<?php if ( !empty( $section_title ) ): ?>
					<div class="news-ticker-label">
						<span class="news-ticker-title"><?php echo esc_html( $section_title ); ?></span>
					</div>
				<?php endif; ?>
				<div class="marquee-part" dir="ltr">
					<div class="marquee news-ticker">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="newsticker-outer">
								<?php if ( has_post_thumbnail() ): ?>
									<span class="newsticker-image">
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									</span>
								<?php endif; ?>
								<span class="newsticker-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</span>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}
