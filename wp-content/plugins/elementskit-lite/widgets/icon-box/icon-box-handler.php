<?php
namespace Elementor;

class ElementsKit_Widget_Icon_Box_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-icon-box';
    }

    static function get_title() {
        return esc_html__( 'Icon Box', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-info-box ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'icon-box/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'icon-box/';
    }
}