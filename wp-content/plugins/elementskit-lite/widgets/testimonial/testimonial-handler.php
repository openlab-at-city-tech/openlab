<?php
namespace Elementor;


class ElementsKit_Widget_Testimonial_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-testimonial';
    }

    static function get_title() {
        return esc_html__( 'Testimonial', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'ekit ekit-testimonial-grid ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'testimonial', 'carousel', 'reviews', 'rating', 'stars'];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'testimonial/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'testimonial/';
    }

}