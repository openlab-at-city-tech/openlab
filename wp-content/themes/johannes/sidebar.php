<aside class="johannes-sidebar row">

    <?php $sidebar = johannes_get( 'sidebar' ); ?>

    <?php  if ( isset( $sidebar['classic'] ) && is_active_sidebar( $sidebar['classic'] ) ): ?>
    	<?php dynamic_sidebar( $sidebar['classic'] ); ?>
    <?php endif; ?>

    <?php  if ( isset( $sidebar['sticky'] ) && is_active_sidebar( $sidebar['sticky'] ) ): ?>
	    <div class="johannes-sticky">
	    	<?php dynamic_sidebar( $sidebar['sticky'] ); ?>
	    </div>
    <?php endif; ?>

</aside>