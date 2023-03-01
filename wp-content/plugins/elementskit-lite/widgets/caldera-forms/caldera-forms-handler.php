<?php
namespace Elementor;


class ElementsKit_Widget_Caldera_Forms_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-caldera-forms';
    }

    static function get_title() {
        return esc_html__( 'Caldera Forms', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-mail ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'caldera', 'wpf', 'wpform' , 'weform', 'fluent form', 'form', 'contact', 'cf7', 'contact form', 'ninja'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'caldera-forms/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'caldera-forms/';
    }
}