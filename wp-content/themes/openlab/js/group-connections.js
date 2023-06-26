(function($){
	const $sendInvitationsWrap = $('#send-invitations');
	const $sendInvitationsList = $('#send-invitations-list');

	$('#new-connection-search').autocomplete({
		minLength: 2,
		source: ajaxurl + '?action=openlab_connection_group_search',
		select: function( event, ui ) {
			// Don't allow dupes.
			if ( document.getElementById( 'connection-invitation-group-' + ui.item.groupId ) ) {
				return false;
			}

			const inviteTemplate = wp.template( 'openlab-connection-invitation' );
			const inviteMarkup = inviteTemplate( ui.item );
			$sendInvitationsList.append( inviteMarkup );

			showHideSendInvitations();

			return false;
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append( "<div><strong>" + item.groupName + "</strong><br>" + item.groupUrl + "</div>" )
			.appendTo( ul );
	};

	$sendInvitationsList.on( 'click', '.remove-connection-invitation', function( e ) {
		e.target.closest( '.group-item' ).remove();

		showHideSendInvitations();

		return false;
	} );

	const showHideSendInvitations = () => {
		if ( $sendInvitationsList.children().length > 0 ) {
			$sendInvitationsWrap.show();
		} else {
			$sendInvitationsWrap.hide();
		}
	}

})(jQuery)
