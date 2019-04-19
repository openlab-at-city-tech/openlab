<?php

class FV_Player_Media_Browser {

  public $ajax_action_name = 'wp_ajax_load_assets';

  public function __construct($ajax_action_name) {
    $this->ajax_action_name = $ajax_action_name;
    $this->register();
  }

  function register() {
    add_action( $this->ajax_action_name, array($this, 'load_assets') );
  }

  function get_formatted_assets_data() {
    return json_decode('{"items":{"name":"Home","type":"folder","path":"Home\/","items":[{"name":"01 The Beginning.mp3","size":2117536,"type":"file","path":"Home\/01 The Beginning.mp3","link":"http:\/\/sjdua7x04ygyx.cloudfront.net\/01%20The%20Beginning.mp3"},{"name":"Fender_Bass_Guitar_Patent.jpg","size":495756,"type":"file","path":"Home\/Fender_Bass_Guitar_Patent.jpg","link":"http:\/\/sjdua7x04ygyx.cloudfront.net\/Fender_Bass_Guitar_Patent.jpg"}]}}', true);
  }

  function load_assets() {
    $json_final = $this->get_formatted_assets_data();

    wp_send_json( $json_final );
    wp_die();
  }

}