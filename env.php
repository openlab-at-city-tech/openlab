<?php

/**
 * WordPress
 */
define( 'DB_NAME', 'openlab_citytech' );
define( 'DB_USER', 'openlab' );
define( 'DB_PASSWORD', '6npBUXKw' );
define( 'DB_HOST', 'localhost' );

/**
 * bbPress (BP version)
 *
 * These should be the same as your WP info
 */
define( 'BBDB_NAME', 'openlab_citytech' );
define( 'BBDB_USER', 'openlab' );
define( 'BBDB_PASSWORD', '6npBUXKw' );
define( 'BBDB_HOST', 'localhost' );

/**
 * Other environment specific constants
 */
define( 'WP_DEBUG', false );
define( 'DOMAIN_CURRENT_SITE', 'openlab.citytech.cuny.edu' );
define( 'PATH_CURRENT_SITE', '/' );

@ini_set('log_errors','On');
@ini_set('display_errors','Off');
@ini_set('error_log','/usr/home/openlab/public_html/php_error.log');

?>
