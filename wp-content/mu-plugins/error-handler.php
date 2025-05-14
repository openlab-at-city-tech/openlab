<?php

// Squelch errors from specific plugins.
set_error_handler(
	function( $errno, $errstr, $errfile, $errline ) {
		$exclude_strings = [
			'WPCF7_TagGenerator::add()',
		];

		foreach ( $exclude_strings as $exclude_string ) {
			if ( str_contains( $errstr, $exclude_string ) ) {
				return true;
			}
		}

		return false;
	}
);
