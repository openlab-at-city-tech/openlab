<?php

function academiathemes_customizer_define_color_scheme_sections( $sections ) {
    $panel           = 'academiathemes' . '_color-scheme';
    $colors_sections = array();

    $colors_sections['color'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'General','bradbury' ),
        'options' => array(

            'color-body-text' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Body Text','bradbury' ),
                ),
            ),

            'color-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link Color','bradbury' ),
                ),
            ),

            'color-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link Color on Hover','bradbury' ),
                ),
            ),

            'color-accent-border' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Accent Color','bradbury' ),
                ),
            ),

            'color-accent' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Accent Color (Meta)','bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-main-menu'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Primary Menu','bradbury' ),
        'options' => array(

            'color-menu-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Menu Item','bradbury' ),
                ),
            ),

            'color-menu-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Menu Item on Hover','bradbury' ),
                ),
            ),

            'color-submenu-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Sub-Menu Item Background','bradbury' ),
                ),
            ),

            'color-submenu-background-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Sub-Menu Item Background on Hover','bradbury' ),
                ),
            ),

            'color-submenu-menu-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Sub-Menu Item','bradbury' ),
                ),
            ),

            'color-submenu-menu-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Sub-Menu Item on Hover','bradbury' ),
                ),
            ),

            'color-submenu-border-bottom' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Sub-Menu Item Border Bottom','bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-mobile-menu'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Mobile Menu', 'bradbury' ),
        'options' => array(

            'color-mobile-menu-toggle-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Open Menu Button Background', 'bradbury' ),
                ),
            ),

            'color-mobile-menu-toggle-background-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Open Menu Button Background :hover', 'bradbury' ),
                ),
            ),

            'color-mobile-menu-toggle' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Open Menu Button Color', 'bradbury' ),
                ),
            ),

            'color-mobile-menu-toggle-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Open Menu Button Color :hover', 'bradbury' ),
                ),
            ),
            'color-mobile-menu-container-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Open Menu Background', 'bradbury' ),
                ),
            ),
            'color-mobile-menu-link-border' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link Border Color', 'bradbury' ),
                ),
            ),
            'color-mobile-menu-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link Color', 'bradbury' ),
                ),
            ),
            'color-mobile-menu-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link Color :hover', 'bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-secondary-menu'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Secondary Menu','bradbury' ),
        'options' => array(

            'color-secondary-menu-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Background','bradbury' ),
                ),
            ),

            'color-secondary-menu-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Menu Item','bradbury' ),
                ),
            ),

            'color-secondary-menu-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Menu Item on Hover','bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-footer'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Footer','bradbury' ),
        'options' => array(

            'color-footer-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Background','bradbury' ),
                ),
            ),

            'color-footer-text' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Text','bradbury' ),
                ),
            ),

            'color-footer-widget-title' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Widget Title','bradbury' ),
                ),
            ),

            'color-footer-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link','bradbury' ),
                ),
            ),

            'color-footer-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link on Hover','bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-footer-credits'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Footer: Copyright','bradbury' ),
        'options' => array(

            'color-footer-credits-background' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Background','bradbury' ),
                ),
            ),

            'color-footer-credits-text' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Text','bradbury' ),
                ),
            ),

            'color-footer-credits-link' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link','bradbury' ),
                ),
            ),

            'color-footer-credits-link-hover' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Link on Hover','bradbury' ),
                ),
            ),

        )
    );

    $colors_sections['color-single'] = array(
        'panel'   => $panel,
        'title'   => esc_html__( 'Posts and Pages','bradbury' ),
        'options' => array(

            'color-single-title' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Post/Page Title','bradbury' ),
                ),
            ),

            'color-single-meta' => array(
                'setting' => array(
                    'sanitize_callback' => 'academiathemes_maybe_hash_hex_color',
                    'transport'  => 'postMessage'
                ),
                'control' => array(
                    'control_type' => 'WP_Customize_Color_Control',
                    'label'        => esc_html__( 'Post Meta & Page Taglines','bradbury' ),
                ),
            ),

        )
    );

    return array_merge( $sections, $colors_sections );
}

add_filter( 'academiathemes_customizer_sections', 'academiathemes_customizer_define_color_scheme_sections' );
