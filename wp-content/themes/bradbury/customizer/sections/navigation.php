<?php

/**
 * @param WP_Customize_Manager $wp_customize
 */
function academia_customizer_navigation( $wp_customize ) {
    $section_id = 'nav';

    // The Navigation section only exists if custom menus have been created.
    if ( ! isset( $wp_customize->get_section( $section_id )->title ) ) {
        $wp_customize->add_section( $section_id );
    }

    $section = $wp_customize->get_section( $section_id );
    $section->priority = 50;
}

add_action( 'customize_register', 'academia_customizer_navigation', 20 );
