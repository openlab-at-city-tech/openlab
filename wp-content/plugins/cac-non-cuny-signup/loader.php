<?php
/*
Plugin Name: CAC Non-CUNY Signup
Description: Allows admin to enter validation codes for non-CUNY email addresses to register
Version: 1.0
Author: Boone Gorges
*/

function cac_non_cuny_signup_loader() {
	require_once __DIR__ . '/inc/schema.php';
	require_once __DIR__ . '/inc/helpers.php';
	require_once __DIR__ . '/inc/signup.php';
	require_once __DIR__ . '/inc/ajax.php';

	CAC_NCS_Schema::init();
}
add_action( 'bp_include', 'cac_non_cuny_signup_loader' );
