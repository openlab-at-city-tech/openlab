<?php
namespace Elementor;

class ElementsKit_Widget_Back_To_Top_Handler extends \ElementsKit_Lite\Core\Handler_Widget {
    
    static function get_name() {
        return 'elementskit-back-to-top';
    }

    static function get_title() {
        return esc_html__( 'Back to Top', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-arrow-up';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_keywords() {
        return ['ekit', 'back to top', 'scroll', 'scroll to top', 'back', 'top'];
    }

}
