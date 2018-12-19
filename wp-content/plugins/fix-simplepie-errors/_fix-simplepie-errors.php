<?php
/** 
 *  Plugin Name:    Fix - SimplePie Errors
 *  Plugin URI:     https://github.com/michaeluno/_fix-simplepie-errors
 *  Description:    Fixes an incompatibility issue of the built-in library, SimplePie 1.3.1, with PHP 7.1 or above.
 *  Author:         Michael Uno
 *  Author URI:     http://en.michaeluno.jp/
 *  Version:        1.0.1
 */

if ( class_exists( 'SimplePie' ) ) {
    return;
}

class Registry_FixSimplePieErrors {

    static public $sFilePath = __FILE__; 
    static public $sDirPath  = '';
    
    static public function setUp() {
        self::$sDirPath = dirname( self::$sFilePath );
    }
}
Registry_FixSimplePieErrors::setUp();


include( dirname( __FILE__ ) . '/include/class-simplepie.php' );