<?php
namespace Elementor;

class ElementsKit_Widget_Image_Accordion_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-image-accordion';
    }

    static function get_title() {
        return esc_html__( 'Image Accordion', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit  ekit-widget-icon ekit-accordion';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'image', 'accordion', 'image faq', 'faq'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'image-accordion/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'image-accordion/';
    }

}