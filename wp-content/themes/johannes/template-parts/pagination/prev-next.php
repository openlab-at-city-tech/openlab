<div class="col-12 text-center johannes-order-2">
    <nav class="johannes-pagination prev-next nav-links">
        <div class="prev">
        	<?php if( get_previous_posts_link() ) : ?>
            	<?php previous_posts_link( __johannes( 'newer_entries' ) ); ?>
            <?php else: ?>
            	<a href="javascript:void(0);" class="johannes-button disabled"><?php echo __johannes( 'newer_entries' ); ?></a>
            <?php endif; ?>
        </div>
        <div class="next">
            <?php if( get_next_posts_link() ) : ?>
            	<?php next_posts_link( __johannes( 'older_entries' ) ); ?>
            <?php else: ?>
            	<a href="javascript:void(0);" class="johannes-button disabled"><?php echo __johannes( 'older_entries' ); ?></a>
            <?php endif; ?>
        </div>
    </nav>
</div>
