<?php // Start profiling
$wp_plugin_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : trailingslashit( ABSPATH ) . 'wp-content/plugins';
@include_once( $wp_plugin_dir . '/p3-profiler/start-profile.php' ); ?>
