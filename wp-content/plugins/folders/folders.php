<?php
/**
 * Plugin Name: Folders
 * Description: Arrange media, pages, custom post types and posts into folders
 * Version: 1.3.6
 * Author: Steve North, Aaron Taylor (6-2 Design)
 * Author URI: http://62design.co.uk/wordpress-plugins/folders/
 */

$GLOBALS['globOptions'] = get_option('folders_settings');
$GLOBALS['folder_types'] = get_types_to_show();

function get_types_to_show() {
  global $globOptions;
  $types = array();

  if (empty($globOptions)) {
    return false;
  }

  foreach($globOptions as $key => $value) {
    if ($value == 1) {
      $types[] = str_replace('folders4', '', $key);
    }
  }
  return $types;
}

function searchForId($id, $menu) {
  if($menu) {
    foreach ($menu as $key => $val) {
      if(array_key_exists(2, $val)) {
        $stripVal = explode('=', $val[2]);
      }
      if(array_key_exists(1, $stripVal)){
        $stripVal = $stripVal[1];
      }
      if ($stripVal === $id) {
        return $key;
      }
    }
  }
}

function folders_admin_notice(){
  $screen = get_current_screen();
  $getPage = $screen->parent_file;
  if ($getPage == 'folders-settings') {
    echo '<div class="update-nag"><p><strong>Notice: </strong>Your folders will <strong>NOT</strong> show in the admin panel until items are assigned to a folder.</p></div>';
  }
}

add_action('admin_notices', 'folders_admin_notice');

/* Combine GMMediaTags */
add_action('after_setup_theme', 'init_GMMediaTags', 30);

function init_GMMediaTags() {

  if ( ! defined('ABSPATH') ) die();

  define('GMMEDIATAGSPATH', plugin_dir_path( __FILE__ ) );
  define('GMMEDIATAGSURL', plugins_url( '/' , __FILE__ ) );

  require_once( GMMEDIATAGSPATH . 'includes/GMMediaTags.class.php');

  // allow disabling plugin from another plugin or theme via filter
  if ( apply_filters('gm_mediatags_enable', true) ) {
    GMMediaTags::init();
  }

}
// Include Custom Post Types
include(plugin_dir_path( __FILE__ ).'includes/types.php');

// Include Options Page
include(plugin_dir_path( __FILE__ ).'includes/options.php');
