<?php
namespace Elementor;


class ElementsKit_Widget_Social_Share_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-social-share';
    }

    static function get_title() {
        return esc_html__( 'Social Share', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' eicon-social-icons ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'social', 'share', 'facebook', 'twitter', 'instagram', 'linkedin'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'social-share/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'social-share/';
    }
}