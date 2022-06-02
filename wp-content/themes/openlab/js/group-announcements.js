(function($) {
	let quillEditors = {}

	$(document).ready( () => {
		const $announcementList = $( '.announcement-list' );
		const $newAnnouncementItem = $( '.announcement-item-new' )

		// Main 'new announcement' form setup.
		if ( $newAnnouncementItem.length > 0 ) {
			const $textarea = $( '#announcement-text' );
			const $title = $( '#title-new-announcement' )
			const $submitButton = $( '#announcement-submit' );

			initQuillEditor( $newAnnouncementItem )
			setUpTextLabelClick( $newAnnouncementItem )
			$textarea.hide();

			const thisEditor = quillEditors[ 'new-announcement' ]

			/**
			 * Get content of the Quill editor and put its content in the textarea.
			 */
			thisEditor.on( 'text-change', function( delta, oldDelta, source ) {
					if( thisEditor.getText().trim() ) {
							let contentHtml = thisEditor.root.innerHTML;
							$textarea.val(contentHtml);
					} else {
							$textarea.val( '' );
					}
			});

			setUpTextLabelClick( $newAnnouncementItem )

			$submitButton.on( 'click', () => {
				const $form = $submitButton.closest( 'form' )

				$('div.error').remove();
				$submitButton.addClass( 'ajax-loading' );
				$submitButton.prop( 'disabled', true );
				$form.addClass("submitted");

				const object = 'groups';
				const group_id = $("#whats-new-post-in").val();
				const content = $textarea.val();

				$.post( ajaxurl, {
					action: 'openlab_post_announcement',
					'cookie': bp_get_cookies(),
					'_wpnonce_post_update': $("input#_wpnonce_post_update").val(),
					'content': content,
					'group_id': group_id,
					title: $title.val()
				},
				function(response) {
					thisEditor.enable( true )

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
						thisEditor.setText( '' )
						$title.val( '' )

						$submitButton.removeClass( 'ajax-loading' );
						$submitButton.prop( 'disabled', false );
					} else {

					}
				});
			})
		}

		// Clicking the 'announcement-text' label should put focus into Quill editor.
		$announcementList.on( 'click', '.announcement-text-label', (e) => {
			const $parentItem = getParentItem( e.target )
			setUpTextLabelClick( $parentItem )
		} )

		// Handle Reply submission.
		$announcementList.on( 'click', '.announcement-reply-submit', (e) => {
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

			e.target.classList.add( 'ajax-loading' )
			e.target.disabled = true

			$.post( ajaxurl, {
					action: 'openlab_post_announcement_reply',
					cookie: bp_get_cookies(),
					nonce: $parentItem.data( 'nonce' ),
					editorId: editorId,
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

						e.target.classList.remove( 'ajax-loading' )
						e.target.disabled = false

						closeReplyMode( $parentItem )

					} else {
					}
				}
		  )
		})

		// Generate Reply form when clicking 'Reply'.
		$announcementList.on( 'click', '.announcement-reply-link', (e) => {
			e.preventDefault()

			const $parentItem = getParentItem( e.target )
			const $parentItemBody = $parentItem.find( '> .group-item-wrapper > .announcement-body' )

			const editorId = $parentItem.data( 'editorId' )

			const formIsShown = $parentItem.hasClass( 'show-reply-form' )
			if ( formIsShown ) {
				$parentItem.removeClass( 'show-reply-form' )
			} else {
				$parentItem.addClass( 'show-reply-form' )

				const replyTemplate = wp.template( 'openlab-announcement-reply-form' )
				const replyMarkup = replyTemplate(
					{
						announcementId: $parentItem.data( 'announcementId' ),
						editorId: editorId,
						replyId: $parentItem.data( 'replyId' )
					}
				)

				$parentItemBody.after( replyMarkup )
				$parentItem.find( '> .group-item-wrapper > .announcement-actions .announcement-reply-link' ).addClass( 'disabled-link' )

				const $replyForm = $parentItem.find( '.announcement-reply-form' )

				initQuillEditor( $parentItem )

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

			const itemType = $parentItem.data( 'itemType' )

			if ( isEditMode ) {
				// Do nothing?
			} else {
				$parentItem.addClass( 'edit-mode' )

				const editorId = $parentItem.data( 'editorId' )

				const templateName = 'reply' === itemType ? 'openlab-announcement-reply-edit-form' : 'openlab-announcement-edit-form'

				const title = 'reply' === itemType ? '' : $parentItem.find( '.announcement-title-rendered' ).html()

				// Build the edit interface.
				const editTemplate = wp.template( templateName )
				const editMarkup = editTemplate(
					{
						announcementId: $parentItem.data( 'announcementId' ),
						editorId: editorId,
						replyId: $parentItem.data( 'replyId' ),
						title
					}
				)

				$parentItemBody.hide().after( editMarkup )

				const $editForm = $parentItem.find( '.announcement-edit-form' )

				initQuillEditor( $parentItem )

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

			e.target.classList.add( 'ajax-loading' )
			e.target.disabled = true

			const postParams = 'announcement' === itemType ?
				{
					action: 'openlab_edit_announcement',
					cookie: bp_get_cookies(),
					nonce: $parentItem.data( 'nonce' ),
					announcementId: $parentItem.data( 'announcementId' ),
					editorId: editorId,
					content: editorContent,
					title: $parentItem.find( 'input.announcement-title' ).val()
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

				e.target.classList.remove( 'ajax-loading' )
				e.target.disabled = false

				if ( response.data.hasOwnProperty( 'title' ) ) {
					setTitle( $parentItem, response.data.title )
				}

				closeEditMode( $parentItem )
			} )
		} )

		// Cancelling edit mode.
		$announcementList.on( 'click', '.announcement-edit-form .edit-cancel', (e) => {
			e.preventDefault()
			closeEditMode( getParentItem( e.target ) )
		} )

		// Cancelling reply mode.
		$announcementList.on( 'click', '.announcement-reply-form .edit-cancel', (e) => {
			e.preventDefault()
			closeReplyMode( getParentItem( e.target ) )
		} )

		// Delete confirmation.
		$announcementList.on( 'click', '.announcement-delete-link', (e) => {
			return window.confirm( 'Are you sure you want to delete this item?' ) ? true : e.preventDefault()
		} )
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

		initQuillEditor( $( announcement ) )
	}

	const closeEditMode = ( $parentItem ) => {
		$parentItem.find( '> .group-item-wrapper > .announcement-edit-form' ).remove()
		$parentItem.removeClass( 'edit-mode' )
		$parentItem.find( '> .group-item-wrapper > .announcement-body' ).show()
		$parentItem.find( '> .group-item-wrapper > .announcement-actions .announcement-edit-link' ).removeClass( 'disabled-link' )
	}

	const closeReplyMode = ( $parentItem ) => {
		$parentItem.find( '> .group-item-wrapper > .announcement-reply-form' ).remove()
		$parentItem.removeClass( 'show-reply-form' )
		$parentItem.find( '> .group-item-wrapper > .announcement-actions .announcement-reply-link' ).removeClass( 'disabled-link' )
	}

	const setUpTextLabelClick = ( $parentItem ) => {
		$parentItem.find( '.announcement-text-label' ).on( 'click', () => {
			const editorId = $parentItem.data( 'editorId' )
			const theEditor = quillEditors[ editorId ]

			if ( theEditor ) {
				theEditor.setSelection( theEditor.getLength() )
			}
		} )
	}

	const setBodyText = ( $parentItem, text ) => {
		$parentItem.find( '> .group-item-wrapper > .announcement-body' ).html( text )
	}

	const setTitle = ( $parentItem, text ) => {
		const editorId = $parentItem.data( 'editorId' )
		document.getElementById( 'title-rendered-' + editorId ).innerHTML = text
	}

	const initQuillEditor = ( $parentItem ) => {
		const toolbarTemplate = wp.template( 'openlab-announcement-quill-toolbar' )

		const editorId = $parentItem.data( 'editorId' )
		const toolbarId = editorId + '-toolbar'
		const toolbarMarkup = toolbarTemplate( { toolbarId } )

		const $editorDiv = $parentItem.find( '.announcement-rich-text-editor' )

		$editorDiv.before( toolbarMarkup )

		quillEditors[ editorId ] = new Quill( $editorDiv[0], {
				modules: {
						toolbar: '#' + toolbarId,
						clipboard: {
							matchVisual: false
						}
				},
				theme: 'snow'
		});
	}
})(jQuery)
