<?php
namespace Elementor;

class ElementsKit_Widget_Button_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-button';
    }

    static function get_title() {
        return esc_html__( 'Button', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-button';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'button/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'button/';
    }
}