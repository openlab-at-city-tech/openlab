<?php

function academiathemes_option_defaults() {
    $defaults = array(

        /**
         * Color Scheme
         */
        // General
        'color-body-text'                     => '#181818',
        'color-link'                          => '#195899',
        'color-link-hover'                    => '#cf4330',
        'color-accent'                        => '#006435',
        'color-accent-border'                 => '#195899',

        // Main Menu
        'color-menu-link'                     => '#121212',
        'color-menu-link-hover'               => '#cf4330',
        'color-submenu-background'            => '#ffffff',
        'color-submenu-background-hover'      => '#f8f8f8',
        'color-submenu-menu-link'             => '#121212',
        'color-submenu-menu-link-hover'       => '#cf4330',
        'color-submenu-border-bottom'         => '#F0F0F0',

        // Mobile Menu
        'color-mobile-menu-toggle-background'         => '#013B93',
        'color-mobile-menu-toggle-background-hover'   => '#B00000',
        'color-mobile-menu-toggle'                    => '#ffffff',
        'color-mobile-menu-toggle-hover'              => '#ffffff',
        'color-mobile-menu-container-background'      => '#111111',
        'color-mobile-menu-link-border'               => '#333333',
        'color-mobile-menu-link'                      => '#ffffff',
        'color-mobile-menu-link-hover'                => '#f0c030',

        // Secondary Menu
        'color-secondary-menu-background'     => '#eeeeee',
        'color-secondary-menu-link'           => '#121212',
        'color-secondary-menu-link-hover'     => '#cf4330',

        // Footer
        'color-footer-background'             => '#111111',
        'color-footer-text'                   => '#cccccc',
        'color-footer-widget-title'           => '#ffffff',
        'color-footer-link'                   => '#ffffff',
        'color-footer-link-hover'             => '#cf4330',

        // Footer: Credits
        'color-footer-credits-background'     => '#ffffff',
        'color-footer-credits-text'           => '#555555',
        'color-footer-credits-link'           => '#555555',
        'color-footer-credits-link-hover'     => '#cf4330',

        // Single Post
        'color-single-title'                  => '#0d0d0d',
        'color-single-meta'                   => '#737373',

        /* translators: This is the copyright notice that appears in the footer of the website. */
        'bradbury_copyright_text'                         => sprintf( esc_html__( 'Copyright &copy; %1$s %2$s.', 'bradbury' ), date( 'Y' ), get_bloginfo( 'name' ) ),
    );

    return $defaults;
}

function academiathemes_get_default( $option ) {
    $defaults = academiathemes_option_defaults();
    $default  = ( isset( $defaults[ $option ] ) ) ? $defaults[ $option ] : false;

    return $default;
}