<?php

$GLOBALS['wp_tests_options'] = array(
    'active_plugins' => array(
	'bp-include-non-member-comments/bp-include-non-member-comments.php',
	'buddypress/bp-loader.php',
	'genesis-connect-for-buddypress/genesis-connect.php',
	'wds-citytech/wds-citytech.php',
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
