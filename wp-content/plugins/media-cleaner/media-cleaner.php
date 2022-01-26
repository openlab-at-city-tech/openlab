<?php
/*
Plugin Name: Media Cleaner
Plugin URI: https://meowapps.com
Description: Clean your WordPress from unused or broken media entries and files.
Version: 6.2.7
Author: Jordy Meow
Author URI: https://jordymeow.com
Text Domain: media-cleaner

Originally developed for two of my websites:
- Jordy Meow (https://offbeatjapan.org)
- Haikyo (https://haikyo.org)
*/

if ( !defined( 'WPMC_VERSION' ) ) {
  define( 'WPMC_VERSION', '6.2.7' );
  define( 'WPMC_PREFIX', 'wpmc' );
  define( 'WPMC_DOMAIN', 'media-cleaner' );
  define( 'WPMC_ENTRY', __FILE__ );
  define( 'WPMC_PATH', dirname( __FILE__ ) );
  define( 'WPMC_URL', plugin_dir_url( __FILE__ ) );
}

require_once( 'classes/init.php');

?>
