<?php 
namespace ElementsKit_Lite\Core;

class Handler_Widget {

	public function wp_init() {
		return false;
	}

	static function get_name() {
		return false;
	}

	static function get_title() {
		return false;
	}

	static function get_icon() {
		return false;
	}

	static function get_categories() {
		return false;
	}
	
	static function get_dir() {
		return false;
	}
	
	static function get_url() {
		return false;
	}
	
	public function register_api() {
		return false;
	}

	public function inline_js() {
		return false;
	}

	public function inline_css() {
		return false;
	}
	
	public function sass() {
		return false;
	}

	public function scripts() {
		return false;
	}
	public function styles() {
		return false;
	}
}
