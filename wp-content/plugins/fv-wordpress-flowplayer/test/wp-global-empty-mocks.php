<?php

// let whoever is listening know we're in test mode
define('PHPUnitTestMode', true);

// whatever classes we define and mock here, we'll make a list
// of these programmatically, so as nobody will forget to add
// new classed to the list of mocked ones if it was hard-coded here
function file_get_php_classes($filepath) {
  $php_code = file_get_contents($filepath);
  $classes = get_php_classes($php_code);
  return $classes;
}

// complementary function to the one above
function get_php_classes($php_code) {
  $classes = array();
  $tokens = token_get_all($php_code);
  $count = count($tokens);
  for ($i = 2; $i < $count; $i++) {
    if (   $tokens[$i - 2][0] == T_CLASS
           && $tokens[$i - 1][0] == T_WHITESPACE
           && $tokens[$i][0] == T_STRING) {

      $class_name = $tokens[$i][1];
      $classes[] = $class_name;
    }
  }
  return $classes;
}

global $mocked_classes;
$mocked_classes = file_get_php_classes(__FILE__);

function get_calling_class() {
  global $mocked_classes;

  //get the trace
  $trace = debug_backtrace();

  // Get the class that is asking for who awoke it
  // note: edited to say "$trace[3]" instead of "$trace[1]",
  //       since we're additionally going through
  //       get_calling_class() function as well as
  //       the checkAndReturnRequestedValue() function
  //       all the way to the actually mocked global function (add_action())
  // note2: if we're coming from a class name in this mock file, we should handle that as well

  // we're calling a method of a class in this mock file, handle it as such
  if (isset($trace[2]['class']) && in_array($trace[2]['class'], $mocked_classes)) {
    $class = $trace[4]['class'];
  } else if (isset($trace[3]['class'])){
    $class = $trace[3]['class'];
  } else {
    $class = false;
  }

  // +1 to i cos we have to account for calling this function
  for ( $i=1; $i<count( $trace ); $i++ ) {
    if ( isset( $trace[$i] ) ) { // is it set?
      if ( isset( $trace[ $i ]['class'] ) && $class != $trace[ $i ]['class'] ) { // is it a different class
        return $trace[ $i ]['class'];
      }
    }
  }
}

function get_calling_mocked_global_function() {
  global $mocked_classes;

  $trace = debug_backtrace();
  // we're calling a method of a class in this mock file, handle it as such
  if (isset($trace[2]['class']) && in_array($trace[2]['class'], $mocked_classes)) {
    return $trace[2]['class'].'::'.$trace[2]['function'];
  } else {
    return $trace[2]['function'];
  }
}

function checkAndReturnRequestedValue() {
  global $mocked_classes;
  $cclass = get_calling_class();

  // no need to do anything special for non-tested classes,
  // just return nothing for them
  // note: we still want to check for return values for methods
  //       called from mocked classes located in this file
  if (substr($cclass, -4) !== 'Test' && !in_array($cclass, $mocked_classes)) {
    return;
  }

  global $testReturnValue;

  // if $testReturnValue is an array, check if we have a return value
  // at the index of the calling class's function
  if (is_array($testReturnValue)) {
    $calling_function = get_calling_mocked_global_function();
    if (isset($testReturnValue[$calling_function])) {
      $retValue = $testReturnValue[$calling_function];
    } else {
      $retValue = '';
    }
  } else {
    $retValue = $testReturnValue;
  }

  return $retValue;
}

function add_action() {
  return checkAndReturnRequestedValue();
}

function add_filter() {
  return checkAndReturnRequestedValue();
}

function apply_filters( $hook, $value ) {
  return $value;
}

function current_user_can( $capability ) {
  return false;
}

function get_option() {
  return checkAndReturnRequestedValue();
}

function home_url( $url = false ) {
  return 'https://site.com/'.$url;
}

function is_multisite() {
  return false;
}

function is_user_logged_in() {
  return false;
}

function sanitize_title( $title ) {
  return preg_replace( '~[^a-z0-9_-]~', '-', $title );
}

function site_url( $url = false ) {
  return 'https://site.com/wp/'.$url;
}

function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer ) {
  echo "Registering $handle for $src?ver=$ver footer? $in_footer\n";
}

function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
  echo "Registering $handle for $src?ver=$ver\n";
}

function wp_localize_script( $handle, $name, $data ) {
  echo "Localizing $handle with $name = ".print_r($data,true)."\n";
}

function wp_remote_get() {
  return checkAndReturnRequestedValue();
}

function is_wp_error() {
  return checkAndReturnRequestedValue();
}

function add_shortcode() {
  return checkAndReturnRequestedValue();
}

function is_admin() {
  return checkAndReturnRequestedValue();
}

function plugins_url( $value, $file = false ) {
  return $value;
}

function update_option() {
  return checkAndReturnRequestedValue();
}

function wp_parse_args() {
  return checkAndReturnRequestedValue();
}

function __($txt) {
  // always return what was given for the translation function
  return $txt;
}

function _e($txt) {
  // always echo what was given for the translation function
  echo $txt;
}

function esc_attr($value) {
  // always return what was given for the attribute escaping function
  return $value;
}

// mocks for the WPDB WordPress database manipulation class
class wpdb {

  public $prefix = '';

  public function get_charset_collate() {
    return checkAndReturnRequestedValue();
  }

  public function query() {
    return checkAndReturnRequestedValue();
  }
}

global $wpdb;
$wpdb = new wpdb();

global $fv_wp_flowplayer_ver;
$fv_wp_flowplayer_ver = '1.2.3.4';

define( 'WP_CONTENT_URL', 'https://site.com/wp-content' );
