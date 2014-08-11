<?php

/**
 * 	Member related functions
 *
 */
function openlab_is_admin_truly_member($group = false) {
    global $groups_template;

    if (empty($group))
        $group = & $groups_template->group;

    return apply_filters('bp_group_is_member', !empty($group->is_member));
}

/**
 * 	People archive page
 *
 */
function openlab_list_members($view) {
    global $wpdb, $bp, $members_template, $wp_query;

    // Set up variables
    // There are two ways to specify user type: through the page name, or a URL param
    $user_type = $sequence_type = $search_terms = $user_school = $user_dept = '';
    if (!empty($_GET['usertype']) && $_GET['usertype'] != 'user_type_all') {
        $user_type = $_GET['usertype'];
        $user_type = ucwords($user_type);
    } else {
        $post_obj = $wp_query->get_queried_object();
        $post_title = !empty($post_obj->post_title) ? ucwords($post_obj->post_title) : '';

        if (in_array($post_title, array('Staff', 'Faculty', 'Students'))) {
            if ('Students' == $post_title) {
                $user_type = 'Student';
            } else {
                $user_type = $post_title;
            }
        }
    }

    if (!empty($_GET['group_sequence'])) {
        $sequence_type = $_GET['group_sequence'];
    }

    if (!empty($_POST['people_search'])) {
        $search_terms = $_POST['people_search'];
    } else if (!empty($_GET['search'])) {
        $search_terms = $_GET['search'];
    } else if (!empty($_POST['group_search'])) {
        $search_terms = $_POST['group_search'];
    }

    if (!empty($_GET['school'])) {
        $user_school = urldecode($_GET['school']);

        // Sanitize
        $schools = openlab_get_school_list();
        if (!isset($schools[$user_school])) {
            $user_school = '';
        }
    }

    if (!empty($_GET['department'])) {
        $user_department = urldecode($_GET['department']);
    }

    // Set up the bp_has_members() arguments
    // Note that we're not taking user_type into account. We'll do that with a query filter
    $args = array('per_page' => 48);

    if ($sequence_type) {
        $args['type'] = $sequence_type;
    }

    // Set up $include
    // $include_noop is a flag that gets triggered when one of the search
    // conditions returns no items. If that happens, don't bother doing
    // the other queries, and just return a null result
    $include_arrays = array();
    $include_noop = false;

    if ($search_terms && !$include_noop) {
        // The first and last name fields are private, so they should
        // not show up in search results
        $first_name_field_id = xprofile_get_field_id_from_name('First Name');
        $last_name_field_id = xprofile_get_field_id_from_name('Last Name');

        // Split the search terms into separate words
        $search_terms_a = explode(' ', $search_terms);

        $search_query = "SELECT user_id
			 FROM {$bp->profile->table_name_data}
			 WHERE field_id NOT IN ({$first_name_field_id}, {$last_name_field_id})";

        if (!empty($search_terms_a)) {
            $match_clauses = array();
            foreach ($search_terms_a as $search_term) {
                $match_clauses[] = "value LIKE '%" . esc_sql(like_escape($search_term)) . "%'";
            }
            $search_query .= " AND ( " . implode(' AND ', $match_clauses) . " )";
        }

        $search_terms_matches = $wpdb->get_col($search_query);

        if (empty($search_terms_matches)) {
            $include_noop = true;
        } else {
            $include_arrays[] = $search_terms_matches;
        }
    }

    if ($user_type && !$include_noop) {
        $user_type_matches = $wpdb->get_col($wpdb->prepare(
                        "SELECT user_id
			 FROM {$bp->profile->table_name_data}
			 WHERE field_id = 7
			       AND
			       value = %s", $user_type
        ));

        if (empty($user_type_matches)) {
            $user_type_matches = array(0);
        }

        if (empty($user_type_matches)) {
            $include_noop = true;
        } else {
            $include_arrays[] = $user_type_matches;
        }
    }

    if ($user_school && !$include_noop) {
        $department_field_id = xprofile_get_field_id_from_name('Department');
        $major_field_id = xprofile_get_field_id_from_name('Major Program of Study');

        $department_list = openlab_get_department_list($user_school);

        // just in case
        $department_list_sql = '';
        foreach ($department_list as &$department_list_item) {
            $department_list_item = $wpdb->prepare('%s', $department_list_item);
        }
        $department_list_sql = implode(',', $department_list);

        $user_school_matches = $wpdb->get_col($wpdb->prepare(
                        "SELECT user_id
			 FROM {$bp->profile->table_name_data}
			 WHERE field_id IN (%d, %d)
			       AND
			       value IN (" . $department_list_sql . ")", $department_field_id, $major_field_id
        ));

        if (empty($user_school_matches)) {
            $include_noop = true;
        } else {
            $include_arrays[] = $user_school_matches;
        }
    }

    if ($user_department && !$include_noop && 'dept_all' !== $user_department) {
        $department_field_id = xprofile_get_field_id_from_name('Department');
        $major_field_id = xprofile_get_field_id_from_name('Major Program of Study');

        // Department comes through $_GET in the hyphenated form, but
        // is stored in the database in the fulltext form. So we have
        // to pull up a list of all departments and attempt a
        // translation.
        //
		// Could this be any more of a mess?
        $regex = esc_sql(str_replace('-', '[ \-]', $user_department));
        $user_departments = $wpdb->get_col($wpdb->prepare(
                        "SELECT name
			 FROM {$bp->profile->table_name_fields}
			 WHERE parent_id IN (%d, %d)
			 AND name REGEXP '{$regex}'", $department_field_id, $major_field_id
        ));

        $user_departments_sql = '';
        foreach ($user_departments as &$ud) {
            $ud = $wpdb->prepare('%s', $ud);
        }
        $user_departments_sql = implode(',', $user_departments);

        $user_department_matches = $wpdb->get_col($wpdb->prepare(
                        "SELECT user_id
			 FROM {$bp->profile->table_name_data}
			 WHERE field_id IN (%d, %d)
			       AND
			       value IN ({$user_departments_sql})", $department_field_id, $major_field_id
        ));

        if (empty($user_department_matches)) {
            $include_noop = true;
        } else {
            $include_arrays[] = $user_department_matches;
        }
    }

    // Parse the results into a single 'include' parameter
    if ($include_noop) {
        $include = array(0);
    } else if (!empty($include_arrays)) {
        foreach ($include_arrays as $iak => $ia) {
            // On the first go-round, seed the temp variable with
            // the first set of includes
            if (!isset($include)) {
                $include = $ia;

                // On subsequent iterations, do array_intersect() to
                // trim down the included users
            } else {
                $include = array_intersect($include, $ia);
            }
        }

        if (empty($include)) {
            $include = array(0);
        }
    }

    if (!empty($include)) {
        $args['include'] = array_unique($include);
    }

    $avatar_args = array(
        'type' => 'full',
        'width' => 72,
        'height' => 72,
        'class' => 'avatar',
        'id' => false,
        'alt' => __('Member avatar', 'buddypress')
    );
    ?>
    <div class="current-group-filters current-portfolio-filters">
        <?php openlab_current_directory_filters(); ?>
    </div>

    <?php if (bp_has_members($args)) : ?>
        <div class="group-count"><?php cuny_members_pagination_count('members'); ?></div>
        <div class="clearfloat"></div>
        <div class="avatar-block">
            <?php
            while (bp_members()) : bp_the_member();
                //the following checks the current $id agains the passed list from the query
                $member_id = $members_template->member->id;


                $registered = bp_format_time(strtotime($members_template->member->user_registered), true)
                ?>
                <div class="person-block col-md-8">
                    <div class="item-avatar">
                        <a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
                    </div>
                    <div class="cuny-member-info">
                        <a class="member-name" href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
                        <span class="member-since-line">Member since <?php echo $registered; ?></span>
                        <?php if (bp_get_member_latest_update()) : ?>
                            <span class="update"><?php bp_member_latest_update('length=10') ?></span>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
        <div id="pag-top" class="pagination">

            <div class="pag-count" id="member-dir-count-top">
                <?php bp_members_pagination_count() ?>
            </div>

            <div class="pagination-links" id="member-dir-pag-top">
                <?php bp_members_pagination_links() ?>
            </div>

        </div>

        <?php
    else:
        if ($user_type == "Student") {
            $user_type = "students";
        }

        if (empty($user_type)) {
            $user_type = 'people';
        }
        ?>

        <div class="widget-error">
            <p><?php _e('There are no ' . strtolower($user_type) . ' to display.', 'buddypress') ?></p>
        </div>

    <?php
    endif;
}

//a variation on bp_members_pagination_count() to match design
function cuny_members_pagination_count($member_name) {
    global $bp, $members_template;

    if (empty($members_template->type))
        $members_template->type = '';

    $start_num = intval(( $members_template->pag_page - 1 ) * $members_template->pag_num) + 1;
    $from_num = bp_core_number_format($start_num);
    $to_num = bp_core_number_format(( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
    $total = bp_core_number_format($members_template->total_member_count);

    $pag = sprintf(__('%1$s to %2$s ( of %3$s members )', 'buddypress'), $from_num, $to_num, $total);
    echo $pag;
}

function openlab_displayed_user_account_type() {
    echo openlab_get_displayed_user_account_type();
}

function openlab_get_displayed_user_account_type() {
    return xprofile_get_field_data('Account Type', bp_displayed_user_id());
}

/**
 * Prints a status message regarding the group visibility.
 *
 * @global BP_Groups_Template $groups_template Groups template object
 * @param object $group Group to get status message for. Optional; defaults to current group.
 */
function openlab_group_status_message($group = null) {
    global $groups_template;

    if (!$group)
        $group = & $groups_template->group;

    $group_label = openlab_get_group_type_label('group_id=' . $group->id . '&case=upper');

    $site_id = openlab_get_site_id_by_group_id($group->id);
    $site_url = openlab_get_group_site_url($group->id);

    if ($site_url) {
        // If we have a site URL but no ID, it's an external site, and is public
        if (!$site_id) {
            $site_status = 1;
        } else {
            $site_status = get_blog_option($site_id, 'blog_public');
        }
    }

    $site_status = (float) $site_status;

    $message = '';

    switch ($site_status) {
        // Public
        case 1 :
        case 0 :
            if ('public' === $group->status) {
                $message = 'This ' . $group_label . ' is OPEN.';
            } else if (!$site_url) {
                // Special case: $site_status will be 0 when the
                // group does not have an associated site. When
                // this is the case, and the group is not
                // public, don't mention anything about the Site.
                $message = 'This ' . $group_label . ' is PRIVATE.';
            } else {
                $message = 'This ' . $group_label . ' Profile is PRIVATE, but the ' . $group_label . ' Site is OPEN to all visitors.';
            }

            break;

        case -1 :
            if ('public' === $group->status) {
                $message = 'This ' . $group_label . ' Profile is OPEN, but only logged-in OpenLab members may view the ' . $group_label . ' Site.';
            } else {
                $message = 'This ' . $group_label . ' Profile is PRIVATE, but all logged-in OpenLab members may view the ' . $group_label . ' Site.';
            }

            break;

        case -2 :
        case -3 :
            if ('public' === $group->status) {
                $message = 'This ' . $group_label . ' Profile is OPEN, but the ' . $group_label . ' Site is PRIVATE.';
            } else {
                $message = 'This ' . $group_label . ' is PRIVATE. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.';
            }

            break;
    }

    echo $message;
}

function openlab_get_groups_of_user($args = array()) {
    global $bp, $wpdb;

    $retval = array(
        'group_ids' => array(),
        'group_ids_sql' => '',
        'activity' => array()
    );

    $defaults = array(
        'user_id' => bp_loggedin_user_id(),
        'show_hidden' => true,
        'group_type' => 'club',
        'get_activity' => true
    );
    $r = wp_parse_args($args, $defaults);

    $select = $where = '';

    $select = "SELECT a.group_id FROM {$bp->groups->table_name_members} a";
    $where = $wpdb->prepare("WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d", $r['user_id']);

    if (!$r['show_hidden']) {
        $select .= " JOIN {$bp->groups->table_name} c ON ( c.id = a.group_id ) ";
        $where .= " AND c.status != 'hidden' ";
    }

    if ('all' != $r['group_type']) {
        // Sanitize
        $group_type = in_array(strtolower($r['group_type']), array('club', 'project', 'course')) ? strtolower($r['group_type']) : 'club';

        $select .= " JOIN {$bp->groups->table_name_groupmeta} d ON ( a.group_id = d.group_id ) ";
        $where .= $wpdb->prepare(" AND d.meta_key = 'wds_group_type' AND d.meta_value = %s ", $group_type);
    }

    $sql = $select . ' ' . $where;

    $group_ids = $wpdb->get_col($sql);

    $retval['group_ids'] = $group_ids;

    // Now that we have group ids, get the associated activity items and format the
    // whole shebang in the proper way
    if (!empty($group_ids)) {
        $retval['group_ids_sql'] = implode(',', $group_ids);

        if ($r['get_activity']) {
            // bp_has_activities() doesn't allow arrays of item_ids, so query manually
            $activities = $wpdb->get_results("SELECT id,item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ( {$retval['group_ids_sql']} ) ORDER BY id DESC");

            // Now walk down the list and try to match with a group. Once one is found, remove
            // that group from the stack
            $group_activity_items = array();
            foreach ((array) $activities as $act) {
                if (!empty($act->content) && in_array($act->item_id, $group_ids) && !isset($group_activity_items[$act->item_id])) {
                    $group_activity_items[$act->item_id] = $act->content;
                    $key = array_search($act->item_id, $group_ids);
                    unset($group_ids[$key]);
                }
            }

            $retval['activity'] = $group_activity_items;
        }
    }

    return $retval;
}

function cuny_student_profile() {
    global $site_members_template, $user_ID, $bp;

    $this_user_id = isset($site_members_template->member->id) ? $site_members_template->member->id : bp_displayed_user_id();
    do_action('bp_before_member_home_content');
    ?>
    <?php $account_type = xprofile_get_field_data('Account Type', $this_user_id); ?>

    <h2 class="entry-title profile-title clearfix">
        <?php bp_displayed_user_fullname() ?>&rsquo;s Profile
        <span class="profile-type pull-right"><?php echo $account_type ?></span>
    </h2>

    <?php if (bp_is_user_activity() || 'public' == bp_current_action()) { ?>
        <?php cuny_member_profile_header(); ?>
    <?php } ?>


    <div id="member-item-body">

        <?php if (bp_is_user_blogs()) { ?>
            <?php do_action('bp_before_member_blogs_content') ?>

            <div class="blogs myblogs">
                <?php bp_get_template_part('blogs/blogs-loop.php'); ?>
            </div><!-- .blogs.myblogs -->

            <?php do_action('bp_after_member_blogs_content') ?>

            <?php do_action('bp_before_member_body') ?>

        <?php } elseif ('view' == bp_current_action()) { ?>
            <?php bp_get_template_part('members/single/messages/single.php'); ?>
        <?php } elseif (bp_is_user_messages()) { ?>
            <?php bp_get_template_part('members/single/messages.php'); ?>
        <?php } elseif (bp_is_user_groups()) { ?>
            <?php bp_get_template_part('members/single/groups.php'); ?>
        <?php } elseif ('edit' == bp_current_action()) { ?>
            <?php bp_get_template_part('members/single/profile/edit.php'); ?>
        <?php } elseif ('change-avatar' == bp_current_action()) { ?>
            <?php bp_get_template_part('members/single/profile/change-avatar.php'); ?>
        <?php } elseif ('requests' == bp_current_action()) { ?>
            <?php bp_get_template_part('members/single/friends/requests.php'); ?>
        <?php } elseif (bp_is_user_friends()) { ?>

            <?php do_action('bp_before_member_friends_content') ?>

            <?php if (is_user_logged_in() && openlab_is_my_profile()): ?>
                <div class="submenu"><?php echo openlab_my_friends_submenu(); ?></div>
            <?php endif; ?>

            <div class="members friends">

                <?php bp_get_template_part('members/members-loop.php'); ?>
            </div><!-- .members.friends -->

            <?php do_action('bp_after_member_friends_content') ?>

        <?php } else { ?>

            <?php echo cuny_profile_activty_block('course', 'My Courses', '', 25); ?>
            <?php echo cuny_profile_activty_block('project', 'My Projects', ' last', 25); ?>
            <?php echo cuny_profile_activty_block('club', 'My Clubs', ' last', 25); ?>

            <div class="clearfloat"></div>
            <script type='text/javascript'>(function($) {
                    $('.activity-list').css('visibility', 'hidden');
                })(jQuery);</script>
            <?php
            if (bp_is_active('friends')) :
                if (!$friend_ids = wp_cache_get('friends_friend_ids_' . $bp->displayed_user->id, 'bp')) {
                    $friend_ids = BP_Friends_Friendship::get_random_friends($bp->displayed_user->id, 20);
                    wp_cache_set('friends_friend_ids_' . $bp->displayed_user->id, $friend_ids, 'bp');
                }
                ?>

                <div class="info-group">
                    <div><h4><?php bp_word_or_name(__("My Friends", 'buddypress'), __("%s's Friends", 'buddypress')) ?></h4></div>

                    <?php if ($friend_ids) { ?>

                        <ul id="member-list">

                            <?php for ($i = 0; $i < count($friend_ids); $i++) { ?>

                                <li>
                                    <a href="<?php echo bp_core_get_user_domain($friend_ids[$i]) ?>"><?php echo bp_core_fetch_avatar(array('item_id' => $friend_ids[$i], 'type' => 'thumb')) ?></a>
                                </li>

                            <?php } ?>

                        </ul>
                        <span><a href="<?php echo $bp->displayed_user->domain . $bp->friends->slug ?>"><?php _e('See All Friends', 'buddypress') ?></a></span>
                    <?php } else { ?>

                        <div id="message" class="info">
                            <p><?php bp_word_or_name(__("You haven't added any friend connections yet.", 'buddypress'), __("%s hasn't created any friend connections yet.", 'buddypress')) ?></p>
                        </div>

                    <?php } ?>
                <?php endif; /* bp_is_active( 'friends' ) */ ?>
                <div class="clear"></div>
            </div>
        <?php } ?>
        <?php do_action('bp_after_member_body') ?>

    </div><!-- #item-body -->

    <?php do_action('bp_after_memeber_home_content') ?>

    <?php
}

function cuny_profile_activty_block($type, $title, $last, $desc_length = 135) {
    global $wpdb, $bp;

    //echo $type."<hr>";
    $ids = "9999999";
    $groups_found = Array();
    if ($type != "blog") {
        $get_group_args = array(
            'user_id' => bp_displayed_user_id(),
            'show_hidden' => false,
            'active_status' => 'all',
            'group_type' => $type,
            'get_activity' => false,
        );
        $groups = openlab_get_groups_of_user($get_group_args);

        //echo $ids;
        if (!empty($groups['group_ids_sql']) && bp_has_groups('include=' . $groups['group_ids_sql'] . '&per_page=20')) :
//	  if ( bp_has_groups( 'include='.$ids.'&per_page=3&max=3' ) ) :
            ?>
            <div id="<?php echo $type ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last ?> col-sm-8">
                <!-- See all link in column headers -->
                <h4><?php echo $title ?>
                    <?php if ($bp->is_item_admin || $bp->is_item_mod): ?>
                        <a class="group-see-all top" href="<?php echo bp_get_root_domain() ?>/my-<?php echo $type; ?>s">See All</a>
                    <?php else: ?>
                        <a class="group-see-all top" href="<?php echo $bp->displayed_user->domain ?>groups/?type=<?php echo $type; ?>">See All</a>
                    <?php endif; ?>
                </h4>
                <?php $x = 0; ?>
                <?php while (bp_groups()) : bp_the_group(); ?>

                    <div class="row row-<?php echo $x + 1; ?>">
                        <div class="activity-avatar">
                            <a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array('width' => 76, 'height' => 76)) ?></a>
                        </div>

                        <div class="activity-content">

                            <div class="activity-header">
                                <a href="<?php bp_group_permalink() ?>"><?php echo openlab_shortened_text(bp_get_group_name(), 35); ?></a>
                            </div>

                            <div class="activity-inner">
                                <?php $activity = bp_get_group_description() ?>
                                <?php /* shorten the description if it's getting long */ ?>
                                <?php $activity = strip_tags(openlab_shortened_text($activity, 75)); ?>

                                <?php echo $activity . ' <a class="read-more" href="' . bp_get_group_permalink() . '">See More</a>'; ?>
                            </div>

                        </div>

                        <div class="clearfloat"></div>

                    </div>

                    <?php /* Increment */ ?>
                    <?php $x += 1; ?>

                    <?php /* Only show 5 items max */ ?>
                    <?php
                    if ($x == 5) {
                        break;
                    }
                    ?>

                <?php endwhile; ?>

            </div>
        <?php else : ?>
            <div id="<?php echo $type ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last ?>">
                <h4><?php echo $title ?></h4>

                <div>
                    <?php
                    if ($type != "course") {
                        if ($bp->loggedin_user->id == $bp->displayed_user->id) {
                            ?>
                            You aren't participating in any <?php echo $type; ?>s on the OpenLab yet. Why not <a href="<?php echo site_url(); ?>/groups/create/step/group-details/?type=<?php echo $type; ?>&new=true">create a <?php echo $type; ?></a>?
                            <?php
                        } else {
                            echo $bp->displayed_user->fullname;
                            ?>
                            hasn't created or joined any <?php echo $type ?>s yet.
                            <?php
                        }
                    } else {
                        if ($bp->loggedin_user->id == $bp->displayed_user->id) {
                            ?>
                            You haven't created any courses yet.
                            <?php
                        } else {
                            echo $bp->displayed_user->fullname;
                            ?>
                            hasn't joined any <?php echo $type ?>s yet.
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <?php
    } else {
        // BLOGS
        global $bp, $wpdb;

        // bp_has_blogs() doesn't let us narrow our options enough
        // Get all group blog ids, so we can exclude them
        $gblogs = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'"));

        $gblogs = implode(',', $gblogs);

        $blogs_query = $wpdb->prepare("
			SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name
			FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u
			WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id AND b.user_id = {$bp->displayed_user->id} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 AND wb.public = 1 AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' AND b.blog_id NOT IN ({$gblogs}) LIMIT 3");

        $myblogs = $wpdb->get_results($blogs_query);
        ?>



        <div id="<?php echo $type ?>-activity-stream" class="<?php echo $type; ?>-list activity-list item-list<?php echo $last ?>">
            <h4><?php echo $title ?></h4>

            <?php if (!empty($myblogs)) : ?>
                <?php foreach ((array) $myblogs as $myblog) : ?>
                    <li>
                        <a href="http://<?php echo trailingslashit($myblog->domain . $myblog->path) ?>"><?php echo $myblog->name ?></a>
                    </li>
                <?php endforeach ?>
            <?php else : ?>
                <?php if (bp_is_my_profile()) : ?>
                    You haven't created or joined any sites yet.
                <?php else : ?>
                    <?php echo $bp->displayed_user->fullname ?> hasn't created or joined any sites yet.
                <?php endif ?>

            <?php endif ?>
        </div>
        <?php
    }
}

function cuny_member_profile_header() {
    global $site_members_template, $user_ID, $bp;

    $this_user_id = isset($site_members_template->member->id) ? $site_members_template->member->id : bp_displayed_user_id();

    $account_type = xprofile_get_field_data('Account Type', $this_user_id);

//
//     whenever profile is viewed, update user meta for first name and last name so this shows up
//     in the back end on users display so teachers see the students full name
//
    $name_member_id = bp_displayed_user_id();
    $first_name = xprofile_get_field_data('First Name', $name_member_id);
    $last_name = xprofile_get_field_data('Last Name', $name_member_id);
    $update_user_first = update_user_meta($name_member_id, 'first_name', $first_name);
    $update_user_last = update_user_meta($name_member_id, 'last_name', $last_name);
    ?>

    <?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
    if (!$dud = bp_displayed_user_domain()) {
        $dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
    }
    ?>

    <div id="member-header">
        <?php do_action('bp_before_member_header') ?>

        <div id="member-header-avatar" class="alignleft">
            <div id="avatar-wrapper">
                <a href="<?php bp_user_link() ?>">
                    <?php bp_displayed_user_avatar('type=full&width=225') ?>
                </a>
            </div><!--memeber-header-avatar-->
            <div id="profile-action-wrapper">
                <?php if (is_user_logged_in() && openlab_is_my_profile()) : ?>
                    <div id="action-edit-profile"><a href="<?php echo $dud . 'profile/edit/'; ?>">Edit Profile</a></div>
                    <div id="action-edit-avatar"><a href="<?php echo $dud . 'profile/change-avatar/'; ?>">Change Avatar</a></div>
                <?php elseif (is_user_logged_in() && !openlab_is_my_profile()) : ?>
                    <?php bp_add_friend_button(openlab_fallback_user(), bp_loggedin_user_id()) ?>

                    <?php
                    echo bp_get_button(array(
                        'id' => 'private_message',
                        'component' => 'messages',
                        'must_be_logged_in' => true,
                        'block_self' => true,
                        'wrapper_id' => 'send-private-message',
                        'link_href' => bp_get_send_private_message_link(),
                        'link_title' => __('Send a private message to this user.', 'buddypress'),
                        'link_text' => __('Send Message', 'buddypress'),
                        'link_class' => 'send-message',
                    ))
                    ?>

                <?php endif ?>
            </div><!--profile-action-wrapper-->
                    <!--<p>Some descriptive tags about the student...</p>-->
        </div><!-- #item-header-avatar -->

        <div id="member-header-content" class="alignleft">

            <h2 class="member-name-title fn"><?php bp_displayed_user_fullname() ?><?php //echo " ".$last_name;             ?></h2>
            <span class="activity"><?php bp_last_activity(bp_displayed_user_id()) ?></span>



            <?php do_action('bp_before_member_header_meta') ?>

            <div id="item-meta">
                <?php if (function_exists('bp_activity_latest_update')) : ?>
                    <div id="latest-update">
                        <?php bp_activity_latest_update(bp_displayed_user_id()) ?>
                    </div>
                <?php endif; ?>
                <!--
                <?php if (bp_is_active('groups') && bp_has_groups()) : ?>
                                                                                                    <div id="areas-of-activty" class="item-list">
                                                                                                    <strong>All groups:</strong>
                    <?php while (bp_groups()) : bp_the_group(); ?>
                                                                                                                        
                                                                                                                                                                <a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a>,
                                                                                                                        
                    <?php endwhile; ?>
                                                                                                    </div>
                <?php endif; ?>
                -->


                <?php do_action('bp_profile_header_meta') ?>

            </div><!-- #item-meta -->

            <div class="profile-fields">
                <?php $exclude_groups = openlab_get_exclude_groups_for_account_type($account_type) ?>
                <?php if (bp_has_profile(array('exclude_groups' => $exclude_groups))) : ?>

                    <table class="profile-fields">

                        <?php while (bp_profile_groups()) : bp_the_profile_group(); ?>

                            <?php if (bp_profile_group_has_fields()) : ?>

                                <?php while (bp_profile_fields()) : bp_the_profile_field(); ?>

                                    <?php if (bp_field_has_data()) : ?>
                                        <?php
                                        if (bp_get_the_profile_field_name() != "Name" &&
                                                bp_get_the_profile_field_name() != "Account Type" &&
                                                bp_get_the_profile_field_name() != "First Name" &&
                                                bp_get_the_profile_field_name() != "Last Name") :
                                            ?>

                                            <tr>
                                                <td class="label" nowrap="nowrap">
                                                    <?php bp_the_profile_field_name() ?>
                                                </td>

                                                <td>
                                                    <?php bp_the_profile_field_value(); ?>
                                                </td>
                                            </tr>

                                        <?php endif; ?>

                                    <?php endif; // bp_field_has_data()   ?>

                                <?php endwhile; // bp_profile_fields()   ?>
                            <?php endif; // bp_profile_group_has_fields()   ?>

                        <?php endwhile; // bp_profile_groups()    ?>
                    </table>

                <?php endif; // bp_has_profile()    ?>
            </div>

        </div><!-- #item-header-content -->

        <?php do_action('bp_after_member_header') ?>

    </div><!-- #item-header -->
    <?php
}

/**
 * Save the Account Type setting on the Account Settings screen.
 */
function openlab_save_account_type_on_settings() {
    if (isset($_POST['account_type'])) {
        $types = array('Student', 'Alumni');
        $account_type = in_array($_POST['account_type'], $types) ? $_POST['account_type'] : 'Student';
        $user_id = bp_displayed_user_id();
        $current_type = openlab_get_displayed_user_account_type();

        // Only students and alums can do this
        if (in_array($current_type, $types)) {
            xprofile_set_field_data('Account Type', bp_displayed_user_id(), $account_type);
        }
    }
}

add_action('bp_core_general_settings_after_save', 'openlab_save_account_type_on_settings');
