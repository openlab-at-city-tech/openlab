/**
 * Editor-side companion for the locked "PDF Embedder" Elementor panel card.
 *
 * See Admin\Education\Elementor::enqueue_editor_script() for why each
 * responsibility exists:
 *
 * 1. Search keywords. Elementor drops the `keywords` field from promotion
 *    config entries, so the panel search would match our card by title only.
 *    Set the keywords on our panel model through the `panel/elements/regionViews`
 *    filter, which runs before the panel view renders.
 *
 * 2. Upgrade dialog. Answer the card's press event with the editor's legacy
 *    dialog in every Elementor state. The panel dispatches the event on
 *    `document`, so a capture-phase listener on `window` runs before Elementor's
 *    promotions app can mount its card, regardless of listener registration
 *    order; stopping propagation there keeps a single dialog with our pricing
 *    link instead of the app's license-activation rewrite.
 *
 * @since 5.0.2
 */

/* global elementor, pdfembElementorEdu */
( function() {
	'use strict';

	if ( typeof pdfembElementorEdu === 'undefined' || ! pdfembElementorEdu.widget ) {
		return;
	}

	if ( window.elementor && elementor.hooks && typeof elementor.hooks.addFilter === 'function' ) {
		elementor.hooks.addFilter( 'panel/elements/regionViews', function( regionViews ) {
			var collection = regionViews && regionViews.elements && regionViews.elements.options && regionViews.elements.options.collection;

			if ( collection && typeof collection.findWhere === 'function' ) {
				var model = collection.findWhere( { name: pdfembElementorEdu.widget, editable: false } );

				if ( model ) {
					model.set( 'keywords', pdfembElementorEdu.keywords || [] );
				}
			}

			return regionViews;
		} );
	}

	if ( ! pdfembElementorEdu.event ) {
		return;
	}

	window.addEventListener( pdfembElementorEdu.event, function( event ) {
		// The dialog helper has shipped with the editor for years, but nothing
		// guarantees it: leave the event alone so Elementor's own card, when
		// loaded, can still answer the press, rather than swallowing it.
		if ( ! window.elementor || ! elementor.promotion || typeof elementor.promotion.showDialog !== 'function' ) {
			return;
		}

		event.stopPropagation();

		var dialog = elementor.promotion.showDialog( {
			title: pdfembElementorEdu.title,
			content: pdfembElementorEdu.content,
			targetElement: ( event.detail && event.detail.target ) || null,
			actionButton: {
				url: pdfembElementorEdu.url,
				text: pdfembElementorEdu.button,
				classes: [ 'elementor-button', 'go-pro' ],
			},
		} );

		decorateDialog( dialog );
		prependBanner( dialog );
		appendDocsLink( dialog );
	}, true );

	/**
	 * Mark the dialog as ours for the duration of this open.
	 *
	 * The widget is a singleton shared with Elementor's own promotions, so the
	 * marker class scopes our CSS (PRO badge removal, close-icon alignment,
	 * button-row layout) to our opens only, and the hide handler removes the
	 * class and our injected elements so nothing leaks into other openers.
	 */
	function decorateDialog( dialog ) {
		if ( ! dialog || typeof dialog.getElements !== 'function' ) {
			return;
		}

		dialog.getElements( 'widget' ).addClass( 'pdfemb-elementor-education-dialog' );

		if ( dialog.pdfembEduCleanupBound || typeof dialog.on !== 'function' ) {
			return;
		}

		dialog.pdfembEduCleanupBound = true;

		dialog.on( 'hide', function() {
			dialog.getElements( 'widget' ).removeClass( 'pdfemb-elementor-education-dialog' );
			dialog.getElements( 'buttonsWrapper' ).find( 'a.pdfemb-elementor-education-docs' ).remove();
			dialog.getElements( 'message' ).find( 'img.pdfemb-elementor-education-banner' ).remove();
		} );
	}

	/**
	 * Place the promo banner at the top of the message area.
	 *
	 * showDialog() rewrites the whole message on every open, so the banner has
	 * to be re-inserted each time; that also means it can never duplicate.
	 */
	function prependBanner( dialog ) {
		if ( ! pdfembElementorEdu.image || ! dialog || typeof dialog.getElements !== 'function' ) {
			return;
		}

		var message = dialog.getElements( 'message' );

		if ( ! message || ! message.length ) {
			return;
		}

		message.prepend( jQuery( '<img>', {
			class: 'pdfemb-elementor-education-banner',
			src: pdfembElementorEdu.image,
			alt: '',
		} ) );
	}

	/**
	 * Place the documentation link at the end of the action-button row.
	 *
	 * The dialog widget is a singleton that Elementor reuses across opens, and
	 * showDialog() recreates the action button by appending it on every open —
	 * so the link is (re)appended after it each time to keep the button-first,
	 * link-last order.
	 */
	function appendDocsLink( dialog ) {
		if ( ! pdfembElementorEdu.docsUrl || ! dialog || typeof dialog.getElements !== 'function' ) {
			return;
		}

		var wrapper = dialog.getElements( 'buttonsWrapper' );

		if ( ! wrapper || ! wrapper.length ) {
			return;
		}

		var link = wrapper.find( 'a.pdfemb-elementor-education-docs' );

		if ( ! link.length ) {
			link = jQuery( '<a>', {
				class: 'pdfemb-elementor-education-docs',
				href: pdfembElementorEdu.docsUrl,
				target: '_blank',
				rel: 'noopener noreferrer',
				text: pdfembElementorEdu.docsText,
			} );
		}

		wrapper.append( link );
	}
}() );
