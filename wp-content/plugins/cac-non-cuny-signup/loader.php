<?php

/*
Plugin Name: CAC Non-CUNY Signup
Description: Allows admin to enter validation codes for non-CUNY email addresses to register
Version: 1.0
Author: Boone Gorges
*/

function cac_non_cuny_signup_loader() {
	require( dirname( __FILE__ ) . '/cac-non-cuny-signup.php' );
}
add_action( 'bp_include', 'cac_non_cuny_signup_loader' );

?>