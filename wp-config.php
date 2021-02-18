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
define( 'OL_VERSION', '1.7.49' );

if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
	require __DIR__ . '/db-settings.php';
}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '69d{;}zQ-#*k#lLV-h)4A;}|2G+tHj(x nN/^AZi(7Uc*)l.6ZE|i)x<Mh~i~)c>');
define('SECURE_AUTH_KEY',  'qb7;_QT2^7p%PYv9(Nd6%%c:I5+a$-m;|!xeBWeG%hg,]J2y>1o`l(O}so<zNfUp');
define('LOGGED_IN_KEY',    '$5oWC#{z5nD6-~NqfX;!$Gv2gZ2D*mGJ4TeI]}>S5e!V+T<+|.(2-314 l]&<09S');
define('NONCE_KEY',        '^8B<lh6nw-{I7t44$a;O,H-m]H[p-g(G`_%cl<1|:KIi?:r63SeSb?@+Jx1N-4N3');
define('AUTH_SALT',        'x.GH/h/NxLF+(a$|}@,@NgUJN-|[RB@,WoWz|sgh9(.yC>mNzUp`W_u){-Lz|xy~');
define('SECURE_AUTH_SALT', 'uOTS=$TC*)fm^R*CV#P/HCi=Yv;Gd_,lR;(B^2DVMwG|d][~E-4F|IL#j50$X(9#');
define('LOGGED_IN_SALT',   '#]A{_#)YC97#:E87*. )|#+z7:~H1>;=TxY!6}Dj^T*xp3mK~Wqu!]aBMQ2Cd/a|');
define('NONCE_SALT',       '1p<2`G4Zst14!:l~$ I|XF_83z|G|Uo~WmESe*x#x`GIF@x`Fcv/4s7yZ<[vL]P4');

/**#@-*/

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define( 'WP_DEFAULT_THEME', 'twentytwelve' );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
