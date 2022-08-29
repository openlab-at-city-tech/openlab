<?php
namespace Elementor;


class ElementsKit_Widget_Progressbar_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-progressbar';
    }

    static function get_title() {
        return esc_html__( 'Progress Bar', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit ekit-progress-bar ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'progressbar/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'progressbar/';
    }
}