<?php

add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_script( 'openlab-koji', content_url( 'mu-plugins/theme-fixes/koji/koji.js', array( 'jquery' ) ) );
	}
);
