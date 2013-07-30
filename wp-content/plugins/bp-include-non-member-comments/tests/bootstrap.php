<?php

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../buddypress/tests' );
}

if ( file_exists( BP_TESTS_DIR . '/bootstrap.php' ) ) :

	require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

	function _bootstrap_bpinmc() {
		// Make sure BP is installed and loaded first
		require BP_TESTS_DIR . '/includes/loader.php';

		// Then load BPINMC
		require dirname( __FILE__ ) . '/../bp-include-non-member-comments.php';
	}
	tests_add_filter( 'muplugins_loaded', '_bootstrap_bpinmc' );

	require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';

	// Load the BP test files
	require BP_TESTS_DIR . '/includes/testcase.php';

endif;
