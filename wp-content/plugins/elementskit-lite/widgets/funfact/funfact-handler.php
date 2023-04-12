<?php
namespace Elementor;


class ElementsKit_Widget_Funfact_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-funfact';
    }

    static function get_title() {
        return esc_html__( 'Funfact', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon  ekit-progress-bar';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'fun', 'factor', 'animation', 'info' , 'number', 'animated'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'funfact/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'funfact/';
    }
}