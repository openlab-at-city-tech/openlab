( function ( $ ) {
	let attachmentNoticeNeedHide, $wrap;

	const showAttachmentNotice = function ( url ) {
		$wrap.find( '.dco-attachment' ).addClass( 'dco-hidden' );

		const $notice = $wrap.find( '.dco-attachment-notice' );
		$notice.children( 'a' ).attr( 'href', url );
		$notice.removeClass( 'dco-hidden' );

		attachmentNoticeNeedHide = false;
	};

	const hideAttachmentNotice = function () {
		$wrap.find( '.dco-attachment' ).removeClass( 'dco-hidden' );
		$wrap.find( '.dco-attachment-notice' ).addClass( 'dco-hidden' );
	};

	$( document ).ready( function () {
		$( '#the-comment-list' ).on(
			'click',
			'.dco-del-attachment',
			function ( event ) {
				event.preventDefault();

				/* eslint-disable no-undef, no-alert */
				if (
					1 === parseInt( dcoCA.delete_attachment_action ) &&
					! confirm( dcoCA.delete_attachment_confirm )
				) {
					return;
				}
				/* eslint-enable no-undef, no-alert */

				const $this = $( this );
				const nonce = $this.data( 'nonce' );
				const id = $this.data( 'id' );

				const data = {
					action: 'delete_attachment',
					id,
					_ajax_nonce: nonce, // eslint-disable-line camelcase
				};

				// eslint-disable-next-line no-undef
				$.post( ajaxurl, data, function ( response ) {
					if ( response.success ) {
						const $comment = $this.closest( '.comment' );
						const $attachment = $comment.find( '.dco-attachment' );
						$attachment.remove();
						$this.remove();
					}
				} );
			}
		);

		// Only for DCO_CA_Admin::show_bulk_action_message()
		if ( $( '#the-comment-list' ).length ) {
			const $referer = $( '[name="_wp_http_referer"]' );
			const params = new URLSearchParams( $referer.val() );
			params.delete( 'deletedattachment' );
			$referer.val( decodeURIComponent( params.toString() ) );
		}

		$( '#dco-comment-attachment' ).on(
			'click',
			'.dco-set-attachment',
			function ( event ) {
				event.preventDefault();

				$wrap = $( this ).closest( '.dco-attachment-wrap' );

				const frame = new wp.media.view.MediaFrame.Select( {
					title: dcoCA.set_attachment_title, // eslint-disable-line no-undef
					multiple: false,
					library: {
						uploadedTo: null,
					},
					button: {
						text: dcoCA.set_attachment_title, // eslint-disable-line no-undef
					},
				} );

				frame.on( 'select', function () {
					let $attachment;
					const $removeAttachment = $wrap.find(
						'.dco-remove-attachment'
					);

					// We set multiple to false so only get one image from the uploader.
					const selection = frame
						.state()
						.get( 'selection' )
						.first()
						.toJSON();

					if ( $removeAttachment.hasClass( 'dco-hidden' ) ) {
						$wrap.trigger( 'dco_ca_before_adding' );

						const $clone = $wrap.clone( true, true );
						$( '#dco-comment-attachment .inside' ).append( $clone );
					} else {
						$wrap.trigger( 'dco_ca_before_replacing' );
					}

					$wrap.find( '.dco-attachment-id' ).val( selection.id );

					attachmentNoticeNeedHide = true;

					switch ( selection.type ) {
						case 'image':
							let thumbnail;
							if ( selection.sizes.hasOwnProperty( 'medium' ) ) {
								thumbnail = selection.sizes.medium;
							} else {
								thumbnail = selection.sizes.full;
							}

							$attachment = $wrap.find( '.dco-image-attachment' );
							if ( ! $attachment.length ) {
								showAttachmentNotice( thumbnail.url );
								break;
							}

							$attachment
								.children( 'img' )
								.attr( {
									src: thumbnail.url,
									width: thumbnail.width,
									height: thumbnail.height,
								} )
								.removeAttr( 'srcset' )
								.removeAttr( 'sizes' );
							break;
						case 'video':
							$attachment = $wrap.find( '.dco-video-attachment' );
							if ( ! $attachment.length ) {
								showAttachmentNotice( selection.url );
								break;
							}

							$attachment
								.find( 'video' )[ 0 ]
								.setSrc( selection.url );
							break;
						case 'audio':
							$attachment = $wrap.find( '.dco-audio-attachment' );
							if ( ! $attachment.length ) {
								showAttachmentNotice( selection.url );
								break;
							}

							$attachment
								.find( 'audio' )[ 0 ]
								.setSrc( selection.url );
							break;
						default:
							$attachment = $wrap.find( '.dco-misc-attachment' );
							if ( ! $attachment.length ) {
								showAttachmentNotice( selection.url );
								break;
							}

							$attachment
								.children( 'a' )
								.attr( 'href', selection.url )
								.text( selection.title );
					}

					if ( attachmentNoticeNeedHide ) {
						hideAttachmentNotice();
					}
					$removeAttachment.removeClass( 'dco-hidden' );
					$wrap
						.find( '.dco-set-attachment' )
						.text( dcoCA.replace_attachment_label ); // eslint-disable-line no-undef
				} );

				frame.open();
			}
		);

		$( '#dco-comment-attachment' ).on(
			'click',
			'.dco-remove-attachment',
			function ( event ) {
				event.preventDefault();

				$wrap = $( this ).closest( '.dco-attachment-wrap' ).remove();
				$wrap.trigger( 'dco_ca_removed' );
			}
		);

		$( '#dco-file-types' ).on(
			'click',
			'.dco-show-all',
			function ( event ) {
				event.preventDefault();

				const $this = $( this );
				const $more = $this.prev();

				if ( $more.is( ':visible' ) ) {
					$more.removeClass( 'show' );
					$this.text( dcoCA.show_all ); // eslint-disable-line no-undef
				} else {
					$more.addClass( 'show' );
					$this.text( dcoCA.show_less ); // eslint-disable-line no-undef
				}
			}
		);

		$( '.dco-file-type' ).each( function () {
			const $this = $( this );
			const $checks = $this.find( '.dco-file-type-item-checkbox' );
			const $checkAll = $this.find( '.dco-file-type-name-checkbox' );

			if ( ! $checks.not( ':checked' ).length ) {
				$checkAll.prop( 'checked', true );
			}
		} );

		$( '#dco-file-types' ).on(
			'click',
			'.dco-file-type-name-checkbox',
			function () {
				const $this = $( this );
				const $checks = $this
					.closest( '.dco-file-type' )
					.find( '.dco-file-type-item-checkbox' );

				if ( $checks.not( ':checked' ).length ) {
					$checks.prop( 'checked', true );
				} else {
					$checks.prop( 'checked', false );
				}
			}
		);

		$( '#dco-file-types' ).on(
			'click',
			'.dco-file-type-item-checkbox',
			function () {
				const $this = $( this );
				const $type = $this.closest( '.dco-file-type' );
				const $checks = $type.find( '.dco-file-type-item-checkbox' );
				const $checkAll = $type.find( '.dco-file-type-name-checkbox' );

				if ( $checks.not( ':checked' ).length ) {
					$checkAll.prop( 'checked', false );
				} else {
					$checkAll.prop( 'checked', true );
				}
			}
		);
	} );
} )( jQuery ); // eslint-disable-line no-undef
