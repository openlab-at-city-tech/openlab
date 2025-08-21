<?php

set_error_handler(
	function( $errno, $errstr, $errfile, $errline ) {
		$exclude_plugins = [
			'buddypress',
			'contact-form-7',
			'gravityforms',
		];

		$exclude_strings = [
			'Function _load_textdomain_just_in_time was called',
			'Function wp_enqueue_style was called',
			'Mustache_Parser::buildTree(): Implicitly marking parameter $parent as nullable is deprecated',
			'Mustache_Engine::loadSource(): Implicitly marking parameter $cache as nullable is deprecated',
			'WPCF7_TagGenerator::add()',
		];

		$exclude_error = false;
		foreach ( $exclude_plugins as $exclude_plugin ) {
			$plugin_path = WP_PLUGIN_DIR . '/' . $exclude_plugin . '/';
			if ( false !== strpos( $errfile, $exclude_plugin ) ) {
				$exclude_error = true;
				break;
			}
		}

		foreach ( $exclude_strings as $exclude_string ) {
			if ( false !== strpos( $errstr, $exclude_string ) ) {
				$exclude_error = true;
				break;
			}
		}

		if ( $exclude_error ) {
			return true;
		}

		if ( str_contains( $errstr, 'Hook bp_uri' ) ) {
			return true;
		}

		return false;
	}
);
