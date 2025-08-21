<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

defined( 'ABSPATH' ) || exit;

/**
 * Silent skin to suppress all output during installation
 */
class Plugin_Skin extends \WP_Upgrader_Skin {
	public function header() {}
	public function footer() {}
	public function error($errors) {}
	public function feedback($string, ...$args) {}
	public function before() {}
	public function after() {}
	public function bulk_header() {}
	public function bulk_footer() {}
}