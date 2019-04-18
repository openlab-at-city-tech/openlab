<?php get_header(); ?>

<?php $sections = johannes_get( 'front_page_sections' ); ?>

<?php if ( !empty( $sections ) ): ?>
	<?php foreach ( $sections as $section ): ?>
		<?php get_template_part( 'template-parts/front-page/' . $section ); ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php get_footer(); ?>