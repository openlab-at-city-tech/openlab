<?php

/**
 * Connected Groups functionality.
 */

require __DIR__ . '/class-openlab-group-connection.php';
require __DIR__ . '/class-openlab-group-connection-invitation.php';

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
 * Checks whether a user can initiate connections for a group.
 *
 * @param int $user_id  ID of the user. Defaults to logged-in user.
 * @param int $group_id ID of the group. Defaults to current group.
 * @return bool
 */
function openlab_user_can_initiate_group_connections( $user_id = null, $group_id = null ) {
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	if ( ! $group_id ) {
		return false;
	}

    return user_can( $user_id, 'bp_moderate' ) || groups_is_user_admin( $user_id, $group_id );
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
 * Register assets.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_register_script(
			'openlab-group-connections',
			get_stylesheet_directory_uri() . '/js/group-connections.js',
			[ 'jquery', 'jquery-ui-autocomplete', 'wp-backbone' ],
			OL_VERSION,
			true
		);
	}
);

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

/**
 * AJAX callback for group search.
 *
 * @return void
 */
add_action(
	'wp_ajax_openlab_connection_group_search',
	function() {
		global $wpdb;

		if ( ! isset( $_GET['term'] ) ) {
			echo wp_json_encode( [] );
			die;
		}

		$term = wp_unslash( $_GET['term'] );

		$group_format_callback = function( $group ) {
			return [
				'groupName'   => $group->name,
				'groupUrl'    => bp_get_group_permalink( $group ),
				'groupAvatar' => bp_get_group_avatar( [ 'html' => false ], $group ),
				'groupId'     => $group->id,
			];
		};

		$retval = [];
		if ( filter_var( $term, FILTER_VALIDATE_URL ) ) {
			$group_base = bp_get_groups_directory_permalink();
			if ( str_starts_with( $term, $group_base ) ) {
				$url_tail   = str_replace( $group_base, '', $term );
				$tail_parts = explode( '/', $url_tail );
				$group_slug = $tail_parts[0];

				$group_id = BP_Groups_Group::group_exists( $group_slug );
				if ( $group_id ) {
					$group    = groups_get_group( $group_id );
					$retval[] = $group_format_callback( $group );
				}
			}
		} else {
			$groups = groups_get_groups(
				[
					'search_terms' => $term,
					'exclude'      => [ bp_get_current_group_id() ],
				]
			);

			$retval = array_map( $group_format_callback, $groups['groups'] );
		}

		echo wp_json_encode( $retval );
		die;

	}
);

/**
 * Catches Make a Connection invitation requests.
 */
add_action(
	'bp_actions',
	function() {
		if ( empty( $_POST['openlab-connection-invitations-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'openlab-connection-invitations', 'openlab-connection-invitations-nonce' );

		if ( ! openlab_is_connections_enabled_for_group() || ! openlab_user_can_initiate_group_connections() ) {
			return;
		}

		if ( empty( $_POST['invitation-group-ids'] ) ) {
			return;
		}

		$group_ids = array_map( 'intval', $_POST['invitation-group-ids'] );

		$retval = [];
		foreach ( $group_ids as $group_id ) {
			$retval[ $group_id ] = openlab_send_connection_invitation(
				[
					'inviter_group_id' => bp_get_current_group_id(),
					'invitee_group_id' => $group_id,
					'inviter_user_id'  => bp_loggedin_user_id(),
				]
			);
		}

		foreach ( $retval as $group_id => $status ) {
			$group = groups_get_group( $group_id );

			if ( $status['success'] ) {
				bp_core_add_message( sprintf( 'Successfully sent invitation to the group "%s".', $group->name ), 'success' );
			} else {
				switch ( $status['status'] ) {
					case 'invitation_exists' :
						bp_core_add_message( sprintf( 'An invitation for the group "%s" already exists.', $group->name ), 'warning' );
						break;

					default :
						bp_core_add_message( sprintf( 'Could not send invitation to the group "%s".', $group->name ), 'error' );
						break;
				}
			}
		}
	}
);

/**
 * Sends a connection invitation.
 *
 * @param array $args {
 *   Array of arguments.
 *   @var int $inviter_group_id ID of the group initiating the invitation.
 *   @var int $invitee_group_id ID of the group receiving the invitation.
 *   @var int $inviter_user_id  ID of the user initiating the invitation.
 * }
 * @return {
 *   @var bool   $success Whether the invitation was sent.
 *   @var string $status  Status code. 'success', 'invitation_exists', 'connection_exists', 'failure'.
 * }
 */
function openlab_send_connection_invitation( $args ) {
	global $wpdb;

	$retval = [
		'success' => false,
		'status'  => 'failure',
	];

	// First check for an existing invitation.
	if ( OpenLab_Group_Connection_Invitation::invitation_exists( $args['inviter_group_id'], $args['invitee_group_id'] ) ) {
		$retval['status'] = 'invitation_exists';
		return $retval;
	}

	$invitation = new OpenLab_Group_Connection_Invitation();
	$invitation->set_inviter_group_id( $args['inviter_group_id'] );
	$invitation->set_invitee_group_id( $args['invitee_group_id'] );
	$invitation->set_inviter_user_id( $args['inviter_user_id'] );

	$saved = $invitation->save();

	if ( $saved ) {
		$retval['success'] = true;
		$retval['status']  = 'success';
	}

	return $retval;
}

/**
 * Creates database tables.
 *
 * @return string[]
 */
function openlab_create_connection_tables() {
	global $wpdb;

	$sql = array();

	$charset_collate = $wpdb->get_charset_collate();

	$table_prefix = $wpdb->get_blog_prefix( get_main_site_id() );

	$invitation_table_name = "{$table_prefix}openlab_connection_invitations";
	$connection_table_name = "{$table_prefix}openlab_connections";
	$metadata_table_name   = "{$table_prefix}openlab_connection_metadata";

	$sql[] = "CREATE TABLE {$invitation_table_name} (
				invitation_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				inviter_user_id bigint(20) NOT NULL,
				inviter_group_id bigint(20) NOT NULL,
				invitee_group_id bigint(20) NOT NULL,
				connection_id bigint(20),
				date_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				date_accepted datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				KEY inviter_user_id (inviter_user_id),
				KEY inviter_group_id (inviter_group_id),
				KEY invitee_group_id (invitee_group_id)
			) {$charset_collate};";

	$sql[] = "CREATE TABLE {$connection_table_name} (
				connection_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				group_1_id bigint(20) NOT NULL,
				group_2_id bigint(20) NOT NULL,
				date_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				KEY connection_id (connection_id),
				KEY group_1_id (group_1_id),
				KEY group_2_id (group_2_id)
			) {$charset_collate};";

	$sql[] = "CREATE TABLE {$metadata_table_name} (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				connection_id bigint(20) NOT NULL,
				group_id bigint(20) NOT NULL,
				meta_key varchar(255) NOT NULL,
				meta_value longtext,
				UNIQUE KEY idx_group_connection (group_id,connection_id),
				KEY meta_key (meta_key)
			) {$charset_collate};";

	if ( ! function_exists( 'dbDelta' ) ) {
		require_once ABSPATH . '/wp-admin/includes/upgrade.php';
	}

	return dbDelta( $sql );
}
