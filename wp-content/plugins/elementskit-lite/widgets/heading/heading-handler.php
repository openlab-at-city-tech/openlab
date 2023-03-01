<?php
namespace Elementor;

class ElementsKit_Widget_Heading_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-heading';
    }

    static function get_title() {
        return esc_html__( 'Heading', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit  ekit-widget-icon ekit-heading-style';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'heading', 'title', 'text'];
    }
    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'heading/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'heading/';
    }
}