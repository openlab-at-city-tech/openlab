<?php
namespace Elementor;


class ElementsKit_Widget_Accordion_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    static function get_name() {
        return 'elementskit-accordion';
    }

    static function get_title() {
        return esc_html__( 'Accordion', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit ekit-widget-icon ekit-accordion';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'accordion/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'accordion/';
    }
}