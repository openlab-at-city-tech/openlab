<?php
/**
 * Library of group-related functions
 *
 */

/**
 * Custom template loader for my-{grouptype}
 */
function openlab_mygroups_template_loader($template) {
    if (is_page()) {
        switch (get_query_var('pagename')) {
            case 'my-courses' :
            case 'my-clubs' :
            case 'my-projects' :
                get_template_part('buddypress/groups/index');
                break;
        }
    }

    return $template;
}

add_filter('template_include', 'openlab_mygroups_template_loader');

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
    <div class="panel panel-default">
        <div class="panel-heading semibold"><?php _e('Privacy Settings', 'buddypress'); ?><?php if ($bp->current_action == 'admin' || $bp->current_action == 'create' || openlab_is_portfolio()): ?>: <?php echo $group_type_name_uc ?> Profile<?php endif; ?></div>

        <div class="radio group-profile panel-body">

            <?php if ($bp->current_action == 'create'): ?>
                <p id="privacy-settings-tag-b"><?php _e('These settings affect how others view your ' . $group_type_name . ' Profile. You may change these settings later in the course Profile Settings.', 'buddypress'); ?></p>
            <?php else: ?>
                <p class="privacy-settings-tag-c"><?php _e('These settings affect how others view your ' . $group_type_name_uc . ' Profile.') ?></p>
            <?php endif; ?>

            <?php
            $new_group_status = bp_get_new_group_status();
            if (!$new_group_status) {
                $new_group_status = !empty($clone_source_group_status) ? $clone_source_group_status : 'public';
            }
            ?>
            <div class="row">
                <div class="col-sm-23 col-sm-offset-1">
                    <label><input type="radio" name="group-status" value="public" <?php checked('public', $new_group_status) ?> />
                        This is a public <?php echo $group_type_name_uc ?></label>
                    <ul>
                        <li>This <?php echo $group_type_name_uc ?> Profile and related content and activity will be visible to the public.</li>
                        <li><?php _e('This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . 's directory, search results, and may be displayed on the OpenLab home page.', 'buddypress') ?></li>
                        <li><?php _e('Any OpenLab member may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
                    </ul>

                    <label><input type="radio" name="group-status" value="private" <?php checked('private', $new_group_status) ?> />
                        <?php _e('This is a private ' . $group_type_name_uc, 'buddypress') ?></label>
                    <ul>
                        <li><?php _e('This ' . $group_type_name_uc . ' Profile and related content and activity will only be visible to members of the group.', 'buddypress') ?></li>
                        <li><?php _e('This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . ' directory, search results, and may be displayed on the OpenLab home page.', 'buddypress') ?></li>
                        <li><?php _e('Only OpenLab members who request membership and are accepted may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
                    </ul>

                    <label><input type="radio" name="group-status" value="hidden" <?php checked('hidden', $new_group_status) ?> />
                        <?php _e('This is a hidden ' . $group_type_name_uc, 'buddypress') ?></label>
                    <ul>
                        <li><?php _e('This ' . $group_type_name_uc . ' Profile, related content and activity will only be visible only to members of the ' . $group_type_name_uc . '.', 'buddypress') ?></li>
                        <li><?php _e('This ' . $group_type_name_uc . ' Profile will NOT be listed in the ' . $group_type_name_uc . ' directory, search results, or OpenLab home page.', 'buddypress') ?></li>
                        <li><?php _e('Only OpenLab members who are invited may join this ' . $group_type_name_uc . '.', 'buddypress') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php /* Site privacy markup */ ?>

    <?php if ($site_id = openlab_get_site_id_by_group_id()) : ?>
        <div class="panel panel-default">
            <div class="panel-heading semibold"><?php _e($group_type_name_uc . ' Site') ?></div>
            <div class="panel-body">
                <p class="privacy-settings-tag-c"><?php _e('These settings affect how others view your ' . $group_type_name_uc . ' Site.') ?></p>
                <?php openlab_site_privacy_settings_markup($site_id) ?>
            </div>
        </div>
    <?php endif ?>

    <?php if ($bp->current_action == 'admin'): ?>
        <?php do_action('bp_after_group_settings_admin'); ?>
        <p><input class="btn btn-primary" type="submit" value="<?php _e('Save Changes', 'buddypress') ?> &#xf138;" id="save" name="save" /></p>
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

    if (!empty($_GET['cat'])) {
        $categories = $_GET['cat'];
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

    if (!empty($categories)) {

        if ('cat_all' === strtolower($categories)) {

            $terms = get_terms('bp_group_categories');
            $term_ids = wp_list_pluck($terms, 'term_id');
        } else {
            $term_obj = get_term_by('slug', $categories, 'bp_group_categories');
            $term_ids = $term_obj->term_id;
        }

        $group_args['tax_query'] = array(
            array(
                'taxonomy' => 'bp_group_categories',
                'terms' => $term_ids,
                'field' => 'term_id',
            )
        );
    }

    if (!empty($_GET['group_sequence'])) {
        $group_args['type'] = $_GET['group_sequence'];
    }

    if (bp_has_groups($group_args)) :
        ?>
        <div class="row group-archive-header-row">
            <div class="current-group-filters current-portfolio-filters col-lg-19 col-md-18 col-sm-16">
                <?php openlab_current_directory_filters(); ?>
            </div>
            <div class="group-count col-lg-5 col-md-6 col-sm-8"><?php cuny_groups_pagination_count(ucwords($group_type) . 's'); ?></div>
        </div>
        <div id="group-list" class="item-list group-list row">
            <?php
            $count = 1;
            while (bp_groups()) : bp_the_group();
                $group_id = bp_get_group_id();
                ?>
                <div class="group-item col-xs-12">
                    <div class="group-item-wrapper">
                        <div class="row">
                            <div class="item-avatar alignleft col-xs-6">
                                <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_attr(bp_get_group_name()); ?>"/></a>
                            </div>
                            <div class="item col-xs-18">

                                <p class="item-title h2">
                                    <a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_group_permalink() ?>" data-basevalue="<?php echo ($group_type == 'course' ? 50 : 65 ) ?>" data-minvalue="20" data-basewidth="290"><?php bp_group_name() ?></a>
                                    <span class="original-copy hidden"><?php bp_group_name() ?></span>
                                </p>
                                <?php
                                //course group type
                                if ($group_type == 'course'):
                                    ?>

                                    <div class="info-line uppercase">
                                        <?php echo openlab_output_course_info_line($group_id); ?>
                                    </div>
                                <?php elseif ($group_type == 'portfolio'): ?>

                                    <div class="info-line"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>

                                <?php endif; ?>
                                <div class="description-line">
                                    <p class="truncate-on-the-fly" data-link="<?php echo bp_get_group_permalink() ?>" data-basevalue="105" data-basewidth="290"><?php echo bp_get_group_description_excerpt() ?></p>
                                    <p class="original-copy hidden"><?php echo bp_get_group_description_excerpt() ?></p>
                                </div>
                            </div>
                        </div><!--item-->
                    </div>
                </div>
                <?php $count++ ?>
            <?php endwhile; ?>
        </div>

        <div class="pagination-links" id="group-dir-pag-top">
            <?php echo openlab_groups_pagination_links() ?>
        </div>
    <?php else: ?>
        <div class="row group-archive-header-row">
            <div class="current-group-filters current-portfolio-filters col-sm-19">
                <?php openlab_current_directory_filters(); ?>
            </div>
        </div>
        <div id="group-list" class="item-list row">
            <div class="widget-error query-no-results col-sm-24">
                <p class="bold"><?php _e('There are no ' . $group_type . 's to display.', 'buddypress') ?></p>
            </div>
        </div>

    <?php endif; ?>
    <?php
}

function openlab_groups_pagination_links() {
    global $groups_template;

    $pagination = paginate_links(array(
        'base' => add_query_arg(array('grpage' => '%#%', 'num' => $groups_template->pag_num, 's' => $search_terms, 'sortby' => $groups_template->sort_by, 'order' => $groups_template->order)),
        'format' => '',
        'total' => ceil((int) $groups_template->total_group_count / (int) $groups_template->pag_num),
        'current' => $groups_template->pag_page,
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

function openlab_forum_pagination() {
    global $forum_template;

    $pagination = paginate_links(array(
        'base' => add_query_arg(array('p' => '%#%', 'n' => $forum_template->pag_num)),
        'format' => '',
        'total' => ceil((int) $forum_template->total_topic_count / (int) $forum_template->pag_num),
        'current' => $forum_template->pag_page,
        'prev_text' => _x('<i class="fa fa-angle-left" aria-hidden="true"></i>', 'Forum pagination previous text', 'buddypress'),
        'next_text' => _x('<i class="fa fa-angle-right" aria-hidden="true"></i>', 'Forum pagination next text', 'buddypress'),
        'mid_size' => 3,
        'type' => 'list',
    ));

    $pagination = str_replace('page-numbers', 'page-numbers pagination', $pagination);
    return $pagination;
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

    $list = '<option value="dept_all" ' . selected('', $department) . ' >All Departments</option>';

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
    $to_num = bp_core_number_format(( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ));
    $total = bp_core_number_format($groups_template->total_group_count);

    echo sprintf(__('%1$s to %2$s (of %3$s ' . $group_name . ')', 'buddypress'), $from_num, $to_num, $total);
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
    }

    return $combos;
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

        <h5><?php _e('Public', 'buddypress') ?></h5>
        <p id="search-setting-note" class="italics note">Note: These options will NOT block access to your site. It is up to search engines to honor your request.</p>
        <div class="row">
            <div class="col-sm-23 col-sm-offset-1">
                <p><label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked('1', $blog_public); ?> /><?php _e('Allow search engines to index this site. Your site will show up in web search results.'); ?></label></p>

                <p><label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked('0', $blog_public); ?> /><?php _e('Ask search engines not to index this site. Your site should not show up in web search results.'); ?></label></p>
            </div>
        </div>

        <?php if (!openlab_is_portfolio() && (!isset($_GET['type']) || 'portfolio' != $_GET['type'] )): ?>

            <h5><?php _e('Private', 'buddypress') ?></h5>
            <div class="row">
                <div class="col-sm-23 col-sm-offset-1">
                    <p><label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', $blog_public); ?>><?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.', 'buddypress'); ?></label></p>

                    <p><label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public); ?>><?php _e('I would like my site to be visible to registered users of this ' . ucfirst($group_type) . '.'); ?></label></p>
                </div>
            </div>

            <h5><?php _e('Hidden', 'buddypress') ?></h5>
            <div class="row">
                <div class="col-sm-23 col-sm-offset-1">
                    <p><label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public); ?>><?php _e('I would like my site to be visible only to site administrators.'); ?></label></p>
                </div>
            </div>

        <?php else : ?>

            <?php /* Portfolios */ ?>
            <h5>Private</h5>
            <div class="row">
                <div class="col-sm-23 col-sm-offset-1">
                    <p><label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked('-1', $blog_public); ?>><?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.', 'buddypress'); ?></label></p>

                    <p><label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public); ?>>I would like my site to be visible only to registered users that I have granted access.</label></p>
                    <p class="description private-portfolio-gloss italics note">Note: If you would like non-City Tech users to view your private site, you will need to make your site public.</p>

                    <p><label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public); ?>>I would like my site to be visible only to me.</label></p>
                </div>
            </div>

        <?php endif; ?>
    </div>
    <?php
}

function openlab_group_profile_header() {
    global $bp;
    $group_type = groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type');
    ?>
    <h1 class="entry-title group-title clearfix"><span class="profile-name hyphenate"><?php echo bp_group_name(); ?></span>
        <button data-target="#sidebar-menu-wrapper" data-backgroundonly="true" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button></h1>
    <?php if (bp_is_group_home() || (bp_is_group_admin_page() && !$bp->is_item_admin)): ?>
        <div class="clearfix">
            <?php if ($group_type != "portfolio") : ?>
                <div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php printf(__('active %s', 'buddypress'), bp_get_group_last_active()) ?></span></div>
            <?php endif; ?>
        </div>
    <?php elseif (bp_is_group_home()): ?>
        <div class="clearfix visible-xs">
            <?php if ($group_type != "portfolio") : ?>
                <div class="info-line pull-right"><span class="timestamp info-line-timestamp"><span class="fa fa-undo" aria-hidden="true"></span> <?php printf(__('active %s', 'buddypress'), bp_get_group_last_active()) ?></span></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php
}

add_action('bp_before_group_body', 'openlab_group_profile_header');

function openlab_get_privacy_icon() {

    switch (bp_get_group_status()) {
        case 'public':
            $status = '<span class="fa fa-eye" aria-hidden="true"></span>';
            break;
        case 'private':
            $status = '<span class="fa fa-lock" aria-hidden="true"></span>';
            break;
        case 'hidden':
            $status = '<span class="fa fa-eye-slash" aria-hidden="true"></span>';
            break;
        default:
            $status = '<span class="fa fa-eye" aria-hidden="true"></span>';
    }

    return $status;
}

function cuny_group_single() {
    ?>
    <?php
    $group_type = openlab_get_group_type(bp_get_current_group_id());
    ?>

    <?php $group_slug = bp_get_group_slug(); ?>

    <?php
    //group page vars
    global $bp, $wpdb;
    $group_id = $bp->groups->current_group->id;
    $group_name = $bp->groups->current_group->name;
    $group_description = $bp->groups->current_group->description;
    $group_type = openlab_get_group_type(bp_get_current_group_id());
    $section = groups_get_groupmeta($group_id, 'wds_section_code');
    $html = groups_get_groupmeta($group_id, 'wds_course_html');
    ?>

    <?php if (bp_is_group_home()): ?>
        <div id="<?php echo $group_type; ?>-header" class="group-header row">

            <div id="<?php echo $group_type; ?>-header-avatar" class="alignleft group-header-avatar col-sm-8 col-xs-12">
                <div class="padded-img darker">
                    <img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_attr($group_name); ?>"/>
                </div>

                <?php if (is_user_logged_in() && $bp->is_item_admin): ?>
                    <div id="group-action-wrapper">
                        <a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo bp_group_permalink() . 'admin/edit-details/'; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Profile</a>
                        <a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo bp_group_permalink() . 'admin/group-avatar/'; ?>"><i class="fa fa-camera" aria-hidden="true"></i> Change Avatar</a>
                    </div>
                <?php elseif (is_user_logged_in()) : ?>
                    <div id="group-action-wrapper">
                        <?php do_action('bp_group_header_actions'); ?>
                    </div>
                <?php endif; ?>
                <?php openlab_render_message(); ?>
        </div><!-- #<?php echo $group_type; ?>-header-avatar -->

            <div id="<?php echo $group_type; ?>-header-content" class="col-sm-16 col-xs-24 alignleft group-header-content group-<?php echo $group_id; ?>">

                <?php do_action('bp_before_group_header_meta') ?>

                <?php if ($group_type == "course"): ?>
                    <div class="info-panel panel panel-default no-margin no-margin-top">
                        <?php
                        $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
                        $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
                        $wds_year = groups_get_groupmeta($group_id, 'wds_year');
                        $wds_departments = groups_get_groupmeta($group_id, 'wds_departments');
                        ?>
                        <div class="table-div">
                            <?php
                            if (bp_is_group_home() && openlab_group_status_message() != '') {

                                do_action('bp_before_group_status_message')
                                ?>

                                <div class="table-row row">
                                    <div class="col-xs-24 status-message italics"><?php echo openlab_group_status_message() ?></div>
                                </div>

                                <?php
                                do_action('bp_after_group_status_message');
                            }
                            ?>
                            <div class="table-row row">
                                <div class="bold col-sm-7">Professor(s)</div>
                                <div class="col-sm-17 row-content"><?php echo openlab_get_faculty_list() ?></div>
                            </div>
                            <div class="table-row row">
                                <div class="bold col-sm-7">Department</div>
                                <div class="col-sm-17 row-content"><?php echo $wds_departments; ?></div>
                            </div>
                            <div class="table-row row">
                                <div class="bold col-sm-7">Course Code</div>
                                <div class="col-sm-17 row-content"><?php echo $wds_course_code; ?></div>
                            </div>
                            <div class="table-row row">
                                <div class="bold col-sm-7">Semester / Year</div>
                                <div class="col-sm-17 row-content"><?php echo $wds_semester; ?> <?php echo $wds_year; ?></div>
                            </div>
                            <div class="table-row row">
                                <div class="bold col-sm-7">Course Description</div>
                                <div class="col-sm-17 row-content"><?php echo apply_filters('the_content', $group_description); ?></div>
                            </div>
                        </div>

                    </div>

                    <?php do_action('bp_group_header_meta') ?>

                <?php else : ?>

                    <div class="info-panel panel panel-default no-margin no-margin-top">
                        <div class="table-div">
                            <div class="table-row row">
                                <div class="col-xs-24 status-message italics"><?php echo openlab_group_status_message() ?></div>
                            </div>

                            <?php
                            $wds_school = openlab_generate_school_name($group_id);
                            $wds_departments = openlab_generate_department_name($group_id);
                            ?> 

                            <?php if ($wds_school && !empty($wds_school)): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">School</div>
                                    <div class="col-sm-17 row-content"><?php echo $wds_school; ?></div>
                                </div>

                            <?php endif; ?>

                            <?php if ($wds_departments && !empty($wds_departments)): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">Department</div>
                                    <div class="col-sm-17 row-content"><?php echo $wds_departments; ?></div>
                                </div>

                            <?php endif; ?>

                            <?php if (function_exists('bpcgc_get_group_selected_terms')): ?>
                                <?php if ($group_terms = bpcgc_get_group_selected_terms($group_id, true)): ?>
                                    <div class="table-row row">
                                        <div class="bold col-sm-7">Category</div>
                                        <div class="col-sm-17 row-content"><?php echo implode(', ', wp_list_pluck($group_terms, 'name')); ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="table-row row">
                                <div class="bold col-sm-7"><?php echo ucfirst($group_type); ?> Description</div>
                                <div class="col-sm-17 row-content"><?php bp_group_description() ?></div>
                            </div>

                            <?php if ($group_type == "portfolio"): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">Member Profile</div>
                                    <div class="col-sm-17 row-content"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>

                <?php endif; ?>
            </div><!-- .header-content -->

            <?php do_action('bp_after_group_header') ?>

                                                                                                                                                                                                                                                                            </div><!--<?php echo $group_type; ?>-header -->

    <?php endif; ?>

    <?php
    openlab_group_profile_activity_list();
}

function openlab_render_message() {
    global $bp;

    if (!empty($bp->template_message)) :
        $type = ( 'success' == $bp->template_message_type ) ? 'updated' : 'error';
        $content = apply_filters('bp_core_render_message_content', $bp->template_message, $type);
        ?>

        <div id="message" class="bp-template-notice <?php echo $type; ?> btn btn-default btn-block btn-primary link-btn clearfix">

            <span class="pull-left fa fa-check" aria-hidden="true"></span>
            <?php echo $content; ?>

        </div>

        <?php
        do_action('bp_core_render_message');

    endif;
}

function openlab_group_profile_activity_list() {
    global $wpdb, $bp;
    ?>
    <div id="single-course-body">
        <?php
//
//     control the formatting of left and right side by use of variable $first_class.
//     when it is "first" it places it on left side, when it is "" it places it on right side
//
//     Initialize it to left side to start with
//
        $first_class = "first";
        ?>
        <?php $group_slug = bp_get_group_slug(); ?>
        <?php $group_type = openlab_get_group_type(bp_get_current_group_id()); ?>

        <?php
        $group = groups_get_current_group();
        ?>

        <?php if (bp_is_group_home()) { ?>

            <?php if (bp_get_group_status() == 'public' || ((bp_get_group_status() == 'hidden' || bp_get_group_status() == 'private') && (bp_is_item_admin() || bp_group_is_member()))) : ?>
                <?php
                if (wds_site_can_be_viewed()) {
                    openlab_show_site_posts_and_comments();
                }
                ?>

                <?php if ($group_type != "portfolio"): ?>
                    <div class="row group-activity-overview">
                        <div class="col-sm-12">
                            <div class="recent-discussions">
                                <div class="recent-posts">
                                    <h2 class="title activity-title"><a class="no-deco" href="<?php site_url(); ?>/groups/<?php echo $group_slug; ?>/forum/">Recent Discussions<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
                                    <?php
                                    $forum_ids = bbp_get_group_forum_ids(bp_get_current_group_id());

                                    // Get the first forum ID
                                    if (!empty($forum_ids)) {
                                        $forum_id = (int) is_array($forum_ids) ? $forum_ids[0] : $forum_ids;
                                    }
                                    ?>

                                    <?php if ($forum_id && bbp_has_topics('posts_per_page=3&post_parent=' . $forum_id)) : ?>
                                        <?php while (bbp_topics()) : bbp_the_topic(); ?>


                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <?php
                                                    $topic_id = bbp_get_topic_id();
                                                    $last_reply_id = bbp_get_topic_last_reply_id($topic_id);

                                                    // Oh, bbPress.
                                                    $last_reply = get_post($last_reply_id);
                                                    if (!empty($last_reply->post_content)) {
                                                        $last_topic_content = wds_content_excerpt(strip_tags($last_reply->post_content), 250);
                                                    }
                                                    ?>

                                                    <?php echo openlab_get_group_activity_content(bbp_get_topic_title(), $last_topic_content, bbp_get_topic_permalink()) ?>

                                                </div></div>                                            <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="panel panel-default"><div class="panel-body">
                                                <p><?php _e('Sorry, there were no discussion topics found.', 'buddypress') ?></p>
                                            </div></div>
                                    <?php endif; ?>
                                </div><!-- .recent-post -->
                            </div>
                        </div>
                        <?php $first_class = ""; ?>
                        <div class="col-sm-12">
                            <div id="recent-docs">
                                <div class="recent-posts">
                                    <h2 class="title activity-title"><a class="no-deco" href="<?php site_url(); ?>/groups/<?php echo $group_slug; ?>/docs/">Recent Docs<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
                                    <?php
                                    $docs_arg = Array("posts_per_page" => "3",
                                        "post_type" => "bp_doc",
                                        "tax_query" =>
                                        Array(Array("taxonomy" => "bp_docs_associated_item",
                                                "field" => "slug",
                                                "terms" => $group_slug)));
                                    $query = new WP_Query($docs_arg);
                                    //				$query = new WP_Query( "posts_per_page=3&post_type=bp_doc&category_name=$group_slug" );
                                    //				$query = new WP_Query( "posts_per_page=3&post_type=bp_doc&category_name=$group_id" );
                                    global $post;
                                    if ($query->have_posts()) {
                                        while ($query->have_posts()) : $query->the_post();
                                            ?>
                                            <div class="panel panel-default"><div class="panel-body">
                                                    <?php echo openlab_get_group_activity_content(get_the_title(), wds_content_excerpt(strip_tags($post->post_content), 250), site_url() . '/groups/' . $group_slug . '/docs/' . $post->post_name); ?>
                                                </div></div>
                                            <?php
                                        endwhile;
                                    } else {
                                        echo '<div class="panel panel-default"><div class="panel-body"><p>No Recent Docs</p></div></div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="members-list" class="info-group">

                        <?php
                        if ($bp->is_item_admin || $bp->is_item_mod):
                            $href = site_url() . '/groups/' . $group_slug . '/admin/manage-members/';
                        else:
                            $href = site_url() . '/groups/' . $group_slug . '/members/';
                        endif;
                        ?>

                        <h2 class="title activity-title"><a class="no-deco" href="<?php echo $href; ?>">Members<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
                        <?php $member_arg = Array("exclude_admins_mods" => false); ?>
                        <?php if (bp_group_has_members($member_arg)) : ?>

                            <ul id="member-list" class="inline-element-list">
                                <?php
                                while (bp_group_members()) : bp_group_the_member();
                                    global $members_template;
                                    $member = $members_template->member;
                                    ?>
                                    <li class="inline-element">
                                        <a href="<?php echo bp_group_member_domain() ?>">
                                            <img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $member->ID, 'object' => 'member', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo $member->fullname; ?>"/>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php bp_group_member_pagination(); ?>
                        <?php else: ?>

                            <div id="message" class="info">
                                <p>This group has no members.</p>
                            </div>

                        <?php endif; ?>

                    </div>

                <?php endif; //end of if $group != 'portfolio'            ?>

            <?php else: ?>
                <?php
                //   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
                //   case show site posts and comments even though this group is private
                //
					if (wds_site_can_be_viewed()) {
                    openlab_show_site_posts_and_comments();
                    echo "<div class='clear'></div>";
                }
                ?>
                <?php /* The group is not visible, show the status message */ ?>

                <?php // do_action( 'bp_before_group_status_message' )             ?>
                <!--
                                                <div id="message" class="info">
                                                        <p><?php // bp_group_status_message()                                                 ?></p>
                                                </div>
                -->
                <?php // do_action( 'bp_after_group_status_message' )            ?>

            <?php endif; ?>

            <?php
        } else {
            bp_get_template_part('groups/single/wds-bp-action-logics.php');
        }
        ?>

    </div><!-- #single-course-body -->
    <?php
}

function openlab_get_group_activity_content($title, $content, $link) {
    $markup = '';

    if ($title !== '') {
        $markup = <<<HTML
                <p class="semibold h6">
                    <span class="hyphenate truncate-on-the-fly" data-basevalue="80" data-minvalue="55" data-basewidth="376">{$title}</span>
                    <span class="original-copy hidden">{$title}</span>
                </p>
HTML;
    }

    $markup .= <<<HTML
            <p class="activity-content">
                <span class="hyphenate truncate-on-the-fly" data-basevalue="120" data-minvalue="75" data-basewidth="376">{$content}</span>
                <span><a href="{$link}" class="read-more">See More<span class="sr-only">{$title}</span></a><span>
                <span class="original-copy hidden">{$content}</span>
            </p>
HTML;

    return $markup;
}

add_filter('bp_get_options_nav_nav-invite-anyone', 'cuny_send_invite_fac_only');

function cuny_send_invite_fac_only($subnav_item) {
    global $bp;
    $account_type = xprofile_get_field_data('Account Type', $bp->loggedin_user->id);

    if ($account_type != 'Student')
        return $subnav_item;
}

/**
 * Add the group type to the Previous Step button during group creation
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/397
 */
function openlab_previous_step_type($url) {
    if (!empty($_GET['type'])) {
        $url = add_query_arg('type', $_GET['type'], $url);
    }

    return $url;
}

add_filter('bp_get_group_creation_previous_link', 'openlab_previous_step_type');

/**
  >>>>>>> 1.3.x
 * Remove the 'hidden' class from hidden group leave buttons
 *
 * A crummy conflict with wp-ajax-edit-comments causes these items to be
 * hidden by jQuery. See b208c80 and #1004
 */
function openlab_remove_hidden_class_from_leave_group_button($button) {
    $button['wrapper_class'] = str_replace(' hidden', '', $button['wrapper_class']);
    return $button;
}

add_action('bp_get_group_join_button', 'openlab_remove_hidden_class_from_leave_group_button', 20);

function openlab_custom_group_buttons($button) {

    if ($button['id'] == 'leave_group') {
        $button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> ' . $button['link_text'] . '</span><i class="fa fa-minus-circle pull-right" aria-hidden="true"></i>';
        $button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
    } else if ($button['id'] == 'join_group' || $button['id'] == 'request_membership') {
        $button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> ' . $button['link_text'] . '</span><i class="fa fa-plus-circle pull-right" aria-hidden="true"></i>';
        $button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
    } else if ($button['id'] == 'membership_requested') {
        $button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> ' . $button['link_text'] . '</span><i class="fa fa-clock-o pull-right" aria-hidden="true"></i>';
        $button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
    } else if ($button['id'] == 'accept_invite') {
        $button['link_text'] = '<span class="pull-left"><i class="fa fa-user" aria-hidden="true"></i> ' . $button['link_text'] . '</span><i class="fa fa-plus-circle pull-right" aria-hidden="true"></i>';
        $button['link_class'] = $button['link_class'] . ' btn btn-default btn-block btn-primary link-btn clearfix';
    }

    return $button;
}

add_filter('bp_get_group_join_button', 'openlab_custom_group_buttons');

/**
 * Output the group subscription default settings
 *
 * This is a lazy way of fixing the fact that the BP Group Email Subscription
 * plugin doesn't actually display the correct default sub level ( even though it
 * does *save* the correct level )
 */
function openlab_default_subscription_settings_form() {
    if (openlab_is_portfolio() || ( isset($_GET['type']) && 'portfolio' == $_GET['type'] )) {
        return;
    }
    ?>
    <hr>
    <h4 id="email-sub-defaults"><?php _e('Email Subscription Defaults', 'bp-ass'); ?></h4>
    <p><?php _e('When new users join this group, their default email notification settings will be:', 'bp-ass'); ?></p>
    <div class="radio email-sub">
        <label><input type="radio" name="ass-default-subscription" value="no" <?php ass_default_subscription_settings('no') ?> />
            <?php _e('No Email ( users will read this group on the web - good for any group - the default )', 'bp-ass') ?></label>
        <label><input type="radio" name="ass-default-subscription" value="sum" <?php ass_default_subscription_settings('sum') ?> />
            <?php _e('Weekly Summary Email ( the week\'s topics - good for large groups )', 'bp-ass') ?></label>
        <label><input type="radio" name="ass-default-subscription" value="dig" <?php ass_default_subscription_settings('dig') ?> />
            <?php _e('Daily Digest Email ( all daily activity bundles in one email - good for medium-size groups )', 'bp-ass') ?></label>
        <label><input type="radio" name="ass-default-subscription" value="sub" <?php ass_default_subscription_settings('sub') ?> />
            <?php _e('New Topics Email ( new topics are sent as they arrive, but not replies - good for small groups )', 'bp-ass') ?></label>
        <label><input type="radio" name="ass-default-subscription" value="supersub" <?php ass_default_subscription_settings('supersub') ?> />
            <?php _e('All Email ( send emails about everything - recommended only for working groups )', 'bp-ass') ?></label>
    </div>
    <hr />
    <?php
}

remove_action('bp_after_group_settings_admin', 'ass_default_subscription_settings_form');
add_action('bp_after_group_settings_admin', 'openlab_default_subscription_settings_form');

/**
 * Save the group default email setting
 *
 * We override the way that GES does it, because we want to save the value even
 * if it's 'no'. This should probably be fixed upstream
 */
function openlab_save_default_subscription($group) {
    global $bp, $_POST;

    if (isset($_POST['ass-default-subscription']) && $postval = $_POST['ass-default-subscription']) {
        groups_update_groupmeta($group->id, 'ass_default_subscription', $postval);
    }
}

remove_action('groups_group_after_save', 'ass_save_default_subscription');
add_action('groups_group_after_save', 'openlab_save_default_subscription');

/**
 * Pagination links in group directories cannot contain the 's' URL parameter for search
 */
function openlab_group_pagination_search_key($pag) {
    if (false !== strpos($pag, 'grpage')) {
        $pag = remove_query_arg('s', $pag);
    }

    return $pag;
}

add_filter('paginate_links', 'openlab_group_pagination_search_key');

////////////////////////////
//    DIRECTORY FILTERS   //
////////////////////////////

/**
 * Get an array describing some details about filters
 *
 * This is the master function where filter data should be stored
 */
function openlab_get_directory_filter($filter_type, $label_type) {
    $filter_array = array(
        'type' => $filter_type,
        'label' => '',
        'options' => array()
    );

    switch ($filter_type) {
        case 'school' :
            $filter_array['label'] = 'School';
            $filter_array['options'] = array(
                'school_all' => 'All',
            );

            foreach (openlab_get_school_list() as $school_key => $school_label) {
                $filter_array['options'][$school_key] = $school_label;
            }

            break;

        case 'department' :
            $filter_array['label'] = 'Department';
            $filter_array['options'] = array(
                'dept_all' => 'All'
            );

            foreach (openlab_get_department_list('', 'short') as $depts) {
                foreach ($depts as $dept_key => $dept_label) {
                    $filter_array['options'][$dept_key] = $dept_label;
                }
            }

            break;

        case 'user_type' :
            $filter_array['label'] = 'User Type';
            $filter_array['options'] = array(
                'user_type_all' => 'All',
                'student' => 'Student',
                'faculty' => 'Faculty',
                'staff' => 'Staff'
            );
            break;

        case 'semester' :
            $filter_array['label'] = 'Semester';
            $filter_array['options'] = array();
            foreach (openlab_get_active_semesters() as $sem) {
                $filter_array['options'][$sem['option_value']] = $sem['option_label'];
            }
            break;
    }

    return $filter_array;
}

/**
 * Gets the current directory filters, and spits out some markup
 */
function openlab_current_directory_filters() {
    $filters = array();

    if (is_page('people')) {
        $current_view = 'people';
    } else {
        $current_view = openlab_get_current_group_type();
    }

    switch ($current_view) {
        case 'portfolio' :
            $filters = array('school', 'department', 'usertype');
            break;

        case 'course' :

            $filters = array('school', 'department', 'semester');
            break;

        case 'club' :
        case 'project' :
            $filters = array('school', 'department', 'cat', 'semester');
            break;

        case 'people' :
            $filters = array('usertype', 'school', 'department');
            break;

        default :
            break;
    }

    $active_filters = array();
    foreach ($filters as $f) {
        if (!empty($_GET[$f]) && !(strpos($_GET[$f], '_all'))) {
            $active_filters[$f] = $_GET[$f];
        }
    }

    $markup = '';
    if (!empty($active_filters)) {
        $markup .= '<h2 class="font-14 regular margin0-0 current-filters"><span class="bread-crumb">';

        $filter_words = array();
        foreach ($active_filters as $ftype => $fvalue) {
            $filter_data = openlab_get_directory_filter($ftype, 'short');

            $word = isset($filter_data['options'][$fvalue]) ? $filter_data['options'][$fvalue] : ucwords($fvalue);

            //dump hyphens from semester values
            if ($filter_data['type'] == 'semester') {
                $word = str_replace('-', ' ', $word);
            }

            //for group categories
            if ($filter_data['type'] == 'cat') {

                $term_obj = get_term_by('slug', $word, 'bp_group_categories');

                if ($term_obj) {
                    $word = $term_obj->name;
                } else {
                    $word = 'All';
                }
            }

            // Leave out the 'All's
            if ('All' != $word) {
                $filter_words[] = '<span>' . $word . '</span>';
            }
        }

        $markup .= implode('<span class="sep">&nbsp;&nbsp;|&nbsp;&nbsp;</span>', $filter_words);

        $markup .= '</span></h2>';
    }

    echo $markup;
}

/**
 * Get a group's recent posts and comments, and display them in two widgets
 */
function openlab_show_site_posts_and_comments() {
    global $first_displayed, $bp;

    $group_id = bp_get_group_id();

    $site_type = false;

    if ($site_id = openlab_get_site_id_by_group_id($group_id)) {
        $site_type = 'local';
    } else if ($site_url = openlab_get_external_site_url_by_group_id($group_id)) {
        $site_type = 'external';
    }

    $posts = array();
    $comments = array();

    switch ($site_type) {
        case 'local':
            switch_to_blog($site_id);

            // Set up posts
            $wp_posts = get_posts(array(
                'posts_per_page' => 3
            ));

            foreach ($wp_posts as $wp_post) {
                $_post = array(
                    'title' => $wp_post->post_title,
                    'content' => strip_tags(bp_create_excerpt($wp_post->post_content, 110, array('html' => true))),
                    'permalink' => get_permalink($wp_post->ID)
                );

                if (!empty($wp_post->post_password)) {
                    $_post['content'] = 'This content is password protected.';
                }

                $posts[] = $_post;
            }

            // Set up comments
            $comment_args = array(
                "status" => "approve",
                "number" => "3"
            );

            $wp_comments = get_comments($comment_args);

            foreach ($wp_comments as $wp_comment) {
                // Skip the crummy "Hello World" comment
                if ($wp_comment->comment_ID == "1") {
                    continue;
                }
                $post_id = $wp_comment->comment_post_ID;

                $comments[] = array(
                    'content' => strip_tags(bp_create_excerpt($wp_comment->comment_content, 110, array('html' => false))),
                    'permalink' => get_permalink($post_id)
                );
            }

            $site_url = get_option('siteurl');

            restore_current_blog();

            break;

        case 'external':
            $posts = openlab_get_external_posts_by_group_id();
            $comments = openlab_get_external_comments_by_group_id();

            break;
    }

    // If we have either, show both
    if (!empty($posts) || !empty($comments)) {
        ?>
        <div class="row group-activity-overview">
            <div class="col-sm-12">
                <div id="recent-course">
                    <div class="recent-posts">
                        <h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr($site_url) ?>">Recent Posts<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>


                        <?php foreach ($posts as $post) : ?>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?php echo openlab_get_group_activity_content($post['title'], $post['content'], $post['permalink']) ?>
                                </div>
                            </div>
                        <?php endforeach ?>

                        <?php if ('external' == $site_type && groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id())) : ?>
                            <p class="description">Feed updates automatically every 10 minutes <a class="refresh-feed" id="refresh-posts-feed" href="<?php echo wp_nonce_url(add_query_arg('refresh_feed', 'posts', bp_get_group_permalink(groups_get_current_group())), 'refresh-posts-feed') ?>">Refresh now</a></p>
                        <?php endif ?>
                    </div><!-- .recent-posts -->
                </div><!-- #recent-course -->
            </div><!-- .one-half -->

            <div class="col-sm-12">
                <div id="recent-site-comments">
                    <div class="recent-posts">
                        <h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr($site_url) ?>">Recent Comments<span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
                        <?php if (!empty($comments)) : ?>
                            <?php foreach ($comments as $comment) : ?>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php echo openlab_get_group_activity_content('', $comment['content'], $comment['permalink']) ?>
                                    </div></div>
                            <?php endforeach ?>
                        <?php else : ?>
                            <div class="panel panel-default">
                                <div class="panel-body"><p>No Comments Found</p></div></div>
                        <?php endif ?>

                        <?php if ('external' == $site_type && groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id())) : ?>
                            <p class="refresh-message description">Feed updates automatically every 10 minutes <a class="refresh-feed" id="refresh-posts-feed" href="<?php echo wp_nonce_url(add_query_arg('refresh_feed', 'comments', bp_get_group_permalink(groups_get_current_group())), 'refresh-comments-feed') ?>">Refresh now</a></p>
                        <?php endif ?>

                    </div><!-- .recent-posts -->
                </div><!-- #recent-site-comments -->
            </div><!-- .one-half -->
        </div>
        <?php
    }
}

function openlab_output_course_info_line($group_id) {
    $infoline_mup = '';

    $admins = groups_get_group_admins($group_id);
    $wds_faculty = openlab_get_faculty_list();
    $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
    $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
    $wds_year = groups_get_groupmeta($group_id, 'wds_year');
    $wds_departments = openlab_shortened_text(groups_get_groupmeta($group_id, 'wds_departments'), 15, false);

    $infoline_elems = array();

    if (openlab_not_empty($wds_faculty)) {
        array_push($infoline_elems, $wds_faculty);
    }
    if (openlab_not_empty($wds_departments)) {
        array_push($infoline_elems, $wds_departments);
    }
    if (openlab_not_empty($wds_course_code)) {
        array_push($infoline_elems, $wds_course_code);
    }
    if (openlab_not_empty($wds_semester) || openlab_not_empty($wds_year)) {
        $semester_year = '<span class="bold">' . $wds_semester . ' ' . $wds_year . '</span>';
        array_push($infoline_elems, $semester_year);
    }

    $infoline_mup = implode('|', $infoline_elems);

    return $infoline_mup;
}

/**
 * Displays per group or porftolio site links
 * @global type $bp
 */
function openlab_bp_group_site_pages() {
    global $bp;

    $group_id = bp_get_current_group_id();

    $group_site_settings = openlab_get_group_site_settings($group_id);

    if (!empty($group_site_settings['site_url']) && $group_site_settings['is_visible']) {

        if (openlab_is_portfolio()) {
            ?>

            <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
            <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>

            <div class="sidebar-block">

                <?php
                $account_type = xprofile_get_field_data('Account Type', $displayed_user_id);
                ?>

                <?php if (openlab_is_my_portfolio() || is_super_admin()) : ?>
                    <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                        <li class="portfolio-site-link bold">
                            <a class="bold no-deco" href="<?php echo esc_url($group_site_settings['site_url']) ?>">Visit <?php echo openlab_get_group_type_label('group_id=' . $group_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span></a>
                        </li>

                        <?php if (openlab_user_portfolio_site_is_local($displayed_user_id)) : ?>
                            <li class="portfolio-dashboard-link">
                                <a class="line-height height-200 font-size font-13" href="<?php openlab_user_portfolio_url($displayed_user_id) ?>/wp-admin">Site Dashboard</a>
                            </li>
                        <?php endif ?>
                    </ul>
                <?php else: ?>

                    <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                        <li class="portfolio-site-link">
                            <a class="bold no-deco" href="<?php echo trailingslashit(esc_attr($group_site_settings['site_url'])); ?>">Visit <?php echo openlab_get_group_type_label('group_id=' . $group_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span></a>
                        </li>
                    </ul>

                <?php endif ?>
            </div>
        <?php } else { ?>

            <div class="sidebar-block">
                <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                    <li class="portfolio-site-link">
                        <?php echo '<a class="bold no-deco" href="' . trailingslashit(esc_attr($group_site_settings['site_url'])) . '">Visit ' . ucwords(groups_get_groupmeta(bp_get_group_id(), "wds_group_type")) . ' Site <span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span></a>'; ?>
                    </li>
                    <?php if ($group_site_settings['is_local'] && ($bp->is_item_admin || is_super_admin() || groups_is_user_member(bp_loggedin_user_id(), bp_get_current_group_id()))) : ?>
                        <li class="portfolio-dashboard-link">
                            <?php echo '<a class="line-height height-200 font-size font-13" href="' . esc_attr(trailingslashit($group_site_settings['site_url'])) . 'wp-admin/">Site Dashboard</a>'; ?>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
            <?php
        } // openlab_is_portfolio()
    } // !empty( $group_site_settings['site_url'] )
}

function openlab_get_faculty_list() {
    global $bp;

    $faculty_list = '';

    if (isset($bp->groups->current_group->admins)) {
        $faculty_id = $bp->groups->current_group->admins[0]->user_id;
        $group_id = $bp->groups->current_group->id;

        $faculty_ids = groups_get_groupmeta($group_id, 'additional_faculty', false);
        array_unshift($faculty_ids, $faculty_id);

        $faculty = array();
        foreach ($faculty_ids as $id) {

            array_push($faculty, bp_core_get_user_displayname($id));
        }

        $faculty = array_unique($faculty);

        $faculty_list = implode(', ', $faculty);
    }

    return $faculty_list;
}

function openlab_get_group_site_settings($group_id) {

    // Set up data. Look for local site first. Fall back on external site.
    $site_id = openlab_get_site_id_by_group_id($group_id);

    if ($site_id) {
        $site_url = get_blog_option($site_id, 'siteurl');
        $is_local = true;

        $blog_public = (float) get_blog_option($site_id, 'blog_public');
        switch ($blog_public) {
            case 1 :
            case 0 :
                $is_visible = true;
                break;

            case -1 :
                $is_visible = is_user_logged_in();
                break;

            case -2 :
                $group = groups_get_current_group();
                $is_visible = $group->is_member || current_user_can('bp_moderate');
                break;

            case -3 :
                $caps = get_user_meta(get_current_user_id(), 'wp_' . $site_id . '_capabilities', true);
                $is_visible = isset($caps['administrator']);
                break;
        }
    } else {
        $site_url = groups_get_groupmeta($group_id, 'external_site_url');
        $is_local = false;
        $is_visible = true;
    }

    $group_site_settings = array(
        'site_url' => $site_url,
        'is_local' => $is_local,
        'is_visible' => $is_visible,
    );

    return $group_site_settings;
}

function openlab_custom_group_excerpts($excerpt, $group) {
    global $post, $bp;

    $hits = array('courses', 'projects', 'clubs', 'portfolios', 'my-courses', 'my-projects', 'my-clubs');
    if (in_array($post->post_name, $hits) || $bp->current_action == 'invites') {
        $excerpt = strip_tags($excerpt);
    }

    return $excerpt;
}

add_filter('bp_get_group_description_excerpt', 'openlab_custom_group_excerpts', 10, 2);

/**
 * Disable BuddyPress Cover Images for groups and users.
 */
add_filter('bp_disable_cover_image_uploads', '__return_true');
add_filter('bp_disable_group_cover_image_uploads', '__return_true');

function openlab_get_group_activity_events_feed() {
    $events_out = '';

    // Non-public groups shouldn't show this to non-members.
    $group = groups_get_current_group();
    if ('public' !== $group->status && empty($group->user_has_access)) {
        return $events_out;
    }

    if (!function_exists('eo_get_events')) {
        return $events_out;
    }

    $args = array(
        'event_start_after' => 'today',
        'bp_group' => bp_get_current_group_id(),
        'numberposts' => 5,
    );

    $events = eo_get_events($args);

    $menu_items = openlab_calendar_submenu();

    ob_start();
    include(locate_template('parts/sidebar/activity-events-feed.php'));
    $events_out .= ob_get_clean();

    return $events_out;
}
