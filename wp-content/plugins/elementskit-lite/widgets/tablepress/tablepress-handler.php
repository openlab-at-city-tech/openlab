<?php
namespace Elementor;


class ElementsKit_Widget_TablePress_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-tablepress';
    }

    static function get_title() {
        return esc_html__( 'TablePress', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-table ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'tablepress/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'tablepress/';
    }
}