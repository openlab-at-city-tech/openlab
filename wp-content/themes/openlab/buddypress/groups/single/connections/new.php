<?php
$group_label_uc = openlab_get_group_type_label( 'case=upper' );

$sent_invites = \OpenLab\Connections\Invitation::get(
	[
		'inviter_group_id' => bp_get_current_group_id(),
		'pending_only'     => true,
	]
);

?>

<?php do_action( 'template_notices' ); ?>

<div class="openlab-connections">
	<?php get_template_part( 'buddypress/groups/single/connections/header' ); ?>

	<form method="post" class="form-panel">
		<div class="panel panel-default">
			<div class="panel-heading">Make a Connection</div>

			<div class="panel-body">
				<p><strong>Search for an OpenLab group</strong></p>
				<p>Start typing the name of the OpenLab group or copy/paste the group's URL.</p>
				<label for="new-connection-search" class="sr-only">Type Group Name or Paste URL</label>
				<input type="text" class="form-control" id="new-connection-search" />
				<input id="new-connection-group-id" name="new-connection-group-id" type="hidden" value="" />

				<div id="send-invitations" style="display: none;">
					<p><strong>Send Invitations</strong></p>
					<p>These groups will be sent an invitation to connect to your <?php echo esc_html( $group_label_uc ); ?>.</p>
					<div id="send-invitations-list" class="invites group-list item-list row"></div>

					<?php wp_nonce_field( 'openlab-connection-invitations', 'openlab-connection-invitations-nonce' ); ?>
					<input type="submit" value="Send Invites" class="btn btn-primary btn-margin btn-margin-top" />
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Pending Invites</div>

			<div class="panel-body">

				<?php if ( $sent_invites ) : ?>
					<p>You have sent invitations to the following groups:</p>
						<div class="sent-invitations connection-invitations">
							<div class="sent-invitation connection-invitation connection-invitation-header">
								<div class="actions"><span class="sr-only"><?php esc_html_e( 'Delete', 'text-domain' ); ?></span></div>
								<div class="group"><?php esc_html_e( 'Group', 'text-domain' ); ?></div>
								<div class="sent"><?php esc_html_e( 'Sent', 'text-domain' ); ?></div>
							</div>

							<?php foreach ( $sent_invites as $invite ) : ?>
								<?php

								$group = groups_get_group( $invite->get_invitee_group_id() );

								$date_sent = '0000-00-00 00:00:00' === $invite->get_date_created() ? '' : date_i18n( get_option( 'date_format' ), strtotime( $invite->get_date_created() ) );

								$delete_url = bp_get_group_permalink( groups_get_current_group() ) . 'connections/new/';
								$delete_url = add_query_arg( 'delete-invitation', $invite->get_invitation_id(), $delete_url );
								$delete_url = wp_nonce_url( $delete_url, 'delete-invitation-' . $invite->get_invitation_id() );

								?>

								<div class="sent-invitation connection-invitation">
									<div class="actions"><a href="<?php echo esc_url( $delete_url ); ?>" class="delete-invite" onclick="return confirm('Are you sure you want to delete this invitation?')" data-invitation-id="<?php echo esc_attr( $invite->get_invitation_id() ); ?>">x<span class="sr-only">Delete Invitation</span></a></div>
									<div class="group"><?php echo esc_html( $group->name ); ?></div>
									<div class="sent"><?php echo esc_html( $date_sent ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
				<?php else : ?>
					<p>None of your connection invitations are awaiting a response.</p>
				<?php endif; ?>
			</div>
		</div>
	</form>
</div>

<script type="text/html" id="tmpl-openlab-connection-invitation">
	<div id="connection-invitation-group-{{ data.groupId }}" class="group-item col-xs-12">
		<div class="group-item-wrapper">
			<div class="row info-row">
				<div class="item-avatar alignleft col-xs-7">
					<a href="{{ data.groupUrl }}"><img class="img-responsive" src="{{ data.groupAvatar }}"" alt="{{ data.groupName }}"></a>
				</div>
				<div class="item col-xs-17">
					<p class="item-title h2"><a class="no-deco truncate-on-the-fly" href="{{ data.groupUrl }}" data-basevalue="65" data-minvalue="20" data-basewidth="280" style="opacity: 1;">{{ data.groupName }}</a></p>

					<div class="action invite-member-actions">
						<button class="remove-connection-invitation link-button">Remove invite</a>
					</div>
				</div>

				<input type="hidden" name="invitation-group-ids[]" value="{{ data.groupId }}" />
			</div>
		</div>
	</div>
</script>
