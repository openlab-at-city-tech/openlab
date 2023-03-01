<?php
namespace Elementor;

class ElementsKit_Widget_Business_Hours_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-business-hours';
    }

    static function get_title() {
        return esc_html__( 'Business Hours', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit-widget-icon eicon-clock-o';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'business hours', 'opening hours', 'hours', 'opening times', 'times', 'currently open', 'watch'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'business-hours/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'business-hours/';
    }
}