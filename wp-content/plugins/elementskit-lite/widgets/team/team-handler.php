<?php
namespace Elementor;


class ElementsKit_Widget_Team_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-team';
    }

    static function get_title() {
        return esc_html__( 'Team', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-image-box ekit-widget-icon ';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'team/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'team/';
    }


}