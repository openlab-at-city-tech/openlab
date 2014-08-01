<?php
/*
Plugin Name: DLBlock
Version: 0.1-alpha
Description: Block downloads
Author: Boone Gorges
Author URI: http://boone.gorg.es
Text Domain: dlblock
Domain Path: /languages
*/

define( 'DLBLOCK_DIR', plugin_dir_path( __FILE__ ) );

function dlblock_admin_init() {
	$blog_public = dlblock_blog_public();

	if ( $blog_public >= 0 ) {
		return;
	}
}
add_action( 'admin_init', 'dlblock_admin_init' );

function dlblock_blog_public() {
	return floatval( get_option( 'blog_public' ) );
}

/**
 * Test whether the attachment upload directory is protected.
 *
 * We create a dummy file in the directory, and then test to see
 * whether we can fetch a copy of the file with a remote request.
 *
 * @since 1.6.0
 *
 * @param bool $force_check True to skip the cache.
 * @return True if protected, false if not.
 */
function dlblock_check_is_protected( $force_check = true ) {
	global $is_apache;

	// Fall back on cached value if it exists
	if ( ! $force_check ) {
		$is_protected = get_option( 'dlblock_protection' );
		if ( '' === $is_protected ) {
			return (bool) $is_protected;
		}
	}

	// This should get abstracted out
	$uploads = wp_upload_dir();
	$test_file = $uploads['basedir'] . DIRECTORY_SEPARATOR . 'test.html';
	$test_text = 'This is a test file for DLBlock. Please do not remove.';

	if ( ! file_exists( $test_file ) ) {
		// Create an .htaccess, if we can
		if ( $is_apache ) {
			dlblock_create_htaccess_file( $uploads['basedir'] );
		}

		// Make a dummy file
		file_put_contents( $uploads['basedir'] . DIRECTORY_SEPARATOR . 'test.html', $test_text );
	}

	$test_url = $uploads['baseurl'] . '/test.html';
	$r = wp_remote_get( $test_url );

	// If the response body includes our test text, we have a problem
	$is_protected = true;
	if ( ! is_wp_error( $r ) && $r['body'] === $test_text ) {
		$is_protected = false;
	}

	// Cache
	$cache = $is_protected ? '1' : '0';
	bp_update_option( 'dlblock_protection', $cache );
	var_dump( $is_protected );

	return $is_protected;
}

function dlblock_create_htaccess_file( $dir ) {
	if ( ! file_exists( 'insert_with_markers' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/misc.php' );
	}

	$site_url = parse_url( site_url() );
	$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '/';

	$rules = dlblock_generate_htaccess_rules( $path );

	insert_with_markers( trailingslashit( $dir ) . '.htaccess', 'DLBlock', $rules );
}

function dlblock_generate_htaccess_rules( $rewrite_base = '/' ) {
	$rules = array(
		'RewriteEngine On',
		'RewriteBase ' . $rewrite_base,
		'RewriteRule (.+) ?dlb_download=$1 [R=302,NC]',
	);

	return $rules;
}

function dlblock_process_download_request() {
	if ( empty( $_GET['dlb_download'] ) ) {
		return;
	}

	$file = urldecode( $_GET['dlb_download'] );

	if ( ! dlblock_user_has_access( $file ) ) {
		// @todo send forbidden headers
		return;
	}

	$uploads = wp_upload_dir();
	$path = $uploads['basedir'] . '/' . $file;

	// x-sendfile
	if ( apache_mod_loaded( 'mod_xsendfile' ) ) {
		require DLBLOCK_DIR . 'lib/xSendfile/xSendfile.php';
		\XSendfile\XSendfile::xSendfile( $path );
		exit;
	} else {
		$headers = dlblock_generate_headers( $path );

		foreach( $headers as $name => $field_value ) {
			@header("{$name}: {$field_value}");
		}

		readfile( $path );
	}
}
add_action( 'template_redirect', 'dlblock_process_download_request' );

/**
 * Generate download headers
 *
 * @since 1.4
 * @param string $filename Full path to file
 * @return array Headers in key=>value format
 */
function dlblock_generate_headers( $filename ) {
	// Disable compression
	if ( function_exists( 'apache_setenv' ) ) {
		@apache_setenv( 'no-gzip', 1 );
	}
	@ini_set( 'zlib.output_compression', 'Off' );

	// @todo Make this more configurable
	$headers = wp_get_nocache_headers();

	// Content-Disposition
	$filename_parts = pathinfo( $filename );
	$headers['Content-Disposition'] = 'attachment; filename="' . $filename_parts['basename'] . '"';

	// Content-Type
	$filetype = wp_check_filetype( $filename );
	$headers['Content-Type'] = $filetype['type'];

	// Content-Length
	$filesize = filesize( $filename );
	$headers['Content-Length'] = $filesize;

	return $headers;
}

function dlblock_user_has_access( $file ) {
	$user_has_access = true;

	// @todo filter and separate MPO stuff
	$blog_public = dlblock_blog_public();

	switch ( $blog_public ) {
		case -1 :
			$user_has_access = is_user_logged_in();
			break;

		case -2 :
			$user_has_access = is_user_member_of_blog( get_current_user_id(), get_current_blog_id() );
			break;

		case -3 :
			$user_has_access = current_user_can( 'manage_options' );
			break;
	}

	return $user_has_access;
}

/** Enabling/disabling protection ********************************************/

function dlblock_update_blog_public( $old_value, $value ) {
	if ( $old_value == $value ) {
		return;
	}

	// @todo Apache check
	$uploads = wp_upload_dir();

	$htaccess_path = $uploads['basedir'] . '/.htaccess';
	if ( floatval( $value ) < 0 ) {
		dlblock_create_htaccess_file( $uploads['basedir'] );
	} else if ( file_exists( $htaccess_path ) ) {
		$htaccess_contents = file_get_contents( $htaccess_path );
		$htaccess_contents = preg_replace( '|# BEGIN DLBlock.*?END DLBlock|s', '', $htaccess_contents );
		if ( '' == trim( $htaccess_contents ) ) {
			@unlink( $htaccess_path );
		} else {
			file_put_contents( $htaccess_path, $htaccess_contents );
		}
	}

}
add_action( 'update_option_blog_public', 'dlblock_update_blog_public', 10, 2 );
