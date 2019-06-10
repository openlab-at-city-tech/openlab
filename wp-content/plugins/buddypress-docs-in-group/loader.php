<?php
/*
Plugin Name: BuddyPress Docs In Group
Version: 1.0.1
Description: Put Group Docs back in Groups
Author: Boone Gorges
Author URI: http://boone.gorg.es
Domain Path: /languages
*/

define( 'BPDIG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BPDIG_PLUGIN_URL', plugins_url( __FILE__ ) );

function bp_docs_in_group_loader() {
	if ( ! class_exists( 'BP_Docs' ) ) {
		return;
	}

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	require BPDIG_PLUGIN_DIR . 'includes/bpdig.php';
}
add_action( 'bp_include', 'bp_docs_in_group_loader', 20 );

