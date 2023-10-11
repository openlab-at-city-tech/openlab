<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

/**
 * Include environment-specific constants, such as DB connection data
 */
define( 'DO_BOOTSTRAP', true );
if ( !defined( 'DB_NAME' ) ) {
	include( dirname( __FILE__ ) . '/env.php' );
}

// Version of the OpenLab. Used for asset versioning and cache busting.
define( 'OL_VERSION', '1.7.71' );

if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
	require __DIR__ . '/db-settings.php';
}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define( 'WPLANG', 'en_US' );

define('WP_ALLOW_MULTISITE', true);
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
$base = '/';
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
define ( 'BP_BLOGS_SLUG', 'sites' );
define ( 'BP_FORUMS_SLUG', 'discussion' );
define( 'BP_GROUP_DOCUMENTS_SLUG', 'files' );
define( 'BP_USE_WP_ADMIN_BAR', true );

define( 'NGG_JQUERY_CONFLICT_DETECTION', false );

// Don't let Neve show its onboarding panel.
define( 'TI_ONBOARDING_DISABLED', true );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define( 'WP_DEFAULT_THEME', 'twentytwelve' );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
