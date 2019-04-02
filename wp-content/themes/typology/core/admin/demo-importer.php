<?php

/**
 * Demo directory path filter
 *
 * It sets the path for demo packages
 *
 * @return string Directory path
 * @since  1.0
 */
if ( !function_exists( 'typology_wbc_change_demo_directory_path' ) ):
    function typology_wbc_change_demo_directory_path( $demo_directory_path ) {

        $demo_directory_path = str_replace( '\\', '/', get_template_directory() . '/inc/demos/' );

        return $demo_directory_path;

    }
endif;

add_filter( 'wbc_importer_dir_path', 'typology_wbc_change_demo_directory_path' );


/**
 * Demo page description filter
 *
 * It sets the description text of demo importer page in theme options
 *
 * @return string Directory path
 * @since  1.0
 */

if ( !function_exists( 'typology_wbc_filter_desc' ) ):
    function typology_wbc_filter_desc( $description ) {

        $message = sprintf( __('Use this panel to import content from theme demo example(s). Note: If you want to try multiple demos, please use %s plugin to reset your WordPress installation after each import and try another demo afterwards.', 'typology') , '<a href="https://wordpress.org/plugins/wordpress-database-reset/" target="_blank">WordPress Database Reset</a>' );
        return $message;
    }
endif;

add_filter( 'wbc_importer_description', 'typology_wbc_filter_desc' );


/**
 * Demos title filter
 *
 * It sets the title of demo importer examples in theme options
 *
 * @return string Directory path
 * @since  1.0
 */

if ( !function_exists( 'typology_wbc_filter_demo_title' ) ):
    function typology_wbc_filter_demo_title( $path ) {

        switch ( $path ) {
            case '01_default': $title = esc_html__( 'Typology Default', 'typology' ); break;
        default: break;
        }
        return $title;
    }
endif;

add_filter( 'wbc_importer_directory_title', 'typology_wbc_filter_demo_title' );


/**
 * Demo import handler
 *
 * Callback function to execute after redux demo importer.
 * It sets menu locations and home page.
 *
 * @param string  $demo_active_import Name of current demo package to import
 * @return void
 * @since  1.0
 */

if ( !function_exists( 'typology_wbc_after_import' ) ) :
    function typology_wbc_after_import( $demo_active_import , $demo_directory_path ) {

        /* Set Menus */


        $menus = array();

        $main_menu = get_term_by( 'name', 'Typology Main', 'nav_menu' );
        if ( isset( $main_menu->term_id ) ) {
            $menus['typology_main_menu'] = $main_menu->term_id;
        }

        if ( !empty( $menus ) ) {
            set_theme_mod( 'nav_menu_locations', $menus );
        }


        /* Set Home Page */

        $home_page_title = 'Typology Home';

        $page = get_page_by_title( $home_page_title );

        if ( isset( $page->ID ) ) {
            update_option( 'page_on_front', $page->ID );
            update_option( 'show_on_front', 'page' );
        }

        

    }

endif;

add_action( 'wbc_importer_after_theme_options_import', 'typology_wbc_after_import', 10, 2 );


?>