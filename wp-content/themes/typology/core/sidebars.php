<?php

/**
 * Register sidebars
 *
 * Callback function for theme sidebars registration and init
 * 
 * @since  1.0
 */

add_action( 'widgets_init', 'typology_register_sidebars' );

if ( !function_exists( 'typology_register_sidebars' ) ) :
	function typology_register_sidebars() {
		
		/* Default Sidebar */
		register_sidebar(
			array(
				'id' => 'typology_default_sidebar',
				'name' => esc_html__( 'Default Sidebar', 'typology' ),
				'description' => esc_html__( 'This is default sidebar', 'typology' ),
				'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4 class="widget-title h5">',
				'after_title' => '</h4>'
			)
		);


		/* Footer Left */
		register_sidebar(
			array(
				'id' => 'typology_footer_sidebar_left',
				'name' => esc_html__( 'Footer Left', 'typology' ),
				'description' => esc_html__( 'This is footer left column', 'typology' ),
				'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4 class="widget-title h5">',
				'after_title' => '</h4>'
			)
		);

		/* Footer Center */
		register_sidebar(
			array(
				'id' => 'typology_footer_sidebar_center',
				'name' => esc_html__( 'Footer Center', 'typology' ),
				'description' => esc_html__( 'This footer center column', 'typology' ),
				'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4 class="widget-title h5">',
				'after_title' => '</h4>'
			)
		);


		/* Footer Right */
		register_sidebar(
			array(
				'id' => 'typology_footer_sidebar_right',
				'name' => esc_html__( 'Footer Right', 'typology' ),
				'description' => esc_html__( 'This footer right column', 'typology' ),
				'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4 class="widget-title h5">',
				'after_title' => '</h4>'
			)
		);

		/* WooCommerce Sidebar */
		if ( typology_is_woocommerce_active()) {	
			register_sidebar(
				array(
					'id' => 'typology_woocommerce_sidebar',
					'name' => esc_html__( 'WooCommerce Sidebar', 'typology' ),
					'description' => esc_html__( 'This sidebar will show on WooCommerce pages', 'typology' ),
					'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h4 class="widget-title h5">',
					'after_title' => '</h4>'
				)
			);
		}

	}

endif;




?>