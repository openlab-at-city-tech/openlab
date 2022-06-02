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

			const $parentItem = getParentItem( e.target )

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

			const $parentItem = getParentItem( e.target )

			const formIsShown = $parentItem.hasClass( 'show-reply-form' )
			if ( formIsShown ) {
				$parentItem.removeClass( 'show-reply-form' )
			} else {
				$parentItem.addClass( 'show-reply-form' )

				const editorId = $parentItem.data( 'editorId' )
				quillEditors[ editorId ].focus()
			}
		})

		// Entering 'Edit' mode.
		$announcementList.on( 'click', '.announcement-edit-link', (e) => {
			e.preventDefault()

			const $parentItem = getParentItem( e.target )
			const $parentItemBody = $parentItem.find( '> .group-item-wrapper > .announcement-body' )

			const isEditMode = $parentItem.hasClass( 'edit-mode' )

			$parentItem.find( '> .group-item-wrapper > .announcement-actions .announcement-edit-link' ).addClass( 'disabled-link' )

			if ( isEditMode ) {
				// Do nothing?
			} else {
				$parentItem.addClass( 'edit-mode' )

				const editorId = $parentItem.data( 'editorId' )

				// Build the edit interface.
				const editTemplate = wp.template( 'openlab-announcement-edit-form' )
				const editMarkup = editTemplate(
					{
						announcementId: $parentItem.data( 'announcementId' ),
						editorId: editorId,
						replyId: $parentItem.data( 'replyId' )
					}
				)

				$parentItemBody.hide().after( editMarkup )

				const $editForm = $parentItem.find( '.announcement-edit-form' )

				quillEditors[ editorId ] = new Quill( $editForm[0].querySelector( '.announcement-rich-text-editor' ), {
						modules: {
								toolbar: '#quill-toolbar-edit-' + editorId,
								clipboard: {
									matchVisual: false
								}
						},
						theme: 'snow'
				});

				const thisEditor = quillEditors[ editorId ]

				const delta = thisEditor.clipboard.convert( $parentItemBody.html() )
				thisEditor.setContents( delta )
				thisEditor.setSelection( thisEditor.getLength() )
			}
		} )

		// Saving an edit.
		$announcementList.on( 'click', '.announcement-edit-submit', (e) => {
			e.preventDefault()

			const $parentItem = getParentItem( e.target )

			const itemType = $parentItem.data( 'itemType' )

			const editorId = $parentItem.data( 'editorId' )
			const thisEditor = quillEditors[ editorId ] || null
			const editorContent = thisEditor ? thisEditor.root.innerHTML : ''

			const postParams = 'announcement' === itemType ?
				{
					action: 'openlab_edit_announcement',
					cookie: bp_get_cookies(),
					nonce: $parentItem.data( 'nonce' ),
					announcementId: $parentItem.data( 'announcementId' ),
					editorId: editorId,
					content: editorContent
				} :
				{
					action: 'openlab_edit_announcement_reply',
					cookie: bp_get_cookies(),
					nonce: $parentItem.data( 'nonce' ),
					replyId: $parentItem.data( 'replyId' ),
					editorId: editorId,
					content: editorContent
				}

			if ( thisEditor ) {
				thisEditor.enable( false )
			}

			$.post( ajaxurl, postParams, function(response) {
				if ( thisEditor ) {
					const delta = thisEditor.clipboard.convert( response.data.content )
					thisEditor.setContents( delta )

					thisEditor.enable( true )
				}

				setBodyText( $parentItem, response.data.content )
				closeEditMode( $parentItem )
			} )
		} )

		// Cancelling edit mode.
		$announcementList.on( 'click', '.edit-cancel', (e) => {
			e.preventDefault()
			closeEditMode( getParentItem( e.target ) )
		} )

		$( '.announcement-item, .announcement-reply-item' ).each( ( key, announcement ) => {
			initAnnouncementEditor( announcement )
		})
	})

	/**
	 * Gets the parent item for a clicked element.
	 *
	 * The parent item is the closest item that's either announcement-item or
	 * announcement-reply-item.
	 *
	 * Returns a jQuery object.
	 */
	const getParentItem = ( element ) => {
		const $parentReply = $( element ).closest( '.announcement-reply-item' )
		const $parentAnnouncement = $( element ).closest( '.announcement-item' )

		return $parentReply.length > 0 ? $parentReply : $parentAnnouncement
	}

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

	const closeEditMode = ( $parentItem ) => {
		$parentItem.find( '> .group-item-wrapper > .announcement-edit-form' ).remove()
		$parentItem.removeClass( 'edit-mode' )
		$parentItem.find( '> .group-item-wrapper > .announcement-body' ).show()
		$parentItem.find( '> .group-item-wrapper > .announcement-actions .announcement-edit-link' ).removeClass( 'disabled-link' )
	}

	const setBodyText = ( $parentItem, text ) => {
		$parentItem.find( '> .group-item-wrapper > .announcement-body' ).html( text )
	}
})(jQuery)
