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

    ?>
    <div class="panel panel-default">
        <div class="panel-heading semibold"><?php _e('Privacy Settings', 'buddypress'); ?><?php if ($bp->current_action == 'admin' || $bp->current_action == 'create' || openlab_is_portfolio()): ?>: <?php echo $group_type_name_uc ?> Profile<?php endif; ?></div>

        <div class="radio group-profile panel-body">

            <?php if ($bp->current_action == 'create'): ?>
                <p id="privacy-settings-tag-b">These settings affect how others view your <?php echo esc_html( $group_type_name ); ?> Profile. You may change these settings later in the <?php echo esc_html( $group_type_name_uc ); ?> Profile Settings.</p>
            <?php else: ?>
                <p class="privacy-settings-tag-c">These settings affect how others view your <?php echo esc_html( $group_type_name_uc ); ?> Profile.</p>
            <?php endif; ?>

            <?php
            $new_group_status = bp_get_new_group_status();
            if ( ! $new_group_status ) {
                $new_group_status = 'public';
            }
            ?>
            <div class="row">
                <div class="col-sm-24">
                    <label><input type="radio" name="group-status" value="public" <?php checked('public', $new_group_status) ?> />
                        This is a public <?php echo $group_type_name_uc ?></label>
                    <ul>
                        <li>This <?php echo $group_type_name_uc ?> Profile and related content and activity will be visible to the public.</li>
                        <li>This <?php echo esc_html( $group_type_name_uc ); ?> will be listed in the <?php echo esc_html( $group_type_name_uc ); ?> directory, search results, and may be displayed on the OpenLab home page.</li>
                        <li>Any OpenLab member may join this <?php echo esc_html( $group_type_name_uc ); ?>. You can change this in the 'Privacy Settings: Membership' section below.</li>
                    </ul>

                    <label><input type="radio" name="group-status" value="private" <?php checked('private', $new_group_status) ?> />This is a private <?php echo esc_html( $group_type_name_uc ); ?></label>
                    <ul>
                        <li>This <?php echo esc_html( $group_type_name_uc ); ?> Profile, related content and activity will only be visible only to members of the <?php echo esc_html( $group_type_name_uc ); ?>.</li>
                        <li>This <?php echo esc_html( $group_type_name_uc ); ?> will be listed in the <?php echo esc_html( $group_type_name_uc ); ?> directory, search results, and may be displayed on the OpenLab home page.</li>
                        <li>Only OpenLab members who request membership and are accepted may join this <?php echo esc_html( $group_type_name_uc ); ?>. You can disable membership requests in the 'Privacy Settings: Membership' section below.</li>
                    </ul>

                    <label><input type="radio" name="group-status" value="hidden" <?php checked('hidden', $new_group_status) ?> />This is a hidden <?php echo esc_html( $group_type_name_uc ); ?></label>

                    <ul>
                        <li>This <?php echo esc_html( $group_type_name_uc ); ?> Profile, related content and activity will only be visible only to members of the <?php echo esc_html( $group_type_name_uc ); ?>.</li>
                        <li>This <?php echo esc_html( $group_type_name_uc ); ?> Profile will NOT be listed in the <?php echo esc_html( $group_type_name_uc ); ?> directory, search results, or OpenLab home page.</li>
                        <li>Only OpenLab members who are invited may join this <?php echo esc_html( $group_type_name_uc ); ?>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php /* Site privacy markup */ ?>

	<?php
	$site_id          = openlab_get_site_id_by_group_id();
	$selected_privacy = 1;
	if ( $site_id ) {
		$has_site         = true;
		$selected_privacy = null; // Will be determined in openlab_site_privacy_settings_markup().
	} else {
		$clone_steps      = groups_get_groupmeta( bp_get_new_group_id(), 'clone_steps', true );
		$has_site         = in_array( 'site', $clone_steps, true );
		$selected_privacy = 1;
	}
	?>

    <?php if ( $has_site ) : ?>
        <div class="panel panel-default">
            <div class="panel-heading semibold">Privacy Settings: <?php echo esc_html( $group_type_name_uc ); ?> Site</div>
            <div class="panel-body">
                <p class="privacy-settings-tag-c">These settings affect how others view your <?php echo esc_html( $group_type_name_uc ); ?> Site.</p>
                <?php openlab_site_privacy_settings_markup( $site_id, $selected_privacy ) ?>
            </div>
        </div>
    <?php endif ?>

    <?php if ($bp->current_action == 'admin'): ?>
    <?php elseif ($bp->current_action == 'create'): ?>
        <?php wp_nonce_field('groups_create_save_group-settings') ?>
        <?php
    endif;
}

/**
 * Markup for the 'Privacy Settings: Membership' section on group settings.
 */
function openlab_group_privacy_membership_settings() {
	$group_id = bp_get_current_group_id();

	$group_type_label = openlab_get_group_type_label(
		[
			'group_id' => $group_id,
			'case'     => 'upper',
		]
	);

	// We use this group for dynamic hiding/showing of the panel. This is the noscript fallback.
	$status = groups_get_current_group()->status;
	$class  = '';
	if ( 'public' === $status ) {
		$class = 'public-group';
	} elseif ( 'private' === $status ) {
		$class = 'private-group';
	} elseif ( 'hidden' === $status ) {
		$class = 'hidden-group';
	}

	$allow_joining_public  = ! openlab_public_group_has_disabled_joining( $group_id );
	$allow_joining_private = ! openlab_private_group_has_disabled_membership_requests( $group_id );

	?>

	<div class="panel panel-default panel-privacy-membership-settings <?php echo esc_attr( $class ); ?>">
		<div class="panel-heading semibold">Privacy Settings: Membership</div>
		<div class="panel-body">
			<div class="privacy-membership-settings-public">
				<p>By default, a public <?php echo esc_html( $group_type_label ); ?> may be joined by any OpenLab member. Uncheck the box below to remove the 'Join <?php echo esc_html( $group_type_label ); ?>' button from the <?php echo esc_html( $group_type_label ); ?> Profile. When the box is unchecked, [Group] membership will be by invitation only.</p>

				<p><input type="checkbox" id="allow-joining-public" name="allow-joining-public" value="1" <?php checked( $allow_joining_public ); ?> /> <label for="allow-joining-public">Allow any OpenLab member to join this public <?php echo esc_html( $group_type_label ); ?></label></p>
			</div>

			<div class="privacy-membership-settings-private">
				<p>By default, any OpenLab member can request membership in a private <?php echo esc_html( $group_type_label ); ?>. Uncheck the box below to remove the 'Request Membership' button from the <?php echo esc_html( $group_type_label ); ?> Profile. When the box is unchecked, <?php echo esc_html( $group_type_label ); ?> membership will be by invitation only.</p>

				<p><input type="checkbox" id="allow-joining-private" name="allow-joining-private" value="1" <?php checked( $allow_joining_private ); ?> /> <label for="allow-joining-private">Allow any OpenLab member to request membership to this private <?php echo esc_html( $group_type_label ); ?></label></p>
			</div>
		</div>
	</div>

	<?php

	wp_nonce_field( 'openlab_privacy_membership_' . $group_id, 'openlab-privacy-membership-nonce' );
}

/**
 * Save the group privacy membership settings after save.
 *
 * @param BP_Groups_Group $group
 */
function openlab_group_privacy_membership_save( $group ) {
    if ( empty( $_POST['openlab-privacy-membership-nonce'] ) ) {
        return;
    }

    check_admin_referer( 'openlab_privacy_membership_' . $group->id, 'openlab-privacy-membership-nonce' );

	switch ( $group->status ) {
		case 'public' :
			if ( empty( $_POST['allow-joining-public'] ) ) {
				groups_update_groupmeta( $group->id, 'disable_public_group_joining', 1 );
			} else {
				groups_delete_groupmeta( $group->id, 'disable_public_group_joining' );
			}
			break;

		case 'private' :
			if ( empty( $_POST['allow-joining-private'] ) ) {
				groups_update_groupmeta( $group->id, 'disable_private_group_membership_requests', 1 );
			} else {
				groups_delete_groupmeta( $group->id, 'disable_private_group_membership_requests' );
			}
			break;
	}
}
add_action( 'groups_group_after_save', 'openlab_group_privacy_membership_save' );

/**
 * Determines whether public joining is disabled for a public group.
 *
 * To avoid bloat in the database, we're only recording when the default behavior is
 * changed. For this reason, it will return false for non-public groups as well.
 *
 * @param int $group_id
 * @return bool
 */
function openlab_public_group_has_disabled_joining( $group_id ) {
	return ! empty( groups_get_groupmeta( $group_id, 'disable_public_group_joining', true ) );
}

/**
 * Determines whether membership requesting is disabled for a private group.
 *
 * To avoid bloat in the database, we're only recording when the default behavior is
 * changed. For this reason, it will return false for non-private groups as well.
 *
 * @param int $group_id
 * @return bool
 */
function openlab_private_group_has_disabled_membership_requests( $group_id ) {
	return ! empty( groups_get_groupmeta( $group_id, 'disable_private_group_membership_requests', true ) );
}

/**
 * Unhooks group join button if it's disabled for the group.
 */
add_action(
	'bp_screens',
	function() {
		if ( ! bp_is_group() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$group = groups_get_current_group();

		if ( groups_is_user_member( bp_loggedin_user_id(), $group->id ) ) {
			return;
		}

		switch ( $group->status ) {
			case 'public' :
				if ( openlab_public_group_has_disabled_joining( $group->id ) ) {
					remove_action( 'bp_group_header_actions', 'bp_group_join_button', 5 );
				}
				break;

			case 'private' :
				if ( openlab_private_group_has_disabled_membership_requests( $group->id ) ) {
					remove_action( 'bp_group_header_actions', 'bp_group_join_button', 5 );

					if ( bp_is_current_action( 'request-membership' ) ) {
						bp_core_redirect( bp_get_group_permalink( $group ) );
						die;
					}
				}
				break;
		}
	}
);

function openlab_groups_pagination_links() {
    global $groups_template;
    $search_terms = '';

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
function openlab_return_course_list( $school, $department ) {
	$list = '<option value="dept_all" ' . selected( '', $department ) . ' >All Departments</option>';

	// Sanitize. If no value is found, don't return any
	// courses
	$departments = openlab_get_department_list();
	if ( ! array_key_exists( $school, $departments ) ) {
		return $list;
	}

	$depts = openlab_get_department_list( $school, 'short' );
	foreach ( $depts as $dept_name => $dept_label ) {
		$list .= '<option value="' . esc_attr( $dept_name ) . '" ' . selected( $department, $dept_name, false ) . '>' . esc_attr( $dept_label ) . '</option>';
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
function cuny_groups_pagination_count($group_name = '') {
    global $bp, $groups_template;

    $start_num = intval(( $groups_template->pag_page - 1 ) * $groups_template->pag_num) + 1;
    $from_num = bp_core_number_format($start_num);
    $to_num = bp_core_number_format(( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ));
    $total = bp_core_number_format($groups_template->total_group_count);

    echo sprintf(__('%1$s to %2$s (of %3$s)', 'buddypress'), $from_num, $to_num, $total);
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
function openlab_site_privacy_settings_markup( $site_id = 0, $selected_privacy = null ) {
	if ( ! $site_id ) {
		$site_id     = get_current_blog_id();
		$blog_public = $selected_privacy;
	} else {
		$blog_public = get_blog_option( $site_id, 'blog_public' );
	}

	$group_type = openlab_get_current_group_type( 'case=upper' );

    ?>

    <div class="radio group-site">

        <h5>Public</h5>
        <div class="row">
            <div class="col-sm-24">
                <label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked( '1', $blog_public ); ?> />Allow search engines to index this site. Your site will show up in web search results.</label>

                <label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked( '0', $blog_public ); ?> />Ask search engines not to index this site. Your site should not show up in web search results.</label>
                <p id="search-setting-note" class="privacy-settings-note italics note">Note: This option will NOT block access to your site. It is up to search engines to honor your request.</p>
            </div>
        </div>

        <?php if (!openlab_is_portfolio() && (!isset($_GET['type']) || 'portfolio' != $_GET['type'] )): ?>

            <h5>Private</h5>
            <div class="row">
                <div class="col-sm-24">
                    <label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '-1', $blog_public ); ?>>I would like my site to be visible only to registered users of City Tech OpenLab.</label>

                    <label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public); ?>>I would like my site to be visible to registered users of this <?php echo esc_html( ucfirst( $group_type ) ); ?>.</label>
                </div>
            </div>

            <h5>Hidden</h5>
            <div class="row">
                <div class="col-sm-24">
                    <label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked( '-3', $blog_public ); ?>>I would like my site to be visible only to site administrators.</label>
                </div>
            </div>

        <?php else : ?>

            <?php /* Portfolios */ ?>
            <h5>Private</h5>
            <div class="row">
                <div class="col-sm-24">
                    <label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '-1', $blog_public ); ?>>I would like my site to be visible only to registered users of City Tech OpenLab.</label>

                    <label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked( '-2', $blog_public ); ?>>I would like my site to be visible only to registered users that I have granted access.</label>
                    <p id="private-portfolio-note" class="privacy-settings-note italics note">Note: If you would like non-City Tech users to view your private site, you will need to make your site public.</p>

                    <label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked( '-3', $blog_public ); ?>>I would like my site to be visible only to me.</label>
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

	if ( 'portfolio' === $group_type ) {
		$show_acknowledgements = false;
	} else {
		$credits = openlab_get_credits( $group_id );

		$show_acknowledgements = $credits['show_acknowledgements'];
		$credits_chunks        = $credits['credits_chunks'];
		$post_credits_markup   = $credits['post_credits_markup'];
	}

    ?>

	<div class="wrapper-block visible-xs sidebar mobile-group-site-links">
		<?php openlab_bp_group_site_pages( true ); ?>
	</div>

    <?php if (bp_is_group_home()): ?>
        <div id="<?php echo esc_attr( $group_type ); ?>-header" class="group-header row">

            <div id="<?php echo esc_attr( $group_type ); ?>-header-avatar" class="alignleft group-header-avatar col-sm-8 col-xs-12">
                <div class="padded-img darker">
                    <img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo esc_attr($group_name); ?>"/>

					<?php openlab_group_single_badges(); ?>

					<?php do_action( 'bp_group_header_after_avatar' ); ?>
                </div>

                <?php if (is_user_logged_in() && $bp->is_item_admin): ?>
                    <div id="group-action-wrapper">
                        <a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo bp_group_permalink() . 'admin/edit-details/'; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Profile</a>
                        <a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo bp_group_permalink() . 'admin/group-avatar/'; ?>"><i class="fa fa-camera" aria-hidden="true"></i> Change Avatar</a>
                        <?php do_action( 'bp_group_header_actions' ); ?>
                    </div>
                <?php elseif (is_user_logged_in()) : ?>
                    <div id="group-action-wrapper">
                        <?php do_action('bp_group_header_actions'); ?>
                    </div>
                <?php endif; ?>
                <?php openlab_render_message(); ?>
            </div><!-- #<?php echo esc_html( $group_type ); ?>-header-avatar -->

            <div id="<?php echo esc_attr( $group_type ); ?>-header-content" class="col-sm-16 col-xs-24 alignleft group-header-content group-<?php echo esc_attr( $group_id ); ?>">

                <?php do_action('bp_before_group_header_meta') ?>

                <?php if ($group_type == "course"): ?>
                    <div class="info-panel panel panel-default no-margin no-margin-top">
                        <?php
                        $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
                        $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
                        $wds_year = groups_get_groupmeta($group_id, 'wds_year');

                        $group_units        = openlab_get_group_academic_units( $group_id );
                        $departments_string = openlab_generate_department_name( $group_units );
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
                                <div class="col-sm-17 row-content"><?php echo esc_html( $departments_string ); ?></div>
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

							<?php if ( openlab_group_can_be_cloned( bp_get_current_group_id() ) ) : ?>
								<div class="table-row row">
                                    <div class="col-xs-24 status-message italics">
										This <?php echo esc_html( $group_type ); ?> may be cloned by logged-in faculty.

										<?php
										$exclude_hidden   = ! current_user_can( 'bp_moderate' );
										$descendant_count = openlab_get_clone_descendant_count_of_group( $group_id, $exclude_hidden );
										?>

										<?php if ( $descendant_count > 0 ) : ?>
											<?php
											$view_clones_link = trailingslashit( home_url( $group_type . 's' ) );
											$view_clones_link = add_query_arg( 'descendant-of', $group_id, $view_clones_link );
											$count_message    = _n( 'It has been cloned or re-cloned %s time', 'It has been cloned or re-cloned %s times', $descendant_count, 'commons-in-a-box' );
											?>
											<?php echo esc_html( sprintf( $count_message, number_format_i18n( $descendant_count ) ) ); ?>; <a href="<?php echo esc_attr( $view_clones_link ); ?>">view clones</a>.
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

                            <?php if ( $show_acknowledgements ) : ?>
                                <div class="table-row row">
                                    <div class="col-xs-24 status-message clone-acknowledgements">
										<?php foreach ( $credits_chunks as $credits_chunk ) : ?>
											<?php if ( ! empty( $credits_chunk['intro'] ) ) : ?>
												<p><?php echo $credits_chunk['intro']; ?></p>
											<?php endif; ?>

											<?php if ( ! empty( $credits_chunk['items'] ) ) : ?>
												<ul class="group-credits">
													<?php echo $credits_chunk['items']; ?>
												</ul>
											<?php endif; ?>
										<?php endforeach; ?>

										<?php if ( ! empty( $post_credits_markup ) ) : ?>
											<?php echo $post_credits_markup; ?>
										<?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

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
                            $group_units     = openlab_get_group_academic_units( $group_id );
                            $wds_school      = openlab_generate_school_office_name( $group_units );
                            $wds_departments = openlab_generate_department_name( $group_units );
                            $group_contacts  = openlab_get_group_contacts( $group_id );

                            // Show 'School' field for Projects, Clubs, or staff Portfolios.
                            $show_school = 'project' === $group_type || 'club' === $group_type;
                            if ( 'portfolio' === $group_type ) {
                                $user_id   = openlab_get_user_id_from_portfolio_group_id( $group_id );
                                $user_type = xprofile_get_field_data( 'Account Type', $user_id );

                                $show_school = 'Staff' === $user_type;
                            }
                            ?>

                            <?php if ( $show_school && $wds_school && ! empty( $wds_school ) ): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">School / Office</div>
                                    <div class="col-sm-17 row-content"><?php echo $wds_school; ?></div>
                                </div>

                            <?php endif; ?>

                            <?php if ($wds_departments && !empty($wds_departments)): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">Department</div>
                                    <div class="col-sm-17 row-content"><?php echo esc_html( $wds_departments ); ?></div>
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
                                <div class="bold col-sm-7"><?php echo esc_html( ucfirst( $group_type ) ); ?> Description</div>
                                <div class="col-sm-17 row-content"><?php bp_group_description() ?></div>
                            </div>

                            <?php if ( $group_contacts ): ?>
                                <div class="table-row row">
                                    <?php /* This won't work at all for l10n */ ?>
                                    <?php
                                    if ( 1 === count( $group_contacts ) ) {
                                        $gc_label = sprintf( '%s Contact', ucwords( $group_type ) );
                                    } else {
                                        $gc_label = sprintf( '%s Contacts', ucwords( $group_type ) );
                                    }
                                    ?>
                                    <div class="bold col-sm-7"><?php echo esc_html( $gc_label ); ?></div>
                                    <div class="col-sm-17 row-content"><?php echo implode( ', ', array_map( 'bp_core_get_userlink', $group_contacts ) ); ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($group_type == "portfolio"): ?>

                                <div class="table-row row">
                                    <div class="bold col-sm-7">Member Profile</div>
                                    <div class="col-sm-17 row-content"><?php echo bp_core_get_userlink(openlab_get_user_id_from_portfolio_group_id(bp_get_group_id())); ?></div>
                                </div>

							<?php endif; ?>

							<?php if ( openlab_group_can_be_cloned( bp_get_current_group_id() ) ) : ?>
								<div class="table-row row">
									<div class="col-xs-24 status-message italics">
										This <?php echo esc_html( $group_type ); ?> may be cloned by logged-in OpenLab members.

										<?php
										$exclude_hidden   = ! current_user_can( 'bp_moderate' );
										$descendant_count = openlab_get_clone_descendant_count_of_group( $group_id, $exclude_hidden );
										?>

										<?php if ( $descendant_count > 0 ) : ?>
											<?php
											$view_clones_link = trailingslashit( home_url( $group_type . 's' ) );
											$view_clones_link = add_query_arg( 'descendant-of', $group_id, $view_clones_link );
											$count_message    = _n( 'It has been cloned or re-cloned %s time', 'It has been cloned or re-cloned %s times', $descendant_count, 'commons-in-a-box' );
											?>
											<?php echo esc_html( sprintf( $count_message, number_format_i18n( $descendant_count ) ) ); ?>; <a href="<?php echo esc_attr( $view_clones_link ); ?>">view clones</a>.
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

                            <?php if ( $show_acknowledgements ) : ?>
                                <div class="table-row row">
                                    <div class="col-xs-24 status-message clone-acknowledgements">
										<?php foreach ( $credits_chunks as $credits_chunk ) : ?>
											<?php if ( ! empty( $credits_chunk['intro'] ) ) : ?>
												<p><?php echo $credits_chunk['intro']; ?></p>
											<?php endif; ?>

											<?php if ( ! empty( $credits_chunk['items'] ) ) : ?>
												<ul class="group-credits">
													<?php echo $credits_chunk['items']; ?>
												</ul>
											<?php endif; ?>
										<?php endforeach; ?>

										<?php if ( ! empty( $post_credits_markup ) ) : ?>
											<?php echo $post_credits_markup; ?>
										<?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endif; ?>
            </div><!-- .header-content -->

            <?php do_action('bp_after_group_header') ?>

		</div><!--<?php echo esc_html( $group_type ); ?>-header -->

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

                <?php if ( $group_type != "portfolio" ): ?>
                    <div class="row group-activity-overview">
						<?php if ( openlab_is_forum_enabled_for_group( $group->id ) ) : ?>
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
						<?php endif; // Recent Discussions ?>

                        <?php $first_class = ""; ?>
						<?php if ( openlab_is_docs_enabled_for_group( $group->id ) ) : ?>
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
						<?php endif; // Recent Docs ?>
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
	$stored_setting = ass_get_default_subscription();

	$group_type_label = openlab_get_group_type_label( [ 'case' => 'upper' ] );

    ?>
    <div class="panel panel-default">
        <div class="panel-heading">Email Subscription Defaults</div>

        <div class="panel-body">
            <p>When new users join this <?php echo esc_html( $group_type_label ); ?>, their default email notification settings will be:</p>
            <div class="radio email-sub">
                <label><input type="radio" name="ass-default-subscription" value="supersub" <?php ass_default_subscription_settings( 'supersub' ) ?> <?php checked( 'supersub', $stored_setting ); ?> /> All Email <span class="bpges-settings-gloss">(Receive email about this <?php echo esc_html( $group_type_label ); ?>'s activity as it happens.)</span></label>
                <label><input type="radio" name="ass-default-subscription" value="dig" <?php ass_default_subscription_settings( 'dig' ) ?> <?php checked( 'dig', $stored_setting ); ?> /> Daily Digest <span class="bpges-settings-gloss">(This <?php echo esc_html( $group_type_label ); ?>'s activity will be bundled in a daily email with other groups set to daily digest.)</span></label>
                <label><input type="radio" name="ass-default-subscription" value="sum" <?php ass_default_subscription_settings( 'sum' ); ?> <?php checked( 'sum', $stored_setting ); ?> /> Weekly Digest <span class="bpges-settings-gloss">(This <?php echo esc_html( $group_type_label ); ?>'s activity will be bundled in a weekly email with other groups set to weekly digest.)</span></label>
                <label><input type="radio" name="ass-default-subscription" value="no" <?php ass_default_subscription_settings( 'no' ) ?> <?php checked( 'no', $stored_setting ); ?> /> No Email <span class="bpges-settings-gloss">(Opt out of all email related to this <?php echo esc_html( $group_type_label ); ?>'s activity.)</span></label>
			</div>
        </div>
    </div>
    <?php
}

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
 * Gets the group type for the current directory.
 *
 * @return string
 */
function openlab_get_group_directory_group_type() {
	$post_obj   = get_queried_object();
	$group_type = openlab_page_slug_to_grouptype();

	return $group_type;
}

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

            foreach (openlab_get_office_list() as $office_key => $office_label) {
                $filter_array['options'][$office_key] = $office_label;
            }

            break;

        case 'department' :
            $filter_array['label'] = 'Department';
            $filter_array['options'] = array(
                'dept_all' => 'All'
            );

            foreach ( openlab_get_entity_departments() as $entity => $depts ) {
                foreach ( $depts as $dept_key => $dept_labels ) {
                    $filter_array['options'][ $dept_key ] = $dept_labels['short_label'] ?? $dept_labels['label'];
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

		case 'group_badge' :
			$filter_array['label']   = 'Type';
			$filter_array['options'] = array();

			if ( class_exists( '\OpenLab\Badges\Badge' ) ) {
				$badges = OpenLab\Badges\Badge::get();
				foreach ( $badges as $badge ) {
					$filter_array['options'][ (string) $badge->get_id() ] = $badge->get_short_name();
				}

				$filter_array['options']['cloneable'] = 'Cloneable';
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
            $filters = array('school', 'office', 'department', 'usertype');
            break;

        case 'course' :
            $filters = array('school', 'department', 'semester', 'group_badge');
            break;

        case 'club' :
        case 'project' :
            $filters = array('school', 'office', 'department', 'cat', 'semester');
            break;

        case 'people' :
            $filters = array('usertype', 'school', 'office', 'department');
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

            if ( 'usertype' === $ftype && ! openlab_user_type_is_valid( $fvalue ) ) {
                continue;
            }

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
                $filter_words[] = '<span>' . esc_html( $word ) . '</span>';
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

    add_filter( 'to/get_terms_orderby/ignore', '__return_true' );
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
			$comment_args = [
				'status'     => 'approve',
				'number'     => 3,
				'meta_query' => [
					'relation' => 'AND',
					[
						'relation' => 'OR',
						[
							'key'   => 'olgc_is_private',
							'value' => '0',
						],
						[
							'key' => 'olgc_is_private',
							'compare' => 'NOT EXISTS',
						],
					],
					[
						'relation' => 'OR',
						[
							'key'   => 'ol_is_private',
							'value' => '0',
						],
						[
							'key' => 'ol_is_private',
							'compare' => 'NOT EXISTS',
						],
					],
				],
			];

			// This isn't official argument just a custom flag.
			// Used by `openlab_private_comments_fallback()`.
			$comment_args['main_site'] = true;

			$wp_comments = get_comments( $comment_args );

            foreach ($wp_comments as $wp_comment) {
                // Skip the crummy "Hello World" comment
                if ($wp_comment->comment_ID == "1") {
                    continue;
                }

				// Filter out comments that have empty content.
				if ( empty( trim( $wp_comment->comment_content ) ) ) {
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
    remove_filter( 'to/get_terms_orderby/ignore', '__return_true' );

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

    $wds_course_code = groups_get_groupmeta($group_id, 'wds_course_code');
    $wds_semester = groups_get_groupmeta($group_id, 'wds_semester');
    $wds_year = groups_get_groupmeta($group_id, 'wds_year');
    $wds_departments = openlab_shortened_text(groups_get_groupmeta($group_id, 'wds_departments'), 15, false);

    $infoline_elems = array();
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
 * Generates the 'faculty' line that appears under group names in course directories.
 *
 * No longer used. See openlab_output_group_contact_line().
 *
 * @param int $group_id ID of the group.
 * @return string
 */
function openlab_output_course_faculty_line( $group_id ) {
	// The world's laziest technique.
	$list = strip_tags( openlab_get_faculty_list( $group_id ) );

	return '<span class="truncate-on-the-fly" data-basevalue="35">' . $list . '</span>';
}

/**
 * Generates the 'contact' line that appears under group names in directories.
 *
 * @param int $group_id ID of the group.
 * @return string
 */
function openlab_output_group_contact_line( $group_id ) {
	$names = array_map(
		function( $user_id ) {
			return bp_core_get_user_displayname( $user_id );
		},
		openlab_get_all_group_contact_ids( $group_id )
	);

	$list = implode( ', ', $names );

	return '<span class="truncate-on-the-fly" data-basevalue="35">' . esc_html( $list ) . '</span>';
}

/**
 * Displays per group or porftolio site links
 * @global type $bp
 */
function openlab_bp_group_site_pages( $mobile = false ) {
    global $bp;

    $group_id = bp_get_current_group_id();

    $group_site_settings = openlab_get_group_site_settings($group_id);

	$responsive_class = $mobile ? 'visible-xs' : 'hidden-xs';

    if (!empty($group_site_settings['site_url']) && $group_site_settings['is_visible']) {

        if (openlab_is_portfolio()) {
            ?>

            <?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
            <?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>

            <div class="sidebar-block group-site-links <?php echo esc_html( $responsive_class ); ?>">

                <?php if (openlab_is_my_portfolio() || is_super_admin()) : ?>
                    <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                        <li class="portfolio-site-link bold">
                            <a class="bold no-deco" href="<?php echo esc_url($group_site_settings['site_url']) ?>">Visit <?php echo openlab_get_group_type_label('group_id=' . $group_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>
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
                            <a class="bold no-deco" href="<?php echo trailingslashit(esc_attr($group_site_settings['site_url'])); ?>">Visit <?php echo openlab_get_group_type_label('group_id=' . $group_id . '&case=upper'); ?> Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>
                        </li>
                    </ul>

                <?php endif ?>
            </div>
        <?php } else { ?>

            <div class="sidebar-block group-site-links <?php echo esc_html( $responsive_class ); ?>">
                <ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
                    <li class="portfolio-site-link">
                        <?php echo '<a class="bold no-deco" href="' . trailingslashit(esc_attr($group_site_settings['site_url'])) . '">Visit ' . ucwords(groups_get_groupmeta(bp_get_group_id(), "wds_group_type")) . ' Site <span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>'; ?>
                    </li>
                    <?php if ( $group_site_settings['is_local'] && ( $bp->is_item_admin || is_super_admin() || ( groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() ) && current_user_can_for_blog( $group_site_settings['site_id'], 'edit_posts' ) ) ) ) : ?>
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

function openlab_get_faculty_list( $group_id = null ) {
    global $bp;

    $faculty_list = '';

	if ( null === $group_id ) {
		$group_id = bp_get_group_id();
	}

	if ( ! $group_id ) {
		return '';
	}

	$group = groups_get_group( $group_id );

	$faculty_ids = array_merge( openlab_get_primary_faculty( $group_id ), openlab_get_additional_faculty( $group_id ) );

	$faculty = array();
	foreach ( $faculty_ids as $id ) {
		$link = sprintf( '<a href="%s">%s</a>', bp_core_get_user_domain( $id ), bp_core_get_user_displayname( $id ) );

		array_push( $faculty, $link );
	}

	$faculty = array_unique( $faculty );

	$faculty_list = implode( ', ', $faculty );

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
        'site_id' => $site_id,
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

	// Don't show on portfolios.
	if ( openlab_is_portfolio() ) {
		return $events_out;
	}

	if ( ! openlab_is_calendar_enabled_for_group() ) {
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
add_action( 'bp_group_options_nav', function() {
	echo openlab_get_group_activity_events_feed();
}, 50 );

/**
 * Group membership request link.
 */
function openlab_group_request_user_link() {
	global $requests_template;

	$user_id   = $requests_template->request->user_id;
	$user_url  = bp_core_get_user_domain( $user_id );
	$user_name = bp_core_get_user_displayname( $user_id );

	return sprintf(
		'<a href="%s" class="truncate-on-the-fly" data-basevalue="20" data-minvalue="10" data-basewidth="152">%s</a>',
		esc_url( $user_url ),
		esc_html( $user_name )
	);
}

/**
 * Adds a Badges link under group avatars on single group headers.
 */
function openlab_add_badge_button_to_profile() {
	if ( ! current_user_can( 'grant_badges' ) ) {
		return;
	}

	$group_id = bp_get_current_group_id();

	$badge_link = bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-settings/#panel-badges';

	?>
	<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo esc_attr( $badge_link ); ?>"><i class="fa fa-certificate" aria-hidden="true"></i> Manage Badges</a>
	<?php
}
add_action( 'bp_group_header_actions', 'openlab_add_badge_button_to_profile', 60 );

add_action( 'bp_after_group_details_creation_step', function() {
	$group_type = ! empty( $_GET['type'] ) ? $_GET['type'] : null;

	if ( 'portfolio' === $group_type ) {
		return;
	}

	openlab_group_sharing_settings_markup( $group_type );
}, 4 );

/**
 * Save the group S/O/D settings after save.
 *
 * @param BP_Groups_Group $group
 */
function openlab_group_academic_unit_save( $group ) {
    if ( empty( $_POST['openlab-academic-unit-selector-nonce'] ) ) {
        return;
    }

    check_admin_referer( 'openlab_academic_unit_selector', 'openlab-academic-unit-selector-nonce' );

    $to_save = [];
    foreach ( [ 'schools', 'offices', 'departments' ] as $unit_type ) {
        $to_save[ $unit_type ] = isset( $_POST[ $unit_type ] ) ? $_POST[ $unit_type ] : array();
    }

    openlab_set_group_academic_units( $group->id, $to_save );
}
add_action( 'groups_group_after_save', 'openlab_group_academic_unit_save' );

/**
 * Gets S/O/D data for a group.
 *
 * @param int $group_id
 */
function openlab_get_group_academic_units( $group_id ) {
    $values = array();
    $map    = array(
        'schools'     => 'openlab_school',
        'offices'     => 'openlab_office',
        'departments' => 'openlab_department',
    );

    foreach ( $map as $type_key => $meta_key ) {
        $units_of_type = groups_get_groupmeta( $group_id, $meta_key, false );
        if ( ! $units_of_type ) {
            $units_of_type = array();
        }
        $values[ $type_key ] = array_unique( $units_of_type );
    }

    return $values;
}

/**
 * Sets academic units for a group.
 *
 * @param int   $group_id
 * @param array $units
 */
function openlab_set_group_academic_units( $group_id, $units ) {
    $map = array(
        'schools'     => 'openlab_school',
        'offices'     => 'openlab_office',
        'departments' => 'openlab_department',
    );

    foreach ( $map as $data_key => $meta_key ) {
        $existing = groups_get_groupmeta( $group_id, $meta_key, false );
        $to_save  = $units[ $data_key ] ?: array();

        $to_delete = array_diff( $existing, $to_save );
        $to_add    = array_diff( $to_save, $existing );

        foreach ( $to_delete as $to_delete_value ) {
            groups_delete_groupmeta( $group_id, $meta_key, $to_delete_value );
        }

        foreach ( $to_add as $to_add_value ) {
            groups_add_groupmeta( $group_id, $meta_key, $to_add_value );
        }
    }
}

/**
 * Save "Add to Portfolio" group settings.
 *
 * @param object $group
 * @return void
 */
function openlab_group_add_to_portfolio_save( $group ) {
	if ( empty( $_POST['add-to-portfolio-toggle-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'add_to_portfolio_toggle', 'add-to-portfolio-toggle-nonce' );

	if ( ! empty( $_POST['portfolio-sharing'] ) ) {
		groups_add_groupmeta( $group->id, 'enable_portfolio_sharing', 'yes' );
	} else {
		groups_delete_groupmeta( $group->id, 'enable_portfolio_sharing' );
	}
}
add_action( 'groups_group_after_save', 'openlab_group_add_to_portfolio_save' );

/**
 * Outputs the badge markup for the group directory.
 *
 * @since 1.2.0
 */
function openlab_group_directory_badges() {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return;
	}

	$group_id = bp_get_group_id();

	$is_cloneable = openlab_group_can_be_cloned( $group_id );
	$is_open      = openlab_group_is_open( $group_id );
	$has_badges   = openlab_group_has_badges( $group_id );

	if ( ! $is_cloneable && ! $is_open && ! $has_badges ) {
		return;
	}

	echo '<div class="col-xs-18 alignright group-directory-badges">';
	\OpenLab\Badges\Template::badge_links( 'directory' );
	echo '</div>';
}
add_action( 'openlab_theme_after_group_group_directory', 'openlab_group_directory_badges' );

/**
 * Checks whether a group has badges.
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_group_has_badges( $group_id ) {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return false;
	}

	$badge_group  = new \OpenLab\Badges\Group( $group_id );
	$group_badges = $badge_group->get_badges();

	return ! empty( $group_badges );
}

/**
 * Outputs the badge markup for single group pages.
 *
 * @since 1.2.0
 */
function openlab_group_single_badges() {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return;
	}

	echo '<div class="group-single-badges">';
	\OpenLab\Badges\Template::badge_links( 'single' );
	echo '</div>';
}

/**
 * Filters the badge link markup to dynamically inject our badge-like flags.
 *
 * @param array  $badge_links Array of badge flags.
 * @param int    $group_id    ID of the group.
 * @param string $context     Context. 'directory' or 'single'.
 * @return array
 */
function openlab_filter_badge_links( $badge_links, $group_id, $context ) {
	// Note that they're applied in reverse order, so 'open' is first.
	$faux_badges = [
		'cloneable' => [
			'add'        => openlab_group_can_be_cloned( $group_id ),
			'link'       => 'https://openlab.citytech.cuny.edu/blog/help/types-of-courses-projects-and-clubs',
			'name'       => 'Cloneable',
			'short_name' => 'Cloneable',
		],
		'open'      => [
			'add'        => openlab_group_is_open( $group_id ),
			'link'       => 'https://openlab.citytech.cuny.edu/blog/help/types-of-courses-projects-and-clubs',
			'name'       => 'Open',
			'short_name' => 'Open',
		],
	];

	foreach ( $faux_badges as $badge_type => $faux_badge ) {
		if ( ! $faux_badge['add'] ) {
			continue;
		}

		// Copied from \OpenLab\Badges\Badge::get_avatar_flag_html().
		$group = groups_get_group( $group_id );

		$tooltip_id = 'badge-tooltip-' . $group->slug . '-' . $badge_type;

		$badge_link_start = '';
		$badge_link_end   = '';

		if ( 'single' === $context ) {
			$badge_link_start = sprintf(
				'<a class="group-badge-shortname" href="%s">',
				esc_attr( $faux_badge['link'] )
			);

			$badge_link_end = '</a>';
		} else {
			$badge_link_start = '<span class="group-badge-shortname">';
			$badge_link_end   = '</span>';
		}

		$html  = '<div class="group-badge">';
		$html .= $badge_link_start;
		$html .= esc_html( $faux_badge['short_name'] );
		$html .= $badge_link_end;

		$html .= '<div id="' . esc_attr( $tooltip_id ) . '" class="badge-tooltip" role="tooltip">';
		$html .= esc_html( $faux_badge['name'] );
		$html .= '</div>';
		$html .= '</div>';

		array_unshift( $badge_links, $html );
	}

	return $badge_links;
}
add_filter( 'openlab_badges_badge_links', 'openlab_filter_badge_links', 10, 3 );

/**
 * Filters the list of group types used by OpenLab Badges.
 */
add_filter(
	'openlab_badges_get_group_types',
	function() {
		$retval = [];

		foreach ( openlab_group_types() as $type ) {
			if ( 'school' === $type ) {
				continue;
			}

			$retval[] = [
				'slug' => $type,
				'name' => ucwords( $type ),
			];
		}

		return $retval;
	}
);

/**
 * Filters the group type of a group, as used by OpenLab Badges.
 */
add_filter(
	'openlab_badges_group_type',
	function( $group_type, $group_id ) {
		return openlab_get_group_type( $group_id );
	},
	10,
	2
);

/**
 * Checks whether a group is "open".
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_group_is_open( $group_id ) {
	$group = groups_get_group( $group_id );

	$is_open = false;
	if ( 'public' === $group->status ) {
		$site_id = openlab_get_site_id_by_group_id( $group_id );
		if ( $site_id ) {
			// Avoid switch_to_blog().
			$blog_public = groups_get_groupmeta( $group_id, 'blog_public', true );
			$is_open     = '0' === $blog_public || '1' === $blog_public;
		} else {
			$is_open = true;
		}
	}

	return $is_open;
}

/**
 * 'is_open' polyfill for the fact that 'status' is not implemented in bp_has_groups().
 *
 * See https://buddypress.trac.wordpress.org/ticket/8310
 */
add_filter(
	'bp_before_groups_get_groups_parse_args',
	function( $args ) {
		$is_open = openlab_get_current_filter( 'is_open' );
		if ( $is_open ) {
			$args['status'] = 'public';
		}
		return $args;
	}
);

/**
 * Gets a list of directory filter fields for each group type.
 */
function openlab_group_type_disabled_filters() {
	$disabled = [
		'course'    => [
			'bp-group-categories-select',
			'portfolio-user-member-type-select',
		],
		'project'   => [
			'course-term-select',
			'portfolio-user-member-type-select',
		],
		'club'      => [
			'course-term-select',
			'portfolio-user-member-type-select',
		],
		'portfolio' => [
			'bp-group-categories-select',
			'checkbox-is-cloneable',
			'course-term-select',
		],
	];

	if ( defined( 'OLBADGES_VERSION' ) ) {
		$all_badges = \OpenLab\Badges\Badge::get();
		foreach ( $all_badges as $badge ) {
			foreach ( $disabled as $group_type => &$type_disabled ) {
				if ( ! in_array( $group_type, $badge->get_group_types(), true ) ) {
					$type_disabled[] = 'checkbox-badge-' . $badge->get_id();
				}
			}
		}
	}

	return $disabled;
}
