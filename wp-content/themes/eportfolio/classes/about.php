<?php
/**
 * ePortfolio About Page
 * @package ePortfolio
 *
 */
if (!class_exists('ePortfolio_About_page')):
    class ePortfolio_About_page
    {
        function __construct()
        {
            add_action('admin_menu', array($this, 'eportfolio_backend_menu'), 999);
        }
        // Add Backend Menu
        function eportfolio_backend_menu()
        {
            add_theme_page(esc_html__('ePortfolio', 'eportfolio'), esc_html__('ePortfolio', 'eportfolio'), 'activate_plugins', 'eportfolio-about', array($this, 'eportfolio_main_page'), 1);
        }
        // Settings Form
        function eportfolio_main_page()
        {
            require get_template_directory() . '/classes/about-render.php';
        }
    }
    new ePortfolio_About_page();
endif;