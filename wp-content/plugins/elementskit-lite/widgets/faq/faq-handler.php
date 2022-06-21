<?php
namespace Elementor;


class ElementsKit_Widget_FAQ_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-faq';
    }

    static function get_title() {
        return esc_html__( 'FAQ', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit ekit-faq ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'faq/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'faq/';
    }
}