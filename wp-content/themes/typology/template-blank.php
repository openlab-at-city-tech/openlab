<?php
/**
 * Template Name: Blank
 */
?>

<?php get_header(); ?>

<?php if( have_posts() ): ?>
    
    <?php while( have_posts() ) : the_post(); ?>
        
        <div id="typology-cover" class="typology-cover typology-cover-empty"></div>
  
        <div class="typology-fake-bg">

            <div class="typology-section">
                
                <div class="section-content section-content-page">
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'typology-post typology-single-post' ); ?>>
                    
                        <div class="entry-content clearfix">
                            <?php the_content(); ?>
                        </div>
                    
                    </article>
                
                </div>
                
            </div>
    
    <?php endwhile; ?>

<?php endif; ?>

<?php get_footer(); ?>