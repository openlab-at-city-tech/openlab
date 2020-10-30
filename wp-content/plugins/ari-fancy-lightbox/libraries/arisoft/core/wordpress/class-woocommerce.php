<?php
namespace Ari\Wordpress;

class Woocommerce {
    static public function is_installed() {
        return class_exists( 'Woocommerce' );
    }

    static public function get_version() {
        if ( ! self::is_installed() )
            return false;

        global $woocommerce;

        if ( is_object( $woocommerce ) && isset( $woocommerce->version ) )
            return $woocommerce->version;

        return false;
    }
}
