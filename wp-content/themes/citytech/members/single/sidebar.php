<?php if ( is_user_logged_in() && openlab_is_my_profile() ) : ?>

        <h2 class="sidebar-title">My OpenLab</h2>

        <div id="item-buttons">
		<?php do_action( 'cuny_bp_profile_menus' ); ?>
	</div><!-- #item-buttons -->

<?php else : ?>

	<h2 class="sidebar-title">People</h2>

	<?php bp_add_friend_button( openlab_fallback_user(), bp_loggedin_user_id() ) ?>

	<?php echo bp_get_button( array(
		'id'                => 'private_message',
		'component'         => 'messages',
		'must_be_logged_in' => true,
		'block_self'        => true,
		'wrapper_id'        => 'send-private-message',
		'link_href'         => bp_get_send_private_message_link(),
		'link_title'        => __( 'Send a private message to this user.', 'buddypress' ),
		'link_text'         => __( 'Send a Message', 'buddypress' ),
		'link_class'        => 'send-message',
	) ) ?>

<?php endif ?>

<?php /* Portfolio links */ ?>
<div class="sidebar-widget" id="portfolio-sidebar-widget">
	<?php if ( openlab_user_has_portfolio() ) : ?>
		<h4 class="sidebar-header">
			<a href="<?php openlab_user_portfolio_url() ?>"><?php openlab_portfolio_label( 'case=upper' ) ?> Site</a>
		</h4>

		<ul class="sidebar-sublinks portfolio-sublinks">
			<li class="portfolio-site-link">
				<a href="<?php openlab_user_portfolio_url() ?>">Site</a>
			</li>

			<li class="portfolio-profile-link">
				<a href="<?php openlab_user_portfolio_profile_url() ?>"><?php openlab_portfolio_label( 'case=upper' ) ?></a>
			</li>

			<?php if ( openlab_is_my_profile() && openlab_user_portfolio_site_is_local() ) : ?>
				<li class="portfolio-dashboard-link">
					<a href="<?php openlab_user_portfolio_url() ?>/wp-admin">Dashboard</a>
				</li>
			<?php endif ?>
		</ul>

	<?php else : ?>
		<h4 class="sidebar-header">
			<a href="<?php openlab_portfolio_creation_url() ?>">+ Create <?php openlab_portfolio_label( 'leading_a=1&case=upper' ) ?></a>
		</h4>
	<?php endif ?>
</div>
<?php /* End portfolio links */ ?>

<?php /* Recent Account Activity / Recent Friend Activity */ ?>
<?php if ( !bp_is_user_messages() ) : ?>

	<?php

	$activity_args = array(
		'per_page'    => openlab_is_my_profile() ? 4 : 2, // Legacy. Not sure why
		'scope'       => bp_is_user_friends() ? 'friends' : '',
		'show_hidden' => openlab_is_my_profile()
	);

	?>

	<?php if ( bp_is_user_friends() ) : ?>
		<h4 class="sidebar-header">Recent Friend Activity</h4>
	<?php else : ?>
		<h4 class="sidebar-header">Recent Account Activity</h4>
	<?php endif ?>

	<?php if ( bp_has_activities( $activity_args ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
			<div>
			<?php while ( bp_activities() ) : bp_the_activity(); ?>

				<div class="activity-avatar">
					<a href="<?php bp_activity_user_link() ?>">
						<?php bp_activity_avatar( 'type=full&width=100&height=100' ) ?>
					</a>
				</div>

				<div class="activity-content">

					<div class="activity-header">
						<?php bp_activity_action() ?>
					</div>

					<?php if ( bp_activity_has_content() ) : ?>
						<div class="activity-inner">
							<?php bp_activity_content_body() ?>
						</div>
					<?php endif; ?>

					<?php do_action( 'bp_activity_entry_content' ) ?>

				</div>
				<hr style="clear:both" />

			<?php endwhile; ?>
			</div>
		</ul>

	<?php else : ?>
		<ul id="activity-stream" class="activity-list item-list">
			<div>
			<div id="message" class="info">
				<p><?php _e( 'No recent activity.', 'buddypress' ) ?></p>
			</div>
			</div>
		</ul>
	<?php endif; ?>
<?php endif ?>