<?php

/**
 * bp_group_documents_front_cssjs()
 *
 * This function will enqueue the components css and javascript files
 * only when the front group documents page is displayed
 */
function bp_group_documents_front_cssjs() {
	global $bp;

	//if we're on a group page
	if ( $bp->current_component == $bp->groups->slug ) {
		wp_enqueue_script( 'bp-group-documents', plugins_url( '/buddypress-group-documents/js/general.js' ),array('jquery') );
		wp_enqueue_style('bp-group-documents',WP_PLUGIN_URL . '/buddypress-group-documents/css/style.css');

		switch( BP_GROUP_DOCUMENTS_THEME_VERSION ){
			case '1.1':
				wp_enqueue_style('bp-group-documents-1.1', WP_PLUGIN_URL . '/buddypress-group-documents/css/11.css');
			break;
			case '1.2':
			//	wp_enqueue_style('bp-group-documents-1.2', WP_PLUGIN_URL . '/buddypress-group-documents/css/12.css');
			break;
		}
	}
}
add_action( 'template_redirect', 'bp_group_documents_front_cssjs', 1 );

/**
 * bp_group_documents_admin_cssjs()
 *
 * This function will enqueue the css and js files for the admin back-end
 */
function bp_group_documents_admin_cssjs() {
	wp_enqueue_style('bp-group-documents-admin',WP_PLUGIN_URL . '/buddypress-group-documents/css/admin.css');
	wp_enqueue_script( 'bp-group-documents-admin', WP_PLUGIN_URL . '/buddypress-group-documents/js/admin.js', array('jquery') );
}
add_action('admin_init','bp_group_documents_admin_cssjs');
		
?>
