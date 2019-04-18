<?php if( $pagination = get_the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __johannes( 'previous_posts' ), 'next_text' => __johannes( 'next_posts' ) ) ) ) : ?>
	    <div class="col-12 text-center johannes-order-2">
            <nav class="johannes-pagination">

            	<?php if( !get_previous_posts_link() ) : ?>
            		<a href="javascript:void(0);" class="prev johannes-button page-numbers disabled"><?php echo __johannes( 'previous_posts' ); ?></a>
	            <?php endif; ?>

                <?php echo wp_kses_post( $pagination ); ?>

                <?php if( !get_next_posts_link() ) : ?>
            		<a href="javascript:void(0);" class="next johannes-button page-numbers disabled"><?php echo __johannes( 'next_posts' ); ?></a>
	            <?php endif; ?>

            </nav>
	    </div>
<?php endif; ?>