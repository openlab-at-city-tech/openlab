<?php
namespace Elementor;

class ElementsKit_Widget_Header_Offcanvas_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name()
    {
        return 'elementskit-header-offcanvas';
    }

    static function get_title()
    {
        return esc_html__('Header Offcanvas', 'elementskit-lite');
    }

    static function get_icon()
    {
        return 'eicon-sidebar ekit-widget-icon ';
    }

    static function get_categories()
    {
        return ['elementskit_headerfooter'];
    }

    static function get_keywords() {
        return [ 'ekit', 'header', 'offcanvas', 'side menu', 'side info'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'header-offcanvas/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'header-offcanvas/';
    }

}