<?php get_header(); ?>
<?php $cover_class = !typology_get_archive_option( 'cover' ) ? 'typology-cover-empty' : ''; ?>
<div id="typology-cover" class="typology-cover <?php echo esc_attr( $cover_class ); ?>">
	<?php get_template_part( 'template-parts/cover/cover-archive' ); ?>
	<?php if ( typology_get_option( 'scroll_down_arrow' ) ): ?>
        <a href="javascript:void(0)" class="typology-scroll-down-arrow"><i class="fa fa-angle-down"></i></a>
	<?php endif; ?>
</div>

<div class="typology-fake-bg">
	<div class="typology-section">
		<?php get_template_part( 'template-parts/ads/top' ); ?>

		<?php if ( !typology_get_archive_option( 'cover' ) ) : ?>
			<?php $heading = typology_get_archive_heading(); ?>
			<?php typology_section_heading( array( 'title' => $heading['title'],  'pre' => $heading['pre'], 'element' => 'h1' , 'avatar' => $heading['avatar'], 'desc' => $heading['desc'] ) ); ?>
		<?php endif; ?>

		<?php $archive_layout = typology_get_archive_option( 'layout' ); ?>

		<?php if ( have_posts() ) : ?>

			<div class="section-content section-content-<?php echo esc_attr( $archive_layout ); ?>">

				<div class="typology-posts">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php $ad_class = typology_has_ad_between( $wp_query->current_post )  ? 'typology-has-ad' : ''; ?>

                        <?php include locate_template( 'template-parts/layouts/content-'. $archive_layout . '.php' ); ?>

						<?php if ( typology_has_ad_between( $wp_query->current_post ) ): ?>
							<?php include locate_template( 'template-parts/ads/between-posts.php' ); ?>
						<?php endif; ?>

					<?php endwhile; ?>

				</div>

				<?php get_template_part( 'template-parts/pagination/'. typology_get_archive_option( 'pagination' ) ); ?>

			</div>

		<?php else: ?>

			<?php get_template_part( 'template-parts/layouts/content-none' ); ?>

		<?php endif; ?>

		<?php get_template_part( 'template-parts/ads/bottom' ); ?>
	</div>

<?php get_footer(); ?>
