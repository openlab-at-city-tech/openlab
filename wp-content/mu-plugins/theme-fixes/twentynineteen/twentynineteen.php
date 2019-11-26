<?php

/**
 * Enqueue custom scripts.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_script( 'openlab-twentynineteen-fixes', content_url( 'mu-plugins/theme-fixes/twentynineteen/twentynineteen.js' ), array( 'jquery' ) );
		wp_enqueue_style( 'openlab-twentynineteen-print', content_url( 'mu-plugins/theme-fixes/twentynineteen/print.css', [], null, 'print' ) );
	}
);
