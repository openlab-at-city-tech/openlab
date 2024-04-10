<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Sydney
 */
?>
<?php $enable_featured 	= get_post_meta( $post->ID, '_sydney_page_enable_featured', true ); ?>
<?php $enable_featured_all 	= get_theme_mod( 'enable_page_feat_images', 0 ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="content-inner">
		<header class="entry-header">
			<?php the_title( '<h1 class="title-post entry-title" ' . sydney_get_schema( "headline" ) . '>', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<?php if ( has_post_thumbnail() && ( $enable_featured || $enable_featured_all ) ) : ?>
		<div class="entry-thumb">
			<?php the_post_thumbnail('large-thumb'); ?>
		</div>
		<?php endif; ?>	

		<div class="entry-content" <?php sydney_do_schema( 'entry_content' ); ?>>
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'sydney' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php edit_post_link( __( 'Edit', 'sydney' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->
	</div>
</article><!-- #post-## -->
