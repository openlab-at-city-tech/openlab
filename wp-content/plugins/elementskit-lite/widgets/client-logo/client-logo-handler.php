<?php
namespace Elementor;

class ElementsKit_Widget_Client_Logo_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-client-logo';
    }

    static function get_title() {

        return esc_html__( 'Client Logo', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-slider-push ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'client-logo/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'client-logo/';
    }

}