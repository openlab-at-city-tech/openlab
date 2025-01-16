/* global jQuery */
(function($) {
	$(function() {
		let modal = $( '#epkb-deactivate-modal' );
		let deactivateLink = $( '#the-list' ).find( '[data-slug="echo-knowledge-base"] span.deactivate a' );

		// Open modal
		deactivateLink.on( 'click', function( e ) {
			e.preventDefault();

			modal.addClass( 'modal-active' );
			deactivateLink = $( this ).attr( 'href' );
			modal.find( 'a.epkb-deactivate-skip-modal' ).attr( 'href', deactivateLink );
		});

		// Close modal; Cancel
		modal.on( 'click', 'button.epkb-deactivate-cancel-modal', function( e ) {
			e.preventDefault();
			modal.removeClass( 'modal-active' );
		});

		// Reason change
		modal.on( 'click', 'input[type="radio"]', function () {
			let parent = $( this ).parents( 'li' );
			let inputValue = $( this ).val();

			$( 'ul.epkb-deactivate-reasons li' ).removeClass( 'epkb-deactivate-reason-selected' );

			parent.addClass( 'epkb-deactivate-reason-selected' );

			$( '.epkb-deactivate-modal-reason-inputs' ).removeClass( 'inputs-active' );
			$( '.epkb-deactivate-modal-reason-inputs--' + inputValue ).addClass( 'inputs-active' ).find( 'textarea' ).focus();
		});

		// Click submit button
		modal.on( 'click', '.epkb-deactivate-submit-modal', function( e ) {
			e.preventDefault();
			
			// set required attr for visible required fields only
			modal.find( 'input[data-required="true"]' ).removeAttr( 'required' );
			modal.find('.inputs-active input[data-required="true"]').prop( 'required', true );

			// submit form
			modal.find( 'form#epkb-deactivate-feedback-dialog-form' ).trigger( 'submit' );
		});

		// Submit form
		modal.on( 'submit', 'form#epkb-deactivate-feedback-dialog-form', function( e ) {
			e.preventDefault();

			if ( ! this.reportValidity() ) {
				return;
			}

			let button = $( this ).find( '.epkb-deactivate-submit-modal' );

			if ( button.hasClass( 'disabled' ) ) {
				return;
			}

			let formData = $( '#epkb-deactivate-feedback-dialog-form', modal ).serialize();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: formData,
				beforeSend: function() {
					button.addClass( 'disabled' );
					button.text( 'Processing...' );
				},
				complete: function() {
					window.location.href = deactivateLink;
				}
			});
		});

	});
}(jQuery));