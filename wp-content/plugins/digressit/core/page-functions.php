<?php

add_action('wp_print_styles', 'digressit_page_print_styles');
//add_action('wp_print_scripts', 'digressit_page_print_scripts' );
add_action('add_dynamic_widget', 'digressit_page_sidebar_widgets');


function digressit_page_print_styles(){
	if(is_page()){
	?>
		<link rel="stylesheet" href="<?php echo get_digressit_media_uri('css/page.css'); ?>" type="text/css" media="screen" />
	<?php
	}
}

/*
function digressit_page_print_scripts(){
	if(is_page()){
		wp_enqueue_script('digressit.page', get_digressit_media_uri('js/digressit.page.js'), 'jquery', false, true );
	}
}
*/
function digressit_page_sidebar_widgets(){
	if(is_page()){
		$options = get_option('digressit');
		if(is_active_sidebar('page-sidebar') && $options['enable_sidebar'] != 0){
			?>
			<div class="sidebar-widgets">
			<div id="dynamic-sidebar" class="sidebar  <?php echo $options['auto_hide_sidebar']; ?> <?php echo $options['sidebar_position']; ?>">		
			<?php
			dynamic_sidebar('Page Sidebar');		
			?>
			</div>
			</div>
			<?php
		}
	}	
}

?>