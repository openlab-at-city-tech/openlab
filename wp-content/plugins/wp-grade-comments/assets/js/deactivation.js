(function () {
	var message = window.OLGCDeactivate.message;
	var pluginRow = document.querySelector( '[data-slug="wp-grade-comments"]' );
	var deactivateBtn = pluginRow.querySelector( '.deactivate > a' );

	deactivateBtn.addEventListener( 'click', function( event ) {
		var deactivate = window.confirm( message );

		if ( ! deactivate ) {
			event.preventDefault();
			return;
		}
	} );
})();
