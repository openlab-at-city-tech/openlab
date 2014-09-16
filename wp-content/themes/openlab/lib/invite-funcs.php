<?php
/**
 * Invite only functions
 * These functions are clones of those found in the Invite Anyone plugin
 * They are duplicated here so that Bootstrap markup can be injected for uniform styling
 * The hope is that a future release of the plugin will provide the ability to inject markup changes via hooks and/or template overrides, eliminating the need for these custom functions
 */

/**
 * Dequeue inherit styling from plugin
 */
function openlab_dequeue_invite_anyone_styles() {
    wp_dequeue_style('invite-anyone-by-email-style');
}

add_action('wp_print_styles', 'openlab_dequeue_invite_anyone_styles', 999);

/**
 * Invite new members custom
 * @global type $bp
 * @return type
 */
function openlab_invite_anyone_screen_one_content() {

    global $bp;

    $iaoptions = invite_anyone_options();

    // Hack - catch already=accepted
    if (!empty($_GET['already']) && 'accepted' === $_GET['already'] && bp_is_my_profile()) {
        _e('It looks like you&#8217;ve already accepted your invitation to join the site.', 'invite-anyone');
        return;
    }

    // If the user has maxed out his invites, no need to go on
    if (!empty($iaoptions['email_limit_invites_toggle']) && $iaoptions['email_limit_invites_toggle'] == 'yes' && !current_user_can('delete_others_pages')) {
        $sent_invites = invite_anyone_get_invitations_by_inviter_id(bp_displayed_user_id());
        $sent_invites_count = $sent_invites->post_count;
        if ($sent_invites_count >= $iaoptions['limit_invites_per_user']) :
            ?>

            <h4><?php _e('Invite New Members', 'bp-invite-anyone'); ?></h4>

            <p id="welcome-message"><?php _e('You have sent the maximum allowed number of invitations.', 'bp-invite-anyone'); ?></em></p>

            <?php
            return;
        endif;
    }

    if (!$max_invites = $iaoptions['max_invites'])
        $max_invites = 5;

    $from_group = false;
    if (!empty($bp->action_variables)) {
        if ('group-invites' == $bp->action_variables[0])
            $from_group = $bp->action_variables[1];
    }

    $returned_data = !empty($bp->invite_anyone->returned_data) ? $bp->invite_anyone->returned_data : false;

    /* If the user is coming from the widget, $returned_emails is populated with those email addresses */
    if (isset($_POST['invite_anyone_widget'])) {
        check_admin_referer('invite-anyone-widget_' . $bp->loggedin_user->id);

        if (!empty($_POST['invite_anyone_email_addresses'])) {
            $returned_data['error_emails'] = invite_anyone_parse_addresses($_POST['invite_anyone_email_addresses']);
        }

        /* If the widget appeared on a group page, the group ID should come along with it too */
        if (isset($_POST['invite_anyone_widget_group']))
            $returned_data['groups'] = $_POST['invite_anyone_widget_group'];
    }

    // $returned_groups is padded so that array_search (below) returns true for first group */

    $counter = 0;
    $returned_groups = array(0);
    if (!empty($returned_data['groups'])) {
        foreach ($returned_data['groups'] as $group_id) {
            $returned_groups[] = $group_id;
        }
    }

    // Get the returned email subject, if there is one
    $returned_subject = !empty($returned_data['subject']) ? stripslashes($returned_data['subject']) : false;

    // Get the returned email message, if there is one
    $returned_message = !empty($returned_data['message']) ? stripslashes($returned_data['message']) : false;

    if (!empty($returned_data['error_message'])) {
        ?>
        <div class="invite-anyone-error error">
            <p><?php _e("Some of your invitations were not sent. Please see the errors below and resubmit the failed invitations.", 'bp-invite-anyone') ?></p>
        </div>
        <?php
    }

    $blogname = get_bloginfo('name');
    $welcome_message = sprintf(__('Invite friends to join %s by following these steps:', 'bp-invite-anyone'), $blogname);
    ?>
    <form id="invite-anyone-by-email" class="form-panel" action="<?php echo $bp->displayed_user->domain . $bp->invite_anyone->slug . '/sent-invites/send/' ?>" method="post">

        <div class="panel panel-default">
            <div class="panel-heading"><?php _e('Invite New Members', 'bp-invite-anyone'); ?></div>
            <div class="panel-body">

                <?php
                if (isset($iaoptions['email_limit_invites_toggle']) && $iaoptions['email_limit_invites_toggle'] == 'yes' && !current_user_can('delete_others_pages')) {
                    if (!isset($sent_invites)) {
                        $sent_invites = invite_anyone_get_invitations_by_inviter_id(bp_loggedin_user_id());
                        $sent_invites_count = $sent_invites->post_count;
                    }

                    $limit_invite_count = (int) $iaoptions['limit_invites_per_user'] - (int) $sent_invites_count;

                    if ($limit_invite_count < 0) {
                        $limit_invite_count = 0;
                    }
                    ?>

                    <p class="description"><?php printf(__('The site administrator has limited each user to %1$d invitations. You have %2$d invitations remaining.', 'bp-invite-anyone'), (int) $iaoptions['limit_invites_per_user'], (int) $limit_invite_count) ?></p>

                    <?php
                }
                ?>

                <p id="welcome-message"><?php echo $welcome_message ?></p>

                <ol id="invite-anyone-steps" class="inline-element-list">

                    <li>
                        <?php if (!empty($returned_data['error_message'])) : ?>
                            <div class="invite-anyone-error error">
                                <p><?php echo $returned_data['error_message'] ?></p>
                            </div>
                        <?php endif ?>

                        <div class="manual-email">
                            <p>
                                <?php _e('Enter email addresses below, one per line.', 'bp-invite-anyone') ?>
                                <?php if (invite_anyone_allowed_domains()) : ?> <?php _e('You can only invite people whose email addresses end in one of the following domains:', 'bp-invite-anyone') ?> <?php echo invite_anyone_allowed_domains(); ?><?php endif; ?>
                            </p>

                            <?php if (false !== $max_no_invites = invite_anyone_max_invites()) : ?>
                                <p class="description"><?php printf(__('You can invite a maximum of %s people at a time.', 'bp-invite-anyone'), $max_no_invites) ?></p>
                            <?php endif ?>
                            <?php openlab_invite_anyone_email_fields($returned_data['error_emails']) ?>
                        </div>

                        <?php /* invite_anyone_after_addresses gets $iaoptions so that Cloudsponge etc can tell whether certain components are activated, without an additional lookup */ ?>
                        <?php do_action('invite_anyone_after_addresses', $iaoptions) ?>

                    </li>

                    <li>
                        <?php if ($iaoptions['subject_is_customizable'] == 'yes') : ?>
                            <label for="invite-anyone-custom-subject"><?php _e('(optional) Customize the subject line of the invitation email.', 'bp-invite-anyone') ?></label>
                            <textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" class="form-control" rows="3" cols="10" ><?php echo invite_anyone_invitation_subject($returned_subject) ?></textarea>
                        <?php else : ?>
                            <label for="invite-anyone-custom-subject"><h4><?php _e('Subject: <span class="disabled-subject">Subject line is fixed</span>', 'bp-invite-anyone') ?></h4></label>
                            <textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" class="form-control" rows="3" cols="10" disabled="disabled"><?php echo invite_anyone_invitation_subject($returned_subject) ?> </textarea>

                            <input type="hidden" id="invite-anyone-customised-subject" name="invite_anyone_custom_subject" value="<?php echo invite_anyone_invitation_subject() ?>" />
                        <?php endif; ?>
                    </li>

                    <li>
                        <?php if ($iaoptions['message_is_customizable'] == 'yes') : ?>
                            <label for="invite-anyone-custom-message"><h4><?php _e('(optional) Customize the text of the invitation.', 'bp-invite-anyone') ?></h4></label>
                            <p class="description"><?php _e('The message will also contain a custom footer containing links to accept the invitation or opt out of further email invitations from this site.', 'bp-invite-anyone') ?></p>
                            <textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" class="form-control" cols="40" rows="7"><?php echo invite_anyone_invitation_message($returned_message) ?></textarea>
                        <?php else : ?>
                            <label for="invite-anyone-custom-message"><?php _e('Message:', 'bp-invite-anyone') ?></label>
                            <textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" class="form-control" disabled="disabled" ><?php echo invite_anyone_invitation_message($returned_message) ?></textarea>

                            <input type="hidden" name="invite_anyone_custom_message" value="<?php echo invite_anyone_invitation_message() ?>" />
                        <?php endif; ?>

                    </li>

                    <?php if (invite_anyone_are_groups_running()) : ?>
                        <?php if ($iaoptions['can_send_group_invites_email'] == 'yes' && bp_has_groups("per_page=10000&type=alphabetical&user_id=" . bp_loggedin_user_id())) : ?>
                            <li>
                                <p><?php _e('(optional) Select some groups. Invitees will receive invitations to these groups when they join the site.', 'bp-invite-anyone') ?></p>
                                <ul id="invite-anyone-group-list" class="inline-element-list row group-list">
                                    <?php while (bp_groups()) : bp_the_group(); ?>
                                        <?php
                                        // Enforce per-group invitation settings
                                        if (!bp_groups_user_can_send_invites(bp_get_group_id()) || 'anyone' !== invite_anyone_group_invite_access_test(bp_get_group_id())) {
                                            continue;
                                        }
                                        ?>
                                        <li class="col-sm-8">
                                            <div class="group-item-wrapper pointer">
                                                <label for="invite_anyone_groups-<?php bp_group_id() ?>" class="invite-anyone-group-name">
                                                    <div class="row">
                                                        <div class="col-sm-2"><input type="checkbox" class="no-margin no-margin-top" name="invite_anyone_groups[]" id="invite_anyone_groups-<?php bp_group_id() ?>" value="<?php bp_group_id() ?>" <?php if ($from_group == bp_get_group_id() || array_search(bp_get_group_id(), $returned_groups)) : ?>checked<?php endif; ?> /></div>
                                                        <div class="col-sm-8"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_group_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo bp_get_group_name(); ?>"/></div>
                                                        <div class="col-sm-14"><?php bp_group_name() ?></div>
                                                    </div>
                                                </label>
                                            </div>

                                        </li>
                                    <?php endwhile; ?>

                                </ul>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php do_action('invite_anyone_addl_fields') ?>

                </ol>
            </div>
        </div>

        <div class="submit">
            <input type="submit" name="invite-anyone-submit" id="invite-anyone-submit" class="btn btn-primary" value="<?php _e('Send Invites', 'buddypress') ?> " />
        </div>


    </form>
    <?php
}

/**
 * Custom invite anyone email textarea
 * @param type $returned_emails
 */
function openlab_invite_anyone_email_fields($returned_emails = false) {
    if (is_array($returned_emails))
        $returned_emails = implode("\n", $returned_emails);
    ?>
    <textarea name="invite_anyone_email_addresses" class="invite-anyone-email-addresses form-control" id="invite-anyone-email-addresses" rows="7"><?php echo $returned_emails ?></textarea>
    <?php
}
