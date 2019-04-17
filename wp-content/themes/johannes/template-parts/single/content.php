<div class="johannes-section">
    <div class="container">
        <div class="section-content row justify-content-center">
            
            <?php if ( johannes_has_sidebar( 'left' ) ): ?>
		        <div class="col-12 col-lg-4 johannes-order-2">
		            <?php get_sidebar(); ?>
		        </div>
    		<?php endif; ?>

            <div class="col-12 col-lg-<?php echo esc_attr( johannes_get_option('single_width') ); ?> single-md-content col-md-special johannes-order-1 <?php echo esc_attr( johannes_single_content_offset() ); ?>">
                 <?php if ( johannes_get( 'avatar' ) ) : ?> 
                    <div class="entry-meta-sidebar ">
                        <div class="written-by">
    					    <span><?php echo __johannes( 'written_by' );?></span>
    					    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename', get_the_author_meta( 'ID' ) ) ) ); ?>">
                               <span><?php echo get_the_author_meta( 'display_name' ); ?></span>
    					       <?php echo get_avatar( get_the_author_meta( 'ID' ), 50 ); ?>
                            </a>
    					</div>
                    </div>
                <?php endif; ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php if ( johannes_get( 'layout' ) == 2 ): ?>

                        <div class="category-pill section-head-alt single-layout-2">
                            <div class="entry-header">
                                <?php echo johannes_breadcrumbs(); ?>
                                <?php if ( johannes_get( 'category' ) ): ?>
                                    <div class="entry-category">
                                        <?php echo johannes_get_category(); ?>
                                    </div>
                                <?php endif; ?>
                                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                                <?php if ( johannes_get( 'meta' ) ): ?>
                                <div class="entry-meta">
                                    <?php echo johannes_get_meta_data( johannes_get( 'meta' ) ); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <?php if( strpos( johannes_get('share'), 'above' ) !== false ) : ?>
                        <?php get_template_part( 'template-parts/single/share' ); ?>
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

                </article>

                <?php if ( johannes_get( 'tags' ) && has_tag() ) : ?>
                    <div class="entry-tags clearfix">
                        <?php the_tags( '<span>'.__johannes( 'tagged_as' ).'</span>', ', ', '' ); ?>
                    </div>
                <?php endif; ?>

                <?php if( strpos( johannes_get('share'), 'below' ) !== false  ) : ?>
                    <?php get_template_part( 'template-parts/single/share' ); ?>
                <?php endif; ?>

                <?php if ( johannes_get( 'author' ) ): ?>
            		<?php get_template_part( 'template-parts/single/author' ); ?>
        		<?php endif; ?>
               
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

<?php if ( johannes_get( 'related' ) ): ?>
    <?php get_template_part( 'template-parts/single/related' ); ?>
<?php endif; ?>