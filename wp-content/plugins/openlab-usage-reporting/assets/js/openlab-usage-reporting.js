(function( $ ) {
	var $progressbar, $progressMessage, pbar, startDate, endDate, currentReportId;

	function datepicker_init() {
		$( '.olur-datepicker' ).datepicker();
	}

	function progressbar_init() {
		pbar = $progressbar.progressbar();
	}

	function launchAsync() {
		startDate = $( '#olur-start' ).val();
		endDate = $( '#olur-end' ).val();
		currentReportId = Math.floor(Date.now() / 1000);

		asyncBatch();
	}

	function asyncBatch() {
		$.ajax({
			url: ajaxurl + '?action=olur_batch',
			method: 'POST',
			data: {
				currentReportId,
				startDate,
				endDate
			},
			success: function(response) {
				$progressbar.progressbar( {
					value: response.data.pct
				} );

				if ( response.data.more ) {
					asyncBatch();
				} else {
					$progressMessage.html( 'Download file: <a href="' + response.data.file + '">' + response.data.file + '</a>' );
				}
			}
		});
	}

	$( document ).ready( function() {
		$progressbar = $( '#progressbar' );
		$progressbar.hide();

		$progressMessage = $( '#progress-message' );
		$progressMessage.hide();

		datepicker_init();
		progressbar_init();

		$( '.olur-dates input[type="submit"]' ).on( 'click', function(e) {
			e.preventDefault();

			$progressbar.show();
			$progressMessage.show();
			launchAsync()
		});
	} );
}(jQuery));
