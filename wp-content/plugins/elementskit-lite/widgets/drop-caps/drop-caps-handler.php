<?php
namespace Elementor;

class ElementsKit_Widget_Drop_Caps_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-drop-caps';
    }

    static function get_title() {
        return esc_html__( 'Drop Caps', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-typography';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'drop', 'caps', 'initial', 'versal', 'letter'];
    }
    
    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'drop-caps/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'drop-caps/';
    }
}