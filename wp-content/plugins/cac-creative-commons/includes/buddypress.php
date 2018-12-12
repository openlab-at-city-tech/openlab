<?php
/**
 * BuddyPress integration.
 *
 * @package cac-creative-commons
 */

/**
 * Filter to enable integration into the BuddyPress groups component.
 *
 * @since 0.1.0
 *
 * @param bool $retval Defaults to false.
 */
$enable_groups = apply_filters( 'cac_cc_enable_buddypress_groups', false );

// Groups.
if ( true === $enable_groups && bp_is_groups_component() ) {
	// Group creation and settings.
	if ( is_user_logged_in() &&
		( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) ||
			bp_is_group_create() && bp_is_action_variable( 'group-settings', 1 ) )
	) {
		require __DIR__ . '/frontend-buddypress-groups-admin.php';
	}

	// Group frontend.
	if ( bp_is_group() ) {
		require __DIR__ . '/frontend-buddypress-groups.php';
	}
}

// Blog create.
if ( is_user_logged_in() && bp_is_create_blog() ) {
	require __DIR__ . '/frontend-buddypress-blog-create.php';
}