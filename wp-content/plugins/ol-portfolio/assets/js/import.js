(function ($) {
	var evtSource = new EventSource( ImportData.url );

	evtSource.onmessage = function ( message ) {
		var data = JSON.parse( message.data );
		switch ( data.action ) {
			case 'complete':
				evtSource.close();
				var import_status_msg = jQuery('#import-status-message');
				import_status_msg.find('p').text( ImportData.strings.complete );
				import_status_msg.removeClass('notice-info');
				import_status_msg.addClass('notice-success');
				break;
		}
	};

	evtSource.addEventListener( 'log', function ( message ) {
		var data = JSON.parse( message.data );
		var row = document.createElement('tr');
		var level = document.createElement( 'td' );
		level.appendChild( document.createTextNode( data.level ) );
		row.appendChild( level );

		var message = document.createElement( 'td' );
		message.appendChild( document.createTextNode( data.message ) );
		row.appendChild( message );

		jQuery('#import-log').append( row );
	});
})(jQuery);
