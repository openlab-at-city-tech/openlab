<?php
/**
 * Template Name: Authors
 */ 
?>

<?php get_header(); ?>

	<?php if( have_posts() ): ?>
		
		<?php while( have_posts() ) : the_post(); ?>
		
		<?php $meta = typology_get_page_meta(); ?>
        
        <?php $cover_class = !absint($meta['cover']) ? 'typology-cover-empty' : ''; ?>
        
        <div id="typology-cover" class="typology-cover <?php echo esc_attr($cover_class); ?>">
            
            <?php if(absint($meta['cover'])): ?>
	            
                <?php get_template_part('template-parts/cover/cover-page'); ?>
	            
                <?php if(typology_get_option( 'scroll_down_arrow' )): ?>
                    <a href="javascript:void(0)" class="typology-scroll-down-arrow"><i class="fa fa-angle-down"></i></a>
	            <?php endif; ?>

            <?php endif; ?>

        </div>
        
        <div class="typology-fake-bg">
            
            <div class="typology-section">
                
                <?php get_template_part('template-parts/ads/top'); ?>

                <div class="section-content section-content-page">

                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'typology-post typology-single-post' ); ?>>
                        
                        <?php if(!absint($meta['cover']) ) : ?>

                            <header class="entry-header">
                                <?php the_title( '<h1 class="entry-title entry-title-cover-empty">', '</h1>' ); ?>
                                <?php if( typology_get_option( 'page_dropcap' ) ) : ?>
                                        <div class="post-letter"><?php echo typology_get_letter(); ?></div>
                                <?php endif; ?>
                            </header>

                        <?php endif; ?>

                        <div class="entry-content clearfix">

                            <?php if( $meta['fimg'] == 'content' && has_post_thumbnail() ) : ?>
                                <div class="typology-featured-image">
                                    <?php the_post_thumbnail('typology-a'); ?>
                                </div>
                            <?php endif; ?>

                            <?php the_content(); ?>

                            <?php $authors = typology_get_authors(); ?>
                           
                            <?php if ( ! empty( $authors ) ) :?>
                                
                                <?php foreach ($authors as $author) : ?>

                                    <div class="typology-author">
                                            
                                        <div class="container">

                                            <div class="col-lg-2">
                                                <?php echo get_avatar( get_the_author_meta( 'ID', $author->ID), 100); ?>
                                            </div>

                                            <div class="col-lg-10">

                                                <?php echo '<h5 class="typology-author-box-title">'.get_the_author_meta('display_name', $author->ID).'</h5>'; ?>

                                                <div class="typology-author-desc">
                                                    <?php echo wpautop( get_the_author_meta('description', $author->ID) ); ?>
                                                </div>

                                                <div class="typology-author-links">
                                                    <?php echo typology_get_author_links( $author->ID ); ?>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </article>

                </div>

                <?php get_template_part('template-parts/ads/bottom'); ?>

            </div>

		<?php endwhile; ?>

	<?php endif; ?>

<?php get_footer(); ?>