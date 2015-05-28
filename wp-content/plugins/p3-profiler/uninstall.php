<?php

if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Define the profiles path
$uploads_dir  = wp_upload_dir();
$profile_path = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'profiles';

// Unhook the profiler
update_option( 'p3-profiler_debug', false );
update_option( 'p3-profiler_debug_log', array() );

// Delete the profiles folder
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	$blogs = get_blog_list( 0, 'all' );
	foreach ( $blogs as $blog ) {
		switch_to_blog( $blog['blog_id'] );
		$uploads_dir = wp_upload_dir();
		$folder      = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR;
		p3_profiler_uninstall_delete_profiles_folder( $folder );

		// Remove any options - pre 1.3.0
		delete_option( 'p3-profiler_disable_opcode_cache' );
		delete_option( 'p3-profiler_use_current_ip' );
		delete_option( 'p3-profiler_ip_address' );
		delete_option( 'p3-profiler_cache_buster' );
		delete_option( 'p3-profiler_debug' );
		delete_option( 'p3-profiler_profiling_enabled' );

		// 1.3.0 and later
		delete_option( 'p3-profiler_version' );
		delete_option( 'p3-profiler_options' );
		delete_option( 'p3-profiler_debug_log' );
	}
	restore_current_blog();
} else {
	p3_profiler_uninstall_delete_profiles_folder( $profile_path );

	// Remove any options - pre.1.3.0
	delete_option( 'p3-profiler_disable_opcode_cache' );
	delete_option( 'p3-profiler_use_current_ip' );
	delete_option( 'p3-profiler_ip_address' );
	delete_option( 'p3-profiler_cache_buster' );
	delete_option( 'p3-profiler_debug' );
	delete_option( 'p3-profiler_profiling_enabled' );
	
	// 1.3.0 and later
	delete_option( 'p3-profiler_version' );
	delete_option( 'p3-profiler_options' );
	delete_option( 'p3-profiler_debug_log' );
}

function p3_profiler_uninstall_delete_profiles_folder( $path ) {
	if ( !file_exists( $path ) )
		return;
	$dir = opendir( $path );
	while ( ( $file = readdir( $dir ) ) !== false ) {
		if ( $file != '.' && $file != '..' ) {
			unlink( $path . DIRECTORY_SEPARATOR . $file );
		}
	}
	closedir( $dir );
	rmdir( $path );
}
