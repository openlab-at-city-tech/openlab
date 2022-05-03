<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class Deactivate {
	public static function deactivate(){
		flush_rewrite_rules();
	}
}