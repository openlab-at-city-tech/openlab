<?php
$group_label_uc = openlab_get_group_type_label( 'case=upper' );
?>

<?php do_action( 'template_notices' ); ?>

<div class="panel panel-default">
	<div class="panel-heading">Make a Connection</div>

	<div class="panel-body">
		<form method="post">
			<p><strong>Search for an OpenLab group</strong></p>
			<p>Start typing the name of the OpenLab group or copy/paste the group's URL.</p>
			<label for="new-connection-search" class="sr-only">Type Group Name or Paste URL</label>
			<input type="text" class="form-control" id="new-connection-search" />
			<input id="new-connection-group-id" name="new-connection-group-id" type="hidden" value="" />

			<div id="send-invitations" style="display: none;">
				<p><strong>Send Invitations</strong></p>
				<p>These groups will be sent an invitation to connect to your <?php echo esc_html( $group_label_uc ); ?>.</p>
				<div id="send-invitations-list" class="invites group-list item-list row">

				</div>
			</div>
		</form>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">Sent Invites</div>
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
