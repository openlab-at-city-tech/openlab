<?php

/**
 * Connected Groups functionality.
 */

/**
 * Checks whether the Connections feature is enabled for a group.
 *
 * Defaults to true, except for Portfolios.
 *
 * @param int $group_id Group ID. Defaults to current group.
 * @return bool
 */
function openlab_is_connections_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to false in case no value is found.
	if ( ! $group_id ) {
		return false;
	}

	$is_disabled = groups_get_groupmeta( $group_id, 'openlab_connections_disabled' );

	// Empty value should default to disabled for portfolios.
	if ( '' === $is_disabled && openlab_is_portfolio( $group_id ) ) {
		$is_disabled = true;
	}

	return empty( $is_disabled );
}

/**
 * Register the Connections group extension.
 */
add_action(
	'bp_init',
	function() {
		require __DIR__ . '/class-openlab-group-connections-extension.php';
		bp_register_group_extension( 'OpenLab_Group_Connections_Extension' );
	}
);

/**
 * Subnav generation for Connections.
 *
 * @return string Subnav markup.
 */
function openlab_group_connections_submenu() {
    $base_url = bp_get_group_permalink( groups_get_current_group() ) . 'connections';

    $user_can_manage = current_user_can( 'bp_moderate' ) || groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() );

    $menu_list = [
        $base_url => 'Connected Groups'
    ];

    if ( $user_can_manage ) {
        $menu_list += [
            $base_url . '/new/'          => 'Make a Connection',
            $base_url . '/invitations/'  => 'Invitations',
        ];
    }

	$current_item    = $base_url;
	$current_subpage = bp_action_variable( 0 );
	if ( in_array( $current_subpage, [ 'new', 'invitations' ], true ) ) {
		$current_item = $base_url . '/' . $current_subpage . '/';
	}

    return openlab_submenu_gen( $menu_list, false, $current_item );
}
