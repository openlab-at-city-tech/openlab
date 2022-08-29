<?php
namespace Elementor;


class ElementsKit_Widget_Lottie_Handler extends \ElementsKit_Lite\Core\Handler_Widget {

    public function wp_init(){
        include self::get_dir() . 'json-handler.php';
    }

    static function get_name() {
        return 'elementskit-lottie';
    }

    static function get_title() {
        return esc_html__( 'Lottie', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-animation ekit-widget-icon';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'lottie/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'lottie/';
    }
}
