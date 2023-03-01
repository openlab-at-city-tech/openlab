<?php
namespace Elementor;

class ElementsKit_Widget_Header_Info_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name()
    {
        return 'elementskit-header-info';
    }

    static function get_title()
    {
        return esc_html__('Header Info', 'elementskit-lite');
    }

    static function get_icon()
    {
        return 'eicon-form-vertical ekit-widget-icon ';
    }

    static function get_categories()
    {
        return ['elementskit_headerfooter'];
    }

    static function get_keywords() {
        return ['ekit', 'header-info', 'info-list', 'info', 'list'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'header-info/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'header-info/';
    }

}