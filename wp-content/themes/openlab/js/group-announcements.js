(function($) {
	$(document).ready( () => {
		const $textarea = $( '#announcement-text' );
		const $submitButton = $( '#announcement-submit' );

		// Add the div needed by Quill.
		$textarea.parent().append( '<div id="announcement-rich-text-editor" class="announcement-rich-text-editor"></div>' );

		$textarea.hide();

		var quillEditor = new Quill( '#announcement-rich-text-editor', {
				modules: {
						toolbar: '#quill-toolbar'
				},
				theme: 'snow'
		});

		/**
		 * Get content of the Quill editor and put its content in the textarea.
		 */
		quillEditor.on( 'text-change', function( delta, oldDelta, source ) {
				if( quillEditor.getText().trim() ) {
						let contentHtml = quillEditor.root.innerHTML;
						$textarea.val(contentHtml);
				} else {
						$textarea.val('');
				}
		});

		$submitButton.on( 'click', () => {
			const $form = $submitButton.closest( 'form' )

			$('div.error').remove();
			$submitButton.addClass('loading');
			$submitButton.prop('disabled', true);
			$form.addClass("submitted");

			const object = 'groups';
			const group_id = $("#whats-new-post-in").val();
			const content = $textarea.val();

			quillEditor.enable( false )

			$.post( ajaxurl, {
				action: 'openlab_post_announcement',
				'cookie': bp_get_cookies(),
				'_wpnonce_post_update': $("input#_wpnonce_post_update").val(),
				'content': content,
				'group_id': group_id
			},
			function(response) {
				quillEditor.enable( true )

				if ( response.success ) {
					$( '#no-announcement-message' ).hide()
					$( '.announcement-list' ).prepend( response.data )

					const $newAnnouncement = $( '.announcement-list > article:first-child' );
					$newAnnouncement.addClass( 'new-update' );
					setTimeout(
						() => {
							$newAnnouncement.removeClass( 'new-update' );
						},
						2000
					);

					$textarea.val( '' )
					quillEditor.setText( '' )

					$submitButton.removeClass('loading');
					$submitButton.prop('disabled', false);
				} else {

				}


			});
		})
	})
})(jQuery)
