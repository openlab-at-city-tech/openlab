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

                <?php wp_nonce_field( 'bpges_admin_notice', 'bpges-admin-notice-nonce' ); ?>

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

    $group_status = ass_get_group_subscription_status( bp_loggedin_user_id(), $group->id );
	if ( ! $group_status ) {
		$group_status = ass_get_default_subscription( $group );
	}

	$group_type_label = openlab_get_group_type_label( [ 'case' => 'upper' ] );

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

						<p>Choose your email subscription options.</p>

						<div class="radio email-sub">
							<label><input type="radio" name="ass_group_subscribe" value="supersub" <?php checked( 'supersub' === $group_status || ! $group_status ); ?> /> All Email <span class="bpges-settings-gloss">(Receive email about this <?php echo esc_html( $group_type_label ); ?>'s activity as it happens.)</span></label>
							<label><input type="radio" name="ass_group_subscribe" value="dig" <?php checked( $group_status, 'dig' ); ?> /> Daily Digest <span class="bpges-settings-gloss">(This <?php echo esc_html( $group_type_label ); ?>'s activity will be bundled in a daily email with other groups set to daily digest.)</span></label>
							<label><input type="radio" name="ass_group_subscribe" value="sum" <?php checked( $group_status, 'sum' ); ?> /> Weekly Digest <span class="bpges-settings-gloss">(This <?php echo esc_html( $group_type_label ); ?>'s activity will be bundled in a weekly email with other groups set to weekly digest.)</span></label>
							<label><input type="radio" name="ass_group_subscribe" value="no" <?php checked( $group_status, 'no' ); ?> /> No Email <span class="bpges-settings-gloss">(Opt out of all email related to this <?php echo esc_html( $group_type_label ); ?>'s activity.)</span></label>
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
add_action(
	'bp_screens',
	function() {
		remove_action( 'bp_notification_settings', 'ass_add_notice_to_notifications_page', 9000 );
		add_action( 'bp_notification_settings', 'openlab_ass_add_notice_to_notifications_page', 9000 );
	}
);

/**
 * Swap out notice language.
 */
add_action(
	'bp_actions',
	function() {
		$bp = buddypress();

		if ( empty( $bp->template_message ) ) {
			return;
		}

		if ( 0 === strpos( $bp->template_message, 'Your email notifications are set to' ) ) {
			$bp->template_message = 'Your email subscription options were successfully updated.';
		}
	},
	6
);

/**
 * Force GES emails to look in a standardized location for templates.
 */
add_filter(
	'bp_email_get_template',
	function( $paths, $object ) {
		if ( ! ( $object instanceof WP_Post ) ) {
			return $paths;
		}

		$situations = get_the_terms( $object->ID, bp_get_email_tax_type() );

		$is_bp_ges_single = false;
		foreach ( $situations as $situation ) {
			if ( 'bp-ges-single' === $situation->slug ) {
				$is_bp_ges_single = true;
				break;
			}
		}

		if ( ! $is_bp_ges_single ) {
			return $paths;
		}

		array_unshift( $paths, 'assets/emails/single-bp-email-bp-ges-single.php' );

		return $paths;
	},
	10,
	2
);

/**
 * Provide additional email args for BPGES single emails.
 */
add_filter(
	'ass_send_email_args',
	function( $args, $email_type ) {
		if ( 'bp-ges-single' !== $email_type ) {
			return $args;
		}

		// Text for the View button.
		$view_text = openlab_get_activity_view_button_label( $args['activity']->type );
		if ( ! $view_text ) {
			$view_text = 'View';
		}

		$args['tokens']['ges.view-text'] = $view_text;

		// Modified 'email setting' text.
		$args['tokens']['ges.email-setting-description'] = str_replace( ' for this group', '', $args['tokens']['ges.email-setting-description'] );

		// Group type label.
		$args['tokens']['ges.group-type'] = openlab_get_group_type_label(
			[
				'group_id' => $args['activity']->item_id,
			]
		);

		return $args;
	},
	10,
	2
);

/**
 * Replace 'in the group' with group-type-specific string in outgoing emails.
 */
add_filter(
	'bpges_activity_action',
	function( $action, $activity ) {
		return openlab_replace_group_type_in_activity_action( $action, $activity->item_id );
	},
	10,
	2
);

add_action(
	'bp_email_set_tokens',
	function( $retval, $tokens, BP_Email $email ) {
		$sender = $email->get_from();
		$email->set_from( $sender->get_address(), get_option( 'blogname' ) );
		return $retval;
	},
	20,
	3
);
