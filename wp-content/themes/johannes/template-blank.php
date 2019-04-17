<?php
/**
 * Template Name: Blank
 */
get_header(); ?>

<?php if( have_posts() ) : ?>
    <?php while( have_posts() ) : the_post(); ?>
    	
	<div class="johannes-section">
	    <div class="container">
	        <div class="section-content row justify-content-center">

	            <div class="col-12">
	                <article <?php post_class(); ?>>
	                	<?php if ( johannes_get( 'display', 'title' ) ): ?>
	                        <div class="entry-header">
	                            <?php the_title( '<h1 class="entry-title h1">', '</h1>' ); ?>
	                        </div>
	                	<?php endif; ?>
	                    <div class="entry-content">
	                        <?php the_content(); ?>
	                    </div>
	                </article>
	            </div>

	        </div>
	    </div>
	</div>

	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>