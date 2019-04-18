<div class="johannes-section">
    <div class="container">
        <div class="section-content row justify-content-center">
            
            <?php if ( johannes_has_sidebar( 'left' ) ): ?>
		        <div class="col-12 col-lg-4 johannes-order-2">
		            <?php get_sidebar(); ?>
		        </div>
    		<?php endif; ?>

            <div class="col-12 col-lg-<?php echo esc_attr( johannes_get_option('page_width') ); ?> single-md-content col-md-special johannes-order-1">

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php if ( johannes_get( 'layout' ) == 2 ): ?>

                        <div class="category-pill section-head-alt single-layout-2">
                            <div class="entry-header">
                                <?php echo johannes_breadcrumbs(); ?>
                                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                            </div>
                        </div>

                    <?php endif; ?>


                    <?php if ( johannes_get( 'headline' ) && has_excerpt() ): ?>
						<div class="entry-summary">
                            <span><?php echo __johannes( 'summary' );?></span>
						    <?php the_excerpt(); ?>
						</div>
					<?php endif; ?>

                    <div class="entry-content entry-single clearfix">
                        <?php the_content(); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="paginated-post-wrapper clearfix">', 'after' => '</div>' ) ); ?>
                    </div>

                    <?php if( is_page_template('template-authors.php') ): ?>
                        <?php get_template_part( 'template-parts/page/authors' ); ?>
                    <?php endif; ?>

                </article>
               
		        <?php comments_template(); ?>

            </div>

            <?php if ( johannes_has_sidebar( 'right' ) ): ?>
		        <div class="col-12 col-lg-4 johannes-order-2">
		            <?php get_sidebar(); ?>
		        </div>
    		<?php endif; ?>

        </div>
    </div>
</div>