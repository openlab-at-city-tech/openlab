<?php

namespace FileBird\Blocks;

class Helpers {
    public static function encrypt( $string ) {
        $encrypt_key = NONCE_KEY;
        return openssl_encrypt( $string, 'AES-128-ECB', $encrypt_key );
      }
    public static function decrypt( $string ) {
        $encrypt_key = NONCE_KEY;
        return openssl_decrypt( $string, 'AES-128-ECB', $encrypt_key );
      }
}