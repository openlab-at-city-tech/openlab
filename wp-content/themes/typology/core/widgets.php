<?php

/**
 * Register widgets
 *
 * Callback function which includes widget classes and initialize theme specific widgets
 *
 * @since  1.0
 */

add_action( 'widgets_init', 'typology_register_widgets' );

if ( !function_exists( 'typology_register_widgets' ) ) :
	function typology_register_widgets() {
		
		include_once get_template_directory() .'/core/widgets/posts.php';
		include_once get_template_directory() .'/core/widgets/categories.php';
		
		register_widget( 'Typology_Posts_Widget' );
		register_widget( 'Typology_Category_Widget' );

	}
endif;


?>