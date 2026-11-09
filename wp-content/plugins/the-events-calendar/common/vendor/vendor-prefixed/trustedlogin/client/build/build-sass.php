<?php
/**
 * Build the SCSS file for the plugin based on the plugin slug passed through the environment variable.
 *
 * @link https://docs.trustedlogin.com/Client/css-namespacing For usage information.
 *
 * phpcs:disable
 */

$slug      = env( 'TRUSTEDLOGIN_SLUG' );
$file_name = $slug . '.scss';

$path  = rtrim( dirname( __DIR__ ), '/\\' ); // Path to trustedlogin-client/, untrailingslashit().
$path .= '/src/assets/src/';

file_put_contents( $path . $file_name, '$namespace: "' . $slug . '" !default;' );
