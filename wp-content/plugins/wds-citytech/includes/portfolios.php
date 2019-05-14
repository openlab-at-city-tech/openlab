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
	if ( ! $user_id ) {
		$user_id = openlab_fallback_user();
	}

	// Extra fallback for the case of portfolios: get the user associated
	// with the current group
	if ( ! $user_id ) {
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
		'user_id'   => $default_user_id,
		'group_id'  => $default_group_id,
		'user_type' => '',
		'case'      => 'lower',
		'leading_a' => false,
	);

	$r = wp_parse_args( $args, $defaults );

	if ( empty( $r['user_id'] ) && ! empty( $r['group_id'] ) ) {
		$r['user_id'] = openlab_get_user_id_from_portfolio_group_id( $r['group_id'] );
	}

	if ( empty( $r['user_type'] ) ) {
		$r['user_type'] = xprofile_get_field_data( 'Account Type', $r['user_id'] );
	}

	// Sanitize
	if ( ! in_array( strtolower( $r['user_type'] ), array( 'student', 'staff', 'faculty' ) ) ) {
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
	if ( openlab_is_portfolio() || ( ! empty( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
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
function openlab_get_group_member_portfolios( $group_id = false, $sort_by = 'display_name', $type = 'public' ) {
	if ( ! $group_id ) {
		$group_id = openlab_fallback_group();
	}

	$cache_key  = 'member_portfolios_' . $sort_by;
	$portfolios = groups_get_groupmeta( $group_id, $cache_key );

	$max = 100;

	if ( '' == $portfolios ) {
		$portfolios    = array();
		$group_members = new BP_Group_Member_Query(
			array(
				'group_id'   => $group_id,
				'per_page'   => false,
				'page'       => false,
				'group_role' => array( 'member', 'mod', 'admin' ),
				'type'       => 'alphabetical',
			)
		);

		$count = 0;
		foreach ( $group_members->results as $member ) {
			if ( $count > $max ) {
				break;
			}

			$count++;

			$portfolio_id      = openlab_get_user_portfolio_id( $member->ID );
			$portfolio_group   = groups_get_group( array( 'group_id' => $portfolio_id ) );
			$portfolio_blog_id = openlab_get_site_id_by_group_id( $portfolio_id );

			if ( empty( $portfolio_id ) || empty( $portfolio_group ) ) {
				continue;
			}

			// Don't add hidden portfolios, unless they've been requested
			if ( 'all' !== $type && 'hidden' === $portfolio_group->status ) {
				continue;
			}

			// If the portfolio_blog_id is empty, this may be an external portfolio.
			if ( empty( $portfolio_blog_id ) ) {
				$portfolio_url = openlab_get_external_site_url_by_group_id( $portfolio_id );

				// No URL found? There's no portfolio to link to.
				if ( empty( $portfolio_url ) ) {
					continue;
				}

				// Use the group title for the link text.
				$portfolio_title = $portfolio_group->name;
			} else {
				$portfolio_url   = openlab_get_user_portfolio_url( $member->ID );
				$portfolio_title = get_blog_option( $portfolio_blog_id, 'blogname' );
			}

			$portfolio = array(
				'user_id'           => $member->ID,
				'user_display_name' => $member->display_name,
				'user_type'         => xprofile_get_field_data( 'Account Type', $member->ID ),
				'portfolio_id'      => $portfolio_id,
				'portfolio_url'     => $portfolio_url,
				'portfolio_title'   => $portfolio_title,
			);

			$portfolios[] = $portfolio;
		}

		switch ( $sort_by ) {
			case 'display_name':
				$key = 'user_display_name';
				break;

			case 'random':
				$key = 'random';
				break;

			case 'title':
			default:
				$key = 'portfolio_title';
				break;
		}

		if ( 'random' === $key ) {
			shuffle( $portfolios );
		} else {
			usort(
				$portfolios,
				function( $a, $b ) use ( $key ) {
					$values = array(
						0 => $a[ $key ],
						1 => $b[ $key ],
					);
					$cmp    = strcasecmp( $values[0], $values[1] );

					if ( 0 > $cmp ) {
						$retval = -1;
					} elseif ( 0 < $cmp ) {
						$retval = 1;
					} else {
						$retval = 0;
					}
					return $retval;
				}
			);
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
 * Bust group portfolio cache when member leaves group
 */
function openlab_bust_group_portfolios_cache_on_group_leave( $group_id ) {
	openlab_bust_group_portfolio_cache( $group_id );
}

add_action( 'groups_uninvite_user', 'openlab_bust_group_portfolios_cache_on_group_leave' );

/* Bust group portfolio cache when member is removed from group.
 *
 * We can't run on 'groups_remove_member' because it runs before the member
 * is removed.
 */

function openlab_bust_group_portfolios_cache_on_group_remove( $user_id, $group_id ) {
	openlab_bust_group_portfolio_cache( $group_id );
}

add_action( 'groups_removed_member', 'openlab_bust_group_portfolios_cache_on_group_remove', 10, 2 );

/* Bust group portfolio cache when a member removes themselves from the group
 *
 */
function openlab_bust_group_portfolios_cache_on_self_remove( $group_id, $user_id ) {
	openlab_bust_group_portfolio_cache( $group_id );
}

add_action( 'groups_leave_group', 'openlab_bust_group_portfolios_cache_on_self_remove', 10, 2 );

/**
 * Bust group portfolio cache when membership changes
 */
function openlab_bust_group_portfolios_cache_on_portfolio_event( $group_id ) {
	if ( ! openlab_is_portfolio( $group_id ) ) {
		return;
	}

	// Delete the portfolio cache for each group the user is a member of
	// Don't regenerate - could be several groups. Let it happen on the fly
	$user_id   = openlab_get_user_id_from_portfolio_group_id( $group_id );
	$group_ids = groups_get_user_groups( $user_id );
	foreach ( $group_ids['groups'] as $gid ) {
		openlab_bust_group_portfolio_cache( $gid );
	}
}
add_action( 'groups_before_delete_group', 'openlab_bust_group_portfolios_cache_on_portfolio_event' );
add_action( 'groups_created_group', 'openlab_bust_group_portfolios_cache_on_portfolio_event' );
add_action( 'groups_group_settings_edited', 'openlab_bust_group_portfolios_cache_on_portfolio_event' );

/**
 * Disable Discussion by default on portfolios.
 */
add_action(
	'groups_created_group',
	function( $group_id ) {
		if ( ! openlab_is_portfolio( $group_id ) ) {
			return;
		}

		groups_update_groupmeta( $group_id, 'openlab_disable_forum', 1 );
	}
);

/**
 * Check whether portfolio list display is enabled for a group.
 */
function openlab_portfolio_list_enabled_for_group( $group_id = 0 ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$group_type = openlab_get_group_type( $group_id );

	// Portfolio groups never have the list enabled
	if ( 'portfolios' === $group_type ) {
		return false;
	}

	// For courses, fall back on 'yes'
	if ( 'course' === $group_type ) {
		$enabled = 'no' !== groups_get_groupmeta( $group_id, 'portfolio_list_enabled' );

		// Otherwise default to 'no'
	} else {
		$enabled = 'yes' === groups_get_groupmeta( $group_id, 'portfolio_list_enabled' );
	}

	return $enabled;
}

/**
 * Adjust widget description to match proper group type.
 *
 * The widget is registered too early to do this in the class constructor.
 */
function openlab_swap_portfolio_widget_description() {
	global $wp_registered_widgets;

	foreach ( $wp_registered_widgets as &$w ) {
		if ( 'Portfolio List' !== $w['name'] ) {
			continue;
		}

		$group_id   = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$group_type = openlab_get_group_type_label(
			array(
				'group_id' => $group_id,
			)
		);

		$w['description'] = sprintf( 'Display a list of the Portfolios belonging to the members of this %s.', $group_type );
	}
}
add_action( 'bp_init', 'openlab_swap_portfolio_widget_description', 20 );

/**
 * Get the heading/title for the group portfolio listing.
 */
function openlab_portfolio_list_group_heading( $group_id = 0 ) {
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$heading = groups_get_groupmeta( $group_id, 'portfolio_list_heading' );

	if ( ! $heading ) {
		$heading = 'Member Portfolios';
	}

	return $heading;
}

/**
 * Add the portfolio display to group sidebars.
 */
function openlab_portfolio_list_group_display() {
	if ( ! openlab_portfolio_list_enabled_for_group() ) {
		return;
	}

	// Non-public groups shouldn't show this to non-members. See #997
	$group = groups_get_current_group();
	if ( 'public' !== $group->status && empty( $group->user_has_access ) ) {
		return false;
	}

	$portfolio_data = openlab_get_group_member_portfolios();

	// No member of the group has a portfolio
	if ( empty( $portfolio_data ) ) {
		return;
	}

	?>

	<div id="group-member-portfolio-sidebar-widget" class="sidebar-widget">
		<h2 class="sidebar-header">
			<?php echo esc_html( openlab_portfolio_list_group_heading() ); ?>
		</h2>

				<div class="sidebar-block">

		<ul class="group-member-portfolio-list sidebar-sublinks inline-element-list group-data-list">
		<?php foreach ( $portfolio_data as $pdata ) : ?>
			<?php $display_string = isset( $pdata['user_type'] ) && in_array( $pdata['user_type'], array( 'Faculty', 'Staff' ) ) ? '%s&#8217;s Portfolio' : '%s&#8217;s ePortfolio'; ?>
			<li><a href="<?php echo esc_url( $pdata['portfolio_url'] ); ?>"><?php echo esc_html( sprintf( $display_string, $pdata['user_display_name'] ) ); ?></a></li>
		<?php endforeach ?>
		</ul>

				</div>
	</div>

	<?php
}
add_action( 'bp_group_options_nav', 'openlab_portfolio_list_group_display', 20 );

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
	if ( ! bp_is_active( 'groups' ) ) {
		return '';
	}

	return bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?type=portfolio&new=true';
}

/**
 * Remove BPGES settings from portfolio group admin and creation screens
 */
function openlab_remove_bpges_settings_for_portfolios() {
	if ( openlab_is_portfolio() || ( bp_is_group_create() && isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		remove_action( 'bp_after_group_settings_admin', 'ass_default_subscription_settings_form' );
		remove_action( 'bp_after_group_settings_creation_step', 'ass_default_subscription_settings_form' );
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
	if ( ! openlab_is_portfolio( $group_id ) ) {
		return;
	}

	// This'll only find internal blogs, of course
	$site_id = openlab_get_site_id_by_group_id( $group_id );

	if ( $site_id ) {
		if ( ! function_exists( 'wpmu_delete_blog' ) ) {
			require_once ABSPATH . '/wp-admin/includes/ms.php';
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
	if ( bp_is_active( 'groups' ) && bp_is_group_creation_step( 'group-details' ) && isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] && openlab_user_has_portfolio( bp_loggedin_user_id() ) ) {
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

/**
 * Register "Portfolio" post meta.
 *
 * @return void
 */
function openlab_portfolio_register_meta() {
	register_meta( 'post', 'portfolio_post_id', [
		'type' => 'integer',
		'single' => true,
		'sanitize_callback' => 'absint',
		'show_in_rest' => true,
	] );

	register_meta( 'comment', 'portfolio_post_id', [
		'type' => 'integer',
		'single' => true,
		'sanitize_callback' => 'absint',
		'show_in_rest' => true,
	] );

	register_meta( 'post', 'source_id', [
		'type' => 'integer',
		'single' => true,
		'sanitize_callback' => 'absint',
		'show_in_rest' => true,
	] );

	register_meta( 'post', 'source_type', [
		'type' => 'string',
		'single' => true,
		'sanitize_callback' => function( $value ) {
			if ( in_array( $value, [ 'post', 'page', 'comment' ], true ) ) {
				return $value;
			}

			return 'posts';
		},
		'show_in_rest' => true,
	] );

	register_meta( 'post', 'source_site_id', [
		'type' => 'integer',
		'single' => true,
		'sanitize_callback' => 'absint',
		'show_in_rest' => true,
	] );

	register_meta( 'post', 'source_date', [
		'type' => 'string',
		'single' => true,
		'sanitize_callback' => 'sanitize_text_field',
		'show_in_rest' => true,
	] );

	register_meta( 'post', 'portfolio_annotation', [
		'type' => 'string',
		'single' => true,
		'sanitize_callback' => 'sanitize_textarea_field',
		'show_in_rest' => true,
	] );
}
add_action( 'init', 'openlab_portfolio_register_meta', 20 );

/**
 * Enqueue "Add to Portfolio" assets.
 *
 * @return void
 */
function openlab_add_to_portfolio_init() {
	$user = wp_get_current_user();

	wp_register_script(
		'a11y-dialog',
		WDS_CITYTECH_URL . '/assets/js/a11y-dialog.min.js',
		[],
		'5.2.0'
	);

	if ( ! $user->exists() ) {
		return;
	}

	// Bail, if user doesn't have portfolio.
	$portfolio_group_id = openlab_get_user_portfolio_id( $user->ID );
	$portfolio_site_id  = openlab_get_site_id_by_group_id( $portfolio_group_id );

	if ( empty( $portfolio_site_id ) ) {
		return;
	}

	// Bail, if we don't support site type. Supported: 'course' and 'project'.
	$type = openlab_get_site_type( get_current_blog_id() );

	if ( ! in_array( $type, [ 'course', 'project' ] ) ) {
		return;
	}

	wp_enqueue_style(
		'add-to-portfolio-styles',
		WDS_CITYTECH_URL . 'assets/css/add-to-portfolio.css',
		[],
		'1.0.0'
	);

	wp_enqueue_script(
		'add-to-portfolio',
		WDS_CITYTECH_URL . '/assets/js/add-to-portfolio.js',
		[ 'a11y-dialog', 'wp-util' ],
		'1.0.0',
		true
	);

	$settings = [
		'root'          => esc_url_raw( get_rest_url() ),
		'portfolioRoot' => esc_url_raw( get_rest_url( $portfolio_site_id ) ),
		'nonce'         => wp_create_nonce( 'wp_rest' ),
	];

	wp_localize_script( 'add-to-portfolio', 'portfolioSettings', $settings );

	// Render required markup.
	add_filter( 'the_content', 'openlab_add_post_to_portfolio', 200 );
	add_filter( 'comment_text', 'openlab_add_comment_to_portfolio', 200 );
	add_action( 'wp_footer', 'openlab_add_to_portfolio_dialog' );
}
add_action( 'wp_enqueue_scripts', 'openlab_add_to_portfolio_init' );

/**
 * Render "Add to Portfolio" button for posts/pages.
 *
 * @param string $content
 * @return string $content
 */
function openlab_add_post_to_portfolio( $content ) {
	if ( ! in_the_loop() ) {
		return $content;
	}

	$post = get_post();
	if ( ! in_array( $post->post_type, [ 'post', 'page'] ) ) {
		return $content;
	}

	if ( get_current_user_id() !== (int) $post->post_author ) {
		return $content;
	}

	ob_start();
	include WDS_CITYTECH_DIR . '/views/portfolio/button-post.php';
	$button = ob_get_clean();

	$content .= $button;

	return $content;
}

/**
 * Render "Add to Portfolio" button for comments.
 *
 * @param string $text
 * @return string $text
 */
function openlab_add_comment_to_portfolio( $text ) {
	$comment = get_comment();

	if ( get_current_user_id() !== (int) $comment->user_id ) {
		return $text;
	}

	ob_start();
	include WDS_CITYTECH_DIR . '/views/portfolio/button-comment.php';
	$button = ob_get_clean();

	$text .= $button;

	return $text;
}

/**
 * Render "Add to Portfolio" dialog markup.
 */
function openlab_add_to_portfolio_dialog() {
	require_once WDS_CITYTECH_DIR . '/views/portfolio/dialog.php';
};

/**
 * Register "Added to Portfolio" content front-end hooks.
 *
 * @return void
 */
function openlab_added_to_portfolio_template() {
	$type = openlab_get_site_type( get_current_blog_id() );

	if ( 'portfolio' !== $type ) {
		return;
	}

	add_filter( 'the_content', 'openlab_portfolio_entry_source_note' );
	add_action( 'wp_footer', 'openlab_portfolio_entry_source_styles' );
}
add_action( 'template_redirect', 'openlab_added_to_portfolio_template' );

/**
 * Register metaboxes for "Portfolio" post/pages.
 *
 * @param string $post_type
 * @param WP_Post $post
 * @return void
 */
function openlab_add_portfolio_metaboxes( $post_type, $post ) {
	$type = openlab_get_site_type( get_current_blog_id() );

	if ( 'portfolio' !== $type ) {
		return;
	}

	// Check if the entry was added from other site.
	$source_id = get_post_meta( (int) $post->ID, 'source_id', true );
	if ( empty( $source_id ) ) {
		return;
	}

	add_meta_box(
		'ol-portfolio-citation',
		'Citation',
		'openlab_portfolio_render_citation_meta',
		[ 'post', 'page' ],
		'normal'
	);

	add_meta_box(
		'ol-portfolio-annotation',
		'Annotation',
		'openlab_portfolio_render_annotation_meta',
		[ 'post', 'page' ],
		'normal'
	);
}
add_action( 'add_meta_boxes', 'openlab_add_portfolio_metaboxes', 10, 2 );

/**
 * Generate "Portfolio" citation data.
 *
 * @param WP_Post $post
 * @return array $data
 */
function openlab_portfolio_get_citation_data( $post ) {
	$source_id = get_post_meta( $post->ID, 'source_id', true );
	$site_id   = get_post_meta( $post->ID, 'source_site_id', true );
	$date      = get_post_meta( $post->ID, 'source_date', true );
	$type      = get_post_meta( $post->ID, 'source_type', true );

	$data = [
		'source_id' => $source_id,
		'date'      => mysql2date( 'F j, Y', $date ),
	];

	// @todo cache this.
	switch_to_blog( $site_id );
	$data['site_name'] = get_option( 'blogname' );
	$data['url']       = ( 'comment' === $type ) ? get_comment_link( $source_id ) : get_permalink( $source_id );
	restore_current_blog();

	return $data;
}

/**
 * Render "Citation" and "Annotation" notes before the content.
 *
 * @param string $content
 * @return string $output
 */
function openlab_portfolio_entry_source_note( $content ) {
	$_post = get_post();
	$data  = openlab_portfolio_get_citation_data( $_post );

	// Check if the entry was added from other site.
	if ( empty( $data['source_id'] ) ) {
		return $content;
	}

	$data['annotation'] = get_post_meta( $_post->ID, 'portfolio_annotation', true );

	extract( [ 'data' => $data ], EXTR_SKIP );
	ob_start();

	include WDS_CITYTECH_DIR . '/views/portfolio/entry-source.php';

	$output = ob_get_clean();
	$output .= $content;

	return $output;
}

/**
 * Render "Portfolio" entry citation.
 *
 * @param WP_Post $post
 * @return void
 */
function openlab_portfolio_render_citation_meta( $post ) {
	$data = openlab_portfolio_get_citation_data( $post );

	extract( [ 'data' => $data ], EXTR_SKIP );
	ob_start();

	include WDS_CITYTECH_DIR . '/views/portfolio/citation.php';

	echo ob_get_clean();
}

/**
 * Render "Portfolio" entry annotation.
 *
 * @param WP_Post $post
 * @return void
 */
function openlab_portfolio_render_annotation_meta( $post ) {
	$annotation = get_post_meta( $post->ID, 'portfolio_annotation', true );
	?>
	<textarea id="annotation" class="large-text" name="portfolio_annotation" rows="5"><?php echo esc_textarea( $annotation ); ?></textarea>
	<?php
	wp_nonce_field( 'portfolio_annotation', 'portfolio_annotation_nonce' );
}

/**
 * Save "Portfolio" entry annotation.
 *
 * @param int $post_id
 * @return void
 */
function openlab_portfolio_save_annotation_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! isset( $_POST['portfolio_annotation_nonce'] ) ) {
		return $post_id;
	}

	if ( ! wp_verify_nonce( $_POST['portfolio_annotation_nonce'], 'portfolio_annotation' ) ) {
		return $post_id;
	}

	$annotation = $_POST['portfolio_annotation'] ?? '';

	// Value is sanitized via register_meta().
	update_post_meta( $post_id, 'portfolio_annotation', $annotation );
}
add_action( 'save_post', 'openlab_portfolio_save_annotation_meta' );

/**
 * Default styles for "Portfolio" entry source.
 *
 * @return void
 */
function openlab_portfolio_entry_source_styles() {
	?>
	<style type="text/css">
		.entry-source-note {
			color: #444;
			background-color: #f0f0f0;
			margin-bottom: 24px;
			padding: 24px;
		}
		.entry-source-note a,
		.entry-source-note a:visited {
			color: #444;
		}
		.entry-source-note .entry__annotation {
			margin-top: 24px;
		}
	</style>
	<?php
}
