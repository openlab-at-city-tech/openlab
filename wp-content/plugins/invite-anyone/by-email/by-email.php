<?php

require BP_INVITE_ANYONE_DIR . 'by-email/by-email-db.php';
require BP_INVITE_ANYONE_DIR . 'widgets/widgets.php';
require BP_INVITE_ANYONE_DIR . 'by-email/cloudsponge-integration.php';

// Temporary function until bp_is_active is fully integrated
function invite_anyone_are_groups_running() {
	if ( function_exists( 'groups_install' ) ) {
		return true;
	}

	if ( function_exists( 'bp_is_active' ) ) {
		if ( bp_is_active( 'groups' ) ) {
			return true;
		}
	}

	return false;
}

function invite_anyone_add_by_email_css() {
	global $bp;

	if ( bp_is_current_component( $bp->current_component ) ) {
		$style_url  = plugins_url() . '/invite-anyone/by-email/by-email-css.css';
		$style_file = WP_PLUGIN_DIR . '/invite-anyone/by-email/by-email-css.css';
		if ( file_exists( $style_file ) ) {
			wp_register_style(
				'invite-anyone-by-email-style',
				$style_url,
				[],
				BP_INVITE_ANYONE_VER
			);

			wp_enqueue_style( 'invite-anyone-by-email-style' );
		}
	}
}
add_action( 'wp_print_styles', 'invite_anyone_add_by_email_css' );

function invite_anyone_add_by_email_js() {
	global $bp;

	if ( bp_is_current_component( BP_INVITE_ANYONE_SLUG ) ) {
		$script_url  = plugins_url() . '/invite-anyone/by-email/by-email-js.js';
		$script_file = WP_PLUGIN_DIR . '/invite-anyone/by-email/by-email-js.js';
		if ( file_exists( $script_file ) ) {
			wp_register_script(
				'invite-anyone-by-email-scripts',
				$script_url,
				[ 'jquery' ],
				BP_INVITE_ANYONE_VER,
				true
			);

			wp_enqueue_script( 'invite-anyone-by-email-scripts' );
		}
	}
}
add_action( 'wp_print_scripts', 'invite_anyone_add_by_email_js' );

function invite_anyone_setup_globals() {
	global $bp, $wpdb;

	if ( ! isset( $bp->invite_anyone ) ) {
		$bp->invite_anyone = new stdClass();
	}

	$bp->invite_anyone->id = 'invite_anyone';

	$bp->invite_anyone->table_name = $wpdb->base_prefix . 'bp_invite_anyone';
	$bp->invite_anyone->slug       = 'invite-anyone';

	/* Register this in the active components array */
	$bp->active_components[ $bp->invite_anyone->slug ] = $bp->invite_anyone->id;
}
add_action( 'bp_setup_globals', 'invite_anyone_setup_globals', 2 );


function invite_anyone_opt_out_screen() {
	global $bp;

	$opt_out_email = isset( $_POST['opt_out_email'] ) ? sanitize_text_field( wp_unslash( $_POST['opt_out_email'] ) ) : '';

	// phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $_POST['oops_submit'] ) ) {
		$oops_email = rawurlencode( $opt_out_email );

		$opt_out_link = add_query_arg(
			array(
				'iaaction' => 'accept-invitation',
				'email'    => $oops_email,
			),
			bp_get_signup_page()
		);

		bp_core_redirect( $opt_out_link );
	}
	// phpcs:enable WordPress.Security.NonceVerification

	$opt_out_button_text = __( 'Opt Out', 'invite-anyone' );
	$oops_button_text    = __( 'Accept Invitation', 'invite-anyone' );

	$sitename = get_bloginfo( 'name' );

	$opt_out_message = sprintf(
		// translators: %s is the site name
		__( 'To opt out of future invitations to %s, make sure that your email is entered in the field below and click "Opt Out".', 'invite-anyone' ),
		$sitename
	);

	$oops_message = sprintf(
		// translators: %s is the site name
		__( 'If you are here by mistake and would like to accept your invitation to %s, click "Accept Invitation" instead.', 'invite-anyone' ),
		$sitename
	);

	// phpcs:ignore WordPress.Security.NonceVerification
	if ( bp_is_register_page() && isset( $_GET['iaaction'] ) && 'opt-out' === urldecode( $_GET['iaaction'] ) ) {
		get_header();
		?>
		<div id="content">
		<div class="padder">
		<?php if ( ! empty( $_POST['opt_out_submit'] ) ) : ?>
			<?php if ( $_POST['opt_out_submit'] === $opt_out_button_text && $opt_out_email ) : ?>
				<?php $email = str_replace( ' ', '+', $opt_out_email ); ?>

				<?php check_admin_referer( 'invite_anyone_opt_out' ); ?>

				<?php if ( invite_anyone_mark_as_opt_out( $email ) ) : ?>
					<?php $opted_out_message = __( 'You have successfully opted out. No more invitation emails will be sent to you by this site.', 'invite-anyone' ); ?>
					<p><?php echo esc_html( $opted_out_message ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Sorry, there was an error in processing your request', 'invite-anyone' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<?php /* I guess this should be some sort of error message? */ ?>
			<?php endif; ?>

		<?php else : ?>
			<?php $email = isset( $_GET['email'] ) ? urldecode( $_GET['email'] ) : ''; ?>
			<?php if ( $email ) : ?>
				<script type="text/javascript">
				jQuery(document).ready( function() {
					jQuery("input#opt_out_email").val("<?php echo esc_js( str_replace( ' ', '+', $email ) ); ?>");
				});
				</script>
			<?php endif; ?>

			<form action="" method="post">

				<?php do_action( 'invite_anyone_before_optout_messages' ); ?>

				<p><?php echo esc_html( $opt_out_message ); ?></p>

				<p><?php echo esc_html( $oops_message ); ?></p>

				<?php do_action( 'invite_anyone_after_optout_messages' ); ?>

				<?php wp_nonce_field( 'invite_anyone_opt_out' ); ?>
				<p><?php esc_html_e( 'Email:', 'invite-anyone' ); ?> <input type="text" id="opt_out_email" name="opt_out_email" size="50" /></p>

				<p><input type="submit" name="opt_out_submit" value="<?php echo esc_attr( $opt_out_button_text ); ?>" /> <input type="submit" name="oops_submit" value="<?php echo esc_attr( $oops_button_text ); ?>" />
				</p>

			</form>
		<?php endif; ?>
		</div>
		</div>
		<?php
		get_footer();
		die();

	}
}
add_action( 'wp', 'invite_anyone_opt_out_screen', 1 );


function invite_anyone_register_screen_message() {
	global $bp;

	if ( ! invite_anyone_is_accept_invitation_page() ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['email'] ) ) {
		$email = urldecode( $_GET['email'] );
	} else {
		$email = '';
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	?>
	<?php if ( empty( $email ) ) : ?>
		<div id="message" class="error"><p><?php esc_html_e( "It looks like you're trying to accept an invitation to join the site, but some information is missing. Please try again by clicking on the link in the invitation email.", 'invite-anyone' ); ?></p></div>
	<?php endif; ?>

	<?php if ( 'request-details' === $bp->signup->step && ! empty( $email ) ) : ?>

		<?php do_action( 'accept_email_invite_before' ); ?>

		<script type="text/javascript">
		jQuery(document).ready( function() {
			jQuery("input#signup_email").val("<?php echo esc_js( str_replace( ' ', '+', $email ) ); ?>");
		});

		</script>


		<?php
		$ia_obj = invite_anyone_get_invitations_by_invited_email( $email );

		$inviters = [];
		if ( $ia_obj->have_posts() ) {
			while ( $ia_obj->have_posts() ) {
				$ia_obj->the_post();
				$inviters[] = get_the_author_meta( 'ID' );
			}
		}

		$inviters = array_unique( $inviters );

		$inviters_names = [];
		foreach ( $inviters as $inviter ) {
			$inviters_names[] = bp_core_get_user_displayname( $inviter );
		}

		if ( ! empty( $inviters_names ) ) {
			$message = sprintf(
				// translators: %s is a comma-separated list of user names
				_n(
					'Welcome! You&#8217;ve been invited to join the site by the following user: %s. Please fill out the information below to create your account.',
					'Welcome! You&#8217;ve been invited to join the site by the following users: %s. Please fill out the information below to create your account.',
					count( $inviters_names ),
					'invite-anyone'
				),
				implode( ', ', $inviters_names )
			);
		} else {
			$message = __( 'Welcome! You&#8217;ve been invited to join the site. Please fill out the information below to create your account.', 'invite-anyone' );
		}

		echo '<div id="message" class="success"><p>' . esc_html( $message ) . '</p></div>';

		?>

	<?php endif; ?>
	<?php
}
add_action( 'bp_before_register_page', 'invite_anyone_register_screen_message' );

function invite_anyone_activate_user( $user_id ) {
	global $bp;

	$email = bp_core_get_user_email( $user_id );

	$inviters = array();

	// Fire the query
	$invites = invite_anyone_get_invitations_by_invited_email( $email );

	if ( $invites->have_posts() ) {
		// From the posts returned by the query, get a list of unique inviters
		$groups = array();
		while ( $invites->have_posts() ) {
			$invites->the_post();

			$inviter_id = get_the_author_meta( 'ID' );
			$inviters[] = $inviter_id;

			$groups_data = wp_get_post_terms( get_the_ID(), invite_anyone_get_invited_groups_tax_name() );
			foreach ( $groups_data as $group_data ) {
				if ( ! isset( $groups[ $group_data->name ] ) ) {
					// Keyed by inviter, which means they'll only get one invite per group
					$groups[ $group_data->name ] = $inviter_id;
				}
			}

			// Mark as accepted
			update_post_meta( get_the_ID(), 'bp_ia_accepted', gmdate( 'Y-m-d H:i:s' ) );
		}

		$inviters = array_unique( $inviters );

		// Friendship requests
		if ( bp_is_active( 'friends' ) && apply_filters( 'invite_anyone_send_friend_requests_on_acceptance', true ) ) {
			if ( function_exists( 'friends_add_friend' ) ) {
				foreach ( $inviters as $inviter ) {
					friends_add_friend( $inviter, $user_id );
				}
			}
		}

		// BuddyPress Followers support
		if ( function_exists( 'bp_follow_start_following' ) && apply_filters( 'invite_anyone_send_follow_requests_on_acceptance', true ) ) {
			foreach ( $inviters as $inviter ) {
				bp_follow_start_following(
					array(
						'leader_id'   => $user_id,
						'follower_id' => $inviter,
					)
				);
				bp_follow_start_following(
					array(
						'leader_id'   => $inviter,
						'follower_id' => $user_id,
					)
				);
			}
		}

		// Group invitations
		if ( bp_is_active( 'groups' ) ) {
			foreach ( $groups as $group_id => $inviter_id ) {
				$args = array(
					'user_id'    => $user_id,
					'group_id'   => $group_id,
					'inviter_id' => $inviter_id,
				);

				groups_invite_user( $args );
				groups_send_invites( $inviter_id, $group_id );
			}
		}
	}

	do_action( 'accepted_email_invite', $user_id, $inviters );
}
add_action( 'bp_core_activated_user', 'invite_anyone_activate_user', 10 );

function invite_anyone_setup_nav() {
	global $bp;

	if ( ! invite_anyone_access_test() ) {
		return;
	}

	/* Add 'Send Invites' to the main user profile navigation */
	bp_core_new_nav_item(
		array(
			'name'                    => __( 'Send Invites', 'invite-anyone' ),
			'slug'                    => $bp->invite_anyone->slug,
			'position'                => 80,
			'screen_function'         => 'invite_anyone_screen_one',
			'default_subnav_slug'     => 'invite-new-members',
			'show_for_displayed_user' => invite_anyone_access_test(),
		)
	);

	$invite_anyone_link = bp_members_get_user_url(
		bp_loggedin_user_id(),
		bp_members_get_path_chunks( [ buddypress()->invite_anyone->slug ] )
	);

	/* Create two sub nav items for this component */
	bp_core_new_subnav_item(
		array(
			'name'            => __( 'Invite New Members', 'invite-anyone' ),
			'slug'            => 'invite-new-members',
			'parent_slug'     => $bp->invite_anyone->slug,
			'parent_url'      => $invite_anyone_link,
			'screen_function' => 'invite_anyone_screen_one',
			'position'        => 10,
			'user_has_access' => invite_anyone_access_test(),
		)
	);

	bp_core_new_subnav_item(
		array(
			'name'            => __( 'Sent Invites', 'invite-anyone' ),
			'slug'            => 'sent-invites',
			'parent_slug'     => $bp->invite_anyone->slug,
			'parent_url'      => $invite_anyone_link,
			'screen_function' => 'invite_anyone_screen_two',
			'position'        => 20,
			'user_has_access' => invite_anyone_access_test(),
		)
	);
}
add_action( 'bp_setup_nav', 'invite_anyone_setup_nav' );

/**
 * Determine if current user can access invitation functions
 */
function invite_anyone_access_test() {
	global $current_user, $bp;

	$access_allowed = true;
	$iaoptions      = invite_anyone_options();

	if ( ! is_user_logged_in() ) {
		$access_allowed = false;
	} elseif ( current_user_can( 'bp_moderate' ) ) {
		// The site admin can see all
		$access_allowed = true;
	} elseif ( bp_displayed_user_id() && ! bp_is_my_profile() ) {
		$access_allowed = false;
	} elseif ( isset( $iaoptions['email_visibility_toggle'] ) && 'no_limit' === $iaoptions['email_visibility_toggle'] ) {
		// This is the last of the general checks: logged in,
		// looking at own profile, and finally admin has set to "All Users".
		$access_allowed = true;
	} elseif ( isset( $iaoptions['email_since_toggle'] ) && 'yes' === $iaoptions['email_since_toggle'] ) {
		// Minimum number of days since joined the site
		$since = isset( $iaoptions['days_since'] ) ? $iaoptions['days_since'] : 0;
		if ( $since ) {
			// WordPress's DAY_IN_SECONDS exists for WP >= 3.5, target version is 3.2, hence hard-coded value of 86400.
			$since = $since * 86400;

			$date_registered = strtotime( $current_user->data->user_registered );
			$time            = time();

			if ( $time - $date_registered < $since ) {
				$access_allowed = false;
			}
		}
	} elseif ( isset( $iaoptions['email_role_toggle'] ) && 'yes' === $iaoptions['email_role_toggle'] ) {
		// Minimum role on this blog. Users who are at the necessary role or higher
		// should move right through this toward the 'return true'
		// at the end of the function.
		$role = $iaoptions['email_role'];
		if ( isset( $iaoptions['minimum_role'] ) && $role ) {
			switch ( $role ) {
				case 'Subscriber' :
					if ( ! current_user_can( 'read' ) ) {
						$access_allowed = false;
					}
					break;

				case 'Contributor' :
					if ( ! current_user_can( 'edit_posts' ) ) {
						$access_allowed = false;
					}
					break;

				case 'Author' :
					if ( ! current_user_can( 'publish_posts' ) ) {
						$access_allowed = false;
					}
					break;

				case 'Editor' :
					if ( ! current_user_can( 'delete_others_pages' ) ) {
						$access_allowed = false;
					}
					break;

				case 'Administrator' :
					if ( ! current_user_can( 'switch_themes' ) ) {
						$access_allowed = false;
					}
					break;
			}//end switch
		}//end if
	} elseif ( isset( $iaoptions['email_blacklist_toggle'] ) && 'yes' === $iaoptions['email_blacklist_toggle'] ) {
		// User blacklist.
		if ( isset( $iaoptions['email_blacklist'] ) ) {
			$blacklist = wp_parse_id_list( $iaoptions['email_blacklist'] );
			$user_id   = intval( $current_user->ID );
			if ( in_array( $user_id, $blacklist, true ) ) {
				$access_allowed = false;
			}
		}
	}

	return apply_filters( 'invite_anyone_access_test', $access_allowed );
}
add_action( 'wp_head', 'invite_anyone_access_test' );

/**
 * Catch and process email sends.
 *
 * @since 1.1.0
 */
function invite_anyone_catch_send() {
	global $bp;

	if ( ! is_user_logged_in() || ! bp_is_my_profile() ) {
		return;
	}

	if ( ! bp_is_current_component( $bp->invite_anyone->slug ) ) {
		return;
	}

	if ( ! bp_is_current_action( 'sent-invites' ) ) {
		return;
	}

	if ( ! bp_is_action_variable( 'send', 0 ) ) {
		return;
	}

	if ( ! invite_anyone_access_test() ) {
		return;
	}

	if ( ! isset( $_POST['ia-send-by-email-nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['ia-send-by-email-nonce'] ), 'invite_anyone_send_by_email' ) ) {
		return;
	}

	if ( ! invite_anyone_process_invitations( stripslashes_deep( $_POST ) ) ) {
		bp_core_add_message( __( 'Sorry, there was a problem sending your invitations. Please try again.', 'invite-anyone' ), 'error' );
	}

	$redirect_url = invite_anyone_get_user_sent_invites_url( bp_displayed_user_id() );
	bp_core_redirect( $redirect_url );
}
add_action( 'bp_actions', 'invite_anyone_catch_send' );

function invite_anyone_catch_clear() {
	global $bp;

	if ( isset( $_COOKIE['invite-anyone'] ) ) {
		$returned_data = json_decode( wp_unslash( $_COOKIE['invite-anyone'] ), true );

		if ( $returned_data ) {
			// We'll take a moment nice and early in the loading process to get returned_data
			$keys = array(
				'error_message',
				'error_emails',
				'subject',
				'message',
				'groups',
			);

			foreach ( $keys as $key ) {
				$bp->invite_anyone->returned_data[ $key ] = null;
				if ( isset( $returned_data[ $key ] ) ) {
					$value                                    = stripslashes_deep( $returned_data[ $key ] );
					$bp->invite_anyone->returned_data[ $key ] = $value;
				}
			}
		}

		setcookie( 'invite-anyone', null, -1, '/' );
	}

	if ( isset( $_GET['clear'] ) ) {
		check_admin_referer( 'invite_anyone_clear' );

		$clear_id   = $_GET['clear'];
		$inviter_id = bp_loggedin_user_id();

		if ( (int) $clear_id ) {
			if ( invite_anyone_clear_sent_invite(
				array(
					'inviter_id' => $inviter_id,
					'clear_id'   => $clear_id,
				)
			) ) {
				bp_core_add_message( __( 'Invitation cleared', 'invite-anyone' ) );
			} else {
				bp_core_add_message( __( 'There was a problem clearing the invitation.', 'invite-anyone' ), 'error' );
			}
		} elseif ( invite_anyone_clear_sent_invite(
			array(
				'inviter_id' => $inviter_id,
				'type'       => $clear_id,
			)
		) ) {

				bp_core_add_message( __( 'Invitations cleared.', 'invite-anyone' ) );
		} else {
			bp_core_add_message( __( 'There was a problem clearing the invitations.', 'invite-anyone' ), 'error' );
		}

		$redirect_url = invite_anyone_get_user_sent_invites_url( bp_displayed_user_id() );
		bp_core_redirect( $redirect_url );
	}
}
add_action( 'bp_template_redirect', 'invite_anyone_catch_clear', 5 );

function invite_anyone_screen_one() {
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'invite_anyone_screen_one' );

	/* bp_template_title ought to be used - bp-default needs to markup the template tag
	and run a conditional check on template tag true to hide empty element markup or not
	add_action( 'bp_template_title', 'invite_anyone_screen_one_title' );
	*/
	add_action( 'bp_template_content', 'invite_anyone_screen_one_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function invite_anyone_screen_one_content() {
	global $bp;

	$iaoptions = invite_anyone_options();

	// Hack - catch already=accepted
	if ( ! empty( $_GET['already'] ) && 'accepted' === $_GET['already'] && bp_is_my_profile() ) {
		esc_html_e( 'It looks like you&#8217;ve already accepted your invitation to join the site.', 'invite-anyone' );
		return;
	}

	// If the user has maxed out his invites, no need to go on
	if ( ! empty( $iaoptions['email_limit_invites_toggle'] ) && 'yes' === $iaoptions['email_limit_invites_toggle'] && ! current_user_can( 'delete_others_pages' ) ) {
		$sent_invites       = invite_anyone_get_invitations_by_inviter_id( bp_displayed_user_id() );
		$sent_invites_count = $sent_invites->post_count;
		if ( $sent_invites_count >= $iaoptions['limit_invites_per_user'] ) :
			?>

			<h4><?php esc_html_e( 'Invite New Members', 'invite-anyone' ); ?></h4>

			<p id="welcome-message"><?php esc_html_e( 'You have sent the maximum allowed number of invitations.', 'invite-anyone' ); ?></em></p>

			<?php
			return;
		endif;
	}

	$max_invites = $iaoptions['max_invites'];
	if ( ! $max_invites ) {
		$max_invites = 5;
	}

	$from_group = false;
	if ( ! empty( $bp->action_variables ) ) {
		if ( 'group-invites' === $bp->action_variables[0] ) {
			$from_group = $bp->action_variables[1];
		}
	}

	$returned_data = ! empty( $bp->invite_anyone->returned_data ) ? $bp->invite_anyone->returned_data : false;

	/* If the user is coming from the widget, $returned_emails is populated with those email addresses */
	if ( isset( $_POST['invite_anyone_widget'] ) ) {
		check_admin_referer( 'invite-anyone-widget_' . $bp->loggedin_user->id );

		if ( ! empty( $_POST['invite_anyone_email_addresses'] ) ) {
			$returned_data['error_emails'] = invite_anyone_parse_addresses( $_POST['invite_anyone_email_addresses'] );
		}

		/* If the widget appeared on a group page, the group ID should come along with it too */
		if ( isset( $_POST['invite_anyone_widget_group'] ) ) {
			$returned_data['groups'] = $_POST['invite_anyone_widget_group'];
		}
	}

	// $returned_groups is padded so that array_search (below) returns true for first group */

	$counter         = 0;
	$returned_groups = array( 0 );
	if ( ! empty( $returned_data['groups'] ) ) {
		foreach ( $returned_data['groups'] as $group_id ) {
			$returned_groups[] = (int) $group_id;
		}
	}

	// Get the returned email subject, if there is one
	$returned_subject = ! empty( $returned_data['subject'] ) ? stripslashes( $returned_data['subject'] ) : false;

	// Get the returned email message, if there is one
	$returned_message = ! empty( $returned_data['message'] ) ? stripslashes( $returned_data['message'] ) : false;

	if ( ! empty( $returned_data['error_message'] ) ) {
		?>
		<div class="invite-anyone-error error">
			<p><?php esc_html_e( 'Some of your invitations were not sent. Please see the errors below and resubmit the failed invitations.', 'invite-anyone' ); ?></p>
		</div>
		<?php
	}

	$blogname        = get_bloginfo( 'name' );
	$welcome_message = sprintf(
		// translators: %s is the name of the blog
		__( 'Invite friends to join %s by following these steps:', 'invite-anyone' ),
		$blogname
	);

	$form_action = bp_members_get_user_url(
		bp_displayed_user_id(),
		bp_members_get_path_chunks( [ buddypress()->invite_anyone->slug, 'sent-invites', 'send' ] )
	);

	?>
	<form id="invite-anyone-by-email" action="<?php echo esc_url( $form_action ); ?>" method="post">

	<h4><?php esc_html_e( 'Invite New Members', 'invite-anyone' ); ?></h4>

	<?php

	if ( isset( $iaoptions['email_limit_invites_toggle'] ) && 'yes' === $iaoptions['email_limit_invites_toggle'] && ! current_user_can( 'delete_others_pages' ) ) {
		if ( ! isset( $sent_invites ) ) {
			$sent_invites       = invite_anyone_get_invitations_by_inviter_id( bp_loggedin_user_id() );
			$sent_invites_count = $sent_invites->post_count;
		}

		$limit_invite_count = (int) $iaoptions['limit_invites_per_user'] - (int) $sent_invites_count;

		if ( $limit_invite_count < 0 ) {
			$limit_invite_count = 0;
		}

		?>

		<p class="description">
			<?php
			echo esc_html(
				sprintf(
					// translators: %1$s is the maximum number of invites, %2$s is the remaining number of invites
					_n(
						'The site administrator has limited each user to %1$s invitation. Remaining invitations: %2$s',
						'The site administrator has limited each user to %1$s invitations. Remaining invitations: %2$s',
						$limit_invite_count,
						'invite-anyone'
					),
					number_format_i18n( $iaoptions['limit_invites_per_user'] ),
					number_format_i18n( $limit_invite_count )
				)
			);
			?>
		</p>

		<?php
	}
	?>

	<p id="welcome-message"><?php echo esc_html( wp_kses_post( $welcome_message ) ); ?></p>

	<ol id="invite-anyone-steps">

		<li>
			<?php if ( ! empty( $returned_data['error_message'] ) ) : ?>
				<div class="invite-anyone-error error">
					<p><?php echo wp_kses_post( $returned_data['error_message'] ); ?></p>
				</div>
			<?php endif ?>

			<div class="manual-email">
				<label for="invite-anyone-email-addresses">
					<?php esc_html_e( 'Enter email addresses below, one per line.', 'invite-anyone' ); ?>
				</label>

				<?php
				if ( invite_anyone_allowed_domains() ) :
					?>
					<p class="description"><?php esc_html_e( 'You can only invite people whose email addresses end in one of the following domains:', 'invite-anyone' ); ?> <?php echo esc_html( invite_anyone_allowed_domains() ); ?></p><?php endif; ?>

				<?php $max_no_invites = invite_anyone_max_invites(); ?>
				<?php if ( false !== $max_no_invites ) : ?>
					<p class="description">
						<?php
						echo esc_html(
							sprintf(
								// translators: %s is the maximum number of invites
								_n(
									'You can invite a maximum of %s person at a time.',
									'You can invite a maximum of %s people at a time.',
									$max_no_invites,
									'invite-anyone'
								),
								number_format_i18n( $max_no_invites )
							)
						);
						?>
					</p>
				<?php endif ?>

				<?php
				$error_emails = is_array( $returned_data ) && isset( $returned_data['error_emails'] ) ? $returned_data['error_emails'] : array();
				?>

				<?php invite_anyone_email_fields( $error_emails ); ?>
			</div>

			<?php /* invite_anyone_after_addresses gets $iaoptions so that Cloudsponge etc can tell whether certain components are activated, without an additional lookup */ ?>
			<?php do_action( 'invite_anyone_after_addresses', $iaoptions ); ?>

		</li>

		<li>
			<?php if ( 'yes' === $iaoptions['subject_is_customizable'] ) : ?>
				<label for="invite-anyone-custom-subject"><?php esc_html_e( '(optional) Customize the subject line of the invitation email.', 'invite-anyone' ); ?></label>
					<textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" rows="15" cols="10" ><?php echo esc_textarea( invite_anyone_invitation_subject( $returned_subject ) ); ?></textarea>
			<?php else : ?>
				<strong><?php esc_html_e( 'Subject:', 'invite-anyone' ); ?></strong> <?php echo esc_html( invite_anyone_invitation_subject( $returned_subject ) ); ?>

				<input type="hidden" id="invite-anyone-customised-subject" name="invite_anyone_custom_subject" value="<?php echo esc_attr( invite_anyone_invitation_subject() ); ?>" />
			<?php endif; ?>
		</li>

		<li>
			<?php if ( 'yes' === $iaoptions['message_is_customizable'] ) : ?>
				<label for="invite-anyone-custom-message"><?php esc_html_e( '(optional) Customize the text of the invitation.', 'invite-anyone' ); ?></label>
				<p class="description"><?php esc_html_e( 'The message will also contain a custom footer containing links to accept the invitation or opt out of further email invitations from this site.', 'invite-anyone' ); ?></p>
					<textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" cols="40" rows="10"><?php echo esc_textarea( invite_anyone_invitation_message( $returned_message ) ); ?></textarea>
			<?php else : ?>
				<label for="invite-anyone-custom-message"><?php esc_html_e( 'Message:', 'invite-anyone' ); ?></label>
					<textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" disabled="disabled"><?php echo esc_textarea( invite_anyone_invitation_message( $returned_message ) ); ?></textarea>

				<input type="hidden" name="invite_anyone_custom_message" value="<?php echo esc_attr( invite_anyone_invitation_message() ); ?>" />
			<?php endif; ?>

		</li>

		<?php if ( invite_anyone_are_groups_running() ) : ?>
			<?php if ( 'yes' === $iaoptions['can_send_group_invites_email'] && bp_has_groups( 'per_page=10000&type=alphabetical&user_id=' . bp_loggedin_user_id() ) ) : ?>
			<li>
				<fieldset>
					<legend><?php esc_html_e( '(optional) Select some groups. Invitees will receive invitations to these groups when they join the site.', 'invite-anyone' ); ?></legend>
					<ul id="invite-anyone-group-list">
						<?php
						while ( bp_groups() ) :
							bp_the_group();
							?>
							<?php

							// Enforce per-group invitation settings
							if ( ! bp_groups_user_can_send_invites( bp_get_group_id() ) || 'anyone' !== invite_anyone_group_invite_access_test( bp_get_group_id() ) ) {
								continue;
							}

							?>
							<li>
							<input type="checkbox" name="invite_anyone_groups[]" id="invite_anyone_groups-<?php echo esc_attr( bp_get_group_id() ); ?>" value="<?php echo esc_attr( bp_get_group_id() ); ?>" <?php checked( bp_get_group_id() === (int) $from_group || array_search( bp_get_group_id(), $returned_groups, true ) ); ?> />

							<label for="invite_anyone_groups-<?php echo esc_attr( bp_get_group_id() ); ?>" class="invite-anyone-group-name"><?php bp_group_avatar_mini(); ?> <span><?php bp_group_name(); ?></span></label>

							</li>
						<?php endwhile; ?>

					</ul>
				</fieldset>
			</li>
			<?php endif; ?>

		<?php endif; ?>

		<?php wp_nonce_field( 'invite_anyone_send_by_email', 'ia-send-by-email-nonce' ); ?>
		<?php do_action( 'invite_anyone_addl_fields' ); ?>

	</ol>

	<div class="submit">
		<input type="submit" name="invite-anyone-submit" id="invite-anyone-submit" value="<?php esc_attr_e( 'Send Invites', 'invite-anyone' ); ?> " />
	</div>

	</form>
	<?php
}

/**
 * invite_anyone_screen_two()
 *
 */
function invite_anyone_screen_two() {
	global $bp;

	do_action( 'invite_anyone_sent_invites_screen' );

	/* bp_template_title ought to be used - bp-default needs to markup the template tag
	and run a conditional check on template tag true to hide empty element markup or not
	add_action( 'bp_template_title', 'invite_anyone_screen_two_title' );
	*/

	add_action( 'bp_template_content', 'invite_anyone_screen_two_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function invite_anyone_screen_two_content() {
	global $bp;

	// Load the pagination helper
	if ( ! class_exists( 'BBG_CPT_Pag' ) ) {
		require_once BP_INVITE_ANYONE_DIR . 'lib/bbg-cpt-pag.php';
	}

	$pagination = new BBG_CPT_Pag();

	$inviter_id = bp_loggedin_user_id();

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['sort_by'] ) ) {
		$sort_by = sanitize_text_field( wp_unslash( $_GET['sort_by'] ) );
	} else {
		$sort_by = 'date_invited';
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['order'] ) ) {
		$order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
	} else {
		$order = 'DESC';
	}

	if ( 'DESC' !== $order ) {
		$order = 'ASC';
	}

	$base_url = invite_anyone_get_user_sent_invites_url( bp_displayed_user_id() );

	?>

		<h4><?php esc_html_e( 'Sent Invites', 'invite-anyone' ); ?></h4>

		<?php $invites = invite_anyone_get_invitations_by_inviter_id( bp_loggedin_user_id(), $sort_by, $order, $pagination->get_per_page, $pagination->get_paged ); ?>

		<?php $pagination->setup_query( $invites ); ?>

		<?php if ( $invites->have_posts() ) : ?>
			<p id="sent-invites-intro"><?php esc_html_e( 'You have sent invitations to the following people.', 'invite-anyone' ); ?></p>

			<div class="ia-pagination">
				<div class="currently-viewing">
					<?php $pagination->currently_viewing_text(); ?>
				</div>

				<div class="pag-links">
					<?php $pagination->paginate_links(); ?>
				</div>
			</div>

			<?php
			$table_summary = __( 'This table displays a list of all your sent invites. Invites that have been accepted are highlighted in the listings. You may clear any individual invites, all accepted invites or all of the invites from the list.', 'invite-anyone' );

			$col_email_class = 'col-email';
			if ( 'email' === $sort_by ) {
				$col_email_class .= ' sort-by-me';
			}

			$col_email_link = add_query_arg(
				[
					'sort_by' => 'email',
					'order'   => ( 'email' === $sort_by && 'ASC' === $order ) ? 'DESC' : 'ASC',
				],
				$base_url
			);

			$col_date_invited_class = 'col-date-invited';
			if ( 'date_invited' === $sort_by ) {
				$col_date_invited_class .= ' sort-by-me';
			}

			$col_date_invited_link = add_query_arg(
				[
					'sort_by' => 'date_invited',
					'order'   => ( 'date_invited' === $sort_by && 'ASC' === $order ) ? 'DESC' : 'ASC',
				],
				$base_url
			);

			$col_date_join_class = 'col-date-joined';
			if ( 'date_joined' === $sort_by ) {
				$col_date_join_class .= ' sort-by-me';
			}

			$col_date_join_link = add_query_arg(
				[
					'sort_by' => 'date_joined',
					'order'   => ( 'date_joined' === $sort_by && 'ASC' === $order ) ? 'DESC' : 'ASC',
				],
				$base_url
			);

			?>

			<table class="invite-anyone-sent-invites zebra" summary="<?php echo esc_attr( $table_summary ); ?>">
				<thead>
					<tr>
						<th scope="col" class="col-delete-invite"></th>

						<?php // translators: %s is the column order ?>
						<th scope="col" class="<?php echo esc_attr( $col_email_class ); ?>"><a class="<?php echo esc_attr( $order ); ?>" title="<?php echo esc_attr( sprintf( __( 'Sort column order: %s', 'invite-anyone' ), $order ) ); ?>" href="<?php echo esc_url( $col_email_link ); ?>"><?php esc_html_e( 'Invited email address', 'invite-anyone' ); ?></a></th>

						<th scope="col" class="col-group-invitations"><?php esc_html_e( 'Group invitations', 'invite-anyone' ); ?></th>

						<?php // translators: %s is the column order ?>
						<th scope="col" class="<?php echo esc_attr( $col_date_invited_class ); ?>"><a class="<?php echo esc_attr( $order ); ?>" title="<?php echo esc_attr( sprintf( __( 'Sort column order: %s', 'invite-anyone' ), $order ) ); ?>" href="<?php echo esc_url( $col_date_invited_link ); ?>"><?php esc_html_e( 'Sent', 'invite-anyone' ); ?></a></th>

						<?php // translators: %s is the column order ?>
						<th scope="col" class="<?php echo esc_attr( $col_date_join_class ); ?>"><a class="<?php echo esc_attr( $order ); ?>" title="<?php echo esc_attr( sprintf( __( 'Sort column order: %s', 'invite-anyone' ), $order ) ); ?>" href="<?php echo esc_url( $col_date_join_link ); ?>"><?php esc_html_e( 'Accepted', 'invite-anyone' ); ?></a></th>
					</tr>
				</thead>

				<tfoot>
				<tr id="batch-clear">
					<td colspan="5" >
					<ul id="invite-anyone-clear-links">
						<li> <a title="<?php esc_attr_e( 'Clear all accepted invites from the list', 'invite-anyone' ); ?>" class="confirm" href="<?php echo esc_url( wp_nonce_url( $base_url . '?clear=accepted', 'invite_anyone_clear' ) ); ?>"><?php esc_html_e( 'Clear all accepted invitations', 'invite-anyone' ); ?></a></li>
						<li class="last"><a title="<?php esc_attr_e( 'Clear all your listed invites', 'invite-anyone' ); ?>" class="confirm" href="<?php echo esc_url( wp_nonce_url( $base_url . '?clear=all', 'invite_anyone_clear' ) ); ?>"><?php esc_html_e( 'Clear all invitations', 'invite-anyone' ); ?></a></li>
					</ul>
				</td>
				</tr>
				</tfoot>

				<tbody>
				<?php
				while ( $invites->have_posts() ) :
					$invites->the_post();
					?>

					<?php
					$emails = wp_get_post_terms( get_the_ID(), invite_anyone_get_invitee_tax_name() );

					// Should never happen, but was messing up my test env
					if ( empty( $emails ) ) {
						continue;
					}

					// Before storing taxonomy terms in the db, we replaced "+" with ".PLUSSIGN.", so we need to reverse that before displaying the email address.
					$email = str_replace( '.PLUSSIGN.', '+', $emails[0]->name );

					$post_id = get_the_ID();

					$query_string = preg_replace( '|clear=[0-9]+|', '', $_SERVER['QUERY_STRING'] );

					$clear_url  = ( $query_string ) ? $base_url . '?' . $query_string . '&clear=' . $post_id : $base_url . '?clear=' . $post_id;
					$clear_url  = wp_nonce_url( $clear_url, 'invite_anyone_clear' );
					$clear_link = '<a class="clear-entry confirm" title="' . esc_attr__( 'Clear this invitation', 'invite-anyone' ) . '" href="' . esc_url( $clear_url ) . '">x<span></span></a>';

					$groups = wp_get_post_terms( get_the_ID(), invite_anyone_get_invited_groups_tax_name() );
					if ( ! empty( $groups ) ) {
						$group_names = '<ul>';
						foreach ( $groups as $group_term ) {
							$group        = new BP_Groups_Group( $group_term->name );
							$group_names .= '<li>' . esc_html( bp_get_group_name( $group ) ) . '</li>';
						}
						$group_names .= '</ul>';
					} else {
						$group_names = '-';
					}

					global $post;

					$date_invited = invite_anyone_format_date( $post->post_date );

					$accepted = get_post_meta( get_the_ID(), 'bp_ia_accepted', true );

					if ( $accepted ) :
						$date_joined = invite_anyone_format_date( $accepted );
						$accepted    = true;
					else :
						$date_joined = '-';
						$accepted    = false;
					endif;

					?>

					<tr
					<?php
					if ( $accepted ) {
						?>
						class="accepted" <?php } ?>>
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<td class="col-delete-invite"><?php echo $clear_link; ?></td>

						<td class="col-email"><?php echo esc_html( $email ); ?></td>

						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<td class="col-group-invitations"><?php echo $group_names; ?></td>
						<td class="col-date-invited"><?php echo esc_html( $date_invited ); ?></td>
						<td class="date-joined col-date-joined"><span></span><?php echo esc_html( $date_joined ); ?></td>
					</tr>
				<?php endwhile ?>
			</tbody>
			</table>

			<div class="ia-pagination">
				<div class="currently-viewing">
					<?php $pagination->currently_viewing_text(); ?>
				</div>

				<div class="pag-links">
					<?php $pagination->paginate_links(); ?>
				</div>
			</div>


		<?php else : ?>

		<p id="sent-invites-intro"><?php esc_html_e( "You haven't sent any email invitations yet.", 'invite-anyone' ); ?></p>

		<?php endif; ?>
	<?php
}

/**
 * Displays the field where email addresses are entered on the Send Invites page
 *
 * In version 0.8, this field was changed to be a textarea rather than individual fields.
 *
 * @package Invite Anyone
 *
 * @param array $returned_emails Optional. Emails returned because of a processing error
 */
function invite_anyone_email_fields( $returned_emails = false ) {
	if ( is_array( $returned_emails ) ) {
		$returned_emails = implode( "\n", $returned_emails );
	}
	?>
	<textarea name="invite_anyone_email_addresses" class="invite-anyone-email-addresses" id="invite-anyone-email-addresses"><?php echo esc_textarea( $returned_emails ); ?></textarea>
	<?php
}

/**
 * Gets the URL of a user's invite-new-members page.
 *
 * @since 1.4.6
 *
 * @param int $user_id User ID.
 * @return string
 */
function invite_anyone_get_user_invite_new_members_url( $user_id ) {
	return bp_members_get_user_url(
		$user_id,
		bp_members_get_path_chunks( [ buddypress()->invite_anyone->slug, 'invite-new-members' ] )
	);
}

/**
 * Gets the URL of a user's sent-invites page.
 *
 * @since 1.4.6
 *
 * @param int $user_id User ID.
 * @return string
 */
function invite_anyone_get_user_sent_invites_url( $user_id ) {
	return bp_members_get_user_url(
		$user_id,
		bp_members_get_path_chunks( [ buddypress()->invite_anyone->slug, 'sent-invites' ] )
	);
}

function invite_anyone_invitation_subject( $returned_message = false ) {
	global $bp;

	if ( ! $returned_message ) {
		$site_name = get_bloginfo( 'name' );

		$iaoptions = invite_anyone_options();

		if ( empty( $iaoptions['default_invitation_subject'] ) ) {
			// translators: %s is the name of the site.
			$text = sprintf( __( 'An invitation to join the %s community.', 'invite-anyone' ), $site_name );
		} else {
			$text = $iaoptions['default_invitation_subject'];
		}

		if ( ! is_admin() ) {
			$text = invite_anyone_wildcard_replace( $text );
		}
	} else {
		$text = $returned_message;
	}

	return stripslashes( $text );
}

function invite_anyone_invitation_message( $returned_message = false ) {
	global $bp;

	if ( ! $returned_message ) {
		$inviter_name = bp_core_get_user_displayname( bp_loggedin_user_id() );
		$blogname     = get_bloginfo( 'name' );

		$iaoptions = invite_anyone_options();

		if ( empty( $iaoptions['default_invitation_message'] ) ) {
			$text = sprintf(
				// translators: %s is the name of the site.
				__(
					'You have been invited by %%INVITERNAME%% to join the %s community.

Visit %%INVITERNAME%%\'s profile at %%INVITERURL%%.',
					'invite-anyone'
				),
				$blogname
			); /* Do not translate the strings embedded in %% ... %% ! */
		} else {
			$text = $iaoptions['default_invitation_message'];
		}

		if ( ! is_admin() ) {
			$text = invite_anyone_wildcard_replace( $text );
		}
	} else {
		$text = $returned_message;
	}

	return apply_filters( 'invite_anyone_get_invitation_message', stripslashes( $text ) );
}

function invite_anyone_process_footer() {
	$iaoptions = invite_anyone_options();

	if ( empty( $iaoptions['addl_invitation_message'] ) ) {

		$footer  = apply_filters( 'invite_anyone_accept_invite_footer_message', __( 'To accept this invitation, please visit %%ACCEPTURL%%', 'invite-anyone' ) );
		$footer .= '

';
		$footer .= apply_filters( 'invite_anyone_opt_out_footer_message', __( 'To opt out of future invitations to this site, please visit %%OPTOUTURL%%', 'invite-anyone' ) );
	} else {
		$footer = $iaoptions['addl_invitation_message'];
	}

	return stripslashes( $footer );
}

/**
 * Get the invitation acceptance URL for a given email address.
 *
 * @since 1.4.0
 *
 * @param string $email Email address.
 * @return string
 */
function invite_anyone_get_accept_url( $email ) {
	$accept_link = add_query_arg(
		array(
			'iaaction' => 'accept-invitation',
			'email'    => $email,
		),
		bp_get_signup_page()
	);

	/**
	 * Filters the invitation acceptance URL for a given email address.
	 *
	 * @since 0.3.3
	 * @since 1.4.0 Introduced `$email` parameter.
	 *
	 * @param string $accept_link Accept URL.
	 * @param string $email       Email address.
	 */
	return apply_filters( 'invite_anyone_accept_url', $accept_link, $email );
}

/**
 * Get the opt-out URL for a given email address.
 *
 * @since 1.4.0
 *
 * @param string $email Email address.
 * @return string
 */
function invite_anyone_get_opt_out_url( $email ) {
	$opt_out_link = add_query_arg(
		array(
			'iaaction' => 'opt-out',
			'email'    => $email,
		),
		bp_get_signup_page()
	);

	/**
	 * Filters the opt-out acceptance URL for a given email address.
	 *
	 * @since 1.4.0
	 *
	 * @param string $opt_out_link Opt-out URL.
	 * @param string $email        Email address.
	 */
	return apply_filters( 'invite_anyone_opt_out_url', $opt_out_link, $email );
}

function invite_anyone_wildcard_replace( $text, $email = false ) {
	global $bp;

	$inviter_name = bp_core_get_user_displayname( bp_loggedin_user_id() );
	$site_name    = get_bloginfo( 'name' );
	$inviter_url  = bp_loggedin_user_url();

	$email = rawurlencode( $email );

	$accept_link  = invite_anyone_get_accept_url( $email );
	$opt_out_link = invite_anyone_get_opt_out_url( $email );

	$text = str_replace( '%%INVITERNAME%%', $inviter_name, $text );
	$text = str_replace( '%%INVITERURL%%', $inviter_url, $text );
	$text = str_replace( '%%SITENAME%%', $site_name, $text );
	$text = str_replace( '%%OPTOUTURL%%', $opt_out_link, $text );
	$text = str_replace( '%%ACCEPTURL%%', $accept_link, $text );

	/* Adding single % replacements because lots of people are making the mistake */
	$text = str_replace( '%INVITERNAME%', $inviter_name, $text );
	$text = str_replace( '%INVITERURL%', $inviter_url, $text );
	$text = str_replace( '%SITENAME%', $site_name, $text );
	$text = str_replace( '%OPTOUTURL%', $opt_out_link, $text );
	$text = str_replace( '%ACCEPTURL%', $accept_link, $text );

	return $text;
}

/**
 * Get the max allowed invites
 */
function invite_anyone_max_invites() {
	$options = invite_anyone_options();
	return isset( $options['max_invites'] ) ? intval( $options['max_invites'] ) : false;
}

function invite_anyone_allowed_domains() {

	$domains = '';

	if ( function_exists( 'get_site_option' ) ) {
		$limited_email_domains = get_site_option( 'limited_email_domains' );

		if ( ! $limited_email_domains || ! is_array( $limited_email_domains ) ) {
			return $domains;
		}

		foreach ( $limited_email_domains as $domain ) {
			$domains .= "<strong>$domain</strong> ";
		}
	}

	return $domains;
}

/**
 * Fetches the invitee taxonomy name out of the $bp global so it can be queried in the template
 *
 * @package Invite Anyone
 * @since 0.8
 *
 * @return str $tax_name
 */
function invite_anyone_get_invitee_tax_name() {
	global $bp;

	$tax_name = '';

	if ( ! empty( $bp->invite_anyone->invitee_tax_name ) ) {
		$tax_name = $bp->invite_anyone->invitee_tax_name;
	}

	return $tax_name;
}

/**
 * Fetches the groups taxonomy name out of the $bp global so it can be queried in the template
 *
 * @package Invite Anyone
 * @since 0.8
 *
 * @return str $tax_name
 */
function invite_anyone_get_invited_groups_tax_name() {
	global $bp;

	$tax_name = '';

	if ( ! empty( $bp->invite_anyone->invited_groups_tax_name ) ) {
		$tax_name = $bp->invite_anyone->invited_groups_tax_name;
	}

	return $tax_name;
}

function invite_anyone_format_date( $date ) {
	return date_i18n( get_option( 'date_format' ), strtotime( $date ) );
}

/**
 * Parses email addresses, comma-separated or line-separated, into an array
 *
 * @package Invite Anyone
 * @since 0.8.8
 *
 * @param str $address_string The raw string from the input box
 * @return array $emails An array of addresses
 */
function invite_anyone_parse_addresses( $address_string ) {

	$emails = array();

	// First, split by line breaks
	$rows = explode( "\n", $address_string );

	// Then look through each row to split by comma
	foreach ( $rows as $row ) {
		$row_addresses = explode( ',', $row );

		// Then walk through and add each address to the array
		foreach ( $row_addresses as $row_address ) {
			$row_address_trimmed = trim( $row_address );

			// We also have to make sure that the email address isn't empty
			if ( ! empty( $row_address_trimmed ) && ! in_array( $row_address_trimmed, $emails, true ) ) {
				$emails[] = $row_address_trimmed;
			}
		}
	}

	return apply_filters( 'invite_anyone_parse_addresses', $emails, $address_string );
}

function invite_anyone_process_invitations( $data ) {
	global $bp;

	$options = invite_anyone_options();

	$emails = [];
	// Parse out the individual email addresses
	if ( ! empty( $data['invite_anyone_email_addresses'] ) ) {
		$emails = invite_anyone_parse_addresses( $data['invite_anyone_email_addresses'] );
	}

	// Filter the email addresses so that plugins can have a field day
	$emails = apply_filters( 'invite_anyone_submitted_email_addresses', $emails, $data );

	// Set up a wrapper for any data to return to the Send Invites screen in case of error
	$returned_data = array(
		'error_message' => false,
		'error_emails'  => array(),
		'groups'        => isset( $data['invite_anyone_groups'] ) ? $data['invite_anyone_groups'] : '',
	);

	if ( 'yes' === $options['subject_is_customizable'] ) {
		$data['invite_anyone_custom_subject'] = $data['invite_anyone_custom_subject'];
	} else {
		$data['invite_anyone_custom_subject'] = invite_anyone_invitation_subject();
	}

	if ( 'yes' === $options['message_is_customizable'] ) {
		$data['invite_anyone_custom_message'] = $data['invite_anyone_custom_message'];
	} else {
		$data['invite_anyone_custom_message'] = invite_anyone_invitation_message();
	}

	$returned_data['subject'] = $data['invite_anyone_custom_subject'];
	$returned_data['message'] = $data['invite_anyone_custom_message'];

	// Check against the max number of invites. Send back right away if there are too many.
	$max_invites = ! empty( $options['max_invites'] ) ? $options['max_invites'] : 5;

	if ( count( $emails ) > $max_invites ) {

		$returned_data['error_message'] = sprintf(
			// translators: %s is the number of maximum invites
			_n(
				'You are only allowed to invite up to %s person at a time. Please remove some addresses and try again',
				'You are only allowed to invite up to %s people at a time. Please remove some addresses and try again',
				$max_invites,
				'invite-anyone'
			),
			$max_invites
		);

		$returned_data['error_emails'] = $emails;

		setcookie( 'invite-anyone', wp_json_encode( $returned_data ), 0, '/' );

		$redirect = invite_anyone_get_user_invite_new_members_url( bp_displayed_user_id() );
		bp_core_redirect( $redirect );
		die();
	}

	if ( empty( $emails ) ) {
		bp_core_add_message( __( 'You didn\'t include any email addresses!', 'invite-anyone' ), 'error' );

		$redirect = invite_anyone_get_user_invite_new_members_url( bp_loggedin_user_id() );
		bp_core_redirect( $redirect );
		die();
	}

	// Max number of invites sent
	$limit_total_invites = ! empty( $options['email_limit_invites_toggle'] ) && 'no' !== $options['email_limit_invites_toggle'];
	if ( $limit_total_invites && ! current_user_can( 'delete_others_pages' ) ) {
		$sent_invites            = invite_anyone_get_invitations_by_inviter_id( bp_loggedin_user_id() );
		$sent_invites_count      = (int) $sent_invites->post_count;
		$remaining_invites_count = (int) $options['limit_invites_per_user'] - $sent_invites_count;

		if ( count( $emails ) > $remaining_invites_count ) {
			$returned_data['error_message'] = sprintf(
				// translators: %s is the number of remaining invites
				_n( 'You are only allowed to invite %s more person. Please remove some addresses and try again', 'You are only allowed to invite %s more people. Please remove some addresses and try again', $remaining_invites_count, 'invite-anyone' ),
				$remaining_invites_count
			);

			$returned_data['error_emails'] = $emails;

			setcookie( 'invite-anyone', wp_json_encode( $returned_data ), 0, '/' );
			$redirect = invite_anyone_get_user_invite_new_members_url( bp_loggedin_user_id() );
			bp_core_redirect( $redirect );
			die();
		}
	}

	// Turn the CS emails into an array so that they can be matched against the main list
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$cs_emails_raw = isset( $_POST['cloudsponge-emails'] ) ? sanitize_text_field( wp_unslash( $_POST['cloudsponge-emails'] ) ) : '';
	if ( $cs_emails_raw ) {
		$cs_emails = explode( ',', $cs_emails_raw );
	}

	// validate email addresses
	foreach ( $emails as $key => $email ) {
		$check = invite_anyone_validate_email( $email );
		switch ( $check ) {

			case 'opt_out' :
				// translators: %s is an email address
				$returned_data['error_message'] .= sprintf( __( '<strong>%s</strong> has opted out of email invitations from this site.', 'invite-anyone' ), $email );
				break;

			case 'used' :
				// translators: %s is an email address
				$returned_data['error_message'] .= sprintf( __( '<strong>%s</strong> is already a registered user of the site.', 'invite-anyone' ), $email );
				break;

			case 'unsafe' :
				// translators: %s is the email address
				$returned_data['error_message'] .= sprintf( __( '<strong>%s</strong> is not a permitted email address.', 'invite-anyone' ), $email );
				break;

			case 'invalid' :
				// translators: %s is an email address
				$returned_data['error_message'] .= sprintf( __( '<strong>%s</strong> is not a valid email address. Please make sure that you have typed it correctly.', 'invite-anyone' ), $email );
				break;

			case 'limited_domain' :
				// translators: %s is the email address
				$returned_data['error_message'] .= sprintf( __( '<strong>%s</strong> is not a permitted email address. Please make sure that you have typed the domain name correctly.', 'invite-anyone' ), $email );
				break;
		}

		// If there was an error in validation, we won't process this email
		if ( 'okay' !== $check ) {
			$returned_data['error_message'] .= '<br />';
			$returned_data['error_emails'][] = $email;
			unset( $emails[ $key ] );
		}
	}

	if ( ! empty( $emails ) ) {

		unset( $message, $to );

		/* send and record invitations */

		do_action( 'invite_anyone_process_addl_fields' );

		$groups   = ! empty( $data['invite_anyone_groups'] ) ? $data['invite_anyone_groups'] : array();
		$is_error = 0;

		$do_bp_email = true === function_exists( 'bp_send_email' ) && true === ! apply_filters( 'bp_email_use_wp_mail', false );

		foreach ( $emails as $email ) {
			$subject = stripslashes( wp_strip_all_tags( $data['invite_anyone_custom_subject'] ) );

			if ( $do_bp_email ) {
				$custom_message = stripslashes( $data['invite_anyone_custom_message'] );
				$accept_url     = invite_anyone_get_accept_url( $email );
				$opt_out_url    = invite_anyone_get_opt_out_url( $email );
			} else {
				$custom_message = stripslashes( wp_strip_all_tags( $data['invite_anyone_custom_message'] ) );
			}

			$footer = invite_anyone_process_footer( $email );
			$footer = invite_anyone_wildcard_replace( $footer, $email );

			$message  = $custom_message . '

================
';
			$message .= $footer;

			/**
			 * Filters the email address of an outgoing email.
			 *
			 * @since 1.3.20 Added the $data parameter.
			 *
			 * @param string $email Email address.
			 * @param array  $data  Data about the information.
			 */
			$to = apply_filters( 'invite_anyone_invitee_email', $email, $data );

			/**
			 * Filters the subject line of an outgoing email.
			 *
			 * @since 1.3.20 Added the $email and $data parameters.
			 *
			 * @param string $subject The email subject.
			 * @param array  $data    Data about the information.
			 * @param string $email   Email address of the invited user.
			 */
			$subject = apply_filters( 'invite_anyone_invitation_subject', $subject, $data, $email );

			/**
			 * Filters the contents of an outgoing plaintext email.
			 *
			 * @since 1.3.20 Added the $email and $data parameters.
			 *
			 * @param string $message The email message.
			 * @param array  $data    Data about the information.
			 * @param string $email   Email address of the invited user.
			 */
			$message = apply_filters( 'invite_anyone_invitation_message', $message, $data, $email );

			// BP 2.5+
			if ( $do_bp_email ) {
				$bp_email_args = array(
					'tokens'  => array(
						'ia.subject'           => $subject,
						'ia.content'           => $custom_message,
						'ia.content_plaintext' => $message,
						'ia.accept_url'        => $accept_url,
						'ia.opt_out_url'       => $opt_out_url,
						'recipient.name'       => $to,
					),
					'subject' => $subject,
					'content' => $message,
				);

				add_filter( 'bp_email_get_salutation', 'invite_anyone_replace_bp_email_salutation', 10, 2 );
				bp_send_email( 'invite-anyone-invitation', $to, $bp_email_args );

				remove_filter( 'bp_email_get_salutation', 'invite_anyone_replace_bp_email_salutation', 10, 2 );

			} else {
				wp_mail( $to, $subject, $message );
			}

			// Determine whether this address came from CloudSponge
			$is_cloudsponge = isset( $cs_emails ) && in_array( $email, $cs_emails, true );

			invite_anyone_record_invitation( $bp->loggedin_user->id, $email, $message, $groups, $subject, $is_cloudsponge );

			do_action( 'sent_email_invite', $bp->loggedin_user->id, $email, $groups );

			unset( $message, $to );
		}

		// Set a success message

		// translators: %s is a comma-separated list of email addresses
		$success_message = sprintf( __( 'Invitations were sent successfully to the following email addresses: %s', 'invite-anyone' ), implode( ', ', $emails ) );
		bp_core_add_message( $success_message );

		do_action( 'sent_email_invites', $bp->loggedin_user->id, $emails, $groups );
	} else {
		$success_message = sprintf( __( 'Please correct your errors and resubmit.', 'invite-anyone' ) );
		bp_core_add_message( $success_message, 'error' );
	}

	// If there are errors, redirect to the Invite New Members page
	if ( ! empty( $returned_data['error_emails'] ) ) {
		setcookie( 'invite-anyone', wp_json_encode( $returned_data ), 0, '/' );
		$redirect = invite_anyone_get_user_invite_new_members_url( bp_loggedin_user_id() );
		bp_core_redirect( $redirect );
		die();
	}

	return true;
}

/**
 * Replaces the customized salutation in outgoing invitation emails.
 *
 * There's no first name, so we just say 'Hello' instead.
 *
 * @since 1.4.0
 *
 * @param string $salutation
 * @param array  $settings
 * @return string
 */
function invite_anyone_replace_bp_email_salutation( $salutation, $settings ) {
	/**
	 * Filters the IA email salutation.
	 *
	 * @since 1.4.0
	 *
	 * @param string $ia_salutation Salutation from IA.
	 * @param string $salutation    Salutation from BP.
	 * @param array  $settings      Email settings.
	 */
	return apply_filters( 'invite_anyone_replace_bp_email_salutation', _x( 'Hello,', 'Invite Anyone email salutation', 'invite-anyone' ), $salutation, $settings );
}

/**
 * Redirect from old 'accept-invitation' and 'opt-out' email formats
 *
 * Invite Anyone used to use the current_action and action_variables for
 * subpages of the registration screen. This caused some problems with URL
 * encoding, and it also broke with BP 2.1. In IA 1.3.4, this functionality
 * was moved to URL arguments; the current function handles backward
 * compatibility with the old addresses.
 *
 * @since 1.3.4
 */
function invite_anyone_accept_invitation_backward_compatibility() {
	if ( ! bp_is_register_page() ) {
		return;
	}

	if ( ! bp_current_action() ) {
		return;
	}

	$action = bp_current_action();

	if ( ! in_array( $action, array( 'accept-invitation', 'opt-out' ), true ) ) {
		return;
	}

	$redirect_to = add_query_arg( 'iaaction', $action, bp_get_signup_page() );

	$email = bp_action_variable( 0 );
	$email = str_replace( ' ', '+', $email );

	if ( ! empty( $email ) ) {
		$redirect_to = add_query_arg( 'email', $email, $redirect_to );
	}

	bp_core_redirect( $redirect_to );
	die();
}
add_action( 'bp_actions', 'invite_anyone_accept_invitation_backward_compatibility', 0 );

/**
 * Is this the 'accept-invitation' page?
 *
 * @since 1.3.4
 *
 * @return bool
 */
function invite_anyone_is_accept_invitation_page() {
	$retval = false;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( bp_is_register_page() && ! empty( $_GET['iaaction'] ) && 'accept-invitation' === urldecode( $_GET['iaaction'] ) ) {
		$retval = true;
	}

	return apply_filters( 'invite_anyone_is_accept_invitation_page', $retval );
}

function invite_anyone_bypass_registration_lock() {
	global $bp;

	if ( ! invite_anyone_is_accept_invitation_page() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$email = isset( $_GET['email'] ) ? urldecode( $_GET['email'] ) : '';
	if ( empty( $email ) ) {
		return;
	}

	$options = invite_anyone_options();

	if ( empty( $options['bypass_registration_lock'] ) || 'yes' !== $options['bypass_registration_lock'] ) {
		return;
	}

	// Check to make sure that it's actually a valid email
	$ia_obj = invite_anyone_get_invitations_by_invited_email( $email );

	if ( ! $ia_obj->have_posts() ) {
		bp_core_add_message( __( "We couldn't find any invitations associated with this email address.", 'invite-anyone' ), 'error' );
		return;
	}

	// To support old versions of BP, we have to force the overloaded
	// site_options property in some cases
	if ( is_multisite() ) {
		$site_options = $bp->site_options;
		if ( ! empty( $bp->site_options['registration'] ) && 'blog' === $bp->site_options['registration'] ) {
			$site_options['registration'] = 'all';
		} elseif ( ! empty( $bp->site_options['registration'] ) && 'none' === $bp->site_options['registration'] ) {
			$site_options['registration'] = 'user';
		}
		$bp->site_options = $site_options;

		add_filter( 'bp_get_signup_allowed', '__return_true' );
	} else {
		add_filter( 'option_users_can_register', '__return_true' );
	}
}
add_action( 'wp', 'invite_anyone_bypass_registration_lock', 1 );

/**
 * Double check that passed email address matches an existing invitation when registration lock bypass is on.
 *
 * @since 1.2
 *
 * @param array $results Error results from user signup validation
 * @return array
 */
function invite_anyone_check_invitation( $results ) {
	if ( ! invite_anyone_is_accept_invitation_page() ) {
		return $results;
	}

	// Check to make sure that it's actually a valid email
	$ia_obj = invite_anyone_get_invitations_by_invited_email( $results['user_email'] );

	if ( ! $ia_obj->have_posts() ) {
		$errors = new WP_Error();
		$errors->add( 'user_email', __( "We couldn't find any invitations associated with this email address.", 'invite-anyone' ) );
		$results['errors'] = $errors;
	}

	return $results;
}
add_filter( 'bp_core_validate_user_signup', 'invite_anyone_check_invitation' );

function invite_anyone_validate_email( $user_email ) {

	$status = 'okay';

	$user = get_user_by( 'email', $user_email );
	if ( invite_anyone_check_is_opt_out( $user_email ) ) {
		$status = 'opt_out';
	} elseif ( $user ) {
		$status = 'used';
	} elseif ( function_exists( 'is_email_address_unsafe' ) && is_email_address_unsafe( $user_email ) ) {
		$status = 'unsafe';
	} elseif ( function_exists( 'is_email' ) && ! is_email( $user_email ) ) {
		$status = 'invalid';
	}

	if ( function_exists( 'get_site_option' ) ) {
		$limited_email_domains = get_site_option( 'limited_email_domains' );
		if ( $limited_email_domains ) {
			if ( is_array( $limited_email_domains ) && ! empty( $limited_email_domains ) ) {
				$emaildomain = strtolower( substr( $user_email, 1 + strpos( $user_email, '@' ) ) );

				$is_valid_domain = false;
				foreach ( $limited_email_domains as $led ) {
					if ( strtolower( $led ) === $emaildomain ) {
						$is_valid_domain = true;
						break;
					}
				}

				if ( ! $is_valid_domain ) {
					$status = 'limited_domain';
				}
			}
		}
	}

	return apply_filters( 'invite_anyone_validate_email', $status, $user_email );
}

/**
 * Catches attempts to reaccept an invitation, and redirects appropriately
 *
 * If you attempt to access the register page when logged in, you get bounced
 * to the home page. This is a BP feature. Because accept-invitation is a
 * subpage of register, this happens for accept-invitation pages as well.
 * However, people are more likely to try to visit this page than the vanilla
 * register page, because they've gotten an email inviting them to the site.
 *
 * So this function catches logged-in visits to /register/accept-invitation,
 * and if the email address in the URL matches the logged-in user's email
 * address, redirects them to their invite-anyone page to see the a message.
 *
 * @since 1.0.20
 */
function invite_anyone_already_accepted_redirect( $redirect ) {
	global $bp;

	if ( ! invite_anyone_is_accept_invitation_page() ) {
		return $redirect;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['email'] ) ) {
		return $redirect;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$reg_email = urldecode( $_GET['email'] );

	if ( bp_core_get_user_email( bp_loggedin_user_id() ) !== $reg_email ) {
		return $redirect;
	}

	$redirect_base = bp_members_get_user_url(
		bp_loggedin_user_id(),
		bp_members_get_path_chunks( [ buddypress()->invite_anyone->slug ] )
	);

	$redirect = add_query_arg( 'already', 'accepted', $redirect_base );

	return $redirect;
}
add_filter( 'bp_loggedin_register_page_redirect_to', 'invite_anyone_already_accepted_redirect' );

/** BP emails ****************************************************************/

/**
 * Install Invite Anyone emails during email installation routine for BuddyPress.
 *
 * @since 1.4.0
 *
 * @param bool $post_exists_check Should we check to see if our email post types exist before installing?
 *                                Default: false.
 */
function invite_anyone_install_emails( $post_exists_check = false ) {
	// No need to check if our post types exist.
	if ( ! $post_exists_check ) {
		invite_anyone_set_email_type( 'invite-anyone-invitation', false );

		// Only create email post types if they do not exist.
	} else {
		switch_to_blog( bp_get_root_blog_id() );

		$mail_types = array( 'invite-anyone-invitation' );

		// Try to fetch email posts with our taxonomy.
		$emails = get_posts(
			array(
				'fields'           => 'ids',
				'post_status'      => 'publish',
				'post_type'        => bp_get_email_post_type(),
				'posts_per_page'   => 1,
				'suppress_filters' => false,
				'tax_query'        => array(
					'relation' => 'OR',
					array(
						'taxonomy' => bp_get_email_tax_type(),
						'field'    => 'slug',
						'terms'    => $mail_types,
					),
				),
			)
		);

		// See if our taxonomies are attached to our email posts.
		$found = array();
		foreach ( $emails as $post_id ) {
			$tax   = wp_get_object_terms( $post_id, bp_get_email_tax_type(), array( 'fields' => 'slugs' ) );
			$found = array_merge( $found, (array) $tax );
		}

		restore_current_blog();

		// Find out if we need to create any posts.
		$to_create = array_diff( $mail_types, $found );
		if ( empty( $to_create ) ) {
			return;
		}

		// Create posts with our email types.
		foreach ( $to_create as $email_type ) {
			invite_anyone_set_email_type( $email_type, false );
		}
	}
}
add_action( 'bp_core_install_emails', 'invite_anyone_install_emails' );

/**
 * Sets the email situation type for use in Invite Anyone.
 *
 * Only applicable for BuddyPress 2.5+.
 *
 * @since 1.4.0
 *
 * @param string $email_type The email type to fetch.
 * @param bool   $term_check Check if our email term exists before creating our specific email
 *                           situation. Default: true.
 */
function invite_anyone_set_email_type( $email_type, $term_check = true ) {
	$switched = false;

	if ( false === bp_is_root_blog() ) {
		$switched = true;
		switch_to_blog( bp_get_root_blog_id() );
	}

	if ( true === $term_check ) {
		$term = term_exists( $email_type, bp_get_email_tax_type() );
	} else {
		$term = 0;
	}

	// Term already exists so don't do anything.
	if ( true === $term_check && 0 !== $term && null !== $term ) {
		if ( true === $switched ) {
			restore_current_blog();
		}

		return;

		// Create our email situation.
	} else {
		// Set up default email content depending on the email type.
		switch ( $email_type ) {
			// Site invitations.
			case 'invite-anyone-invitation' :
				/* translators: do not remove {} brackets or translate its contents. */
				$post_title = __( '[{{{site.name}}}] {{{ia.subject}}}', 'invite-anyone' );

				/* translators: do not remove {} brackets or translate its contents. */
				$html_content = __( '{{{ia.content}}}<br /><hr><a href="{{{ia.accept_url}}}">Accept or reject this invitation</a> &middot; <a href="{{{ia.opt_out_url}}}">Opt out of future invitations</a>', 'invite-anyone' );

				/* translators: do not remove {} brackets or translate its contents. */
				$plaintext_content = __( '{{{ia.content_plaintext}}}', 'invite-anyone' );

				$situation_desc = __( 'A user is invited to join the site by email. Used by the Invite Anyone plugin.', 'invite-anyone' );

				break;
		}

		// Sanity check!
		if ( false === isset( $post_title ) ) {
			if ( true === $switched ) {
				restore_current_blog();
			}

			return;
		}

		$id = $email_type;

		$defaults = array(
			'post_status' => 'publish',
			'post_type'   => bp_get_email_post_type(),
		);

		$email = array(
			'post_title'   => $post_title,
			'post_content' => $html_content,
			'post_excerpt' => $plaintext_content,
		);

		// Email post content.
		$post_id = wp_insert_post( bp_parse_args( $email, $defaults, 'install_email_' . $id ) );

		// Save the situation.
		if ( ! is_wp_error( $post_id ) ) {
			$tt_ids = wp_set_object_terms( $post_id, $id, bp_get_email_tax_type() );

			// Situation description.
			if ( ! is_wp_error( $tt_ids ) ) {
				$term = get_term_by( 'term_taxonomy_id', (int) $tt_ids[0], bp_get_email_tax_type() );
				wp_update_term(
					(int) $term->term_id,
					bp_get_email_tax_type(),
					array(
						'description' => $situation_desc,
					)
				);
			}
		}
	}

	if ( true === $switched ) {
		restore_current_blog();
	}
}
