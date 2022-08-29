<?php
namespace Elementor;


class ElementsKit_Widget_Fluent_Forms_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-fluent-forms';
    }

    static function get_title() {
        return esc_html__( 'Fluent Forms', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-mail ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'fluent-forms/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'fluent-forms/';
    }
}