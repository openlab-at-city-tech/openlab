<?php
/**
 *  Member related functions
 *
 */
use OpenLab\Favorites\Favorite\Query;

function openlab_is_admin_truly_member( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group = & $groups_template->group;
	}

	return apply_filters( 'bp_group_is_member', ! empty( $group->is_member ) );
}

function openlab_flush_user_cache_on_save( $user_id, $posted_field_ids, $errors ) {

	clean_user_cache( $user_id );
}

add_action( 'xprofile_updated_profile', 'openlab_flush_user_cache_on_save', 10, 3 );

/**
 *  People archive page
 *
 */
function openlab_list_members( $view ) {
	global $wpdb, $bp, $members_template, $wp_query;

	$valid_user_types = openlab_valid_user_types();

	$user_type = openlab_get_current_filter( 'member_type' );
	if ( $user_type && $user_type !== 'all' ) {
		$valid_user_types = openlab_valid_user_types();
		$user_type        = $valid_user_types[ $user_type ]['label'];
	}

	$search_terms = openlab_get_current_filter( 'search' );

	$school = openlab_get_current_filter( 'school' );
	if ( $school ) {
		$user_school = urldecode( $school );

		// Sanitize
		$schools = openlab_get_school_list();
		if ( ! isset( $schools[ $user_school ] ) ) {
			$user_school = '';
		}
	}

	$office = openlab_get_current_filter( 'office' );
	if ( $office ) {
		$user_office = urldecode( $office );

		// Sanitize
		$offices = openlab_get_office_list();
		if ( ! isset( $offices[ $user_office ] ) ) {
			$user_office = '';
		}
	}

	$user_department = openlab_get_current_filter( 'department' );
	if ( $user_department ) {
		$user_department = urldecode( $_GET['department'] );
	}

	// Set up the bp_has_members() arguments
	$args = array(
		'member_type' => $user_type,
		'per_page'    => 48,
		'type'        => openlab_get_current_filter( 'sort' ),
	);

	// Set up $include
	// $include_noop is a flag that gets triggered when one of the search
	// conditions returns no items. If that happens, don't bother doing
	// the other queries, and just return a null result
	$include_arrays = array();
	$include_noop   = false;

	if ( $search_terms && ! $include_noop ) {
		// The first and last name fields are private, so they should
		// not show up in search results
		$first_name_field_id = xprofile_get_field_id_from_name( 'First Name' );
		$last_name_field_id  = xprofile_get_field_id_from_name( 'Last Name' );

		// Split the search terms into separate words
		$search_terms_a = explode( ' ', $search_terms );

		$search_query = "SELECT user_id
			 FROM {$bp->profile->table_name_data}
			 WHERE field_id NOT IN ({$first_name_field_id}, {$last_name_field_id})";

		if ( ! empty( $search_terms_a ) ) {
			$match_clauses = array();
			foreach ( $search_terms_a as $search_term ) {
				$match_clauses[] = "value LIKE '%" . esc_sql( like_escape( $search_term ) ) . "%'";
			}
			$search_query .= ' AND ( ' . implode( ' AND ', $match_clauses ) . ' )';
		}

		$search_terms_matches = $wpdb->get_col( $search_query );

		if ( empty( $search_terms_matches ) ) {
			$include_noop = true;
		} else {
			$include_arrays[] = $search_terms_matches;
		}
	}

	if ( $user_school && ! $include_noop ) {
		$user_school_matches = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'openlab_school' AND
            meta_value = %s",
				$user_school
			)
		);

		if ( empty( $user_school_matches ) ) {
			$include_noop = true;
		} else {
			$include_arrays[] = $user_school_matches;
		}
	}

	if ( $user_office && ! $include_noop ) {
		$user_office_matches = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'openlab_office' AND
            meta_value = %s",
				$user_office
			)
		);

		if ( empty( $user_office_matches ) ) {
			$include_noop = true;
		} else {
			$include_arrays[] = $user_office_matches;
		}
	}

	if ( $user_department && ! $include_noop && 'all' !== $user_department ) {
		$user_department_matches = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'openlab_department' AND
            meta_value = %s",
				$user_department
			)
		);

		if ( empty( $user_department_matches ) ) {
			$include_noop = true;
		} else {
			$include_arrays[] = $user_department_matches;
		}
	}

	// Parse the results into a single 'include' parameter
	if ( $include_noop ) {
		$include = array( 0 );
	} elseif ( ! empty( $include_arrays ) ) {
		foreach ( $include_arrays as $iak => $ia ) {
			// On the first go-round, seed the temp variable with
			// the first set of includes
			if ( ! isset( $include ) ) {
				$include = $ia;

				// On subsequent iterations, do array_intersect() to
				// trim down the included users
			} else {
				$include = array_intersect( $include, $ia );
			}
		}

		if ( empty( $include ) ) {
			$include = array( 0 );
		}
	}

	if ( ! empty( $include ) ) {
		$args['include'] = array_unique( $include );
	}

	$avatar_args = array(
		'type'   => 'full',
		'width'  => 72,
		'height' => 72,
		'class'  => 'avatar',
		'id'     => false,
		'alt'    => __( 'Member avatar', 'buddypress' ),
	);
	?>

	<?php if ( bp_has_members( $args ) ) : ?>
		<div class="row group-archive-header-row">
			<div class="current-group-filters current-portfolio-filters col-md-18 col-sm-16">Use the search and filters to find People.</div>
			<div class="col-md-6 col-sm-8 text-right"><?php cuny_members_pagination_count( 'members' ); ?></div>
		</div>

		<div id="group-members-list" class="group-list item-list row">
			<?php
			while ( bp_members() ) :
				bp_the_member();
				//the following checks the current $id agains the passed list from the query
				$member_id = $members_template->member->id;

				$registered = bp_format_time( strtotime( $members_template->member->user_registered ), true )
				?>
				<div class="group-item col-md-8 col-xs-12">
					<div class="group-item-wrapper">
						<div class="row">
							<div class="item-avatar col-md-10 col-xs-8">
								<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="
																	  <?php
																		echo bp_core_fetch_avatar(
																			array(
																				'item_id' => bp_get_member_user_id(),
																				'object'  => 'member',
																				'type'    => 'full',
																				'html'    => false,
																			)
																		);
																		?>
																		" alt="<?php echo esc_attr( sprintf( 'Avatar of %s', bp_get_member_name() ) ); ?>"/></a>
							</div>
							<div class="item col-md-14 col-xs-16">
								<h2 class="item-title"><a class="truncate-on-the-fly no-deco" data-basewidth="100" data-basevalue="20" data-minvalue="20" data-srprovider="true" href="<?php bp_member_permalink(); ?>" title="<?php bp_member_name(); ?>"><?php bp_member_name(); ?></a></h2>
								<span class="member-since-line timestamp">Member since <?php echo $registered; ?></span>
								<?php if ( bp_get_member_latest_update() ) : ?>
									<span class="update"><?php bp_member_latest_update( 'length=10' ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

			<?php endwhile; ?>
		</div>
		<div id="pag-top" class="pagination">

			<div class="pagination-links" id="member-dir-pag-top">
				<?php echo openlab_members_pagination_links(); ?>
			</div>

		</div>

		<?php
	else :
		if ( $user_type == 'Student' ) {
			$user_type = 'students';
		}

		if ( empty( $user_type ) ) {
			$user_type = 'people';
		}
		?>
		<div class="row group-archive-header-row">
			<div class="current-group-filters current-portfolio-filters col-sm-18">&nbsp;</div>
		</div>

		<div id="group-members-list" class="item-list group-list row">
			<div class="widget-error query-no-results col-sm-24">
				<p class="bold"><?php _e( 'There are no ' . strtolower( $user_type ) . ' to display.', 'buddypress' ); ?></p>
			</div>
		</div>

		<?php
	endif;
}

function openlab_members_pagination_links( $page_args = 'upage' ) {
	global $members_template;

	$pagination = paginate_links(
		array(
			'base'      => add_query_arg( $page_args, '%#%' ),
			'format'    => '',
			'total'     => ceil( (int) $members_template->total_member_count / (int) $members_template->pag_num ),
			'current'   => (int) $members_template->pag_page,
			'prev_text' => _x( '<i class="fa fa-angle-left" aria-hidden="true"></i><span class="sr-only">Previous</span>', 'Group pagination previous text', 'buddypress' ),
			'next_text' => _x( '<i class="fa fa-angle-right" aria-hidden="true"></i><span class="sr-only">Next</span>', 'Group pagination next text', 'buddypress' ),
			'mid_size'  => 3,
			'type'      => 'list',
		)
	);

	$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );
	return $pagination;
}

//a variation on bp_members_pagination_count() to match design
function cuny_members_pagination_count( $member_name ) {
	global $bp, $members_template;

	if ( empty( $members_template->type ) ) {
		$members_template->type = '';
	}

	$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
	$from_num  = bp_core_number_format( $start_num );
	$to_num    = bp_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
	$total     = bp_core_number_format( $members_template->total_member_count );

	$pag = sprintf( __( '%1$s to %2$s (of %3$s)', 'buddypress' ), $from_num, $to_num, $total );
	echo $pag;
}

function openlab_displayed_user_account_type() {
	echo openlab_get_displayed_user_account_type();
}

function openlab_get_displayed_user_account_type() {
	return openlab_get_user_member_type( bp_displayed_user_id() );
}

/**
 * Prints a status message regarding the group visibility.
 *
 * @global BP_Groups_Template $groups_template Groups template object
 * @param object $group Group to get status message for. Optional; defaults to current group.
 */
function openlab_group_status_message( $group = null ) {
	global $groups_template;

	if ( ! $group ) {
		$group = & $groups_template->group;
	}

	$group_label = openlab_get_group_type_label( 'group_id=' . $group->id . '&case=upper' );

	$site_id  = openlab_get_site_id_by_group_id( $group->id );
	$site_url = openlab_get_group_site_url( $group->id );

	$site_status = 1;
	if ( $site_url ) {
		// If we have a site URL but no ID, it's an external site, and is public
		if ( ! $site_id ) {
			$site_status = 1;
		} else {
			$site_status = get_blog_option( $site_id, 'blog_public' );
		}
	}

	$site_status = (float) $site_status;

	$message = '';

	$public_group_has_disabled_joining   = openlab_public_group_has_disabled_joining( $group->id );
	$private_group_has_disabled_requests = openlab_private_group_has_disabled_membership_requests( $group->id );

	switch ( $site_status ) {
		// Public
		case 1:
		case 0:
			if ( 'public' === $group->status ) {
				if ( $public_group_has_disabled_joining ) {
					$message = 'This ' . $group_label . ' is OPEN but membership is by invitation.';
				} else {
					$message = 'This ' . $group_label . ' is OPEN.';
				}
			} elseif ( ! $site_url ) {
				// Special case: $site_status will be 0 when the
				// group does not have an associated site. When
				// this is the case, and the group is not
				// public, don't mention anything about the Site.
				if ( $private_group_has_disabled_requests ) {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE and membership is by invitation only.' : 'This ' . $group_label . ' is HIDDEN and membership is by invitation only.';
				} else {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE.' : 'This ' . $group_label . ' is HIDDEN';
				}
			} else {
				if ( $private_group_has_disabled_requests ) {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' Profile is PRIVATE and membership is by invitation only, but the ' . $group_label . ' Site is OPEN to all visitors.' : 'This ' . $group_label . ' Profile is HIDDEN and membership is by invitation only, but the ' . $group_label . ' Site is OPEN to all visitors.';
				} else {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' Profile is PRIVATE, but the ' . $group_label . ' Site is OPEN to all visitors.' : 'This ' . $group_label . ' Profile is HIDDEN, but the ' . $group_label . ' Site is OPEN to all visitors.';
				}
			}

			break;

		case -1:
			if ( 'public' === $group->status ) {
				if ( $public_group_has_disabled_joining ) {
					$message = 'This ' . $group_label . ' Profile is OPEN and membership is by invitation only, but only logged-in OpenLab members may view the ' . $group_label . ' Site.';
				} else {
					$message = 'This ' . $group_label . ' Profile is OPEN, but only logged-in OpenLab members may view the ' . $group_label . ' Site.';
				}
			} else {
				if ( $private_group_has_disabled_requests ) {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' Profile is PRIVATE and is by invitation only, but all logged-in OpenLab members may view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' Profile is HIDDEN and is by invitation only, but all logged-in OpenLab members may view the ' . $group_label . ' Site.';
				} else {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' Profile is PRIVATE, but all logged-in OpenLab members may view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' Profile is HIDDEN, but all logged-in OpenLab members may view the ' . $group_label . ' Site.';
				}
			}

			break;

		case -2:
			if ( 'public' === $group->status ) {
				if ( $public_group_has_disabled_joining ) {
					$message = 'This ' . $group_label . ' Profile is OPEN but membership is by invitation. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.';
				} else {
					$message = 'This ' . $group_label . ' Profile is OPEN, but the ' . $group_label . ' Site is PRIVATE.';
				}
			} else {
				if ( $private_group_has_disabled_requests ) {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE and membership is by invitation only. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' is HIDDEN and membership is by invitation only. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.';
				} else {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' is HIDDEN. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.';
				}
			}

			break;

		case -3:
			if ( 'public' === $group->status ) {
				if ( $public_group_has_disabled_joining ) {
					$message = 'This ' . $group_label . ' Profile is OPEN but membership is by invitation. You must be an administrator to view the ' . $group_label . ' Site.';
				} else {
					$message = 'This ' . $group_label . ' Profile is OPEN, but you must be an administrator to view the ' . $group_label . ' Site.';
				}
			} else {
				if ( $private_group_has_disabled_requests ) {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE and membership is by invitation only. You must be an administrator to view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' is HIDDEN and membership is by invitation only. You must be an administrator to view the ' . $group_label . ' Site.';
				} else {
					$message = 'private' === $group->status ? 'This ' . $group_label . ' is PRIVATE. You must be an administrator to view the ' . $group_label . ' Site.' : 'This ' . $group_label . ' is HIDDEN. You must be an administrator to view the ' . $group_label . ' Site.';
				}
			}

			break;
	}

	return $message;
}

function openlab_get_groups_of_user( $args = array() ) {
	global $bp, $wpdb;

	$retval = array(
		'group_ids'     => array(),
		'group_ids_sql' => '',
		'activity'      => array(),
	);

	$defaults = array(
		'user_id'      => bp_loggedin_user_id(),
		'show_hidden'  => true,
		'group_type'   => 'club',
		'get_activity' => true,
	);
	$r        = wp_parse_args( $args, $defaults );

	$select = $where = '';

	$select = "SELECT a.group_id FROM {$bp->groups->table_name_members} a";
	$where  = $wpdb->prepare( 'WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d', $r['user_id'] );

	if ( ! $r['show_hidden'] ) {
		$select .= " JOIN {$bp->groups->table_name} c ON ( c.id = a.group_id ) ";
		$where  .= " AND c.status != 'hidden' ";
	}

	if ( 'all' != $r['group_type'] ) {
		// Sanitize
		$group_type = in_array( strtolower( $r['group_type'] ), array( 'club', 'project', 'course' ) ) ? strtolower( $r['group_type'] ) : 'club';

		$select .= " JOIN {$bp->groups->table_name_groupmeta} d ON ( a.group_id = d.group_id ) ";
		$where  .= $wpdb->prepare( " AND d.meta_key = 'wds_group_type' AND d.meta_value = %s ", $group_type );
	}

	$sql = $select . ' ' . $where;

	$group_ids = $wpdb->get_col( $sql );

	$retval['group_ids'] = $group_ids;

	// Now that we have group ids, get the associated activity items and format the
	// whole shebang in the proper way
	if ( ! empty( $group_ids ) ) {
		$retval['group_ids_sql'] = implode( ',', $group_ids );

		if ( $r['get_activity'] ) {
			// bp_has_activities() doesn't allow arrays of item_ids, so query manually
			$activities = $wpdb->get_results( "SELECT id,item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ( {$retval['group_ids_sql']} ) ORDER BY id DESC" );

			// Now walk down the list and try to match with a group. Once one is found, remove
			// that group from the stack
			$group_activity_items = array();
			foreach ( (array) $activities as $act ) {
				if ( ! empty( $act->content ) && in_array( $act->item_id, $group_ids ) && ! isset( $group_activity_items[ $act->item_id ] ) ) {
					$group_activity_items[ $act->item_id ] = $act->content;
					$key                                   = array_search( $act->item_id, $group_ids );
					unset( $group_ids[ $key ] );
				}
			}

			$retval['activity'] = $group_activity_items;
		}
	}

	return $retval;
}

function cuny_student_profile() {
	global $user_ID, $bp;

	do_action( 'bp_before_member_home_content' );
	?>

	<?php if ( bp_is_user_activity() || 'public' == bp_current_action() ) { ?>
		<?php cuny_member_profile_header(); ?>
		<div id="portfolio-sidebar-inline-widget" class="visible-xs sidebar sidebar-inline"><?php openlab_members_sidebar_blocks(); ?></div>
	<?php } ?>

	<div id="member-item-body" class="row">

		<?php echo cuny_profile_activty_block( 'course', 'My Courses', '', 25 ); ?>
		<?php echo cuny_profile_activty_block( 'project', 'My Projects', ' last', 25 ); ?>
		<?php echo cuny_profile_activty_block( 'club', 'My Clubs', ' last', 25 ); ?>

		<script type='text/javascript'>(function ($) {
				$('.activity-list').css('visibility', 'hidden');
			})(jQuery);</script>
		<?php
		if ( bp_is_active( 'friends' ) ) :
			if ( ! $friend_ids = wp_cache_get( 'friends_friend_ids_' . $bp->displayed_user->id, 'bp' ) ) {
				$friend_ids = BP_Friends_Friendship::get_random_friends( $bp->displayed_user->id, 20 );
				wp_cache_set( 'friends_friend_ids_' . $bp->displayed_user->id, $friend_ids, 'bp' );
			}
			?>

			<div id="members-list" class="info-group col-xs-24">

				<?php if ( $friend_ids ) { ?>

					<h2 class="title activity-title"><a class="no-deco" href="<?php echo $bp->displayed_user->domain . $bp->friends->slug; ?>"><?php bp_word_or_name( __( 'My Friends', 'buddypress' ), __( "%s's Friends", 'buddypress' ) ); ?><span class="fa fa-chevron-circle-right font-size font-18" aria-hidden="true"></span></a></h2>

					<ul id="member-list" class="inline-element-list">

						<?php foreach ( $friend_ids as $friend_id ) { ?>

							<li class="inline-element">
								<a href="<?php echo bp_core_get_user_domain( $friend_id ); ?>">
									<img class="img-responsive" src ="
									<?php
									echo bp_core_fetch_avatar(
										array(
											'item_id' => $friend_id,
											'object'  => 'member',
											'type'    => 'full',
											'html'    => false,
										)
									);
									?>
									" alt="<?php echo bp_core_get_user_displayname( $friend_id ); ?>"/>
								</a>
							</li>

						<?php } ?>

					</ul>
				<?php } else { ?>

					<h2 class="title activity-title"><?php bp_word_or_name( __( 'My Friends', 'buddypress' ), __( "%s's Friends", 'buddypress' ) ); ?></h2>

					<div id="message" class="info">
						<p><?php bp_word_or_name( __( "You haven't added any friend connections yet.", 'buddypress' ), __( "%s hasn't created any friend connections yet.", 'buddypress' ) ); ?></p>
					</div>

				<?php } ?>
			<?php endif; /* bp_is_active( 'friends' ) */ ?>
		</div>
		<?php do_action( 'bp_after_member_body' ); ?>

	</div><!-- #item-body -->

	<?php do_action( 'bp_after_memeber_home_content' ); ?>

	<?php
}

function cuny_profile_activty_block( $type, $title, $last, $desc_length = 135 ) {
	global $wpdb, $bp;

	//echo $type."<hr>";
	$ids          = '9999999';
	$groups_found = array();
	if ( $type != 'blog' ) {
		$get_group_args = array(
			'user_id'       => bp_displayed_user_id(),
			'show_hidden'   => false,
			'active_status' => 'all',
			'group_type'    => $type,
			'get_activity'  => false,
		);

		// Get private groups of the user
		$private_groups = openlab_get_user_private_membership( bp_displayed_user_id() );
		$exclude_groups = '';

		// Exclude private groups if not current user's profile or don't have moderate access.
		if( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) ) {
			$exclude_groups = '&exclude=' . implode(',', $private_groups);
		}

		$groups         = openlab_get_groups_of_user( $get_group_args );

		//echo $ids;
		if ( ! empty( $groups['group_ids_sql'] ) && bp_has_groups( 'include=' . $groups['group_ids_sql'] . '&per_page=20' . $exclude_groups ) ) :
			//    if ( bp_has_groups( 'include='.$ids.'&per_page=3&max=3' ) ) :
			?>
			<div id="<?php echo $type; ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last; ?> col-sm-8 col-xs-12">
				<?php
				if ( $bp->is_item_admin || $bp->is_item_mod ) :
					$href = bp_get_root_domain() . '/my-' . $type . 's';
				else :
					$href = $bp->displayed_user->domain . 'groups/?type=' . $type;
				endif;
				?>
				<h2 class="title activity-title"><a class="no-deco" href="<?php echo $href; ?>"><?php echo $title; ?><span class="fa fa-chevron-circle-right font-size font-18" aria-hidden="true"></span></a></h2>
				<?php $x = 0; ?>
				<?php
				while ( bp_groups() ) :
					bp_the_group();
					?>

					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">

								<div class="activity-avatar col-sm-10 col-xs-8">
									<a href="<?php bp_group_permalink(); ?>"><img class="img-responsive" src ="
																		 <?php
																			echo bp_core_fetch_avatar(
																				array(
																					'item_id' => bp_get_group_id(),
																					'object'  => 'group',
																					'type'    => 'full',
																					'html'    => false,
																				)
																			);
																			?>
																			" alt="<?php echo bp_get_group_name(); ?>"/></a>
								</div>

								<div class="activity-content truncate-combo col-sm-14 col-xs-16">

									<p class="overflow-hidden h6">
										<a class="font-size font-14 no-deco truncate-name truncate-on-the-fly hyphenate" href="<?php bp_group_permalink(); ?>" data-basevalue="34" data-minvalue="20" data-basewidth="143" data-srprovider="true"><?php echo bp_get_group_name(); ?></a>
										<span class="original-copy hidden"><?php echo bp_get_group_name(); ?></span>
									</p>

									<?php $activity = strip_tags( bp_get_group_description() ); ?>
									<div class="truncate-wrapper overflow-hidden">
										<p class="truncate-on-the-fly hyphenate" data-link="<?php echo bp_get_group_permalink(); ?>" data-includename="<?php echo bp_get_group_name(); ?>" data-basevalue="65" data-basewidth="143"><?php echo $activity; ?></p>
										<p class="original-copy hidden"><?php echo $activity; ?></p>
									</div>

									<?php if( current_user_can( 'bp_moderate' ) && in_array( bp_get_group_id(), $private_groups, true ) ) { ?>
									<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> Membership hidden</p>
									<?php } ?>

								</div>

							</div>

						</div>
					</div>

					<?php /* Increment */ ?>
					<?php $x += 1; ?>

					<?php /* Only show 5 items max */ ?>
					<?php
					if ( $x == 5 ) {
						break;
					}
					?>

				<?php endwhile; ?>

			</div>
		<?php else : ?>
			<div id="<?php echo $type; ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last; ?> col-sm-8 col-xs-12">
				<h4><?php echo $title; ?></h4>

				<div class="panel panel-default">
					<div class="panel-body">
						<p>
							<?php
							if ( $type != 'course' ) {
								if ( $bp->loggedin_user->id == $bp->displayed_user->id ) {
									?>
									You aren't participating in any <?php echo $type; ?>s on the OpenLab yet. Why not <a href="<?php echo site_url(); ?>/groups/create/step/group-details/?type=<?php echo $type; ?>&new=true">create a <?php echo $type; ?></a>?
									<?php
								} else {
									echo $bp->displayed_user->fullname;
									?>
									hasn't created or joined any <?php echo $type; ?>s yet.
									<?php
								}
							} else {
								if ( $bp->loggedin_user->id == $bp->displayed_user->id ) {
									?>
									You haven't created any courses yet.
									<?php
								} else {
									echo $bp->displayed_user->fullname;
									?>
									hasn't joined any <?php echo $type; ?>s yet.
									<?php
								}
							}
							?>
						</p>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php
	} else {
		// BLOGS
		global $bp, $wpdb;

		// bp_has_blogs() doesn't let us narrow our options enough
		// Get all group blog ids, so we can exclude them
		$gblogs = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ) );

		$gblogs = implode( ',', $gblogs );

		$blogs_query = $wpdb->prepare(
			"
			SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name
			FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u
			WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id AND b.user_id = {$bp->displayed_user->id} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 AND wb.public = 1 AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' AND b.blog_id NOT IN ({$gblogs}) LIMIT 3"
		);

		$myblogs = $wpdb->get_results( $blogs_query );
		?>



		<div id="<?php echo $type; ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last; ?>">
			<h4><?php echo $title; ?></h4>

			<?php if ( ! empty( $myblogs ) ) : ?>
				<?php foreach ( (array) $myblogs as $myblog ) : ?>
					<li>
						<a href="http://<?php echo trailingslashit( $myblog->domain . $myblog->path ); ?>"><?php echo $myblog->name; ?></a>
					</li>
				<?php endforeach ?>
			<?php else : ?>
				<?php if ( bp_is_my_profile() ) : ?>
					You haven't created or joined any sites yet.
				<?php else : ?>
					<?php echo $bp->displayed_user->fullname; ?> hasn't created or joined any sites yet.
				<?php endif ?>

			<?php endif ?>
		</div>
		<?php
	}
}

function cuny_member_profile_header() {
	global $user_ID, $bp;

	$this_user_id = bp_displayed_user_id();

	$account_type = openlab_get_user_member_type( $this_user_id );

	//
	//     whenever profile is viewed, update user meta for first name and last name so this shows up
	//     in the back end on users display so teachers see the students full name
	//
	$name_member_id    = bp_displayed_user_id();
	$first_name        = xprofile_get_field_data( 'First Name', $name_member_id );
	$last_name         = xprofile_get_field_data( 'Last Name', $name_member_id );
	$update_user_first = update_user_meta( $name_member_id, 'first_name', $first_name );
	$update_user_last  = update_user_meta( $name_member_id, 'last_name', $last_name );
	?>

	<?php
	// Get the displayed user's base domain
	// This is required because the my-* pages aren't really displayed user pages from BP's
	// point of view
	if ( ! $dud = bp_displayed_user_domain() ) {
		$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
	}
	?>

	<div id="member-header" class="member-header row">
		<?php
		do_action( 'bp_before_member_header' );

		$this_user_id = bp_displayed_user_id();
		do_action( 'bp_before_member_home_content' );
		?>

		<div id="member-header-avatar" class="alignleft group-header-avatar col-sm-8 col-xs-12">
			<div id="avatar-wrapper">
				<div class="padded-img darker">
					<img class="img-responsive padded" src ="
					<?php
					echo bp_core_fetch_avatar(
						array(
							'item_id' => $this_user_id,
							'object'  => 'member',
							'type'    => 'full',
							'html'    => false,
						)
					);
					?>
					" alt="<?php echo bp_core_get_user_displayname( $this_user_id ); ?>"/>
				</div>
			</div><!--memeber-header-avatar-->
			<div id="profile-action-wrapper">
				<?php if ( is_user_logged_in() && openlab_is_my_profile() ) : ?>
					<div id="group-action-wrapper">
						<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo $dud . 'profile/edit/'; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Profile</a>
						<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo $dud . 'profile/change-avatar/'; ?>"><i class="fa fa-camera" aria-hidden="true"></i> Change Avatar</a>
					</div>
				<?php elseif ( is_user_logged_in() && ! openlab_is_my_profile() ) : ?>
					<?php bp_add_friend_button( openlab_fallback_user(), bp_loggedin_user_id() ); ?>

					<?php
					echo bp_get_button(
						array(
							'id'                => 'private_message',
							'component'         => 'messages',
							'must_be_logged_in' => true,
							'block_self'        => true,
							'wrapper_id'        => 'send-private-message',
							'link_href'         => bp_get_send_private_message_link(),
							'link_title'        => __( 'Send a private message to this user.', 'buddypress' ),
							'link_text'         => __( '<i class="fa fa-envelope" aria-hidden="true"></i> Send Message', 'buddypress' ),
							'link_class'        => 'send-message btn btn-default btn-block btn-primary link-btn',
						)
					)
					?>

				<?php endif ?>
			</div><!--profile-action-wrapper-->
					<!--<p>Some descriptive tags about the student...</p>-->
		</div><!-- #item-header-avatar -->

		<div id="member-header-content" class="col-sm-16 col-xs-24">

			<?php do_action( 'bp_before_member_header_meta' ); ?>

			<div id="item-meta">

				<?php do_action( 'bp_profile_header_meta' ); ?>

			</div><!-- #item-meta -->

			<div class="profile-fields">
				<div class="info-panel panel panel-default no-margin no-margin-top">
					<div class="profile-fields table-div">

						<?php
						$exclude_fields = array(
							openlab_get_xprofile_field_id( 'Name' ),
							openlab_get_xprofile_field_id( 'Account Type' ),
							openlab_get_xprofile_field_id( 'First Name' ),
							openlab_get_xprofile_field_id( 'Last Name' ),
							openlab_get_xprofile_field_id( 'Major Program of Study' ),
							openlab_get_xprofile_field_id( 'Department' ),
							openlab_get_xprofile_field_id( 'Email address (Student)' ),
						);

						$exclude_fields = array_values( $exclude_fields );

						$has_profile_args = array(
							'exclude_fields' => $exclude_fields,
							'exclude_groups' => openlab_get_exclude_groups_for_account_type( $account_type ),
						);

						// This field is shown first for Student, Alumni; after Title for others.
						$show_dept_field_next = in_array( $account_type, array( 'student', 'alumni' ) );

						// Special case: faculty/staff doesn't have Title data.
						if ( ! $show_dept_field_next ) {
							$title_field_id = 'faculty' === $account_type ? 16 : 206;
							$user_title     = xprofile_get_field_data( $title_field_id, bp_displayed_user_id() );
							if ( ! $user_title ) {
								$show_dept_field_next = true;
							}
						}

						$user_units = openlab_get_user_academic_units( bp_displayed_user_id() );
						$department = openlab_generate_department_name( $user_units );
						$dept_label = in_array( $account_type, array( 'student', 'alumni' ), true ) ? 'Major Program of Study' : 'Department';

						?>

						<?php if ( bp_has_profile( $has_profile_args ) ) : ?>

							<?php
							while ( bp_profile_groups() ) :
								bp_the_profile_group();
								?>

								<?php if ( bp_profile_group_has_fields() ) : ?>

									<?php
									while ( bp_profile_fields() ) :
										bp_the_profile_field();
										?>

										<?php if ( bp_field_has_data() ) : ?>
											<?php
											if ( bp_get_the_profile_field_name() != 'Name' &&
													bp_get_the_profile_field_name() != 'Account Type' &&
													bp_get_the_profile_field_name() != 'First Name' &&
													bp_get_the_profile_field_name() != 'Last Name' ) :
												?>

												<div class="table-row row">
													<div class="bold col-sm-7 profile-field-label">
														<?php bp_the_profile_field_name(); ?>
													</div>

													<div class="col-sm-17 profile-field-value">
														<?php
														if ( bp_get_the_profile_field_name() == 'Academic interests' || bp_get_the_profile_field_name() == 'Bio' ) {
															echo bp_get_the_profile_field_value();
														} elseif ( 'Department' === bp_get_the_profile_field_name() || 'Major Program of Study' === bp_get_the_profile_field_name() ) {
															$user_units = openlab_get_user_academic_units( bp_displayed_user_id() );
															$department = openlab_generate_department_name( $user_units );

															echo esc_html( $department );
														} else {
															$field_value = str_replace( '<p>', '', bp_get_the_profile_field_value() );
															$field_value = str_replace( '</p>', '', $field_value );
															echo $field_value;
														}
														?>
													</div>
												</div>

												<?php $show_dept_field_next = 'Title' === bp_get_the_profile_field_name(); ?>

												<?php if ( $show_dept_field_next ) : ?>
													<?php if ( $department ) : ?>
														<div class="table-row row">
															<div class="bold col-sm-7 profile-field-label">
																<?php echo esc_html( $dept_label ); ?>
															</div>

															<div class="col-sm-17 profile-field-value">
																<?php echo esc_html( $department ); ?>
															</div>
														</div>
													<?php endif; ?>
												<?php endif; ?>

											<?php endif; ?>

										<?php endif; // bp_field_has_data() ?>

									<?php endwhile; // bp_profile_fields() ?>

								<?php endif; // bp_profile_group_has_fields() ?>

							<?php endwhile; // bp_profile_groups() ?>
						<?php elseif ( $department ) : ?>
							<?php /* Special case: User has no other profile fields but has a Department */ ?>
							<div class="table-row row">
								<div class="bold col-sm-7 profile-field-label">
									<?php echo esc_html( $dept_label ); ?>
								</div>

								<div class="col-sm-17 profile-field-value">
									<?php echo esc_html( $department ); ?>
								</div>
							</div>
						<?php endif; // bp_has_profile() ?>
					</div>
				</div>
			</div>

		</div><!-- #item-header-content -->

		<?php do_action( 'bp_after_member_header' ); ?>

	</div><!-- #item-header -->
	<?php
}

function openlab_custom_add_friend_button( $button ) {

	if ( $button['id'] == 'not_friends' ) {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user no-margin no-margin-left" aria-hidden="true"></i> Add Friend</span><i class="fa fa-plus-circle pull-right no-margin no-margin-right" aria-hidden="true"></i>';
		if ( bp_current_action() == 'my-friends' ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	} elseif ( $button['id'] == 'pending' ) {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user no-margin no-margin-left" aria-hidden="true"></i> Pending Friend</span><i class="fa fa-clock-o pull-right no-margin no-margin-right" aria-hidden="true"></i>';
		if ( bp_current_action() == 'my-friends' ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	} else {
		$button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> Friend</span><i class="fa fa-check-circle pull-right" aria-hidden="true"></i>';
		if ( bp_current_action() == 'my-friends' ) {
			$button['link_class'] = $button['link_class'] . ' btn btn-primary btn-xs link-btn clearfix';
		} else {
			$button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
		}
	}

	return $button;
}

add_filter( 'bp_get_add_friend_button', 'openlab_custom_add_friend_button' );

function openlab_member_header() {
	$this_user_id = bp_displayed_user_id();
	?>
	<?php $account_type = openlab_get_user_member_type_label( $this_user_id ); ?>

	<h1 class="entry-title profile-title clearfix">
		<span class="profile-name"><?php bp_displayed_user_fullname(); ?>&rsquo;s Profile</span>
		<span class="profile-type pull-right hidden-xs"><?php echo esc_html( $account_type ); ?></span>
		<button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</h1>
	<?php if ( bp_is_user_activity() ) : ?>
		<div class="clearfix hidden-xs">
			<div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php bp_last_activity( bp_displayed_user_id() ); ?></span></div>
		</div>
	<?php endif; ?>
	<div class="clearfix visible-xs">
		<span class="profile-type pull-left"><?php echo esc_html( $account_type ); ?></span>
		<div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php bp_last_activity( bp_displayed_user_id() ); ?></span></div>
	</div>
	<?php
}

add_action( 'bp_before_member_body', 'openlab_member_header' );

function openlab_messages_pagination() {
	global $messages_template;

	$page_args = array(
		'mpage' => '%#%',
	);

	if ( (int) $messages_template->total_thread_count && (int) $messages_template->pag_num ) {
		$pagination = paginate_links(
			array(
				'base'      => add_query_arg( $page_args, '' ),
				'format'    => '',
				'total'     => ceil( (int) $messages_template->total_thread_count / (int) $messages_template->pag_num ),
				'current'   => $messages_template->pag_page,
				'prev_text' => _x( '<i class="fa fa-angle-left" aria-hidden="true"></i>', 'Group pagination previous text', 'buddypress' ),
				'next_text' => _x( '<i class="fa fa-angle-right" aria-hidden="true"></i>', 'Group pagination next text', 'buddypress' ),
				'mid_size'  => 3,
				'type'      => 'list',
			)
		);
	}

	$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );

	return $pagination;
}

function openlab_get_custom_activity_action( $activity = null ) {
	global $activities_template;

	if ( null === $activity ) {
		$activity = $activities_template->activity;
	}

	//the things we do...
	$action_output     = '';
	$action_output_raw = $activity->action;
	$action_output_ary = explode( '<a', $action_output_raw );
	$count             = 0;
	foreach ( $action_output_ary as $action_redraw ) {
		if ( ! ctype_space( $action_redraw ) ) {
			$class          = ( $count == 0 ? 'activity-user' : 'activity-action' );
			$action_output .= '<a class="' . $class . '"' . $action_redraw;
			$count++;
		}
	}

	$time_since = apply_filters_ref_array( 'bp_activity_time_since', array( '<span class="time-since">' . bp_core_time_since( $activity->date_recorded ) . '</span>', &$activity ) );

	$title  = '<p class="item inline-links semibold hyphenate">' . $action_output . '</p>';
	$title .= '<p class="item timestamp"><span class="fa fa-undo" aria-hidden="true"></span> ' . $time_since . '</p>';

	return $title;
}

function openlab_trim_member_name( $name ) {
	global $post, $bp;

	$trim_switch = false;

	if ( $post->post_name == 'people' || $bp->current_action == 'members' ) {
		$trim_switch = true;
	}

	if ( $trim_switch ) {
		$process_name = explode( ' ', $name );
		$new_name     = '';
		foreach ( $process_name as $process ) {
			$new_name .= ' ' . openlab_shortened_text( $process, 12, false );
		}

		$name = $new_name;
	}

	return $name;
}

add_filter( 'bp_member_name', 'openlab_trim_member_name' );

function openlab_trim_message_subject( $subject ) {
	global $bp;

	if ( $bp->current_component == 'messages' && ( $bp->current_action == 'inbox' || $bp->current_action == 'sentbox' ) ) {
		$subject = openlab_shortened_text( $subject, 20, false );
	}

	return $subject;
}

add_filter( 'bp_get_message_thread_subject', 'openlab_trim_message_subject' );

/**
 * Ensure that @-mentions in message content are properly linked.
 */
add_filter( 'bp_get_the_thread_message_content', 'bp_activity_at_name_filter' );

/**
 * Pulls academic unit data from POST.
 *
 * Abstracted here for reuse in registration process.
 */
function openlab_get_academic_unit_data_from_post() {
	$to_save = array();
	foreach ( array( 'schools', 'offices', 'departments' ) as $unit_type ) {
		$to_save[ $unit_type ] = isset( $_POST[ $unit_type ] ) ? wp_unslash( $_POST[ $unit_type ] ) : array();
	}

	return $to_save;
}

/**
 * Pulls legacy academic unit data from POST.
 *
 * Abstracted here for reuse in registration process.
 */
function openlab_get_legacy_academic_unit_data_from_post() {
	$submitted_dept = null;
	if ( ! empty( $_POST['departments-dropdown'] ) ) {
		$submitted_dept = wp_unslash( $_POST['departments-dropdown'] );
	}

	// Identify the school.
	$all_depts   = openlab_get_entity_departments();
	$all_schools = openlab_get_school_list();
	$user_school = null;
	foreach ( $all_schools as $school => $_ ) {
		if ( isset( $all_depts[ $school ][ $submitted_dept ] ) ) {
			$user_school = $school;
		}
	}

	$to_save = array(
		'schools'     => array(),
		'offices'     => array(),
		'departments' => array( $submitted_dept ),
	);

	if ( $user_school ) {
		$to_save['schools'][] = $user_school;
	}

	return $to_save;
}

/**
 * Save the member S/O/D settings after save.
 *
 * @param int $user_id
 */
function openlab_user_academic_unit_save( $user_id ) {
	if ( empty( $_POST['openlab-academic-unit-selector-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'openlab_academic_unit_selector', 'openlab-academic-unit-selector-nonce' );

	$to_save = openlab_get_academic_unit_data_from_post();

	openlab_set_user_academic_units( $user_id, $to_save );
}
add_action( 'xprofile_updated_profile', 'openlab_user_academic_unit_save' );

/**
 * Save the legacy Major dropdown for users on profile save.
 *
 * @param int $user_id
 */
function openlab_user_academic_unit_save_legacy( $user_id ) {
	if ( empty( $_POST['openlab-academic-unit-selector-legacy-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'openlab_academic_unit_selector_legacy', 'openlab-academic-unit-selector-legacy-nonce' );

	$to_save = openlab_get_legacy_academic_unit_data_from_post();

	openlab_set_user_academic_units( $user_id, $to_save );
}
add_action( 'xprofile_updated_profile', 'openlab_user_academic_unit_save_legacy' );

/**
 * Gets academic units for a user.
 *
 * @param int $user_id
 */
function openlab_get_user_academic_units( $user_id ) {
	$values = array();
	$map    = array(
		'schools'     => 'openlab_school',
		'offices'     => 'openlab_office',
		'departments' => 'openlab_department',
	);

	foreach ( $map as $type_key => $meta_key ) {
		$units_of_type = get_user_meta( $user_id, $meta_key, false );
		if ( ! $units_of_type ) {
			$units_of_type = array();
		}
		$values[ $type_key ] = array_unique( $units_of_type );
	}

	return $values;
}

/**
 * Sets academic units for a user.
 *
 * @param int   $user_id
 * @param array $units
 */
function openlab_set_user_academic_units( $user_id, $units ) {
	$map = array(
		'schools'     => 'openlab_school',
		'offices'     => 'openlab_office',
		'departments' => 'openlab_department',
	);

	foreach ( $map as $data_key => $meta_key ) {
		$existing = get_user_meta( $user_id, $meta_key, false );
		$to_save  = $units[ $data_key ] ?: array();

		$to_delete = array_diff( $existing, $to_save );
		$to_add    = array_diff( $to_save, $existing );

		foreach ( $to_delete as $to_delete_value ) {
			delete_user_meta( $user_id, $meta_key, $to_delete_value );
		}

		foreach ( $to_add as $to_add_value ) {
			add_user_meta( $user_id, $meta_key, $to_add_value );
		}
	}
}

/**
 * Register the My Activity nav item with BuddyPress.
 */
function openlab_register_my_activity() {
	bp_core_new_nav_item(
		[
			'name'                    => 'My Activity',
			'slug'                    => 'my-activity',
			'component_id'            => 'my-activity',
			'show_for_displayed_user' => false,
			'position'                => 5,
			'screen_function'         => 'openlab_load_my_activity',
		]
	);
}
add_action( 'bp_setup_nav', 'openlab_register_my_activity' );

/**
 * Load the My Activity template.
 */
function openlab_load_my_activity() {
	if ( ! bp_is_my_profile() ) {
		return false;
	}

	bp_core_load_template( 'members/single/plugins' );
}


/**
 * Construct the array of arguments for the activities loop.
 *
 */
function openlab_activities_loop_args( $activity_type = '', $filter = '' ) {
    $args['count_total'] = true;

	if( ! empty( $filter ) ) {
		$args['action'] = $filter;
	}

    switch( $activity_type ) {
        case 'mine':
            $args += [
                'scope' => 'just-me',
            ];
            break;
        case 'favorites':
            $favorites = Query::get_results(
                [
                    'user_id' => bp_loggedin_user_id(),
                ]
            );

            $group_ids = '';

            if( $favorites ) {
                $group_ids = [];
                foreach( $favorites as $favorite ) {
                    array_push( $group_ids, $favorite->get_group_id() );
                }
            }

            $args += [
                'filter_query'	=> [
                    'relation'	=> 'AND',
                    'component'	=> [
                        'column'	=> 'component',
                        'value'		=> 'groups',
                    ],
                    'group_id'	=> [
                        'column'	=> 'item_id',
                        'value'		=> $group_ids,
                        'compare'	=> 'IN',
                    ],
                ],
            ];

            break;
        case 'mentions':
            $args += [
                'scope' => 'mentions'
            ];
            break;
        case 'starred':
            $args += [
                'scope' => 'favorites'
            ];
            break;
        default:
            $args += [
                'scope' => 'groups',
            ];
    }

    return $args;
}

/**
 * User's activity stream pagination.
 *
 */
function openlab_activities_pagination_links() {
    global $activities_template;

    $pagination = paginate_links(array(
        'base' => add_query_arg(array('acpage' => '%#%') ),
        'format' => '',
        'total' => ceil((int) $activities_template->total_activity_count / (int) $activities_template->pag_num),
        'current' => $activities_template->pag_page,
        'prev_text' => _x('<i class="fa fa-angle-left" aria-hidden="true"></i><span class="sr-only">Previous</span>', 'Group pagination previous text', 'buddypress'),
        'next_text' => _x('<i class="fa fa-angle-right" aria-hidden="true"></i><span class="sr-only">Next</span>', 'Group pagination next text', 'buddypress'),
        'mid_size' => 3,
        'type' => 'list',
        'before_page_number' => '<span class="sr-only">Page</span>',
    ));

    $pagination = str_replace('page-numbers', 'page-numbers pagination', $pagination);

    //for screen reader only text - current page
    $pagination = str_replace('current\'><span class="sr-only">Page', 'current\'><span class="sr-only">Current Page', $pagination);

    return $pagination;
}

/**
 * Get group id based on the activity id
 *
 */
function openlab_get_group_id_by_activity_id( $activity_id ) {
    if( ! empty( $activity_id ) ) {
        global $wpdb;

        // BuddyPress activity table
        $activity_table = $wpdb->prefix . 'bp_activity';

        // Get group id based on the activity id
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT item_id FROM $activity_table WHERE id = %s",
                $activity_id
            )
        );
    }
}

function openlab_get_group_id_by_event_id( $activity_id ) {
	if( ! empty( $activity_id ) ) {
		global $wpdb;

		// BuddyPress activity table
		$activity_table = $wpdb->prefix . 'bp_activity';

		// Get activity event id
		$event_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT secondary_item_id FROM $activity_table WHERE id = %s AND component = %s",
				$activity_id, 'events'
			)
		);

		if( ! empty( $event_id ) ) {
			$group_ids = (array) bpeo_get_event_groups( $event_id );

			if( isset( $group_ids[0] ) ) {
				return $group_ids[0];
			}
		}
	}
}

/**
 * Change the date format in the activity text displayed on
 * the "My Activity" page.
 *
 */
function openlab_change_activity_date_format() {
	$activity_date = bp_get_activity_date_recorded();
	return date('F n, Y \a\t g:i a', strtotime( $activity_date ) );
}

/**
 * Ajax fav/unfav activity item
 *
 */
add_action( 'wp_ajax_openlab_fav_activity', 'openlab_fav_activity' );
function openlab_fav_activity() {

	if( isset( $_POST['activity_id'] ) && isset( $_POST['user_action'] ) ) {
		$activity_id = intval( $_POST['activity_id'] );
		$action = $_POST['user_action'];
		$user_id = bp_loggedin_user_id();

		if( $action === 'fav' ) {
			if( bp_activity_add_user_favorite( $activity_id ) ) {
				echo json_encode( array(
					'success'	=> true,
					'activity'	=> $activity_id,
					'action'	=> $action,
					'user_id'	=> $user_id,
					'message'	=> __( 'Activity added to favorite list.', 'openlab' ),
				) );
				wp_die();
			}
		} else {
			if( bp_activity_remove_user_favorite( $activity_id ) ) {
				echo json_encode( array(
					'success'	=> true,
					'activity'	=> $activity_id,
					'action'	=> $action,
					'user_id'	=> $user_id,
					'message'	=> __( 'Activity removed from favorite list.', 'openlab' ),
				) );
				wp_die();
			}
		}

		echo json_encode( array(
			'success'	=> false,
			'message'	=> 'Something went wrong.',
		) );
		wp_die();
	}

	echo json_encode( array(
		'success'	=> false,
		'message'	=> 'Missing activity id and action.',
	) );
	wp_die();
}

/**
 * Change the date format of the member joined since text
 *
 */
function openlab_member_joined_since() {
	global $members_template;

	return printf(
		__( 'joined %s', 'buddypress' ),
		date( 'F j, Y', strtotime( $members_template->member->date_modified ) )
	);
}

/**
 * Modify the output of the activity items in the "My Activity" page
 *
 */
function openlab_get_user_activity_action( $activity = null ) {
	global $activities_template;

	if ( null === $activity ) {
		$activity = $activities_template->activity;
	}

	// Get activity body content
	$output = $activity->action;

	// Remove "in the group/forum" text from the activity on the group activity stream
	if( bp_is_group() ) {
		$group = bp_get_group( bp_get_current_group_id() );
		$group_link = bp_get_group_permalink( $group );
		$output = preg_replace( '/in the group <a href="[^"]+">' . preg_quote( bp_get_group_name() ) . '<\/a>/', '', $output );
		$output = str_replace( 'in the forum <a href="' . $group_link . 'forum/">' . bp_get_group_name() . '</a>', '', $output );
		$output = str_replace( 'in <a href="' . $group_link . '">' . bp_get_group_name() . '</a>', '', $output );
	} else {
		if( $activity->type == 'bbp_topic_create' || $activity->type == 'bbp_reply_create' ) {
			$output = str_replace( 'in the forum', 'in the group', $output );
		}
	}

	if( $activity->type == 'added_group_document' ) {
		$output = str_replace( 'uploaded the file', 'added the file', $output );
	}

	// Create DateTime from the activity date
	$activity_datetime = new DateTime( $activity->date_recorded );
	// Create TimeZone from the timezone selected in the WP Settings
	$wp_timezone = new DateTimeZone( wp_timezone_string() );
	// Set timezone to the activity DateTime
	$activity_datetime->setTimezone( $wp_timezone );

	// Modify activity date format, remove link and add "on" before the date
	$output .= ' on ' . $activity_datetime->format('F d, Y \a\t g:i a');
	$output = wpautop( $output );

	// Activity view button
	$view_button_label = openlab_get_activity_view_button_label( $activity->type );
	$view_button_link = openlab_get_activity_button_link( $activity );

	// Append activity view button
	if( $view_button_label ) {
		$output .= '<a href="' . $view_button_link . '" class="btn btn-xs btn-primary">' . $view_button_label . '</a>';
	}

	return $output;
}

/**
 * Create button label based on the activity type
 *
 */
function openlab_get_activity_view_button_label( $activity_type = '' ) {
	switch ( $activity_type ) {
		case 'edited_group_document' :
		case 'added_group_document' :
			return 'View File';

		case 'bp_doc_created' :
		case 'bp_doc_edited' :
		case 'bp_doc_comment' :
			return 'View Doc';

		case 'bpeo_create_event' :
			return 'View Event';

		case 'created_announcement' :
			return 'View Announcement';

		case 'created_announcement_reply' :
		case 'bbp_reply_create' :
			return 'View Reply';

		case 'bbp_topic_create' :
			return 'View Discussion Topic';

		case 'new_blog_post' :
			return 'View Post';

		case 'new_blog_comment' :
			return 'View Comment';

		case 'created_group' :
		case 'joined_group'	:
		case 'bpges_notice' :
			return 'View Group';

		case 'new_blog' :
			return 'View Site';

		case 'new_avatar' :
		case 'updated_profile' :
			return 'View Profile';
	}
}

/**
 * Get link for the button, based on the activity
 *
 */
function openlab_get_activity_button_link( $activity ) {
	global $activities_template;

	if( null === $activity ) {
		$activity = $activities_template->activity;
	}

	switch( $activity->type ) {
		case 'edited_group_document':
		case 'added_group_document':
			$document = new BP_Group_Documents( (string)$activity->secondary_item_id );
			return $document->get_url( false );
		case 'bp_doc_created':
		case 'bp_doc_edited':
			return $activity->primary_link;
		case 'created_group':
		case 'joined_group':
		case 'bpges_notice':
			$group = bp_get_group_by( 'id', $activity->item_id );
			return bp_get_group_permalink( $group );
		case 'default':
			return $activity->primary_link;
	}

	return $activity->primary_link;
}

/**
 * Check if the membership for the specified group is private
 * for the logged user.
 *
 */
function openlab_is_my_membership_private( $group_id ) {
	// Skip if group id is missing
	if ( empty( $group_id ) ) {
		return false;
	}

	global $wpdb;

	// Get private membership table
	$table_name = $wpdb->prefix . 'private_membership';

	// Get current user id
	$user_id = bp_loggedin_user_id();

	// Check if the membership is private based on user id and group id
	$query = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE `user_id` = %d AND `group_id` = %d", $user_id, $group_id ) );

	// If there is a record, return true. Otherwise, return false
	if ( $query ) {
		return true;
	}

	return false;
}

/**
 * Get user private membership group
 */
function openlab_get_user_private_membership( $user_id ) {
	// Skip if user id is missing
	if ( empty( $user_id ) ) {
		return [];
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'private_membership';
	$query = $wpdb->get_results( $wpdb->prepare( "SELECT `group_id` FROM $table_name WHERE `user_id` = %d", $user_id ), OBJECT_K );

	$private_groups = array();

	if ( $query ) {
		foreach ( $query as $item ) {
			$private_groups[] = (int) $item->group_id;
		}
	}

	return $private_groups;
}

/**
 * Update private membership table with the user's
 * group privacy data.
 */
add_action( 'wp_ajax_openlab_update_member_group_privacy', 'openlab_update_member_group_privacy' );
function openlab_update_member_group_privacy() {
	global $wpdb;

	// Get private membership table
	$table_name = $wpdb->prefix . 'private_membership';

	// Get current user id
	$user_id = bp_loggedin_user_id();

	// Check if group id is provded in the request
	if( ! isset( $_POST['group_id'] ) ) {
		echo json_encode( array(
			'success'	=> false,
			'message'	=> 'Group ID is missing.'
		) );
		die();
	}

	$group_id = $_POST['group_id'];
	$is_private = ( $_POST['is_private'] ) ? filter_var($_POST['is_private'], FILTER_VALIDATE_BOOLEAN) : false;

	if( $is_private ) {
		if( $wpdb->insert( $table_name, array( 'user_id' => $user_id, 'group_id' => $group_id ) ) ) {
			echo json_encode( array(
				'success'	=> true,
				'message'	=> 'User membership is set to private'
			) );
			die();
		}
	} else {
		if( $wpdb->delete( $table_name, array( 'user_id' => $user_id, 'group_id' => $group_id ) ) ) {
			echo json_encode( array(
				'success'	=> true,
				'message'	=> 'User membership is set to public.'
			) );
			die();
		}
	}

	echo json_encode( array(
		'success'	=> false,
		'message'	=> 'Something went wrong'
	) );
	die();
}
