<?php

function academiathemes_customizer_define_general_sections( $sections ) {
    $panel           = 'academiathemes' . '_general';
    $general_sections = array();

    $theme_sidebar_positions = array(
        'left'      => esc_html__('Left', 'bradbury'),
        'right'     => esc_html__('Right', 'bradbury'),
        'hidden'    => esc_html__('Hidden', 'bradbury')
    );

    $theme_dynamic_menu_positions = array(
        'display'   => esc_html__('Display', 'bradbury'),
        'none'      => esc_html__("Don't display", 'bradbury')
    );

    $general_sections['general'] = array(
        'panel'     => $panel,
        'title'     => esc_html__( 'General Settings', 'bradbury' ),
        'priority'  => 4900,
        'options'   => array(

            'theme-sidebar-position'    => array(
                'setting'               => array(
                    'default'           => 'left',
                    'sanitize_callback' => 'academiathemes_sanitize_text'
                ),
                'control'           => array(
                    'label'         => esc_html__( 'Sidebar Position', 'bradbury' ),
                    'type'          => 'radio',
                    'choices'       => $theme_sidebar_positions
                ),
            ),

            'theme-dynamic-menu-position'    => array(
                'setting'               => array(
                    'default'           => 0,
                    'sanitize_callback' => 'absint'
                ),
                'control'           => array(
                    'label'         => esc_html__( 'Display the Dynamic Menu', 'bradbury' ),
                    'type'          => 'checkbox'
                ),
            ),

            'theme-display-post-featured-image'    => array(
                'setting'               => array(
                    'sanitize_callback' => 'absint',
                    'default'           => 0
                ),
                'control'               => array(
                    'label'             => __( 'Display Featured Images in Posts and Pages', 'bradbury' ),
                    'type'              => 'checkbox'
                )
            ),

            'bradbury-display-pages'    => array(
                'setting'               => array(
                    'sanitize_callback' => 'absint',
                    'default'           => 0
                ),
                'control'               => array(
                    'label'             => __( 'Display Featured Pages on Homepage', 'bradbury' ),
                    'type'              => 'checkbox'
                )
            ),

            'bradbury-featured-page-1'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'bradbury_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Slideshow: Featured Page #1', 'bradbury' ),
                    'description'       => sprintf( wp_kses( __( 'This list is populated with <a href="%1$s">Pages</a>.', 'bradbury' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ),
                    'type'              => 'select',
                    'choices'           => bradbury_get_pages()
                ),
            ),

            'bradbury-featured-page-2'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'bradbury_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Slideshow: Featured Page #2', 'bradbury' ),
                    'type'              => 'select',
                    'choices'           => bradbury_get_pages()
                ),
            ),

            'bradbury-featured-page-3'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'bradbury_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Slideshow: Featured Page #3', 'bradbury' ),
                    'type'              => 'select',
                    'choices'           => bradbury_get_pages()
                ),
            ),

            'bradbury-featured-page-4'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'bradbury_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Slideshow: Featured Page #4', 'bradbury' ),
                    'type'              => 'select',
                    'choices'           => bradbury_get_pages()
                ),
            ),

            'bradbury-featured-page-5'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'bradbury_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Slideshow: Featured Page #5', 'bradbury' ),
                    'type'              => 'select',
                    'choices'           => bradbury_get_pages()
                ),
            ),

        ),
    );

    $general_sections['footer'] = array(
        'panel'     => $panel,
        'title'   => esc_html__( 'Footer', 'bradbury' ),
        'priority' => 4910,
        'options' => array(

            'bradbury_copyright_text' => array(
                'setting' => array(
                    'default'           => __('Copyright &copy; ','bradbury') . date("Y",time()) . ' ' . get_bloginfo('name'),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'control' => array(
                    'label'             => esc_html__( 'Copyright Text', 'bradbury' ),
                    'type'              => 'text',
                ),
            ),

            'theme-display-footer-credit' => array(
                'setting'               => array(
                    'sanitize_callback' => 'absint',
                    'default'           => 1
                ),
                'control'               => array(
                    'label'             => __( 'Display "Theme by AcademiaThemes"', 'bradbury' ),
                    'type'              => 'checkbox'
                )
            ),

        ),
    );

    return array_merge( $sections, $general_sections );
}

add_filter( 'academiathemes_customizer_sections', 'academiathemes_customizer_define_general_sections' );
