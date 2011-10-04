<?php

function bp_group_management_group_action_buttons( $id, $group ) {
?>
  <p>
	    	<a class="button" href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=<?php echo $id; ?>"><?php _e( 'Members', 'bp-group-management' ) ?></a> 
	    	<a class="button-secondary action" href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=delete&amp;id=<?php echo $id; ?>"><?php _e( 'Delete', 'bp-group-management' ) ?></a>
	    	<a class="button-secondary action" href="<?php echo bp_get_group_permalink( $group ); ?>admin"><?php _e( 'Admin', 'bp-group-management' ) ?></a> 
	    	<a class="button-secondary action" href="<?php echo bp_get_group_permalink( $group ); ?>"><?php _e('Visit', 'bp-group-management'); ?></a>
	    </p>
<?php
}


/* The next few functions recreate core BP functionality, minus the check for $bp->is_item_admin and with some tweaks to the returned values */
function bp_group_management_ban_member( $user_id, $group_id ) {
	global $bp;
		
	$member = new BP_Groups_Member( $user_id, $group_id );

	do_action( 'groups_ban_member', $group_id, $user_id );

	if ( !$member->ban() )
		return false;

	update_usermeta( $user_id, 'total_group_count', (int)$total_count - 1 );
	
	return true;
}

function bp_group_management_unban_member( $user_id, $group_id ) {
	global $bp;

	$member = new BP_Groups_Member( $user_id, $group_id );

	do_action( 'groups_unban_member', $group_id, $user_id );

	return $member->unban();
}

function bp_group_management_promote_member( $user_id, $group_id, $status ) {
	global $bp;

	$member = new BP_Groups_Member( $user_id, $group_id );

	do_action( 'groups_promote_member', $group_id, $user_id, $status );

	return $member->promote( $status );
}

function bp_group_management_delete_group( $group_id ) {
	global $bp;
	
	$group = new BP_Groups_Group( $group_id );

	if ( !$group->delete() )
		return false;

	/* Delete all group activity from activity streams */
	if ( function_exists( 'bp_activity_delete_by_item_id' ) ) {
		bp_activity_delete_by_item_id( array( 'item_id' => $group_id, 'component' => $bp->groups->id ) );
	}

	// Remove all outstanding invites for this group
	groups_delete_all_group_invites( $group_id );

	// Remove all notifications for any user belonging to this group
	bp_core_delete_all_notifications_by_type( $group_id, $bp->groups->slug );

	do_action( 'groups_delete_group', $group_id );

	return true;
}

function bp_group_management_join_group( $group_id, $user_id = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	/* Check if the user has an outstanding invite, is so delete it. */
	if ( groups_check_user_has_invite( $user_id, $group_id ) )
		groups_delete_invite( $user_id, $group_id );

	/* Check if the user has an outstanding request, is so delete it. */
	if ( groups_check_for_membership_request( $user_id, $group_id ) )
		groups_delete_membership_request( $user_id, $group_id );

	/* User is already a member, just return true */
	if ( groups_is_user_member( $user_id, $group_id ) )
		return true;

	if ( !$bp->groups->current_group )
		$bp->groups->current_group = new BP_Groups_Group( $group_id );

	$new_member = new BP_Groups_Member;
	$new_member->group_id = $group_id;
	$new_member->user_id = $user_id;
	$new_member->inviter_id = 0;
	$new_member->is_admin = 0;
	$new_member->user_title = '';
	$new_member->date_modified = gmdate( "Y-m-d H:i:s" );
	$new_member->is_confirmed = 1;

	if ( !$new_member->save() )
		return false;

	/* Record this in activity streams */
	groups_record_activity( array(
		'user_id' => $user_id,
		'action' => apply_filters( 'groups_activity_joined_group', sprintf( __( '%s joined the group %s', 'bp-group-management'), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>' ) ),
		'type' => 'joined_group',
		'item_id' => $group_id
	) );

	/* Modify group meta */
	groups_update_groupmeta( $group_id, 'total_member_count', (int) groups_get_groupmeta( $group_id, 'total_member_count') + 1 );
	groups_update_groupmeta( $group_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );

	do_action( 'groups_join_group', $group_id, $user_id );

	return true;
}

function bp_group_management_pagination_links() {
	global $groups_template;
	$add_args = array();
	if ( $_GET['order'] )
		$add_args['order'] = $_GET['order'];
	
	$links = paginate_links( array(
			'base' => add_query_arg( array( 'grpage' => '%#%', 'num' => $groups_template->pag_num, 's' => $_REQUEST['s'], 'sortby' => $groups_template->sort_by ) ),
			'format' => '',
			'total' => ceil($groups_template->total_group_count / $groups_template->pag_num),
			'current' => $groups_template->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1,
			'add_args' => $add_args
		));
	echo $links;
}


?>