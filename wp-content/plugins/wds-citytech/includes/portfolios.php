<?php

/**
 * Functionality related to (e)Portfolio
 *
 * Overview:
 *  - 'portfolio' is a group type, alongside 'course', 'project', and 'club'
 *  - Portfolios must have associated sites
 *  - In templates, the word 'portfolio' should always be used for faculty/staff, 'eportfolio' for
 *    students
 *  - One portfolio per user
 */

/////////////////////////
//  PORTFOLIO DETAILS  //
/////////////////////////

/**
 * Get a user's portfolio *group* id
 *
 * @param int $user_id Defaults to displayed user, then to current member loop user
 * @return int
 */
function openlab_get_user_portfolio_id( $user_id = 0 ) {
	if ( !$user_id ) {
		$user_id = openlab_fallback_user();
	}

	// Extra fallback for the case of portfolios: get the user associated
	// with the current group
	if ( !$user_id ) {
		$user_id = openlab_get_user_id_from_portfolio_group_id( bp_get_current_group_id() );
	}

	$group_id = bp_get_user_meta( $user_id, 'portfolio_group_id', true );

	return (int) $group_id;
}

/**
 * Does a given user have a portfolio?
 *
 * @param int $user_id Defaults to displayed user, then to current member loop user
 * @return bool
 */
function openlab_user_has_portfolio( $user_id = 0 ) {
	return (bool) openlab_get_user_portfolio_id( $user_id );
}

/**
 * Echo a user's portfolio site URL
 */
function openlab_user_portfolio_url( $user_id = 0 ) {
	echo openlab_get_user_portfolio_url( $user_id );
}
	/**
	 * Get a user's portfolio URL
	 *
	 * @param int $user_id Defaults to displayed user, then to current member loop user
	 * @return string URL of the portfolio
	 */
	function openlab_get_user_portfolio_url( $user_id = 0 ) {
		$group_id = openlab_get_user_portfolio_id( $user_id );
		$site_url = openlab_get_group_site_url( $group_id );

		return $site_url;
	}

/**
 * Echo a user's portfolio profile URL
 */
function openlab_user_portfolio_profile_url( $user_id = 0 ) {
	echo openlab_get_user_portfolio_profile_url( $user_id );
}
	/**
	 * Get a user's portfolio profile URL
	 *
	 * @param int $user_id
	 * @return string
	 */
	function openlab_get_user_portfolio_profile_url( $user_id = 0 ) {
		$group_id    = openlab_get_user_portfolio_id( $user_id );
		$profile_obj = groups_get_group( array( 'group_id' => $group_id ) );
		return bp_get_group_permalink( $profile_obj );
	}

/**
 * Is a user's portfolio site local (vs external)?
 *
 * @param int $user_id
 * @return bool
 */
function openlab_user_portfolio_site_is_local( $user_id = 0 ) {
	$group_id = openlab_get_user_portfolio_id( $user_id );
	return (bool) openlab_get_site_id_by_group_id( $group_id );
}

/**
 * Get the user id of a portfolio user from the portfolio group's id
 */
function openlab_get_user_id_from_portfolio_group_id( $group_id = 0 ) {
	global $wpdb;

	$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'portfolio_group_id' AND meta_value = %s", $group_id ) );

	return $user_id;
}

/**
 * Echoes the output of openlab_get_portfolio_label()
 */
function openlab_portfolio_label( $args = array() ) {
	echo openlab_get_portfolio_label( $args );
}
	/**
	 * Get the portfolio label for the given user
	 *
	 * Portfolio labels are: 'portfolio' for fac/staff, 'ePortfolio' for students
	 *
	 * @param array $args The argument array:
	 *   - 'user_id'   The numeric id of the user. If you don't provide this value, it'll fall
	 *                 back on the displayed user, and then on the current user in a member loop
	 *   - 'user_type' The type of user ('faculty', 'staff', 'student'). If you don't provide
	 *                 this value, it'll be auto-detected based on the user_id. Note that this
	 *                 value will always take precedence, even if it's wrong - so don't pass in
	 *                 a value (and let the function detect it automatically) if you're not
	 *                 totally sure what to put here.
	 *   - 'case'      'lower' or 'upper'. Note that this refers to the first letter only, which
	 *                 only matters in the case of 'portfolio'. That is, we'll return
	 *                 'ePortfolio' for upper *or* lower case.
	 *   - 'leading_a' Because 'ePortfolio' starts with a vowel and 'portfolio' does not, the
	 *                 indefinite article varies between them. If you need to use this article,
	 *                 set 'leading_a' to true, and you'll get back 'an ePortfolo' or 'a
	 *                 portfolio', as appropriate. Note that 'case' has no effect on the
	 *                 article: it's always lowercase.
	 */
	function openlab_get_portfolio_label( $args = array() ) {
		$default_user_id  = openlab_fallback_user();
		$default_group_id = openlab_fallback_group();

		$defaults = array(
			'user_id'      => $default_user_id,
			'group_id'     => $default_group_id,
			'user_type'    => '',
			'case'         => 'lower',
			'leading_a'    => false,
		);

		$r = wp_parse_args( $args, $defaults );

		if ( empty( $r['user_id'] ) && !empty( $r['group_id'] ) ) {
			$r['user_id'] = openlab_get_user_id_from_portfolio_group_id( $r['group_id'] );
		}

		if ( empty( $r['user_type'] ) ) {
			$r['user_type'] = xprofile_get_field_data( 'Account Type', $r['user_id'] );
		}

		// Sanitize
		if ( !in_array( strtolower( $r['user_type'] ), array( 'student', 'staff', 'faculty' ) ) ) {
			$r['user_type'] = 'student';
		}

		$r['user_type'] = strtolower( $r['user_type'] );

		if ( 'student' == $r['user_type'] ) {
			$label = 'ePortfolio';

			if ( (bool) $r['leading_a'] ) {
				$label = 'an ' . $label;
			}
		} else {
			$label = 'upper' == $r['case'] ? 'Portfolio' : 'portfolio';

			if ( (bool) $r['leading_a'] ) {
				$label = 'a ' . $label;
			}
		}

		return $label;
	}

/**
 * Suggest a name for a portfolio, based on the user's FN + LN
 */
function openlab_suggest_portfolio_name() {
	$fname = xprofile_get_field_data( 'First Name', bp_loggedin_user_id() );
	$lname = xprofile_get_field_data( 'Last Name', bp_loggedin_user_id() );

	return sprintf( "%s %s's %s", $fname, $lname, openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() ) );
}

/**
 * Suggest a path for a portfolio, based on the user's FN + LN
 */
function openlab_suggest_portfolio_path() {
	$fname = xprofile_get_field_data( 'First Name', bp_loggedin_user_id() );
	$lname = xprofile_get_field_data( 'Last Name', bp_loggedin_user_id() );

	$slug = strtolower( substr( $fname, 0, 1 ) . $lname . '-' . strtolower( openlab_get_portfolio_label( 'user_id=' . bp_loggedin_user_id() ) ) );
	$slug = sanitize_title( $slug );

	return $slug;
}

/**
 * Ensure that a suggested name is included in the Name input of the creation screen
 */
function openlab_bp_get_new_group_name( $name ) {
	if ( openlab_is_portfolio() || ( !empty( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		if ( '' == $name ) {
			$name = openlab_suggest_portfolio_name();
		}
	}

	return $name;
}
add_filter( 'bp_get_new_group_name', 'openlab_bp_get_new_group_name' );

/** Group Portfolios *********************************************************/

/**
 * Get an array of group member portfolio info
 */
function openlab_get_group_member_portfolios( $group_id = false, $sort_by = 'display_name' ) {
	if ( ! $group_id ) {
		$group_id = openlab_fallback_group();
	}

	$cache_key = 'member_portfolios_' . $sort_by;
	$portfolios = groups_get_groupmeta( $group_id, $cache_key );

	if ( '' == $portfolios ) {
		$portfolios = array();
		$group_members = new BP_Group_Member_Query( array(
			'group_id' => $group_id,
			'per_page' => false,
			'page' => false,
			'group_role' => array( 'member', 'mod', 'admin', ),
			'type' => 'alphabetical',
		) );

		foreach ( $group_members->results as $member ) {
			$portfolio_id = openlab_get_user_portfolio_id( $member->ID );
			$portfolio_blog_id = openlab_get_site_id_by_group_id( $portfolio_id );

			if ( ! $portfolio_id || ! $portfolio_blog_id ) {
				continue;
			}

			$portfolio = array(
				'user_id' => $member->ID,
				'user_display_name' => $member->display_name,
				'portfolio_id' => $portfolio_id,
				'portfolio_url' => openlab_get_user_portfolio_url( $member->ID ),
				'portfolio_title' => get_blog_option( $portfolio_blog_id, 'blogname' ),
			);

			$portfolios[] = $portfolio;
		}

		switch ( $sort_by ) {
			case 'display_name' :
				$key = 'user_display_name';
				break;

			case 'random' :
				$key = 'random';
				break;

			case 'title' :
			default :
				$key = 'portfolio_title';
				break;
		}

		if ( 'random' === $key ) {
			shuffle( $portfolios );
		} else {
			usort( $portfolios, create_function( '$a, $b', '
				$key = "' . $key . '";
				$values = array( 0 => $a[ $key ], 1 => $b[ $key ], );
				$cmp = strcasecmp( $values[0], $values[1] );

				if ( 0 > $cmp ) {
					$retval = -1;
				} else if ( 0 < $cmp ) {
					$retval = 1;
				} else {
					$retval = 0;
				}
				return $retval;
			' ) );
		}

		groups_update_groupmeta( $group_id, $cache_key, $portfolios );
	}

	return $portfolios;
}

/**
 * Cache busting for group portfolio lists
 *
 * Bust the cache when:
 * - group membership changes - openlab_bust_group_portfolios_cache_on_membership_change()
 * - a group member adds/removes a portfolio site
 */
function openlab_bust_group_portfolio_cache( $group_id = 0 ) {
	global $wpdb, $bp;

	$keys = $wpdb->get_col( $wpdb->prepare( "SELECT meta_key FROM {$bp->groups->table_name_groupmeta} WHERE group_id = %d AND meta_key LIKE 'member_portfolios_%%'", $group_id ) );
	foreach ( $keys as $k ) {
		groups_delete_groupmeta( $group_id, $k );
	}

	// regenerate
	openlab_get_group_member_portfolios();
}

/**
 * Bust group portfolio cache when membership changes
 */
function openlab_bust_group_portfolios_cache_on_membership_change( $member ) {
	openlab_bust_group_portfolio_cache( $member->group_id );
}
add_action( 'groups_member_after_save', 'openlab_bust_group_portfolios_cache_on_membership_change' );

/**
 * Bust group portfolio cache when membership changes
 */
function openlab_bust_group_portfolios_cache_on_portfolio_event( $group_id ) {
	if ( ! openlab_is_portfolio( $group_id ) ) {
		return;
	}

	// Delete the portfolio cache for each group the user is a member of
	// Don't regenerate - could be several groups. Let it happen on the fly
	$user_id = openlab_get_user_id_from_portfolio_group_id( $group_id );
	$group_ids = groups_get_user_groups( $user_id );
	foreach ( $group_ids as $gid ) {
		openlab_bust_group_portfolio_cache( $gid );
	}
}
add_action( 'groups_before_delete_group', 'openlab_bust_group_portfolios_cache_on_portfolio_event' );
add_action( 'groups_created_group', 'openlab_bust_group_portfolios_cache_on_portfolio_event' );

/**
 * Check whether portfolio list display is enabled for a group.
 */
function openlab_portfolio_list_enabled_for_group( $group_id = 0 ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

        // This kind of check will make 'no' the fallback
	return 'yes' === groups_get_groupmeta( $group_id, 'portfolio_list_enabled' );
}

/**
 * Get the heading/title for the group portfolio listing.
 */
function openlab_portfolio_list_group_heading( $group_id = 0 ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$heading = groups_get_groupmeta( $group_id, 'portfolio_list_heading' );

	if ( ! $heading ) {
		$heading = 'Student ePortfolios';
	}

	return $heading;
}

/**
 * Add the portfolio display to group sidebars.
 */
function openlab_portfolio_list_group_display() {
	if ( 'course' !== openlab_get_group_type( bp_get_current_group_id() ) ) {
		return;
	}

	if ( ! openlab_portfolio_list_enabled_for_group() ) {
		return;
	}

	$portfolio_data = openlab_get_group_member_portfolios();

	?>

	<div id="group-member-portfolio-sidebar-widget" class="sidebar-widget">
		<h4 class="sidebar-header">
			<?php echo esc_html( openlab_portfolio_list_group_heading() ) ?>
		</h4>

		<ul class="group-member-portfolio-list">
		<?php foreach ( $portfolio_data as $pdata ) : ?>
			<li><a href="<?php echo esc_url( $pdata['portfolio_url'] ) ?>"><?php echo esc_html( sprintf( '%s&#8217;s ePortfolio', $pdata['user_display_name'] ) ) ?></a></li>
		<?php endforeach ?>
		</ul>
	</div>

	<?php
}
add_action( 'bp_group_options_nav', 'openlab_portfolio_list_group_display', 20 );

/**
 * Don't enable the eportfolio widget for non-courses.
 */
function openlab_disable_eportfolio_widget_for_non_courses() {
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	$group_type = openlab_get_group_type( $group_id );
	if ( 'course' !== $group_type ) {
		unregister_widget( 'OpenLab_Course_Portfolios_Widget' );

		// unregister_widget() appears to be broken, so...
		global $wp_registered_widgets;
		foreach ( $wp_registered_widgets as $n => $rw ) {
			if ( isset( $rw['callback'][0] ) && is_a( $rw['callback'][0], 'OpenLab_Course_Portfolios_Widget' ) ) {
				unset( $wp_registered_widgets[ $n ] );
			}
		}
	}
}
add_action( 'admin_init', 'openlab_disable_eportfolio_widget_for_non_courses' );

/**
 * Catch form requests (from the widget dropdown) to redirect to a student portfolio
 *
 * See {@link OpenLab_Course_Portfolios_Widget::widget()}
 */
function openlab_redirect_to_student_portfolio_catcher() {
	if ( empty( $_GET['portfolio-goto'] ) ) {
		return;
	}

	check_admin_referer( 'portfolio_goto', '_pnonce' );

	$url = urldecode( $_GET['portfolio-goto'] );
	wp_redirect( $url );
}
add_action( 'wp', 'openlab_redirect_to_student_portfolio_catcher' );

/////////////////////////
//     ACCESS LIST     //
/////////////////////////

/**
 * Template loader
 */
function openlab_groups_screen_group_admin_access_list() {
	global $bp;

	if ( bp_is_groups_component() && bp_is_action_variable( 'access-list', 0 ) ) {
		if ( $bp->is_item_admin || $bp->is_item_mod  ) {
			// If the edit form has been submitted, save the edited details
			if ( isset( $_POST['save'] ) ) {
				// Check the nonce
				if ( !check_admin_referer( 'groups_edit_group_details' ) )
					return false;

				if ( !groups_edit_base_group_details( $_POST['group-id'], $_POST['group-name'], $_POST['group-desc'], (int)$_POST['group-notify-members'] ) ) {
					bp_core_add_message( __( 'There was an error updating group details, please try again.', 'buddypress' ), 'error' );
				} else {
					bp_core_add_message( __( 'Group details were successfully updated.', 'buddypress' ) );
				}

				do_action( 'groups_group_details_edited', $bp->groups->current_group->id );

				bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/' );
			}

			do_action( 'groups_screen_group_admin_edit_details', $bp->groups->current_group->id );

			bp_core_load_template( apply_filters( 'groups_template_group_admin', 'groups/single/home' ) );
		}
	}
}
add_action( 'bp_screens', 'openlab_groups_screen_group_admin_access_list' );

/* Creates the list of members on the Sent Invite screen */
function openlab_access_list_checkboxes() {
	echo openlab_get_access_list_checkboxes();
}
	function openlab_get_access_list_checkboxes( $args = '' ) {
		global $bp, $wpdb;

		$defaults = array(
			'group_id' => false,
			'separator' => 'li',
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( !$group_id )
			$group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : $bp->groups->current_group->id;

		// No good way to get this through an api function
		$friends = $wpdb->get_results( $wpdb->prepare( "SELECT ID, display_name FROM {$wpdb->users} WHERE user_status = 0 AND ID != %d", get_current_user_id() ) );

		if ( $friends ) {
			$group_members = BP_Groups_Member::get_all_for_group( $group_id, false, false, false ); // last param - exclude_admins_mods
			$gms = array();
			foreach ( (array) $group_members['members'] as $gm ) {
				$gms[] = $gm->user_id;
			}

			for ( $i = 0; $i < count( $friends ); $i++ ) {
				$checked = '';
				if ( $gms ) {
					if ( in_array( $friends[$i]->ID, $gms ) ) {
						$checked = ' checked="checked"';
					}
				}

				$items[] = '<' . $separator . '><input' . $checked . ' type="checkbox" name="friends[]" id="f-' . $friends[$i]->ID . '" value="' . esc_html( $friends[$i]->ID ) . '" /> ' . $friends[$i]->display_name . '</' . $separator . '>';
			}
		}

		return implode( "\n", (array)$items );
	}


/**
 * Load the necessary JS and CSS from Invite Anyone // lazy
 */
function openlab_portfolio_access_list_enqueues() {
	if ( bp_is_groups_component() && bp_is_action_variable( 'access-list', 0 ) ) {
		wp_enqueue_script( 'invite-anyone-autocomplete-js', WP_PLUGIN_URL . '/invite-anyone/group-invites/jquery.autocomplete/jquery.autocomplete-min.js', array( 'jquery' ) );

		wp_register_script( 'invite-anyone-js', WP_PLUGIN_URL . '/wds-citytech/assets/js/access-list.js', array( 'invite-anyone-autocomplete-js' ) );
		wp_enqueue_script( 'invite-anyone-js' );

		$style_url  = WP_PLUGIN_URL . '/invite-anyone/group-invites/group-invites-css.css';
		$style_file = WP_PLUGIN_DIR . '/invite-anyone/group-invites/group-invites-css.css';

		if ( file_exists( $style_file ) ) {
			wp_register_style( 'invite-anyone-group-invites-style', $style_url );
			wp_enqueue_style( 'invite-anyone-group-invites-style' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'openlab_portfolio_access_list_enqueues' );

function openlab_ajax_invite_user() {
	global $bp;

	check_ajax_referer( 'groups_invite_uninvite_user' );

	if ( !$_POST['friend_id'] || !$_POST['friend_action'] || !$_POST['group_id'] )
		die();

	if ( 'invite' == $_POST['friend_action'] ) {
		// Add the user to the group, silently
		$new_member                = new BP_Groups_Member;
		$new_member->group_id      = (int) $_POST['group_id'];
		$new_member->user_id       = (int) $_POST['friend_id'];
		$new_member->inviter_id    = 0;
		$new_member->is_admin      = 0;
		$new_member->user_title    = '';
		$new_member->date_modified = bp_core_current_time();
		$new_member->is_confirmed  = 1;

		$new_member->save();

		$user = new BP_Core_User( $_POST['friend_id'] );

		$group_slug = isset( $bp->groups->root_slug ) ? $bp->groups->root_slug : $bp->groups->slug;

		echo '<li id="uid-' . $user->id . '">';
		echo bp_core_fetch_avatar( array( 'item_id' => $user->id ) );
		echo '<h4>' . bp_core_get_userlink( $user->id ) . '</h4>';
		echo '<span class="activity">' . esc_html( $user->last_active ) . '</span>';
		echo '<div class="action">
				<a class="remove" href="' . wp_nonce_url( $bp->loggedin_user->domain . $group_slug . '/' . $_POST['group_id'] . '/invites/remove/' . $user->id, 'groups_invite_uninvite_user' ) . '" id="uid-' . esc_html( $user->id ) . '">' . __( 'Remove Access', 'buddypress' ) . '</a>
			  </div>';
		echo '</li>';

		die();
	} else if ( 'uninvite' == $_POST['friend_action'] ) {
		// Remove the user from the group, silently
		if ( !groups_uninvite_user( $_POST['friend_id'], $_POST['group_id'] ) )
			die();

		die();
	} else {
		die();
	}
}
add_action( 'wp_ajax_openlab_ajax_invite_user', 'openlab_ajax_invite_user' );

function openlab_ajax_autocomplete() {
	global $bp;

	$return = array(
		'query' 	=> $_REQUEST['query'],
		'data' 		=> array(),
		'suggestions' 	=> array()
	);

	$users = invite_anyone_invite_query( $_REQUEST['group_id'], $_REQUEST['query'] );

	if ( $users ) {
		$suggestions = array();
		$data 	     = array();

		foreach ( $users as $user ) {
			$suggestions[] = $user->display_name . ' (' . $user->user_login . ')';
			$data[] = $user->ID;
		}

		$return['suggestions'] = $suggestions;
		$return['data'] = $data;
	}

	die( json_encode( $return ) );
}
add_action( 'wp_ajax_openlab_ajax_autocomplete', 'openlab_ajax_autocomplete' );

function openlab_access_remove_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id )
		$user_id = $members_template->member->user_id;

	echo bp_get_group_member_remove_link( $user_id );
}
	function openlab_get_access_remove_link( $user_id = 0, $group = false ) {
		global $members_template, $groups_template;

		if ( !$group )
			$group =& $groups_template->group;

		return apply_filters( 'bp_get_group_member_remove_link', wp_nonce_url( bp_get_group_permalink( $group ) . 'admin/access-list/remove/' . $user_id, 'groups_remove_member' ) );
	}

/////////////////////////
//    MISCELLANEOUS    //
/////////////////////////

/**
 * Echoes the URL for the portfolio creation page
 */
function openlab_portfolio_creation_url() {
	echo openlab_get_portfolio_creation_url();
}
	/**
	 * Returns the URL for the portfolio creation page
	 */
	function openlab_get_portfolio_creation_url() {
		return bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=portfolio&new=true';
	}

/**
 * Remove BPGES settings from portfolio group admin and creation screens
 */
function openlab_remove_bpges_settings_for_portfolios() {
	if ( openlab_is_portfolio() || ( bp_is_group_create() && isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		remove_action( 'bp_after_group_settings_admin' ,'ass_default_subscription_settings_form' );
		remove_action( 'bp_after_group_settings_creation_step' ,'ass_default_subscription_settings_form' );
	}
}
add_action( 'bp_actions', 'openlab_remove_bpges_settings_for_portfolios', 1 );

/**
 * Mark a group as being a user's portfolio
 */
function openlab_associate_portfolio_group_with_user( $group_id, $user_id ) {
	bp_update_user_meta( $user_id, 'portfolio_group_id', $group_id );

	$account_type = xprofile_get_field_data( 'Account Type', $user_id );
	groups_update_groupmeta( $group_id, 'portfolio_user_type', $account_type );
}

/**
 * Is this my portfolio?
 */
function openlab_is_my_portfolio() {
	return bp_is_group() && openlab_is_portfolio() && is_user_logged_in() && openlab_get_user_id_from_portfolio_group_id( bp_get_current_group_id() ) == bp_loggedin_user_id();
}

/**
 * On portfolio group deletion, also do the following:
 *  - Mark blog as deleted
 *  - Delete user metadata regarding portfolio affiliation
 */
function openlab_delete_portfolio( $group_id ) {
	if ( !openlab_is_portfolio( $group_id ) ) {
		return;
	}

	// This'll only find internal blogs, of course
	$site_id = openlab_get_site_id_by_group_id( $group_id );

	if ( $site_id ) {
		if ( !function_exists( 'wpmu_delete_blog' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/ms.php' );
		}
		wpmu_delete_blog( $site_id );
	}

	$user_id = openlab_get_user_id_from_portfolio_group_id( $group_id );
	bp_delete_user_meta( $user_id, 'portfolio_group_id' );
}
add_action( 'groups_before_delete_group', 'openlab_delete_portfolio' );

/**
 * After portfolio delete, redirect to user profile page
 */
function openlab_delete_portfolio_redirect() {
	bp_core_redirect( bp_loggedin_user_domain() );
}

/**
 * Enforce one portfolio per person, by redirecting away from the portfolio creation page
 */
function openlab_enforce_one_portfolio_per_person() {
	if ( bp_is_group_creation_step( 'group-details' ) && isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] && openlab_user_has_portfolio( bp_loggedin_user_id() ) ) {
		bp_core_add_message( sprintf( 'You already have %s', openlab_get_portfolio_label( 'leading_a=1' ) ), 'error' );
		bp_core_redirect( bp_loggedin_user_domain() );
	}
}
add_action( 'bp_actions', 'openlab_enforce_one_portfolio_per_person', 1 );

/**
 * Don't display Email settings on portfolio profile headers
 */
function openlab_remove_email_settings_from_portfolios() {
	if ( openlab_is_portfolio() ) {
		remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );
	}
}
add_action( 'bp_group_header_meta', 'openlab_remove_email_settings_from_portfolios', 1 );

