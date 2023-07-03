<?php
/**
 * Group blogs functionality
 */

/**
 * Utility function for fetching the group id for a blog
 */
function openlab_get_group_id_by_blog_id( $blog_id ) {
	global $wpdb, $bp;

	if ( ! bp_is_active( 'groups' ) ) {
		return 0;
	}

	$group_id = wp_cache_get( $blog_id, 'site_group_ids' );
	if ( false === $group_id ) {
		$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", $blog_id ) ); // WPCS: unprepared SQL ok.
		if ( null === $group_id ) {
			$group_id = 0;
		}
		wp_cache_set( $blog_id, $group_id, 'site_group_ids' );
	}

	return (int) $group_id;
}

/**
 * Utility function for fetching the site id for a group
 */
function openlab_get_site_id_by_group_id( $group_id = 0 ) {
	if ( ! bp_is_active( 'groups' ) ) {
		return 0;
	}

	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	return (int) groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
}

/**
 * Busts cache when group site ID is changed.
 *
 * @param int $group_id
 * @param int $site_id
 */
function openlab_bust_site_group_id_cache( $group_id, $site_id ) {
	wp_cache_delete( $site_id, 'site_group_ids' );
}
add_action( 'openlab_set_group_site_id', 'openlab_bust_site_group_id_cache', 10, 2 );

/**
 * Utility function for fetching site type based on group type.
 *
 * @param int $site_id
 * @return string
 */
function openlab_get_site_type( $site_id ) {
	$group_id = openlab_get_group_id_by_blog_id( $site_id );

	if ( ! $group_id ) {
		return '';
	}

	return openlab_get_group_type( $group_id );
}

/**
 * Use this function to get the URL of a group's site. It'll work whether the site is internal
 * or external
 *
 * @param int $group_id
 */
function openlab_get_group_site_url( $group_id = false ) {
	if ( false === $group_id ) {
		$group_id = openlab_fallback_group();
	}

	$site_url = '';

	if ( ! $group_id ) {
		return $site_url;
	}

	// First check for an internal site, then external
	if ( $site_id = openlab_get_site_id_by_group_id( $group_id ) ) {
		$site_url = get_blog_option( $site_id, 'siteurl' );
	} else {
		$site_url = openlab_get_external_site_url_by_group_id( $group_id );
	}

	return $site_url;
}

/**
 * Link a site to a group.
 *
 * @param int $group_id
 * @param int $site_id
 */
function openlab_set_group_site_id( $group_id, $site_id ) {
	groups_update_groupmeta( $group_id, 'wds_bp_group_site_id', $site_id );

	/**
	 * Fires when a group's site ID is set or updated.
	 *
	 * @param int $group_id
	 * @param int $site_id
	 */
	do_action( 'openlab_set_group_site_id', $group_id, $site_id );
}

/**
 * Syncs the group site's blog_public setting to the linked group.
 */
function openlab_sync_group_site_blog_public( $old_value, $value ) {
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	if ( ! $group_id ) {
		return;
	}

	groups_update_groupmeta( $group_id, 'blog_public', $value );
}
add_action( 'update_option_blog_public', 'openlab_sync_group_site_blog_public', 10, 2 );

/**
 * Syncs the group site's blog_public setting to the linked group when group is saved.
 */
function openlab_sync_group_blog_public_on_group_save( $group ) {
	$site_id = openlab_get_site_id_by_group_id( $group->id );
	if ( ! $site_id ) {
		return;
	}

	$blog_public = get_blog_option( $site_id, 'blog_public' );

	groups_update_groupmeta( $group->id, 'blog_public', (int) $blog_public );
}
add_action( 'groups_group_after_save', 'openlab_sync_group_blog_public_on_group_save' );

// Ensure that old-style blog comment activity is enabled.
add_filter( 'bp_disable_blogforum_comments', '__return_true' );

////////////////////////
/// MEMBERSHIP SYNC ////
////////////////////////

function openlab_get_blog_role_for_group_role( $group_id, $user_id, $group_role = null ) {
	$role_settings = openlab_get_group_member_role_settings( $group_id );

	if ( null === $group_role ) {
		if ( groups_is_user_admin( $user_id, $group_id ) ) {
			$group_role = 'admin';
		} elseif ( groups_is_user_mod( $user_id, $group_id ) ) {
			$group_role = 'mod';
		} else {
			$group_role = 'member';
		}
	}

	return isset( $role_settings[ $group_role ] ) ? $role_settings[ $group_role ] : 'author';

	if ( '-3' == $blog_public ) {
		if ( 'admin' === $group_role ) {
			$blog_role = 'administrator';
		}
	} else {
		if ( 'admin' === $group_role ) {
			$blog_role = 'administrator';
		} elseif ( 'mod' === $group_role ) {
			$blog_role = 'editor';
		} else {
			// Default role is lower for portfolios
			$blog_role = openlab_is_portfolio() ? 'subscriber' : 'author';
		}
	}

	return $blog_role;
}

/**
 * Add user to the group blog when joining the group
 */
function openlab_add_user_to_groupblog( $group_id, $user_id, $role = null ) {
	$blog_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );

	if ( $blog_id ) {
		if ( null === $role ) {
			$role = openlab_get_blog_role_for_group_role( $group_id, $user_id );
		}

		if ( isset( $role ) ) {
			add_user_to_blog( $blog_id, $user_id, $role );
		}
	}
}
add_action( 'groups_join_group', 'openlab_add_user_to_groupblog', 10, 2 );

/**
 * Modify group site membership on promotion.
 */
function openlab_add_user_to_groupblog_on_promotion( $group_id, $user_id, $status ) {
	$role = openlab_get_blog_role_for_group_role( $group_id, $user_id, $status );
	openlab_add_user_to_groupblog( $group_id, $user_id, $role );
}
add_action( 'groups_promote_member', 'openlab_add_user_to_groupblog_on_promotion', 10, 3 );

/**
 * Modify group site membership on hooks that take group_id + user_id.
 */
function openlab_add_user_to_groupblog_on_demotion( $group_id, $user_id ) {
	$role = openlab_get_blog_role_for_group_role( $group_id, $user_id, 'member' );
	openlab_add_user_to_groupblog( $group_id, $user_id, $role );
}
add_action( 'groups_demote_member', 'openlab_add_user_to_groupblog_on_demotion', 10, 2 );
add_action( 'groups_unban_member', 'openlab_add_user_to_groupblog_on_demotion', 10, 2 );

/**
 * Join a user to a groupblog when joining the group
 *
 * This function exists because the arguments are passed to the hook in the wrong order
 */
function openlab_add_user_to_groupblog_accept( $user_id, $group_id ) {
	openlab_add_user_to_groupblog( $group_id, $user_id );
}
add_action( 'groups_membership_accepted', 'openlab_add_user_to_groupblog_accept', 10, 2 );
add_action( 'groups_accept_invite', 'openlab_add_user_to_groupblog_accept', 10, 2 );

/**
 * Sync group membership to a site at the moment that the site is linked to the group.
 */
function openlab_sync_group_site_membership( $group_id, $site_id ) {
	$group_members = groups_get_group_members(
		array(
			'group_id'            => $group_id,
			'exclude_admins_mods' => false,
			'exclude'             => array( get_current_user_id() ),
		)
	);

	foreach ( $group_members['members'] as $group_member ) {
		openlab_add_user_to_groupblog( $group_id, $group_member->user_id );
	}
}
add_action( 'openlab_set_group_site_id', 'openlab_sync_group_site_membership', 10, 2 );


/**
 * Placeholder docs for openlab_remove_user_from_groupblog()
 * I had to move that function to wds-citytech/wds-citytech.php because of
 * the order in which AJAX functions are loaded
 */

/**
 * When a user visits a group blog, check to see whether the user should be an admin, based on
 * membership in the corresponding group.
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/317 for more discussion.
 */
function openlab_force_blog_role_sync() {
	global $bp, $wpdb;

	if ( ! is_user_logged_in() ) {
		return;
	}

	// Super admins do not need to be reassigned.
	if ( is_super_admin() ) {
		return;
	}

	// Is this blog associated with a group?
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

	if ( $group_id ) {

		// Get the user's group status, if any
		$member = $wpdb->get_row( $wpdb->prepare( "SELECT is_admin, is_mod FROM {$bp->groups->table_name_members} WHERE is_confirmed = 1 AND is_banned = 0 AND group_id = %d AND user_id = %d", $group_id, get_current_user_id() ) ); // WPCS: unprepared SQL ok.

		$userdata = get_userdata( get_current_user_id() );

		if ( ! empty( $member ) ) {
			$blog_public = get_blog_option( get_current_blog_id(), 'blog_public' );
			if ( '-3' == $blog_public ) {
				$status = $member->is_admin ? 'administrator' : '';
			} else {
				$status = openlab_is_portfolio( $group_id ) ? 'subscriber' : 'author';

				if ( $member->is_admin ) {
					$status = 'administrator';
				} elseif ( $member->is_mod ) {
					$status = 'editor';
				}
			}

			$role_is_correct = in_array( $status, $userdata->roles );

			// If the status is a null string, we should remove the user and redirect away
			if ( '' === $status ) {
				if ( current_user_can( 'edit_posts' ) ) {
					remove_user_from_blog( get_current_user_id(), get_current_blog_id() );
					bp_core_redirect( get_option( 'siteurl' ) );
				} else {
					return;
				}
			}

			if ( $status && ! $role_is_correct ) {
				$user = new WP_User( get_current_user_id() );
				$user->set_role( $status );
			}
		} else {
			$role_is_correct = ! current_user_can( 'read' );

			if ( ! $role_is_correct ) {
				remove_user_from_blog( get_current_user_id(), get_current_blog_id() );
			}
		}

		if ( ! $role_is_correct ) {
			// Redirect, just for good measure
			echo '<script type="text/javascript">window.location="' . esc_url_raw( get_option( 'siteurl' ) ) . '";</script>';
		}
	}
}

//add_action( 'init', 'openlab_force_blog_role_sync', 999 );
//add_action( 'admin_init', 'openlab_force_blog_role_sync', 999 );


////////////////////////
///     ACTIVITY     ///
////////////////////////

/**
 * Get blog posts into group streams
 */
function openlab_group_blog_activity( $activity ) {

	if ( 'new_blog_post' !== $activity->type && 'new_blog_comment' !== $activity->type ) {
		return $activity;
	}

	$blog_id = $activity->item_id;

	if ( 'new_blog_post' == $activity->type ) {
		$post_id = $activity->secondary_item_id;
		$post    = get_post( $post_id );
	} elseif ( 'new_blog_comment' == $activity->type ) {
		$comment = get_comment( $activity->secondary_item_id );
		$post_id = $comment->comment_post_ID;
		$post    = get_post( $post_id );
	}

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( ! $group_id ) {
		return $activity;
	}

	$group = groups_get_group( array( 'group_id' => $group_id ) );

	// Verify if we already have the modified activity for this blog post
	$id = bp_activity_get_activity_id(
		array(
			'user_id'           => $activity->user_id,
			'type'              => $activity->type,
			'item_id'           => $group_id,
			'secondary_item_id' => $activity->secondary_item_id,
		)
	);

	// if we don't have, verify if we have an original activity
	if ( ! $id ) {
		$id = bp_activity_get_activity_id(
			array(
				'user_id'           => $activity->user_id,
				'type'              => $activity->type,
				'item_id'           => $activity->item_id,
				'secondary_item_id' => $activity->secondary_item_id,
			)
		);
	}

	// If we found an activity for this blog post, then overwrite it to
	// avoid have multiple activities for every blog post edit.
	//
	// Here we'll also prevent email notifications from being sent
	if ( $id ) {
		$activity->id = $id;
		remove_action( 'bp_activity_after_save', 'ass_group_notification_activity', 50 );
	}

	// Replace the necessary values to display in group activity stream
	if ( 'new_blog_post' == $activity->type ) {
		$activity->action = sprintf(
			'%1$s posted %2$s in %3$s',
			bp_core_get_userlink( $activity->user_id ),
			'<a href="' . get_permalink( $post->ID ) . '">' . esc_html( $post->post_title ) . '</a>',
			'<a href="' . bp_get_group_permalink( $group ) . '">' . esc_html( $group->name ) . '</a>'
		);
	} else {
		$userlink = '';
		if ( $activity->user_id ) {
			$userlink = bp_core_get_userlink( $activity->user_id );
		} else {
			$userlink = '<a href="' . esc_attr( $comment->comment_author_url ) . '">' . esc_html( $comment->comment_author ) . '</a>';
		}
		$activity->action = sprintf(
			'%1$s left a %2$s on the post %3$s in the group %4$s',
			$userlink,
			'<a href="' . esc_url( get_comment_link( $comment ) ) . '">comment</a>',
			'<a href="' . get_permalink( $post->ID ) . '">' . esc_html( $post->post_title ) . '</a>',
			'<a href="' . bp_get_group_permalink( $group ) . '">' . esc_html( $group->name ) . '</a>'
		);
	}

	$activity->item_id   = (int) $group_id;
	$activity->component = 'groups';

	$public = get_blog_option( $blog_id, 'blog_public' );

	if ( 0 > (int) $public ) {
		$activity->hide_sitewide = 1;
	}

	// Mark the group as having been active
	groups_update_groupmeta( $group_id, 'last_activity', bp_core_current_time() );

	// prevent infinite loops, but let this function run on later activities ( for unit tests )
	// @see https://buddypress.trac.wordpress.org/ticket/3980
	remove_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );
	add_action(
		'bp_activity_after_save',
		function() {
			add_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );
		}
	);

	return $activity;
}
add_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );

/**
 * When a blog post is deleted, remove the corresponding activity item
 *
 * We have to do this manually because the activity filter in
 * bp_blogs_remove_post() does not align with the schema imposed by OL's
 * groupblog hacks
 *
 * See #850
 */
function openlab_group_blog_remove_activity( $post_id, $blog_id = 0, $user_id = 0 ) {
	global $wpdb, $bp;

	if ( empty( $wpdb->blogid ) ) {
		return false;
	}

	$post_id = (int) $post_id;

	if ( ! $blog_id ) {
		$blog_id = (int) $wpdb->blogid;
	}

	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( $group_id ) {
		// Delete activity stream item
		bp_blogs_delete_activity(
			array(
				'item_id'           => $group_id,
				'secondary_item_id' => $post_id,
				'component'         => 'groups',
				'type'              => 'new_blog_comment',
			)
		);
	}
}
add_action( 'delete_post', 'openlab_group_blog_remove_activity' );
add_action( 'trash_post', 'openlab_group_blog_remove_activity' );

/**
 * When a blog comment is deleted, remove the corresponding activity item
 *
 * We have to do this manually because the activity filter in
 * bp_blogs_remove_comment() does not align with the schema imposed by OL's
 * groupblog hacks
 *
 * See #850
 */
function openlab_group_blog_remove_comment_activity( $comment_id ) {
	global $wpdb, $bp;

	if ( empty( $wpdb->blogid ) ) {
		return false;
	}

	$comment_id = (int) $comment_id;
	$blog_id    = (int) $wpdb->blogid;

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( $group_id ) {
		// Delete activity stream item
		bp_blogs_delete_activity(
			array(
				'item_id'           => $group_id,
				'secondary_item_id' => $comment_id,
				'component'         => 'groups',
				'type'              => 'new_blog_comment',
			)
		);
	}
}
add_action( 'delete_comment', 'openlab_group_blog_remove_comment_activity' );
add_action( 'trash_comment', 'openlab_group_blog_remove_comment_activity' );
add_action( 'spam_comment', 'openlab_group_blog_remove_comment_activity' );

////////////////////////
///  MISCELLANEOUS   ///
////////////////////////
/**
 * Catch 'unlink-site' requests, process, and send back
 */
function openlab_process_unlink_site() {
	if ( bp_is_group_admin_page() && bp_is_action_variable( 'unlink-site', 1 ) ) {
		check_admin_referer( 'unlink-site' );

		if ( ! groups_is_user_admin( get_current_user_id(), bp_get_current_group_id() ) ) {
			return;
		}

		$meta_to_delete = array(
			'external_site_url',
			'wds_bp_group_site_id',
			'external_site_comments_feed',
			'external_site_posts_feed',
		);

		foreach ( $meta_to_delete as $m ) {
			groups_delete_groupmeta( bp_get_current_group_id(), $m );
		}
	}
}

add_action( 'bp_actions', 'openlab_process_unlink_site', 1 );

/**
 * Renders the markup for group-site affilitation
 */
function wds_bp_group_meta() {
	global $wpdb, $bp, $current_site, $base;

	$the_group_id = 0;

	if ( bp_is_group() && ! bp_is_group_create() ) {
		$the_group_id = bp_get_current_group_id();
	}

	$group_type = openlab_get_group_type( $the_group_id );

	if ( isset( $_GET['type'] ) && ( 'group' == $group_type || bp_is_group_create() ) ) {
		$group_type = $_GET['type'];
	}

	// Sanitization for the group type. We'll check plurals too, in case
	// the $_GET param gets messed up
	if ( 's' == substr( $group_type, -1 ) ) {
		$group_type = substr( $group_type, 0, strlen( $group_type ) - 1 );
	}

	if ( ! in_array( $group_type, openlab_group_types() ) ) {
		$group_type = 'group';
	}

	if ( 'group' == $group_type ) {
		$type = isset( $_COOKIE['wds_bp_group_type'] ) ? $_COOKIE['wds_bp_group_type'] : '';
	}

	$group_school       = groups_get_groupmeta( $the_group_id, 'wds_group_school' );
	$group_project_type = groups_get_groupmeta( $the_group_id, 'wds_group_project_type' );

	if ( 'portfolio' == $group_type ) {
		$group_label = openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() );
	} else {
		$group_label = $group_type;
	}
	?>

	<div class="ct-group-meta">

		<?php
		if ( ! empty( $group_type ) && 'group' !== $group_type ) {
			echo wds_load_group_type( $group_type ); // WPCS: XSS ok.
			?>
			<input type="hidden" name="group_type" value="<?php echo esc_attr( $group_type ); ?>" />
			<?php
		}
		?>

		<?php do_action( 'openlab_group_creation_extra_meta' ); ?>

		<?php $group_site_url = openlab_get_group_site_url( $the_group_id ); ?>

		<div class="panel panel-default" id="panel-site-details">
			<div class="panel-heading">Site Details</div>
			<div class="panel-body">

				<?php if ( ! empty( $group_site_url ) ) : ?>

					<div id="current-group-site">
						<?php
						$maybe_site_id = openlab_get_site_id_by_group_id( $the_group_id );

						if ( $maybe_site_id ) {
							$group_site_name    = get_blog_option( $maybe_site_id, 'blogname' );
							$group_site_text    = '<strong>' . $group_site_name . '</strong>';
							$group_site_url_out = '<a class="bold" href="' . $group_site_url . '">' . $group_site_url . '</a>';
							$show_admin_bar     = cboxol_show_admin_bar_for_anonymous_users( $maybe_site_id );
						} else {
							$group_site_text    = '';
							$group_site_url_out = '<a class="bold" href="' . $group_site_url . '">' . $group_site_url . '</a>';
							$show_admin_bar     = false;
						}
						?>
						<p>This <?php echo esc_html( openlab_get_group_type_label() ); ?> is currently associated with the site <?php echo $group_site_text; // WPCS: XSS ok ?></p>
						<ul id="change-group-site"><li><?php echo $group_site_url_out; ?> <a class="button underline confirm" href="<?php echo esc_attr( wp_nonce_url( bp_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/unlink-site/', 'unlink-site' ) ); ?>" id="change-group-site-toggle">Unlink</a></li></ul>

						<?php if ( ! openlab_get_external_site_url_by_group_id( $the_group_id ) ) : ?>
							<div class="show-admin-bar-on-site-setting">
								<p><input type="checkbox" name="show-admin-bar-on-site" id="show-admin-bar-on-site" <?php checked( $show_admin_bar ); ?>> <label for="show-admin-bar-on-site"><?php esc_html_e( 'Show WordPress admin bar to non-logged-in visitors to my site?', 'commons-in-a-box' ); ?></label></p>
								<p class="group-setting-note italics note"><?php esc_html_e( 'The admin bar appears at the top of your site. Logged-in visitors will always see it but you can hide it for site visitors who are not logged in.', 'commons-in-a-box' ); ?></p>
								<?php wp_nonce_field( 'openlab_site_admin_bar_settings', 'openlab-site-admin-bar-settings-nonce', false ); ?>
							</div>
						<?php endif; ?>

					</div>

				<?php else : ?>

					<?php
					// Set up user blogs for fields below
					$user_blogs = get_blogs_of_user( get_current_user_id() );

					// Exclude blogs where the user is not an Admin
					foreach ( $user_blogs as $ubid => $ub ) {
						$role = get_user_meta( bp_loggedin_user_id(), $wpdb->base_prefix . $ub->userblog_id . '_capabilities', true );

						if ( ! array_key_exists( 'administrator', (array) $role ) ) {
							unset( $user_blogs[ $ubid ] );
						}
					}
					$user_blogs = array_values( $user_blogs );
					?>
					<style type="text/css">
						.disabled-opt {
							opacity: .4;
						}
					</style>

					<input type="hidden" name="action" value="copy_blog" />

					<div class="form-table groupblog-setup"
					<?php
					if ( ! empty( $group_site_url ) ) :
						?>
						 style="display: none;"<?php endif ?>>
						<?php if ( 'portfolio' !== $group_type ) : ?>
							<?php $show_website = 'none'; ?>
							<div class="form-field form-required">
								<div scope='row' class="site-details-query">
									<label><input type="checkbox" id="wds_website_check" name="wds_website_check" value="yes" /> Set up a site?</label>
								</div>
							</div>
						<?php else : ?>
							<?php $show_website = 'auto'; ?>
						<?php endif ?>

						<div id="site-options">
							<div id="wds-website-tooltips" class="form-field form-required" style="display:<?php echo esc_html( $show_website ); ?>">
								<div>

								<?php
								switch ( $group_type ) {
									case 'course':
										?>
										<p class="ol-tooltip">Take a moment to consider the address for your site. You will not be able to change it once you've created it. We recommend the following format:</p>

										<ul class="ol-tooltip">
											<li class="hyphenate">FacultyLastNameCourseCodeSemYear</li>
											<li class="hyphenate">smithadv1100sp2012</li>
										</ul>

										<p class="ol-tooltip">If you teach multiple sections on the OpenLab, consider adding other identifying information to the address. Please note that all addresses must be unique.</p>
										<?php
										break;

									case 'project':
										?>
										<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu.</p>
										<?php
										break;

									case 'club':
										?>
										<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu. </p>
										<?php
										break;
								}
								?>
								</div>
							</div>

							<?php if ( bp_is_group_create() && $group_type !== 'portfolio' ) : ?>
								<div id="wds-website-clone" class="form-field form-required">
									<div id="noo_clone_options">
										<div class="row">
											<div class="radio disabled-opt col-sm-6">
												<label>
													<input type="radio" class="noo_radio" name="new_or_old" id="new_or_old_clone" value="clone" disabled/>
													Name your cloned site:</label>
											</div>
											<div class="col-sm-5 site-label">
												<?php global $current_site; ?>
												<?php echo esc_html( $current_site->domain . $current_site->path ); ?>
											</div>
											<div class="col-sm-13">
												<input class="form-control domain-validate" size="40" id="clone-destination-path" name="clone-destination-path" type="text" title="Path" value="" />
											</div>
											<input name="blog-id-to-clone" value="" type="hidden" />
										</div>
										<p id="cloned-site-url"></p>
									</div>

								</div>
							<?php endif ?>

							<div id="wds-website" class="form-field form-required">

								<div id="noo_new_options">
									<div id="noo_new_options-div" class="row">
										<div class="radio col-sm-6">
											<label>
												<input type="radio" class="noo_radio" name="new_or_old" id="new_or_old_new" value="new" />
												Create a new site:</label>
										</div>

										<div class="col-sm-5 site-label">
											<?php
											$suggested_path = $group_type == 'portfolio' ? openlab_suggest_portfolio_path() : '';
											echo esc_html( $current_site->domain . $current_site->path );
											?>
										</div>

										<div class="col-sm-13">
											<input id="new-site-domain" class="form-control domain-validate" size="40" name="blog[domain]" type="text" title="Domain" value="<?php echo esc_html( $suggested_path ); ?>" />
										</div>
									</div>

								</div>
							</div>

							<?php /* Existing blogs - only display if some are available */ ?>
							<?php
							// Exclude blogs already used as groupblogs
							global $wpdb, $bp;
							$current_groupblogs = $wpdb->get_col( "SELECT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ); // WPCS: unprepared SQL ok.

							foreach ( $user_blogs as $ubid => $ub ) {
								if ( in_array( $ub->userblog_id, $current_groupblogs ) ) {
									unset( $user_blogs[ $ubid ] );
								}
							}
							$user_blogs = array_values( $user_blogs );
							?>

							<?php if ( ! empty( $user_blogs ) ) : ?>
								<div id="wds-website-existing" class="form-field form-required">

									<div id="noo_old_options">
										<div class="row">
											<div class="radio col-sm-6">
												<label>
													<input type="radio" class="noo_radio" id="new_or_old_old" name="new_or_old" value="old" />
													Use an existing site:</label>
											</div>
											<div class="col-sm-18">
												<label class="sr-only" for="groupblog-blogid">Choose a site</label>
												<select class="form-control" name="groupblog-blogid" id="groupblog-blogid">
													<option value="0">- Choose a site -</option>
													<?php foreach ( (array) $user_blogs as $user_blog ) : ?>
														<option value="<?php echo esc_attr( $user_blog->userblog_id ); ?>"><?php echo esc_html( $user_blog->blogname ); ?></option>
													<?php endforeach ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							<?php endif ?>

							<div id="wds-website-external" class="form-field form-required">

								<div id="noo_external_options">
									<div class="form-group row">
										<div class="radio col-sm-6">
											<label>
												<input type="radio" class="noo_radio" id="new_or_old_external" name="new_or_old" value="external" />
												Use an external site:
											</label>
										</div>
										<div class="col-sm-18">
											<label class="sr-only" for="external-site-url">Input external site URL</label>
											<input class="form-control pull-left" type="text" name="external-site-url" id="external-site-url" placeholder="http://" />
											<a class="btn btn-primary no-deco top-align pull-right" id="find-feeds" href="#" display="none">Check<span class="sr-only"> external site for Post and Comment feeds</span></a>
										</div>
									</div>
								</div>
							</div>
							<div id="check-note-wrapper" style="display:<?php echo esc_attr( $show_website ); ?>"><div colspan="2"><p id="check-note" class="italics disabled-opt">Note: Please click the Check button to search for Post and Comment feeds for your external site. Doing so will push new activity to your <?php echo esc_html( ucfirst( $group_type ) ); ?> Profile page. If no feeds are detected, you may type in the Post and Comment feed URLs directly or just leave blank.</p></div></div>
						</div>
					</div><!-- #site-options -->
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'bp_after_group_details_creation_step', 'wds_bp_group_meta' );
add_action( 'bp_after_group_details_admin', 'wds_bp_group_meta' );

/**
 * Outputs the Member Role Settings panel.
 */
function openlab_group_member_role_settings( $group_type ) {
	global $bp;

	$show_panel = false;
	$site_id    = null;

	if ( bp_is_group_create() && groups_get_groupmeta( bp_get_new_group_id(), 'clone_source_group_id' ) ) {
		$clone_steps = groups_get_groupmeta( bp_get_new_group_id(), 'clone_steps', true );
		$show_panel  = in_array( 'site', $clone_steps, true );
	} else {
		$site_id    = openlab_get_site_id_by_group_id();
		$show_panel = ! empty( $site_id );
	}

	if ( ! $show_panel ) {
		return;
	}

	$group_type_name    = $group_type;
	$group_type_name_uc = ucfirst( $group_type );

	if ( 'portfolio' === $group_type ) {
		$group_type_name = openlab_get_portfolio_label(
			array(
				'group_id' => bp_get_current_group_id(),
			)
		);

		$group_type_name_uc = openlab_get_portfolio_label(
			array(
				'group_id' => bp_get_current_group_id(),
				'case'     => 'upper',
			)
		);
	}

	$site_roles = array(
		'administrator' => 'Administrator',
		'editor'        => 'Editor',
		'author'        => 'Author',
		'contributor'   => 'Contributor',
		'subscriber'    => 'Subscriber',
	);

	if ( bp_is_group_create() ) {
		$settings = array(
			'admin'  => 'administrator',
			'mod'    => 'editor',
			'member' => 'author',
		);
	} else {
		$settings = openlab_get_group_member_role_settings( bp_get_current_group_id() );
	}

	?>
	<div class="panel panel-default member-roles">
		<div class="panel-heading semibold">Member Role Settings</div>

		<div class="group-profile panel-body">
			<p>These settings control the default member roles on your associated <?php echo esc_html( $group_type_name_uc ); ?> site when members join the <?php echo esc_html( $group_type_name_uc ); ?>. You may also adjust individual member roles in Membership settings and on the <?php echo esc_html( $group_type_name_uc ); ?> site Dashboard.</p>

			<div class="row">
				<div class="col-sm-24">
					<ul class="member-role-selectors">
						<li>
							<label for="member_role_member"><?php echo esc_html( $group_type_name_uc ); ?> members have the following role on the <?php echo esc_html( $group_type_name_uc ); ?> site:</label>
							<select class="form-control" name="member_role_member">
								<?php foreach ( $site_roles as $site_role => $site_role_label ) : ?>
									<option value="<?php echo esc_attr( $site_role ); ?>" <?php selected( $site_role, $settings['member'] ); ?>><?php echo esc_html( $site_role_label ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>

						<li>
							<label for="member_role_admin"><?php echo esc_html( $group_type_name_uc ); ?> moderators have the following role on the <?php echo esc_html( $group_type_name_uc ); ?> site:</label>
							<select class="form-control" name="member_role_mod">
								<?php foreach ( $site_roles as $site_role => $site_role_label ) : ?>
									<option value="<?php echo esc_attr( $site_role ); ?>" <?php selected( $site_role, $settings['mod'] ); ?>><?php echo esc_html( $site_role_label ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>

						<li>
							<label for="member_role_admin"><?php echo esc_html( $group_type_name_uc ); ?> administrators have the following role on the <?php echo esc_html( $group_type_name_uc ); ?> site:</label>
							<select class="form-control" name="member_role_admin">
								<?php foreach ( $site_roles as $site_role => $site_role_label ) : ?>
									<option value="<?php echo esc_attr( $site_role ); ?>" <?php selected( $site_role, $settings['admin'] ); ?>><?php echo esc_html( $site_role_label ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
					</ul>
				</div>
			</div>

			<div class="row">
				<div class="member-role-definition col-sm-24">
					<div class="member-role-definition-label"><i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?php echo esc_html( $group_type_name_uc ); ?> Member Role Definitions</div>
					<div class="member-role-definition-text">
						<ul>
							<li><strong>Administrator</strong>: Someone who can change course, project, or club settings (such as changing privacy settings); edit course, project, or club details; edit, close, and delete discussion forum topics; and edit and delete docs. They can also change the avatar, manage membership, and delete the course, project, or club.</li>
							<li><strong>Moderator</strong>: Someone who can edit course, project, or club details; edit, close, and delete discussion forum topics; and edit and delete docs.</li>
							<li><strong>Member</strong>: Someone who can post in discussion forums, edit docs (depending on settings determined by the admin), and upload files.</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="member-role-definition col-sm-24">
					<div class="member-role-definition-label"><i class="fa fa-caret-square-o-right" aria-hidden="true"></i><?php echo esc_html( $group_type_name_uc ); ?> Site Member Role Definitions</div>
					<div class="member-role-definition-text">
						<ul>
							<li><strong>Administrator</strong>: Someone who can control every aspect of a site, from managing content and comments, to choosing site themes to activating widgets and plugins.  In most cases, you should not make another site user an Administrator unless you want them to have equal control over your site content and functions.</li>
							<li><strong>Editor</strong>: Someone who can write and publish posts, as well as manage the posts of other users.  Editors can also make changes to pages, but cannot change the theme, menu, widgets, plugins, or edit other user roles.</li>
							<li><strong>Author</strong>: Someone who can publish and edit their own content, but cannot change or delete anything that anyone else has created on the site.  In most cases, if you are adding additional users to your site, making them site Authors is the best choice.</li>
							<li><strong>Contributor</strong>: Someone who can write and edit their own posts, but can’t publish them.  They can save them as drafts for an Editor or Administrator to publish.</li>
							<li><strong>Subscriber</strong>: Someone who can only log in and manage their profile, but they can’t post or change anything on the site.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

/**
 * Gets the member role settings for a group.
 */
function openlab_get_group_member_role_settings( $group_id ) {
	$defaults = [
		'admin'  => 'administrator',
		'mod'    => 'editor',
		'member' => 'author',
	];

	$raw_settings = groups_get_groupmeta( $group_id, 'member_site_roles' );

	if ( ! $raw_settings ) {
		$settings = $defaults;
	} else {
		$settings = [];
		foreach ( $defaults as $group_role => $site_role ) {
			$settings[ $group_role ] = isset( $raw_settings[ $group_role ] ) ? $raw_settings[ $group_role ] : $site_role;
		}
	}

	return $settings;
}

/**
 * Server side group blog URL validation
 *
 * When you attempt to create a groupblog, this function catches the request and checks to make sure
 * that the URL is not used. If it is, an error is sent back.
 */
function openlab_validate_groupblog_url() {
	global $current_blog;

	/**
	 * This is terrifying.
	 * We check for a groupblog in the following cases:
	 * a ) 'new' == $_POST['new_or_old'] || 'clone' == $_POST['new_or_old'], and either
	 * b1 ) the 'Set up a site?' checkbox has been checked, OR
	 * b2 ) the group type is Portfolio, which requires a blog
	 */
	if (
			isset( $_POST['new_or_old'] ) &&
			( 'new' == $_POST['new_or_old'] || 'clone' == $_POST['new_or_old'] ) &&
			( isset( $_POST['wds_website_check'] ) || in_array( $_POST['group_type'], array( 'portfolio' ), true ) )
	) {
		// Which field we check depends on whether this is a clone
		$path = '';
		if ( 'clone' == $_POST['new_or_old'] ) {
			$path = $_POST['clone-destination-path'];
		} else {
			$path = $_POST['blog']['domain'];
		}

		if ( empty( $path ) ) {
			bp_core_add_message( 'Your site URL cannot be blank.', 'error' );
			bp_core_redirect( wp_guess_url() );
		}

		if ( domain_exists( $current_blog->domain, '/' . $path . '/', 1 ) ) {
			bp_core_add_message( 'That site URL is already taken. Please try another.', 'error' );
			bp_core_redirect( wp_guess_url() );
		}
	}
}

add_action( 'bp_actions', 'openlab_validate_groupblog_url', 1 );

/**
 * For groupblog types other than 'Create a new site', perform basic validation
 */
function openlab_validate_groupblog_selection() {
	if ( isset( $_POST['new_or_old'] ) ) {
		switch ( $_POST['new_or_old'] ) {
			case 'old':
				if ( empty( $_POST['groupblog-blogid'] ) ) {
					$error_message = 'You must select an existing site from the dropdown menu.';
				}
				break;

			case 'external':
				if ( empty( $_POST['external-site-url'] ) || ! openlab_validate_url( $_POST['external-site-url'] ) || 'http://' == trim( $_POST['external-site-url'] ) ) {
					$error_message = 'You must provide a valid external site URL.';
				}
				break;
		}

		if ( isset( $error_message ) ) {
			bp_core_add_message( $error_message, 'error' );
			bp_core_redirect( wp_guess_url() );
		}
	}
}

add_action( 'bp_actions', 'openlab_validate_groupblog_selection', 1 );

/**
 * Handler for AJAX group blog URL validation
 */
function openlab_validate_groupblog_url_handler() {
	global $current_blog;

	$path = isset( $_POST['path'] ) ? $_POST['path'] : '';
	if ( domain_exists( $current_blog->domain, '/' . $path . '/', 1 ) ) {
		$retval = 'exists';
	} else {
		$retval = '';
	}
	die( $retval );
}

add_action( 'wp_ajax_openlab_validate_groupblog_url_handler', 'openlab_validate_groupblog_url_handler' );

/**
 * The following function overrides the BP_Blogs_Blog::get() in function bp_blogs_get_blogs(),
 * when looking at the my-sites page, so that the only blogs shown are those without a group
 * attached to them.
 */
function openlab_filter_groupblogs_from_my_sites( $blogs, $params ) {

	// Note: It may be desirable to expand the locations where this filtering happens
	// I'm just playing it safe for the time being
	if ( ! is_page( 'my-sites' ) ) {
		return $blogs;
	}

	global $bp, $wpdb;

	// return apply_filters( 'bp_blogs_get_blogs', BP_Blogs_Blog::get( $type, $per_page, $page, $user_id, $search_terms ), $params );
	//  get( $type, $limit = false, $page = false, $user_id = 0, $search_terms = false )
	// Set up the necessary variables for the rest of the function, out of $params
	$type         = $params['type'];
	$limit        = $params['per_page'];
	$page         = $params['page'];
	$user_id      = $params['user_id'];
	$search_terms = $params['search_terms'];

	// The magic: Pull up a list of blogs that have associated groups, and exclude them
	$exclude_blogs = $wpdb->get_col( "SELECT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ); // WPCS: unprepared SQL ok.

	if ( ! empty( $exclude_blogs ) ) {
		$exclude_sql = ' AND b.blog_id NOT IN ( ' . implode( ',', $exclude_blogs ) . ' ) ';
	} else {
		$exclude_sql = '';
	}

	if ( ! is_user_logged_in() || ( ! is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) ) {
		$hidden_sql = 'AND wb.public = 1';
	} else {
		$hidden_sql = '';
	}

	$pag_sql = ( $limit && $page ) ? $wpdb->prepare( ' LIMIT %d, %d', intval( ( $page - 1 ) * $limit ), intval( $limit ) ) : '';

	$user_sql = ! empty( $user_id ) ? $wpdb->prepare( ' AND b.user_id = %d', $user_id ) : '';

	switch ( $type ) {
		case 'active':
		default:
			$order_sql = 'ORDER BY bm.meta_value DESC';
			break;
		case 'alphabetical':
			$order_sql = 'ORDER BY bm2.meta_value ASC';
			break;
		case 'newest':
			$order_sql = 'ORDER BY wb.registered DESC';
			break;
		case 'random':
			$order_sql = 'ORDER BY RAND()';
			break;
	}

	if ( ! empty( $search_terms ) ) {
		$filter      = like_escape( $wpdb->escape( $search_terms ) );
		$paged_blogs = $wpdb->get_results( "SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' AND bm2.meta_value LIKE '%%$filter%%' {$user_sql} {$exclude_sql} GROUP BY b.blog_id {$order_sql} {$pag_sql}" ); // WPCS: unprepared SQL ok.
		$total_blogs = $wpdb->get_var( "SELECT COUNT( DISTINCT b.blog_id ) FROM {$bp->blogs->table_name} b, {$wpdb->base_prefix}blogs wb, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2 WHERE b.blog_id = wb.blog_id AND bm.blog_id = b.blog_id AND bm2.blog_id = b.blog_id AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} AND bm.meta_key = 'name' AND bm2.meta_key = 'description' AND ( bm.meta_value LIKE '%%$filter%%' || bm2.meta_value LIKE '%%$filter%%' ) {$user_sql} {$exclude_sql}" ); // WPCS: unprepared SQL ok.
	} else {
		$paged_blogs = $wpdb->get_results( "SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id {$user_sql} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} {$exclude_sql} AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' GROUP BY b.blog_id {$order_sql} {$pag_sql}" ); // WPCS: unprepared SQL ok.
		$total_blogs = $wpdb->get_var( "SELECT COUNT( DISTINCT b.blog_id ) FROM {$bp->blogs->table_name} b, {$wpdb->base_prefix}blogs wb WHERE b.blog_id = wb.blog_id {$user_sql} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} {$exclude_sql}" ); // WPCS: unprepared SQL ok.
	}

	$blog_ids = array();
	foreach ( (array) $paged_blogs as $blog ) {
		$blog_ids[] = $blog->blog_id;
	}

	$blog_ids    = $wpdb->escape( join( ',', (array) $blog_ids ) );
	$paged_blogs = BP_Blogs_Blog::get_blog_extras( $paged_blogs, $blog_ids, $type );

	return array(
		'blogs' => $paged_blogs,
		'total' => $total_blogs,
	);
}

add_filter( 'bp_blogs_get_blogs', 'openlab_filter_groupblogs_from_my_sites', 10, 2 );

/**
 * This function checks the blog_public option of the group site, and depending on the result,
 * returns whether the current user can view the site.
 */
function wds_site_can_be_viewed() {
	global $user_ID;

	// External sites can always be viewed
	if ( openlab_get_external_site_url_by_group_id() ) {
		return true;
	}

	$blog_public          = false;
	$group_id             = bp_get_group_id();
	$wds_bp_group_site_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );

	if ( $wds_bp_group_site_id != '' ) {
		$blog_private = get_blog_option( $wds_bp_group_site_id, 'blog_public' );

		switch ( $blog_private ) {
			case '-3':
				if ( is_user_logged_in() ) {
					$user_capabilities = get_user_meta( $user_ID, 'wp_' . $wds_bp_group_site_id . '_capabilities', true );
					if ( isset( $user_capabilities['administrator'] ) ) {
						$blog_public = true;
					}
				}
				break;

			case '-2':
				if ( is_user_logged_in() ) {
					$user_capabilities = get_user_meta( $user_ID, 'wp_' . $wds_bp_group_site_id . '_capabilities', true );
					if ( $user_capabilities != '' ) {
						$blog_public = true;
					}
				}
				break;

			case '-1':
				if ( is_user_logged_in() ) {
					$blog_public = true;
				}
				break;

			default:
				$blog_public = true;
				break;
		}
	}
	return $blog_public;
}

////////////////////////
///  EXTERNAL SITES  ///
////////////////////////

/**
 * Markup for the External Blog feed URL stuff on group creation/admin
 */
function openlab_feed_url_markup() {
	$group_id = bp_get_current_group_id();

	if ( empty( $group_id ) ) {
		return;
	}

	$external_site_url = groups_get_groupmeta( $group_id, 'external_site_url' );

	if ( empty( $external_site_url ) ) {
		// No need to go on if you're using a local site
		return;
	}
	?>

	<p>RSS feeds are used to pull new post and comment activity from your external site into your activity stream.</p>

	<?php $posts_feed_url = groups_get_groupmeta( $group_id, 'external_site_posts_feed' ); ?>
	<?php $comments_feed_url = groups_get_groupmeta( $group_id, 'external_site_comments_feed' ); ?>

	<?php if ( $posts_feed_url || $comments_feed_url ) : ?>
		<p>We located the following RSS feed URLs for your external site. Correct errors or provide missing feed addresses in the fields below.</p>
	<?php else : ?>
		<p>We weren't able to auto-locate your RSS feeds. If your site has RSS feeds, enter their addresses below.</p>
	<?php endif ?>

	<p><label for="external-site-posts-feed">Posts:</label> <input id="external-site-posts-feed" name="external-site-posts-feed" value="<?php echo esc_attr( $posts_feed_url ); ?>" /></p>

	<p><label for="external-site-comments-feed">Comments:</label> <input id="external-site-comments-feed" name="external-site-comments-feed" value="<?php echo esc_attr( $comments_feed_url ); ?>" /></p>

	<br />
	<hr>

	<?php
}

//add_action( 'bp_before_group_settings_creation_step', 'openlab_feed_url_markup' );

/**
 * Wrapper function to get the URL of an external site, if it exists
 */
function openlab_get_external_site_url_by_group_id( $group_id = 0 ) {
	if ( 0 == (int) $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$external_site_url = groups_get_groupmeta( $group_id, 'external_site_url' );

	return $external_site_url;
}

/**
 * Given a group id, fetch its external posts
 *
 * Attempts to fetch from a transient before refreshing
 */
function openlab_get_external_posts_by_group_id( $group_id = 0 ) {
	if ( 0 == (int) $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Check transients first
	$posts = get_transient( 'openlab_external_posts_' . $group_id );

	if ( empty( $posts ) ) {
		$feed_url = groups_get_groupmeta( $group_id, 'external_site_posts_feed' );

		if ( $feed_url ) {
			$posts = openlab_format_rss_items( $feed_url );
			set_transient( 'openlab_external_posts_' . $group_id, $posts, 60 * 10 );

			// Translate the feed items into activity items
			openlab_convert_feed_to_activity( $posts, 'posts' );
		}
	}

	return $posts;
}

/**
 * Given a group id, fetch its external comments
 *
 * Attempts to fetch from a transient before refreshing
 */
function openlab_get_external_comments_by_group_id( $group_id = 0 ) {
	if ( 0 == (int) $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Check transients first
	$comments = get_transient( 'openlab_external_comments_' . $group_id );

	if ( empty( $comments ) ) {
		$feed_url = groups_get_groupmeta( $group_id, 'external_site_comments_feed' );

		if ( $feed_url ) {
			$comments = openlab_format_rss_items( $feed_url );
			set_transient( 'openlab_external_comments_' . $group_id, $comments, 60 * 10 );

			// Translate the feed items into activity items
			openlab_convert_feed_to_activity( $comments, 'comments' );
		}
	}

	return $comments;
}

/**
 * Given an RSS feed URL, fetch the items and parse into an array containing permalink, title,
 * and content
 */
function openlab_format_rss_items( $feed_url, $num_items = 3 ) {
	$feed_posts = fetch_feed( $feed_url );

	if ( is_wp_error( $feed_posts ) ) {
		return;
	}

	$items = array();

	foreach ( $feed_posts->get_items( 0, $num_items ) as $key => $feed_item ) {
		$items[] = array(
			'permalink' => $feed_item->get_link(),
			'title'     => $feed_item->get_title(),
			'content'   => strip_tags( bp_create_excerpt( $feed_item->get_content(), 135, array( 'html' => true ) ) ),
			'author'    => $feed_item->get_author(),
			'date'      => $feed_item->get_date(),
		);
	}

	return $items;
}

/**
 * Convert RSS items to activity items
 */
function openlab_convert_feed_to_activity( $items = array(), $item_type = 'posts' ) {
	$type  = 'posts' == $item_type ? 'new_blog_post' : 'new_blog_comment';
	$group = groups_get_current_group();

	$hide_sitewide = false;
	if ( ! empty( $group ) && isset( $group->status ) && 'public' != $group->status ) {
		$hide_sitewide = true;
	}

	$group_id = ! empty( $group ) ? $group->id : '';

	foreach ( (array) $items as $item ) {
		// Make sure we don't have duplicates
		// We check based on the item's permalink
		if ( ! openlab_external_activity_item_exists( $item['permalink'], $group_id, $type ) ) {
			$action = '';

			$group           = groups_get_current_group();
			$group_name      = $group->name;
			$group_permalink = bp_get_group_permalink( $group );
			$group_type      = openlab_group_type( 'lower', 'single', $group->id );

			if ( 'posts' == $item_type ) {
				$action = sprintf(
					'A new post %s was published in the ' . $group_type . ' %s',
					'<a href="' . esc_attr( $item['permalink'] ) . '">' . esc_html( $item['title'] ) . '</a>',
					'<a href="' . $group_permalink . '">' . $group_name . '</a>'
				);
			} elseif ( 'comments' == $item_type ) {
				$action = sprintf(
					'A new comment was posted on the post %s in the ' . $group_type . ' %s',
					'<a href="' . esc_attr( $item['permalink'] ) . '">' . esc_html( $item['title'] ) . '</a>',
					'<a href="' . $group_permalink . '">' . $group_name . '</a>'
				);
			}

			$item_date = strtotime( $item['date'] );
			$now       = time();
			if ( $item_date > $now ) {
				$item_date = $now;
			}
			$recorded_time = date( 'Y-m-d H:i:s', $item_date );

			$args = array(
				'action'        => $action,
				'content'       => $item['content'],
				'component'     => 'groups',
				'type'          => $type,
				'primary_link'  => $item['permalink'],
				'user_id'       => 0, // todo
				'item_id'       => bp_get_current_group_id(), // improve?
				'recorded_time' => $recorded_time,
				'hide_sitewide' => $hide_sitewide,
			);

			remove_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );
			bp_activity_add( $args );
		}
	}
}

/**
 * Check to see whether an external blog post activity item exists for this item already
 *
 * @param str Permalink of original post
 * @param int Associated group id
 * @param str Activity type ( new_blog_post, new_blog_comment )
 * @return bool
 */
function openlab_external_activity_item_exists( $permalink, $group_id, $type ) {
	global $wpdb, $bp;

	$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE primary_link = %s AND type = %s AND component = 'groups' AND item_id = %s", $permalink, $type, $group_id ); // WPCS: unprepared SQL ok.

	return (bool) $wpdb->get_var( $sql );
}

/**
 * Validate a URL format
 */
function openlab_validate_url( $url ) {
	if ( 0 !== strpos( $url, 'http' ) ) {
		// Let's guess that http was left off
		$url = 'http://' . $url;
	}

	$url = trailingslashit( $url );

	return $url;
}

/**
 * Given a site URL, try to get feed URLs
 */
function openlab_find_feed_urls( $url ) {

	// Supported formats
	$formats = array(
		'wordpress' => array(
			'posts'    => '{{URL}}feed',
			'comments' => '{{URL}}/comments/feed',
		),
		'blogger'   => array(
			'posts'    => '{{URL}}feeds/posts/default?alt=rss',
			'comments' => '{{URL}}feeds/comments/default?alt=rss',
		),
		'drupal'    => array(
			'posts' => '{{URL}}posts/feed',
		),
	);

	$feed_urls = array();

	foreach ( $formats as $ftype => $f ) {
		$maybe_feed_url = str_replace( '{{URL}}', trailingslashit( $url ), $f['posts'] );

		// Do a HEAD check first to avoid loops when self-querying.
		$maybe_feed_head = wp_remote_head(
			$maybe_feed_url,
			array(
				'redirection' => 2,
			)
		);

		if ( 200 != wp_remote_retrieve_response_code( $maybe_feed_head ) ) {
			continue;
		}

		$maybe_feed = wp_remote_get( $maybe_feed_url );
		if ( ! is_wp_error( $maybe_feed ) && 200 == $maybe_feed['response']['code'] ) {

			// Check to make sure this is actually a feed
			$feed_items = fetch_feed( $maybe_feed_url );
			if ( is_wp_error( $feed_items ) ) {
				continue;
			}

			$feed_urls['posts'] = $maybe_feed_url;
			$feed_urls['type']  = $ftype;

			// Test the comment feed
			if ( isset( $f['comments'] ) ) {
				$maybe_comments_feed_url = str_replace( '{{URL}}', trailingslashit( $url ), $f['comments'] );
				$maybe_comments_feed     = wp_remote_get( $maybe_comments_feed_url );

				if ( 200 == $maybe_comments_feed['response']['code'] ) {
					$feed_urls['comments'] = $maybe_comments_feed_url;
				}
			}

			break;
		}
	}

	return $feed_urls;
}

/**
 * AJAX handler for feed detection
 */
function openlab_detect_feeds_handler() {
	$url   = isset( $_REQUEST['site_url'] ) ? $_REQUEST['site_url'] : ''; // WPCS: CSRF ok.
	$feeds = openlab_find_feed_urls( $url );

	die( wp_json_encode( $feeds ) );
}

add_action( 'wp_ajax_openlab_detect_feeds', 'openlab_detect_feeds_handler' );

/**
 * Catch feed refresh requests and processem
 */
function openlab_catch_refresh_feed_requests() {
	if ( ! bp_is_group() ) {
		return;
	}

	if ( ! isset( $_GET['refresh_feed'] ) || ! in_array( $_GET['refresh_feed'], array( 'posts', 'comments' ) ) ) {
		return;
	}

	if ( ! groups_is_user_admin( bp_loggedin_user_id(), bp_get_current_group_id() ) ) {
		return;
	}

	$feed_type = $_GET['refresh_feed'];

	check_admin_referer( 'refresh-' . $feed_type . '-feed' );

	delete_transient( 'openlab_external_' . $feed_type . '_' . bp_get_current_group_id() );
	call_user_func( 'openlab_get_external_' . $feed_type . '_by_group_id' );
}

add_action( 'bp_actions', 'openlab_catch_refresh_feed_requests' );

/**
 * Until we get the dynamic portfolio picker working properly, we manually fall
 * back on old logic
 */
function openlab_get_groupblog_template( $user_id, $group_id ) {
	$group_type = openlab_get_group_type( $group_id );

	$template = '';
	switch ( $group_type ) {
		case 'portfolio':
			$account_type = openlab_get_user_member_type( $user_id );

			switch ( $account_type ) {
				case 'faculty':
					$template = 'template-portfolio';
					break;
				case 'staff':
					$template = 'template-portfolio-staff';
					break;
				case 'student':
					$template = 'template-eportfolio';

					$group_units = openlab_get_group_academic_units( $group_id );
					if ( in_array( 'communication-design', $group_units['departments'], true ) ) {
						$template = 'template-eportfolio-communication-design';
					}
					break;
				case 'alumni':
					$template = 'template-eportfolio-alumni';
					break;
			}
			break;

		default:
			$template = 'template-' . strtolower( $group_type );
			break;
	}

	if ( ! $template ) {
		return 0;
	}

	// Get the ID.
	$network = get_network();

	$site = get_site_by_path( $network->domain, $template );

	if ( $site ) {
		return $site->blog_id;
	} else {
		return 0;
	}
}

/**
 * On portfolio creation, select the appropriate template for the user
 */
class OpenLab_GroupBlog_Template_Picker {

	protected $user_id    = 0;
	protected $template   = null;
	protected $group_type = 'group';

	protected $student_department;
	protected $account_type;
	protected $department_templates;

	public function __construct( $user_id = 0 ) {
		$user_id = intval( $user_id );
		if ( ! $user_id ) {
			$user_id = bp_loggedin_user_id();
		}
		$this->user_id = $user_id;

		// The apply_filters() is mainly for use in unit testing
		$this->department_templates = apply_filters( 'openlab_department_templates', array() );
	}

	public function set_template( $template ) {
		$this->template = $template;
		return $template;
	}

	public function get_group_type() {
		return $this->group_type;
	}

	public function set_group_type( $type ) {
		if ( ! in_array( $type, openlab_group_types() ) ) {
			$type = 'group';
		}

		$this->group_type = $type;
	}

	public function get_user_type() {
		if ( ! $this->account_type ) {
			$account_type       = openlab_get_user_member_type( $this->user_id );
			$this->account_type = $account_type;
		}

		return $this->account_type;
	}

	public function set_user_type( $type ) {
		$this->account_type = $type;
	}

	public function get_student_department() {
		if ( ! isset( $this->student_department ) ) {
			$dept_field               = 'student' === $this->get_user_type() ? 'Major Program of Study' : 'Department';
			$this->student_department = xprofile_get_field_data( $dept_field, $this->user_id );
		}

		return $this->student_department;
	}

	public function set_student_department( $department ) {
		$this->student_department = $department;
	}

	public function get_template_from_group_type() {
		return 'template-' . strtolower( $this->group_type );
	}

	public function get_portfolio_template_for_user() {
		$user_type = $this->get_user_type();

		$template = '';
		switch ( $user_type ) {
			case 'faculty':
				$template = 'template-portfolio';
				break;
			case 'staff':
				$template = 'template-portfolio-staff';
				break;
			case 'student':
				$template = $this->get_portfolio_template_for_student();
				break;
		}

		return $template;
	}

	public function get_portfolio_template_for_student() {
		$department = $this->get_student_department();

		if ( isset( $this->department_templates[ $department ] ) ) {
			$template = $this->department_templates[ $department ];
		} else {
			$template = 'template-eportfolio';
		}

		return $template;
	}

}

// Disable admin notices for wp-grade-comments.
add_filter( 'olgc_display_notices', '__return_false' );

// Disable admin notices for openlab-private-comments.
add_filter( 'olpc_display_notices', '__return_false' );

/**
 * Map "instructor" status to group administrator for wp-grade-comments.
 */
function openlab_olgc_is_instructor() {
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	return (bool) groups_is_user_admin( get_current_user_id(), $group_id );
}
add_filter( 'olgc_is_instructor', 'openlab_olgc_is_instructor' );

/**
 * Catch wp-grade-comments notice dismissals.
 */
function openlab_catch_olgc_notice_dismissals() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['olgc-notice-dismiss'] ) ) {
		return;
	}

	check_admin_referer( 'olgc_notice_dismiss' );

	update_option( 'olgc_notice_dismissed', 1 );
}
add_action( 'admin_init', 'openlab_catch_olgc_notice_dismissals' );

/**
 * Email the post author when a wp-grade-comments or openlab-private-comments "private" comment is posted.
 *
 * @param int        $comment_id ID of the comment.
 * @param WP_Comment $comment    Comment object.
 */
function openlab_olgc_notify_postauthor( $comment_id, $comment ) {
	$olgc_is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );
	$olpc_is_private = get_comment_meta( $comment_id, 'ol_is_private', true );
	if ( ! $olgc_is_private && ! $olpc_is_private ) {
		return;
	}

	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	if ( ! $group_id ) {
		return;
	}

	// Sanity check.
	$comment_author_user = get_user_by( 'email', $comment->comment_author_email );
	if ( ! $comment_author_user ) {
		return;
	}

	$post = get_post( $comment->comment_post_ID );
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	// No self-notifications.
	if ( (int) $post->post_author === (int) $comment_author_user->ID ) {
		return;
	}

	$author_user = get_user_by( 'id', $post->post_author );
	if ( ! $author_user ) {
		return;
	}

	// Don't allow duplicate core notification to be sent.
	remove_action( 'comment_post', 'wp_new_comment_notify_postauthor' );

	$group = groups_get_group( $group_id );

	$subject = sprintf( 'A new private comment on %s in %s', $post->post_title, $group->name );

	$comment_link = get_comment_link( $comment );

	$message = sprintf(
		'There is a new private comment on your site %s.<br /><br />

Post name: %s<br />
Comment author: %s<br />
Comment URL: %s',
		get_option( 'blogname' ),
		$post->post_title,
		bp_core_get_userlink( $comment_author_user->ID ),
		sprintf( '<a href="%s">%s</a>', $comment_link, $comment_link )
	);

	$message = openlab_comment_email_boilerplate( $message );

	wp_mail( $author_user->user_email, $subject, $message );
}
add_action( 'wp_insert_comment', 'openlab_olgc_notify_postauthor', 20, 2 );

/**
 * Email the course instructor when a wp-grade-comments or openlab-private-comments "private" comment is posted.
 *
 * @param int        $comment_id ID of the comment.
 * @param WP_Comment $comment    Comment object.
 */
function openlab_olgc_notify_instructor( $comment_id, $comment ) {
	$olgc_is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );
	$olpc_is_private = get_comment_meta( $comment_id, 'ol_is_private', true );
	if ( ! $olgc_is_private && ! $olpc_is_private ) {
		return;
	}

	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	if ( ! $group_id ) {
		return;
	}

	$admins = groups_get_group_admins( $group_id );
	if ( ! $admins ) {
		return;
	}

	// Sanity check.
	$comment_author_user = get_user_by( 'email', $comment->comment_author_email );
	if ( ! $comment_author_user ) {
		return;
	}

	$group = groups_get_group( $group_id );
	$post  = get_post( $comment->comment_post_ID );

	$subject = sprintf( 'A new private comment on %s in %s', $post->post_title, $group->name );

	$comment_link = get_comment_link( $comment );

	$message = sprintf(
		'There is a new private comment on your site %s.<br /><br />

Post name: %s<br />
Comment author: %s<br />
Comment URL: %s',
		get_option( 'blogname' ),
		$post->post_title,
		bp_core_get_userlink( $comment_author_user->ID ),
		sprintf( '<a href="%s">%s</a>', $comment_link, $comment_link )
	);

	$message = openlab_comment_email_boilerplate( $message );

	$comment_user = get_userdata( $comment->user_id );

	foreach ( $admins as $admin ) {
		// Don't send notification to instructor of her own comment.
		if ( (int) $admin->user_id === (int) $comment_author_user->ID ) {
			continue;
		}

		$admin_user = get_user_by( 'id', $admin->user_id );
		if ( ! $admin_user ) {
			continue;
		}

		wp_mail( $admin_user->user_email, $subject, $message );
	}
}
add_action( 'wp_insert_comment', 'openlab_olgc_notify_instructor', 20, 2 );

/**
 * Email authors of private comments when they receive replies.
 *
 * @param int        $comment_id ID of the comment.
 * @param WP_Comment $comment    Comment object.
 */
function openlab_olpc_notify_comment_author_of_reply( $comment_id, $comment ) {
	$olgc_is_private = get_comment_meta( $comment_id, 'olgc_is_private', true );
	$olpc_is_private = get_comment_meta( $comment_id, 'ol_is_private', true );
	if ( ! $olgc_is_private && ! $olpc_is_private ) {
		return;
	}

	$parent_comment = get_comment( $comment->comment_parent );
	if ( ! $parent_comment ) {
		return;
	}

	$comment_post = get_post( $comment->comment_post_ID );
	if ( ! $comment_post ) {
		return;
	}

	// Post authors receive notification separately.
	if ( $comment->user_id === $comment_post->user_id ) {
		return;
	}

	$recipient = get_user_by( 'id', $parent_comment->user_id );
	if ( ! $recipient ) {
		return;
	}

	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	$group    = groups_get_group( $group_id );

	$subject = sprintf( 'A new reply to your private comment on %s in %s', $comment_post->post_title, $group->name );

	$comment_link = get_comment_link( $comment );

	$message = sprintf(
		'There is a new reply to your private comment on the site %s.<br /><br />

Post name: %s<br />
Comment author: %s<br />
Comment URL: %s',
		get_option( 'blogname' ),
		$comment_post->post_title,
		bp_core_get_userlink( $comment->user_id ),
		sprintf( '<a href="%s">%s</a>', $comment_link, $comment_link )
	);

	$message = openlab_comment_email_boilerplate( $message );

	wp_mail( $recipient->user_email, $subject, $message );
}
add_action( 'wp_insert_comment', 'openlab_olpc_notify_comment_author_of_reply', 20, 2 );

/**
 * Show a notice on the dashboard of cloned course sites.
 */
function openlab_cloned_course_notice() {
	global $current_blog;

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Don't show for sites created before 2016-03-09.
	$latest     = new DateTime( '2016-03-09' );
	$registered = new DateTime( $current_blog->registered );
	if ( $latest > $registered ) {
		return;
	}

	// Allow dismissal.
	if ( get_option( 'openlab-clone-notice-dismissed' ) ) {
		return;
	}

	// Only show for cloned courses.
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	if ( ! groups_get_groupmeta( $group_id, 'clone_source_group_id' ) ) {
		return;
	}

	// Groan
	$dismiss_url = $_SERVER['REQUEST_URI'];
	$nonce       = wp_create_nonce( 'ol_clone_dismiss' );
	$dismiss_url = add_query_arg( 'ol-clone-dismiss', '1', $dismiss_url );
	$dismiss_url = add_query_arg( '_wpnonce', $nonce, $dismiss_url );

	?>
	<style type="text/css">
		.ol-cloned-message {
			position: relative;
		}
		.ol-cloned-message > p > span {
			width: 80%;
		}
		.ol-clone-message-dismiss {
			position: absolute;
			right: 15px;
		}
	</style>
	<div class="updated fade ol-cloned-message">
		<p><span>Please Note: Your cloned site has been published. Please preview the site and make any adjustments (ie: set selected pages and posts to draft) as needed. <strong>This is a change to cloning functionality, which used to keep posts and pages in draft.</strong></span>
		<a class="ol-clone-message-dismiss" href="<?php echo esc_attr( esc_url( $dismiss_url ) ); ?>">Dismiss</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'openlab_cloned_course_notice' );

/**
 * Catch cloned course notice dismissals.
 */
function openlab_catch_cloned_course_notice_dismissals() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_GET['ol-clone-dismiss'] ) ) {
		return;
	}

	check_admin_referer( 'ol_clone_dismiss' );

	update_option( 'openlab-clone-notice-dismissed', 1 );
}
add_action( 'admin_init', 'openlab_catch_cloned_course_notice_dismissals' );

/**
 * Catches and processes admin-bar settings.
 */
function openlab_save_group_site_admin_bar_settings() {
	if ( ! isset( $_POST['openlab-site-admin-bar-settings-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'openlab_site_admin_bar_settings', 'openlab-site-admin-bar-settings-nonce' );

	$group = groups_get_current_group();

	$site_id = openlab_get_site_id_by_group_id( $group->id );
	if ( ! $site_id ) {
		return;
	}

	$show_admin_bar = ! empty( $_POST['show-admin-bar-on-site'] );

	if ( $show_admin_bar ) {
		delete_blog_option( $site_id, 'cboxol_hide_admin_bar_for_anonymous_users' );
	} else {
		update_blog_option( $site_id, 'cboxol_hide_admin_bar_for_anonymous_users', 1 );
	}
}
add_action( 'bp_actions', 'openlab_save_group_site_admin_bar_settings', 1 );

/** "Display Name" column on users.php ***************************************/

add_filter(
	'manage_users_columns',
	function( $cols ) {
		$new_cols = [];
		foreach ( $cols as $col_slug => $col_name ) {
			$new_cols[ $col_slug ] = $col_name;
			if ( 'name' === $col_slug ) {
				$new_cols['display_name'] = 'Display Name';
			}
		}
		return $new_cols;
	}
);

add_action(
	'manage_users_custom_column',
	function( $retval, $col, $user_id ) {
		if ( 'display_name' !== $col ) {
			return $retval;
		}

		return esc_html( bp_core_get_user_displayname( $user_id ) );
	},
	10,
	3
);

/**
 * Hide private comments even after the plugin is deactivated.
 *
 * @param WP_Comment_Query $query
 * @return void
 */
function openlab_private_comments_fallback( WP_Comment_Query $query ) {
	// Bail if request if from the main site.
	if ( isset( $query->query_vars['main_site'] ) && $query->query_vars['main_site'] ) {
		return;
	}

	// Make private comments visible for admins in the dashboard.
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		return;
	}

	$meta_query     = [];
	$active_plugins = (array) get_option( 'active_plugins', [] );

	if ( ! in_array( 'wp-grade-comments/wp-grade-comments.php', $active_plugins, true ) ) {
		$meta_query[] = [
			'relation' => 'OR',
			[
				'key'   => 'olgc_is_private',
				'value' => '0',
			],
			[
				'key' => 'olgc_is_private',
				'compare' => 'NOT EXISTS',
			],
		];
	}

	if ( ! in_array( 'openlab-private-comments/openlab-private-comments.php', $active_plugins, true ) ) {
		$meta_query[] = [
			'relation' => 'OR',
			[
				'key'   => 'ol_is_private',
				'value' => '0',
			],
			[
				'key' => 'ol_is_private',
				'compare' => 'NOT EXISTS',
			],
		];
	}

	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}

	if ( ! empty( $meta_query ) ) {
		$query->meta_query = new WP_Meta_Query( $meta_query );
	}
}
add_action( 'pre_get_comments', 'openlab_private_comments_fallback' );

// If one of the plugins is active. The `openlab_private_comments_fallback` will apply to count.
if (
	! has_filter( 'get_comments_number', 'olgc_get_comments_number' ) &&
	! has_filter( 'get_comments_number', 'OpenLab\\PrivateComments\\filter_comment_count' )
) {
	add_filter( 'get_comments_number', 'openlab_comment_count_fallback', 20, 2 );
}

/**
 * Filter comment count after plugins are deactivated.
 *
 * @param int $count   Comment count.
 * @param int $post_id ID of the post.
 * @return int $count  Adjusted comment count.
 */
function openlab_comment_count_fallback( $count, $post_id = 0 ) {
	// No need for fallback when we don't have post or comments.
	if ( empty( $post_id ) || empty( $count ) ) {
		return $count;
	}

	// Check plugin artifacts.
	$grade_comments   = get_option( 'olgc_notice_dismissed' );
	$private_comments = get_option( 'olpc_notice_dismissed' );

	$filter_count = ( $grade_comments !== false || $private_comments !== false );
	if ( ! $filter_count ) {
		return $count;
	}

	// Query if filtered via `openlab_private_comments_fallback` function.
	$query = new \WP_Comment_Query();
	$comments = $query->query( [
		'post_id' => $post_id,
		'fields'  => 'ids',
	] );

	return count( $comments );
}
