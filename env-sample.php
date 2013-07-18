<?php

/**
 * WordPress
 */
define( 'DB_NAME', '' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '' );

/**
 * bbPress (BP version)
 *
 * These should be the same as your WP info
 */
define( 'BBDB_NAME', '' );
define( 'BBDB_USER', '' );
define( 'BBDB_PASSWORD', '' );
define( 'BBDB_HOST', '' );

/**
 * Other environment specific constants
 */
define( 'IS_LOCAL_ENV', true ); // Leave this as true, except on staging and production environments
define( 'ENV_TYPE', 'local' ); // You can change this string to whatever you'd like to display as "[x] ENVIRONMENT"
define( 'WP_DEBUG', false );
define( 'DOMAIN_CURRENT_SITE', 'openlabdev.org' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'DO_SHARDB', false );

@ini_set('log_errors','On');
@ini_set('display_errors','Off');
@ini_set('error_log','/usr/home/openlab/public_html/php_error.log');
