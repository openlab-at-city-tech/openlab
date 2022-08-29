<?php
namespace Elementor;

class ElementsKit_Widget_Contact_Form7_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-contact-form7';
    }

    static function get_title() {
        return esc_html__( 'Contact Form 7', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-mail ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'contact-form7/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'contact-form7/';
    }
}
