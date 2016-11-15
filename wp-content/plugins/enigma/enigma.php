<?php
/*
Plugin Name: Enigma
Plugin URI: https://leonax.net/
Description: Enigma encrypts any text on demand on server and decrypts in browser to avoid censorship. 
Author: Shuhai Shen
Version: 2.7
Author URI: https://leonax.net/
License: MIT
*/

add_action('init', 'enigma_init');
add_shortcode('enigma', 'enigma_process');

function enigma_init(){
  if (!is_admin()) {
    wp_enqueue_script(
      'engima_script',
      plugins_url( 'enigma.js' , __FILE__ ),
      array( 'jquery' ),
      false,
      true);
  }
}

function enigma_ord($str, $len = -1, $idx = 0, &$bytes = 0){
  if ($len === -1){
    $len = strlen(str);
  }
  $h = ord($str[$idx]);

  if ($h <= 0x7F) {
    $bytes = 1;
    return $h;
  } else if ($h < 0xC2) {
    return false;
  } else if ($h <= 0xDF && $idx < $len - 1) {
    $bytes = 2;
    return ($h & 0x1F) <<  6 | (ord($str[$idx + 1]) & 0x3F);
  } else if ($h <= 0xEF && $idx < $len - 2) {
    $bytes = 3;
    return ($h & 0x0F) << 12 | (ord($str[$idx + 1]) & 0x3F) << 6
                             | (ord($str[$idx + 2]) & 0x3F);
  } else if ($h <= 0xF4 && $idx < $len - 3) {
    $bytes = 4;
    return ($h & 0x0F) << 18 | (ord($str[$idx + 1]) & 0x3F) << 12
                             | (ord($str[$idx + 2]) & 0x3F) << 6
                             | (ord($str[$idx + 3]) & 0x3F);
  }
  return false;
}

function enigma_unicode($dec, $type) {
  $hex = dechex($dec);
  if ($type === 0) {
    return '\\u' . str_pad($hex, 4, '0', STR_PAD_LEFT);
  }
  if ($type === 1) {
    if ($dec < 256) {
      return '-' . str_pad($hex, 2, '0', STR_PAD_LEFT);
    }
    return '=' . str_pad($hex, 4, '0', STR_PAD_LEFT);
  }
}

function enigma_encode($content, $text, $ondemand) {
  if ($content === NULL || is_feed()){
    return $text;
  }
  
  if (strlen($content) === 0) {
    return "";
  }
  
  $content = do_shortcode($content);

  $encoding_type = mt_rand(0, 1);
  $idx = 0;
  $script = '';
  $len = strlen($content);
  while ($idx < $len) {
    $bytes = 0;
    $unicode = enigma_ord($content, $len, $idx, $bytes);
    $script .= enigma_unicode($unicode, $encoding_type);
    $idx += $bytes;
  }

  $divid = uniqid("engimadiv");
  $js = "<span id='$divid' data-enigmat='$encoding_type' data-enigmav='$script' data-enigmad='$ondemand'>$text</span>";

  return $js;
}

function enigma_process($attr, $content = NULL) {
  $attr = shortcode_atts(array('text' => '', 'ondemand' => 'n'), $attr);
  return enigma_encode($content, $attr['text'], $attr['ondemand']);
}

?>