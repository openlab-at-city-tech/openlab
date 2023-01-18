<?php
namespace Elementor;


class ElementsKit_Widget_Post_Grid_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-post-grid';
    }

    static function get_title() {
        return esc_html__( 'Post Grid', 'elementskit-lite' );
    }

    static function get_icon() {
        return 'eicon-posts-grid ekit-widget-icon ';
    }

    static function get_keywords() {
        return [ 'grid', 'post grid', 'post', 'ekit', 'elementskit post grid' ];
    }

    static function get_categories() {
        return [ 'elementskit_headerfooter' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'post-grid/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'post-grid/';
    }

}