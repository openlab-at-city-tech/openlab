<?php
namespace Elementor;


class ElementsKit_Widget_Icon_Hover_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-icon-hover';
    }

    static function get_title() {
        return esc_html__( 'Icon Hover', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-image-hotspot ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'icon-hover/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'icon-hover/';
    }
}