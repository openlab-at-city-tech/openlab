<?php

$GLOBALS['wp_tests_options'] = array(
    'active_plugins' => array(
	'wds-citytech/wds-citytech.php',
	'buddypress/bp-loader.php',
	'genesis-connect-for-buddypress/genesis-connect.php',
    ),
);

define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );

//$_SERVER['REQUEST_URI'] = '/';

require getenv( 'BP_TESTS_DIR' ) . '/includes/bootstrap.php';

// Let me run on BP < 1.7
if ( ! function_exists( 'buddypress' ) ) :
	function buddypress() {
		global $bp;
		return $bp;
	}
endif;
