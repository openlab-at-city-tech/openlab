<?php

/**
 * Register menus
 *
 * Callback function theme menus registration and init
 *
 * @since  1.0
 */

add_action( 'init', 'typology_register_menus' );

if ( !function_exists( 'typology_register_menus' ) ) :
	function typology_register_menus() {
		register_nav_menu( 'typology_main_menu', esc_html__( 'Main Menu' , 'typology' ) );
		register_nav_menu( 'typology_social_menu', esc_html__( 'Social Menu' , 'typology' ) );
	}
endif;


?>