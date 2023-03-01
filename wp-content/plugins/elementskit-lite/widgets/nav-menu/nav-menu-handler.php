<?php
namespace Elementor;

class ElementsKit_Widget_Nav_Menu_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'ekit-nav-menu';
    }

    static function get_title() {
        return esc_html__( 'ElementsKit Nav Menu', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-nav-menu ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit_headerfooter' ];
    }

    static function get_keywords() {
        return ['ekit', 'menu', 'nav-menu', 'nav', 'navigation', 'navigation-menu', 'mega', 'megamenu', 'mega-menu'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'nav-menu/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'nav-menu/';
    }

}