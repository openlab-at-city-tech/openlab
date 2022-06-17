<?php
namespace Elementor;

class ElementsKit_Widget_Blog_Posts_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-blog-posts';
    }

    static function get_title() {
        return esc_html__( 'Blog Posts', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-posts-grid';
    }

    static function get_categories() {
        return [ 'elementskit' ];
    }

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'blog-posts/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'blog-posts/';
    }

}