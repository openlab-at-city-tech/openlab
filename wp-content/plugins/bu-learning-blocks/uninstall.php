<?php
/**
 * Uninstalls plugin options and custom post types.
 *
 * @package BU Learning Blocks
 *
 * @since Version 0.0.6
 **/

/* Exit if plugin delete hasn't been called */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$bulb_options = array(
	'bulb_active',
	'bulb_cpt_install',
);
foreach ( $bulb_options as $option ) {
	if ( get_option( $option ) ) {
		delete_option( $option );
	}
}

$bulb_cpt_args  = array(
	'post_type'      => 'bulb-learning-module',
	'posts_per_page' => -1,
);
$bulb_cpt_posts = get_posts( $bulb_cpt_args );
if ( ! empty( $bulb_cpt_posts ) ) {
	foreach ( $bulb_cpt_posts as $bulb_post ) {
		wp_delete_post( $bulb_post->ID, false );
	}
}
