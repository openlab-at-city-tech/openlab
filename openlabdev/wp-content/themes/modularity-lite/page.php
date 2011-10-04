<?php
/**
 * @package WordPress
 * @subpackage Modularity
 */
?>
<?php get_header(); ?>
<div class="span-<?php modularity_sidebar_class(); ?>">
<div class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<?php the_content( __( 'Read the rest of this page &raquo;', 'modularity' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'modularity' ), 'after' => '</div>' ) ); ?>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link( __( 'Edit', 'modularity' ), '<p>', '</p>'); ?>

		<?php if ( comments_open() ) comments_template(); ?>

	</div>
	</div>
	<?php get_sidebar(); ?>
<!-- Begin Footer -->
<?php get_footer(); ?>