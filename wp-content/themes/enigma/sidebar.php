<div class="col-md-4 enigma-sidebar">
	<?php if ( is_active_sidebar( 'sidebar-primary' ) )
	{ dynamic_sidebar( 'sidebar-primary' );	}
	else  { 
	$args = array(
	'before_widget' => '<div class="enigma_sidebar_widget">',
	'after_widget'  => '</div>',
	'before_title'  => '<div class="enigma_sidebar_widget_title"><h2>',
	'after_title'   => '</h2></div>' );
	the_widget('WP_Widget_Archives', null, $args);
	} ?>
</div>