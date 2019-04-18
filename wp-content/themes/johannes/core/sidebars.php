<?php

/**
 * Register sidebars
 *
 * Callback function for theme sidebars registration and init
 *
 * @since  1.0
 */

add_action( 'widgets_init', 'johannes_register_sidebars' );

if ( !function_exists( 'johannes_register_sidebars' ) ) :
    function johannes_register_sidebars() {


        /* Hidden Sidebar */
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_hidden',
                'name' => esc_html__( 'Hidden Sidebar', 'johannes' ),
                'description' => esc_html__( 'This is a sidebar which opens on hamburger click in the site header', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-inside '. esc_attr( johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) ) ).'">',
                'after_widget' => '</div></div>',
                'before_title' => '<h4 class="widget-title">',
                'after_title' => '</h4>'
            )
        );

        /* Default Sidebar */
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_default',
                'name' => esc_html__( 'Default Sidebar', 'johannes' ),
                'description' => esc_html__( 'This is the default sidebar', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s col-12 col-md-6 col-lg-12"><div class="widget-inside '. esc_attr( johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) ) ).'">',
                'after_widget' => '</div></div>',
                'before_title' => '<h4 class="widget-title">',
                'after_title' => '</h4>'
            )
        );

        /* Default Sidebar */
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_default_sticky',
                'name' => esc_html__( 'Default Sticky Sidebar', 'johannes' ),
                'description' => esc_html__( 'This is the default sticky sidebar', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s col-12 col-md-6 col-lg-12"><div class="widget-inside '. esc_attr( johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) ) ).'">',
                'after_widget' => '</div></div>',
                'before_title' => '<h4 class="widget-title">',
                'after_title' => '</h4>'
            )
        );

        /* Footer Sidebar Area 1*/
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_footer_1',
                'name' => esc_html__( 'Footer Column 1', 'johannes' ),
                'description' => esc_html__( 'This is footer area column 1.', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s ">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="widget-title">',
                'after_title' => '</h5>'
            )
        );

        /* Footer Sidebar Area 2*/
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_footer_2',
                'name' => esc_html__( 'Footer Column 2', 'johannes' ),
                'description' => esc_html__( 'This is footer area column 2.', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="widget-title">',
                'after_title' => '</h5>'
            )
        );



        /* Footer Sidebar Area 3*/
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_footer_3',
                'name' => esc_html__( 'Footer Column 3', 'johannes' ),
                'description' => esc_html__( 'This is footer area column 3.', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="widget-title">',
                'after_title' => '</h5>'
            )
        );

        /* Footer Sidebar Area 4*/
        register_sidebar(
            array(
                'id' => 'johannes_sidebar_footer_4',
                'name' => esc_html__( 'Footer Column 4', 'johannes' ),
                'description' => esc_html__( 'This is footer area column 4.', 'johannes' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="widget-title">',
                'after_title' => '</h5>'
            )
        );

        /* Add sidebars from theme options */
        $custom_sidebars = johannes_get_option( 'sidebars' );

        if ( !empty( $custom_sidebars ) ) {
            foreach ( $custom_sidebars as $key => $sidebar ) {

                if ( is_numeric( $key ) ) {
                    register_sidebar(
                        array(
                            'id' => 'johannes_sidebar_'.$key,
                            'name' => $sidebar['name'],
                            'description' => '',
                            'before_widget' => '<div id="%1$s" class="widget %2$s col-12 col-md-6 col-lg-12"><div class="widget-inside '. esc_attr( johannes_get_background_css_class( johannes_get_option( 'widget_bg' ) ) ).'">',
                            'after_widget' => '</div></div>',
                            'before_title' => '<h4 class="widget-title">',
                            'after_title' => '</h4>'
                        )
                    );
                }
            }
        }
    }

endif;




?>
