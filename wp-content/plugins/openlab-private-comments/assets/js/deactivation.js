(function () {
	var message = window.OLPCDeactivate.message;
	var pluginRow = document.querySelector( '[data-slug="openlab-private-comments"]' );
	var deactivateBtn = pluginRow.querySelector( '.deactivate > a' );

	deactivateBtn.addEventListener( 'click', function( event ) {
		var deactivate = window.confirm( message );

		if ( ! deactivate ) {
			event.preventDefault();
			return;
		}
	} );
})();
