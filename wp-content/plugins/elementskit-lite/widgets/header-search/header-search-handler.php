<?php
namespace Elementor;

class ElementsKit_Widget_Header_Search_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name()
    {
        return 'elementskit-header-search';
    }

    static function get_title()
    {
        return esc_html__('Header Search', 'elementskit-lite');
    }

    static function get_icon()
    {
        return 'eicon-search ekit-widget-icon ';
    }

    static function get_categories()
    {
        return ['elementskit_headerfooter'];
    }

    static function get_keywords() {
        return [ 'ekit', 'header', 'search', 'search box'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'header-search/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'header-search/';
    }

}