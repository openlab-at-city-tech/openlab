<?php

$tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $tests_dir ) {
	die( "No WP_TESTS_DIR path defined.\n" );
}

require_once $tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../text-hover.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $tests_dir . '/includes/bootstrap.php';
