<?php
// Register widgetized areas
function the_widgets_init() {
	if ( !function_exists('register_sidebars') ) {
	    return;
	}
	
	register_sidebar(array(
		'name'=>'Middle Sidebar (Home)',
		'id' => 'middle_sidebar',
	    'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    'after_widget' => '</div>',
	    'before_title' => '<h3 class="mast">',
	    'after_title' => '</h3>',
	));
	
	register_sidebar(array(
		'name'=>'Right Sidebar',
		'id' => 'right_sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="mast">',
		'after_title' => '</h3>',
	));
}

add_action( 'init', 'the_widgets_init' );
?>