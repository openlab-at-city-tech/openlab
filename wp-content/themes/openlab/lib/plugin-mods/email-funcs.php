<?php
/**
 * Custom functionality extrapolated from BuddyPress Group Email Subscription
 * See also: openlab/buddypress/groups/single/notifications.php for template overrides
 */

/**
 * create a form that allows admins to email everyone in the group
 * @global type $bp
 */
function openlab_ass_admin_notice_form() {
    global $bp;
    
    do_action('template_notices');

    if (groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id()) || is_super_admin()) {
        $submit_link = bp_get_groups_action_link('notifications');
        ?>
        <form action="<?php echo $submit_link ?>" method="post" class="form-panel">
            <?php wp_nonce_field('ass_email_options'); ?>
            
            <div class="panel-button-group">
                <div class="panel panel-default">
                    <div class="panel-heading semibold"><?php _e('Send an email notice to everyone in the group', 'bp-ass'); ?></div>
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
                    <div class="panel-heading semibold"><?php _e('Welcome Email', 'bp-ass'); ?></div>
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

/**
 * show group subscription settings on the notification page.
 * @global type $bp
 * @return boolean
 */
function openlab_ass_group_subscribe_settings() {
    global $bp;

    $group = groups_get_current_group();

    if (!is_user_logged_in() || !empty($group->is_banned) || !$group->is_member)
        return false;

    $group_status = ass_get_group_subscription_status(bp_loggedin_user_id(), $group->id);

    $submit_link = bp_get_groups_action_link('notifications');
    ?>
    <div id="ass-email-subscriptions-options-page">
        <form action="<?php echo $submit_link ?>" method="post" class="form-panel">
            <div class="panel-button-group">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('Email Subscription Options', 'bp-ass') ?></div>
                    <div class="panel-body">
                        <input type="hidden" name="ass_group_id" value="<?php echo $group->id; ?>"/>
                        <?php wp_nonce_field('ass_subscribe'); ?>

                        <b><?php _e('How do you want to read this group?', 'bp-ass'); ?></b>

                        <div class="ass-email-type radio">
                            <label><input type="radio" name="ass_group_subscribe" value="no" <?php if ($group_status == "no" || $group_status == "un" || !$group_status) echo 'checked="checked"'; ?>><?php _e('No Email', 'bp-ass'); ?></label>
                            <div class="ass-email-explain italics"><?php _e('I will read this group on the web', 'bp-ass'); ?></div>
                        </div>

                        <div class="ass-email-type radio">
                            <label><input type="radio" name="ass_group_subscribe" value="sum" <?php if ($group_status == "sum") echo 'checked="checked"'; ?>><?php _e('Weekly Summary Email', 'bp-ass'); ?></label>
                            <div class="ass-email-explain italics"><?php _e('Get a summary of new topics each week', 'bp-ass'); ?></div>
                        </div>

                        <div class="ass-email-type radio">
                            <label><input type="radio" name="ass_group_subscribe" value="dig" <?php if ($group_status == "dig") echo 'checked="checked"'; ?>><?php _e('Daily Digest Email', 'bp-ass'); ?></label>
                            <div class="ass-email-explain italics"><?php _e('Get all the day\'s activity bundled into a single email', 'bp-ass'); ?></div>
                        </div>

                        <?php if (ass_get_forum_type()) : ?>
                            <div class="ass-email-type radio">
                                <label><input type="radio" name="ass_group_subscribe" value="sub" <?php if ($group_status == "sub") echo 'checked="checked"'; ?>><?php _e('New Topics Email', 'bp-ass'); ?></label>
                                <div class="ass-email-explain italics"><?php _e('Send new topics as they arrive (but don\'t send replies)', 'bp-ass'); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="ass-email-type radio">
                            <label><input type="radio" name="ass_group_subscribe" value="supersub" <?php if ($group_status == "supersub") echo 'checked="checked"'; ?>><?php _e('All Email', 'bp-ass'); ?></label>
                            <div class="ass-email-explain italics"><?php _e('Send all group activity as it arrives', 'bp-ass'); ?></div>
                        </div>
                    </div>
                </div>

                <input type="submit" value="<?php _e('Save Settings', 'bp-ass') ?>" id="ass-save" name="ass-save" class="btn btn-primary">
            </div>

            <?php if (ass_get_forum_type() == 'buddypress') : ?>
                <p class="ass-sub-note italics"><?php _e('Note: Normally, you receive email notifications for topics you start or comment on. This can be changed at', 'bp-ass'); ?> <a href="<?php echo bp_loggedin_user_domain() . BP_SETTINGS_SLUG . '/notifications/' ?>"><?php _e('email notifications', 'bp-ass'); ?></a>.</p>
            <?php endif; ?>

        </form>
    </div><!-- end ass-email-subscriptions-options-page -->
    <?php
}

// Add a notice at end of email notification about how to change group email subscriptions
function openlab_ass_add_notice_to_notifications_page() {
?>
		<div id="group-email-settings">
			<table class="notification-settings zebra">
				<thead>
					<tr>
						<th class="icon">&nbsp;</th>
						<th class="title"><?php _e( 'Individual Group Email Settings', 'bp-ass' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>
							<p><?php _e('To change the email notification settings for your Courses, Projects, Clubs and Portfolio:','bp-ass'); ?></p>
                                                        <ol>
                                                            <li>Visit the group's Profile page</li>
                                                            <li>In the sidebar, click "Membership"</li>
                                                            <li>Select "Your Email Options"</li>
                                                        </ol>

							<?php if ( get_option( 'ass-global-unsubscribe-link' ) == 'yes' ) : ?>
								<p><a href="<?php echo wp_nonce_url( add_query_arg( 'ass_unsubscribe', 'all' ), 'ass_unsubscribe_all' ); ?>"><?php _e( "Or set all your group's email options to No Email", 'bp-ass' ); ?></a></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
<?php
}

remove_action ( 'bp_notification_settings', 'ass_add_notice_to_notifications_page', 9000 );
add_action( 'bp_notification_settings', 'openlab_ass_add_notice_to_notifications_page', 9000 );
