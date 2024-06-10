<?php

//Change "Group" to something else
class bpass_Translation_Mangler {
	/*
	* Filter the translation string before it is displayed.
	*
	* This function will choke if we try to load it when not viewing a group page or in a group loop
	* So we bail in cases where neither of those things is present, by checking $groups_template
	*/
	static function filter_gettext( $translation, $text, $domain ) {
		global $bp, $groups_template;

		if ( empty( $groups_template->group ) && empty( $bp->groups->current_group ) ) {
			return $translation;
		}

		if ( ! empty( $groups_template->group->id ) ) {
			$group_id = $groups_template->group->id;
		} elseif ( ! empty( $bp->groups->current_group->id ) ) {
			$group_id = $bp->groups->current_group->id;
		} else {
			return $translation;
		}

		if ( isset( $_COOKIE['wds_bp_group_type'] ) && bp_is_group_create() ) {
			$grouptype = $_COOKIE['wds_bp_group_type'];
		} else {
			$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
		}

		$uc_grouptype     = ucfirst( $grouptype );
		$plural_grouptype = $grouptype . 's';
		$translations     = get_translations_for_domain( 'bp-ass' );

		switch ( $text ) {
			case 'How do you want to read this group?':
				return $translations->translate( "How do you want to read this $grouptype?" );
			break;

			case 'I will read this group on the web':
				return $translations->translate( "I will read this $grouptype on the web" );
			break;

			case 'Send all group activity as it arrives':
				return $translations->translate( "Send all $grouptype activity as it arrives" );
			break;

			case 'Your email notifications are set to %s for this group.':
				return $translations->translate( "Your email notifications are set to %s for this $grouptype." );
			break;

			case 'When new users join this group, their default email notification settings will be:':
				return $translations->translate( "When new users join this $grouptype, their default email notification settings will be:" );
			break;

			case 'No Email (users will read this group on the web - good for any group - the default)':
				return $translations->translate( "No Email (users will read this $grouptype on the web - good for any $grouptype - the default)" );
			break;

			case "Weekly Summary Email (the week's topics - good for large groups)":
				return $translations->translate( "Weekly Summary Email (the week\'s topics - good for large $plural_grouptype)" );
			break;

			case 'Daily Digest Email (all daily activity bundles in one email - good for medium-size groups)':
				return $translations->translate( "Daily Digest Email (all daily activity bundles in one email - good for medium-size $plural_grouptype)" );
			break;

			case 'New Topics Email (new topics are sent as they arrive, but not replies - good for small groups)':
				return $translations->translate( "New Topics Email (new topics are sent as they arrive, but not replies - good for small $plural_grouptype)" );
			break;

			case 'All Email (send emails about everything - recommended only for working groups)':
				return $translations->translate( "All Email (send emails about everything - recommended only for working $plural_grouptype)" );
			break;

			case 'Group Email Settings':
				return $translations->translate( "$uc_grouptype Email Settings" );
			break;

			case 'To change the email notification settings for your groups go to %s and click change for each group.':
				return $translations->translate( "To change the email notification settings for your $plural_grouptype go to %s and click change for each $grouptype." );
			break;

			case 'Send an email notice to everyone in the group':
				return $translations->translate( "Send an email notice to everyone in the $grouptype" );
			break;

			case 'You can use the form below to send an email notice to all group members.':
				return $translations->translate( "You can use the form below to send an email notice to all $grouptype members." );
			break;

			case 'Everyone in the group will receive the email -- regardless of their email settings -- so use with caution':
				return $translations->translate( "Everyone in the $grouptype will receive the email -- regardless of their email settings -- so use with caution" );
			break;

			case ' - sent from the group ':
				return $translations->translate( " - sent from the $grouptype " );
			break;

			case 'Send an email when a new member join the group.':
				return $translations->translate( "Send an email when a new member joins the $grouptype." );
			break;

			case 'Email this notice to everyone in the group':
				return $translations->translate( "Email this notice to everyone in the $grouptype" );
			break;

			case "This is a notice from the group '%s':

\"%s\"


To view this group log in and follow the link below:
%s

---------------------
":
				return $translations->translate(
					"This is a notice from the $grouptype '%s':

\"%s\"


To view this $grouptype log in and follow the link below:
%s

---------------------
"
				);
			break;

			case 'Leave Group':
				return $translations->translate( 'Leave ' . ucwords( $grouptype ) );
			break;

			case 'You successfully left the group.':
				return $translations->translate( 'You successfully left the ' . $grouptype . '.' );
			break;

			case 'You joined the group!':
				return $translations->translate( 'You joined the ' . $grouptype . '!' );
			break;
		}
		return $translation;
	}
}
add_filter( 'gettext', array( 'bpass_Translation_Mangler', 'filter_gettext' ), 10, 3 );

/**
 * Add members to wpms website if attached to bp group and they are a group member
 *
 * @todo With an updated of BP Groupblog, this should not be necssary. As it is, it adds a lot of
 *       overhead, and should be rewritten to avoid PHP warnings.
 */
//add_action('bp_actions','wds_add_group_members_2_blog');
function wds_add_group_members_2_blog() {
	global $wpdb, $user_ID, $bp;

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

	if ( $group_id = bp_get_current_group_id() ) {
		 $blog_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
	}

	if ( $user_ID != 0 && ! empty( $group_id ) && ! empty( $blog_id ) ) {
		switch_to_blog( $blog_id );
		if ( ! is_user_member_of_blog( $blog_id ) ) {
			$sql = "SELECT user_title FROM {$bp->groups->table_name}_members WHERE group_id = $group_id and user_id=$user_ID AND is_confirmed='1'";
			$rs  = $wpdb->get_results( $sql );

			if ( count( $rs ) > 0 ) {
				$user_title = '';
				foreach ( $rs as $r ) {
					$user_title = $r->user_title;
				}
				if ( $user_title == 'Group Admin' ) {
					$role = 'administrator';
				} elseif ( $user_title == 'Group Mod' ) {
					$role = 'editor';
				} else {
					$role = 'author';
				}
				add_user_to_blog( $blog_id, $user_ID, $role );
			}
		}
		restore_current_blog();
	}
}

/**
 * When a Notice is sent, send an email to all members
 */
function openlab_send_notice_email( $subject, $message ) {
	global $wpdb;

	$to = get_option( 'admin_email' );
	//$to = 'boonebgorges@gmail.com'; // for testing
	$subject = 'Message from OpenLab: ' . $subject;

	$emails = $wpdb->get_col( $wpdb->prepare( "SELECT user_email FROM $wpdb->users WHERE spam = 0" ) );

	// For testing - limits recipients to Boone
	/*
	foreach( $emails as $key => $e ) {
		if ( false === strpos( $e, 'boonebgorges' ) ) {
			unset( $emails[$key] );
		}
	}*/

	$emails = implode( ',', $emails );

	$headers = array( 'bcc:' . $emails );

	wp_mail( $to, $subject, $message, $headers );
}
add_filter( 'messages_send_notice', 'openlab_send_notice_email', 10, 2 );

/**
 * Redirect profile edit to the correct field group
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/172
 */
function openlab_redirect_to_profile_edit_group() {
	if ( bp_is_user_profile_edit() ) {
		if ( ! bp_action_variables() ) {
			 $account_type = openlab_get_user_member_type( bp_displayed_user_id() );
			if ( $account_type === 'student' ) {
				$pgroup = '2';
			} elseif ( $account_type === 'faculty' ) {
				$pgroup = '3';
			} elseif ( $account_type === 'alumni' ) {
				$pgroup = '4';
			} elseif ( $account_type === 'staff' ) {
				$pgroup = '5';
			} else {
				$pgroup = '1';
			}

			bp_core_redirect( bp_displayed_user_domain() . 'profile/edit/group/' . $pgroup . '/' );
		}
	}
}
add_action( 'bp_actions', 'openlab_redirect_to_profile_edit_group', 1 );

/**
 * Custom reordering of profile fields.
 *
 * - For Alumni, "Graduation Year" should always follow "Major Program of Study".
 */
function openlab_reorder_profile_fields( $has_profile ) {
	global $profile_template;

	$alumni_group_index  = false;
	$student_group_index = false;
	foreach ( $profile_template->groups as $group_index => $group ) {
		if ( 'Alumni' === $group->name ) {
			$alumni_group_index = $group_index;
		} elseif ( 'Student' === $group->name ) {
			$student_group_index = $group_index;
		}
	}

	if ( false !== $alumni_group_index && false !== $student_group_index ) {
		// Find the Graduation Year field.
		$gy_field = $gy_field_index = null;
		foreach ( $profile_template->groups[ $alumni_group_index ]->fields as $field_index => $field ) {
			if ( 'Graduation Year' === $field->name ) {
				$gy_field       = clone $field;
				$gy_field_index = $field_index;
			}
		}

		if ( null !== $gy_field ) {
			// Put it in the right place in the Student array.
			$mpos_field_index = null;
			foreach ( $profile_template->groups[ $student_group_index ]->fields as $field_index => $field ) {
				if ( 'Major Program of Study' === $field->name ) {
					$mpos_field_index = $field_index;
					break;
				}
			}

			if ( null !== $mpos_field_index ) {
				$sfields = $profile_template->groups[ $student_group_index ]->fields;

				// Can't make array_splice() work for some reason.
				$sfields_before = array_slice( $sfields, 0, $mpos_field_index + 1 );
				$sfields_after  = array_slice( $sfields, $mpos_field_index + 1 );
				$sfields        = array_merge( $sfields_before, array( $gy_field ), $sfields_after );
				$profile_template->groups[ $student_group_index ]->fields = $sfields;

				// Unset the original field location.
				unset( $profile_template->groups[ $alumni_group_index ]->fields[ $gy_field_index ] );
			}
		}
	}

	return $has_profile;
}
add_filter( 'bp_has_profile', 'openlab_reorder_profile_fields' );

/**
 * Add the group type to the form action of the group creation forms
 */
function openlab_group_type_in_creation_form_action( $action ) {
	if ( false === strpos( $action, '?type=' ) && isset( $_GET['type'] ) ) {
		$action = add_query_arg( 'type', $_GET['type'], $action );
	}

	return $action;
}
add_action( 'bp_get_group_creation_form_action', 'openlab_group_type_in_creation_form_action' );

/**
 * When creating a group, if you fill in the wrong details, you should be redirected with the
 * correct group type appended to the URL.
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/326
 */
function openlab_group_creation_redirect( $redirect ) {
	if ( bp_is_group_create() ) {
		if ( false === strpos( $redirect, '?type=' ) && isset( $_GET['type'] ) ) {
			$redirect = add_query_arg( 'type', $_GET['type'], $redirect );
		}
	}

	return $redirect;
}
add_filter( 'wp_redirect', 'openlab_group_creation_redirect' );

/**
 * Don't show a bbPress step during group creation.
 */
function openlab_remove_forum_step_from_group_creation() {
	$gcs = buddypress()->groups->group_creation_steps;
	if ( isset( $gcs['forum'] ) ) {
		unset( $gcs['forum'] );
	}
	buddypress()->groups->group_creation_steps = $gcs;
}

add_action( 'bp_actions', 'openlab_remove_forum_step_from_group_creation', 9 );

/**
 * Create bbPress 2.x forum for newly created groups.
 */
function openlab_create_forum_on_group_creation( $group_id, $member, $group ) {
	// Set the default forum status
	switch ( $group->status ) {
		case 'hidden':
			$status = bbp_get_hidden_status_id();
			break;
		case 'private':
			$status = bbp_get_private_status_id();
			break;
		case 'public':
		default:
			$status = bbp_get_public_status_id();
			break;
	}
	// Create the initial forum
	$forum_id = bbp_insert_forum(
		array(
			'post_parent'  => bbp_get_group_forums_root_id(),
			'post_title'   => $group->name,
			'post_content' => $group->description,
			'post_status'  => $status,
		)
	);
	bbp_add_forum_id_to_group( $group_id, $forum_id );
	bbp_add_group_id_to_forum( $forum_id, $group_id );
	// Update forum active
	groups_update_groupmeta( $group_id, '_bbp_forum_enabled_' . $forum_id, true );
	// Set forum enabled status
	$group->enable_forum = 1;
	// Save the group
	$group->save();
	bbp_repair_forum_visibility();
}

add_action( 'groups_create_group', 'openlab_create_forum_on_group_creation', 10, 3 );
/**
 * Force group forums to be active.
 *
 * This is redundant but for some reason bbPress requires it.
 */
add_filter( 'bp_get_new_group_enable_forum', '__return_true' );

/**
 * Make sure the comment-dupe data doesn't get saved in the comments activity
 */
function openlab_pre_save_comment_activity( $activity ) {
	$is_old_blog_comment = 'blogs' === $activity->component && 'new_blog_comment' === $activity->type;

	// OMG
	$is_new_blog_comment = false;
	if ( 'activity' === $activity->component && 'activity_comment' === $activity->type ) {
		$parent = bp_activity_get_specific( array( 'activity_ids' => array( $activity->secondary_item_id ) ) );
		if ( $parent['activities'] ) {
			$is_new_blog_comment = 'new_blog_post' === $parent['activities'][0]->type;
		}
	}

	if ( $is_old_blog_comment || $is_new_blog_comment ) {
		$activity->content = preg_replace( '/disabledupes\{.*\}disabledupes/', '', $activity->content );
	}
}
add_filter( 'bp_activity_before_save', 'openlab_pre_save_comment_activity', 2 );

/**
 * Feature toggling for new groups.
 */
add_filter( 'bp_docs_enable_group_create_step', '__return_false' );

/**
 * Bust the home page activity transients when new items are posted
 */
function openlab_clear_home_page_transients() {
	delete_site_transient( 'openlab_home_group_activity_items_course' );
	delete_site_transient( 'openlab_home_group_activity_items_project' );
	delete_site_transient( 'openlab_home_group_activity_items_club' );
	delete_site_transient( 'openlab_home_group_activity_items_portfolio' );
}
add_action( 'bp_activity_after_save', 'openlab_clear_home_page_transients' );

/**
 * Fix the busted redirect on group subscription settings
 */
function openlab_fix_group_sub_settings_redirect( $redirect ) {
	if ( bp_get_root_domain() === $redirect && groups_get_current_group() && bp_is_current_action( 'notifications' ) && ! empty( $_POST ) ) {
		$redirect = bp_get_group_permalink( groups_get_current_group() ) . 'notifications/';
	}
	return $redirect;
}
add_filter( 'wp_redirect', 'openlab_fix_group_sub_settings_redirect' );

/**
 * Remove the Sitewide Notices sitewide box added by BP theme compat
 *
 * @see #923
 */
function openlab_remove_sitewide_notices() {
	global $wp_filter;

	// hackkkkkkkkk
	if ( isset( $wp_filter['wp_footer'][9999] ) ) {
		foreach ( $wp_filter['wp_footer'][9999] as $fname => $filter ) {
			if ( false !== strpos( $fname, 'sitewide_notices' ) ) {
				remove_action( 'wp_footer', $fname, 9999 );
			}
		}
	}
}
add_action( 'wp_footer', 'openlab_remove_sitewide_notices' );

/**
 * Markup for the 'A member has joined a public group for which you are an admin' setting.
 */
function openlab_group_join_admin_notification_markup() {
	$send = bp_get_user_meta( bp_displayed_user_id(), 'notification_joined_my_public_group', true );
	if ( ! $send ) {
		$send = 'yes';
	}

	?>
	<tr id="groups-notification-settings-joined-my-public-group">
		<td></td>
		<td>A member has joined a public group for which you are an admin.</td>
		<td class="yes"><input type="radio" name="notifications[notification_joined_my_public_group]" id="notification-groups-joined-my-public-group-yes" value="yes" <?php checked( $send, 'yes', true ); ?>/><label for="notification-groups-joined-my-public-group-yes" class="bp-screen-reader-text">
																																													 <?php
																																														/* translators: accessibility text */
																																														_e( 'Yes, send email', 'buddypress' );
																																														?>
		</label></td>
		<td class="no"><input type="radio" name="notifications[notification_joined_my_public_group]" id="notification-groups-joined-my-public-group-no" value="no" <?php checked( $send, 'no', true ); ?>/><label for="notification-groups-joined-my-public-group-no" class="bp-screen-reader-text">
																																												  <?php
																																													/* translators: accessibility text */
																																													_e( 'No, do not send email', 'buddypress' );
																																													?>
		</label></td>
	</tr>
	<?php
}
add_action( 'groups_screen_notification_settings', 'openlab_group_join_admin_notification_markup' );

/**
 * Send email notification to admin when a member joins a public group.
 */
function openlab_send_group_join_admin_notification( $group_id, $user_id ) {
	$group = groups_get_group( $group_id );
	if ( 'public' !== $group->status ) {
		return;
	}

	$subject = sprintf( 'A new member has joined %s', $group->name );
	$message = sprintf(
		'A new member has joined your group %1$s on the %2$s.

User name: %3$s
Profile link: %4$s

Visit the group: %5$s',
		$group->name,
		bp_get_option( 'blogname' ),
		bp_core_get_user_displayname( $user_id ),
		bp_core_get_user_domain( $user_id ),
		bp_get_group_permalink( $group )
	);

	foreach ( $group->admins as $admin ) {
		$send = bp_get_user_meta( $admin->user_id, 'notification_joined_my_public_group', true );
		if ( 'no' === $send ) {
			continue;
		}

		wp_mail( $admin->user_email, $subject, $message );
	}
}
add_action( 'groups_join_group', 'openlab_send_group_join_admin_notification', 10, 2 );

/**
 * Fetch ID of a field by name.
 *
 * Fields are hardcoded to avoid lookups, but centralized here for easy management.
 *
 * @param string $field_name
 */
function openlab_get_xprofile_field_id( $field_name ) {
	switch ( $field_name ) {
		case 'First Name':
			return 241;

		case 'Last Name':
			return 3;

		// On the 'Student' field group.
		case 'Phone':
			return 194;

		case 'Major Program of Study':
			return 4;

		case 'Department':
			return 19;

		case 'Account Type' :
			return 7;

		case 'Email address (Student)' :
			return 195;
	}
}

/**
 * Force BP Group Documents (Files) upload extensions to match WP's.
 */
function openlab_filter_bp_group_documents_valid_file_formats( $formats ) {
	$wp_types      = get_allowed_mime_types();
	$formats_array = array();
	foreach ( $wp_types as $exts => $_ ) {
		$formats_array = array_merge( $formats_array, explode( '|', $exts ) );
	}
	return implode( ',', $formats_array );
}
add_filter( 'option_bp_group_documents_valid_file_formats', 'openlab_filter_bp_group_documents_valid_file_formats' );

/**
 * Force @-mentions scripts to load on appropriate pages.
 */
add_filter(
	'bp_activity_maybe_load_mentions_scripts',
	function( $load ) {
		global $pagenow;

		if ( ! is_user_logged_in() ) {
			return $load;
		}

		if ( bp_is_messages_compose_screen() || bp_is_messages_conversation() ) {
			return true;
		}

		if ( bp_is_group() && bp_is_current_action( 'forum' ) ) {
			return true;
		}

		return $load;
	}
);

/**
 * Add data-suggestions-group-id attribute to blog comment fields.
 */
add_filter(
	'comment_form_fields',
	function( $fields ) {
		if ( ! isset( $fields['comment'] ) ) {
			return $fields;
		}

		if ( ! is_user_logged_in() ) {
			return $fields;
		}

		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		if ( ! $group_id ) {
			return $fields;
		}

		$fields['comment'] = str_replace(
			'<textarea ',
			sprintf( '<textarea data-suggestions-group-id="%s" ', esc_attr( $group_id ) ),
			$fields['comment']
		);

		return $fields;
	}
);

/**
 * Add data-suggestions-group-id attribute to post editor.
 */
add_filter(
	'the_editor',
	function( $editor ) {
		if ( ! is_user_logged_in() ) {
			return $editor;
		}

		$group_id = null;
		if ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		} elseif ( ! bp_is_root_blog() ) {
			$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		}

		if ( ! $group_id ) {
			return $editor;
		}

		$editor = str_replace(
			'<textarea ',
			sprintf( '<textarea data-suggestions-group-id="%s" ', esc_attr( $group_id ) ),
			$editor
		);

		return $editor;
	}
);

/**
 * Move data-suggestions-group-id to the TinyMCE instance so it's recognized by the Mentions script.
 */
add_filter(
	'tiny_mce_before_init',
	function( $settings, $editor_id ) {
		if ( 'content' === $editor_id ) {
			$settings['init_instance_callback'] = "function() {
			window.bp.mentions.tinyMCEinit();

			var groupId = jQuery( '#content' ).data( 'suggestions-group-id' );

			if ( typeof window.tinyMCE === 'undefined' || window.tinyMCE.activeEditor === null || typeof window.tinyMCE.activeEditor === 'undefined' ) {
				return;
			} else {
				jQuery( window.tinyMCE.activeEditor.contentDocument.activeElement )
				  .data( 'bp-suggestions-group-id', groupId );
			}
		}";
		}

		return $settings;
	},
	20,
	2
);

add_action(
	'bp_after_email_footer',
	function() {
		remove_action( 'bp_after_email_footer', 'ass_bp_email_footer_html_unsubscribe_links' );
		add_action(
			'bp_after_email_footer',
			function() {
				static $added = null;

				if ( $added ) {
					return;
				}

				$tokens = buddypress()->ges_tokens;

				if ( isset( $tokens['subscription_type'] ) && ! empty( $tokens['group.id'] ) ) {
					$settings_url = bp_get_group_permalink( groups_get_group( $tokens['group.id'] ) ) . 'notifications/';

					$link_format = '<a href="%1$s" title="%2$s" style="text-decoration: underline;">%3$s</a>';
					$footer_link = sprintf(
						$link_format,
						esc_attr( $settings_url ),
						'Group notification settings',
						'Unsubscribe or change the frequency of email notifications.'
					);

					$added = true;

					echo $footer_link;
				}
			}
		);
	},
	0
);

/**
 * Corrects the unsubscribe URL in outgoing emails to OL members.
 */
add_action(
	'bp_send_email',
	function( $email, $email_type, $to, $args ) {
		if ( $to instanceof WP_User ) {
			$user_id = $to->ID;
		} elseif ( is_numeric( $to ) ) {
			$user_id = $to;
		} else {
			if ( is_string( $to ) ) {
				$user_email = $to;
			} elseif ( is_array( $to ) ) {
				foreach ( $to[0] as $to_email => $to_username ) {
					$user_email = $to_email;
					break;
				}
			}

			if ( ! empty( $user_email ) ) {
				$user = get_user_by( 'email', $user_email );
				if ( $user ) {
					$user_id = $user->ID;
				}
			}
		}

		if ( empty( $user_id ) ) {
			return;
		}

		$new_tokens                = $args['tokens'];
		$new_tokens['unsubscribe'] = trailingslashit( bp_core_get_user_domain( $user_id ) ) . 'settings/notifications';
		$email->set_tokens( $new_tokens );

		return $args;
	},
	10,
	4
);

/**
 * Allow most HTML in GES notices.
 */
function openlab_ass_clean_content( $content ) {
	$blog_public = (int) get_option( 'blog_public' );

	if ( $blog_public >= 0 ) {
		return $content;
	}

	// Remove <img> tags from non-public sites.
	$content = preg_replace( '/<img[^>]+\>/i', '', $content );

	return $content;
}
add_filter( 'ass_clean_content', 'openlab_ass_clean_content', 4 );
remove_filter( 'ass_clean_content', 'strip_tags', 4 );

add_action( 'admin_head', function() {
	remove_action( 'admin_head', 'bpges_39_migration_admin_notice' );
}, 5 );

/**
 * Get a link to a group.
 */
function openlab_get_group_link( $group_id ) {
	$group = groups_get_group( $group_id );
	return sprintf( '<a href="%s">%s</a>', esc_attr( bp_get_group_permalink( $group ) ), esc_html( $group->name ) );
}

/**
 * Can the user send messages?
 *
 * @param int $user_id Optional. Defaults to the current user.
 * @return bool
 */
function openlab_user_can_send_messages( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	$user_member_type = openlab_get_user_member_type( $user_id );

	$allowed_types = [ 'faculty', 'staff' ];

	return in_array( $user_member_type, $allowed_types, true );
}

/**
 * Was the current thread started by a faculty or staff?
 *
 * @return bool
 */
function openlab_message_thread_was_started_by_faculty_or_staff() {
	global $thread_template;

	if ( ! isset( $thread_template->thread->thread_id ) ) {
		return false;
	}

	$thread_sender = $thread_template->thread->messages[0]->sender_id;

	$thread_sender_member_type = openlab_get_user_member_type( $thread_sender );

	return in_array( $thread_sender_member_type, [ 'faculty', 'staff' ], true );
}
