<?php
/** 
 * The base configurations of bbPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys and bbPress Language. You can get the MySQL settings from your
 * web host.
 *
 * This file is used by the installer during installation.
 *
 * @package bbPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for bbPress */
define( 'BBDB_NAME', 'openlab_devorg' );

/** MySQL database username */
define( 'BBDB_USER', 'openlab_2' );

/** MySQL database password */
define( 'BBDB_PASSWORD', 'gDymUyFY' );

/** MySQL hostname */
define( 'BBDB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'BBDB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'BBDB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/bbpress/ WordPress.org secret-key service}
 *
 * @since 1.0
 */
define( 'BB_AUTH_KEY', '69d{;}zQ-#*k#lLV-h)4A;}|2G+tHj(x nN/^AZi(7Uc*)l.6ZE|i)x<Mh~i~)c>' );
define( 'BB_SECURE_AUTH_KEY', 'qb7;_QT2^7p%PYv9(Nd6%%c:I5+a$-m;|!xeBWeG%hg,]J2y>1o`l(O}so<zNfUp' );
define( 'BB_LOGGED_IN_KEY', '$5oWC#{z5nD6-~NqfX;!$Gv2gZ2D*mGJ4TeI]}>S5e!V+T<+|.(2-314 l]&<09S' );
define( 'BB_NONCE_KEY', '^8B<lh6nw-{I7t44$a;O,H-m]H[p-g(G`_%cl<1|:KIi?:r63SeSb?@+Jx1N-4N3' );
/**#@-*/

/**
 * bbPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$bb_table_prefix = 'wp_bb_';

/**
 * bbPress Localized Language, defaults to English.
 *
 * Change this to localize bbPress. A corresponding MO file for the chosen
 * language must be installed to a directory called "my-languages" in the root
 * directory of bbPress. For example, install de.mo to "my-languages" and set
 * BB_LANG to 'de' to enable German language support.
 */
define( 'BB_LANG', '' );

$bb->custom_user_table = 'wp_users';
$bb->custom_user_meta_table = 'wp_usermeta';

$bb->uri = 'http://openlabdev.org/wp-content/plugins/buddypress/bp-forums/bbpress/';
$bb->name = ' Forums';
$bb->wordpress_mu_primary_blog_id = 1;

define('BB_AUTH_SALT', 'x.GH/h/NxLF+(a$|}@,@NgUJN-|[RB@,WoWz|sgh9(.yC>mNzUp`W_u){-Lz|xy~');
define('BB_LOGGED_IN_SALT', '#]A{_#)YC97#:E87*. )|#+z7:~H1>;=TxY!6}Dj^T*xp3mK~Wqu!]aBMQ2Cd/a|');
define('BB_SECURE_AUTH_SALT', 'uOTS=$TC*)fm^R*CV#P/HCi=Yv;Gd_,lR;(B^2DVMwG|d][~E-4F|IL#j50$X(9#');

define('WP_AUTH_COOKIE_VERSION', 2);

?>