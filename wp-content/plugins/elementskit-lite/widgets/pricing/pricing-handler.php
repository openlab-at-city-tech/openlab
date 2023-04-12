<?php
namespace Elementor;


class ElementsKit_Widget_Pricing_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-pricing';
    }

    static function get_title() {
        return esc_html__( 'Pricing Table', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-price-table ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'price',  'pricing', 'table', 'package', 'plan'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'pricing/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'pricing/';
    }

}