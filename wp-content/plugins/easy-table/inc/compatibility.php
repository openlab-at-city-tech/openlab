<?php 
if(!function_exists('shortcode_exists')) {
	function shortcode_exists( $tag ) {
        global $shortcode_tags;
        return array_key_exists( $tag, $shortcode_tags );
	}
}