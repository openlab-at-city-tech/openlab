<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Koyel
 */
 ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( 'post' === get_post_type() ) : ?>
		<?php if(! is_single()): ?>
		<div class="entry-meta">
			<?php koyel_blog_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; 
		 if (is_singular()){
			koyel_blog_single_cat(); 
		}
	endif; ?>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="single-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif; ?>
	</header><!-- .entry-header -->
	<?php if (is_single()) : ?>
		<div class="single-entry-meta">
			<?php koyel_blog_posted_by(); ?><span class="line">/</span><?php  koyel_blog_posted_on(); ?><span class="line">/</span><?php koyel_blog_single_comment(); ?>
		</div>
		<?php if ( has_post_thumbnail () ): ?>
		<div class="single-area-img">
			<?php koyel_post_thumbnail(); ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="entry-content <?php if ( ! has_post_thumbnail () && is_single() ): ?>padding-top<?php endif; ?>">
		<?php

		if(is_single( )){
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'koyel-blog' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
		}else{
			the_excerpt();
		}
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'koyel-blog' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	<?php if ( ! is_single()) : ?>
	<div class="single-img">
		<?php if ( has_post_thumbnail () ):
            koyel_post_thumbnail(); 
        else : ?>
        <img src="<?php echo esc_url (get_stylesheet_directory_uri() . '/assets/img/01.jpg' ); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
	</div>
	<footer class="entry-footer">
		<?php echo'<a href="'.esc_url ( get_the_permalink( $post->ID ) ).'" class="more-btn">'.esc_html__('Read More','koyel-blog').'<i class="fa fa-angle-right" aria-hidden="true"></i></a>'; ?>
	</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->