<?php

if ( !class_exists( 'WP_Http' ) ) {
    include_once( ABSPATH . WPINC. '/class-http.php' );
}

class FDX1BaseShortener {
	var $name;
	
	function FDX1BaseShortener( $name ) {
		$this->name = $name;
	}
	
	function get_name() {
		return $this->name;	
	}
	
	function set_name( $name ) {
		$this->name = $name;	
	}
	
	function shorten( $link ) {
		return $link;	
	}
}