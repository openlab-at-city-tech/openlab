<?php
/**
 * Library of group-related functions
 *
 */

/**
 * Custom template loader for my-{grouptype}
 */
function openlab_mygroups_template_loader( $template ) {
	if ( is_page() ) {
		switch ( get_query_var( 'pagename' ) ) {
			case 'my-courses' :
			case 'my-clubs' :
			case 'my-projects' :
				get_template_part('buddypress/groups/index');
				break;
		}
	}

	return $template;
}
add_filter( 'template_include', 'openlab_mygroups_template_loader' );

/**
 * This function consolidates the group privacy settings in one spot for easier updating
 *
 */
function openlab_group_privacy_settings($group_type) {
    global $bp;

    $group_type_name = $group_type;
    $group_type_name_uc = ucfirst($group_type);

    if ('portfolio' === $group_type) {
        $group_type_name = openlab_get_portfolio_label(array(
            'group_id' => bp_get_current_group_id(),
        ));
        $group_type_name_uc = openlab_get_portfolio_label(array(
            'group_id' => bp_get_current_group_id(),
            'case' => 'upper',
        ));
    }

    // If this is a cloned group/site, fetch the clone source's details
    $clone_source_group_status = $clone_source_blog_status = '';
    if (bp_is_group_create()) {
        $new_group_id = bp_get_new_group_id();
        if ('course' === $group_type) {
            $clone_source_group_id = groups_get_groupmeta($new_group_id, 'clone_source_group_id');
            $clone_source_site_id = groups_get_groupmeta($new_group_id, 'clone_source_blog_id');

            $clone_source_group = groups_get_group(array('group_id' => $clone_source_group_id));
            $clone_source_group_status = $clone_source_group->status;

            $clone_source_blog_status = get_blog_option($clone_source_site_id, 'blog_public');
        }
    }
    ?>
    <h4 class="privacy-title"><?php _e('Privacy Settings', 'buddypress'); ?></h4>
    <p class="privacy-settings-tag-a">Set privacy options for your <?php echo $group_type_name_uc ?></p>
    <?php if ($bp->current_action == 'admin' || $bp->current_action == 'create' || openlab_is_portfolio()): ?>
        <h5><?php echo $group_type_name_uc ?> Profile</h5>
    <?php endif; ?>

    <?php if ($bp->current_action == 'create'): ?>
        <p id="privacy-settings-tag-b"><?php _e('To change these settings later, use the ' . $group_type_name . ' Profile Settings page.', 'buddypress'); ?></p>
    <?php else: ?>
        <p class="privacy-settings-tag-c"><?php _e('These settings affect how others view your ' . $group_type_name_uc . ' Profile.') ?></p>
    <?php endif; ?>

    <div class="radio group-profile">

        <?php
        $new_group_status = bp_get_new_group_status();
        if (!$new_group_status) {
            $new_group_status = !empty($clone_source_group_status) ? $clone_source_group_status : 'public';
        }
        ?>

        <label>
            <input type="radio" name="group-status" value="public" <?php checked('public', $new_group_status) ?> />
            <strong>This is a public <?php echo $group_type_name_uc ?></strong>
            <ul>
                <li>This <?php echo $group_type_name_uc ?> Profile and related content and activity will be visible to the public.</li>
                <li><?php _e('This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . 's directory, search results, and may be displayed on the OpenLab home page.', 'buddypress') ?></li>
                <li><?php _e('Any OpenLab member may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
            </ul>
        </label>

        <label>
            <input type="radio" name="group-status" value="private" <?php checked('private', $new_group_status) ?> />
            <strong><?php _e('This is a private ' . $group_type_name_uc, 'buddypress') ?></strong>
            <ul>
                <li><?php _e('This ' . $group_type_name_uc . ' Profile and related content and activity will only be visible to members of the group.', 'buddypress') ?></li>
                <li><?php _e('This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . ' directory, search results, and may be displayed on the OpenLab home page.', 'buddypress') ?></li>
                <li><?php _e('Only OpenLab members who request membership and are accepted may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
            </ul>
        </label>

        <label>
            <input type="radio" name="group-status" value="hidden" <?php checked('hidden', $new_group_status) ?> />
            <strong><?php _e('This is a hidden ' . $group_type_name_uc, 'buddypress') ?></strong>
            <ul>
                <li><?php _e('This ' . $group_type_name_uc . ' Profile, related content and activity will only be visible only to members of the ' . $group_type_name_uc . '.', 'buddypress') ?></li>
                <li><?php _e('This ' . $group_type_name_uc . ' Profile will NOT be listed in the ' . $group_type_name_uc . ' directory, search results, or OpenLab home page.', 'buddypress') ?></li>
                <li><?php _e('Only OpenLab members who are invited may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
            </ul>
        </label>
    </div>

    <?php /* Site privacy markup */ ?>

    <?php if ($site_id = openlab_get_site_id_by_group_id()) : ?>
        <h5><?php _e($group_type_name_uc . ' Site') ?></h5>
        <p class="privacy-settings-tag-c"><?php _e('These settings affect how others view your ' . $group_type_name_uc . ' Site.') ?></p>
        <?php openlab_site_privacy_settings_markup($site_id) ?>
    <?php endif ?>

    <?php if ($bp->current_action == 'admin'): ?>
        <?php do_action('bp_after_group_settings_admin'); ?>
        <p><input type="submit" value="<?php _e('Save Changes', 'buddypress') ?> &rarr;" id="save" name="save" /></p>
        <?php wp_nonce_field('groups_edit_group_settings'); ?>
    <?php elseif ($bp->current_action == 'create'): ?>
        <?php wp_nonce_field('groups_create_save_group-settings') ?>
        <?php
    endif;
}

/**
 * This function outputs the full archive for a specific group, currently delineated by the archive page slug
 *
 */
function openlab_group_archive() {
    global $wpdb, $bp, $groups_template, $post;

    if (!bp_is_active('groups')) {
        return;
    }

//geting the grouptype by slug - the archive pages are curently WP pages and don't have a specific grouptype associated with them - this function uses the curent page slug to assign a grouptype
//@to-do - get the archive page in the right spot to function correctly within the BP framework
    $group_type = openlab_page_slug_to_grouptype();

    $sequence_type = '';
    if (!empty($_GET['group_sequence'])) {
        $sequence_type = "type=" . $_GET['group_sequence'] . "&";
    }

    $search_terms = $search_terms_raw = '';

    if (!empty($_POST['group_search'])) {
        $search_terms_raw = $_POST['group_search'];
        $search_terms = "search_terms=" . $search_terms_raw . "&";
    }
    if (!empty($_GET['search'])) {
        $search_terms_raw = $_GET['search'];
        $search_terms = "search_terms=" . $search_terms_raw . "&";
    }

    if (!empty($_GET['school'])) {
        $school = $_GET['school'];
        /* if ( $school=="tech" ) {
          $school="Technology & Design";
          } elseif ( $school=="studies" ) {
          $school="Professional Studies";
          } elseif ( $school=="arts" ) {
          $school="Arts & Sciences";
          } */
    }

    if (!empty($_GET['department'])) {
        $department = str_replace("-", " ", $_GET['department']);
        $department = ucwords($department);
    }
    if (!empty($_GET['semester'])) {
        $semester = str_replace("-", " ", $_GET['semester']);
        $semester = explode(" ", $semester);
        $semester_season = ucwords($semester[0]);
        $semester_year = ucwords($semester[1]);
        $semester = trim($semester_season . ' ' . $semester_year);
    }

// Set up filters
    $meta_query = array(
        array(
            'key' => 'wds_group_type',
            'value' => $group_type,
        ),
    );

    if (!empty($school) && 'school_all' != strtolower($school)) {
        $meta_query[] = array(
            'key' => 'wds_group_school',
            'value' => $school,
            'compare' => 'LIKE',
        );
    }

    if (!empty($department) && 'dept_all' != strtolower($department)) {
        $meta_query[] = array(
            'key' => 'wds_departments',
            'value' => $department,
            'compare' => 'LIKE',
        );
    }

    if (!empty($semester) && 'semester_all' != strtolower($semester)) {
        $meta_query[] = array(
            'key' => 'wds_semester',
            'value' => $semester_season,
        );
        $meta_query[] = array(
            'key' => 'wds_year',
            'value' => $semester_year,
        );
    }

    if (!empty($_GET['usertype']) && 'user_type_all' != $_GET['usertype']) {
        $meta_query[] = array(
            'key' => 'portfolio_user_type',
            'value' => ucwords($_GET['usertype']),
        );
    }

    $group_args = array(
        'search_terms' => $search_terms_raw,
        'per_page' => 12,
        'meta_query' => $meta_query,
    );

    if (!empty($_GET['group_sequence'])) {
        $group_args['type'] = $_GET['group_sequence'];
    }

    if (bp_has_groups($group_args)) :
        ?>
        <div class="current-group-filters current-portfolio-filters">
            <?php openlab_current_directory_filters(); ?>
        </div>
        <div class="group-count"><?php cuny_groups_pagination_count(ucwords($group_type) . 's'); ?></div>
        <div class="clearfloat"></div>
        <ul id="group-list" class="item-list">
            <?php
            $count = 1;
            while (bp_groups()) : bp_the_group();
                $group_id = bp_get_group_id();
                ?>
                <li class="course<?php echo cuny_o_e_class($count) ?> col-md-6">
                    <div class="item-avatar alignleft">
                        <a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array('type' => 'full', 'width' => 100, 'height' => 100)) ?></a>
                    </div>
                    <div class="item">

                        <h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
                        <?php
                        //course group type
                        if ($group_type == 'course'):
                            ?>

                            <?php
                            $admins = groups_get_group_admins($group_id);
                            $faculty_id = $admins[0]->user_id;
                            $first_name = ucfirst(xprofile_get_field_data('First Name', $faculty_id));
                            $last_name = ucfirst(xprofile_get_field_data('Last Name', $faculty_id));
                            $wds_faculty = $first_name . " " . $last_name;
                            $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
                            $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
                            $wds_year = groups_get_groupmeta($group_id, 'wds_year');
                            $wds_departments = groups_get_groupmeta($group_id, 'wds_departments');
                            ?>
                            <div class="info-line"><?php echo $wds_faculty; ?> | <?php echo openlab_shortened_text($wds_departments, 20); ?> | <?php echo $wds_course_code; ?><br /> <?php echo $wds_semester; ?> <?php echo $wds_year; ?></div>
                        <?php elseif ($group_type == 'portfolio'): ?>

                            <div class="info-line"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>

                        <?php endif; ?>

                        <?php
                        $len = strlen(bp_get_group_description());
                        if ($len > 135) {
                            $this_description = substr(bp_get_group_description(), 0, 135);
                            $this_description = str_replace("</p>", "", $this_description);
                            echo $this_description . '&hellip; <a href="' . bp_get_group_permalink() . '">See&nbsp;More</a></p>';
                        } else {
                            bp_group_description();
                        }
                        ?>
                    </div><!--item-->

                </li>
                <?php
                if ($count % 2 == 0) {
                    echo '<hr style="clear:both;" />';
                }
                ?>
                <?php $count++ ?>
            <?php endwhile; ?>
        </ul>

        <div class="pagination-links" id="group-dir-pag-top">
            <?php bp_groups_pagination_links() ?>
        </div>
    <?php else: ?>
        <div class="current-group-filters current-portfolio-filters">
            <?php openlab_current_directory_filters(); ?>
        </div>
        <div class="widget-error">
            <?php _e('There are no ' . $group_type . 's to display.', 'buddypress') ?>
        </div>

    <?php endif; ?>
    <?php
}

/*
 * Redirect to users profile after deleting a group
 */
add_action('groups_group_deleted', 'openlab_delete_group', 20);

/**
 * After portfolio delete, redirect to user profile page
 */
function openlab_delete_group() {
    bp_core_redirect(bp_loggedin_user_domain());
}

/**
 * This function prints out the departments for the course archives ( non ajax )
 *
 * @param string $school The id of the school to return a course list for
 * @param string $department The id of the deparment currently selected in
 *        the dropdown.
 */
function openlab_return_course_list($school, $department) {

    $list = '<option value="dept_all" ' . selected('', $department) . ' >All</option>';

    // Sanitize. If no value is found, don't return any
    // courses
    if (!in_array($school, array('tech', 'studies', 'arts'))) {
        return $list;
    }

    $depts = openlab_get_department_list($school, 'short');

    foreach ($depts as $dept_name => $dept_label) {
        $list .= '<option value="' . esc_attr($dept_name) . '" ' . selected($department, $dept_name, false) . '>' . esc_attr($dept_label) . '</option>';
    }

    return $list;
}

function openlab_group_post_count($filters, $group_args) {

    $post_count = 0;

    $meta_filter = new BP_Groups_Meta_Filter($filters);
    if (bp_has_groups($group_args)) :

        while (bp_groups()) : bp_the_group();
            $post_count++;
        endwhile;

    endif;
    $meta_filter->remove_filters();

    return $post_count;
}

//a variation on bp_groups_pagination_count() to match design
function cuny_groups_pagination_count($group_name) {
    global $bp, $groups_template;

    $start_num = intval(( $groups_template->pag_page - 1 ) * $groups_template->pag_num) + 1;
    $from_num = bp_core_number_format($start_num);
    $to_num = bp_core_number_format(( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ) );
    $total = bp_core_number_format($groups_template->total_group_count);

    echo sprintf(__('%1$s to %2$s ( of %3$s ' . $group_name . ' )', 'buddypress'), $from_num, $to_num, $total);
}

/**
 * Get list of active semesters for use in course sidebar filter.
 */
function openlab_get_active_semesters() {
    global $wpdb, $bp;

    $tkey = 'openlab_active_semesters';
    $combos = get_transient($tkey);

    if (false === $combos) {
        $sems = array('Winter', 'Spring', 'Summer', 'Fall');
        $years = array();
        $this_year = date('Y');
        for ($i = 2011; $i <= $this_year; $i++) {
            $years[] = $i;
        }

        // Combos
        $combos = array();
        foreach ($years as $year) {
            foreach ($sems as $sem) {
                $combos[] = array(
                    'year' => $year,
                    'sem' => $sem,
                    'option_value' => sprintf('%s-%s', strtolower($sem), $year),
                    'option_label' => sprintf('%s %s', $sem, $year),
                );
            }
        }

        // Verify that the combos are all active
        foreach ($combos as $ckey => $c) {
            $active = (bool) $wpdb->get_var($wpdb->prepare("SELECT COUNT(gm1.id) FROM {$bp->groups->table_name_groupmeta} gm1 JOIN {$bp->groups->table_name_groupmeta} gm2 ON gm1.group_id = gm2.group_id WHERE gm1.meta_key = 'wds_semester' AND gm1.meta_value = %s AND gm2.meta_key = 'wds_year' AND gm2.meta_value = %s", $c['sem'], $c['year']));

            if (!$active) {
                unset($combos[$ckey]);
            }
        }

        $combos = array_values(array_reverse($combos));

        set_transient($tkey, $combos);
        var_dump('Miss');
    }

    return $combos;
}

/**
 * Output the sidebar content for a single group
 */
function cuny_buddypress_group_actions() {
    if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
            ?>
            <div class="group-nav sidebar-widget">
                <?php echo openlab_group_visibility_flag() ?>
                <div id="item-buttons">
                    <h2 class="sidebar-header"><?php echo openlab_get_group_type_label('case=upper') ?></h2>
                    <ul>
                        <?php bp_get_options_nav(); ?>
                    </ul>
                </div><!-- #item-buttons -->
            </div>
            <?php do_action('bp_group_options_nav') ?>
            <?php
        endwhile;
    endif;
}

/**
 * Output the group visibility flag, shown above the right-hand nav
 */
function openlab_group_visibility_flag($type = 'group') {
    static $group_buttons;

    if (!in_array($type, array('group', 'site'))) {
        return;
    }

    if (!isset($group_buttons)) {
        $group_buttons = array();
    }

    // We stash it so that we only have to do the calculation once
    if (isset($group_buttons[$type])) {
        return $group_buttons[$type];
    }

    $group = groups_get_current_group();

    $site_url = openlab_get_group_site_url($group->id);
    $site_id = openlab_get_site_id_by_group_id($group->id);

    if ($site_url) {
        // If we have a site URL but no ID, it's an external site, and is public
        if (!$site_id) {
            $site_status = 1;
        } else {
            $site_status = get_blog_option($site_id, 'blog_public');
        }
    }

    $g_text = $s_text = '';
    $g_flag_type = $s_flag_type = 'down';
    $site_status = (float) $site_status;

    switch ($site_status) {

        // Public
        case 1 :
        case 0 :
            // If the group is also public, we use a single "up" flag
            if ('public' === $group->status) {
                $g_text = 'Open';
                $g_flag_type = 'up';

                // Special case: groups without a site will show up as
                // $site_status = 0. They should get an up flag, since
                // "Private" applies to the entire group (the entire
                // group consisting of the profile)
            } else if (!$site_url) {
                $g_text = 'Private';
                $g_flag_type = 'up';
            } else {
                $g_text = 'Private';
                $s_text = 'Open';
            }

            break;

        case -1 :
            $user_has_access = is_user_logged_in();

            if ('public' === $group->status) {
                $g_text = 'Open';
            } else {
                $g_text = 'Private';
            }

            if ($user_has_access) {
                // If the group is public, show a single Public up flag
                if ('public' === $group->status) {
                    $g_flag_type = 'up';

                    // For a private group, separate the flags
                } else {
                    $s_text = 'Open';
                }
            } else {
                // Two separate flags
                if ('public' === $group->status) {
                    $s_text = 'Private';

                    // Single "up" private flag
                } else {
                    $g_flag_type = 'up';
                }
            }

            break;

        case -2 :
        case -3 :
            if ('public' === $group->status) {
                $g_text = 'Open';
                $s_text = 'Private';
            } else {
                $g_text = 'Private';
                $g_flag_type = 'up';
            }

            break;
    }

    // Assemble the HTML
    $group_buttons['group'] = sprintf(
            '<div class="group-visibility-flag group-visibility-flag-group group-visibility-flag-%s group-visibility-flag-%s">%s</div>', strtolower($g_text), $g_flag_type, $g_text
    );

    // Only build the site button if there's something to build
    if (!empty($s_text)) {
        $group_buttons['site'] = sprintf(
                '<div class="group-visibility-flag group-visibility-flag-site group-visibility-flag-%s group-visibility-flag-%s">%s</div>', strtolower($s_text), $s_flag_type, $s_text
        );
    }

    return isset($group_buttons[$type]) ? $group_buttons[$type] : '';
}

/**
 * Markup for groupblog privacy settings
 */
function openlab_site_privacy_settings_markup($site_id = 0) {
    global $blogname, $current_site;

    if (!$site_id) {
        $site_id = get_current_blog_id();
    }

    $blog_name = get_blog_option($site_id, 'blogname');
    $blog_public = get_blog_option($site_id, 'blog_public');
    $group_type = openlab_get_current_group_type('case=upper');
    ?>

    <div class="radio group-site">

        <h6><?php _e('Public', 'buddypress') ?></h6>
        <span id="search-setting-note">Note: These options will NOT block access to your site. It is up to search engines to honor your request.</span>
        <label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked('1', $blog_public); ?> /><?php _e('Allow search engines to index this site. Your site will show up in web search results.'); ?></label>

        <label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked('0', $blog_public); ?> /><?php _e('Ask search engines not to index this site. Your site should not show up in web search results.'); ?></label>

        <?php if (!openlab_is_portfolio() && (!isset($_GET['type']) || 'portfolio' != $_GET['type'] )): ?>

            <h6><?php _e('Private', 'buddypress') ?></h6>
            <label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', $blog_public); ?>><?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.', 'buddypress'); ?></label>

            <label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public); ?>><?php _e('I would like my site to be visible to registered users of this ' . ucfirst($group_type) . '.'); ?></label>

            <h6><?php _e('Hidden', 'buddypress') ?></h6>
            <label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public); ?>><?php _e('I would like my site to be visible only to site administrators.'); ?></label>

        <?php else : ?>

            <?php /* Portfolios */ ?>
            <h6>Private</h6>
            <label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', $blog_public); ?>><?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.', 'buddypress'); ?></label>

            <label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public); ?>>I would like my site to be visible only to registered users that I have granted access.</label>
            <p class="description private-portfolio-gloss">Note: If you would like non-City Tech users to view your private site, you will need to make your site public.</p>

            <label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public); ?>>I would like my site to be visible only to me.</label>

        <?php endif; ?>
    </div>
    <?php
}
