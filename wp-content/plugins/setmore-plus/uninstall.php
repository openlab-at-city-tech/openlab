<?php
/**
 * Setmore Plus uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

$options = get_option( 'setmoreplus' );
if ( $options['lnt'] ) {
	delete_option( 'setmoreplus' );
	delete_option( 'setmoreplus_version' );
}
