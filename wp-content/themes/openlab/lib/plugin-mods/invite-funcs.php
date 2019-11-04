<?php
/**
 * Invite only functions
 * These functions are clones of those found in the Invite Anyone plugin
 * They are duplicated here so that Bootstrap markup can be injected for uniform styling
 * See also: openlab/buddypress/members/single/invite-anyone.php for template overrides
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

    $blogname = get_bloginfo('name');
    $welcome_message = sprintf(__('Invite friends to join %s by following these steps:', 'bp-invite-anyone'), $blogname);
    ?>
    <form id="invite-anyone-by-email" class="form-panel" action="<?php echo $bp->displayed_user->domain . $bp->invite_anyone->slug . '/sent-invites/send/' ?>" method="post">

        <div class="panel panel-default">
            <div class="panel-heading"><?php _e('Invite New Members', 'bp-invite-anyone'); ?></div>
            <div class="panel-body">

                <?php
                if (!empty($returned_data['error_message'])) {
                    ?>
                    <div class="invite-anyone-error bp-template-notice error">
                        <p><?php _e("Some of your invitations were not sent. Please see the errors below and resubmit the failed invitations.", 'bp-invite-anyone') ?></p>
                    </div>
                    <?php
                }
                if (!empty($returned_data['error_message'])) :
                    ?>
                    <div class="invite-anyone-error bp-template-notice error">
                        <p><?php echo $returned_data['error_message'] ?></p>
                    </div>
                <?php endif ?>

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
                                        <li class="col-md-8 col-sm-12">
                                            <div class="group-item-wrapper pointer">
                                                <label for="invite_anyone_groups-<?php bp_group_id() ?>" class="invite-anyone-group-name">
                                                    <div class="row">
                                                        <div class="col-xs-2"><input type="checkbox" class="no-margin no-margin-top" name="invite_anyone_groups[]" id="invite_anyone_groups-<?php bp_group_id() ?>" value="<?php bp_group_id() ?>" <?php if ($from_group == bp_get_group_id() || array_search(bp_get_group_id(), $returned_groups)) : ?>checked<?php endif; ?> /></div>
                                                        <div class="col-xs-8"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_group_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo bp_get_group_name(); ?>"/></div>
                                                        <div class="col-xs-14"><?php bp_group_name() ?></div>
                                                    </div>
                                                </label>
                                            </div>

                                        </li>
                                    <?php endwhile; ?>

                                </ul>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php wp_nonce_field( 'invite_anyone_send_by_email', 'ia-send-by-email-nonce' ); ?>
                    <?php do_action('invite_anyone_addl_fields') ?>

                </ol>
            </div>
        </div>

        <div class="submit">
            <input type="submit" name="invite-anyone-submit" id="invite-anyone-submit" class="btn btn-primary btn-margin btn-margin-top" value="<?php _e('Send Invites', 'buddypress') ?> " />
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

function openlab_invite_anyone_screen_two_content() {
    global $bp;

    // Load the pagination helper
    if (!class_exists('BBG_CPT_Pag'))
        require_once( BP_INVITE_ANYONE_DIR . 'lib/bbg-cpt-pag.php' );
    $pagination = new BBG_CPT_Pag;

    $inviter_id = bp_loggedin_user_id();

    if (isset($_GET['sort_by']))
        $sort_by = $_GET['sort_by'];
    else
        $sort_by = 'date_invited';

    if (isset($_GET['order']))
        $order = $_GET['order'];
    else
        $order = 'DESC';

    $base_url = $bp->displayed_user->domain . $bp->invite_anyone->slug . '/sent-invites/';
    ?>

    <?php $invites = invite_anyone_get_invitations_by_inviter_id(bp_loggedin_user_id(), $sort_by, $order, $pagination->get_per_page, $pagination->get_paged) ?>

    <?php $pagination->setup_query($invites) ?>

    <?php if ($invites->have_posts()) : ?>
    <div class="form-panel sent-invites-panel">
    <div class="panel panel-default">
        <div class="panel-heading"><span class="bold"><?php _e('Sent Invites', 'bp-invite-anyone'); ?></span><div class="pull-right pagination-viewing"><?php $pagination->currently_viewing_text() ?></div></div>
        <div class="panel-body">

                <p id="sent-invites-intro"><?php _e('You have sent invitations to the following people.', 'bp-invite-anyone') ?></p>

                <table class="invite-anyone-sent-invites zebra table no-margin no-margin-bottom"
                       summary="<?php _e('This table displays a list of all your sent invites.
			Invites that have been accepted are highlighted in the listings.
			You may clear any individual invites, all accepted invites or all of the invites from the list.', 'bp-invite-anyone') ?>">
                    <thead>
                        <tr>
				<th scope="col" class="col-delete-invite"></th>
				<th scope="col" class="col-email<?php if ( $sort_by == 'email' ) : ?> sort-by-me<?php endif ?>"><a class="<?php echo $order ?>" title="Sort column order <?php echo $order ?>" href="<?php echo $base_url ?>?sort_by=email&amp;order=<?php if ( $sort_by == 'email' && $order == 'ASC' ) : ?>DESC<?php else : ?>ASC<?php endif; ?>"><?php _e( 'Invited email address', 'invite-anyone' ) ?></a></th>
				<th scope="col" class="col-group-invitations"><?php _e( 'Group invitations', 'invite-anyone' ) ?></th>
				<th scope="col" class="col-date-invited<?php if ( $sort_by == 'date_invited' ) : ?> sort-by-me<?php endif ?>"><a class="<?php echo $order ?>" title="Sort column order <?php echo $order ?>" href="<?php echo $base_url ?>?sort_by=date_invited&amp;order=<?php if ( $sort_by == 'date_invited' && $order == 'DESC' ) : ?>ASC<?php else : ?>DESC<?php endif; ?>"><?php _e( 'Sent', 'invite-anyone' ) ?></a></th>
				<th scope="col" class="col-date-joined<?php if ( $sort_by == 'date_joined' ) : ?> sort-by-me<?php endif ?>"><a class="<?php echo $order ?>" title="Sort column order <?php echo $order ?>" href="<?php echo $base_url ?>?sort_by=date_joined&amp;order=<?php if ( $order == 'DESC' ) : ?>ASC<?php else : ?>DESC<?php endif; ?>"><?php _e( 'Accepted', 'invite-anyone' ) ?></a></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr id="batch-clear">
                            <td colspan="5" >
                                <div id="invite-anyone-clear-links" class="inline-element-list">
                                    <a title="<?php _e('Clear all accepted invites from the list', 'bp-invite-anyone') ?>" class="confirm btn btn-primary link-btn" href="<?php echo wp_nonce_url($base_url . '?clear=accepted', 'invite_anyone_clear') ?>"><?php _e('Clear all accepted invitations', 'bp-invite-anyone') ?></a>
                                    <a title="<?php _e('Clear all your listed invites', 'bp-invite-anyone') ?>" class="confirm btn btn-primary link-btn" href="<?php echo wp_nonce_url($base_url . '?clear=all', 'invite_anyone_clear') ?>"><?php _e('Clear all invitations', 'bp-invite-anyone') ?></a>
                                </div>
                            </td>
                        </tr>
                    </tfoot>

                    <tbody>
                        <?php while ($invites->have_posts()) : $invites->the_post() ?>

                            <?php
                            $emails = wp_get_post_terms(get_the_ID(), invite_anyone_get_invitee_tax_name());

                            // Should never happen, but was messing up my test env
                            if (empty($emails)) {
                                continue;
                            }

                            // Before storing taxonomy terms in the db, we replaced "+" with ".PLUSSIGN.", so we need to reverse that before displaying the email address.
                            $email = str_replace('.PLUSSIGN.', '+', $emails[0]->name);

                            $post_id = get_the_ID();

                            $query_string = preg_replace("|clear=[0-9]+|", '', $_SERVER['QUERY_STRING']);

                            $clear_url = ( $query_string ) ? $base_url . '?' . $query_string . '&clear=' . $post_id : $base_url . '?clear=' . $post_id;
                            $clear_url = wp_nonce_url($clear_url, 'invite_anyone_clear');
                            $clear_link = '<a class="clear-entry confirm" title="' . __('Clear this invitation', 'bp-invite-anyone') . '" href="' . $clear_url . '">x<span></span></a>';

                            $groups = wp_get_post_terms(get_the_ID(), invite_anyone_get_invited_groups_tax_name());
                            if (!empty($groups)) {
                                $group_names = '<ul class="inline-element-list">';
                                foreach ($groups as $group_term) {
                                    $group = new BP_Groups_Group($group_term->name);
                                    $group_names .= '<li>' . bp_get_group_name($group) . '</li>';
                                }
                                $group_names .= '</ul>';
                            } else {
                                $group_names = '-';
                            }

                            global $post;

                            $date_invited = invite_anyone_format_date($post->post_date);

                            $accepted = get_post_meta(get_the_ID(), 'bp_ia_accepted', true);

                            if ($accepted):
                                $date_joined = invite_anyone_format_date($accepted);
                                $accepted = true;
                            else:
                                $date_joined = '-';
                                $accepted = false;
                            endif;
                            ?>

				<tr <?php if($accepted){ ?> class="accepted" <?php } ?>>
					<td class="col-delete-invite"><?php echo $clear_link ?></td>
					<td class="col-email"><?php echo esc_html( $email ) ?></td>
					<td class="col-group-invitations"><?php echo $group_names ?></td>
					<td class="col-date-invited"><?php echo $date_invited ?></td>
					<td class="date-joined hidden-xs col-date-joined"><span></span><?php echo $date_joined ?></td>
				</tr>
                        <?php endwhile ?>
                    </tbody>
                </table>

                <div class="ia-pagination">
                    <div class="pag-links">
                        <?php $pagination->paginate_links() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php else : ?>

        <div class="info group-list row" id="message">
            <div class="col-md-24">
		<p class="bold"><?php _e("You haven't sent any email invitations yet.", 'bp-invite-anyone') ?></p>
            </div>
	</div>

    <?php endif; ?>
    <?php
}

/**
 * Don't allow group queries on invitation pages to load too many groups.
 *
 * See #1996.
 */
function openlab_limit_invite_anyone_group_query( $args ) {
	if ( ! bp_is_user() ) {
		return $args;
	}

	$dbs = debug_backtrace();
	foreach ( $dbs as $db ) {
		if ( isset( $db['function'] ) && 'openlab_invite_anyone_screen_one_content' === $db['function'] ) {
			$args['per_page'] = 100;
			return $args;
		}
	}

	return $args;
}
add_filter( 'bp_after_has_groups_parse_args', 'openlab_limit_invite_anyone_group_query' );

/**
 * Catches group member import requests.
 */
add_action(
	'bp_actions',
	function() {
		if ( ! bp_is_group() || ! bp_is_current_action( 'invite-anyone' ) ) {
			return;
		}

		if ( ! isset( $_POST['group-import-members-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'group_import_members', 'group-import-members-nonce' );

		// @todo Better permission check?
		if ( ! bp_is_item_admin() ) {
			return;
		}

		$emails_raw = wp_unslash( $_POST['email-addresses-to-import'] );

		$lines  = preg_split( '/\R/', $emails_raw );
		$emails = [];
		foreach ( $lines as $line ) {
			$line_emails = preg_split( '/[,\s]+/', $line );

			$emails = array_merge( $emails, $line_emails );
		}

		$status = [
			'success'         => [],
			'invalid_address' => [],
			'illegal_address' => [],
			'not_found'       => [],
		];

		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				$status['invalid_address'][] = $email;
				continue;
			}

			$email_domains = [ 'citytech.cuny.edu', 'mail.citytech.cuny.edu' ];
			if ( is_array( $email_domains ) && ! empty( $email_domains ) ) {
				$emaildomain = strtolower( substr( $email, 1 + strpos( $email, '@' ) ) );
				if ( ! in_array( $emaildomain, $email_domains, true ) ) {
					$status['illegal_address'][] = $email;
					continue;
				}
			}

			if ( is_email_address_unsafe( $email ) ) {
				$status['illegal_address'][] = $email;
				continue;
			}

			$user = get_user_by( 'email', $email );
			if ( ! $user ) {
				$status['not_found'][] = $email;
				continue;
			}

			groups_join_group( bp_get_current_group_id(), $user->ID );

			$status['success'][] = $email;
		}

		$timestamp = time();
		groups_update_groupmeta( bp_get_current_group_id(), 'import_' . $timestamp, $status );

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'invite-anyone?import_id=' . $timestamp );
	},
	5
);
