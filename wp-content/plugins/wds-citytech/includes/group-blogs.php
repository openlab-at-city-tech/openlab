<?php

/**
 * Group blogs functionality
 */

/**
 * Utility function for fetching the group id for a blog
 */
function openlab_get_group_id_by_blog_id( $blog_id ) {
	global $wpdb, $bp;

	$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", $blog_id ) );

	return (int) $group_id;
}

/**
 * Utility function for fetching the site id for a group
 */
function openlab_get_site_id_by_group_id( $group_id = 0 ) {
	if ( !$group_id ) {
		$group_id = bp_get_current_group_id();
	}

	return (int) groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
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

        if ( !$group_id ) {
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


////////////////////////
/// MEMBERSHIP SYNC ////
////////////////////////

/**
 * Add user to the group blog when joining the group
 */
function openlab_add_user_to_groupblog( $group_id, $user_id ) {
	$blog_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );

	if ( $blog_id ) {
		if ( groups_is_user_admin( $user_id, $group_id ) ) {
		      $role = "administrator";
		} else if ( groups_is_user_mod( $user_id, $group_id ) ){
		      $role = "editor";
		} else {
		      $role = "author";
		}
		add_user_to_blog( $blog_id, $user_id, $role );
	}
}
add_action( 'groups_join_group', 'openlab_add_user_to_groupblog', 10, 2 );

/**
 * Join a user to a groupblog when joining the group
 *
 * This function exists because the arguments are passed to the hook in the wrong order
 */
function openlab_add_user_to_groupblog_accept( $user_id, $group_id ) {
	openlab_add_user_to_groupblog( $group_id, $user_id );
}
add_action( 'groups_accept_invite', 'openlab_add_user_to_groupblog_accept', 10, 2 );


/**
 * Remove user from group blog when leaving group
 */
function openlab_remove_user_from_groupblog( $group_id, $user_id ) {
	$blog_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );

	if ( $blog_id ) {
		remove_user_from_blog( $user_id, $blog_id );
	}
}
add_action( 'groups_leave_group', 'openlab_remove_user_from_groupblog', 10, 2 );

/**
 * When a user visits a group blog, check to see whether the user should be an admin, based on
 * membership in the corresponding group.
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/317 for more discussion.
 */
function openlab_force_blog_role_sync() {
	global $bp, $wpdb;

	if ( !is_user_logged_in() ) {
		return;
	}

	// Is this blog associated with a group?
	$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

	if ( $group_id ) {

		// Get the user's group status, if any
		$member = $wpdb->get_row( $wpdb->prepare( "SELECT is_admin, is_mod FROM {$bp->groups->table_name_members} WHERE is_confirmed = 1 AND is_banned = 0 AND group_id = %d AND user_id = %d", $group_id, get_current_user_id() ) );

		$userdata = get_userdata( get_current_user_id() );

		if ( !empty( $member ) ) {
			$status = 'author';

			if ( $member->is_admin ) {
				$status = 'administrator';
			} else if ( $member->is_mod ) {
				$status = 'editor';
			}

			$role_is_correct = in_array( $status, $userdata->roles );

			if ( !$role_is_correct ) {
				$user = new WP_User( get_current_user_id() );
				$user->set_role( $status );
			}
		} else {
			$role_is_correct = empty( $userdata->roles );

			if ( !$role_is_correct ) {
				remove_user_from_blog( get_current_user_id(), get_current_blog_id() );
			}
		}

		if ( !$role_is_correct ) {
			// Redirect, just for good measure
			echo '<script type="text/javascript">window.location="' . $_SERVER['REQUEST_URI'] . '";</script>';
		}
	}
}
add_action( 'init', 'openlab_force_blog_role_sync', 999 );


////////////////////////
///     ACTIVITY     ///
////////////////////////

/**
 * Get blog posts into group streams
 */
function openlab_group_blog_activity( $activity ) {

	if ( $activity->type != 'new_blog_post' && $activity->type != 'new_blog_comment' )
		return $activity;

	$blog_id = $activity->item_id;

	if ( 'new_blog_post' == $activity->type ) {
		$post_id = $activity->secondary_item_id;
		$post    = get_post( $post_id );
	} else if ( 'new_blog_comment' == $activity->type ) {
		$comment = get_comment( $activity->secondary_item_id );
		$post_id = $comment->comment_post_ID;
		$post    = get_post( $post_id );
	}

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( !$group_id )
		return $activity;

	$group = groups_get_group( array( 'group_id' => $group_id ) );

	// Verify if we already have the modified activity for this blog post
	$id = bp_activity_get_activity_id( array(
		'user_id'           => $activity->user_id,
		'type'              => $activity->type,
		'item_id'           => $group_id,
		'secondary_item_id' => $activity->secondary_item_id
	) );

	// if we don't have, verify if we have an original activity
	if ( !$id ) {
		$id = bp_activity_get_activity_id( array(
			'user_id'           => $activity->user_id,
			'type'              => $activity->type,
			'item_id'           => $activity->item_id,
			'secondary_item_id' => $activity->secondary_item_id
		) );
	}

	// If we found an activity for this blog post then overwrite that to avoid have multiple activities for every blog post edit
	if ( $id ) {
		$activity->id = $id;
	}

	// Replace the necessary values to display in group activity stream
	$activity->action = sprintf( __( '%s wrote a new blog post %s in the group %s:', 'groupblog'), bp_core_get_userlink( $activity->user_id ), '<a href="' . get_permalink( $post->ID ) .'">' . esc_html( $post->post_title ) . '</a>', '<a href="' . bp_get_group_permalink( $group ) . '">' . esc_html( $group->name ) . '</a>' );

	$activity->item_id       = (int)$group_id;
	$activity->component     = 'groups';

	$public = get_blog_option( $blog_id, 'blog_public' );

	if ( 0 > (float) $public ) {
		$activity->hide_sitewide = 1;
	} else {
		$activity->hide_sitewide = 0;
	}

	// Mark the group as having been active
	groups_update_groupmeta( $group_id, 'last_activity', bp_core_current_time() );

	// prevent infinite loops
	remove_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );

	return $activity;
}
add_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );

////////////////////////
///  MISCELLANEOUS   ///
////////////////////////

/**
 * Get a group's recent posts and comments, and display them in two widgets
 */
function show_site_posts_and_comments() {
	global $first_displayed, $bp;

	$group_id = bp_get_group_id();

	$site_type = false;

	if ( $site_id = openlab_get_site_id_by_group_id( $group_id ) ) {
		$site_type = 'local';
	} else if ( $site_url = openlab_get_external_site_url_by_group_id( $group_id ) ) {
		$site_type = 'external';
	}

	$posts = array();
	$comments = array();

	switch ( $site_type ) {
		case 'local':
			switch_to_blog( $site_id );

			// Set up posts
			$wp_posts = get_posts( array(
				'posts_per_page' => 3
			) );

			foreach( $wp_posts as $wp_post ) {
				$posts[] = array(
					'title' => $wp_post->post_title,
					'content' => strip_tags( bp_create_excerpt( $wp_post->post_content, 135, array( 'html' => true ) ) ),
					'permalink' => get_permalink( $wp_post->ID )
				);
			}

			// Set up comments
			$comment_args = array(
				"status" => "approve",
				"number" => "3"
			);

			$wp_comments = get_comments( $comment_args );

			foreach( $wp_comments as $wp_comment ) {
				// Skip the crummy "Hello World" comment
				if ( $wp_comment->comment_ID == "1" ) {
					continue;
				}
				$post_id = $wp_comment->comment_post_ID;

				$comments[] = array(
					'content' => strip_tags( bp_create_excerpt( $wp_comment->comment_content, 135, array( 'html' => false ) ) ),
					'permalink' => get_permalink( $post_id )
				);
			}

			$site_url = get_option( 'siteurl' );

			restore_current_blog();

			break;

		case 'external':
			$posts = openlab_get_external_posts_by_group_id();
			$comments = openlab_get_external_comments_by_group_id();

			break;
	}

	// If we have either, show both
	if ( !empty( $posts ) || !empty( $comments ) ) {
		?>
		<div class="one-half first">
			<div id="recent-course">
				<div class="recent-posts">
					<h4 class="group-activity-title">Recent Site Posts</h4>

					<ul>
					<?php foreach( $posts as $post ) : ?>
						<li>
						<p>
							<?php echo $post['content'] ?> <a href="<?php echo $post['permalink'] ?>" class="read-more">See&nbsp;More</a>
						</p>
						</li>
					<?php endforeach ?>
					</ul>

						<div class="view-more"><a href="<?php echo esc_attr( $site_url ) ?>">See All</a></div>

				</div><!-- .recent-posts -->
			</div><!-- #recent-course -->
		</div><!-- .one-half -->

		<div class="one-half">
			<div id="recent-site-comments">
				<div class="recent-posts">
					<h4 class="group-activity-title">Recent Site Comments</h4>



						<ul>
						<?php if ( !empty( $comments ) ) : ?>
							<?php foreach( $comments as $comment ) : ?>
								<li>
									<?php echo $comment['content'] ?> <a href="<?php echo $comment['permalink'] ?>" class="read-more">See&nbsp;More</a>
								</li>
							<?php endforeach ?>
						<?php else : ?>
							<li><p>No Comments Found</p></li>
						<?php endif ?>

						</ul>

				</div><!-- .recent-posts -->
			</div><!-- #recent-site-comments -->
		</div><!-- .one-half -->
		<?php
	}
}

/**
 * Displays a link to the group's site on the sidebar
 */
function wds_bp_group_site_pages(){
	global $bp;

	$group_id = bp_get_current_group_id();

	// Set up data. Look for local site first. Fall back on external site.
	$site_id = openlab_get_site_id_by_group_id( $group_id );

	if ( $site_id ) {
		$site_url = get_blog_option( $site_id, 'siteurl' );
		$is_local = true;
	} else {
		$site_url = groups_get_groupmeta( $group_id, 'external_site_url' );
		$is_local = false;
	}

	if ( !empty( $site_url ) ) {

		if ( openlab_is_portfolio() ) { ?>
			<div class="sidebar-widget" id="portfolio-sidebar-widget">
				<h4 class="sidebar-header">
					<a href="<?php openlab_user_portfolio_url() ?>"><?php openlab_portfolio_label( 'case=upper' ) ?> Site</a>
				</h4>

				<?php if ( openlab_is_my_portfolio() || is_super_admin() ) : ?>
					<ul class="sidebar-sublinks portfolio-sublinks">
						<li class="portfolio-site-link">
							<a href="<?php openlab_user_portfolio_url() ?>">Site</a>
						</li>

						<?php if ( openlab_user_portfolio_site_is_local() ) : ?>
							<li class="portfolio-dashboard-link">
								<a href="<?php openlab_user_portfolio_url() ?>/wp-admin">Dashboard</a>
							</li>
						<?php endif ?>
					</ul>
				<?php endif ?>
			</div>
		<?php } else {

			echo "<ul class='website-links'>";

			echo "<li id='site-link'><a href='" . trailingslashit( esc_attr( $site_url ) ) . "'>" . ucwords( groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' ) ) . " Site</a></li>";

			// Only show the local admin link. Group members only
			if ( $is_local && bp_group_is_member() ) {
				echo "<li><a href='" . esc_attr( trailingslashit( $site_url ) ) . "wp-admin/'>Dashboard</a></li>";
			}

			echo '</ul>';
		}
	}
}
add_action( 'bp_group_options_nav', 'wds_bp_group_site_pages' );

/**
 * Catch 'unlink-site' requests, process, and send back
 */
function openlab_process_unlink_site() {
	if ( bp_is_group_admin_page( 'edit-details' ) && bp_is_action_variable( 'unlink-site', 1 ) ) {
		check_admin_referer( 'unlink-site' );

		$meta_to_delete = array(
			'external_site_url',
			'wds_bp_group_site_id',
			'external_site_comments_feed',
			'external_site_posts_feed'
		);

		foreach( $meta_to_delete as $m ) {
			groups_delete_groupmeta( bp_get_current_group_id(), $m );
		}
	}
}
add_action( 'bp_actions', 'openlab_process_unlink_site', 1 );

/**
 * Renders the markup for group-site affilitation
 */
function wds_bp_group_meta(){
	global $wpdb, $bp, $current_site, $base;

	$the_group_id = bp_is_group() ? bp_get_current_group_id() : 0;

	$group_type = openlab_get_group_type( $the_group_id );

	if ( 'group' == $group_type && isset( $_GET['type'] ) ) {
		$group_type = $_GET['type'];
	}

        // Sanitization for the group type. We'll check plurals too, in case
        // the $_GET param gets messed up
        if ( 's' == substr( $group_type, -1 ) ) {
                $group_type = substr( $group_type, 0, strlen( $group_type ) - 1 );
        }

        if ( !in_array( $group_type, openlab_group_types() ) ) {
                $group_type = 'group';
        }

	if ( 'group' == $group_type ) {
		$type = isset( $_COOKIE["wds_bp_group_type"] ) ? $_COOKIE['wds_bp_group_type'] : '';
	}

	$group_school       = groups_get_groupmeta($the_group_id, 'wds_group_school' );
	$group_project_type = groups_get_groupmeta($the_group_id, 'wds_group_project_type' );

	if ( 'portfolio' == $group_type ) {
		$group_label = openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() );
	} else {
		$group_label = $group_type;
	}

	?>

    <div class="ct-group-meta">

	<?php

	if ( !empty( $group_type ) && $group_type != "group" ) {
		  echo wds_load_group_type( $group_type ); ?>
                  <input type="hidden" name="group_type" value="<?php echo $group_type;?>" />
                  <?php
	}

	$group_site_url = openlab_get_group_site_url( $the_group_id ); ?>

	<?php if ( !empty( $group_site_url ) ) : ?>

		<div id="current-group-site">

			<?php $maybe_site_id = openlab_get_site_id_by_group_id( $the_group_id );

			if ( $maybe_site_id ) {
				$group_site_name = get_blog_option( $maybe_site_id, 'blogname' );
				$group_site_text = '<strong>' . $group_site_name . '</strong> (<a href="' . $group_site_url . '">' . $group_site_url . '</a>)';
			} else {
				$group_site_text = '<strong><a href="' . $group_site_url . '">' . $group_site_url . '</a></strong>';
			}

			?>
			<p>This <?php echo $group_type ?> is currently associated with the site <?php echo $group_site_text ?>. <span id="change-group-site"><a class="button confirm" href="<?php echo wp_nonce_url( bp_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/unlink-site/', 'unlink-site' ) ?>" id="change-group-site-toggle" />Unlink</a></p>

		</div>

	<?php else : ?>

		<?php

		switch ( $group_type ) {
			case 'portfolio' :
				$account_type = strtolower( xprofile_get_field_data( 'Account Type', bp_loggedin_user_id() ) );

				switch ( $account_type ) {
					case 'faculty' :
						$template = 'template-portfolio';
						break;
					case 'staff' :
						$template = 'template-portfolio-staff';
						break;
					case 'student' :
						$template = 'template-eportfolio';
						break;
				}
				break;

			default :
				$template = "template-" . strtolower( $group_type );
				break;
		}

		$blog_details = get_blog_details( $template );

		?>
		<style type="text/css">
		.disabled-opt {
			opacity: .4;
		}
		</style>

		<input type="hidden" name="action" value="copy_blog" />
		<input type="hidden" name="source_blog" value="<?php echo $blog_details->blog_id; ?>" />

		<table class="form-table groupblog-setup"<?php if ( !empty( $group_site_url ) ) : ?> style="display: none;"<?php endif ?>>
			<?php if ( $group_type != "course" && $group_type != 'portfolio' ) : ?>
				<?php $show_website = "none" ?>
				<tr class="form-field form-required">
					<th scope='row'>
						<input type="checkbox" name="wds_website_check" value="yes" /> Set up a site?
					</th>
				</tr>
			<?php else : ?>
				<?php $show_website = 'block' ?>

				<?php if ( 'course' == $group_type ) : ?>
					<tr class="form-field form-required">
						<th>Site Details</th>
					</tr>
				<?php endif ?>
			<?php endif ?>

			<tr id="wds-website-tooltips" class="form-field form-required" style="display:<?php echo $show_website;?>"><td colspan="2">
				<?php switch ( $group_type ) :
					case 'course' : ?>
						<p class="ol-tooltip">Take a moment to consider the address for your site. You will not be able to change it once you've created it. If this Course site will be used again on the OpenLab, you may want to keep it simple. We recommend the following format:</p>

						<ul class="ol-tooltip">
							<li>FacultyLastNameCourseCode</li>
							<li>smithadv1100</li>
						</ul>

						<p class="ol-tooltip">If you plan to create a new course each semester, you may choose to add Semester and Year.</p>

						<ul class="ol-tooltip">
							<li>FacultyLastNameCourseCodeSemYear</li>
							<li>smithadv1100sp2012</li>
						</ul>

						<p class="ol-tooltip">If you teach multiple sections and plan to create additional course sites on the OpenLab, consider adding other identifying information to the URL.</p>

						<?php break;
					case 'project' : ?>
						<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu.</p>

						<p class="ol-tooltip"><strong>Is this an ePortfolio?</strong> Since the ePortfolio is designed to be a Career Portfolio, choose a site address that will appear professional. We recommend one of the following formats (enter in the gray box below):</p>

						<ul class="ol-tooltip">
							<li>FirstNameLastName_eportfolio</li>
							<li>JaneSmith_eportfolio (Example)</li>
							<li>FirstInitialLastName_eportfolio</li>
							<li>JSmith_eportfolio (Example)</li>
						</ul>

						<?php break;
					case 'club' : ?>
						<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu. </p>

						<?php break ?>

				<?php endswitch ?>
			</td></tr>

			<tr id="wds-website" class="form-field form-required" style="display:<?php echo $show_website;?>">
				<th valign="top" scope='row'>

					<input type="radio" class="noo_radio" name="new_or_old" id="new_or_old_new" value="new" />
					Create a new site:
				</th>

				<td id="noo_new_options">
				<?php

				$suggested_path = $group_type == 'portfolio' ? openlab_suggest_portfolio_path() : '';

				if( constant( "VHOST" ) == 'yes' ) : ?>
					<input size="40" name="blog[domain]" type="text" title="<?php _e('Domain') ?>" value="<?php $suggested_path ?>" />.<?php echo $current_site->domain;?>
				<?php else:
					echo $current_site->domain . $current_site->path ?><input size="40" name="blog[domain]" type="text" title="<?php _e('Domain') ?>" value="<?php echo $suggested_path ?>" />
				<?php endif; ?>

				</td>
			</tr>

                        <?php /* Existing blogs - only display if some are available */ ?>
                        <?php
                        $user_blogs = get_blogs_of_user( get_current_user_id() );

                        // Exclude blogs already used as groupblogs
                        global $wpdb, $bp;
                        $current_groupblogs = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ) );

                        foreach( $user_blogs as $ubid => $ub ) {
                                if ( in_array( $ubid, $current_groupblogs ) ) {
                                        unset( $user_blogs[$ubid] );
                                }
                        }
                        $user_blogs = array_values( $user_blogs );

                        // Exclude blogs where the user is not an Admin
                        foreach( $user_blogs as $ubid => $ub ) {
                                $role = get_user_meta( bp_loggedin_user_id(), $wpdb->base_prefix . $ub->userblog_id . '_capabilities', true );

                                if ( !array_key_exists( 'administrator', (array) $role ) ) {
                                        unset( $user_blogs[$ubid] );
                                }
                        }
                        $user_blogs = array_values( $user_blogs );

                        ?>

                        <?php if ( !empty( $user_blogs ) ) : ?>
                                <tr id="wds-website-existing" class="form-field form-required" style="display:<?php echo $show_website;?>">
                                        <th valign="top" scope='row'>
                                                <input type="radio" class="noo_radio" id="new_or_old_old" name="new_or_old" value="old" />
                                                Use an existing site:
                                        </th>

                                        <td id="noo_old_options">
                                                <select name="groupblog-blogid" id="groupblog-blogid">
                                                        <option value="0">- Choose a site -</option>
                                                        <?php foreach( (array)$user_blogs as $user_blog ) : ?>
                                                                <option value="<?php echo $user_blog->userblog_id; ?>"><?php echo $user_blog->blogname; ?></option>
                                                        <?php endforeach ?>
                                                </select>
                                        </td>
                                </tr>
                        <?php endif ?>

			<tr id="wds-website-external" class="form-field form-required" style="display:<?php echo $show_website;?>">
				<th valign="top" scope='row'>
					<input type="radio" class="noo_radio" id="new_or_old_external" name="new_or_old" value="external" />
					Use an external site:
				</th>

				<td id="noo_external_options">
					<input size="50" type="text" name="external-site-url" id="external-site-url" placeholder="http://" /> <a class="button" id="find-feeds" href="#" display="none">Check</a>
				</td>
			</tr>
		</table>
        
        <p id="check-note">Note: Please click the Check button to search for Post and Comment feeds for your external site. Doing so will push new activity to your <Group> Profile page. If no feeds are detected, you may type in the Post and Comment feed URL's directly or just leave blank.</p>

	<?php endif ?>
	</div>
	<?php
}
add_action( 'bp_after_group_details_creation_step', 'wds_bp_group_meta');
add_action( 'bp_after_group_details_admin', 'wds_bp_group_meta');


/**
 * Server side group blog URL validation
 *
 * When you attempt to create a groupblog, this function catches the request and checks to make sure
 * that the URL is not used. If it is, an error is sent back.
 */
function openlab_validate_groupblog_url() {
	global $current_blog;

	if ( isset( $_POST['wds_website_check'] ) ) {
		$path = isset( $_POST['blog']['domain'] ) ? $_POST['blog']['domain'] : '';

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
	if ( !is_page( 'my-sites' ) ) {
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
	$exclude_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ) );

	if ( !empty( $exclude_blogs ) ) {
		$exclude_sql = $wpdb->prepare( " AND b.blog_id NOT IN (" . implode( ',', $exclude_blogs ) . ") " );
	} else {
		$exclude_sql = '';
	}

	if ( !is_user_logged_in() || ( !is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
		$hidden_sql = "AND wb.public = 1";
	else
		$hidden_sql = '';

	$pag_sql = ( $limit && $page ) ? $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) ) : '';

	$user_sql = !empty( $user_id ) ? $wpdb->prepare( " AND b.user_id = %d", $user_id ) : '';

	switch ( $type ) {
		case 'active': default:
			$order_sql = "ORDER BY bm.meta_value DESC";
			break;
		case 'alphabetical':
			$order_sql = "ORDER BY bm2.meta_value ASC";
			break;
		case 'newest':
			$order_sql = "ORDER BY wb.registered DESC";
			break;
		case 'random':
			$order_sql = "ORDER BY RAND()";
			break;
	}

	if ( !empty( $search_terms ) ) {
		$filter = like_escape( $wpdb->escape( $search_terms ) );
		$paged_blogs = $wpdb->get_results( "SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' AND bm2.meta_value LIKE '%%$filter%%' {$user_sql} {$exclude_sql} GROUP BY b.blog_id {$order_sql} {$pag_sql}" );
		$total_blogs = $wpdb->get_var( "SELECT COUNT(DISTINCT b.blog_id) FROM {$bp->blogs->table_name} b, {$wpdb->base_prefix}blogs wb, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2 WHERE b.blog_id = wb.blog_id AND bm.blog_id = b.blog_id AND bm2.blog_id = b.blog_id AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} AND bm.meta_key = 'name' AND bm2.meta_key = 'description' AND ( bm.meta_value LIKE '%%$filter%%' || bm2.meta_value LIKE '%%$filter%%' ) {$user_sql} {$exclude_sql}" );
	} else {
		$paged_blogs = $wpdb->get_results( $wpdb->prepare( "SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id {$user_sql} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} {$exclude_sql} AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' GROUP BY b.blog_id {$order_sql} {$pag_sql}" ) );
		$total_blogs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT b.blog_id) FROM {$bp->blogs->table_name} b, {$wpdb->base_prefix}blogs wb WHERE b.blog_id = wb.blog_id {$user_sql} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql} {$exclude_sql}" ) );
	}

	$blog_ids = array();
	foreach ( (array)$paged_blogs as $blog ) {
		$blog_ids[] = $blog->blog_id;
	}

	$blog_ids = $wpdb->escape( join( ',', (array)$blog_ids ) );
	$paged_blogs = BP_Blogs_Blog::get_blog_extras( $paged_blogs, $blog_ids, $type );

	return array( 'blogs' => $paged_blogs, 'total' => $total_blogs );
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

	$blog_public = false;
	$group_id = bp_get_group_id();
	$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );

	if($wds_bp_group_site_id!=""){
		$blog_private = get_blog_option( $wds_bp_group_site_id, 'blog_public' );

		switch ( $blog_private ) {
			case '-3' : // todo?
			case '-2' :
				if ( is_user_logged_in() ) {
					$user_capabilities = get_user_meta($user_ID,'wp_' . $wds_bp_group_site_id . '_capabilities',true);
					if ($user_capabilities != "") {
						$blog_public = true;
					}
				}
				break;

			case '-1' :
				if ( is_user_logged_in() ) {
					$blog_public = true;
				}
				break;

			default :
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

	<?php $posts_feed_url = groups_get_groupmeta( $group_id, 'external_site_posts_feed' ) ?>
	<?php $comments_feed_url = groups_get_groupmeta( $group_id, 'external_site_comments_feed' ) ?>

	<?php if ( $posts_feed_url || $comments_feed_url ) : ?>
		<p>We located the following RSS feed URLs for your external site. Correct errors or provide missing feed addresses in the fields below.</p>
	<?php else : ?>
		<p>We weren't able to auto-locate your RSS feeds. If your site has RSS feeds, enter their addresses below.</p>
	<?php endif ?>

	<p><label for="external-site-posts-feed">Posts:</label> <input id="external-site-posts-feed" name="external-site-posts-feed" value="<?php echo esc_attr( $posts_feed_url ) ?>" /></p>

	<p><label for="external-site-comments-feed">Comments:</label> <input id="external-site-comments-feed" name="external-site-comments-feed" value="<?php echo esc_attr( $comments_feed_url ) ?>" /></p>

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
			set_transient( 'openlab_external_posts_' . $group_id, $posts, 60*10 );

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
			set_transient( 'openlab_external_comments_' . $group_id, $comments, 60*10 );

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

	if ( empty( $feed_posts ) || is_wp_error( $feed_posts ) ) {
		return;
	}

	$items = array();

	foreach( $feed_posts->get_items( 0, $num_items ) as $key => $feed_item ) {
		$items[] = array(
			'permalink' => $feed_item->get_link(),
			'title'     => $feed_item->get_title(),
			'content'   => strip_tags( bp_create_excerpt( $feed_item->get_content(), 135, array( 'html' => true ) ) ),
			'author'    => $feed_item->get_author(),
			'date'      => $feed_item->get_date()
		);
	}

	return $items;
}

/**
 * Convert RSS items to activity items
 */
function openlab_convert_feed_to_activity( $items = array(), $item_type = 'posts' ) {
	$type = 'posts' == $item_type ? 'new_blog_post' : 'new_blog_comment';

	$hide_sitewide = false;
	if ( $group = groups_get_current_group() && isset( $group->status ) && 'public' != $group->status ) {
		$hide_sitewide = true;
	}

	foreach( (array) $items as $item ) {
		// Make sure we don't have duplicates
		// We'll check based on content + time of publication
		if ( !openlab_external_activity_item_exists( $item['date'], $item['content'], $type ) ) {
			$action = '';

			$group           = groups_get_current_group();
			$group_name      = $group->name;
			$group_permalink = bp_get_group_permalink( $group );
			$group_type      = openlab_group_type( 'lower', 'single', $group->id );

			if ( 'posts' == $item_type ) {
				$action = sprintf( 'A new post %s was published in the ' . $group_type . ' %s',
					'<a href="' . esc_attr( $item['permalink'] ) . '">' . esc_html( $item['title'] ) . '</a>',
					'<a href="' . $group_permalink . '">' . $group_name . '</a>'
				);
			} else if ( 'comments' == $item_type ) {
				$action = sprintf( 'A new comment was posted on the post %s in the ' . $group_type . ' %s',
					'<a href="' . esc_attr( $item['permalink'] ) . '">' . esc_html( $item['title'] ) . '</a>',
					'<a href="' . $group_permalink . '">' . $group_name . '</a>'
				);
			}

			$args = array(
				'action'            => $action,
				'content'           => $item['content'],
				'component'         => 'groups',
				'type'              => $type,				'primary_link'      => $item['permalink'],
				'user_id'           => 0, // todo
				'item_id'           => bp_get_current_group_id(), // improve?
				'recorded_time'     => date( 'Y-m-d H:i:s', strtotime( $item['date'] ) ),
				'hide_sitewide'     => $hide_sitewide
			);

			remove_action( 'bp_activity_before_save', 'openlab_group_blog_activity' );
			bp_activity_add( $args );
		}
	}
}

/**
 * Check to see whether an external blog post activity item exists for this item already
 *
 * We do this manually because BP doesn't allow for easy querying by exact time
 *
 * @param str Date of original post
 * @param str Content of original post
 * @return bool
 */
function openlab_external_activity_item_exists( $date = '', $content = '', $type = 'new_blog_post' ) {
	global $wpdb, $bp;

	if ( !is_numeric( $date ) ) {
		$date = strtotime( $date );
	}

	$date = date( 'Y-m-d H:i:s', $date );

	$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE date_recorded = %s AND type = %s AND content = %s", $date, $type, $content );

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
			'comments' => '{{URL}}/comments/feed'
		),
		'blogger' => array(
			'posts'    => '{{URL}}feeds/posts/default?alt=rss',
			'comments' => '{{URL}}feeds/comments/default?alt=rss'
		),
		'drupal' => array(
			'posts'    => '{{URL}}posts/feed'
		)
	);

	$feed_urls = array();

	foreach( $formats as $ftype => $f ) {
		$maybe_feed_url = str_replace( '{{URL}}', trailingslashit( $url ), $f['posts'] );
		$maybe_feed = wp_remote_get( $maybe_feed_url );
		if ( !is_wp_error( $maybe_feed ) && 200 == $maybe_feed['response']['code'] ) {

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
				$maybe_comments_feed = wp_remote_get( $maybe_comments_feed_url );

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
	$url = isset( $_REQUEST['site_url'] ) ? $_REQUEST['site_url'] : '';
	$feeds = openlab_find_feed_urls( $url );

	die( json_encode( $feeds ) );
}
add_action( 'wp_ajax_openlab_detect_feeds', 'openlab_detect_feeds_handler' );
?>
