<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Api\Callbacks;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class SanitizationCallbacks {

	public function sanitizeTextFields( $input ) {
		$input = sanitize_text_field( $input );
		return filter_var( $input, FILTER_SANITIZE_STRING );
	}

	public function sanitizeCheckbox( $input ) {
		return (isset($input) ? true : false );
	}
}