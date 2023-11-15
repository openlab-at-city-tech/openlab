<?php
/**
 * Group single page
 *
 */
if (bp_has_groups()) : while (bp_groups()) : bp_the_group();
        ?>

        <?php do_action('bp_before_group_home_content'); ?>
        <div class="col-sm-18 col-xs-24 groups-single-home" role="main">
            <div id="openlab-main-content" class="content-wrapper">

                <?php
                do_action('bp_before_group_body');

                /**
                 * Does this next bit look familiar? If not, go check out WordPress's
                 * /wp-includes/template-loader.php file.
                 *
                 * @todo A real template hierarchy? Gasp!
                 */
                // Group is visible
                if (bp_get_group_status() == 'public' || bp_get_group_status() == 'private' || (bp_get_group_status() == 'hidden' && (bp_is_item_admin() || bp_group_is_member()))) :

                    // Looking at home location
                    if (bp_is_group_home()) :

                        // Use custom front if one exists
                        $custom_front = bp_locate_template(array('groups/single/front.php'), false, true);

                        if (!empty($custom_front)) : load_template($custom_front, true);

                        // Default to activity
                        elseif (bp_is_active('activity')) : cuny_group_single();

                        // Otherwise show members
                        elseif (bp_is_active('members')) : bp_get_template_part('groups/single/members');

                        endif;

                    // Not looking at home
                    else :

                        // Group Admin
                        if (bp_is_group_admin_page()) : bp_get_template_part('groups/single/admin');

                        // Group Activity
                        elseif (bp_is_group_activity()) : bp_get_template_part('groups/single/activity');

                        // Group Members
                        elseif (bp_is_group_members()) : bp_get_template_part('groups/single/members');

                        // Group Invitations
                        elseif (bp_is_group_invites()) : bp_get_template_part('groups/single/send-invites');

                        // Membership request
                        elseif (bp_is_group_membership_request()) : bp_get_template_part('groups/single/request-membership');

                        // Email subscription options
                        elseif (bp_current_action() == 'notifications') : bp_get_template_part('groups/single/notifications');

                        // Anything else (plugins mostly)
                        else : bp_get_template_part('groups/single/plugins');

                        endif;
                    endif;

                // Group is not visible
                elseif (!bp_group_is_visible()) :

                    // Membership request
                    if (bp_is_group_membership_request()) :
                        bp_get_template_part('groups/single/request-membership');

                    // The group is not visible, show the status message
                    else :

                        do_action('bp_before_group_status_message');
                        ?>

                        <div id="message" class="info">
                            <p><?php bp_group_status_message(); ?></p>
                        </div>

                        <?php
                        do_action('bp_after_group_status_message');

                    endif;
                endif;

                do_action('bp_after_group_body');
                ?>
            </div>
        </div><!-- #item-body -->

        <?php do_action('bp_after_group_home_content'); ?>

    <?php
    endwhile;
endif;
?>

<?php openlab_bp_sidebar('actions', false, ' mobile-enabled'); ?>
