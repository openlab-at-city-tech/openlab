<?php
namespace Elementor;

class ElementsKit_Widget_Category_List_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
        return 'elementskit-category-list';
    }

    static function get_title() {
        return esc_html__( 'Category List', 'elementskit-lite' );
    }

    static function get_icon() {
        return ' ekit-widget-icon eicon-bullet-list';
    }


	static function get_keywords() {
		return [ 'list', 'category list', 'category', 'ekit', 'elementskit-lite', 'elementskit category list' ];
	}

    static function get_categories() {
        return [ 'elementskit_headerfooter' ];
	}

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'category-list/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'category-list/';
    }
}