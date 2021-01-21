<?php
/*
Plugin Name: CAC Non-CUNY Signup
Description: Allows admin to enter validation codes for non-CUNY email addresses to register
Version: 1.0
Author: Boone Gorges
*/

namespace OpenLab\SignupCodes;

const PLUGIN_DIR = __DIR__;
const PLUGIN_FILE = __FILE__;

function bootstrap() {
	require_once __DIR__ . '/inc/schema.php';
	require_once __DIR__ . '/inc/helpers.php';
	require_once __DIR__ . '/inc/signup.php';
	require_once __DIR__ . '/inc/ajax.php';

	Schema::init();
}
add_action( 'bp_include', __NAMESPACE__ . '\\bootstrap' );
