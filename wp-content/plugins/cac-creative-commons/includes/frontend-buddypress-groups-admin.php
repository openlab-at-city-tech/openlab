<?php
/**
 * Frontend integration to a BuddyPress group's "Manage > Details" page.
 *
 * @package cac-creative-commons
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/frontend.php';

// Enqueue chooser assets.
add_action( 'wp_enqueue_scripts', function() {
	require_once CAC_CC_DIR . '/includes/admin-functions.php';
	cac_cc_register_scripts();
} );

/**
 * Save license for BuddyPress groups.
 *
 * @since 0.1.0
 */
function cac_cc_group_save() {
	if ( ! isset( $_POST['cac-cc-nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['cac-cc-nonce'], 'cac-cc-license' ) ) {
		return;
	}

	$group_id = bp_is_group() ? bp_get_current_group_id() : bp_get_new_group_id();

	// Update license.
	groups_update_groupmeta( $group_id, 'cac_cc_license', strip_tags( $_POST['cac-cc-license'] ) );
}
add_action( 'groups_group_details_edited', 'cac_cc_group_save', 0 );
add_action( 'groups_create_group_step_save_group-settings', 'cac_cc_group_save', 0 );

/**
 * Output markup on a group's "Manage > Edit Details" page.
 *
 * @since 0.1.0
 */
function cac_cc_group_edit_details_content() {
	add_filter( 'option_cac_cc_default', '_cac_cc_fetch_group_license' );
	add_filter( 'default_option_cac_cc_default', '_cac_cc_fetch_group_license' );

	cac_cc_get_template_part( 'bp-group-edit-details' );

	remove_filter( 'option_cac_cc_default', '_cac_cc_fetch_group_license' );
	remove_filter( 'default_option_cac_cc_default', '_cac_cc_fetch_group_license' );
}
add_action( 'groups_custom_group_fields_editable', 'cac_cc_group_edit_details_content', 0 );

/**
 * Output markup during the group creation "Settings" step.
 *
 * @since 0.1.0
 */
function cac_cc_group_create_content() {
	cac_cc_get_template_part( 'bp-group-create' );
}
add_action( 'bp_after_group_settings_creation_step', 'cac_cc_group_create_content', 0 );