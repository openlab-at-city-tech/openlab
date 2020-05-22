(function($){
	var $attachmentField,
		$commentForm,
		$typeNotice,
		$uploadSizeNotice;

	function enableForm() {
		$commentSubmit.removeAttr( 'disabled' );
	}

	function disableForm() {
		$commentSubmit.attr( 'disabled', 'disabled' );
	}

	function addSizeNotice() {
		$uploadSizeNotice.addClass( 'has-error' );
	}

	function removeSizeNotice() {
		$uploadSizeNotice.removeClass( 'has-error' );
	}

	function addTypeNotice() {
		$typeNotice.addClass( 'has-error' );
	}

	function removeTypeNotice() {
		$typeNotice.removeClass( 'has-error' );
	}

	$(document).ready(function(){
		$attachmentField = $('input#attachment');
		$commentSubmit = $attachmentField.closest( 'form' ).find( 'input[type="submit"]' );
		$typeNotice = $('.comment-attachment-allowed-file-types');
		$uploadSizeNotice = $('.comment-attachment-max-upload-size');

		$attachmentField.on('change',function(e){
			var file = e.target.files[0];

			var hasError = false;

			if ( 'undefined' !== typeof file ) {
				if ( file.size > OpenLabDCOCommentAttachment.max_upload_size ) {
					hasError = true;
					addSizeNotice();
				} else {
					enableForm();
					removeSizeNotice();
				}

				var fileExtension = file.name.split('.').pop();
				if ( -1 === OpenLabDCOCommentAttachment.allowed_types.indexOf( fileExtension ) ) {
					hasError = true;
					addTypeNotice();
				} else {
					removeTypeNotice();
				}
			}

			if ( hasError ) {
				disableForm();
			} else {
				enableForm();

				// Need duplicate logic in case file is removed.
				removeSizeNotice();
				removeTypeNotice();
			}
		});
	});
}(jQuery));
