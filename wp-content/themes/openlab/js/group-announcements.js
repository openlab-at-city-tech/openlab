(function($) {
	let quillEditors = {}

	$(document).ready( () => {
		const $textarea = $( '#announcement-text' );
		const $submitButton = $( '#announcement-submit' );
		const $announcementList = $( '.announcement-list' );

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

					initAnnouncementEditor( $newAnnouncement[0] )

					$textarea.val( '' )
					quillEditor.setText( '' )

					$submitButton.removeClass('loading');
					$submitButton.prop('disabled', false);
				} else {

				}
			});
		})

		// Handle Reply submission.
		$announcementList.on( 'click', '.reply-submit', (e) => {
			e.preventDefault()

			const $parentReply = $( e.target ).closest( '.announcement-reply-item' )
			const $parentAnnouncement = $( e.target ).closest( '.announcement-item' )

			const $parentItem = $parentReply.length > 0 ? $parentReply : $parentAnnouncement

			const announcementId = $parentItem.data( 'announcementId' )
			const parentReplyId = $parentItem.data( 'replyId' )

			const editorId = $parentItem.data( 'editorId' )

			const replyEditor = quillEditors[ editorId ] || null

			if ( replyEditor ) {
				replyEditor.enable( false )
			}

			const replyContent = replyEditor ? replyEditor.getText() : ''

			$.post( ajaxurl, {
					action: 'openlab_post_announcement_reply',
					cookie: bp_get_cookies(),
					'announcement-reply-nonce': $( '#announcement-reply-nonce-' + editorId ).val(),
					content: replyContent,
					announcementId: announcementId,
					parentReplyId: parentReplyId
				},
				function(response) {
					if ( response.success ) {
						$parentItem.removeClass( 'show-reply-form' )

						const $parentReplies = parentReplyId ? $parentItem.find( '> .group-item-wrapper > .announcement-reply-replies' ) : $parentItem.find( '.announcement-replies' )
						$parentReplies.prepend( response.data )

						if ( replyEditor ) {
							replyEditor.enable( true )
							replyEditor.setText( '' )
						}

						const $newReply = $parentReplies.find( '.announcement-reply-item:first-child' );
						$newReply.addClass( 'new-update' );
						setTimeout(
							() => {
								$newReply.removeClass( 'new-update' );
							},
							2000
						);

						initAnnouncementEditor( $newReply[0] )

					} else {
					}
				}
		  )
		})

		// Clicking 'Reply' link should toggle Reply fields.
		$announcementList.on( 'click', '.announcement-reply-link', (e) => {
			e.preventDefault()

			const $parentReply = $( e.target ).closest( '.announcement-reply-item' )
			const $parentAnnouncement = $( e.target ).closest( '.announcement-item' )

			const $parentItem = $parentReply.length > 0 ? $parentReply : $parentAnnouncement

			const formIsShown = $parentItem.hasClass( 'show-reply-form' )
			if ( formIsShown ) {
				$parentItem.removeClass( 'show-reply-form' )
			} else {
				$parentItem.addClass( 'show-reply-form' )

				const editorId = $parentItem.data( 'editorId' )
				quillEditors[ editorId ].focus()
			}
		})

		$( '.announcement-item, .announcement-reply-item' ).each( ( key, announcement ) => {
			initAnnouncementEditor( announcement )
		})
	})

	const initAnnouncementEditor = ( announcement ) => {
		if ( ! announcement.classList.contains( 'user-can-reply' ) ) {
			return
		}

		const editorId = announcement.dataset.editorId

		const toolbarId = 'quill-toolbar-' + editorId
		const toolbarEl = document.getElementById( toolbarId )

		// Sanity check.
		if ( ! toolbarEl ) {
			return
		}

		$( announcement ).find( '.announcement-textarea' ).append( '<div class="announcement-rich-text-editor"></div>' );

		quillEditors[ editorId ] = new Quill( announcement.querySelector( '.announcement-rich-text-editor' ), {
				modules: {
						toolbar: toolbarEl
				},
				theme: 'snow'
		});
	}
})(jQuery)
