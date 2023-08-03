<?php do_action( 'template_notices' ); ?>

<?php
$pending_invites = \OpenLab\Connections\Invitation::get(
	[
		'invitee_group_id' => bp_get_current_group_id(),
		'pending_only'     => true,
	]
);
?>

<div class="openlab-connections">
	<?php get_template_part( 'buddypress/groups/single/connections/header' ); ?>

	<form class="form-panel">
		<div class="panel panel-default">
			<div class="panel-heading">Invitations</div>
			<div class="panel-body">
				<p>Accept or decline invitations to connect. Manage the activity shared out to your connected groups in <a href="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) . 'connections/' ); ?>">Manage Connections</a>.</p>

				<?php if ( $pending_invites ) : ?>
					<div class="pending-invitations connection-invitations">
						<div class="pending-invitation connection-invitation connection-invitation-header">
							<div class="actions"><span class="sr-only"><?php esc_html_e( 'Actions', 'openlab-connections' ); ?></span></div>
							<div class="group"><?php esc_html_e( 'Group', 'text-domain' ); ?></div>
							<div class="received"><?php esc_html_e( 'Received', 'text-domain' ); ?></div>
						</div>

						<?php foreach ( $pending_invites as $pending_invite ) : ?>
							<?php

							$group = groups_get_group( $pending_invite->get_invitee_group_id() );

							$date_received = '0000-00-00 00:00:00' === $pending_invite->get_date_created() ? '' : date_i18n( get_option( 'date_format' ), strtotime( $pending_invite->get_date_created() ) );

							$invitation_id = $pending_invite->get_invitation_id();

							$accept_url = wp_nonce_url( $pending_invite->get_accept_url(), 'accept-invitation-' . $invitation_id );;
							$reject_url = wp_nonce_url( $pending_invite->get_reject_url(), 'reject-invitation-' . $invitation_id );;

							?>

							<div class="pending-invitation connection-invitation">
								<div class="actions">
									<a class="btn btn-primary" href="<?php echo esc_url( $accept_url ); ?>"><?php esc_html_e( 'Accept' ); ?></a>
									<a class="btn btn-default" href="<?php echo esc_url( $reject_url ); ?>"><?php esc_html_e( 'Reject' ); ?></a>
								</div>

								<div class="group"><?php echo esc_html( $group->name ); ?></div>
								<div class="received"><?php echo esc_html( $date_received ); ?></div>

							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p><?php esc_html_e( 'You have no pending invitations at this time.', 'openlab-connections' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</form>
</div>
