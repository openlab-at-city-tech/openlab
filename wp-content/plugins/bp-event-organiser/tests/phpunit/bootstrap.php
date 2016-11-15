<?php

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../../buddypress/tests/phpunit' );
}

if ( file_exists( BP_TESTS_DIR . '/bootstrap.php' ) ) :

	require_once getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/functions.php';

	$eo_dir = dirname( __FILE__ ) . '/../../../event-organiser/tests';

	function _bootstrap_bp() {
		// Make sure BP is installed and loaded first.
		require BP_TESTS_DIR . '/includes/loader.php';

		if ( ! ( $GLOBALS['wp_rewrite'] instanceof WP_Rewrite ) ) {
			$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		}

		if ( ! isset( $GLOBALS['wp_roles'] ) || ! ( $GLOBALS['wp_roles'] instanceof WP_Roles ) ) {
			$GLOBALS['wp_roles'] = new WP_Roles();
		}

		// Bootstrap EO.
		require dirname( __FILE__ ) . '/../../../event-organiser/event-organiser.php';
		eventorganiser_install();

		// Then load BPEO.
		require dirname( __FILE__ ) . '/../../bp-event-organiser.php';
	}
	tests_add_filter( 'muplugins_loaded', '_bootstrap_bp' );

	require getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/bootstrap.php';

	// Load the BP test files.
	require BP_TESTS_DIR . '/includes/testcase.php';

	// Load EO's factory.
	require $eo_dir . '/framework/factory.php';

	// Load our own testcase.
	require dirname( __FILE__ ) . '/testcase.php';

endif;
