<div class="center-column-sidebar">

<div id="footer-widgets">
	<?php if ( is_active_sidebar( 'widget-area-1' ) ) : ?>
	<ul id="footer-left" class="widget-area">
		<?php dynamic_sidebar( 'widget-area-1' ); ?>
	</ul>
	<?php endif ?>

	<?php if ( is_active_sidebar( 'widget-area-2' ) ) : ?>
	<ul id="footer-middle" class="widget-area">
		<?php dynamic_sidebar( 'widget-area-2' ); ?>
	</ul>
	<?php endif ?>

	<?php if ( is_active_sidebar( 'widget-area-3' ) ) : ?>
	<ul id="footer-right" class="widget-area">
		<?php dynamic_sidebar( 'widget-area-3' ); ?>
	</ul>
	<?php endif ?>
</div>	