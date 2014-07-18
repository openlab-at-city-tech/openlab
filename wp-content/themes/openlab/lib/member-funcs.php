<?php
/**
 * 	Member related functions
 *
 */

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
                <div class="person-block col-md-4">
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
