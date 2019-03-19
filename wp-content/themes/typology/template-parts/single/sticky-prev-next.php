<nav class="typology-prev-next-nav typology-flex-center">
	
	<?php $prev_next = typology_get_prev_next_posts(); ?>

<div class="typology-prev-link typology-sticky-l">	
	<?php if( !empty( $prev_next['prev'] ) ): ?>			
			<a href="<?php echo esc_url( get_permalink( $prev_next['prev'] ) ); ?>">
				<span class="typology-pn-ico"><i class="fa fa-chevron-left"></i></span>
				<span class="typology-pn-link"><?php echo get_the_title( $prev_next['prev'] );?></span>
			</a>
	<?php endif; ?>
</div>
	
	<a href="javascript: void(0);" class="typology-sticky-to-top typology-sticky-c">
			<span class="typology-top-ico"><i class="fa fa-chevron-up"></i></span>
			<span class="typology-top-link"><?php echo __typology('to_top');?></span>
	</a>

<div class="typology-next-link typology-sticky-r">	
	<?php if( !empty( $prev_next['next'] ) ): ?>
	
			<a href="<?php echo esc_url( get_permalink( $prev_next['next'] ) ); ?>">
				<span class="typology-pn-ico"><i class="fa fa-chevron-right"></i></span>
				<span class="typology-pn-link"><?php echo get_the_title( $prev_next['next'] );?></span>
			</a>
	<?php endif; ?>
		</div>
</nav>

