<?php

/**
 * Connected Groups functionality.
 *
 * This file contains only those items that are very specific to the OpenLab theme.
 * For full Connection functionality, see the openlab-connections plugin.
 */

/**
 * Subnav generation for Connections.
 *
 * @return string Subnav markup.
 */
function openlab_group_connections_submenu() {
    $base_url = bp_get_group_permalink( groups_get_current_group() ) . 'connections';

    $menu_list = [
        $base_url                    => 'Connected Groups',
		$base_url . '/new/'          => 'Make a Connection',
		$base_url . '/invitations/'  => 'Invitations',
    ];

	$current_item    = $base_url;
	$current_subpage = bp_action_variable( 0 );
	if ( in_array( $current_subpage, [ 'new', 'invitations' ], true ) ) {
		$current_item = $base_url . '/' . $current_subpage . '/';
	}

    return openlab_submenu_gen( $menu_list, false, $current_item );
}
