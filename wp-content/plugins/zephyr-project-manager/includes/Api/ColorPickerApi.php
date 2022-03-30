<?php
/**
* @package ZephyrProjectManager
*/

namespace Inc\Api;

if ( !defined( 'ABSPATH' ) ) {
    die;
}

class ColorPickerApi {

    public static function sanitizeColor( $value ) {
        $value = trim( $value );
        $value = strip_tags( stripslashes( $value ) );
        return $value;
    }
     
    /* Function that will check if value is a valid HEX color. */
    public static function checkColor( $value ) { 
        // Check if user insert a HEX color with #
        if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {      
            return true;
        }
        return false;
    }

}