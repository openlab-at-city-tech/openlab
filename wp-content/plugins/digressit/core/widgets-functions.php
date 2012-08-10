<?php

global $blog_id;

//add_action('wp_print_styles', 'digress_widgets_print_styles');
//add_action('wp_print_scripts', 'digressit_widgets_print_scripts' );


function digress_widgets_print_styles(){
?>
<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/widgets.css'); ?>" type="text/css" media="screen" />
<?php
}
/*
function digressit_widgets_print_scripts(){
	wp_enqueue_script('digressit.widgets', get_digressit_media_uri('js/digressit.widgets.js'), 'jquery', false, true );
}
*/


if ( function_exists('register_sidebar') ) {

	if(is_multisite() && ($blog_id == 1)){
		register_sidebar(array(
			'id' => 'frontpage-content',		
			'name' => 'Frontpage Content',
			'before_widget' => '<div id="%1$s-content" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'id' => 'mainpage-content',		
		'name' => 'Mainpage Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'single-content',		
		'name' => 'Single Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'page-content',		
		'name' => 'Page Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));



	if(is_multisite() && ($blog_id == 1)){
		register_sidebar(array(
			'name' => 'Frontpage Sidebar',
			'id' => 'frontpage-sidebar',		
			'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'name' => 'Mainpage Sidebar',
		'id' => 'mainpage-sidebar',		
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	))	;

	register_sidebar(array(
		'id' => 'single-sidebar',		
		'name' => 'Single Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'page-sidebar',		
		'name' => 'Page Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));


	if(is_multisite() && ($blog_id == 1)){
	
		register_sidebar(array(
			'id' => 'frontpage-topbar',		
			'name' => 'Frontpage Topbar',
			'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'id' => 'mainpage-topbar',		
		'name' => 'Mainpage Topbar',
		'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'single-topbar',		
		'name' => 'Single Topbar',
		'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'page-topbar',		
		'name' => 'Page Topbar',
		'before_widget' => '<div id="%1$s-topbar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));
	



	


}


?>