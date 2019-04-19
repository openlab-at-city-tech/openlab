<?php

function fv_player_gutenberg() {
  global $fv_wp_flowplayer_ver;
  wp_register_script( 'fv-player-gutenberg', flowplayer::get_plugin_url().'/js/gutenberg.js', array( 'wp-blocks', 'wp-element', 'wp-components' ), $fv_wp_flowplayer_ver );
  
  if( function_exists('register_block_type') ) {
    register_block_type( 'fv-player-gutenberg/basic', array(
      'editor_script' => 'fv-player-gutenberg',
    ) );
  }
}
add_action( 'init', 'fv_player_gutenberg' );
