<?php

function openlab_papercite_init() {
	global $papercite;

	if ( empty( $papercite ) ) {
		return;
	}

	if ( ! class_exists( 'Papercite' ) ) {
		return;
	}

	require __DIR__ . '/includes/class-openlab-papercite.php';

	$papercite = new OpenLab_Papercite();
}
add_action( 'init', 'openlab_papercite_init', 20 );
