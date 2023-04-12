<?php
namespace Elementor;


class ElementsKit_Widget_Wp_Forms_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-wp-forms';
    }

    static function get_title() {
        return esc_html__( 'Wp Forms', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-mail ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'wpf', 'wpform', 'weform', 'form', 'contact', 'cf7', 'contact form'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'wp-forms/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'wp-forms/';
    }
}