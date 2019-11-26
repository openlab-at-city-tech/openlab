<?php

add_action(
	'customize_register',
	function( $wp_customize ) {
		$wp_customize->remove_control( 'lingonberry_accent_color' );
	},
	20
);
