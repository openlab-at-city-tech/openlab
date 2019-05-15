<?php
/**
 * Bootstrap the plugin unit testing environment.
 *
 * Edit 'active_plugins' setting below to point to your main plugin file.
 *
 * @package wordpress-plugin-tests
 */

/**
 * PLEASE NOTE - before this can be used, please checkout the latest WP TestSuite
 * by using "svn checkout http://develop.svn.wordpress.org/trunk/ testSuite" and rename
 * files "wp-config-sample.php" and "wp-tests-config-sample.php" by removing the "-sample"
 * part from it. Then update those files to contain an EMPTY database and login information,
 * as these tests will create a NEW WP INSTALLATION AND WIPE OUT EVERYTHING THERE IS IN THE DB.
 *
 * Also, please make sure to update the value of "WP_PHP_BINARY" constant to point to your PHP binary.
 */

// let whoever is listening know we're in test mode
define('PHPUnitTestMode', true);

// Activates this plugin in WordPress so it can be tested.
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array(
	  'fv-wordpress-flowplayer/flowplayer.php',
  )
);

// If the develop repo location is defined (as WP_DEVELOP_DIR), use that
// location. Otherwise, we'll just assume that this plugin is installed in a
// WordPress develop SVN checkout.

if( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
	require getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/bootstrap.php';
} else {
	require '../testSuite/tests/phpunit/includes/bootstrap.php';
}
