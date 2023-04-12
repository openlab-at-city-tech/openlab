<?php
namespace Elementor;

class ElementsKit_Widget_Image_Comparison_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-image-comparison';
    }

    static function get_title() {
        return esc_html__( 'Image Comparison', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit  ekit-widget-icon ekit-image-comparison';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['compare', 'image', 'before image', 'after image', 'comparison'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'image-comparison/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'image-comparison/';
    }
}