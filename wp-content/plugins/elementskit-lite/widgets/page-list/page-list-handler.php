<?php
namespace Elementor;


class ElementsKit_Widget_Page_List_Handler extends \ElementsKit_Lite\Core\Handler_Widget{

    static function get_name() {
		return 'elementskit-page-list';
	}


	static function get_title() {
		return esc_html__( 'Page List', 'elementskit-lite' );
	}


	static function get_icon() {
		return 'eicon-bullet-list ekit-widget-icon ';
	}


	static function get_keywords() {
		return [ 'list', 'page list', 'page', 'ekit', 'elementskit page list' ];
	}

    static function get_categories() {
        return [ 'elementskit_headerfooter' ];
	}

    static function get_dir() {
        return \ElementsKit_Lite::widget_dir() . 'page-list/';
    }

    static function get_url() {
        return \ElementsKit_Lite::widget_url() . 'page-list/';
    }

}