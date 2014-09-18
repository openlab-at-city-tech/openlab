<?php
/**
 * Custom functionality extrapolated from BuddyPress Group Email Subscription 
 */

/**
 * create a form that allows admins to email everyone in the group
 * @global type $bp
 */
function openlab_ass_admin_notice_form() {
    global $bp;

    if (groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id()) || is_super_admin()) {
        $submit_link = bp_get_groups_action_link('notifications');
        ?>
        <form action="<?php echo $submit_link ?>" method="post" class="form-panel">
            <?php wp_nonce_field('ass_email_options'); ?>

            <div class="panel-button-group">
                <div class="panel panel-default">
                    <div class="panel-heading bold"><?php _e('Send an email notice to everyone in the group', 'bp-ass'); ?></div>
                    <div class="panel-body">

                        <p><?php _e('You can use the form below to send an email notice to all group members.', 'bp-ass'); ?> <br>
                            <b><?php _e('Everyone in the group will receive the email -- regardless of their email settings -- so use with caution', 'bp-ass'); ?></b>.</p>

                        <p>
                            <label for="ass-admin-notice-subject"><?php _e('Email Subject:', 'bp-ass') ?></label>
                            <input type="text" name="ass_admin_notice_subject" id="ass-admin-notice-subject" value="" />
                        </p>

                        <p>
                            <label for="ass-admin-notice-textarea"><?php _e('Email Content:', 'bp-ass') ?></label>
                            <textarea class="form-control" value="" name="ass_admin_notice" id="ass-admin-notice-textarea"></textarea>
                        </p>
                    </div>
                </div>

                <input class="btn btn-primary" type="submit" name="ass_admin_notice_send" value="<?php _e('Email this notice to everyone in the group', 'bp-ass') ?>" />
            </div>


            <?php $welcome_email = groups_get_groupmeta(bp_get_current_group_id(), 'ass_welcome_email'); ?>
            <?php $welcome_email_enabled = isset($welcome_email['enabled']) ? $welcome_email['enabled'] : ''; ?>

            <div class="panel-button-group">
                <div class="panel panel-default">
                    <div class="panel-heading bold"><?php _e('Welcome Email', 'bp-ass'); ?></div>
                    <div class="panel-body">

                        <p><?php _e('Send an email when a new member join the group.', 'bp-ass'); ?></p>

                        <p class="checkbox">
                            <label>
                                <input<?php checked($welcome_email_enabled, 'yes'); ?> type="checkbox" name="ass_welcome_email[enabled]" id="ass-welcome-email-enabled" value="yes" />
                                <?php _e('Enable welcome email', 'bp-ass'); ?>
                            </label>
                        </p>

                        <p class="ass-welcome-email-field<?php if ($welcome_email_enabled != 'yes') echo ' hide-if-js'; ?>">
                            <label for="ass-welcome-email-subject"><?php _e('Email Subject:', 'bp-ass'); ?></label>
                            <input value="<?php echo isset($welcome_email['subject']) ? $welcome_email['subject'] : ''; ?>" type="text" name="ass_welcome_email[subject]" id="ass-welcome-email-subject" class="form-control" />
                        </p>

                        <p class="ass-welcome-email-field<?php if ($welcome_email_enabled != 'yes') echo ' hide-if-js'; ?>">
                            <label for="ass-welcome-email-content"><?php _e('Email Content:', 'bp-ass'); ?></label>
                            <textarea name="ass_welcome_email[content]" id="ass-welcome-email-content" class="form-control"><?php echo isset($welcome_email['content']) ? $welcome_email['content'] : ''; ?></textarea>
                        </p>
                    </div>
                </div>

                <input class="btn btn-primary" type="submit" name="ass_welcome_email_submit" value="<?php _e('Save', 'bp-ass'); ?>" />
            </div>
        </form>
        <?php
    }
}
