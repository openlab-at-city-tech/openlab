(function($){
	var $attachmentField,
		$commentForm,
		$uploadSizeNotice;

	function enableForm() {
		$commentSubmit.removeAttr( 'disabled' );
	}

	function disableForm() {
		$commentSubmit.attr( 'disabled', 'disabled' );
	}

	function addNotice() {
		$uploadSizeNotice.addClass( 'has-error' );
	}

	function removeNotice() {
		$uploadSizeNotice.removeClass( 'has-error' );
	}

	$(document).ready(function(){
		$attachmentField = $('input#attachment');
		$commentSubmit = $attachmentField.closest( 'form' ).find( 'input[type="submit"]' );
		$uploadSizeNotice = $('.comment-attachment-max-upload-size');

		$attachmentField.on('change',function(e){
			var file = e.target.files[0];

			if ( file.size > OpenLabDCOCommentAttachment.max_upload_size ) {
				disableForm();
				addNotice();
			} else {
				enableForm();
				removeNotice();
			}

			console.log(file);
			console.log(window.OpenLabDCOCommentAttachment);
		});
	});
}(jQuery));
