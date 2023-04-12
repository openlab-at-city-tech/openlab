<?php
namespace Elementor;


class ElementsKit_Widget_Piechart_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-piechart';
    }

    static function get_title() {
        return esc_html__( 'Pie Chart', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-shape ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'chart', 'pie', 'doughnut', 'statistic'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'piechart/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'piechart/';
    }
}
