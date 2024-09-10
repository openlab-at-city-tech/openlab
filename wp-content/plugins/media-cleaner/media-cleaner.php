<?php
/*
Plugin Name: Media Cleaner
Plugin URI: https://meowapps.com
Description: Clean your WordPress! Eliminate unused and broken media files. For a faster, and better website.
Version: 6.7.8
Author: Jordy Meow
Author URI: https://jordymeow.com
Text Domain: media-cleaner

Originally developed for two of my websites:
- Jordy Meow (https://offbeatjapan.org)
- Haikyo (https://haikyo.org)
*/

if ( !defined( 'WPMC_VERSION' ) ) {
  define( 'WPMC_VERSION', '6.7.8' );
  define( 'WPMC_PREFIX', 'wpmc' );
  define( 'WPMC_DOMAIN', 'media-cleaner' );
  define( 'WPMC_ENTRY', __FILE__ );
  define( 'WPMC_PATH', dirname( __FILE__ ) );
  define( 'WPMC_URL', plugin_dir_url( __FILE__ ) );
  define( 'WPMC_ITEM_ID', 987 );
}

require_once( 'classes/init.php');

?>
